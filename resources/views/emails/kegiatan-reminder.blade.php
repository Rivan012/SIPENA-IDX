<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Kegiatan - SIPENA IDX</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #9F0E0F;
            padding: 28px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #fecdd3;
            font-size: 12px;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .intro-text {
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .event-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #9F0E0F;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .event-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 16px 0;
            line-height: 1.4;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .detail-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        .detail-label {
            width: 100px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
        }
        .detail-value {
            font-weight: 500;
            color: #1e293b;
        }
        .note-card {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .note-title {
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .note-body {
            font-size: 13px;
            color: #78350f;
            margin: 0;
            line-height: 1.5;
        }
        .cta-container {
            text-align: center;
            margin: 28px 0 16px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #9F0E0F;
            color: #ffffff !td;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SIPENA — IDX BENGKULU</h1>
            <p>Sistem Informasi Perencanaan & Pelaksanaan Edukasi Pasar Modal</p>
        </div>

        <div class="content">
            <div class="greeting">Halo, {{ $recipientName ?? 'Bapak/Ibu/Rekan Mitra Pengusul' }}!</div>
            <p class="intro-text">
                Ini adalah pengingat resmi untuk agenda kegiatan edukasi pasar modal yang Anda usulkan / selenggarakan bersama Kantor Perwakilan BEI Bengkulu. Harap perhatikan detail pelaksanaan berikut:
            </p>

            <div class="event-card">
                <h2 class="event-title">{{ $kegiatan->nama_kegiatan }}</h2>
                <table class="detail-table">
                    <tr>
                        <td class="detail-label">Kategori</td>
                        <td class="detail-value">{{ $kegiatan->jenis_kegiatan }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Tanggal</td>
                        <td class="detail-value">
                            {{ $kegiatan->tanggal ? \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Waktu</td>
                        <td class="detail-value">
                            {{ $kegiatan->waktu_mulai ? substr($kegiatan->waktu_mulai, 0, 5) : '09:00' }} - {{ $kegiatan->waktu_selesai ? substr($kegiatan->waktu_selesai, 0, 5) : '15:30' }} WIB
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Lokasi</td>
                        <td class="detail-value">{{ $kegiatan->lokasi ?: 'Gedung Bursa Efek Indonesia / Lokasi Mitra' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Penyelenggara</td>
                        <td class="detail-value">{{ $kegiatan->mitra ?: 'Kantor Perwakilan BEI Bengkulu' }}</td>
                    </tr>
                </table>
            </div>

            @if(!empty($customNote))
                <div class="note-card">
                    <div class="note-title">Catatan Tambahan Panitia</div>
                    <p class="note-body">{{ $customNote }}</p>
                </div>
            @endif

            <div class="cta-container">
                <a href="{{ config('app.url') }}" class="cta-button" style="color: #ffffff;">Buka Portal SIPENA</a>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 4px 0;"><strong>Kantor Perwakilan Bursa Efek Indonesia Bengkulu</strong></p>
            <p style="margin: 0;">Email ini dikirim otomatis oleh sistem SIPENA IDX. Jangan membalas email ini secara langsung.</p>
        </div>
    </div>
</body>
</html>
