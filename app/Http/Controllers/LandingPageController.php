<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Support\ActivityDescriptionSanitizer;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    public function __invoke(ActivityDescriptionSanitizer $descriptionSanitizer): Response
    {
        $agendaItems = Kegiatan::publicSchedule()
            ->withCount('peserta')
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get()
            ->map(function (Kegiatan $kegiatan) use ($descriptionSanitizer): array {
                $kuotaTotal = $kegiatan->kuota_peserta;
                $kuotaTerisi = (int) $kegiatan->peserta_count;
                $sisaKuota = $kuotaTotal === null ? null : max(0, $kuotaTotal - $kuotaTerisi);
                $statusKode = $sisaKuota === 0 ? 'full' : ($sisaKuota !== null && $sisaKuota <= 5 ? 'limited' : 'open');
                $kategori = match ($kegiatan->jenis_kegiatan) {
                    'SPM' => 'Sekolah Pasar Modal',
                    'Edukasi' => 'Literasi Publik',
                    default => $kegiatan->jenis_kegiatan,
                };

                return [
                    'id' => $kegiatan->id,
                    'tanggal' => $kegiatan->tanggal?->toDateString(),
                    'jam' => substr($kegiatan->waktu_mulai, 0, 5)
                        .($kegiatan->waktu_selesai ? ' - '.substr($kegiatan->waktu_selesai, 0, 5) : '')
                        .' WIB',
                    'kategori' => $kategori,
                    'judul' => $kegiatan->nama_kegiatan,
                    'lokasi' => $kegiatan->lokasi,
                    'mitra' => $kegiatan->mitra ?: 'Belum ditentukan',
                    'kuotaTotal' => $kuotaTotal,
                    'kuotaTerisi' => $kuotaTerisi,
                    'status' => match ($statusKode) {
                        'full' => 'Kuota Penuh',
                        'limited' => 'Sisa Kuota Terbatas',
                        default => 'Pendaftaran Dibuka',
                    },
                    'statusKode' => $statusKode,
                    'deskripsiHtml' => $descriptionSanitizer->sanitize($kegiatan->deskripsi),
                ];
            });

        return Inertia::render('Welcome', [
            'agendaItems' => $agendaItems,
            'categories' => $agendaItems->pluck('kategori')->unique()->values()->prepend('Semua'),
        ]);
    }
}
