<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\Kegiatan;
use App\Support\ActivityDescriptionSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class KegiatanController extends Controller
{
    public function __construct(private readonly ActivityDescriptionSanitizer $descriptionSanitizer) {}

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
                'kuota_peserta' => null,
            ],
            'descriptionImageUploadUrl' => route('user.description-images.store'),
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
            'kuota_peserta' => ['required', 'integer', 'min:1'],
            'proposal_file' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
        ]);

        $existingCount = Kegiatan::whereDate('tanggal', $validated['tanggal'])
            ->whereNotIn('status', ['dibatalkan', 'ditolak'])
            ->count();

        // ponytail: batas 4 per hari; upgrade ke kuota dinamis via DB settings jika dibutuhkan.
        if ($existingCount >= 4) {
            return back()
                ->withInput()
                ->withErrors(['tanggal' => 'Batas maksimal 4 pengajuan pada tanggal ini telah tercapai.'])
                ->with('error', 'Pengajuan pada tanggal tersebut sudah penuh (maksimal 4 kegiatan). Silakan pilih tanggal lain.');
        }

        $validated['deskripsi'] = $this->descriptionSanitizer->sanitize($validated['deskripsi']);

        $proposalFilePath = $request->file('proposal_file')?->store('proposals', 'local');

        $kegiatan = Kegiatan::create([
            'user_id' => Auth::guard('web')->id(),
            'proposal_file_path' => $proposalFilePath,
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'deskripsi' => $validated['deskripsi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'] ?? null,
            'lokasi' => $validated['lokasi'],
            'mitra' => $validated['mitra'] ?? Auth::guard('web')->user()?->name,
            'kuota_peserta' => $validated['kuota_peserta'],
            'status' => 'menunggu_approval',
        ]);

        Aktivitas::log('pengajuan_proposal', "Mengajukan proposal kegiatan '{$kegiatan->nama_kegiatan}'", $kegiatan);

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

        $kegiatanList = $query->paginate(9)->withQueryString()->through(function (Kegiatan $kegiatan) {
            $kegiatan->setAttribute('deskripsi_excerpt', $this->descriptionSanitizer->excerpt($kegiatan->deskripsi));
            $kegiatan->setAttribute('description_banner', $this->descriptionSanitizer->firstImageUrl($kegiatan->deskripsi));

            return $kegiatan;
        });
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
        $item = Kegiatan::with(['agenda', 'peserta', 'user', 'reviewedByAdmin'])->findOrFail($kegiatan);
        $item->setAttribute('deskripsi_html', $this->descriptionSanitizer->sanitize($item->deskripsi));
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
        // ponytail: protect finalized proposals; allow admin overrides via admin controller.
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai', 'dibatalkan'], true), 403);
        $kegiatan->deskripsi = $this->descriptionSanitizer->sanitize($kegiatan->deskripsi);

        return Inertia::render('User/Kegiatan/Edit', [
            'kegiatan' => $kegiatan,
            'descriptionImageUploadUrl' => route('user.description-images.store'),
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);
        // ponytail: protect finalized proposals; allow admin overrides via admin controller.
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai', 'dibatalkan'], true), 403);

        $validated = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'string', 'max:10'],
            'waktu_selesai' => ['nullable', 'string', 'max:10'],
            'lokasi' => ['required', 'string', 'max:255'],
            'mitra' => ['nullable', 'string', 'max:255'],
            'kuota_peserta' => ['required', 'integer', 'min:1'],
            'proposal_file' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
        ]);

        $existingCount = Kegiatan::whereDate('tanggal', $validated['tanggal'])
            ->where('id', '!=', $kegiatan->id)
            ->whereNotIn('status', ['dibatalkan', 'ditolak'])
            ->count();

        // ponytail: batas 4 per hari; upgrade ke kuota dinamis via DB settings jika dibutuhkan.
        if ($existingCount >= 4) {
            return back()
                ->withInput()
                ->withErrors(['tanggal' => 'Batas maksimal 4 pengajuan pada tanggal ini telah tercapai.'])
                ->with('error', 'Pengajuan pada tanggal tersebut sudah penuh (maksimal 4 kegiatan). Silakan pilih tanggal lain.');
        }

        $validated['deskripsi'] = $this->descriptionSanitizer->sanitize($validated['deskripsi']);

        $oldProposalFilePath = $kegiatan->proposal_file_path;
        $proposalFilePath = $request->file('proposal_file')?->store('proposals', 'local');

        $wasRevising = $kegiatan->status === 'revisi';

        $kegiatan->update([
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'deskripsi' => $validated['deskripsi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'] ?? null,
            'lokasi' => $validated['lokasi'],
            'mitra' => $validated['mitra'] ?? $kegiatan->mitra,
            'kuota_peserta' => $validated['kuota_peserta'],
            'proposal_file_path' => $proposalFilePath ?? $oldProposalFilePath,
        ]);

        if ($proposalFilePath && $oldProposalFilePath) {
            Storage::disk('local')->delete($oldProposalFilePath);
        }

        if ($wasRevising) {
            $kegiatan->update([
                'status' => 'menunggu_approval',
                'approval_note' => null,
                'reviewed_by_admin_id' => null,
                'approval_processed_at' => null,
            ]);
        }

        Aktivitas::log('pembaruan_proposal', "Memperbarui proposal kegiatan '{$kegiatan->nama_kegiatan}'", $kegiatan);

        return redirect()->route('user.kegiatan.show', $kegiatan)->with('success', 'Proposal kegiatan berhasil diperbarui.');
    }

    public function downloadProposal(Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);
        abort_unless($kegiatan->proposal_file_path && Storage::disk('local')->exists($kegiatan->proposal_file_path), 404);

        return Storage::disk('local')->download(
            $kegiatan->proposal_file_path,
            "proposal-{$kegiatan->id}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);
        // ponytail: protect finalized proposals; allow admin overrides via admin controller.
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'], true), 403);

        if ($kegiatan->proposal_file_path) {
            Storage::disk('local')->delete($kegiatan->proposal_file_path);
        }

        Aktivitas::log('hapus_proposal', "Menghapus proposal kegiatan '{$kegiatan->nama_kegiatan}'", $kegiatan);

        $kegiatan->delete();

        return redirect()->route('user.kegiatan-saya')->with('success', 'Proposal kegiatan berhasil dihapus.');
    }

    public function cancel(Kegiatan $kegiatan)
    {
        abort_if((int) $kegiatan->user_id !== (int) Auth::guard('web')->id(), 403);
        // ponytail: protect finalized proposals; allow admin overrides via admin controller.
        abort_if(in_array($kegiatan->status, ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai', 'dibatalkan'], true), 403);

        $kegiatan->update(['status' => 'dibatalkan']);

        Aktivitas::log('pembatalan_proposal', "Membatalkan proposal kegiatan '{$kegiatan->nama_kegiatan}'", $kegiatan);

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

        Aktivitas::log('pendaftaran_kegiatan', "Mendaftar pada kegiatan '{$item->nama_kegiatan}'", $item);

        return back()->with('success', "Selamat! Anda berhasil terdaftar pada kegiatan \"{$item->nama_kegiatan}\".");
    }

    public function batal($kegiatan)
    {
        $item = Kegiatan::findOrFail($kegiatan);
        $user = Auth::guard('web')->user();

        $item->users()->detach($user->id);

        Aktivitas::log('pembatalan_keikutsertaan', "Membatalkan pendaftaran kegiatan '{$item->nama_kegiatan}'", $item);

        return back()->with('success', "Pendaftaran Anda untuk kegiatan \"{$item->nama_kegiatan}\" telah dibatalkan.");
    }
}
