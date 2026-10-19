<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Peserta;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('export') === 'pdf' || $request->get('export') === 'csv') {
            return $this->export($request);
        }

        $search = $request->get('search');
        $role = $request->get('role', 'all');

        $totalUsers = User::count();
        $totalAdmins = Admin::count();
        $totalPartisipasi = Peserta::count();
        $totalHadir = Peserta::where('status', 'hadir')->count();

        $stats = [
            'total_users' => $totalUsers,
            'total_admins' => $totalAdmins,
            'total_partisipasi' => $totalPartisipasi,
            'total_hadir' => $totalHadir,
        ];

        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $userQuery = User::query()->with(['kegiatan'])->withCount('kegiatan');

        if ($search) {
            $userQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        if ($sort === 'kegiatan_count') {
            $userQuery->orderBy('kegiatan_count', $direction);
        } elseif (in_array($sort, ['name', 'email', 'created_at'], true)) {
            $userQuery->orderBy($sort, $direction);
        } else {
            $userQuery->latest();
        }

        $users = $userQuery->paginate(10, ['*'], 'users_page')->withQueryString();

        $adminQuery = Admin::query()->latest();
        if ($search) {
            $adminQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }
        $admins = $adminQuery->get();

        return Inertia::render('Admin/Monitoring/Index', [
            'users' => $users,
            'admins' => $admins,
            'stats' => $stats,
            'role' => $role,
            'filters' => $request->only(['search', 'role', 'sort', 'direction']),
        ]);
    }

    public function export(Request $request)
    {
        $role = $request->get('role', 'users');
        $search = $request->get('search');
        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $printedBy = auth('admin')->user()?->name ?? 'Administrator KP BEI Bengkulu';

        if ($role === 'admins') {
            $adminQuery = Admin::query();
            if ($search) {
                $adminQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%")
                        ->orWhere('no_telepon', 'like', "%{$search}%");
                });
            }

            if ($sort && in_array($sort, ['name', 'email', 'jabatan', 'created_at'], true)) {
                $adminQuery->orderBy($sort, $direction);
            } else {
                $adminQuery->latest();
            }

            $admins = $adminQuery->get();
            $stats = ['total' => $admins->count()];

            return Pdf::loadView('pdf.pengguna', [
                'role' => 'admins',
                'admins' => $admins,
                'stats' => $stats,
                'filters' => $request->only(['search', 'role']),
                'printedBy' => $printedBy,
            ])
                ->setPaper('a4', 'landscape')
                ->download('data-administrator-'.date('Ymd-His').'.pdf');
        }

        $userQuery = User::with(['kegiatan'])->withCount('kegiatan');

        if ($search) {
            $userQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        if ($sort === 'kegiatan_count') {
            $userQuery->orderBy('kegiatan_count', $direction);
        } elseif (in_array($sort, ['name', 'email', 'created_at'], true)) {
            $userQuery->orderBy($sort, $direction);
        } else {
            $userQuery->latest();
        }

        $users = $userQuery->get();

        $stats = [
            'total' => $users->count(),
            'terverifikasi' => $users->whereNotNull('email_verified_at')->count(),
            'belum_verifikasi' => $users->whereNull('email_verified_at')->count(),
            'total_partisipasi' => $users->sum('kegiatan_count'),
        ];

        return Pdf::loadView('pdf.pengguna', [
            'role' => 'users',
            'users' => $users,
            'stats' => $stats,
            'filters' => $request->only(['search', 'role']),
            'printedBy' => $printedBy,
        ])
            ->setPaper('a4', 'landscape')
            ->download('data-mitra-'.date('Ymd-His').'.pdf');
    }
}
