<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::query()
            ->with('rencanaKegiatan.detailKegiatan', 'rencanaKegiatan.admin')
            ->withCount('peserta')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('nama_kegiatan', 'like', "%{$s}%")
                ->orWhere('lokasi', 'like', "%{$s}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->kategori, fn ($q, $k) => $q->where('jenis_kegiatan', $k))
            // berjalan & mendatang first, selesai & dibatalkan last
            ->orderByRaw("CASE WHEN status IN ('selesai','dibatalkan') THEN 1 ELSE 0 END")
            ->orderByDesc('tanggal');

        if ($request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $year = Kegiatan::whereYear('tanggal', now()->year)->exists() ? now()->year : 2025;
        $month = now()->year == $year ? now()->month : 2;
        $bulanIni = Kegiatan::whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        $stats = [
            'year' => $year,
            'total' => Kegiatan::whereYear('tanggal', $year)->count(),
            'rencana' => RencanaKegiatan::whereYear('tanggal_rencana', $year)->count(),
            'bulan_ini' => (clone $bulanIni)->count(),
            'lokasi_bulan_ini' => (clone $bulanIni)->distinct()->count('lokasi'),
            'selesai' => Kegiatan::where('status', 'selesai')->count(),
            'lpj' => Kegiatan::where('status', 'selesai')->has('laporanKegiatan')->count(),
            'menunggu' => Kegiatan::where('status', 'menunggu_approval')->count(),
        ];

        return view('admin.kegiatan.index', [
            'kegiatan' => $query->paginate(10)->withQueryString(),
            'stats' => $stats,
            'kategori' => Kegiatan::distinct()->orderBy('jenis_kegiatan')->pluck('jenis_kegiatan'),
        ]);
    }

    public function create()
    {
        return view('admin.kegiatan.create', $this->formOptions(new Kegiatan(['status' => 'menunggu_approval'])));
    }

    public function store(Request $request)
    {
        Kegiatan::create($this->validated($request));

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load('rencanaKegiatan.detailKegiatan', 'pengajuanKegiatan', 'agenda', 'peserta.user', 'laporanKegiatan');

        return view('admin.kegiatan.show', compact('kegiatan'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        return view('admin.kegiatan.edit', $this->formOptions($kegiatan));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $kegiatan->update($this->validated($request, $kegiatan));

        return redirect()->route('admin.kegiatan.show', $kegiatan)->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function validated(Request $request, ?Kegiatan $kegiatan = null): array
    {
        return $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'lokasi' => 'required|string|max:255',
            'kuota_peserta' => 'nullable|integer|min:1',
            'status' => ['required', Rule::in(array_keys(Kegiatan::STATUS))],
            'rencana_kegiatan_id' => ['nullable', 'exists:rencana_kegiatan,id', Rule::unique('kegiatan')->ignore($kegiatan)],
            'pengajuan_kegiatan_id' => ['nullable', 'exists:pengajuan_kegiatan,id', Rule::unique('kegiatan')->ignore($kegiatan)],
        ], [], [
            'nama_kegiatan' => 'nama kegiatan',
            'jenis_kegiatan' => 'kategori',
            'waktu_mulai' => 'waktu mulai',
            'waktu_selesai' => 'waktu selesai',
            'kuota_peserta' => 'target peserta',
            'rencana_kegiatan_id' => 'rencana kegiatan',
            'pengajuan_kegiatan_id' => 'pengajuan',
        ]);
    }

    private function formOptions(Kegiatan $kegiatan): array
    {
        return [
            'kegiatan' => $kegiatan,
            'rencanaKegiatan' => RencanaKegiatan::orderByDesc('tanggal_rencana')->get(['id', 'nama_kegiatan']),
            'pengajuanKegiatan' => PengajuanKegiatan::with('rencanaKegiatan:id,nama_kegiatan')->where('status', 'disetujui')->get(),
            'kategori' => Kegiatan::distinct()->orderBy('jenis_kegiatan')->pluck('jenis_kegiatan'),
        ];
    }

    private function exportCsv($rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Kode', 'Nama Kegiatan', 'Kategori', 'Tanggal', 'Waktu', 'Lokasi', 'Target Peserta', 'Peserta', 'Status']);
            foreach ($rows as $k) {
                fputcsv($out, [$k->kode, $k->nama_kegiatan, $k->jenis_kegiatan, $k->tanggal->format('Y-m-d'),
                    substr($k->waktu_mulai, 0, 5), $k->lokasi, $k->kuota_peserta, $k->peserta_count, $k->status_label]);
            }
            fclose($out);
        }, 'jadwal-kegiatan-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }
}
