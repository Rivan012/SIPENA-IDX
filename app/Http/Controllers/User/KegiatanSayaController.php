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

        $statusFilter = $request->get('status');
        if ($statusFilter) {
            if ($statusFilter === 'menunggu_approval') {
                $query->whereIn('status', ['menunggu_approval', 'diproses']);
            } elseif ($statusFilter === 'disetujui') {
                $query->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung']);
            } elseif ($statusFilter === 'revisi') {
                $query->where('status', 'revisi');
            } elseif ($statusFilter === 'ditolak') {
                $query->where('status', 'ditolak');
            } elseif ($statusFilter === 'selesai') {
                $query->whereIn('status', ['selesai', 'dibatalkan']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('jenis_kegiatan', 'like', "%{$search}%");
            });
        }

        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('tanggal', 'desc');
        }

        $kegiatanList = $query->paginate(10)->withQueryString();

        $baseQuery = $user->kegiatanDiajukan();
        $counts = [
            'semua' => (clone $baseQuery)->count(),
            'menunggu_approval' => (clone $baseQuery)->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'disetujui' => (clone $baseQuery)->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung'])->count(),
            'revisi' => (clone $baseQuery)->where('status', 'revisi')->count(),
            'ditolak' => (clone $baseQuery)->where('status', 'ditolak')->count(),
            'selesai' => (clone $baseQuery)->whereIn('status', ['selesai', 'dibatalkan'])->count(),
        ];

        return Inertia::render('User/KegiatanSaya', [
            'kegiatanList' => $kegiatanList,
            'counts' => $counts,
            'filters' => $request->only(['sort', 'direction', 'status', 'search']),
        ]);
    }
}
