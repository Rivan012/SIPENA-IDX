<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LaporanKegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['judul_laporan', 'tanggal_laporan', 'total_peserta_hadir'];

        $query = LaporanKegiatan::with('kegiatan');

        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('tanggal_laporan');
        }

        $laporanList = $query->paginate(10)->withQueryString();

        return Inertia::render('User/Laporan/Index', [
            'laporanList' => $laporanList,
            'filters' => $request->only(['sort', 'direction']),
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
