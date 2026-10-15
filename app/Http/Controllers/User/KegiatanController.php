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
    public function create(): Response
    {
        return Inertia::render('User/Kegiatan/Create', [
            'kegiatan' => [
                'nama_kegiatan' => '',
                'deskripsi' => '',
                'jenis_kegiatan' => 'SPM',
                'tanggal' => now()->addDays(7)->format('Y-m-d'),
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '12:00',
                'lokasi' => '',
                'mitra' => Auth::guard('web')->user()?->name,
                'kuota_peserta' => 80,
            ],
        ]);
    }

    public function storeProposal(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'lokasi' => ['required', 'string', 'max:255'],
            'mitra' => ['nullable', 'string', 'max:255'],
            'kuota_peserta' => ['nullable', 'integer', 'min:1'],
        ]);

        Kegiatan::create([
            'user_id' => Auth::guard('web')->id(),
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'deskripsi' => $validated['deskripsi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'] ?? null,
            'lokasi' => $validated['lokasi'],
            'mitra' => $validated['mitra'] ?? Auth::guard('web')->user()?->name,
            'kuota_peserta' => $validated['kuota_peserta'] ?? 80,
            'status' => 'menunggu_approval',
        ]);

        return redirect()->route('user.kegiatan.index')->with('success', 'Proposal kegiatan Anda berhasil dikirim dan menunggu review admin.');
    }

    public function index(Request $request): Response
    {
        $query = Kegiatan::query()
            ->with(['agenda', 'peserta'])
            ->whereIn('status', ['menunggu_approval', 'dipublikasikan', 'berlangsung', 'selesai'])
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
        $item = Kegiatan::with(['agenda', 'peserta', 'user'])->findOrFail($kegiatan);
        $user = Auth::guard('web')->user();
        $isRegistered = $user ? $item->peserta()->where('user_id', $user->id)->exists() : false;
        $isOwner = $user ? (int) $item->user_id === (int) $user->id : false;

        return Inertia::render('User/Kegiatan/Show', [
            'kegiatan' => $item,
            'isRegistered' => $isRegistered,
            'isOwner' => $isOwner,
        ]);
    }

    public function edit(Kegiatan $kegiatan): Response
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);

        return Inertia::render('User/Kegiatan/Edit', [
            'kegiatan' => $kegiatan,
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);

        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'lokasi' => ['required', 'string', 'max:255'],
            'mitra' => ['nullable', 'string', 'max:255'],
            'kuota_peserta' => ['nullable', 'integer', 'min:1'],
        ]);

        $kegiatan->update([
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'deskripsi' => $validated['deskripsi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'] ?? null,
            'lokasi' => $validated['lokasi'],
            'mitra' => $validated['mitra'] ?? $kegiatan->mitra,
            'kuota_peserta' => $validated['kuota_peserta'] ?? $kegiatan->kuota_peserta,
        ]);

        return redirect()->route('user.kegiatan.show', $kegiatan)->with('success', 'Proposal kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);

        $kegiatan->delete();

        return redirect()->route('user.kegiatan-saya')->with('success', 'Proposal kegiatan berhasil dihapus.');
    }

    public function cancel(Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);

        $kegiatan->update(['status' => 'dibatalkan']);

        return redirect()->route('user.kegiatan.show', $kegiatan)->with('success', 'Proposal kegiatan berhasil dibatalkan.');
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
