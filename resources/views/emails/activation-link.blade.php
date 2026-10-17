<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun - SIPENA IDX</title>
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
            max-width: 560px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #9F0E0F;
            padding: 24px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #fecdd3;
            font-size: 11px;
        }
        .content {
            padding: 32px;
            text-align: center;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            text-align: left;
        }
        .intro-text {
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
            text-align: left;
        }
        .cta-container {
            text-align: center;
            margin: 28px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #9F0E0F;
            color: #ffffff !important;
            padding: 13px 32px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(159, 14, 15, 0.2);
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
            text-align: left;
            margin-bottom: 20px;
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
        .subtext {
            font-size: 11px;
            color: #94a3b8;
            word-break: break-all;
            margin-top: 16px;
            line-height: 1.4;
            text-align: left;
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
            <div class="greeting">Halo, {{ $userName ?? 'Mitra Baru SIPENA' }}!</div>
            <p class="intro-text">
                Terima kasih telah mendaftar di portal SIPENA Kantor Perwakilan Bursa Efek Indonesia Bengkulu. Untuk menyelesaikan pendaftaran dan mengaktifkan akun Anda, silakan klik tombol aktivasi di bawah ini:
            </p>

            <div class="cta-container">
                <a href="{{ $activationUrl }}" class="cta-button" target="_blank">Aktivasi Akun Sekarang</a>
            </div>

            <div class="info-box">
                <strong>Catatan Keamanan:</strong>
                <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                    <li>Tautan aktivasi akun ini hanya berlaku selama <strong>60 menit</strong>.</li>
                    <li>Setelah aktivasi berhasil, Anda dapat langsung masuk ke dashboard dan mengusulkan kegiatan.</li>
                    <li>Jika Anda tidak pernah mendaftar di SIPENA IDX, abaikan email ini.</li>
                </ul>
            </div>

            <div class="subtext">
                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke peramban web Anda:<br>
                <a href="{{ $activationUrl }}" style="color: #9F0E0F;">{{ $activationUrl }}</a>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 4px 0;"><strong>Kantor Perwakilan Bursa Efek Indonesia Bengkulu</strong></p>
            <p style="margin: 0;">Email otomatis sistem SIPENA IDX. Jangan membalas email ini secara langsung.</p>
        </div>
    </div>
</body>
</html>
