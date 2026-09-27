# DESIGN.md — SIPENA-IDX

> **Sistem Informasi Perencanaan Kegiatan Edukasi dan Sosialisasi IDX Bengkulu**
>
> Design specification diturunkan dari System Proposal SIPENA-IDX versi 2.0.

## 1. Design Goal

Desain SIPENA-IDX harus mendukung satu alur kerja yang konsisten:

```text
Perencanaan
→ Pengajuan
→ Review/Status
→ Revisi bila diperlukan
→ Persetujuan
→ Publikasi
→ Agenda H-1
→ Partisipasi
→ Monitoring Peserta
→ Pelaporan
```

UI harus membedakan dengan jelas area **Admin** dan **User**, tetapi menggunakan bahasa visual dan komponen yang konsisten.

## 2. Design Principles

### 2.1 Clarity First

Informasi penting seperti status pengajuan, tanggal kegiatan, lokasi, dan agenda harus dapat ditemukan tanpa membuka banyak halaman.

### 2.2 Status Visibility

Status proses harus selalu terlihat pada daftar/detail data yang relevan.

### 2.3 Role-Oriented UI

Menu yang ditampilkan mengikuti role:

- Admin: management-oriented.
- User: discovery and participation-oriented.

### 2.4 Responsive by Default

Layout harus bekerja pada desktop, tablet, dan mobile. Proposal secara eksplisit mensyaratkan tampilan responsive.

### 2.5 Consistency

Form, table, badge, modal, toast, pagination, empty state, dan confirmation dialog harus menggunakan pola yang konsisten.

### 2.6 Safe Actions

Aksi destruktif seperti delete harus membutuhkan konfirmasi. Aksi perubahan status harus memberikan feedback yang jelas.

## 3. Information Architecture

```text
SIPENA-IDX
│
├── Public / Auth
│   ├── Landing / Beranda
│   ├── Login
│   └── Register
│
├── User
│   ├── Dashboard
│   ├── Kegiatan
│   │   ├── Daftar Kegiatan
│   │   └── Detail Kegiatan
│   ├── Kegiatan Saya
│   ├── Notifikasi
│   ├── Laporan Kegiatan
│   └── Profil
│
└── Admin
    ├── Dashboard
    ├── Rencana Kegiatan
    ├── Detail Kegiatan
    ├── Pengajuan
    ├── Kegiatan
    ├── Agenda
    ├── Peserta
    ├── Laporan Kegiatan
    ├── Notifikasi
    ├── Monitoring
    └── Konfigurasi
```

> Struktur menu di atas adalah penerjemahan UI dari requirement proposal. Nama menu dapat disesuaikan pada implementasi selama fungsi tetap tercakup.

## 4. Navigation

### Admin

Sidebar:

```text
Dashboard

PERENCANAAN
- Rencana Kegiatan
- Pengajuan
- Kegiatan
- Agenda

PELAKSANAAN
- Peserta
- Monitoring

PELAPORAN
- Laporan Kegiatan

KOMUNIKASI
- Notifikasi

SISTEM
- Konfigurasi
- Profil
- Logout
```

### User

Navbar/sidebar:

```text
Dashboard
Kegiatan
Kegiatan Saya
Notifikasi
Laporan
Profil
Logout
```

## 5. Dashboard Design

### 5.1 Admin Dashboard

Dashboard menampilkan ringkasan proses:

```text
┌───────────────────────────────────────────────────────────┐
│ Dashboard Admin                                           │
├────────────┬────────────┬────────────┬───────────────────┤
│ Draft      │ Diajukan   │ Disetujui  │ Kegiatan Aktif   │
│ 12         │ 4          │ 8          │ 3                 │
└────────────┴────────────┴────────────┴───────────────────┘

Pengajuan Terbaru
┌───────────────────────────────────────────────────────────┐
│ Nama Kegiatan | Tanggal | Status | Aksi                  │
└───────────────────────────────────────────────────────────┘

Kegiatan Terdekat
┌───────────────────────────────────────────────────────────┐
│ Kegiatan | Tanggal | Lokasi | Peserta | Status           │
└───────────────────────────────────────────────────────────┘
```

