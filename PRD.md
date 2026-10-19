# PRD — SIPENA-IDX

> **Sistem Informasi Perencanaan Kegiatan Edukasi dan Sosialisasi IDX Bengkulu**
>
> Dokumen ini diturunkan dari *System Proposal* SIPENA-IDX versi 2.0, September 2026.

## 1. Ringkasan Produk

SIPENA-IDX adalah sistem informasi berbasis web untuk membantu perencanaan, pengajuan, publikasi, pelaksanaan, partisipasi, dan pelaporan kegiatan edukasi dan sosialisasi di IDX Bengkulu.

Pada proses **As-Is**, perencanaan dan pelaksanaan kegiatan masih dilakukan secara manual. Admin menyusun rencana, mengajukan anggaran/kegiatan, menunggu proses pemeriksaan, menyiapkan kegiatan, menyampaikan informasi kepada peserta, melaksanakan kegiatan, membuat laporan, dan mengarsipkan dokumen.

Pada proses **To-Be**, seluruh alur utama dikelola melalui satu sistem: Admin login, membuat rencana, mengisi detail, mengajukan, memantau status, melakukan revisi bila diperlukan, mempublikasikan kegiatan, mengunggah agenda H-1, memantau peserta, dan membuat laporan. User dapat melihat kegiatan, detail jadwal/lokasi/waktu, menerima notifikasi, serta mengikuti kegiatan.

## 2. Tujuan Produk

### 2.1 Tujuan Utama

1. Mengintegrasikan proses perencanaan sampai pelaporan kegiatan dalam satu sistem.
2. Mengurangi proses manual dalam pengelolaan kegiatan.
3. Menyediakan status pengajuan yang dapat dipantau.
4. Menyediakan informasi kegiatan yang dapat diakses User.
5. Mengirimkan notifikasi untuk perubahan status dan agenda H-1.
6. Menyimpan data peserta dan laporan secara digital.
7. Menyediakan data yang dapat digunakan kembali untuk kebutuhan pelaporan dan monitoring.

### 2.2 Sasaran

- **Admin IDX:** pengelolaan kegiatan secara terstruktur.
- **User/Pengguna:** akses informasi dan partisipasi kegiatan.
- **Pengelola sistem:** memperoleh data terorganisasi dan jejak aktivitas.

## 3. Aktor

| Aktor | Deskripsi |
|---|---|
| Admin IDX | Mengelola rencana, detail, pengajuan, kegiatan, agenda, peserta, laporan, dan notifikasi. |
| User/Pengguna | Melihat informasi kegiatan, detail jadwal/lokasi/waktu, mendaftar/mengikuti kegiatan, melihat status keikutsertaan, dan menerima notifikasi. |
| Sistem | Menyimpan data, memproses status, melakukan validasi, mengirim notifikasi, menerapkan hak akses, dan menyediakan pencarian/filter. |

> Catatan: System Proposal juga menyebut Admin sebagai entitas tersendiri pada model struktural/ERD. Pada implementasi, mekanisme representasi Admin perlu diputuskan apakah berupa entitas terpisah atau role pada entitas User. PRD ini mempertahankan istilah proposal tanpa memaksakan implementasi database tertentu.

## 4. Scope Produk

### In Scope

- Autentikasi Admin dan User.
- CRUD rencana kegiatan.
- Pengelolaan detail kegiatan.
- Pengajuan kegiatan.
- Pemantauan status pengajuan.
- Revisi dan pengajuan kembali.
- Publikasi kegiatan.
- Pengelolaan agenda termasuk agenda H-1.
- Pendaftaran/keikutsertaan User.
- Monitoring peserta oleh Admin.
- Notifikasi kegiatan, perubahan status, dan agenda H-1.
- Pembuatan dan pengelolaan laporan kegiatan.
- Profil User.
- Pencarian dan filtering data.
- Riwayat data.
- Audit trail.
- Upload file laporan.
- Backup data.
- Integrasi Google Calendar yang disebut sebagai kebutuhan operasional.

### Out of Scope / Belum Didefinisikan

Proposal tidak mendefinisikan secara rinci:
- payment/gateway;
- chat real-time;
- absensi dengan QR;
- penilaian kegiatan;
- multi-organisasi/tenant;
- detail mekanisme approval selain status disetujui/ditolak/revisi;
- provider notifikasi tertentu;
- spesifikasi API publik.

