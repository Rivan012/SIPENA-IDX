<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::guard('web')->user();

        $kegiatanDiikuti = $user ? $user->kegiatan()->count() : 0;
        $kegiatanHadir = $user ? $user->kegiatan()->wherePivot('status', 'hadir')->count() : 0;

        $upcomingEvents = Kegiatan::whereIn('status', ['dipublikasikan', 'berlangsung'])
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->take(4)
            ->get();

        $myRecentEvents = $user ? $user->kegiatan()->orderBy('tanggal', 'desc')->take(3)->get() : collect();

        return view('user.dashboard', [
            'user' => $user,
            'kegiatanDiikuti' => $kegiatanDiikuti,
            'kegiatanHadir' => $kegiatanHadir,
            'upcomingEvents' => $upcomingEvents,
            'myRecentEvents' => $myRecentEvents,
        ]);
    }
}
