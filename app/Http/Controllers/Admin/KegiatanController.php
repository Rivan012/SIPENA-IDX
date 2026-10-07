<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::with(['pengajuanKegiatan', 'rencanaKegiatan', 'peserta']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($jenis = $request->get('jenis_kegiatan')) {
            $query->where('jenis_kegiatan', $jenis);
        }

        $kegiatan = $query->latest()->paginate(10)->withQueryString();

        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function create()
    {
        $rencanaKegiatan = RencanaKegiatan::whereDoesntHave('kegiatan')->get();
        $pengajuanKegiatan = PengajuanKegiatan::where('status', 'disetujui')->whereDoesntHave('kegiatan')->get();

        return view('admin.kegiatan.create', compact('rencanaKegiatan', 'pengajuanKegiatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'lokasi' => 'required|string|max:255',
            'kuota_peserta' => 'nullable|integer|min:1',
            'status' => 'required|string|in:draft,dipublikasikan,berlangsung,selesai,dibatalkan',
            'pengajuan_kegiatan_id' => 'nullable|exists:pengajuan_kegiatan,id',
            'rencana_kegiatan_id' => 'nullable|exists:rencana_kegiatan,id',
        ]);

        Kegiatan::create($validated);

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $kegiatan = Kegiatan::with(['pengajuanKegiatan', 'rencanaKegiatan', 'agenda', 'peserta.user', 'laporanKegiatan'])->findOrFail($id);

        return view('admin.kegiatan.show', compact('kegiatan'));
    }

    public function edit(string $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $rencanaKegiatan = RencanaKegiatan::where(function ($q) use ($kegiatan) {
            $q->whereDoesntHave('kegiatan')->orWhere('id', $kegiatan->rencana_kegiatan_id);
        })->get();
        $pengajuanKegiatan = PengajuanKegiatan::where(function ($q) use ($kegiatan) {
            $q->where('status', 'disetujui')->whereDoesntHave('kegiatan')
              ->orWhere('id', $kegiatan->pengajuan_kegiatan_id);
        })->get();

        return view('admin.kegiatan.edit', compact('kegiatan', 'rencanaKegiatan', 'pengajuanKegiatan'));
    }

    public function update(Request $request, string $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'lokasi' => 'required|string|max:255',
            'kuota_peserta' => 'nullable|integer|min:1',
            'status' => 'required|string|in:draft,dipublikasikan,berlangsung,selesai,dibatalkan',
            'pengajuan_kegiatan_id' => 'nullable|exists:pengajuan_kegiatan,id',
            'rencana_kegiatan_id' => 'nullable|exists:rencana_kegiatan,id',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }
}
