<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LaporanKegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function index(): Response
    {
        $laporanList = LaporanKegiatan::with('kegiatan')
            ->latest('tanggal_laporan')
            ->paginate(10);

        return Inertia::render('User/Laporan/Index', [
            'laporanList' => $laporanList,
        ]);
    }

    public function show($laporan): Response
    {
        $item = LaporanKegiatan::with(['kegiatan', 'admin'])->findOrFail($laporan);

        return Inertia::render('User/Laporan/Show', [
            'laporan' => $item,
        ]);
    }
}