Fitur tersebut tidak boleh dianggap sebagai requirement MVP tanpa keputusan lanjutan.

## 5. User Journey

### 5.1 Admin

```text
Login
  ↓
Dashboard
  ↓
Buat Rencana Kegiatan
  ↓
Input Detail Kegiatan
  ↓
Simpan
  ↓
Ajukan Kegiatan
  ↓
Status Pengajuan
  ├── Perlu Revisi → Revisi → Ajukan Kembali
  └── Disetujui
        ↓
     Publikasikan
        ↓
     Unggah Agenda H-1
        ↓
     Pantau Peserta
        ↓
     Buat Laporan
        ↓
     Arsip/riwayat data
```

### 5.2 User

```text
Registrasi/Login
  ↓
Beranda / Daftar Kegiatan
  ↓
Detail Kegiatan
  ├── Jadwal
  ├── Lokasi
  ├── Waktu
  └── Informasi kegiatan
  ↓
Ikuti/Daftar Kegiatan
  ↓
Status Keikutsertaan
  ↓
Terima Notifikasi
  ↓
Ikuti Kegiatan
  ↓
Lihat Laporan jika tersedia
```

## 6. Functional Requirements

### 6.1 Admin

| ID | Requirement | Prioritas |
|---|---|---|
| FR-A1 | Admin dapat login menggunakan email dan password serta logout. | Must |
| FR-A2 | Admin dapat menambah, melihat, mengubah, dan menghapus rencana kegiatan. | Must |
| FR-A3 | Admin dapat mengelola detail kegiatan: deskripsi, jenis kegiatan, lokasi, dan waktu pelaksanaan. | Must |
| FR-A4 | Admin dapat melihat, memeriksa, memproses, menerima, atau menolak pengajuan kegiatan. | Must |
| FR-A5 | Admin dapat mengelola kegiatan berdasarkan pengajuan yang telah diproses. | Must |
| FR-A6 | Admin dapat CRUD agenda kegiatan, termasuk agenda H-1. | Must |
| FR-A7 | Admin dapat membuat dan mengelola laporan kegiatan serta file laporan. | Must |
| FR-A8 | Admin dapat melihat dan mengelola daftar peserta serta status keikutsertaan. | Must |
| FR-A9 | Admin dapat membuat dan mengirim notifikasi terkait kegiatan, agenda, status, dan informasi penting. | Must |
| FR-A10 | Admin dapat memonitor proses dari rencana, pengajuan, pelaksanaan, peserta, sampai laporan. | Must |

### 6.2 User

| ID | Requirement | Prioritas |
|---|---|---|
| FR-U1 | User dapat registrasi, login, dan logout. | Must |
| FR-U2 | User dapat melihat daftar kegiatan beserta nama, deskripsi, tanggal, lokasi, dan status. | Must |
| FR-U3 | User dapat melihat agenda berupa tanggal, waktu, lokasi, dan keterangan. | Must |
| FR-U4 | User dapat mendaftarkan diri sebagai peserta kegiatan. | Must |
| FR-U5 | User dapat melihat status pendaftaran/keikutsertaan. | Must |
| FR-U6 | User dapat melihat laporan kegiatan apabila tersedia untuk User. | Should |
| FR-U7 | User dapat menerima dan melihat notifikasi. | Must |
| FR-U8 | User dapat melihat dan mengubah profil: nama, email, password, dan nomor telepon. | Must |

### 6.3 Functional Requirements Umum

| ID | Requirement | Prioritas |
|---|---|---|
| FR-G1 | Sistem mengirim notifikasi otomatis untuk informasi penting, perubahan status, perubahan agenda, dan informasi kegiatan. | Must |
| FR-G2 | Sistem menerapkan Role-Based Access Control untuk Admin dan User. | Must |
| FR-G3 | Sistem memvalidasi input rencana, pengajuan, kegiatan, peserta, agenda, laporan, dan notifikasi. | Must |
| FR-G4 | Sistem menyimpan dan memperbarui status rencana, pengajuan, kegiatan, dan peserta. | Must |
| FR-G5 | Sistem menyimpan file laporan dan menghubungkannya dengan kegiatan. | Must |
| FR-G6 | Sistem menyediakan pencarian/filter berdasarkan nama, tanggal, lokasi, dan status. | Should |
| FR-G7 | Sistem menyimpan riwayat data kegiatan, peserta, agenda, dan laporan. | Must |
| FR-G8 | Sistem mengamankan autentikasi menggunakan email/password dan pembatasan akses. | Must |

