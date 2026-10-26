<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\Kegiatan;
use App\Support\KegiatanNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApprovalController extends Controller
{
    public function index(Request $request): Response
    {
        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['nama_kegiatan', 'jenis_kegiatan', 'created_at', 'status', 'mitra'];

        $query = $this->approvalItemsQuery($request);
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $approvalItems = $query
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Kegiatan $kegiatan): array => $this->formatApprovalItem($kegiatan));

        $stats = [
            'total' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['menunggu_approval', 'diproses', 'revisi', 'ditolak'])->count(),
            'pending_total' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'disetujui' => Kegiatan::whereNotNull('user_id')->where('status', 'disetujui')->count(),
            'perhatian' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['revisi', 'ditolak'])->count(),
        ];

        $categories = Kegiatan::query()
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('jenis_kegiatan')
            ->filter()
            ->sort()
            ->values();

        return Inertia::render('Admin/Pengajuan/Index', [
            'approvalItems' => $approvalItems,
            'stats' => $stats,
            'categories' => $categories,
            'filters' => $request->only(['search', 'status', 'kategori', 'sort', 'direction']),
        ]);
    }

    public function export(Request $request)
    {
        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['nama_kegiatan', 'jenis_kegiatan', 'created_at', 'status', 'mitra'];

        $query = $this->approvalItemsQuery($request)->with(['user']);

        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $items = $query->get();

        $stats = [
            'total' => $items->count(),
            'pending_total' => $items->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'disetujui' => $items->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'])->count(),
            'perhatian' => $items->whereIn('status', ['revisi', 'ditolak'])->count(),
        ];

        $filters = $request->only(['search', 'status', 'kategori']);
        $printedBy = auth('admin')->user()?->name ?? 'Administrator KP BEI Bengkulu';

        return Pdf::loadView('pdf.approval', [
            'items' => $items,
            'stats' => $stats,
            'filters' => $filters,
            'printedBy' => $printedBy,
        ])
            ->setPaper('a4', 'landscape')
            ->download('daftar-approval-proposal-'.date('Ymd-His').'.pdf');
    }

    public function updateMakerProposalStatus(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        abort_unless($kegiatan->user_id !== null, 404);

        $allowedTransitions = [
            'menunggu_approval' => ['diproses', 'disetujui', 'revisi', 'ditolak'],
            'diproses' => ['disetujui', 'revisi', 'ditolak'],
            'disetujui' => ['dipublikasikan'],
            'dipublikasikan' => ['berlangsung'],
            'berlangsung' => ['selesai'],
        ];

        abort_unless(
            isset($allowedTransitions[$kegiatan->status]),
            409
        );

        $validated = $request->validate([
            'status' => ['required', Rule::in($allowedTransitions[$kegiatan->status])],
            'catatan' => [
                Rule::requiredIf(in_array($request->input('status'), ['revisi', 'ditolak'], true)),
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $kegiatan->update([
            'status' => $validated['status'],
            'approval_note' => array_key_exists('catatan', $validated) ? $validated['catatan'] : $kegiatan->approval_note,
            'reviewed_by_admin_id' => Auth::guard('admin')->id(),
            'approval_processed_at' => now(),
        ]);

        $statusLabel = Kegiatan::STATUS[$validated['status']][0] ?? $validated['status'];
        Aktivitas::log(
            'approval_proposal',
            "Memperbarui status proposal '{$kegiatan->nama_kegiatan}' menjadi {$statusLabel}" . (!empty($validated['catatan']) ? " (Catatan: {$validated['catatan']})" : ''),
            $kegiatan,
            ['status' => $validated['status'], 'catatan' => $validated['catatan'] ?? null]
        );

        KegiatanNotificationService::notifyStatusChange(
            $kegiatan,
            $validated['status'],
            $validated['catatan'] ?? null
        );

        $message = match ($validated['status']) {
            'dipublikasikan' => 'Kegiatan berhasil dijadwalkan.',
            'berlangsung' => 'Status kegiatan diubah menjadi berlangsung.',
            'selesai' => 'Status kegiatan berhasil diselesaikan.',
            default => 'Status proposal mitra berhasil diperbarui.',
        };

        return back()->with('success', $message);
    }

    private function approvalItemsQuery(Request $request): Builder
    {
        return Kegiatan::query()
            ->with('user')
            ->whereNotNull('user_id')
            ->whereNotIn('status', ['dipublikasikan', 'berlangsung', 'selesai'])
            ->when($request->get('search'), function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('nama_kegiatan', 'like', "%{$search}%")
                        ->orWhere('mitra', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhere('lokasi', 'like', "%{$search}%");
                });
            })
            ->when($request->get('kategori'), fn (Builder $query, string $category): Builder => $query->where('jenis_kegiatan', $category))
            ->when($request->get('status'), function (Builder $query, string $status): void {
                if ($status === 'menunggu') {
                    $query->where('status', 'menunggu_approval');

                    return;
                }

                $query->where('status', $status);
            }, function (Builder $query): void {
                // ponytail: default approval queue shows active proposals awaiting approval; approved proposals move to kelola kegiatan.
                $query->whereIn('status', ['menunggu_approval', 'diproses', 'revisi', 'ditolak']);
            });
    }

    private function formatApprovalItem(Kegiatan $kegiatan): array
    {
        return [
            'id' => $kegiatan->id,
            'source_id' => $kegiatan->id,
            'identifier' => 'MKR-'.sprintf('%05d', $kegiatan->id),
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'jenis_kegiatan' => $kegiatan->jenis_kegiatan,
            'tanggal' => $kegiatan->tanggal,
            'submitted_at' => $kegiatan->created_at,
            'submitter' => $kegiatan->user?->name,
            'mitra' => $kegiatan->mitra,
            'status' => $kegiatan->status,
            'status_label' => $kegiatan->status_label,
            'status_class' => $kegiatan->status_class,
            'review_note' => $kegiatan->approval_note,
            'proposal_file_path' => $kegiatan->proposal_file_path,
            'can_review' => in_array($kegiatan->status, ['menunggu_approval', 'diproses'], true),
        ];
    }
}
