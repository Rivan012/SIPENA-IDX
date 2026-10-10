# SIPENA — IDX Bengkulu
### Sistem Informasi Perencanaan & Edukasi Pasar Modal
**PT Bursa Efek Indonesia (BEI) — Kantor Perwakilan Bengkulu**

SIPENA adalah platform web terintegrasi untuk perencanaan jadwal edukasi pasar modal, pemantauan alokasi anggaran belanja RKAP, alur approval proposal kegiatan, dan portal peserta edukasi publik.

---

## 🚀 Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Inertia.js v2 (`@inertiajs/vue3`), Vue 3, Tailwind CSS v4, Ziggy
- **Database**: MySQL / MariaDB (SQLite untuk test suite in-memory)
- **Build Tool**: Vite 8

---

## ✨ Fitur Utama

### 1. Landing Page Publik
- **Katalog Jadwal Edukasi**: Preview kegiatan mendatang (Oktober–November 2026).
- **Filter Interaktif**: Pencarian real-time berdasarkan topik, kategori, wilayah, dan format (Daring/Tatap Muka) tanpa reload.
- **Topik Belajar**: Dasar pasar modal, Sekolah Pasar Modal (SPM), pasar modal syariah, dan literasi investasi.
- **Informasi & FAQ**: Profil BEI, alur partisipasi, dan pertanyaan umum.

### 2. Portal Peserta (User)
- **Sidebar Navigasi Responsif**: Desain konsisten dengan navigasi sisi kiri dan drawer mobile.
- **Dashboard Peserta**: Ringkasan keikutsertaan dan agenda terdekat.
- **Katalog & Pendaftaran**: Pendaftaran mandiri dan pembatalan partisipasi kegiatan.
- **Kegiatan Saya**: Pelacakan status keikutsertaan acara edukasi.
- **Laporan Edukasi & Notifikasi**: Akses laporan kegiatan publik dan pusat pemberitahuan.
- **Profil Pengguna**: Pembaruan data akun dan kata sandi.

### 3. Modul Administrator
- **Dashboard Eksekutif**: Metrik jumlah kegiatan, realisasi vs pagu anggaran RKAP, pending approval, dan agenda terbaru.
- **Kalender Interaktif**: Tampilan kalender bulanan dengan panel detail agenda di sisi samping tanpa reload halaman saat tanggal diklik.
- **Manajemen Kegiatan**: CRUD kegiatan edukasi, kuota peserta, status pelaksanaan, dan ekspor CSV.
- **Pagu & Anggaran RKAP**: Alokasi anggaran program, rincian pos belanja detail kegiatan, perhitungan serapan RKAP, dan ekspor CSV.
- **Approval Pengajuan Proposal**: Verifikasi berjenjang (Setujui, Minta Revisi, Tolak), pencatatan catatan review, auto-create entri kegiatan saat proposal disetujui, dan ekspor CSV.
- **Monitoring & Pengguna**: Tab switching Peserta vs Administrator, pemantauan riwayat keikutsertaan, dan ekspor CSV.

---

## 🛠️ Panduan Instalasi

### 1. Prasyarat
- PHP >= 8.2 dengan ekstensi `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`
- Composer 2.x
- Node.js >= 18.x & npm

### 2. Langkah Setup

```bash
# Clone repository
git clone <repository-url>
cd SIPENA-IDX

# Install dependensi PHP
composer install

# Install dependensi Frontend
npm install

# Salin environment file
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Konfigurasi database pada file .env, kemudian jalankan migrasi & seeder
php artisan migrate --seed

# Kompilasi aset frontend
npm run build

# Jalankan local development server
php artisan serve
```

Aplikasi dapat diakses melalui browser pada: `http://localhost:8000`

---

## 👤 Akun Pengujian Default

| Role | Email | Password | Akses |
|---|---|---|---|
| **Administrator** | `admin@bei.co.id` | `password123` | `/admin/login` |
| **Peserta (User)** | `peserta@example.com` | `password123` | `/login` |

---

## 🧪 Pengujian Otomatis (Testing)

Proyek dilengkapi dengan 48 feature test PHPUnit yang mencakup seluruh alur otentikasi, perizinan, aksi CRUD, alur approval, navigasi kalender, dan kontrak respons Inertia.js:

```bash
php artisan test
```

**Hasil Verifikasi**:
```text
Tests:    48 passed (266 assertions)
Duration: ~2.0s
```

---

## 📁 Struktur Direktori Utama

```text
SIPENA-IDX/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/         # Controller operasional admin (Dashboard, Kegiatan, Kalender, Anggaran, Approval, Monitoring)
│   │   │   ├── User/          # Controller portal peserta (Dashboard, Kegiatan, Laporan, Notifikasi, Profil)
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   │       └── HandleInertiaRequests.php
│   └── Models/                # Model Eloquent (Kegiatan, RencanaKegiatan, DetailKegiatan, PengajuanKegiatan, dll.)
├── resources/
│   ├── js/
│   │   ├── Layouts/           # Master layouts (AdminLayout.vue, UserLayout.vue)
│   │   ├── Pages/             # Halaman Inertia Vue 3 (Admin, User, Auth, Welcome.vue)
│   │   └── app.js             # Inisialisasi Inertia & Ziggy
│   └── views/
│       └── app.blade.php      # Root HTML template Inertia
├── routes/
│   └── web.php                # Definisi rute aplikasi
└── tests/
    └── Feature/               # Test suites (AdminKegiatan, AdminKalender, AdminAnggaran, AdminApproval, AdminPengguna, Auth, dll.)
```

---

## 📄 Lisensi
Hak Cipta © 2026 PT Bursa Efek Indonesia — Kantor Perwakilan Bengkulu.