Tujuan utamanya adalah memberikan monitoring cepat terhadap proses dari rencana hingga laporan.

### 5.2 User Dashboard

```text
┌───────────────────────────────────────────────────────────┐
│ Selamat datang, [Nama]                                    │
├───────────────────────────────────────────────────────────┤
│ Kegiatan Mendatang                                        │
│                                                           │
│ [Card Kegiatan] [Card Kegiatan] [Card Kegiatan]           │
├───────────────────────────────────────────────────────────┤
│ Notifikasi Terbaru                                        │
└───────────────────────────────────────────────────────────┘
```

## 6. Design System

### 6.1 Visual Direction

Gunakan visual identity yang:

- profesional;
- informatif;
- ringan;
- mudah dipindai;
- tidak terlalu dekoratif pada halaman administrasi.

### 6.2 Color Roles

Warna tidak harus mengikuti warna tertentu dari proposal karena proposal tidak menetapkan palette. Gunakan semantic color roles:

| Token | Fungsi |
|---|---|
| `primary` | Aksi utama dan elemen brand |
| `secondary` | Aksi sekunder |
| `success` | Disetujui/berhasil |
| `warning` | Menunggu/perlu perhatian |
| `danger` | Ditolak/error/delete |
| `info` | Informasi |
| `surface` | Background/card |
| `text` | Teks utama |
| `muted` | Teks sekunder |
| `border` | Garis/pemisah |

### 6.3 Typography

Rekomendasi:

- Heading: sans-serif semibold/bold.
- Body: sans-serif regular.
- Label/form: medium.
- Status/badge: medium/semibold.

Prioritaskan keterbacaan dan hierarchy daripada dekorasi.

### 6.4 Spacing

Gunakan sistem spacing konsisten, misalnya kelipatan 4 atau 8 px:

```text
4   — micro spacing
8   — icon/text gap
12  — compact component
16  — default component spacing
24  — section spacing
32  — major section
48  — page-level spacing
```

## 7. Core Components

### 7.1 Button

Variants:

- Primary
- Secondary
- Outline
- Ghost
- Danger

States:

- Default
- Hover
- Focus
- Disabled
- Loading

### 7.2 Status Badge

Gunakan badge untuk:

- Draft
- Diajukan
- Diproses
- Perlu Revisi
- Ditolak
- Disetujui
- Dipublikasikan
- Dilaksanakan
- Selesai

Status harus memiliki teks; jangan hanya mengandalkan warna.

### 7.3 Data Table

Table digunakan terutama untuk Admin.

Kolom minimal menampilkan:

```text
Nama
Tanggal
Status
Updated
Action
```

Fitur:

- Search.
- Filter.
- Sort bila dibutuhkan.
- Pagination.
- Empty state.
- Loading state.
- Row action.

### 7.4 Form

Form harus:

- memiliki label;
- memiliki helper text jika diperlukan;
- menampilkan validasi inline;
- mempertahankan input saat validasi gagal;
- menampilkan success/error feedback;
- mengonfirmasi aksi yang berisiko.

### 7.5 Notification

Gunakan:

- toast untuk feedback transaksi;
- notification center untuk informasi yang harus dibaca kembali.

## 8. Screen Specification

### 8.1 Login

Elemen:

```text
Logo
Judul
Email
Password
[ Login ]
Lupa password? (jika fitur disediakan)
Register (untuk User)
```

Acceptance:
- Error credential jelas.
- Loading state ketika submit.
- Redirect berdasarkan role setelah berhasil.

### 8.2 Register User

Elemen minimal sesuai proposal:

```text
Nama
Email
Password
Konfirmasi Password
Nomor Telepon
[ Daftar ]
```

