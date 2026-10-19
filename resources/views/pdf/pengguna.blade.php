<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ ($role ?? 'users') === 'admins' ? 'Laporan Data Administrator' : 'Laporan Data Mitra & Galeri Investasi' }} - SIPENA IDX Bengkulu</title>
    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
            size: a4 landscape;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #85131B;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-logo {
            width: 70px;
            vertical-align: middle;
            text-align: center;
        }
        .kop-logo img {
            height: 52px;
            width: auto;
        }
        .kop-text {
            vertical-align: middle;
            padding-left: 12px;
        }
        .kop-title {
            font-size: 13pt;
            font-weight: bold;
            color: #85131B;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-subtitle {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .kop-desc {
            font-size: 7.5pt;
            color: #64748b;
            margin: 0;
        }
        .doc-header {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 0.3px;
        }
        .doc-meta {
            font-size: 7.5pt;
            color: #64748b;
        }
        .summary-boxes {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .summary-cell {
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            text-align: center;
        }
        .summary-num {
            font-size: 11pt;
            font-weight: bold;
            color: #85131B;
        }
        .summary-label {
            font-size: 6.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .data-table th {
            background-color: #85131B;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7pt;
            padding: 6px 5px;
            border: 1px solid #731017;
            text-align: left;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 0.5px solid #86efac; }
        .badge-info { background-color: #dbeafe; color: #1e40af; border: 0.5px solid #93c5fd; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border: 0.5px solid #fde68a; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; border: 0.5px solid #fca5a5; }
        .badge-neutral { background-color: #f1f5f9; color: #475569; border: 0.5px solid #cbd5e1; }
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .signature-col {
            width: 35%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT RESMI -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ public_path('images/logo-emblem.png') }}" alt="Logo BEI">
            </td>
            <td class="kop-text">
                <div class="kop-title">PT BURSA EFEK INDONESIA</div>
                <div class="kop-subtitle">KANTOR PERWAKILAN BENGKULU</div>
                <div class="kop-desc">
                    Sistem Informasi Perencanaan Kegiatan Edukasi dan Sosialisasi (SIPENA IDX)<br>
                    Jl. Pangeran Digandhim No. 12, Padang Harapan, Kota Bengkulu 38225 | Telp: (0736) 7321234 | Email: bengkulu@idx.co.id
                </div>
            </td>
        </tr>
    </table>

    <!-- JUDUL LAPORAN -->
    <div class="doc-header">
        <div class="doc-title">
            {{ ($role ?? 'users') === 'admins' ? 'Laporan Data Administrator Sistem' : 'Laporan Data Mitra & Galeri Investasi Terdaftar' }}
        </div>
        <div class="doc-meta">
            Dicetak pada: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong> &bull;
            Oleh: <strong>{{ $printedBy ?? 'Administrator KP BEI Bengkulu' }}</strong>
            @if(!empty($filters['search']))
                <br>
                <span style="color: #85131B; font-weight: bold;">Filter Pencarian:</span> "{{ $filters['search'] }}"
            @else
                <br><span style="color: #64748b;">(Menampilkan Seluruh Data {{ ($role ?? 'users') === 'admins' ? 'Administrator' : 'Mitra Terdaftar' }})</span>
            @endif
        </div>
    </div>

    @if(($role ?? 'users') === 'admins')
        <!-- STATISTIK RINGKAS ADMIN -->
        <table class="summary-boxes">
            <tr>
                <td class="summary-cell" style="width: 50%;">
                    <div class="summary-num">{{ count($admins ?? []) }}</div>
                    <div class="summary-label">Total Administrator Ditampilkan</div>
                </td>
                <td class="summary-cell" style="width: 50%;">
                    <div class="summary-num">{{ $stats['total'] ?? count($admins ?? []) }}</div>
                    <div class="summary-label">Pengguna Berwenang</div>
                </td>
            </tr>
        </table>

        <!-- TABEL DATA ADMINISTRATOR -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28px; text-align: center;">No</th>
                    <th style="width: 55px; text-align: center;">ID</th>
                    <th>Nama Administrator</th>
                    <th style="width: 210px;">Alamat Email</th>
                    <th style="width: 130px;">No. Telepon</th>
                    <th style="width: 170px;">Jabatan</th>
                    <th style="width: 110px;">Tgl Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $index => $adm)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-weight: bold; color: #85131B;">#{{ $adm->id }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $adm->name }}</strong>
                        </td>
                        <td>{{ $adm->email }}</td>
                        <td>{{ $adm->no_telepon ?: '-' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $adm->jabatan ?: 'Administrator' }}</span>
                        </td>
                        <td>{{ $adm->created_at ? $adm->created_at->translatedFormat('d M Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 16px; color: #94a3b8;">
                            Tidak ada data administrator yang sesuai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <!-- STATISTIK RINGKAS MITRA -->
        <table class="summary-boxes">
            <tr>
                <td class="summary-cell">
                    <div class="summary-num">{{ count($users ?? []) }}</div>
                    <div class="summary-label">Mitra Ditampilkan</div>
                </td>
                <td class="summary-cell">
                    <div class="summary-num">{{ $stats['terverifikasi'] ?? 0 }}</div>
                    <div class="summary-label">Email Terverifikasi</div>
                </td>
                <td class="summary-cell">
                    <div class="summary-num">{{ $stats['belum_verifikasi'] ?? 0 }}</div>
                    <div class="summary-label">Belum Terverifikasi</div>
                </td>
                <td class="summary-cell">
                    <div class="summary-num">{{ $stats['total_partisipasi'] ?? 0 }}</div>
                    <div class="summary-label">Total Partisipasi Kegiatan</div>
                </td>
            </tr>
        </table>

        <!-- TABEL DATA MITRA -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 24px; text-align: center;">No</th>
                    <th style="width: 45px; text-align: center;">ID</th>
                    <th>Nama Mitra / Institusi</th>
                    <th style="width: 170px;">Alamat Email</th>
                    <th style="width: 100px;">No. Telepon</th>
                    <th style="width: 75px; text-align: center;">Status Verif</th>
                    <th style="width: 65px; text-align: center;">Partisipasi</th>
                    <th>Kegiatan Terakhir Diikuti</th>
                    <th style="width: 90px;">Tgl Registrasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                    @php
                        $lastKegiatan = $u->kegiatan ? $u->kegiatan->sortByDesc('pivot.created_at')->first() : null;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-weight: bold; color: #85131B;">#{{ $u->id }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $u->name }}</strong>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->no_telepon ?: '-' }}</td>
                        <td style="text-align: center;">
                            @if($u->email_verified_at)
                                <span class="badge badge-success">Terverifikasi</span>
                            @else
                                <span class="badge badge-warning">Belum Aktif</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $u->kegiatan ? $u->kegiatan->count() : 0 }}
                        </td>
                        <td style="font-size: 7pt; color: #475569;">
                            {{ $lastKegiatan ? $lastKegiatan->nama_kegiatan : '-' }}
                        </td>
                        <td>{{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 16px; color: #94a3b8;">
                            Tidak ada data mitra yang sesuai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- LEMBAR PENGESAHAN -->
    <table class="signature-table">
        <tr>
            <td style="width: 65%;"></td>
            <td class="signature-col">
                Kota Bengkulu, {{ now()->translatedFormat('d F Y') }}<br>
                Mengetahui,<br>
                <strong>Kantor Perwakilan BEI Bengkulu</strong>
                <br><br><br><br>
                <u><strong>{{ $printedBy ?? 'Administrator KP BEI Bengkulu' }}</strong></u><br>
                <span style="color: #64748b; font-size: 7pt;">Pengelola Relasi Kemitraan</span>
            </td>
        </tr>
    </table>

    <!-- SCRIPT NOMOR HALAMAN DOMPDF -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $pdf->page_text(745, 565, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 7, array(0.4, 0.4, 0.4));
            $pdf->page_text(42, 565, "SIPENA IDX Bengkulu &bull; Dokumen Data Pengguna Sistem", $font, 7, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>