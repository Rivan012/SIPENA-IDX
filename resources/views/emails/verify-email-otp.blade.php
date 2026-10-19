<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi - SIPENA IDX</title>
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
        }
        .intro-text {
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .otp-box {
            display: inline-block;
            background-color: #fff1f2;
            border: 2px dashed #9F0E0F;
            border-radius: 14px;
            padding: 16px 36px;
            margin: 8px 0 24px 0;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #9F0E0F;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            text-align: left;
            margin-bottom: 16px;
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
            <div class="greeting">Halo, {{ $userName ?? 'Mitra Baru' }}!</div>
            <p class="intro-text">
                Terima kasih telah mendaftar di SIPENA IDX Bengkulu. Gunakan kode verifikasi (OTP) berikut untuk memverifikasi akun Anda agar dapat mengakses dashboard:
            </p>

            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>

            <div class="info-box">
                <strong>Perhatian:</strong>
                <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                    <li>Kode OTP ini hanya berlaku selama <strong>15 menit</strong>.</li>
                    <li>Jangan berikan kode ini kepada pihak manapun, termasuk pihak yang mengatasnamakan BEI.</li>
                    <li>Setelah terverifikasi, Anda dapat langsung mengusulkan dan mengikuti kegiatan edukasi pasar modal.</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 4px 0;"><strong>Kantor Perwakilan Bursa Efek Indonesia Bengkulu</strong></p>
            <p style="margin: 0;">Email otomatis sistem SIPENA IDX. Jangan membalas email ini secara langsung.</p>
        </div>
    </div>
</body>
</html>
