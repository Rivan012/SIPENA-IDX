<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::guard('web')->user();

        $proposals = $user->kegiatanDiajukan();

        return Inertia::render('User/Dashboard', [
            'proposedCount' => (clone $proposals)->where('status', 'menunggu_approval')->count(),
            'otherStatusCount' => (clone $proposals)->whereNotIn('status', ['menunggu_approval', 'selesai'])->count(),
            'completedCount' => (clone $proposals)->where('status', 'selesai')->count(),
            'recentNotifications' => $user->notifikasi()
                ->latest('id')
                ->take(5)
                ->get(),
        ]);
    }
}
