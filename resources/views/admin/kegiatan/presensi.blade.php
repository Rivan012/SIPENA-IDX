<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Daftar Hadir - {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        @page {
            margin: 10mm 12mm;
        }

        body {
            color: #171717;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8pt;
        }

        .letterhead {
            border: 1px solid #222;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
            width: 100%;
        }

        .letterhead-logo {
            border-right: 1px solid #222;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
            width: 25%;
        }

        .logo {
            display: block;
            height: 47mm;
            margin: auto;
            width: 47mm;
        }

        .letterhead-content {
            padding: 5mm 7mm;
            vertical-align: middle;
        }

        h1 {
            font-size: 12pt;
            margin: 0;
            text-align: center;
        }

        .event-details {
            border-collapse: collapse;
            width: 100%;
        }

        .event-details td {
            height: 10mm;
            padding: 1px 3px;
            vertical-align: bottom;
        }

        .event-details .label {
            font-weight: bold;
            white-space: nowrap;
            width: 28mm;
        }

        .event-details .colon {
            text-align: center;
            width: 5mm;
        }

        .portrait table.roster {
            table-layout: fixed;
        }

        .portrait .number {
            width: 5mm;
        }

        .portrait .name {
            width: 44mm;
        }

        .portrait .email {
            width: 42mm;
        }

        .portrait .phone {
            width: 52mm;
        }

        .portrait .signature {
            width: 20mm;
        }

        .landscape .letterhead {
            margin-bottom: 6px;
        }

        .landscape .letterhead-logo {
            padding: 4px;
        }

        .landscape .logo {
            height: 34mm;
            width: 34mm;
        }

        .landscape .letterhead-content {
            padding: 3mm 5mm;
        }

        .landscape h1 {
            font-size: 13pt;
        }

        .landscape .event-details td {
            height: 8mm;
            padding: 1px 3px;
            font-size: 9pt;
        }

        .landscape .roster th,
        .landscape .roster td {
            padding: 2px 4px;
        }

        .landscape .roster th {
            font-size: 9pt;
        }

        .landscape .roster tbody td {
            height: 15px;
        }

        .landscape .privacy {
            font-size: 7.5pt;
            line-height: 1.2;
            margin-top: 6px;
            padding: 5px 7px;
        }

        .landscape .privacy h2 {
            font-size: 8.5pt;
            margin-bottom: 3px;
        }

        .landscape .privacy p {
            margin-bottom: 2px;
        }

        table.roster {
            border-collapse: collapse;
            width: 100%;
        }

        .roster th,
        .roster td {
            border: 1px solid #333;
            padding: 3px 5px;
        }

        .roster th {
            background: #e8e8e8;
            font-size: 8pt;
            text-align: center;
        }

        .number {
            text-align: center;
            width: 30px;
        }

        .name {
            width: 26%;
        }

        .email {
            width: 26%;
        }

        .phone {
            width: 19%;
        }

        .signature {
            text-align: center;
            width: 90px;
        }

        .roster tbody tr {
            page-break-inside: avoid;
        }

        .roster tbody td {
            height: 16px;
        }

        .privacy {
            border: 1px solid #333;
            font-size: 7pt;
            line-height: 1.25;
            margin-top: 8px;
            padding: 6px 8px;
        }

        .privacy h2 {
            font-size: 8pt;
            margin: 0 0 4px;
        }

        .privacy p {
            margin: 0 0 3px;
        }

        .contact {
            margin: 0 0 3px;
        }

        .consent {
            font-weight: bold;
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body class="{{ $orientation }}">
    <table class="letterhead">
        <tbody>
            <tr>
                <td class="letterhead-logo">
                    <img class="logo" src="{{ public_path('images/logo-emblem.png') }}" alt="Logo IDX">
                </td>
                <td class="letterhead-content">
                    <h1>FORMULIR DAFTAR HADIR PESERTA</h1>
                    <table class="event-details">
                        <tbody>
                            <tr>
                                <td class="label">KP BEI</td>
                                <td class="colon">:</td>
                                <td class="field-value">Bengkulu</td>
                            </tr>
                            <tr>
                                <td class="label">KEGIATAN</td>
                                <td class="colon">:</td>
                                <td class="field-value">{{ $kegiatan->nama_kegiatan }}</td>
                            </tr>
                            <tr>
                                <td class="label">TANGGAL</td>
                                <td class="colon">:</td>
                                <td class="field-value">{{ $kegiatan->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="roster">
        <thead>
            <tr>
                <th class="number">No.</th>
                <th class="name">NAMA</th>
                <th class="email">EMAIL</th>
                <th class="phone">NO. HP / WA</th>
                <th class="signature">TTD</th>
            </tr>
        </thead>
        <tbody>
            @for($row = 1; $row <= ($orientation === 'landscape' ? 15 : 25); $row++)
                <tr>
                    <td class="number">{{ $row }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <section class="privacy">
        <h2>PERLINDUNGAN DATA PRIBADI</h2>
        <p>
            Dalam rangka penyelenggaraan {{ $kegiatan->nama_kegiatan }} yang akan berlangsung pada
            {{ $kegiatan->tanggal?->format('d/m/Y') ?? '-' }}, kami memerlukan persetujuan Anda untuk pemrosesan data pribadi Anda
            (termasuk namun tidak terbatas pada nama, alamat email, nomor telepon, dan data pribadi lainnya yang diisi pada formulir ini)
            untuk tujuan administrasi dan pelaksanaan kegiatan ini.
        </p>
        <p>
            Kami menjamin bahwa data pribadi Anda hanya akan digunakan untuk keperluan acara ini dan tidak akan dibagikan kepada pihak ketiga tanpa izin Anda.
            Setelah tujuan pemrosesan Data Pribadi telah tercapai, data pribadi Anda akan kami hapus atau arsipkan sesuai dengan privacy notice** yang berlaku.
        </p>
        <p>Jika ada pertanyaan lebih lanjut terkait pemrosesan data pribadi ini, Anda dapat menghubungi kami melalui:</p>
        <p class="contact">Email: contactcenter@idx.co.id | Telepon: 150515 (Nasional) | Whatsapp: +6281181150515</p>
        <p class="consent">
            Dengan membubuhkan Paraf pada form ini, Anda telah memberikan persetujuan kepada kami untuk memproses data pribadi Anda terkait pelaksanaan
            {{ $kegiatan->nama_kegiatan }} dan telah memahami hak-hak Anda sesuai ketentuan yang berlaku.
        </p>
    </section>
</body>
</html>
