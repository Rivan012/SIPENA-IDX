<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailKegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
use App\Support\ActivityDescriptionSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RencanaKegiatanController extends Controller
{
    public const PAGU_RKAP_DEFAULT = 350000000;

    public function __construct(private readonly ActivityDescriptionSanitizer $descriptionSanitizer) {}

    public function index(Request $request)
    {
        if ($request->get('export') === 'csv') {
            return $this->export($request);
        }

        $query = RencanaKegiatan::query()
            ->with(['detailKegiatan', 'pengajuanKegiatan', 'kegiatan', 'admin'])
            ->latest('tanggal_rencana');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('detailKegiatan', function ($dq) use ($search) {
                        $dq->where('lokasi', 'like', "%{$search}%")
                            ->orWhere('deskripsi', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($jenis = $request->get('jenis')) {
            $query->where('jenis_kegiatan', $jenis);
        }

        $selectedYear = $request->get('year');
        if (! $selectedYear) {
            $latestRencana = RencanaKegiatan::orderByDesc('tanggal_rencana')->first();
            $selectedYear = $latestRencana?->tanggal_rencana?->format('Y') ?? 2025;
        }
        if ($selectedYear !== 'all') {
            $query->whereYear('tanggal_rencana', (int) $selectedYear);
        }

        $allRencanaQuery = RencanaKegiatan::with('detailKegiatan');
        if ($selectedYear !== 'all') {
            $allRencanaQuery->whereYear('tanggal_rencana', (int) $selectedYear);
        }
        $allRencana = $allRencanaQuery->get();

        $paguRkap = self::PAGU_RKAP_DEFAULT;
        $totalAlokasi = $allRencana->sum(fn ($r) => (float) ($r->detailKegiatan?->anggaran ?? 0));
        $disetujuiList = $allRencana->where('status', 'disetujui');
        $totalDisetujui = $disetujuiList->sum(fn ($r) => (float) ($r->detailKegiatan?->anggaran ?? 0));
        $sisaPagu = max(0, $paguRkap - $totalDisetujui);
        $persenPenyerapan = $paguRkap > 0 ? min(100, round(($totalDisetujui / $paguRkap) * 100, 1)) : 0;

        $stats = [
            'pagu_rkap' => $paguRkap,
            'pagu_rkap_formatted' => 'Rp '.number_format($paguRkap, 0, ',', '.'),
            'total_alokasi' => $totalAlokasi,
            'total_alokasi_formatted' => 'Rp '.number_format($totalAlokasi, 0, ',', '.'),
            'total_disetujui' => $totalDisetujui,
            'total_disetujui_formatted' => 'Rp '.number_format($totalDisetujui, 0, ',', '.'),
            'sisa_pagu' => $sisaPagu,
            'sisa_pagu_formatted' => 'Rp '.number_format($sisaPagu, 0, ',', '.'),
            'persen_penyerapan' => $persenPenyerapan,
            'total_kegiatan' => $allRencana->count(),
            'count_draft' => $allRencana->where('status', 'draft')->count(),
            'count_diajukan' => $allRencana->where('status', 'diajukan')->count(),
            'count_disetujui' => $disetujuiList->count(),
            'count_ditolak' => $allRencana->where('status', 'ditolak')->count(),
            'selected_year' => $selectedYear,
        ];

        $rencanaList = $query->paginate(10)->withQueryString();
        $availableCategories = RencanaKegiatan::distinct()->pluck('jenis_kegiatan')->filter()->values();

        return Inertia::render('Admin/RencanaKegiatan/Index', [
            'rencanaList' => $rencanaList,
            'stats' => $stats,
            'categories' => $availableCategories,
            'filters' => $request->only(['search', 'status', 'jenis', 'year']),
        ]);
    }

    public function create(): Response
    {
        $rencana = new RencanaKegiatan([
            'tanggal_rencana' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'draft',
            'jenis_kegiatan' => 'SPM',
        ]);
        $detail = new DetailKegiatan([
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '15:00',
            'target_peserta' => 100,
            'anggaran' => 10000000,
        ]);

        return Inertia::render('Admin/RencanaKegiatan/Create', [
            'rencana' => $rencana,
            'detail' => $detail,
            'descriptionImageUploadUrl' => route('admin.description-images.store'),
            'categories' => ['SPM', 'Sosialisasi', 'Workshop', 'Edukasi', 'Webinar'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'tanggal_rencana' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:255'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'target_peserta' => ['nullable', 'integer', 'min:1'],
            'anggaran' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,diajukan'],
        ]);

        if (isset($validated['deskripsi'])) {
            $validated['deskripsi'] = $this->descriptionSanitizer->sanitize($validated['deskripsi']);
        }

        $adminId = Auth::guard('admin')->id();
        $status = $validated['status'] ?? 'draft';

        $rencana = DB::transaction(function () use ($validated, $adminId, $status) {
            $rencana = RencanaKegiatan::create([
                'admin_id' => $adminId,
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'jenis_kegiatan' => $validated['jenis_kegiatan'],
                'tanggal_rencana' => $validated['tanggal_rencana'],
                'status' => $status,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DetailKegiatan::create([
                'rencana_kegiatan_id' => $rencana->id,
                'deskripsi' => $validated['deskripsi'] ?? ('Pelaksanaan program '.$validated['nama_kegiatan']),
                'jenis_kegiatan' => $validated['jenis_kegiatan'],
                'tanggal' => $validated['tanggal_rencana'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'lokasi' => $validated['lokasi'],
                'target_peserta' => $validated['target_peserta'] ?? 100,
                'anggaran' => $validated['anggaran'],
            ]);

            if ($status === 'diajukan') {
                PengajuanKegiatan::create([
                    'rencana_kegiatan_id' => $rencana->id,
                    'admin_id' => $adminId,
                    'nomor_pengajuan' => 'PROP-'.date('Ymd').'-'.sprintf('%03d', $rencana->id),
                    'tanggal_pengajuan' => now()->toDateString(),
                    'status' => 'diajukan',
                    'catatan' => 'Diajukan saat pembuatan rencana kegiatan & anggaran.',
                ]);
            }

            return $rencana;
        });

        return redirect()->route('admin.rencana-kegiatan.index')
            ->with('success', "Rencana Anggaran \"{$rencana->nama_kegiatan}\" berhasil dibuat.");
    }

    public function show(RencanaKegiatan $rencana_kegiatan): Response
    {
        $rencana_kegiatan->load(['detailKegiatan', 'pengajuanKegiatan.admin', 'kegiatan', 'admin']);

        if ($rencana_kegiatan->detailKegiatan) {
            $rencana_kegiatan->detailKegiatan->setAttribute(
                'deskripsi_html',
                $this->descriptionSanitizer->sanitize($rencana_kegiatan->detailKegiatan->deskripsi),
            );
        }

        return Inertia::render('Admin/RencanaKegiatan/Show', [
            'rencana' => $rencana_kegiatan,
            'detail' => $rencana_kegiatan->detailKegiatan,
            'pengajuan' => $rencana_kegiatan->pengajuanKegiatan->sortByDesc('created_at')->first(),
        ]);
    }

    public function edit(RencanaKegiatan $rencana_kegiatan): Response
    {
        $rencana_kegiatan->load('detailKegiatan');

        if ($rencana_kegiatan->detailKegiatan) {
            $rencana_kegiatan->detailKegiatan->deskripsi = $this->descriptionSanitizer->sanitize($rencana_kegiatan->detailKegiatan->deskripsi);
        }

        return Inertia::render('Admin/RencanaKegiatan/Edit', [
            'rencana' => $rencana_kegiatan,
            'detail' => $rencana_kegiatan->detailKegiatan ?? new DetailKegiatan,
            'descriptionImageUploadUrl' => route('admin.description-images.store'),
            'categories' => ['SPM', 'Sosialisasi', 'Workshop', 'Edukasi', 'Webinar'],
        ]);
    }

    public function update(Request $request, RencanaKegiatan $rencana_kegiatan)
    {
        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'tanggal_rencana' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:255'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'target_peserta' => ['nullable', 'integer', 'min:1'],
            'anggaran' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,diajukan,disetujui,ditolak'],
        ]);

        if (isset($validated['deskripsi'])) {
            $validated['deskripsi'] = $this->descriptionSanitizer->sanitize($validated['deskripsi']);
        }

        DB::transaction(function () use ($validated, $rencana_kegiatan) {
            $rencana_kegiatan->update([
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'jenis_kegiatan' => $validated['jenis_kegiatan'],
                'tanggal_rencana' => $validated['tanggal_rencana'],
                'status' => $validated['status'] ?? $rencana_kegiatan->status,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $rencana_kegiatan->detailKegiatan()->updateOrCreate(
                ['rencana_kegiatan_id' => $rencana_kegiatan->id],
                [
                    'deskripsi' => $validated['deskripsi'] ?? ('Pelaksanaan program '.$validated['nama_kegiatan']),
                    'jenis_kegiatan' => $validated['jenis_kegiatan'],
                    'tanggal' => $validated['tanggal_rencana'],
                    'waktu_mulai' => $validated['waktu_mulai'],
                    'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                    'lokasi' => $validated['lokasi'],
                    'target_peserta' => $validated['target_peserta'] ?? 100,
                    'anggaran' => $validated['anggaran'],
                ]
            );
        });

        return redirect()->route('admin.rencana-kegiatan.index')
            ->with('success', "Rencana Anggaran \"{$rencana_kegiatan->nama_kegiatan}\" berhasil diperbarui.");
    }

    public function destroy(RencanaKegiatan $rencana_kegiatan)
    {
        $nama = $rencana_kegiatan->nama_kegiatan;
        $rencana_kegiatan->delete();

        return redirect()->route('admin.rencana-kegiatan.index')
            ->with('success', "Rencana Anggaran \"{$nama}\" berhasil dihapus.");
    }

    public function ajukan(RencanaKegiatan $rencana_kegiatan)
    {
        $adminId = Auth::guard('admin')->id();

        DB::transaction(function () use ($rencana_kegiatan, $adminId) {
            $rencana_kegiatan->update(['status' => 'diajukan']);

            PengajuanKegiatan::firstOrCreate(
                ['rencana_kegiatan_id' => $rencana_kegiatan->id],
                [
                    'admin_id' => $adminId,
                    'nomor_pengajuan' => 'PROP-'.date('Ymd').'-'.sprintf('%03d', $rencana_kegiatan->id),
                    'tanggal_pengajuan' => now()->toDateString(),
                    'status' => 'diajukan',
                    'catatan' => 'Diajukan untuk peninjauan dan persetujuan anggaran.',
                ]
            );
        });

        return back()->with('success', "Rencana \"{$rencana_kegiatan->nama_kegiatan}\" berhasil diajukan untuk review anggaran.");
    }

    public function export(Request $request): StreamedResponse
    {
        $query = RencanaKegiatan::with('detailKegiatan', 'admin')->latest('tanggal_rencana');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $items = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="rencana-anggaran-sipena-'.date('Ymd-His').'.csv"',
        ];

        return response()->stream(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Kode Rencana',
                'Nama Kegiatan',
                'Jenis Kegiatan',
                'Tanggal Pelaksanaan',
                'Lokasi',
                'Target Peserta',
                'Alokasi Anggaran (Rp)',
                'Status',
                'PIC Pembuat',
            ]);

            foreach ($items as $r) {
                fputcsv($handle, [
                    $r->kode,
                    $r->nama_kegiatan,
                    $r->jenis_kegiatan,
                    $r->tanggal_rencana?->format('d/m/Y') ?? '-',
                    $r->detailKegiatan?->lokasi ?? '-',
                    $r->detailKegiatan?->target_peserta ?? 0,
                    $r->detailKegiatan?->anggaran ?? 0,
                    $r->status_label,
                    $r->admin?->name ?? 'Admin BEI',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
