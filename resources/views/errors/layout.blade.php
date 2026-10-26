<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — SIPENA IDX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #0c0f17;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        .glow-1 {
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(133, 19, 27, 0.3) 0%, rgba(104, 10, 16, 0.08) 55%, transparent 75%);
            top: -120px;
            left: -120px;
            pointer-events: none;
        }
        .glow-2 {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30, 41, 59, 0.6) 0%, transparent 70%);
            bottom: -120px;
            right: -120px;
            pointer-events: none;
        }
        .card {
            position: relative;
            z-index: 10;
            background: rgba(18, 24, 38, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
            max-width: 520px;
            width: 100%;
            padding: 44px 36px;
            text-align: center;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 18px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 24px;
        }
        .brand img {
            height: 26px;
            width: 26px;
            object-fit: contain;
        }
        .brand-text {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #e2e8f0;
        }
        .brand-separator {
            color: rgba(255, 255, 255, 0.2);
        }
        .brand-sub {
            color: #f87171;
            font-weight: 600;
        }
        .code-wrapper {
            position: relative;
            margin-bottom: 14px;
        }
        .code-watermark {
            font-size: 116px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.06em;
            color: rgba(255, 255, 255, 0.035);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            user-select: none;
            pointer-events: none;
        }
        .code-badge {
            display: inline-block;
            font-size: 52px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #ffffff 40%, #fca5a5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
            line-height: 1.35;
        }
        .message {
            font-size: 13px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 30px;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
        }
        @media (min-width: 480px) {
            .actions {
                flex-direction: row;
            }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            outline: none;
            font-family: inherit;
        }
        .btn-primary {
            background: #85131B;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(133, 19, 27, 0.45);
        }
        .btn-primary:hover {
            background: #680A10;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(133, 19, 27, 0.55);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="glow-1"></div>
    <div class="glow-2"></div>

    <div class="card">
        <div class="brand">
            <img src="/images/logo-emblem.png" alt="Logo IDX">
            <span class="brand-text">SIPENA</span>
            <span class="brand-separator">|</span>
            <span class="brand-text brand-sub">KP BEI Bengkulu</span>
        </div>

        <div class="code-wrapper">
            <div class="code-watermark">@yield('code')</div>
            <div class="code-badge">@yield('code')</div>
        </div>

        <h1 class="title">@yield('title')</h1>
        <p class="message">@yield('message')</p>

        <div class="actions">
            @yield('actions')
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Halaman Utama
            </a>
            <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali
            </button>
        </div>

        <div class="footer">
            SIPENA &bull; Sistem Informasi Perencanaan &amp; Edukasi Pasar Modal<br>
            Kantor Perwakilan PT Bursa Efek Indonesia Bengkulu
        </div>
    </div>
</body>
</html>