### 8.3 Rencana Kegiatan — Admin

List:

```text
[ + Buat Rencana ]

Search...
Filter Status...

┌──────────────────────────────────────────────────────┐
│ Nama | Jenis | Tanggal | Status | Aksi               │
└──────────────────────────────────────────────────────┘
```

Detail/create/edit harus mencakup data rencana dan detail kegiatan yang dibutuhkan.

### 8.4 Pengajuan — Admin

Detail pengajuan:

```text
Header
- Nama kegiatan
- Status
- Tanggal pengajuan

Detail
- Informasi kegiatan
- Detail kegiatan
- Catatan/status

Action
[ Ajukan ]
[ Revisi ]
[ Ajukan Kembali ]
```

Tampilkan timeline status agar perubahan mudah dipahami.

### 8.5 Kegiatan — User

Card kegiatan:

```text
┌─────────────────────────────┐
│ Nama Kegiatan               │
│ Deskripsi singkat           │
│                             │
│ Tanggal                     │
│ Lokasi                      │
│ Status                      │
│                             │
│ [ Lihat Detail ]            │
└─────────────────────────────┘
```

### 8.6 Detail Kegiatan — User

Struktur:

```text
Nama Kegiatan
Status

Deskripsi
Tanggal
Waktu
Lokasi
Jenis Kegiatan

Agenda
├── Agenda utama
└── Agenda H-1

[ Ikuti Kegiatan ]

Laporan (jika tersedia)
```

### 8.7 Pendaftaran Kegiatan

Sebelum submit:

```text
Konfirmasi keikutsertaan
Nama: [User]
Kegiatan: [Kegiatan]
Tanggal: [Tanggal]

[ Batal ] [ Ikuti Kegiatan ]
```

Setelah berhasil:

```text
Status: Terdaftar
```

### 8.8 Agenda — Admin

Admin dapat:

- melihat agenda;
- membuat agenda;
- mengubah agenda;
- menghapus agenda;
- menandai/mengelola agenda H-1.

Form:

```text
Kegiatan
Tanggal
Waktu
Lokasi
Keterangan
Jenis/penanda H-1
```

### 8.9 Peserta — Admin

Tampilan:

```text
Kegiatan: [Nama]

Total Peserta: XX

┌──────────────────────────────────────────┐
│ Nama | Email | Telepon | Status | Aksi   │
└──────────────────────────────────────────┘
```

### 8.10 Laporan — Admin

Alur:

```text
Pilih Kegiatan
    ↓
Sistem mengambil data kegiatan + peserta
    ↓
Admin membuat laporan
    ↓
Upload/isi laporan
    ↓
Simpan
    ↓
Laporan tersedia
```

Proposal menyebut laporan dapat berisi tanggal laporan, isi laporan, dan file laporan.

### 8.11 Notifikasi

Notification center:

```text
Notifikasi
────────────────────────
● Agenda H-1
  Kegiatan akan dilaksanakan besok.

● Status Pengajuan
  Pengajuan kegiatan telah disetujui.

○ Perubahan Agenda
  Waktu kegiatan telah diperbarui.
```

Unread notification harus dibedakan secara visual dan tetap dapat dibaca kembali.

## 9. UX Flow

### 9.1 Pengajuan

```text
Rencana
 ↓
Lengkapi Detail
 ↓
Review
 ↓
Ajukan
 ↓
Status: Diproses
 ↓
 ┌───────────────┐
 │               │
Revisi         Setujui
 │               │
 ↓               ↓
Edit          Publikasi
 ↓
Ajukan Kembali
```

### 9.2 Publikasi sampai Pelaporan

```text
Disetujui
 ↓
Publikasikan
 ↓
User melihat kegiatan
 ↓
User mengikuti kegiatan
 ↓
Admin unggah agenda H-1
 ↓
Sistem kirim notifikasi
 ↓
Kegiatan dilaksanakan
 ↓
Admin monitoring peserta
 ↓
Admin membuat laporan
```

