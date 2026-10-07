<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailKegiatan;
use App\Models\RencanaKegiatan;
use Illuminate\Http\Request;

class DetailKegiatanController extends Controller
{
    public function store(Request $request, $rencana)
    {
        $rencanaModel = RencanaKegiatan::findOrFail($rencana);

        $validated = $request->validate([
            'deskripsi' => ['required', 'string'],
            'jenis_kegiatan' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'lokasi' => ['required', 'string', 'max:255'],
            'target_peserta' => ['nullable', 'integer', 'min:1'],
            'anggaran' => ['required', 'numeric', 'min:0'],
        ]);

        $detail = $rencanaModel->detailKegiatan()->create($validated);

        return back()->with('success', 'Rincian detail pos anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, $rencana)
    {
        $rencanaModel = RencanaKegiatan::findOrFail($rencana);

        $validated = $request->validate([
            'deskripsi' => ['required', 'string'],
            'jenis_kegiatan' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'lokasi' => ['required', 'string', 'max:255'],
            'target_peserta' => ['nullable', 'integer', 'min:1'],
            'anggaran' => ['required', 'numeric', 'min:0'],
        ]);

        $detail = $rencanaModel->detailKegiatan()->updateOrCreate(
            ['rencana_kegiatan_id' => $rencanaModel->id],
            $validated
        );

        return back()->with('success', 'Rincian detail pos anggaran berhasil diperbarui.');
    }
}
