<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanKegiatanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('export') === 'csv') {
            return $this->export($request);
        }

        $query = PengajuanKegiatan::query()
            ->with(['rencanaKegiatan.detailKegiatan', 'kegiatan', 'admin'])
            ->latest('tanggal_pengajuan');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%")
                    ->orWhere('catatan_revisi', 'like', "%{$search}%")
                    ->orWhereHas('rencanaKegiatan', function ($rq) use ($search) {
                        $rq->where('nama_kegiatan', 'like', "%{$search}%")
                            ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                            ->orWhereHas('detailKegiatan', function ($dq) use ($search) {
                                $dq->where('lokasi', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($kategori = $request->get('kategori')) {
            $query->whereHas('rencanaKegiatan', function ($rq) use ($kategori) {
                $rq->where('jenis_kegiatan', $kategori);
            });
        }

        $all = PengajuanKegiatan::with('rencanaKegiatan.detailKegiatan')->get();
        $menungguList = $all->where('status', 'diajukan');
        $diprosesList = $all->where('status', 'diproses');
        $disetujuiList = $all->where('status', 'disetujui');
        $revisiList = $all->where('status', 'revisi');
        $ditolakList = $all->where('status', 'ditolak');

        $totalAnggaranReview = $all->whereIn('status', ['diajukan', 'diproses'])
            ->sum(fn ($p) => (float) ($p->rencanaKegiatan?->detailKegiatan?->anggaran ?? 0));

        $stats = [
            'total' => $all->count(),
            'menunggu' => $menungguList->count(),
            'diproses' => $diprosesList->count(),
            'pending_total' => $menungguList->count() + $diprosesList->count(),
            'disetujui' => $disetujuiList->count(),
            'revisi' => $revisiList->count(),
            'ditolak' => $ditolakList->count(),
            'anggaran_review' => 'Rp '.number_format($totalAnggaranReview, 0, ',', '.'),
        ];

        $pengajuanList = $query->paginate(10)->withQueryString();
        $categories = RencanaKegiatan::distinct()->pluck('jenis_kegiatan')->filter()->values();

        $makerQuery = Kegiatan::query()
            ->with(['user', 'reviewedByAdmin'])
            ->whereNotNull('user_id');

        if ($makerSearch = $request->get('maker_search')) {
            $makerQuery->where(function ($q) use ($makerSearch) {
                $q->where('nama_kegiatan', 'like', "%{$makerSearch}%")
                    ->orWhere('mitra', 'like', "%{$makerSearch}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$makerSearch}%"));
            });
        }

        if ($makerStatus = $request->get('maker_status')) {
            if ($makerStatus === 'menunggu') {
                $makerQuery->whereIn('status', ['menunggu_approval', 'diproses']);
            } elseif ($makerStatus === 'disetujui') {
                $makerQuery->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai']);
            } elseif ($makerStatus === 'masalah') {
                $makerQuery->whereIn('status', ['revisi', 'ditolak', 'dibatalkan']);
            }
        }

        $makerProposals = $makerQuery->latest('created_at')->paginate(8, ['*'], 'maker_page')->withQueryString();
        $makerStats = [
            'menunggu' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'disetujui' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'])->count(),
            'masalah' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['revisi', 'ditolak', 'dibatalkan'])->count(),
        ];
        $priorityProposal = Kegiatan::query()
            ->with('user')
            ->whereNotNull('user_id')
            ->whereIn('status', ['menunggu_approval', 'diproses'])
            ->oldest('created_at')
            ->first();

        return Inertia::render('Admin/Pengajuan/Index', [
            'pengajuanList' => $pengajuanList,
            'makerProposals' => $makerProposals,
            'makerStats' => $makerStats,
            'makerFilters' => $request->only(['maker_search', 'maker_status']),
            'priorityProposal' => $priorityProposal,
            'stats' => $stats,
            'categories' => $categories,
            'filters' => $request->only(['search', 'status', 'kategori']),
        ]);
    }

    public function show($pengajuan): Response
    {
        $item = PengajuanKegiatan::with(['rencanaKegiatan.detailKegiatan', 'kegiatan', 'admin'])
            ->findOrFail($pengajuan);

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
                        'deskripsi' => $detail?->deskripsi ?? ('Program edukasi pasar modal: '.($rencana?->nama_kegiatan ?? '')),
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
        $query = PengajuanKegiatan::with(['rencanaKegiatan.detailKegiatan', 'admin'])->latest('tanggal_pengajuan');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $items = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="daftar-approval-pengajuan-'.date('Ymd-His').'.csv"',
        ];

        return response()->stream(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Nomor Pengajuan',
                'Nama Kegiatan',
                'Kategori',
                'Tanggal Pengajuan',
                'Tanggal Pelaksanaan',
                'Alokasi Anggaran (Rp)',
                'Status Approval',
                'Pengusul',
                'Catatan Review',
            ]);

            foreach ($items as $p) {
                fputcsv($handle, [
                    $p->nomor_pengajuan,
                    $p->nama_kegiatan,
                    $p->jenis_kegiatan,
                    $p->tanggal_pengajuan?->format('d/m/Y') ?? '-',
                    $p->rencanaKegiatan?->tanggal_rencana?->format('d/m/Y') ?? '-',
                    $p->anggaran,
                    $p->status_label,
                    $p->admin?->name ?? 'Admin BEI',
                    $p->catatan_revisi ?: ($p->catatan ?: '-'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
