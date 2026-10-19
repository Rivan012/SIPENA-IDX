<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AktivitasController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = Auth::guard('web')->id();
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = Aktivitas::query()
            ->with(['kegiatan'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('kegiatan', fn ($kq) => $kq->where('user_id', $userId));
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('aksi', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['created_at', 'aksi'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $stats = [
            'total' => (clone $query)->count(),
            'proposal' => Aktivitas::where('user_id', $userId)->where('aksi', 'like', '%proposal%')->count(),
            'approval' => Aktivitas::whereHas('kegiatan', fn ($q) => $q->where('user_id', $userId))->where('aksi', 'approval_proposal')->count(),
        ];

        return Inertia::render('User/Aktivitas/Index', [
            'aktivitas' => $query->paginate(15)->withQueryString(),
            'stats' => $stats,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }
}
