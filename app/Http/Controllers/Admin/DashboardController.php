<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailKegiatan;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\Peserta;
use App\Models\RencanaKegiatan;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalKegiatan = Kegiatan::count();
        $kegiatanTerlaksana = Kegiatan::where('status', 'terlaksana')->count();
        $totalPeserta = Peserta::count();
        $paguAnggaran = DetailKegiatan::sum('anggaran') ?: 250000000;
        $realisasiAnggaran = DetailKegiatan::whereHas('rencanaKegiatan.kegiatan', fn ($q) => $q->where('status', 'terlaksana'))->sum('anggaran') ?: 145000000;
        $persentaseRealisasi = $paguAnggaran > 0 ? round(($realisasiAnggaran / $paguAnggaran) * 100, 1) : 0;

        $kegiatanTerbaru = Kegiatan::orderByDesc('tanggal')
            ->take(5)
            ->get()
            ->map(fn ($k) => [
                'id' => $k->id,
                'kode' => $k->kode,
                'nama_kegiatan' => $k->nama_kegiatan,
                'jenis_kegiatan' => $k->jenis_kegiatan,
                'tanggal' => $k->tanggal?->format('d M Y'),
                'lokasi' => $k->lokasi,
                'status' => $k->status,
                'status_label' => $k->status_label,
                'status_class' => $k->status_class,
            ]);

        $pengajuanTerbaru = PengajuanKegiatan::with('rencanaKegiatan')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nomor_pengajuan' => $p->nomor_pengajuan,
                'nama_kegiatan' => $p->nama_kegiatan,
                'jenis_kegiatan' => $p->jenis_kegiatan,
                'status' => $p->status,
                'status_label' => $p->status_label,
                'status_class' => $p->status_class,
                'anggaran' => $p->anggaran,
                'formatted_anggaran' => $p->formatted_anggaran,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_kegiatan' => $totalKegiatan,
                'kegiatan_terlaksana' => $kegiatanTerlaksana,
                'total_peserta' => $totalPeserta,
                'pagu_anggaran' => 'Rp ' . number_format($paguAnggaran, 0, ',', '.'),
                'realisasi_anggaran' => 'Rp ' . number_format($realisasiAnggaran, 0, ',', '.'),
                'persentase_realisasi' => $persentaseRealisasi,
            ],
            'kegiatanTerbaru' => $kegiatanTerbaru,
            'pengajuanTerbaru' => $pengajuanTerbaru,
        ]);
    }
}
