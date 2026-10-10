<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KegiatanController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Kegiatan::query()
            ->with(['agenda', 'peserta'])
            ->whereIn('status', ['dipublikasikan', 'selesai', 'berlangsung'])
            ->latest('tanggal');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->get('jenis')) {
            $query->where('jenis_kegiatan', $jenis);
        }

        $kegiatanList = $query->paginate(9)->withQueryString();
        $categories = Kegiatan::distinct()->pluck('jenis_kegiatan')->filter()->values();

        $userKegiatanIds = Auth::guard('web')->user()?->kegiatan()->pluck('kegiatan.id')->toArray() ?? [];

        return Inertia::render('User/Kegiatan/Index', [
            'kegiatanList' => $kegiatanList,
            'categories' => $categories,
            'userKegiatanIds' => $userKegiatanIds,
            'filters' => $request->only(['search', 'jenis']),
        ]);
    }

    public function show($kegiatan): Response
    {
        $item = Kegiatan::with(['agenda', 'peserta'])->findOrFail($kegiatan);
        $user = Auth::guard('web')->user();
        $isRegistered = $user ? $item->peserta()->where('user_id', $user->id)->exists() : false;

        return Inertia::render('User/Kegiatan/Show', [
            'kegiatan' => $item,
            'isRegistered' => $isRegistered,
        ]);
    }

    public function daftar($kegiatan)
    {
        $item = Kegiatan::findOrFail($kegiatan);
        $user = Auth::guard('web')->user();

        if ($item->peserta()->where('user_id', $user->id)->exists()) {
            return back()->with('info', 'Anda sudah terdaftar pada kegiatan ini.');
        }

        if ($item->kuota_peserta && $item->peserta()->count() >= $item->kuota_peserta) {
            return back()->with('error', 'Mohon maaf, kuota peserta untuk kegiatan ini telah penuh.');
        }

        $item->users()->attach($user->id, ['status' => 'terdaftar']);

        return back()->with('success', "Selamat! Anda berhasil terdaftar pada kegiatan \"{$item->nama_kegiatan}\".");
    }

    public function batal($kegiatan)
    {
        $item = Kegiatan::findOrFail($kegiatan);
        $user = Auth::guard('web')->user();

        $item->users()->detach($user->id);

        return back()->with('success', "Pendaftaran Anda untuk kegiatan \"{$item->nama_kegiatan}\" telah dibatalkan.");
    }
}
