<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\KategoriKegiatan;
use App\Models\Kegiatan;
use App\Support\ActivityDescriptionSanitizer;
use App\Support\KegiatanNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KegiatanController extends Controller
{
    public function __construct(private readonly ActivityDescriptionSanitizer $descriptionSanitizer) {}

    public function index(Request $request)
    {
        $query = $this->manageableActivitiesQuery()
            ->withCount('peserta')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('nama_kegiatan', 'like', "%{$s}%")
                ->orWhere('lokasi', 'like', "%{$s}%")
                ->orWhere('mitra', 'like', "%{$s}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->kategori, fn ($q, $k) => $q->where('jenis_kegiatan', $k));

        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['nama_kegiatan', 'tanggal', 'lokasi', 'kuota_peserta', 'mitra', 'status'];

        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderByRaw("CASE WHEN status IN ('selesai','dibatalkan') THEN 1 ELSE 0 END")
                ->orderByDesc('tanggal');
        }

        if ($request->export === 'pdf' || $request->export === 'csv') {
            return $this->exportPdf($query->get(), $request);
        }

        $year = $this->manageableActivitiesQuery()->whereYear('tanggal', now()->year)->exists() ? now()->year : 2025;
        $stats = [
            'year' => $year,
            'total' => $this->manageableActivitiesQuery()->whereYear('tanggal', $year)->count(),
            'all' => $this->manageableActivitiesQuery()->count(),
            'disetujui' => $this->manageableActivitiesQuery()->where('status', 'disetujui')->count(),
            'terjadwal' => $this->manageableActivitiesQuery()->where('status', 'dipublikasikan')->count(),
            'berjalan' => $this->manageableActivitiesQuery()->where('status', 'berlangsung')->count(),
            'selesai' => $this->manageableActivitiesQuery()->where('status', 'selesai')->count(),
            'menunggu' => $this->manageableActivitiesQuery()->where('status', 'menunggu_approval')->count(),
        ];

        return Inertia::render('Admin/Kegiatan/Index', [
            'kegiatan' => $query->paginate(10)->withQueryString(),
            'stats' => $stats,
            'kategori' => $this->manageableActivitiesQuery()->distinct()->orderBy('jenis_kegiatan')->pluck('jenis_kegiatan'),
            'filters' => $request->only(['search', 'status', 'kategori', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Kegiatan/Create', $this->formOptions(new Kegiatan(['status' => 'menunggu_approval'])));
    }

    public function store(Request $request)
    {
        $kegiatan = Kegiatan::create($this->validated($request));

        Aktivitas::log('tambah_kegiatan', "Menambahkan kegiatan baru '{$kegiatan->nama_kegiatan}'", $kegiatan);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Kegiatan $kegiatan): Response
    {
        $kegiatan->load('agenda', 'laporanKegiatan');
        $kegiatan->setAttribute('deskripsi_html', $this->descriptionSanitizer->sanitize($kegiatan->deskripsi));

        return Inertia::render('Admin/Kegiatan/Show', [
            'kegiatan' => $kegiatan,
        ]);
    }

    public function downloadProposal(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->proposal_file_path && Storage::disk('local')->exists($kegiatan->proposal_file_path), 404);

        return Storage::disk('local')->download(
            $kegiatan->proposal_file_path,
            "proposal-{$kegiatan->id}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function downloadAttendance(Request $request, Kegiatan $kegiatan): HttpResponse
    {
        abort_unless(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'], true), 404);

        if (! $request->has('orientation')) {
            $request->merge(['orientation' => 'portrait']);
        }

        $validated = $request->validate([
            'orientation' => ['required', Rule::in(['portrait', 'landscape'])],
        ]);

        $columns = $this->resolveAttendanceColumns($request->input('columns'));

        return Pdf::loadView('admin.kegiatan.presensi', [
            'kegiatan' => $kegiatan,
            'orientation' => $validated['orientation'],
            'columns' => $columns,
        ])
            ->setPaper('a4', $validated['orientation'])
            ->download("presensi-{$kegiatan->id}-{$validated['orientation']}.pdf");
    }

    private function resolveAttendanceColumns($input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $rawList = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $input)));
        } elseif (is_array($input)) {
            $rawList = $input;
        } else {
            $rawList = null;
        }

        if (empty($rawList)) {
            if (Storage::disk('local')->exists('settings/presensi_template.json')) {
                $savedSettings = SettingController::getPresensiSettings();
                $enabledCols = array_filter($savedSettings['columns'], fn ($c) => ! empty($c['enabled']));
                if (! empty($enabledCols)) {
                    $rawList = array_map(fn ($c) => $c['label'], $enabledCols);
                }
            }
        }

        if (empty($rawList)) {
            return [
                ['key' => 'name', 'label' => 'NAMA', 'class' => 'name'],
                ['key' => 'email', 'label' => 'EMAIL', 'class' => 'email'],
                ['key' => 'phone', 'label' => 'NO. HP / WA', 'class' => 'phone'],
                ['key' => 'signature', 'label' => 'TTD', 'class' => 'signature'],
            ];
        }

        $resolved = [];
        foreach ($rawList as $item) {
            $label = is_array($item) ? ($item['label'] ?? $item['name'] ?? '') : (string) $item;
            $label = trim($label);
            if ($label === '') {
                continue;
            }

            $lower = strtolower($label);
            $class = match (true) {
                str_contains($lower, 'nama') || str_contains($lower, 'name') => 'name',
                str_contains($lower, 'email') => 'email',
                str_contains($lower, 'hp') || str_contains($lower, 'telepon') || str_contains($lower, 'phone') || str_contains($lower, 'wa') => 'phone',
                str_contains($lower, 'ttd') || str_contains($lower, 'tanda tangan') || str_contains($lower, 'paraf') || str_contains($lower, 'signature') => 'signature',
                default => 'custom-col',
            };

            $resolved[] = [
                'key' => is_array($item) ? ($item['key'] ?? \Illuminate\Support\Str::slug($label)) : \Illuminate\Support\Str::slug($label),
                'label' => strtoupper($label),
                'class' => $class,
            ];
        }

        return ! empty($resolved) ? $resolved : [
            ['key' => 'name', 'label' => 'NAMA', 'class' => 'name'],
            ['key' => 'email', 'label' => 'EMAIL', 'class' => 'email'],
            ['key' => 'phone', 'label' => 'NO. HP / WA', 'class' => 'phone'],
            ['key' => 'signature', 'label' => 'TTD', 'class' => 'signature'],
        ];
    }

    public function edit(Kegiatan $kegiatan): Response
    {
        // ponytail: protect approved activities from direct edit/delete; status changes happen via updateStatus.
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'], true), 403, 'Kegiatan yang sudah disetujui tidak dapat diedit.');

        return Inertia::render('Admin/Kegiatan/Edit', $this->formOptions($kegiatan));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'], true), 403, 'Kegiatan yang sudah disetujui tidak dapat diedit.');

        $kegiatan->update($this->validated($request, $kegiatan));

        return redirect()->route('admin.kegiatan.show', $kegiatan)->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['disetujui', 'dipublikasikan', 'berlangsung', 'selesai', 'dibatalkan'])],
        ]);

        $kegiatan->update(['status' => $validated['status']]);

        KegiatanNotificationService::notifyStatusChange(
            $kegiatan,
            $validated['status']
        );

        $label = Kegiatan::STATUS[$validated['status']][0] ?? $validated['status'];

        Aktivitas::log(
            'perubahan_status',
            "Mengubah status kegiatan '{$kegiatan->nama_kegiatan}' menjadi {$label}",
            $kegiatan,
            ['status' => $validated['status']]
        );

        return back()->with('success', "Status kegiatan berhasil diperbarui menjadi {$label}.");
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'], true), 403, 'Kegiatan yang sudah disetujui tidak dapat dihapus.');

        if ($kegiatan->proposal_file_path) {
            Storage::disk('local')->delete($kegiatan->proposal_file_path);
        }

        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function validated(Request $request, ?Kegiatan $kegiatan = null): array
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'lokasi' => 'required|string|max:255',
            'mitra' => 'nullable|string|max:255',
            'kuota_peserta' => 'nullable|integer|min:1',
            'status' => ['required', Rule::in(array_keys(Kegiatan::STATUS))],
        ]);

        $validated['deskripsi'] = $this->descriptionSanitizer->sanitize($validated['deskripsi']);

        return $validated;
    }

    private function manageableActivitiesQuery(): Builder
    {
        return Kegiatan::query()->where(function (Builder $query) {
            $query->whereNull('user_id')
                ->orWhereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai']);
        });
    }

    private function formOptions(Kegiatan $kegiatan): array
    {
        if ($kegiatan->exists) {
            $kegiatan->deskripsi = $this->descriptionSanitizer->sanitize($kegiatan->deskripsi);
        }

        return [
            'kegiatan' => $kegiatan,
            'descriptionImageUploadUrl' => route('admin.description-images.store'),
            'kategori' => KategoriKegiatan::active()
                ->orWhere('nama', $kegiatan->jenis_kegiatan)
                ->orderBy('nama')
                ->pluck('nama')
                ->unique()
                ->values(),
        ];
    }

    private function exportPdf($rows, Request $request)
    {
        $stats = [
            'total' => $rows->count(),
            'disetujui' => $rows->where('status', 'disetujui')->count(),
            'terjadwal' => $rows->where('status', 'dipublikasikan')->count(),
            'berjalan' => $rows->where('status', 'berlangsung')->count(),
            'selesai' => $rows->where('status', 'selesai')->count(),
            'menunggu' => $rows->where('status', 'menunggu_approval')->count(),
        ];

        $filters = $request->only(['search', 'status', 'kategori']);
        $printedBy = auth('admin')->user()?->name ?? 'Administrator KP BEI Bengkulu';

        return Pdf::loadView('pdf.kegiatan', [
            'items' => $rows,
            'stats' => $stats,
            'filters' => $filters,
            'printedBy' => $printedBy,
        ])
            ->setPaper('a4', 'landscape')
            ->download('laporan-kegiatan-'.now()->format('Ymd-His').'.pdf');
    }
}
