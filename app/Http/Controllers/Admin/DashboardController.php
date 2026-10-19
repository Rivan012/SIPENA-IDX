<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalKegiatan = Kegiatan::publicSchedule()->count();
        $kegiatanTerlaksana = Kegiatan::where('status', 'selesai')->count();
        $totalMitra = User::count();

        $kegiatanTerbaru = Kegiatan::publicSchedule()
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
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

        $proposalMenungguReview = Kegiatan::query()
            ->with('user')
            ->whereNotNull('user_id')
            ->whereIn('status', ['menunggu_approval', 'diproses'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Kegiatan $kegiatan) => [
                'id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'jenis_kegiatan' => $kegiatan->jenis_kegiatan,
                'pengusul' => $kegiatan->user?->name,
                'status' => $kegiatan->status,
                'status_label' => $kegiatan->status_label,
                'status_class' => $kegiatan->status_class,
            ]);

        $tahun = (int) request('tahun', now()->year);
        $allKegiatan = Kegiatan::all();

        // Filter data kegiatan sesuai tahun aktif
        $kegiatanTahunIni = $allKegiatan->filter(function ($k) use ($tahun) {
            $date = $k->tanggal ?: $k->created_at;

            return $date && (int) $date->format('Y') === $tahun;
        });

        // 12 Bulan komparasi: Diusulkan vs Terlaksana dari database
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $bulanStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanStats[] = [
                'nama' => $namaBulan[$i - 1],
                'bulan' => $i,
                'diusulkan' => 0,
                'terlaksana' => 0,
                'target' => 0,
            ];
        }

        foreach ($kegiatanTahunIni as $k) {
            $date = $k->tanggal ?: $k->created_at;
            $m = (int) $date->format('n');
            if ($m >= 1 && $m <= 12) {
                // Diusulkan: total proposal / kegiatan terdaftar
                $bulanStats[$m - 1]['diusulkan']++;
                // Terlaksana: status selesai
                if ($k->status === 'selesai') {
                    $bulanStats[$m - 1]['terlaksana']++;
                }
            }
        }

        // Distribusi Kategori / Modul kegiatan dari database
        $kategoriColors = ['#0052cc', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];
        $kategoriList = [];
        $kategoriGroups = $kegiatanTahunIni->groupBy(fn ($k) => $k->jenis_kegiatan ?: 'Lainnya');
        $totalKegiatanCount = max(1, $kegiatanTahunIni->count());
        $colorIdx = 0;

        foreach ($kategoriGroups as $jenis => $items) {
            $count = $items->count();
            $percentage = round(($count / $totalKegiatanCount) * 100, 1);
            $totalKuota = $items->sum(fn ($k) => (int) ($k->kuota_peserta ?: 0));

            $kategoriList[] = [
                'label' => $jenis,
                'count' => $count,
                'persen' => $percentage.'%',
                'persen_num' => $percentage,
                'kuota' => $totalKuota,
                'warna' => $kategoriColors[$colorIdx % count($kategoriColors)],
            ];
            $colorIdx++;
        }

        if (empty($kategoriList)) {
            $kategoriList = [
                ['label' => 'Sekolah Pasar Modal (SPM)', 'count' => 0, 'persen' => '0%', 'persen_num' => 0, 'kuota' => 0, 'warna' => '#0052cc'],
                ['label' => 'Sosialisasi & Edukasi', 'count' => 0, 'persen' => '0%', 'persen_num' => 0, 'kuota' => 0, 'warna' => '#10b981'],
                ['label' => 'Workshop & Analisis', 'count' => 0, 'persen' => '0%', 'persen_num' => 0, 'kuota' => 0, 'warna' => '#f59e0b'],
                ['label' => 'Literasi Publik & Lainnya', 'count' => 0, 'persen' => '0%', 'persen_num' => 0, 'kuota' => 0, 'warna' => '#85131B'],
            ];
        }

        $totalDiusulkan = $kegiatanTahunIni->count();
        $totalTerlaksana = $kegiatanTahunIni->where('status', 'selesai')->count();
        $totalTerjadwal = $kegiatanTahunIni->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung'])->count();
        $totalMenunggu = $kegiatanTahunIni->whereIn('status', ['menunggu_approval', 'diproses'])->count();
        $persenTerlaksana = $totalDiusulkan > 0 ? round(($totalTerlaksana / $totalDiusulkan) * 100, 1).'%' : '0%';

        $rkapChart = [
            'tahun' => $tahun,
            'bulan' => $bulanStats,
            'kategori' => $kategoriList,
            'summary' => [
                'totalDiusulkan' => $totalDiusulkan,
                'totalTerlaksana' => $totalTerlaksana,
                'totalTerjadwal' => $totalTerjadwal,
                'totalMenunggu' => $totalMenunggu,
                'persenTerlaksana' => $persenTerlaksana,
            ],
            'demografis' => $kategoriList,
        ];

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_kegiatan' => $totalKegiatan,
                'kegiatan_terlaksana' => $kegiatanTerlaksana,
                'total_mitra' => $totalMitra,
                'total_peserta' => $totalMitra,
            ],
            'kegiatanTerbaru' => $kegiatanTerbaru,
            'proposalMenungguReview' => $proposalMenungguReview,
            'rkapChart' => $rkapChart,
        ]);
    }
}
