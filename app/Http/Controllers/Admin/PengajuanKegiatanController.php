<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
use App\Support\ActivityDescriptionSanitizer;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanKegiatanController extends Controller
{
    public function __construct(private readonly ActivityDescriptionSanitizer $descriptionSanitizer) {}

    public function index(Request $request)
    {
        if ($request->get('export') === 'csv') {
            return $this->export($request);
        }

        $approvalItems = $this->formatApprovalItems(
            $this->approvalItemsQuery($request)
                ->orderByDesc('submitted_at')
                ->orderBy('source')
                ->orderByDesc('source_id')
                ->paginate(10)
                ->withQueryString()
        );

        $makerStats = DB::table('kegiatan')
            ->whereNotNull('user_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('menunggu_approval', 'diproses') THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN status IN ('disetujui', 'dipublikasikan', 'berlangsung', 'selesai') THEN 1 ELSE 0 END) as disetujui")
            ->selectRaw("SUM(CASE WHEN status IN ('revisi', 'ditolak') THEN 1 ELSE 0 END) as perhatian")
            ->first();

        $budgetStats = DB::table('pengajuan_kegiatan as pengajuan')
            ->leftJoin('rencana_kegiatan as rencana', 'rencana.id', '=', 'pengajuan.rencana_kegiatan_id')
            ->leftJoin('detail_kegiatan as detail', 'detail.rencana_kegiatan_id', '=', 'rencana.id')
            ->selectRaw('COUNT(DISTINCT pengajuan.id) as total')
            ->selectRaw("SUM(CASE WHEN pengajuan.status IN ('diajukan', 'diproses') THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN pengajuan.status = 'disetujui' THEN 1 ELSE 0 END) as disetujui")
            ->selectRaw("SUM(CASE WHEN pengajuan.status IN ('revisi', 'ditolak') THEN 1 ELSE 0 END) as perhatian")
            ->selectRaw("SUM(CASE WHEN pengajuan.status IN ('diajukan', 'diproses') THEN COALESCE(detail.anggaran, 0) ELSE 0 END) as anggaran_review")
            ->first();

        $stats = [
            'total' => (int) $makerStats->total + (int) $budgetStats->total,
            'pending_total' => (int) $makerStats->menunggu + (int) $budgetStats->menunggu,
            'disetujui' => (int) $makerStats->disetujui + (int) $budgetStats->disetujui,
            'perhatian' => (int) $makerStats->perhatian + (int) $budgetStats->perhatian,
            'anggaran_review' => 'Rp '.number_format((float) $budgetStats->anggaran_review, 0, ',', '.'),
        ];

        $categories = Kegiatan::query()->whereNotNull('user_id')->distinct()->pluck('jenis_kegiatan')
            ->merge(RencanaKegiatan::query()->distinct()->pluck('jenis_kegiatan'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('Admin/Pengajuan/Index', [
            'approvalItems' => $approvalItems,
            'stats' => $stats,
            'categories' => $categories,
            'filters' => $request->only(['search', 'status', 'kategori']),
        ]);
    }

    public function show($pengajuan): Response
    {
        $item = PengajuanKegiatan::with(['rencanaKegiatan.detailKegiatan', 'kegiatan', 'admin'])
            ->findOrFail($pengajuan);

        if ($item->rencanaKegiatan?->detailKegiatan) {
            $item->rencanaKegiatan->detailKegiatan->setAttribute(
                'deskripsi_html',
                $this->descriptionSanitizer->sanitize($item->rencanaKegiatan->detailKegiatan->deskripsi),
            );
        }

        return Inertia::render('Admin/Pengajuan/Show', [
            'pengajuan' => $item,
            'rencana' => $item->rencanaKegiatan,
            'detail' => $item->rencanaKegiatan?->detailKegiatan,
            'kegiatan' => $item->kegiatan,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rencana_kegiatan_id' => ['required', 'exists:rencana_kegiatan,id'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $adminId = Auth::guard('admin')->id();
        $rencana = RencanaKegiatan::findOrFail($validated['rencana_kegiatan_id']);

        $pengajuan = DB::transaction(function () use ($validated, $adminId, $rencana) {
            $rencana->update(['status' => 'diajukan']);

            return PengajuanKegiatan::create([
                'rencana_kegiatan_id' => $rencana->id,
                'admin_id' => $adminId,
                'nomor_pengajuan' => 'PROP-'.date('Ymd').'-'.sprintf('%03d', $rencana->id),
                'tanggal_pengajuan' => now()->toDateString(),
                'status' => 'diajukan',
                'catatan' => $validated['catatan'] ?? 'Diajukan untuk review dan persetujuan kegiatan.',
            ]);
        });

        return redirect()->route('admin.pengajuan.index')
            ->with('success', "Pengajuan untuk \"{$rencana->nama_kegiatan}\" berhasil dibuat dengan nomor {$pengajuan->nomor_pengajuan}.");
    }

    public function updateStatus(Request $request, $pengajuan)
    {
        $item = PengajuanKegiatan::with('rencanaKegiatan.detailKegiatan')->findOrFail($pengajuan);

        $validated = $request->validate([
            'status' => ['required', 'in:diproses,disetujui,revisi,ditolak,diajukan'],
            'catatan' => ['nullable', 'string'],
            'catatan_revisi' => ['nullable', 'string'],
            'terbitkan_kegiatan' => ['nullable', 'boolean'],
        ]);

        $newStatus = $validated['status'];

        DB::transaction(function () use ($item, $newStatus, $validated, $request) {
            $item->status = $newStatus;
            $item->diproses_at = now();

            if (! empty($validated['catatan'])) {
                $item->catatan = $validated['catatan'];
            }

            if (! empty($validated['catatan_revisi'])) {
                $item->catatan_revisi = $validated['catatan_revisi'];
            }

            $item->save();

            if ($item->rencanaKegiatan) {
                if ($newStatus === 'disetujui') {
                    $item->rencanaKegiatan->update(['status' => 'disetujui']);
                } elseif ($newStatus === 'ditolak') {
                    $item->rencanaKegiatan->update(['status' => 'ditolak']);
                } elseif ($newStatus === 'revisi') {
                    $item->rencanaKegiatan->update(['status' => 'diajukan']);
                }
            }

            if ($newStatus === 'disetujui' && $request->boolean('terbitkan_kegiatan', true)) {
                if (! $item->kegiatan) {
                    $rencana = $item->rencanaKegiatan;
                    $detail = $rencana?->detailKegiatan;

                    Kegiatan::create([
                        'pengajuan_kegiatan_id' => $item->id,
                        'rencana_kegiatan_id' => $rencana?->id,
                        'nama_kegiatan' => $rencana?->nama_kegiatan ?? 'Kegiatan Edukasi Pasar Modal',
                        'deskripsi' => $this->descriptionSanitizer->sanitize(
                            $detail?->deskripsi ?? ('Program edukasi pasar modal: '.($rencana?->nama_kegiatan ?? ''))
                        ),
                        'jenis_kegiatan' => $rencana?->jenis_kegiatan ?? 'Edukasi',
                        'tanggal' => $detail?->tanggal ?? ($rencana?->tanggal_rencana ?? now()->toDateString()),
                        'waktu_mulai' => $detail?->waktu_mulai ?? '09:00',
                        'waktu_selesai' => $detail?->waktu_selesai ?? '15:30',
                        'lokasi' => $detail?->lokasi ?? 'Galeri Investasi KP BEI Bengkulu',
                        'kuota_peserta' => $detail?->target_peserta ?? 100,
                        'status' => 'dipublikasikan',
                    ]);
                }
            }
        });

        $statusLabels = [
            'disetujui' => 'disetujui',
            'diproses' => 'sedang ditinjau',
            'revisi' => 'diminta untuk revisi',
            'ditolak' => 'ditolak',
            'diajukan' => 'diajukan kembali',
        ];
        $label = $statusLabels[$newStatus] ?? $newStatus;

        return back()->with('success', "Status pengajuan {$item->nomor_pengajuan} berhasil diperbarui menjadi {$label}.");
    }

    public function updateMakerProposalStatus(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        abort_unless($kegiatan->user_id !== null, 404);
        abort_unless(in_array($kegiatan->status, ['menunggu_approval', 'diproses'], true), 409);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['diproses', 'disetujui', 'revisi', 'ditolak'])],
            'catatan' => [
                Rule::requiredIf(in_array($request->input('status'), ['revisi', 'ditolak'], true)),
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $kegiatan->update([
            'status' => $validated['status'],
            'approval_note' => $validated['catatan'] ?? null,
            'reviewed_by_admin_id' => Auth::guard('admin')->id(),
            'approval_processed_at' => now(),
        ]);

        return back()->with('success', 'Status proposal maker berhasil diperbarui.');
    }

    public function export(Request $request): StreamedResponse
    {
        $items = $this->formatApprovalItems(
            $this->approvalItemsQuery($request)
                ->orderByDesc('submitted_at')
                ->orderBy('source')
                ->orderByDesc('source_id')
                ->get()
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="daftar-approval-pengajuan-'.date('Ymd-His').'.csv"',
        ];

        return response()->stream(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Sumber',
                'Nomor',
                'Nama Kegiatan',
                'Kategori',
                'Pengusul',
                'Tanggal Pengajuan',
                'Alokasi Anggaran (Rp)',
                'Status Approval',
                'Catatan Review',
            ]);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item['source_label'],
                    $item['identifier'],
                    $item['nama_kegiatan'],
                    $item['jenis_kegiatan'],
                    $item['submitter'] ?: '-',
                    $item['submitted_at'] ?: '-',
                    $item['anggaran'] ?? '-',
                    $item['status_label'],
                    $item['review_note'] ?: '-',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    private function approvalItemsQuery(Request $request): Builder
    {
        $makerQuery = DB::table('kegiatan')
            ->leftJoin('users', 'users.id', '=', 'kegiatan.user_id')
            ->whereNotNull('kegiatan.user_id')
            ->selectRaw("'maker' as source")
            ->addSelect([
                'kegiatan.id as source_id',
                DB::raw('NULL as nomor_pengajuan'),
                'kegiatan.nama_kegiatan',
                'kegiatan.jenis_kegiatan',
                'kegiatan.created_at as submitted_at',
                'kegiatan.status',
                'users.name as submitter',
                'kegiatan.mitra',
                DB::raw('NULL as anggaran'),
                'kegiatan.approval_note as review_note',
                'kegiatan.proposal_file_path',
            ]);

        $budgetQuery = DB::table('pengajuan_kegiatan as pengajuan')
            ->join('rencana_kegiatan as rencana', 'rencana.id', '=', 'pengajuan.rencana_kegiatan_id')
            ->leftJoin('detail_kegiatan as detail', 'detail.rencana_kegiatan_id', '=', 'rencana.id')
            ->leftJoin('admins', 'admins.id', '=', 'pengajuan.admin_id')
            ->selectRaw("'anggaran' as source")
            ->addSelect([
                'pengajuan.id as source_id',
                'pengajuan.nomor_pengajuan',
                'rencana.nama_kegiatan',
                'rencana.jenis_kegiatan',
                'pengajuan.tanggal_pengajuan as submitted_at',
                'pengajuan.status',
                'admins.name as submitter',
                DB::raw('NULL as mitra'),
                'detail.anggaran as anggaran',
                'pengajuan.catatan_revisi as review_note',
                DB::raw('NULL as proposal_file_path'),
            ]);

        $this->applyApprovalFilters($makerQuery, $budgetQuery, $request);

        return DB::query()->fromSub($makerQuery->unionAll($budgetQuery), 'approval_items');
    }

    private function applyApprovalFilters(Builder $makerQuery, Builder $budgetQuery, Request $request): void
    {
        if ($search = $request->get('search')) {
            $makerQuery->where(function (Builder $query) use ($search) {
                $query->where('kegiatan.nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('kegiatan.mitra', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('kegiatan.lokasi', 'like', "%{$search}%");
            });

            $budgetQuery->where(function (Builder $query) use ($search) {
                $query->where('pengajuan.nomor_pengajuan', 'like', "%{$search}%")
                    ->orWhere('rencana.nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('rencana.jenis_kegiatan', 'like', "%{$search}%")
                    ->orWhere('detail.lokasi', 'like', "%{$search}%")
                    ->orWhere('pengajuan.catatan', 'like', "%{$search}%")
                    ->orWhere('pengajuan.catatan_revisi', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $makerQuery->where('kegiatan.jenis_kegiatan', $kategori);
            $budgetQuery->where('rencana.jenis_kegiatan', $kategori);
        }

        $status = $request->get('status');

        if ($status === 'menunggu') {
            $makerQuery->where('kegiatan.status', 'menunggu_approval');
            $budgetQuery->where('pengajuan.status', 'diajukan');
        } elseif ($status === 'diproses') {
            $makerQuery->where('kegiatan.status', 'diproses');
            $budgetQuery->where('pengajuan.status', 'diproses');
        } elseif (in_array($status, ['disetujui', 'revisi', 'ditolak'], true)) {
            $makerQuery->where('kegiatan.status', $status);
            $budgetQuery->where('pengajuan.status', $status);
        }
    }

    private function formatApprovalItems($items)
    {
        $formatter = function (object $item): array {
            $statuses = $item->source === 'maker' ? Kegiatan::STATUS : PengajuanKegiatan::STATUS;
            [$statusLabel, $statusClass] = $statuses[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-700'];

            return [
                'id' => $item->source.':'.$item->source_id,
                'source' => $item->source,
                'source_label' => $item->source === 'maker' ? 'Maker' : 'Anggaran',
                'source_id' => (int) $item->source_id,
                'identifier' => $item->source === 'maker'
                    ? 'MKR-'.sprintf('%05d', $item->source_id)
                    : ($item->nomor_pengajuan ?: 'PROP-'.sprintf('%05d', $item->source_id)),
                'nama_kegiatan' => $item->nama_kegiatan,
                'jenis_kegiatan' => $item->jenis_kegiatan,
                'submitted_at' => $item->submitted_at,
                'submitter' => $item->submitter,
                'mitra' => $item->mitra,
                'status' => $item->status,
                'status_label' => $statusLabel,
                'status_class' => $statusClass,
                'anggaran' => $item->anggaran === null ? null : (float) $item->anggaran,
                'formatted_anggaran' => $item->anggaran === null
                    ? null
                    : 'Rp '.number_format((float) $item->anggaran, 0, ',', '.'),
                'review_note' => $item->review_note,
                'proposal_file_path' => $item->proposal_file_path,
                'can_review' => $item->source === 'maker'
                    ? in_array($item->status, ['menunggu_approval', 'diproses'], true)
                    : in_array($item->status, ['diajukan', 'diproses'], true),
            ];
        };

        if ($items instanceof LengthAwarePaginator) {
            return $items->through($formatter);
        }

        return $items->map($formatter);
    }
}
