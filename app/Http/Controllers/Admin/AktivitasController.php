<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AktivitasController extends Controller
{
    public function index(Request $request): Response
    {
        if ($request->get('export') === 'pdf' || $request->get('export') === 'csv') {
            return $this->export($request);
        }

        $search = $request->get('search');
        $role = $request->get('role');
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = Aktivitas::query()->with(['kegiatan', 'user', 'admin']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('aktor_nama', 'like', "%{$search}%")
                    ->orWhere('aksi', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['admin', 'mitra', 'sistem'], true)) {
            $query->where('aktor_role', $role);
        }

        $allowedSorts = ['created_at', 'aktor_nama', 'aksi'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $stats = [
            'total' => Aktivitas::count(),
            'admin' => Aktivitas::where('aktor_role', 'admin')->count(),
            'mitra' => Aktivitas::where('aktor_role', 'mitra')->count(),
            'today' => Aktivitas::whereDate('created_at', today())->count(),
        ];

        return Inertia::render('Admin/Aktivitas/Index', [
            'aktivitas' => $query->paginate(15)->withQueryString(),
            'stats' => $stats,
            'filters' => $request->only(['search', 'role', 'sort', 'direction']),
        ]);
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = Aktivitas::query()->with(['kegiatan', 'user', 'admin']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('aktor_nama', 'like', "%{$search}%")
                    ->orWhere('aksi', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['admin', 'mitra', 'sistem'], true)) {
            $query->where('aktor_role', $role);
        }

        $allowedSorts = ['created_at', 'aktor_nama', 'aksi'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $items = $query->get();

        $stats = [
            'total' => $items->count(),
            'admin' => $items->where('aktor_role', 'admin')->count(),
            'mitra' => $items->where('aktor_role', 'mitra')->count(),
            'today' => $items->filter(fn ($a) => $a->created_at && $a->created_at->isToday())->count(),
        ];

        $filters = $request->only(['search', 'role']);
        $printedBy = auth('admin')->user()?->name ?? 'Administrator KP BEI Bengkulu';

        return Pdf::loadView('pdf.aktivitas', [
            'items' => $items,
            'stats' => $stats,
            'filters' => $filters,
            'printedBy' => $printedBy,
        ])
            ->setPaper('a4', 'landscape')
            ->download('log-aktivitas-'.date('Ymd-His').'.pdf');
    }
}