## 10. Responsive Behavior

### Desktop

- Sidebar tetap.
- Table penuh.
- Detail menggunakan dua kolom jika sesuai.
- Dashboard menggunakan grid.

### Tablet

- Sidebar dapat collapse.
- Table dapat horizontal scroll.
- Form dapat menjadi satu kolom.

### Mobile

- Navigation menggunakan drawer/bottom navigation sesuai kebutuhan.
- Card menggantikan table jika data kompleks.
- Form satu kolom.
- Action utama tetap mudah dijangkau.
- Detail kegiatan diprioritaskan daripada informasi sekunder.

## 11. Accessibility

Minimum design rules:

- Semua input memiliki label.
- Focus state terlihat.
- Kontras teks memadai.
- Jangan menyampaikan status hanya melalui warna.
- Tombol memiliki nama aksi yang jelas.
- Icon-only button memiliki accessible label.
- Error form ditempatkan dekat field terkait.
- Layout dapat digunakan dengan keyboard pada desktop.

## 12. Feedback & Error Handling

### Success

```text
"Rencana kegiatan berhasil disimpan."
"Pengajuan kegiatan berhasil dikirim."
"Agenda H-1 berhasil diunggah."
"Laporan kegiatan berhasil disimpan."
```

### Error

Pesan harus menjelaskan tindakan yang perlu dilakukan, misalnya:

```text
"Data belum lengkap. Periksa field yang ditandai."
"Pengajuan tidak dapat dikirim karena detail kegiatan belum lengkap."
"File laporan tidak dapat diunggah. Periksa format dan ukuran file."
```

### Confirmation

Untuk delete:

```text
Hapus rencana kegiatan?
Data yang dihapus tidak dapat digunakan kembali.

[Batal] [Hapus]
```

## 13. Loading & Empty States

### Loading

Gunakan skeleton pada:

- dashboard cards;
- table;
- activity feed;
- detail kegiatan.

### Empty

Contoh:

```text
Belum ada kegiatan
Belum ada kegiatan yang dipublikasikan saat ini.
```

Untuk Admin:

```text
Belum ada rencana kegiatan.
[ + Buat Rencana ]
```

## 14. Permission Matrix

| Fitur | Admin | User |
|---|:---:|:---:|
| Login/Logout | ✓ | ✓ |
| Register | - | ✓ |
| CRUD Rencana | ✓ | - |
| CRUD Detail | ✓ | - |
| Kelola Pengajuan | ✓ | R* |
| Publikasi | ✓ | - |
| CRUD Kegiatan | ✓ | R |
| CRUD Agenda | ✓ | R |
| Daftar Peserta | ✓ | C |
| Monitoring Peserta | ✓ | - |
| CRUD Laporan | ✓ | R* |
| Kelola Notifikasi | ✓ | - |
| Lihat Notifikasi | ✓ | ✓ |
| Kelola Profil | sesuai role | ✓ |
| Monitoring | ✓ | - |

`R*` berarti akses baca hanya bila data memang disediakan untuk User sesuai proposal.

## 15. Data & UI Relationship

UI harus mengikuti domain model proposal:

```text
RencanaKegiatan
    │
    ├── DetailKegiatan
    │
    └── PengajuanKegiatan
             │
             └── Kegiatan
                    ├── Agenda
                    ├── Peserta
                    │      └── User
                    └── LaporanKegiatan

User
 └── Notifikasi
```

## 16. Architecture Direction

Proposal menyebut framework web modern seperti Laravel dan database MySQL. Karena proposal tidak menetapkan arsitektur implementasi secara detail, desain teknis berikut diposisikan sebagai direction, bukan requirement wajib:

