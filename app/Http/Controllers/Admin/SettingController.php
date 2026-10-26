<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\KategoriKegiatan;
use App\Models\Kegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $kategori = KategoriKegiatan::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'keterangan' => $item->keterangan,
                    'is_active' => (bool) $item->is_active,
                    'kegiatan_count' => Kegiatan::where('jenis_kegiatan', $item->nama)->count(),
                    'created_at' => $item->created_at?->format('d/m/Y H:i'),
                ];
            });

        $stats = [
            'total' => KategoriKegiatan::count(),
            'aktif' => KategoriKegiatan::where('is_active', true)->count(),
            'nonaktif' => KategoriKegiatan::where('is_active', false)->count(),
        ];

        return Inertia::render('Admin/Setting/Index', [
            'kategori' => $kategori,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    public function storeKategori(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategori_kegiatan,nama'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah terdaftar.',
            'nama.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $kategori = KategoriKegiatan::create([
            'nama' => trim($validated['nama']),
            'keterangan' => $validated['keterangan'] ?? null,
            'is_active' => true,
        ]);

        Aktivitas::log('tambah_kategori', "Menambahkan kategori kegiatan '{$kategori->nama}'");

        return back()->with('success', "Kategori '{$kategori->nama}' berhasil ditambahkan.");
    }

    public function toggleKategori(KategoriKegiatan $kategori): RedirectResponse
    {
        $kategori->update([
            'is_active' => ! $kategori->is_active,
        ]);

        $statusText = $kategori->is_active ? 'diaktifkan' : 'dinonaktifkan';
        Aktivitas::log('status_kategori', "Kategori '{$kategori->nama}' {$statusText}");

        return back()->with('success', "Kategori '{$kategori->nama}' berhasil {$statusText}.");
    }

    public function destroyKategori(KategoriKegiatan $kategori): RedirectResponse
    {
        $usageCount = Kegiatan::where('jenis_kegiatan', $kategori->nama)->count();

        if ($usageCount > 0) {
            return back()->with('error', "Kategori '{$kategori->nama}' tidak dapat dihapus karena sedang digunakan oleh {$usageCount} kegiatan. Nonaktifkan kategori sebagai alternatif.");
        }

        $nama = $kategori->nama;
        $kategori->delete();

        Aktivitas::log('hapus_kategori', "Menghapus kategori kegiatan '{$nama}'");

        return back()->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }

    public static function defaultPresensiColumns(): array
    {
        return [
            ['id' => 'name', 'label' => 'NAMA', 'enabled' => true, 'required' => true, 'isDefault' => true],
            ['id' => 'instansi', 'label' => 'INSTANSI / ASAL', 'enabled' => true, 'required' => false, 'isDefault' => true],
            ['id' => 'email', 'label' => 'EMAIL', 'enabled' => true, 'required' => false, 'isDefault' => true],
            ['id' => 'phone', 'label' => 'NO. HP / WA', 'enabled' => true, 'required' => false, 'isDefault' => true],
            ['id' => 'signature', 'label' => 'TTD', 'enabled' => true, 'required' => false, 'isDefault' => true],
            ['id' => 'keterangan', 'label' => 'KETERANGAN', 'enabled' => false, 'required' => false, 'isDefault' => true],
        ];
    }

    public static function getPresensiSettings(): array
    {
        if (Storage::disk('local')->exists('settings/presensi_template.json')) {
            $data = json_decode(Storage::disk('local')->get('settings/presensi_template.json'), true);
            if (is_array($data) && ! empty($data['columns'])) {
                return [
                    'columns' => $data['columns'],
                    'orientation' => $data['orientation'] ?? 'portrait',
                ];
            }
        }

        return [
            'columns' => self::defaultPresensiColumns(),
            'orientation' => 'portrait',
        ];
    }

    public function presensi(): Response
    {
        $settings = self::getPresensiSettings();

        $activeCount = count(array_filter($settings['columns'], fn ($c) => ! empty($c['enabled'])));

        $stats = [
            'total' => count($settings['columns']),
            'aktif' => $activeCount,
            'nonaktif' => count($settings['columns']) - $activeCount,
            'orientation' => $settings['orientation'],
        ];

        return Inertia::render('Admin/Setting/Presensi', [
            'settings' => $settings,
            'stats' => $stats,
        ]);
    }

    public function updatePresensiTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'columns' => ['required', 'array', 'min:1'],
            'columns.*.id' => ['required', 'string'],
            'columns.*.label' => ['required', 'string', 'max:50'],
            'columns.*.enabled' => ['required', 'boolean'],
            'orientation' => ['required', 'in:portrait,landscape'],
        ]);

        Storage::disk('local')->put('settings/presensi_template.json', json_encode([
            'columns' => $validated['columns'],
            'orientation' => $validated['orientation'],
            'updated_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT));

        Aktivitas::log('update_template_presensi', 'Memperbarui template bawaan formulir presensi kegiatan');

        return back()->with('success', 'Template formulir presensi berhasil disimpan.');
    }

    public function resetPresensiTemplate(): RedirectResponse
    {
        if (Storage::disk('local')->exists('settings/presensi_template.json')) {
            Storage::disk('local')->delete('settings/presensi_template.json');
        }

        Aktivitas::log('reset_template_presensi', 'Mengembalikan template formulir presensi ke pengaturan standar');

        return back()->with('success', 'Template formulir presensi berhasil dikembalikan ke standar.');
    }
}
