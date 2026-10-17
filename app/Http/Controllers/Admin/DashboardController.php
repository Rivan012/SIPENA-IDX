<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Peserta;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalKegiatan = Kegiatan::count();
        $kegiatanTerlaksana = Kegiatan::where('status', 'terlaksana')->count();
        $totalPeserta = Peserta::count();

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

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_kegiatan' => $totalKegiatan,
                'kegiatan_terlaksana' => $kegiatanTerlaksana,
                'total_peserta' => $totalPeserta,
            ],
            'kegiatanTerbaru' => $kegiatanTerbaru,
            'proposalMenungguReview' => $proposalMenungguReview,
        ]);
    }
}
