<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $admin = auth('admin')->user();
        $user = auth('web')->user();

        return [
            ...parent::share($request),
            'auth' => [
                'admin' => $admin ? [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'jabatan' => $admin->jabatan,
                ] : null,
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_telepon' => $user->no_telepon,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'pendingApprovalCount' => fn () => rescue(fn () => \App\Models\PengajuanKegiatan::whereIn('status', ['diajukan', 'diproses'])->count(), 0, false),
            'userUnreadCount' => fn () => rescue(fn () => $user ? $user->notifikasi()->where('is_read', false)->count() : 0, 0, false),
        ];
    }
}
