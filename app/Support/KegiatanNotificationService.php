<?php

namespace App\Support;

use App\Mail\KegiatanStatusNotificationMail;
use App\Models\Kegiatan;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KegiatanNotificationService
{
    /**
     * Kirim notifikasi dashboard mitra dan email saat status kegiatan berubah.
     */
    public static function notifyStatusChange(Kegiatan $kegiatan, string $status, ?string $catatan = null): void
    {
        $user = $kegiatan->user;
        if (! $user) {
            return;
        }

        $meta = self::getStatusMeta($kegiatan, $status);
        if (! $meta) {
            return;
        }

        $pesanNotif = $meta['pesan'];
        if ($catatan) {
            $pesanNotif .= "\n\nCatatan Admin: {$catatan}";
        }

        // 1. Simpan notifikasi ke database untuk dashboard mitra
        Notifikasi::create([
            'user_id' => $user->id,
            'admin_id' => Auth::guard('admin')->id(),
            'judul' => $meta['judul'],
            'pesan' => $pesanNotif,
            'tipe' => 'status_kegiatan',
            'data' => [
                'kegiatan_id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'status' => $status,
                'catatan' => $catatan,
            ],
            'is_read' => false,
        ]);

        // 2. Kirim notifikasi email ke alamat mitra
        if (! empty($user->email)) {
            try {
                Mail::to($user->email)->send(
                    new KegiatanStatusNotificationMail($kegiatan, $status, $meta['judul'], $meta['pesan'], $catatan)
                );
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email status kegiatan ke {$user->email}: " . $e->getMessage());
            }
        }
    }

    public static function getStatusMeta(Kegiatan $kegiatan, string $status): ?array
    {
        $tanggal = $kegiatan->tanggal ? \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') : '-';
        $waktu = $kegiatan->waktu_mulai ? substr($kegiatan->waktu_mulai, 0, 5) . ' WIB' : '';

        return match ($status) {
            'disetujui' => [
                'judul' => 'Proposal Kegiatan Disetujui',
                'pesan' => "Proposal kegiatan '{$kegiatan->nama_kegiatan}' telah disetujui oleh Admin BEI Bengkulu dan siap untuk dijadwalkan.",
            ],
            'dipublikasikan' => [
                'judul' => 'Kegiatan Telah Terjadwal',
                'pesan' => "Kegiatan '{$kegiatan->nama_kegiatan}' telah dijadwalkan pada {$tanggal} {$waktu} di {$kegiatan->lokasi}.",
            ],
            'berlangsung' => [
                'judul' => 'Kegiatan Sedang Berlangsung',
                'pesan' => "Kegiatan '{$kegiatan->nama_kegiatan}' saat ini sedang berlangsung.",
            ],
            'selesai' => [
                'judul' => 'Kegiatan Telah Selesai',
                'pesan' => "Kegiatan '{$kegiatan->nama_kegiatan}' telah selesai dilaksanakan. Terima kasih atas partisipasi dan kerja samanya.",
            ],
            default => null,
        };
    }
}