## 7. Status Utama

Proposal menyatakan adanya proses status pengajuan dan kondisi disetujui/ditolak/revisi. Implementasi sebaiknya menggunakan state machine agar transisi konsisten.

Contoh state yang diturunkan dari alur proposal:

```text
Draft
  ↓
Diajukan
  ↓
Diproses
  ├── Perlu Revisi → Direvisi → Diajukan kembali
  ├── Ditolak
  └── Disetujui
        ↓
Dipublikasikan
        ↓
Dilaksanakan
        ↓
Dilaporkan
```

Status final dan aturan transisinya perlu dikonfirmasi pada tahap desain teknis karena proposal tidak memberikan enumerasi status yang lengkap.

## 8. Notifikasi

Sistem harus mendukung notifikasi untuk:

1. Pengajuan berhasil dikirim.
2. Perubahan status pengajuan.
3. Permintaan revisi.
4. Pengajuan disetujui.
5. Informasi kegiatan dipublikasikan.
6. Agenda H-1.
7. Perubahan agenda.
8. Informasi penting lain dari Admin.

Sequence diagram proposal secara eksplisit menunjukkan layanan notifikasi digunakan ketika status berubah dan ketika agenda H-1 dikirim.

## 9. Data Domain

Entitas/model yang disebutkan dalam proposal:

- `User`
- `Admin`
- `RencanaKegiatan`
- `DetailKegiatan`
- `PengajuanKegiatan`
- `Kegiatan`
- `Agenda`
- `Peserta`
- `LaporanKegiatan`
- `Notifikasi`

Hubungan bisnis utamanya:

```text
Admin
  └── membuat/mengelola RencanaKegiatan
          ├── memiliki DetailKegiatan
          └── memiliki PengajuanKegiatan
                    ↓
                 Kegiatan
                 ├── Agenda
                 ├── Peserta ← User
                 └── LaporanKegiatan

User
  ├── mengikuti Kegiatan melalui Peserta
  └── menerima Notifikasi
```

Proposal menyebutkan bahwa ERD mencakup Admin, Rencana Kegiatan, Detail Kegiatan, Pengajuan Kegiatan, Kegiatan, Agenda, User, Peserta, Laporan Kegiatan, dan Notifikasi.

## 10. CRUD Matrix

Berdasarkan CRUD Analysis pada proposal:

| Model | User | Admin |
|---|---|---|
| RencanaKegiatan | R | CRUD |
| DetailKegiatan | R | CRUD |
| PengajuanKegiatan | R | CRUD |
| Kegiatan | R | CRUD |
| Agenda | R | CRUD |
| Peserta | C | CRUD |
| LaporanKegiatan | R | CRUD |
| Notifikasi | R | CRUD |

Interpretasi:
- `C` = Create
- `R` = Read
- `U` = Update
- `D` = Delete

## 11. Non-Functional Requirements

### 11.1 Operational

- Web-based.
- Dapat diakses melalui Chrome, Edge, dan Firefox.
- Dapat digunakan dari laptop maupun perangkat seluler.
- Tidak memerlukan instalasi tambahan pada sisi pengguna.
- Backup data otomatis setiap hari.
- Integrasi Google Calendar.
- Uptime minimal 99%.
- Audit trail aktivitas pengguna.

### 11.2 Performance

- Waktu respons halaman utama dan transaksi umum maksimal 3 detik.
- Minimal 20 pengguna aktif secara bersamaan.
- Ekspor/rekap PDF atau Excel maksimal 15 detik.
- Kapasitas penyimpanan minimal 10 GB data aktif per semester.

### 11.3 Security

- HTTPS/SSL.
- Kredensial unik email/password.
- Password disimpan menggunakan hash.
- RBAC.
- Auto logout setelah 15 menit tidak ada aktivitas.
- Validasi input untuk mengurangi risiko SQL Injection dan XSS.

### 11.4 Usability

- Antarmuka sederhana dan konsisten.
- Bahasa Indonesia.
- Error message dan notifikasi informatif.
- Responsive.
- Panduan/help untuk modul utama.

### 11.5 Maintainability

- Proposal merekomendasikan framework web modern seperti Laravel dan database MySQL.
- Kode modular.
- Dokumentasi struktur database, API, dan arsitektur.
- Konfigurasi dapat diubah melalui menu konfigurasi tanpa modifikasi kode.

