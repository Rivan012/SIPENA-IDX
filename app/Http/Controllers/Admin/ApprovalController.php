<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
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
        $approvalItems = $this->approvalItemsQuery($request)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Kegiatan $kegiatan): array => $this->formatApprovalItem($kegiatan));

        $stats = [
            'total' => Kegiatan::whereNotNull('user_id')->count(),
            'pending_total' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['menunggu_approval', 'diproses'])->count(),
            'disetujui' => Kegiatan::whereNotNull('user_id')->whereIn('status', ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'])->count(),
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
            'filters' => $request->only(['search', 'status', 'kategori']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $items = $this->approvalItemsQuery($request)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return response()->streamDownload(function () use ($items): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Nomor',
                'Nama Kegiatan',
                'Kategori',
                'Pengusul',
                'Tanggal Pengajuan',
                'Status Approval',
                'Catatan Review',
            ]);

            foreach ($items as $kegiatan) {
                fputcsv($handle, [
                    'MKR-'.sprintf('%05d', $kegiatan->id),
                    $kegiatan->nama_kegiatan,
                    $kegiatan->jenis_kegiatan,
                    $kegiatan->user?->name ?: $kegiatan->mitra ?: '-',
                    $kegiatan->created_at?->toDateString() ?: '-',
                    $kegiatan->status_label,
                    $kegiatan->approval_note ?: '-',
                ]);
            }

            fclose($handle);
        }, 'daftar-approval-proposal-'.date('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    public function updateMakerProposalStatus(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        abort_unless($kegiatan->user_id !== null, 404);
        abort_unless(in_array($kegiatan->status, ['menunggu_approval', 'diproses'], true), 409);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['diproses', 'disetujui', 'revisi', 'ditolak'])],
            'catatan' => [
                Rule::requiredIf(in_array($request->input('status'), ['revisi', 'ditolak'], true)),
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $kegiatan->update([
            'status' => $validated['status'],
            'approval_note' => $validated['catatan'] ?? null,
            'reviewed_by_admin_id' => Auth::guard('admin')->id(),
            'approval_processed_at' => now(),
        ]);

        return back()->with('success', 'Status proposal mitra berhasil diperbarui.');
    }

    private function approvalItemsQuery(Request $request): Builder
    {
        return Kegiatan::query()
            ->with('user')
            ->whereNotNull('user_id')
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
