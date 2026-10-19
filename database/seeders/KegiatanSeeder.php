<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agenda;
use App\Models\Aktivitas;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Akun Utama KP BEI Bengkulu
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@idx-bengkulu.id'],
            [
                'name' => 'Admin IDX Bengkulu',
                'password' => Hash::make('password123'),
                'no_telepon' => '081271234567',
                'jabatan' => 'Trainer & Edukasi KP BEI Bengkulu',
            ]
        );

        // 2. Mitra Strategis & Galeri Investasi (GI) di Bengkulu
        $mitraList = [
            [
                'email' => 'user@example.com',
                'name' => 'Budi Santoso (Koordinator GI UNIB)',
                'no_telepon' => '081278901234',
                'institusi' => 'GI BEI FEB Universitas Bengkulu',
            ],
            [
                'email' => 'gi.unib@idx-bengkulu.id',
                'name' => 'Galeri Investasi BEI Universitas Bengkulu',
                'no_telepon' => '085267112233',
                'institusi' => 'FEB Universitas Bengkulu',
            ],
            [
                'email' => 'gi.uinfas@idx-bengkulu.id',
                'name' => 'Galeri Investasi Syariah BEI UINFAS Bengkulu',
                'no_telepon' => '085388990011',
                'institusi' => 'FEBI UIN Fatmawati Sukarno Bengkulu',
            ],
            [
                'email' => 'gi.unived@idx-bengkulu.id',
                'name' => 'Galeri Investasi BEI Universitas Dehasen',
                'no_telepon' => '081369445566',
                'institusi' => 'Fakultas Ekonomi UNIVED Bengkulu',
            ],
            [
                'email' => 'gi.umb@idx-bengkulu.id',
                'name' => 'Galeri Investasi BEI Universitas Muhammadiyah Bengkulu',
                'no_telepon' => '082180776655',
                'institusi' => 'Fakultas Ekonomi & Bisnis UMB',
            ],
            [
                'email' => 'gi.unihaz@idx-bengkulu.id',
                'name' => 'Galeri Investasi BEI Universitas Hazairin',
                'no_telepon' => '081273554433',
                'institusi' => 'Universitas Prof. Dr. Hazairin, SH',
            ],
            [
                'email' => 'dpm.bengkulu@idx-bengkulu.id',
                'name' => 'Duta Pasar Modal BEI Bengkulu',
                'no_telepon' => '089633221100',
                'institusi' => 'Komunitas Duta Pasar Modal Bengkulu',
            ],
            [
                'email' => 'bkd.pemprov@bengkuluprov.go.id',
                'name' => 'BKD Pemerintah Provinsi Bengkulu',
                'no_telepon' => '081173009988',
                'institusi' => 'Badan Kepegawaian Daerah Prov. Bengkulu',
            ],
            [
                'email' => 'hipmi.bengkulu@gmail.com',
                'name' => 'BPC HIPMI Kota Bengkulu',
                'no_telepon' => '081377889900',
                'institusi' => 'Himpunan Pengusaha Muda Indonesia Bengkulu',
            ],
        ];

        $users = [];
        foreach ($mitraList as $mitraData) {
            $user = User::updateOrCreate(
                ['email' => $mitraData['email']],
                [
                    'name' => $mitraData['name'],
                    'password' => Hash::make('password123'),
                    'no_telepon' => $mitraData['no_telepon'],
                    'email_verified_at' => now(),
                ]
            );
            $users[$mitraData['email']] = $user;
        }

        $allUsers = User::all();

        // 3. Dataset Kegiatan Edukasi Pasar Modal KP BEI Bengkulu TA 2026 (Jan - Des)
        $kegiatanData = [
            // --- JANUARI 2026 ---
            [
                'nama' => 'Sekolah Pasar Modal (SPM) Level 1: Pengenalan Investasi Saham Pemula',
                'deskripsi' => 'Pengenalan struktur pasar modal Indonesia, mekanisme transaksi bursa, dan pembukaan rekening efek bersama Phintraco Sekuritas.',
                'jenis' => 'SPM',
                'tanggal' => '2026-01-15',
                'mulai' => '09:00',
                'selesai' => '12:30',
                'lokasi' => 'Ruang Seminar Gedung Dekanat FEB Universitas Bengkulu',
                'mitra' => 'GI BEI FEB Universitas Bengkulu',
                'user_email' => 'gi.unib@idx-bengkulu.id',
                'kuota' => 120,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Sosialisasi IDX Mobile & Edukasi Cerdas Finansial Mahasiswa UINFAS',
                'deskripsi' => 'Sosialisasi fitur aplikasi IDX Mobile, cara membaca running trade, dan tips memilih saham berfundamental sehat.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-01-22',
                'mulai' => '13:30',
                'selesai' => '16:00',
                'lokasi' => 'Auditorium Syariah FEBI UIN Fatmawati Sukarno Bengkulu',
                'mitra' => 'GI Syariah BEI UINFAS Bengkulu',
                'user_email' => 'gi.uinfas@idx-bengkulu.id',
                'kuota' => 90,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- FEBRUARI 2026 ---
            [
                'nama' => 'Sekolah Pasar Modal Syariah (SPMS) Tingkat Dasar',
                'deskripsi' => 'Prinsip syariah dalam investasi pasar modal, akad efek syariah, daftar saham dalam Indeks Saham Syariah Indonesia (ISSI).',
                'jenis' => 'SPM',
                'tanggal' => '2026-02-12',
                'mulai' => '08:30',
                'selesai' => '12:00',
                'lokasi' => 'Aula Pascasarjana UINFAS Bengkulu',
                'mitra' => 'GI Syariah BEI UINFAS Bengkulu',
                'user_email' => 'gi.uinfas@idx-bengkulu.id',
                'kuota' => 150,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Edukasi Literasi Pasar Modal bagi ASN Pemerintah Provinsi Bengkulu',
                'deskripsi' => 'Pengenalan instrumen investasi aman dan persiapan dana pensiun melalui Surat Berharga Negara (SBN) & Reksadana.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-02-19',
                'mulai' => '09:00',
                'selesai' => '12:00',
                'lokasi' => 'Graha Bina Praja Kantor Gubernur Bengkulu',
                'mitra' => 'BKD Pemerintah Provinsi Bengkulu',
                'user_email' => 'bkd.pemprov@bengkuluprov.go.id',
                'kuota' => 180,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Pelatihan Duta Pasar Modal (DPM) Goes to School Bengkulu',
                'deskripsi' => 'Pembekalan materi literasi dasar pasar modal bagi fasilitator siswa SMA/SMK se-Kota Bengkulu.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-02-26',
                'mulai' => '13:00',
                'selesai' => '17:00',
                'lokasi' => 'Ruang Edukasi Kantor Perwakilan BEI Bengkulu',
                'mitra' => 'Duta Pasar Modal BEI Bengkulu',
                'user_email' => 'dpm.bengkulu@idx-bengkulu.id',
                'kuota' => 45,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- MARET 2026 ---
            [
                'nama' => 'Sekolah Pasar Modal (SPM) Level 2: Analisis Fundamental & Valuasi Saham',
                'deskripsi' => 'Membaca laporan keuangan emiten, rasio PER, PBV, ROE, dan studi kasus memilih saham dividen konsisten.',
                'jenis' => 'SPM',
                'tanggal' => '2026-03-05',
                'mulai' => '09:00',
                'selesai' => '15:00',
                'lokasi' => 'Laboratorium Pasar Modal GI UNIB',
                'mitra' => 'GI BEI FEB Universitas Bengkulu',
                'user_email' => 'gi.unib@idx-bengkulu.id',
                'kuota' => 110,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Seminar Pasar Modal: Waspada Bahaya Pinjol Ilegal & Investasi Bodong',
                'deskripsi' => 'Edukasi pencegahan penipuan berkedok robot trading dan pinjaman online ilegal bersama Satgas PASTI OJK Bengkulu.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-03-18',
                'mulai' => '09:30',
                'selesai' => '12:30',
                'lokasi' => 'Aula Universitas Prof. Dr. Hazairin SH Bengkulu',
                'mitra' => 'GI BEI Universitas Hazairin',
                'user_email' => 'gi.unihaz@idx-bengkulu.id',
                'kuota' => 140,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- APRIL 2026 ---
            [
                'nama' => 'Sekolah Pasar Modal Syariah (SPMS) Tingkat Lanjutan',
                'deskripsi' => 'Analisis rasio keuangan syariah, mekanisme sukuk korporasi, dan simulasi transaksi saham syariah JII.',
                'jenis' => 'SPM',
                'tanggal' => '2026-04-09',
                'mulai' => '09:00',
                'selesai' => '13:00',
                'lokasi' => 'Auditorium UINFAS Bengkulu',
                'mitra' => 'GI Syariah BEI UINFAS Bengkulu',
                'user_email' => 'gi.uinfas@idx-bengkulu.id',
                'kuota' => 100,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Edukasi Manajemen Keuangan Bisnis & Pasar Modal bagi Pengusaha Muda HIPMI',
                'deskripsi' => 'Pemanfaatan instrumen pasar modal untuk likuiditas bisnis serta persiapan alternatif pendanaan UKM.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-04-23',
                'mulai' => '14:00',
                'selesai' => '17:00',
                'lokasi' => 'Meeting Room Hotel Santika Bengkulu',
                'mitra' => 'BPC HIPMI Kota Bengkulu',
                'user_email' => 'hipmi.bengkulu@gmail.com',
                'kuota' => 65,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- MEI 2026 ---
            [
                'nama' => 'DPM Goes to School: Literasi Finansial Remaja SMAN 1 Kota Bengkulu',
                'deskripsi' => 'Pengenalan budaya menabung saham sejak dini dan cerdas membedakan kebutuhan vs keinginan finansial.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-05-14',
                'mulai' => '08:30',
                'selesai' => '11:30',
                'lokasi' => 'Aula SMAN 1 Kota Bengkulu',
                'mitra' => 'Duta Pasar Modal BEI Bengkulu',
                'user_email' => 'dpm.bengkulu@idx-bengkulu.id',
                'kuota' => 85,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Workshop Analisis Teknikal Saham: Membaca Indikator Moving Average & RSI',
                'deskripsi' => 'Praktek charting langsung menggunakan platform sekuritas mitra untuk momentum entry dan exit perdagangan saham.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-05-28',
                'mulai' => '09:00',
                'selesai' => '14:30',
                'lokasi' => 'Laboratorium Komputer UNIVED Bengkulu',
                'mitra' => 'GI BEI Universitas Dehasen',
                'user_email' => 'gi.unived@idx-bengkulu.id',
                'kuota' => 70,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- JUNI 2026 ---
            [
                'nama' => 'Sekolah Pasar Modal Terpadu Batch Pertengahan Tahun 2026',
                'deskripsi' => 'Pelatihan intensif 1 hari penuh meliputi materi Level 1 & 2 serta sertifikasi kelulusan SPM resmi IDX.',
                'jenis' => 'SPM',
                'tanggal' => '2026-06-11',
                'mulai' => '08:30',
                'selesai' => '16:00',
                'lokasi' => 'Gedung Serba Guna FEB UNIB',
                'mitra' => 'GI BEI FEB Universitas Bengkulu',
                'user_email' => 'gi.unib@idx-bengkulu.id',
                'kuota' => 135,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Temu Investor Muda Bengkulu: Strategi Portofolio Hadapi Volatilitas Global',
                'deskripsi' => 'Forum diskusi interaktif investor ritel lokal dengan analis sekuritas terkait sentimen suku bunga bank sentral.',
                'jenis' => 'Edukasi',
                'tanggal' => '2026-06-25',
                'mulai' => '15:30',
                'selesai' => '18:00',
                'lokasi' => 'Kaba Kafe & Resto Pantai Panjang Bengkulu',
                'mitra' => 'Duta Pasar Modal BEI Bengkulu',
                'user_email' => 'dpm.bengkulu@idx-bengkulu.id',
                'kuota' => 55,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- JULI 2026 ---
            [
                'nama' => 'Sosialisasi Obligasi Negara Ritel (ORI) & Sukuk Tabungan bagi Pegawai BUMN Bengkulu',
                'deskripsi' => 'Pengenalan instrumen SBN Ritel yang dijamin negara, aman dari gagal bayar, dan imbal hasil kompetitif.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-07-09',
                'mulai' => '09:00',
                'selesai' => '12:00',
                'lokasi' => 'Aula Pertemuan Kantor Pos Indonesia Bengkulu',
                'mitra' => 'BKD Pemerintah Provinsi Bengkulu',
                'user_email' => 'bkd.pemprov@bengkuluprov.go.id',
                'kuota' => 95,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Workshop Tata Kelola Galeri Investasi BEI Menuju Akreditasi Nasional',
                'deskripsi' => 'Rapat koordinasi dan standarisasi operasional GI BEI perguruan tinggi mitra wilayah Bengkulu.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-07-23',
                'mulai' => '10:00',
                'selesai' => '15:00',
                'lokasi' => 'Ruang Rapat KP BEI Bengkulu',
                'mitra' => null,
                'user_email' => null,
                'kuota' => 35,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- AGUSTUS 2026 ---
            [
                'nama' => 'Kompetisi Analisis Saham Mahasiswa Se-Provinsi Bengkulu 2026',
                'deskripsi' => 'Perlombaan riset emiten dan presentasi stock pitch di hadapan dewan juri analis sekuritas profesional.',
                'jenis' => 'Edukasi',
                'tanggal' => '2026-08-13',
                'mulai' => '08:30',
                'selesai' => '16:00',
                'lokasi' => 'Auditorium Kampus 4 Universitas Muhammadiyah Bengkulu',
                'mitra' => 'GI BEI Universitas Muhammadiyah Bengkulu',
                'user_email' => 'gi.umb@idx-bengkulu.id',
                'kuota' => 90,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Sosialisasi Perlindungan Pemodal Pasar Modal & Peran SIPF',
                'deskripsi' => 'Mekanisme jaminan keamanan dana investor melalui Indonesia SIPF (Securities Investor Protection Fund).',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-08-27',
                'mulai' => '09:30',
                'selesai' => '12:00',
                'lokasi' => 'Ruang Rapat Kantor OJK Provinsi Bengkulu',
                'mitra' => null,
                'user_email' => null,
                'kuota' => 50,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- SEPTEMBER 2026 ---
            [
                'nama' => 'Sekolah Pasar Modal Mahasiswa Baru Fakultas Ekonomi dan Bisnis 2026',
                'deskripsi' => 'Program orientasi literasi pasar modal terintegrasi dengan mata kuliah pengantar investasi UNIB.',
                'jenis' => 'SPM',
                'tanggal' => '2026-09-10',
                'mulai' => '08:00',
                'selesai' => '14:00',
                'lokasi' => 'Gedung Serba Guna Universitas Bengkulu',
                'mitra' => 'GI BEI FEB Universitas Bengkulu',
                'user_email' => 'gi.unib@idx-bengkulu.id',
                'kuota' => 220,
                'status' => 'selesai',
                'has_lpj' => true,
            ],
            [
                'nama' => 'Roadshow Bulan Inklusi Keuangan (BIK) Rejang Lebong & Curup',
                'deskripsi' => 'Penyuluhan pasar modal bagi mahasiswa dan masyarakat di daerah Curup bersama mitra perbankan & sekuritas.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-09-24',
                'mulai' => '09:00',
                'selesai' => '13:30',
                'lokasi' => 'Aula Kampus IAIN Curup Bengkulu',
                'mitra' => null,
                'user_email' => null,
                'kuota' => 160,
                'status' => 'selesai',
                'has_lpj' => true,
            ],

            // --- OKTOBER 2026 (Bulan Berjalan) ---
            [
                'nama' => 'Capital Market Summit & Expo (CMSE) Bengkulu 2026',
                'deskripsi' => 'Pameran pasar modal tahunan terbesar di Bengkulu: booth sekuritas, talkshow emiten, dan konsultasi investasi gratis.',
                'jenis' => 'Edukasi',
                'tanggal' => '2026-10-18',
                'mulai' => '10:00',
                'selesai' => '21:00',
                'lokasi' => 'Atrium Utama Bencoolen Mall Bengkulu',
                'mitra' => 'Duta Pasar Modal BEI Bengkulu',
                'user_email' => 'dpm.bengkulu@idx-bengkulu.id',
                'kuota' => 350,
                'status' => 'berlangsung',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Workshop Manajemen Risiko Investasi & Penggunaan Fitur Stop Loss',
                'deskripsi' => 'Teknik menjaga psikologi trading, alokasi aset proporsional, dan disiplin pembatasan risiko modal.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-10-24',
                'mulai' => '09:00',
                'selesai' => '13:00',
                'lokasi' => 'Lab Komputer Lantai 3 UNIVED Bengkulu',
                'mitra' => 'GI BEI Universitas Dehasen',
                'user_email' => 'gi.unived@idx-bengkulu.id',
                'kuota' => 70,
                'status' => 'dipublikasikan',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Sekolah Pasar Modal (SPM) Syariah Spesial Santri & Akademisi',
                'deskripsi' => 'Edukasi pasar modal syariah memperingati Hari Santri Nasional bersama OJK dan BEI Bengkulu.',
                'jenis' => 'SPM',
                'tanggal' => '2026-10-29',
                'mulai' => '09:00',
                'selesai' => '14:30',
                'lokasi' => 'Gedung Rektorat UINFAS Bengkulu',
                'mitra' => 'GI Syariah BEI UINFAS Bengkulu',
                'user_email' => 'gi.uinfas@idx-bengkulu.id',
                'kuota' => 110,
                'status' => 'dipublikasikan',
                'has_lpj' => false,
            ],

            // --- NOVEMBER 2026 (Jadwal Mendatang) ---
            [
                'nama' => 'Sosialisasi Go Public (IPO) bagi Pengusaha Lokal Bengkulu',
                'deskripsi' => 'Workshop peluang pendanaan ekspansi bisnis melalui Initial Public Offering di Papan Pengembangan & Akselerasi IDX.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-11-05',
                'mulai' => '09:00',
                'selesai' => '13:00',
                'lokasi' => 'Ballroom Hotel Grage Horizon Bengkulu',
                'mitra' => 'BPC HIPMI Kota Bengkulu',
                'user_email' => 'hipmi.bengkulu@gmail.com',
                'kuota' => 60,
                'status' => 'dipublikasikan',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Pelatihan Analisis Candlestick & Price Action Investor Pemula UMB',
                'deskripsi' => 'Pendalaman psikologi pasar melalui formasi candlestick harian dan identifikasi pola pembalikan arah tren harga.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-11-12',
                'mulai' => '09:00',
                'selesai' => '12:30',
                'lokasi' => 'Aula Fakultas Ekonomi UMB',
                'mitra' => 'GI BEI Universitas Muhammadiyah Bengkulu',
                'user_email' => 'gi.umb@idx-bengkulu.id',
                'kuota' => 80,
                'status' => 'dipublikasikan',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Edukasi Pengenalan Waran Terstruktur & ETF sebagai Alternatif Diversifikasi',
                'deskripsi' => 'Mengenal produk terstruktur bursa untuk lindung nilai (hedging) dan diversifikasi portofolio investor aktif.',
                'jenis' => 'Edukasi',
                'tanggal' => '2026-11-19',
                'mulai' => '13:30',
                'selesai' => '16:00',
                'lokasi' => 'Ruang Seminar GI UNIB',
                'mitra' => 'GI BEI FEB Universitas Bengkulu',
                'user_email' => 'gi.unib@idx-bengkulu.id',
                'kuota' => 60,
                'status' => 'disetujui',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Webinar Nasional: Tantangan dan Peluang Pasar Modal Indonesia Menjelang 2027',
                'deskripsi' => 'Webinar daring bersama pembicara analis makroekonomi KSEI, BEI pusat, dan ekonom Universitas Bengkulu.',
                'jenis' => 'Webinar',
                'tanggal' => '2026-11-26',
                'mulai' => '13:30',
                'selesai' => '16:00',
                'lokasi' => 'Platform Zoom Webinar KP BEI Bengkulu',
                'mitra' => null,
                'user_email' => null,
                'kuota' => 500,
                'status' => 'disetujui',
                'has_lpj' => false,
            ],

            // --- DESEMBER 2026 (Proposal Mitra Menunggu Approval & Ditinjau) ---
            [
                'nama' => 'Market Outlook 2027: Prospek Saham Perbankan & Komoditas Pasca Pemilu Global',
                'deskripsi' => 'Analisis proyeksi pasar modal tahun 2027, rekomendasi sektor defensif, dan strategi alokasi portofolio.',
                'jenis' => 'Seminar',
                'tanggal' => '2026-12-03',
                'mulai' => '09:00',
                'selesai' => '13:00',
                'lokasi' => 'Gedung Rektorat Universitas Bengkulu Lantai 3',
                'mitra' => 'GI BEI FEB Universitas Bengkulu',
                'user_email' => 'gi.unib@idx-bengkulu.id',
                'kuota' => 150,
                'status' => 'menunggu_approval',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Klinik Portofolio Akhir Tahun & Tax Planning Aset Finansial',
                'deskripsi' => 'Evaluasi kinerja porto saham 2026, rebalancing aset, dan panduan pelaporan SPT Tahunan instrumen pasar modal.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-12-10',
                'mulai' => '09:00',
                'selesai' => '12:30',
                'lokasi' => 'Aula FEBI UIN FAS Bengkulu',
                'mitra' => 'GI Syariah BEI UINFAS Bengkulu',
                'user_email' => 'gi.uinfas@idx-bengkulu.id',
                'kuota' => 100,
                'status' => 'menunggu_approval',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Pelatihan Sertifikasi WPPE (Wakil Perantara Pedagang Efek) Pemasaran',
                'deskripsi' => 'Bimbingan intensif persiapan ujian sertifikasi profesi pasar modal lisensi WPPE bagi mahasiswa tingkat akhir.',
                'jenis' => 'Workshop',
                'tanggal' => '2026-12-17',
                'mulai' => '08:30',
                'selesai' => '15:30',
                'lokasi' => 'Kampus 1 UNIVED Bengkulu',
                'mitra' => 'GI BEI Universitas Dehasen',
                'user_email' => 'gi.unived@idx-bengkulu.id',
                'kuota' => 45,
                'status' => 'diproses',
                'has_lpj' => false,
            ],
            [
                'nama' => 'Sosialisasi Literasi Investasi untuk Tenaga Kesehatan RSUD M. Yunus Bengkulu',
                'deskripsi' => 'Pengenalan perencanaan keuangan mandiri dan investasi reksadana pendapatan tetap untuk dokter dan perawat.',
                'jenis' => 'Sosialisasi',
                'tanggal' => '2026-12-22',
                'mulai' => '13:00',
                'selesai' => '15:30',
                'lokasi' => 'Aula Gedung Diklat RSUD M. Yunus Bengkulu',
                'mitra' => 'BKD Pemerintah Provinsi Bengkulu',
                'user_email' => 'bkd.pemprov@bengkuluprov.go.id',
                'kuota' => 75,
                'status' => 'revisi',
                'has_lpj' => false,
            ],
        ];

        // 4. Bersihkan data seeder sebelumnya jika perlu, lalu insert
        Kegiatan::query()->delete();

        foreach ($kegiatanData as $item) {
            $userModel = !empty($item['user_email']) && isset($users[$item['user_email']])
                ? $users[$item['user_email']]
                : null;

            $kegiatan = Kegiatan::create([
                'user_id' => $userModel ? $userModel->id : null,
                'nama_kegiatan' => $item['nama'],
                'deskripsi' => $item['deskripsi'],
                'jenis_kegiatan' => $item['jenis'],
                'tanggal' => $item['tanggal'],
                'waktu_mulai' => $item['mulai'],
                'waktu_selesai' => $item['selesai'],
                'lokasi' => $item['lokasi'],
                'mitra' => $item['mitra'],
                'kuota_peserta' => $item['kuota'],
                'status' => $item['status'],
                'reviewed_by_admin_id' => in_array($item['status'], ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai']) ? $admin->id : null,
                'approval_processed_at' => in_array($item['status'], ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai']) ? now() : null,
                'approval_note' => $item['status'] === 'revisi'
                    ? 'Mohon lengkapi surat pengantar resmi dari pimpinan RSUD dan lampiran rincian susunan acara (rundown).'
                    : ($item['status'] === 'disetujui' ? 'Proposal telah sesuai dengan panduan edukasi BEI Bengkulu.' : null),
            ]);

            // Agenda utama
            Agenda::create([
                'kegiatan_id' => $kegiatan->id,
                'judul' => $item['nama'],
                'tanggal' => $item['tanggal'],
                'waktu_mulai' => $item['mulai'],
                'waktu_selesai' => $item['selesai'],
                'lokasi' => $item['lokasi'],
                'keterangan' => 'Sesi utama kegiatan edukasi dan sosialisasi pasar modal BEI Bengkulu.',
                'is_h_minus_1' => false,
            ]);

            // Peserta dummy jika ada user
            if ($allUsers->isNotEmpty() && in_array($item['status'], ['selesai', 'berlangsung', 'dipublikasikan'])) {
                $sampleUsers = $allUsers->take(min(3, $allUsers->count()));
                foreach ($sampleUsers as $u) {
                    $kegiatan->users()->syncWithoutDetaching([
                        $u->id => ['status' => $item['status'] === 'selesai' ? 'hadir' : 'terdaftar']
                    ]);
                }
            }

            // Laporan LPJ jika kegiatan selesai
            if (!empty($item['has_lpj'])) {
                LaporanKegiatan::create([
                    'kegiatan_id' => $kegiatan->id,
                    'admin_id' => $admin->id,
                    'judul_laporan' => 'Laporan Pertanggungjawaban (LPJ): '.$item['nama'],
                    'tanggal_laporan' => $item['tanggal'],
                    'isi_laporan' => 'Kegiatan telah sukses diselenggarakan di '.$item['lokasi'].'. Tingkat partisipasi dan antusiasme peserta sangat baik sesuai target kuota.',
                    'total_peserta_hadir' => (int) round($item['kuota'] * 0.95),
                    'status' => 'final',
                ]);
            }

            // Catatan Riwayat Aktivitas
            if ($userModel) {
                Aktivitas::create([
                    'user_id' => $userModel->id,
                    'admin_id' => null,
                    'kegiatan_id' => $kegiatan->id,
                    'aktor_nama' => $userModel->name,
                    'aktor_role' => 'mitra',
                    'aksi' => 'pengajuan_proposal',
                    'deskripsi' => "Mitra {$userModel->name} mengajukan proposal kegiatan '{$item['nama']}'",
                    'created_at' => $kegiatan->created_at,
                    'updated_at' => $kegiatan->created_at,
                ]);
            }

            if (in_array($item['status'], ['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'])) {
                Aktivitas::create([
                    'user_id' => $userModel?->id,
                    'admin_id' => $admin->id,
                    'kegiatan_id' => $kegiatan->id,
                    'aktor_nama' => $admin->name,
                    'aktor_role' => 'admin',
                    'aksi' => 'approval_proposal',
                    'deskripsi' => "Admin {$admin->name} menyetujui proposal kegiatan '{$item['nama']}'",
                    'created_at' => $kegiatan->approval_processed_at ?? $kegiatan->created_at,
                    'updated_at' => $kegiatan->approval_processed_at ?? $kegiatan->created_at,
                ]);
            }

            if (in_array($item['status'], ['dipublikasikan', 'berlangsung', 'selesai'])) {
                Aktivitas::create([
                    'user_id' => $userModel?->id,
                    'admin_id' => $admin->id,
                    'kegiatan_id' => $kegiatan->id,
                    'aktor_nama' => $admin->name,
                    'aktor_role' => 'admin',
                    'aksi' => 'perubahan_status',
                    'deskripsi' => "Admin {$admin->name} mengubah status kegiatan '{$item['nama']}' menjadi ".ucfirst($item['status']),
                    'created_at' => $kegiatan->created_at,
                    'updated_at' => $kegiatan->created_at,
                ]);
            }
        }
    }
}