### 11.6 Portability

- Dapat berjalan pada server Windows maupun Linux.
- Kompatibel dengan browser modern minimal dua tahun terakhir.
- Database dapat diekspor/impor tanpa kehilangan integritas.
- Dapat berjalan pada jaringan lokal maupun internet publik.

## 12. Acceptance Criteria Tingkat Produk

### Rencana & Pengajuan

- Admin dapat membuat rencana.
- Detail kegiatan dapat disimpan.
- Rencana dapat diajukan.
- Status pengajuan dapat dilihat.
- Pengajuan yang membutuhkan revisi dapat diperbaiki dan diajukan kembali.
- Perubahan status memicu notifikasi sesuai aturan.

### Publikasi & Partisipasi

- Kegiatan yang disetujui dapat dipublikasikan.
- User hanya melihat informasi kegiatan yang memang tersedia/dipublikasikan.
- User dapat melihat jadwal, lokasi, dan waktu.
- User dapat mengikuti kegiatan.
- Data keikutsertaan tersimpan.

### Agenda & Notifikasi

- Admin dapat membuat/mengubah agenda.
- Agenda H-1 dapat dikirim sebagai notifikasi.
- User dapat melihat notifikasi.

### Pelaporan

- Admin dapat melihat peserta suatu kegiatan.
- Admin dapat membuat laporan berdasarkan data kegiatan/peserta.
- Laporan tersimpan dan dapat ditampilkan kembali.
- File laporan dapat dikelola sesuai hak akses.

## 13. Success Metrics

Proposal tidak menetapkan KPI bisnis kuantitatif selain kebutuhan teknis. Untuk implementasi, metrik berikut dapat digunakan sebagai metrik operasional, bukan sebagai requirement baru:

- Persentase pengajuan yang berhasil diproses tanpa kehilangan data.
- Persentase notifikasi berhasil dikirim.
- Waktu respons rata-rata.
- Jumlah error transaksi.
- Jumlah kegiatan yang terdokumentasi lengkap dari rencana sampai laporan.
- Ketersediaan sistem.

## 14. Dependencies

- Database relasional.
- Layanan autentikasi/session.
- Layanan penyimpanan file.
- Layanan notifikasi.
- Integrasi Google Calendar.
- Infrastruktur HTTPS/SSL.
- Mekanisme backup.

Provider dan detail teknis layanan tersebut belum ditetapkan dalam System Proposal.

## 15. Risiko Produk

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Status pengajuan tidak konsisten | Alur kegiatan terganggu | Gunakan state transition yang tervalidasi |
| Notifikasi gagal | User tidak menerima pengingat | Simpan status pengiriman dan retry |
| Upload file tidak tervalidasi | Risiko keamanan/storage | Validasi tipe, ukuran, nama file, dan permission |
| Hak akses salah | Data dapat diakses role yang tidak sesuai | RBAC + authorization policy |
| Kehilangan data | Dokumen/laporan hilang | Backup otomatis dan pengujian restore |
| Beban meningkat | Respons sistem menurun | Pagination, indexing, caching sesuai kebutuhan |

## 16. MVP

MVP minimal mencakup:

1. Authentication dan RBAC.
2. Dashboard.
3. CRUD Rencana Kegiatan.
4. Detail Kegiatan.
5. Pengajuan dan status.
6. Revisi/pengajuan kembali.
7. Publikasi kegiatan.
8. Agenda.
9. User registration/login.
10. Daftar dan detail kegiatan.
11. Pendaftaran peserta.
12. Notifikasi.
13. Monitoring peserta.
14. Laporan kegiatan.
15. Profil User.
16. Pencarian/filter dasar.
17. Audit trail dan backup.

## 17. Traceability

Requirement utama berasal dari bagian *Requirement Definition*, *Use-Case Diagram*, *Activity Diagram*, *Sequence Diagram*, *CRUD Analysis*, dan *ERD* pada System Proposal SIPENA-IDX versi 2.0.

Dokumen sumber mendeskripsikan To-Be process pada halaman 9–10, functional requirements pada halaman 10–13, non-functional requirements pada halaman 14–16, functional model pada halaman 17–21, structural model pada halaman 22–23, behavioral model pada halaman 24–31, serta CRUD/ERD pada halaman 32–35.