```text
Browser
   │
   ▼
Web UI
   │
   ▼
Application Layer
   ├── Authentication / Authorization
   ├── Activity Management
   ├── Submission Workflow
   ├── Notification
   ├── Participant
   └── Reporting
   │
   ▼
Database
   │
   ├── Users/Admin
   ├── Activities
   ├── Submissions
   ├── Participants
   ├── Agenda
   ├── Reports
   └── Notifications

External Integration
   └── Google Calendar
```

## 17. Database Design Direction

Model yang harus tercermin pada persistence layer:

```text
users
admins
rencana_kegiatan
detail_kegiatan
pengajuan_kegiatan
kegiatan
agenda
peserta
laporan_kegiatan
notifikasi
```

> Nama tabel di atas merupakan translasi konvensi dari nama model pada proposal. Struktur kolom, PK/FK, nullable rules, indexes, dan cardinality final harus mengikuti ERD/keputusan implementasi.

## 18. Design Constraints

1. Bahasa UI: Bahasa Indonesia.
2. Web responsive.
3. Admin dan User memiliki akses berbeda.
4. Status proses harus terlihat.
5. Notifikasi merupakan bagian inti workflow.
6. File laporan harus dapat dikaitkan dengan kegiatan.
7. Sistem harus mendukung pencarian/filter.
8. Audit trail dan backup merupakan kebutuhan operasional.
9. HTTPS dan RBAC merupakan kebutuhan keamanan.
10. Integrasi Google Calendar tercantum dalam proposal.

## 19. Design QA Checklist

### Layout

- [ ] Responsive desktop/tablet/mobile.
- [ ] Sidebar/navbar konsisten.
- [ ] Spacing konsisten.
- [ ] Tidak ada horizontal overflow yang tidak disengaja.

### Form

- [ ] Semua field memiliki label.
- [ ] Validasi inline.
- [ ] Error state.
- [ ] Loading state.
- [ ] Success feedback.
- [ ] Confirmation untuk destructive action.

### Workflow

- [ ] Status pengajuan terlihat.
- [ ] Revisi dapat dilakukan.
- [ ] Pengajuan kembali tersedia.
- [ ] Publikasi hanya untuk data yang memenuhi state.
- [ ] Agenda H-1 dapat dibuat.
- [ ] Notifikasi dapat dilihat.
- [ ] Peserta dapat dimonitor.
- [ ] Laporan dapat dibuat.

### Security UX

- [ ] Menu mengikuti role.
- [ ] Aksi unauthorized tidak ditampilkan atau ditolak.
- [ ] Session timeout ditangani dengan jelas.
- [ ] File upload memiliki feedback validasi.

## 20. Traceability

Design ini memetakan kebutuhan proposal ke UI/UX sebagai berikut:

| Proposal | Design |
|---|---|
| FR-A1 / FR-U1 | Login/Register/Auth screens |
| FR-A2–A3 | Rencana & Detail screens |
| FR-A4 | Pengajuan & Status screens |
| FR-A5–A6 | Kegiatan & Agenda management |
| FR-A7 | Laporan |
| FR-A8 | Peserta/Monitoring |
| FR-A9, FR-G1 | Notification center + notification feedback |
| FR-A10 | Admin dashboard/monitoring |
| FR-U2–U5 | Kegiatan, detail, dan pendaftaran |
| FR-U6–U8 | Laporan, notifikasi, profil |
| FR-G2 | Role-based navigation |
| FR-G3 | Form validation |
| FR-G4 | Status badge/timeline |
| FR-G5 | File upload |
| FR-G6 | Search/filter |
| FR-G7 | History |
| FR-G8 | Authentication/security UX |

## 21. Source Boundary

Dokumen ini tidak menambahkan fitur bisnis yang tidak tercantum pada System Proposal. Beberapa keputusan visual dan arsitektur UI merupakan **design direction** untuk menerjemahkan requirement menjadi antarmuka dan harus divalidasi sebelum implementasi final.

Sumber utama: *Draft Rapi-System Proposal.docx*, versi 2.0, September 2026.
