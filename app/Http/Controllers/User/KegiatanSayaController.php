<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanSayaController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();

        $kegiatanList = $user->kegiatan()
            ->with(['agenda'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('user.kegiatan-saya', [
            'kegiatanList' => $kegiatanList,
        ]);
    }
}
