<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KegiatanSayaController extends Controller
{
    public function index(): Response
    {
        $user = Auth::guard('web')->user();

        $kegiatanList = $user->kegiatanDiajukan()
            ->with(['agenda'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return Inertia::render('User/KegiatanSaya', [
            'kegiatanList' => $kegiatanList,
        ]);
    }
}
