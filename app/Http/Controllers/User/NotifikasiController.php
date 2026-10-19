<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NotifikasiController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::guard('web')->id();

        $notifikasi = Notifikasi::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere(function ($sub) {
                      $sub->whereNull('user_id')->whereNull('admin_id');
                  });
            })
            ->latest()
            ->paginate(15);

        return Inertia::render('User/Notifikasi/Index', [
            'notifikasi' => $notifikasi,
        ]);
    }

    public function markAsRead($notifikasi)
    {
        $userId = Auth::guard('web')->id();
        $item = Notifikasi::where('id', $notifikasi)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere(function ($sub) {
                      $sub->whereNull('user_id')->whereNull('admin_id');
                  });
            })
            ->firstOrFail();

        $item->update([
            'is_read' => true,
            'dibaca_at' => now(),
        ]);

        return back()->with('success', 'Notifikasi telah ditandai sudah dibaca.');
    }
}
