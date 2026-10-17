<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Support\ActivityDescriptionSanitizer;
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
            ->when($request->kategori, fn ($q, $k) => $q->where('jenis_kegiatan', $k))
            ->orderByRaw("CASE WHEN status IN ('selesai','dibatalkan') THEN 1 ELSE 0 END")
            ->orderByDesc('tanggal');

        if ($request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $year = $this->manageableActivitiesQuery()->whereYear('tanggal', now()->year)->exists() ? now()->year : 2025;
        $stats = [
            'year' => $year,
            'total' => $this->manageableActivitiesQuery()->whereYear('tanggal', $year)->count(),
            'berjalan' => $this->manageableActivitiesQuery()->where('status', 'berlangsung')->count(),
            'selesai' => $this->manageableActivitiesQuery()->where('status', 'selesai')->count(),
            'menunggu' => $this->manageableActivitiesQuery()->where('status', 'menunggu_approval')->count(),
        ];

        return Inertia::render('Admin/Kegiatan/Index', [
            'kegiatan' => $query->paginate(10)->withQueryString(),
            'stats' => $stats,
            'kategori' => $this->manageableActivitiesQuery()->distinct()->orderBy('jenis_kegiatan')->pluck('jenis_kegiatan'),
            'filters' => $request->only(['search', 'status', 'kategori']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Kegiatan/Create', $this->formOptions(new Kegiatan(['status' => 'menunggu_approval'])));
    }

    public function store(Request $request)
    {
        Kegiatan::create($this->validated($request));

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Kegiatan $kegiatan): Response
    {
        $kegiatan->load('agenda', 'peserta.user', 'laporanKegiatan');
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
        abort_unless($kegiatan->status === 'dipublikasikan', 404);

        $validated = $request->validate([
            'orientation' => ['required', Rule::in(['portrait', 'landscape'])],
        ]);

        return Pdf::loadView('admin.kegiatan.presensi', [
            'kegiatan' => $kegiatan,
            'orientation' => $validated['orientation'],
        ])
            ->setPaper('a4', $validated['orientation'])
            ->download("presensi-{$kegiatan->id}-{$validated['orientation']}.pdf");
    }

    public function edit(Kegiatan $kegiatan): Response
    {
        return Inertia::render('Admin/Kegiatan/Edit', $this->formOptions($kegiatan));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $kegiatan->update($this->validated($request, $kegiatan));

        return redirect()->route('admin.kegiatan.show', $kegiatan)->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
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
            'kategori' => Kegiatan::distinct()->orderBy('jenis_kegiatan')->pluck('jenis_kegiatan'),
        ];
    }

    private function exportCsv($rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Kode', 'Nama Kegiatan', 'Kategori', 'Tanggal', 'Waktu', 'Lokasi', 'Mitra', 'Target Peserta', 'Peserta', 'Status']);
            foreach ($rows as $k) {
                fputcsv($out, [
                    $k->kode,
                    $k->nama_kegiatan,
                    $k->jenis_kegiatan,
                    $k->tanggal?->format('Y-m-d'),
                    substr($k->waktu_mulai, 0, 5),
                    $k->lokasi,
                    $k->mitra,
                    $k->kuota_peserta,
                    $k->peserta_count,
                    $k->status_label,
                ]);
            }
            fclose($out);
        }, 'jadwal-kegiatan-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }
}
