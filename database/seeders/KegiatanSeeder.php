<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agenda;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first() ?: Admin::create([
            'name' => 'Admin BEI',
            'email' => 'admin@idx-bengkulu.id',
            'password' => bcrypt('password123'),
            'no_telepon' => '081234567890',
            'jabatan' => 'Koordinator Perencanaan',
        ]);

        $users = User::all();
        if ($users->isEmpty()) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => bcrypt('password123'),
                'no_telepon' => '081298765432',
            ]);
            $users = collect([$user]);
        }

        $items = [
            [
                'nama' => 'Sekolah Pasar Modal (SPM) Level 1 & 2 - GI BEI UNIB',
                'jenis' => 'SPM',
                'tanggal' => '2025-02-28',
                'mulai' => '09:00',
                'selesai' => '15:30',
                'lokasi' => 'Gedung B Ruang Seminar GI UNIB Bengkulu',
                'kuota' => 245,
                'status' => 'dipublikasikan',
                'is_h1' => true,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Sosialisasi Public: Akselerasi OKM Riau',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2025-03-05',
                'mulai' => '13:30',
                'selesai' => '16:30',
                'lokasi' => 'Hotel Gumaya Semarang',
                'kuota' => 45,
                'status' => 'menunggu_approval',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Klinik Cerdas Investasi Syariah 2025',
                'jenis' => 'Edukasi',
                'tanggal' => '2025-03-10',
                'mulai' => '08:30',
                'selesai' => '12:00',
                'lokasi' => 'Aula Pascasarjana UINSU Medan',
                'kuota' => 150,
                'status' => 'disetujui',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Pembekalan Duta Pasar Modal BEI Jatim',
                'jenis' => 'Workshop',
                'tanggal' => '2025-02-20',
                'mulai' => '08:00',
                'selesai' => '14:00',
                'lokasi' => 'Kantor Perwakilan Surabaya',
                'kuota' => 60,
                'status' => 'berlangsung',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Workshop Investasi Pasar Modal ASN Pemprov Bali',
                'jenis' => 'Workshop',
                'tanggal' => '2025-02-14',
                'mulai' => '09:00',
                'selesai' => '15:00',
                'lokasi' => 'Gedung Wiswa Sabha Renon',
                'kuota' => 200,
                'status' => 'selesai',
                'is_h1' => false,
                'has_lpj' => true,
            ],
            [
                'nama' => 'Rapat Evaluasi GI',
                'jenis' => 'Edukasi',
                'tanggal' => '2025-02-01',
                'mulai' => '09:00',
                'selesai' => '12:00',
                'lokasi' => 'GI BEI UNIB',
                'kuota' => 30,
                'status' => 'selesai',
                'is_h1' => false,
                'has_lpj' => true,
            ],
            [
                'nama' => 'Sosialisasi IDX Mobile',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2025-02-03',
                'mulai' => '10:00',
                'selesai' => '12:00',
                'lokasi' => 'Galeri Investasi UNIB',
                'kuota' => 100,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Webinar Saham Syariah',
                'jenis' => 'Webinar',
                'tanggal' => '2025-02-05',
                'mulai' => '13:30',
                'selesai' => '15:30',
                'lokasi' => 'Zoom Webinar',
                'kuota' => 300,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'SPM Wilayah Sumut',
                'jenis' => 'SPM',
                'tanggal' => '2025-02-07',
                'mulai' => '09:00',
                'selesai' => '15:00',
                'lokasi' => 'Aula Pascasarjana UINSU',
                'kuota' => 120,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Aktivasi GI Polban',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2025-02-10',
                'mulai' => '09:00',
                'selesai' => '13:00',
                'lokasi' => 'Politeknik Negeri Bandung',
                'kuota' => 80,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Sosialisasi ASN Pemprov',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2025-02-12',
                'mulai' => '08:30',
                'selesai' => '12:00',
                'lokasi' => 'Aula Pemprov Bengkulu',
                'kuota' => 150,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Roadshow Dosen FEB',
                'jenis' => 'Edukasi',
                'tanggal' => '2025-02-17',
                'mulai' => '10:00',
                'selesai' => '14:00',
                'lokasi' => 'FEB Universitas Bengkulu',
                'kuota' => 75,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Review Proposal GI',
                'jenis' => 'Edukasi',
                'tanggal' => '2025-02-19',
                'mulai' => '14:00',
                'selesai' => '16:00',
                'lokasi' => 'Ruang Rapat KP BEI',
                'kuota' => 20,
                'status' => 'menunggu_approval',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'SPM Lanjutan KP Jateng',
                'jenis' => 'SPM',
                'tanggal' => '2025-02-21',
                'mulai' => '08:30',
                'selesai' => '15:30',
                'lokasi' => 'Hotel Gumaya Semarang',
                'kuota' => 110,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Literasi Guru SMA',
                'jenis' => 'Edukasi',
                'tanggal' => '2025-02-24',
                'mulai' => '09:00',
                'selesai' => '12:30',
                'lokasi' => 'SMAN 1 Bengkulu',
                'kuota' => 60,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Approval Booth Kampus',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2025-02-26',
                'mulai' => '10:00',
                'selesai' => '15:00',
                'lokasi' => 'UMB Bengkulu',
                'kuota' => 40,
                'status' => 'menunggu_approval',
                'is_h1' => false,
                'has_lpj' => false,
            ],
            [
                'nama' => 'Simulasi Trading Sat',
                'jenis' => 'Workshop',
                'tanggal' => '2025-03-01',
                'mulai' => '09:00',
                'selesai' => '13:00',
                'lokasi' => 'GI BEI UMB',
                'kuota' => 50,
                'status' => 'dipublikasikan',
                'is_h1' => false,
                'has_lpj' => false,
            ],
        ];

        foreach ($items as $data) {
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $data['nama'],
                'deskripsi' => 'Program edukasi dan sosialisasi pasar modal: '.$data['nama'],
                'jenis_kegiatan' => $data['jenis'],
                'tanggal' => $data['tanggal'],
                'waktu_mulai' => $data['mulai'],
                'waktu_selesai' => $data['selesai'],
                'lokasi' => $data['lokasi'],
                'kuota_peserta' => $data['kuota'],
                'status' => $data['status'],
            ]);

            Agenda::create([
                'kegiatan_id' => $kegiatan->id,
                'judul' => $data['nama'].($data['is_h1'] ? ' (H-1)' : ''),
                'tanggal' => $data['tanggal'],
                'waktu_mulai' => $data['mulai'],
                'waktu_selesai' => $data['selesai'],
                'lokasi' => $data['lokasi'],
                'keterangan' => 'Sesi utama kegiatan.',
                'is_h_minus_1' => $data['is_h1'],
            ]);

            if ($users->isNotEmpty()) {
                $kegiatan->users()->attach($users->first()->id, ['status' => 'hadir']);
            }

            if ($data['has_lpj']) {
                LaporanKegiatan::create([
                    'kegiatan_id' => $kegiatan->id,
                    'admin_id' => $admin->id,
                    'judul_laporan' => 'LPJ - '.$data['nama'],
                    'tanggal_laporan' => $data['tanggal'],
                    'isi_laporan' => 'Kegiatan telah dilaksanakan dengan baik dan terdokumentasi.',
                    'total_peserta_hadir' => $data['kuota'],
                    'status' => 'final',
                ]);
            }
        }
    }
}
