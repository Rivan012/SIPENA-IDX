<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Log Riwayat Aktivitas - SIPENA IDX Bengkulu</title>
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
        <div class="doc-title">Laporan Log Audit &amp; Riwayat Aktivitas Sistem</div>
        <div class="doc-meta">
            Dicetak pada: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong> &bull;
            Oleh: <strong>{{ $printedBy ?? 'Administrator KP BEI Bengkulu' }}</strong>
            @if(!empty($filters['role']) || !empty($filters['search']))
                &bull; Filter:
                @if(!empty($filters['role'])) [Peran: {{ ucfirst($filters['role']) }}] @endif
                @if(!empty($filters['search'])) [Cari: "{{ $filters['search'] }}"] @endif
            @endif
        </div>
    </div>

    <!-- STATISTIK RINGKAS -->
    <table class="summary-boxes">
        <tr>
            <td class="summary-cell">
                <div class="summary-num">{{ count($items) }}</div>
                <div class="summary-label">Total Log Diekspor</div>
            </td>
            <td class="summary-cell">
                <div class="summary-num">{{ $stats['admin'] ?? 0 }}</div>
                <div class="summary-label">Tindakan Admin</div>
            </td>
            <td class="summary-cell">
                <div class="summary-num">{{ $stats['mitra'] ?? 0 }}</div>
                <div class="summary-label">Aktivitas Mitra</div>
            </td>
            <td class="summary-cell">
                <div class="summary-num">{{ $stats['today'] ?? 0 }}</div>
                <div class="summary-label">Aktivitas Hari Ini</div>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px; text-align: center;">No</th>
                <th style="width: 105px;">Waktu (WIB)</th>
                <th style="width: 120px;">Aktor / Pelaku</th>
                <th style="width: 70px; text-align: center;">Peran</th>
                <th style="width: 100px;">Aksi</th>
                <th>Deskripsi Aktivitas</th>
                <th style="width: 130px;">Terkait Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $act)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        {{ $act->created_at ? $act->created_at->translatedFormat('d M Y') : '-' }}<br>
                        <span style="color: #64748b; font-size: 6.8pt;">{{ $act->created_at ? $act->created_at->format('H:i:s') : '' }} WIB</span>
                    </td>
                    <td>
                        <strong style="color: #0f172a;">{{ $act->aktor_nama }}</strong>
                    </td>
                    <td style="text-align: center;">
                        @if($act->aktor_role === 'admin')
                            <span class="badge badge-danger">Admin</span>
                        @elseif($act->aktor_role === 'mitra')
                            <span class="badge badge-info">Mitra</span>
                        @else
                            <span class="badge badge-neutral">Sistem</span>
                        @endif
                    </td>
                    <td style="font-weight: bold; color: #85131B;">
                        {{ $act->aksi }}
                    </td>
                    <td>
                        {{ $act->deskripsi }}
                    </td>
                    <td style="font-size: 7pt; color: #475569;">
                        {{ $act->kegiatan ? $act->kegiatan->nama_kegiatan : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 16px; color: #94a3b8;">
                        Tidak ada log aktivitas yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
                <span style="color: #64748b; font-size: 7pt;">Pengawas Sistem &amp; Log Audit</span>
            </td>
        </tr>
    </table>

    <!-- SCRIPT NOMOR HALAMAN DOMPDF -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $pdf->page_text(745, 565, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 7, array(0.4, 0.4, 0.4));
            $pdf->page_text(42, 565, "SIPENA IDX Bengkulu &bull; Dokumen Audit Log & Riwayat Aktivitas", $font, 7, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>
