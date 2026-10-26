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

        $proposedCount = (clone $proposals)->whereIn('status', ['menunggu_approval', 'diproses'])->count();
        $approvedCount = (clone $proposals)->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung'])->count();
        $revisionCount = (clone $proposals)->where('status', 'revisi')->count();
        $rejectedCount = (clone $proposals)->where('status', 'ditolak')->count();
        $completedCount = (clone $proposals)->whereIn('status', ['selesai', 'dibatalkan'])->count();
        $totalCount = (clone $proposals)->count();

        return Inertia::render('User/Dashboard', [
            'proposedCount' => $proposedCount,
            'approvedCount' => $approvedCount,
            'revisionCount' => $revisionCount,
            'rejectedCount' => $rejectedCount,
            'completedCount' => $completedCount,
            'otherStatusCount' => $approvedCount,
            'totalProposalsCount' => $totalCount,
            'recentNotifications' => $user->notifikasi()
                ->latest('id')
                ->take(5)
                ->get(),
        ]);
    }
}
