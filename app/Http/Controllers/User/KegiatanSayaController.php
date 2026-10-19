<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KegiatanSayaController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::guard('web')->user();

        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['nama_kegiatan', 'tanggal', 'lokasi', 'status'];

        $query = $user->kegiatanDiajukan()->with(['agenda', 'reviewedByAdmin']);

        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('tanggal', 'desc');
        }

        $kegiatanList = $query->paginate(10)->withQueryString();

        return Inertia::render('User/KegiatanSaya', [
            'kegiatanList' => $kegiatanList,
            'filters' => $request->only(['sort', 'direction']),
        ]);
    }
}
