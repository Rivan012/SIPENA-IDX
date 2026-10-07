<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('export') === 'csv') {
            return $this->export($request);
        }

        $search = $request->get('search');
        $role = $request->get('role', 'all');

        // Stats calculation
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

        // Query Users
        $userQuery = User::query()->with(['kegiatan'])->latest();

        if ($search) {
            $userQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        $users = $userQuery->paginate(10, ['*'], 'users_page')->withQueryString();

        // Query Admins
        $adminQuery = Admin::query()->latest();
        if ($search) {
            $adminQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }
        $admins = $adminQuery->get();

        return view('admin.monitoring.index', [
            'users' => $users,
            'admins' => $admins,
            'stats' => $stats,
            'role' => $role,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $users = User::with(['kegiatan'])->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="data-pengguna-peserta-' . date('Ymd-His') . '.csv"',
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, [
                'ID',
                'Nama Lengkap',
                'Email',
                'No. Telepon',
                'Total Kegiatan Diikuti',
                'Terakhir Terdaftar',
                'Tanggal Registrasi',
            ]);

            foreach ($users as $u) {
                $lastKegiatan = $u->kegiatan->sortByDesc('pivot.created_at')->first();
                fputcsv($handle, [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->no_telepon ?? '-',
                    $u->kegiatan->count(),
                    $lastKegiatan ? $lastKegiatan->nama_kegiatan : '-',
                    $u->created_at?->format('d/m/Y H:i') ?? '-',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
