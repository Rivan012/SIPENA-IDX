<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPENA — IDX' }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-white text-slate-900 antialiased font-sans">
    {{-- Global Top Header --}}
    <header class="w-full bg-white border-b border-slate-200 px-6 sm:px-10 lg:px-14 py-3.5 flex items-center justify-between z-10">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <span class="text-xl font-bold tracking-tight text-[#7A0C14]">SIPENA</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#FEECEC] text-[#7A0C14] border border-[#F8D7DA] tracking-wide uppercase">IDX</span>
                <span class="hidden sm:inline-block text-xs text-slate-500 pl-2.5 border-l border-slate-300 font-normal">Perencanaan & Edukasi</span>
            </a>
        </div>
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#7A0C14] hover:text-[#5E080F] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke halaman publik</span>
            </a>
        </div>
    </header>

    {{-- Main Split View --}}
    <main class="flex-1 flex flex-col lg:flex-row">
        {{-- Left Column: Branding Showcase --}}
        <section class="w-full lg:w-[48%] xl:w-[44%] bg-[#FAF9F6] border-b lg:border-b-0 lg:border-r border-slate-200/80 px-6 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12 flex flex-col justify-between">
            <div class="space-y-6">
                <div>
                    <span class="text-xs font-bold tracking-wider text-[#7A0C14] uppercase">PERENCANAAN &amp; EDUKASI PASAR MODAL</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B192C] mt-2.5 leading-snug">Terhubung untuk edukasi yang lebih baik.</h1>
                    <p class="text-sm text-slate-600 mt-2.5 leading-relaxed">Satu ruang untuk perencanaan dan koordinasi kegiatan edukasi pasar modal BEI Pusat dan Kantor Perwakilan.</p>
                </div>

                {{-- Illustration Box --}}
                <div class="pt-1">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                        <img src="{{ asset('images/auth-illustration.png') }}" alt="Ilustrasi kegiatan edukasi pasar modal" class="w-full h-auto object-cover block">
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Ilustrasi kegiatan edukasi pasar modal.</p>
                </div>

                {{-- Feature Bullet List --}}
                <div class="pt-2 space-y-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#FEECEC] text-[#7A0C14] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-[#0B192C]">Perencanaan yang terarah</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Agenda dan kegiatan dalam satu tempat.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#FEECEC] text-[#7A0C14] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-[#0B192C]">Koordinasi lintas unit</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Mendukung kolaborasi Pusat dan Kantor Perwakilan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Right Column: Form Container --}}
        <section class="w-full lg:w-[52%] xl:w-[56%] bg-white px-6 py-10 sm:px-10 lg:px-14 flex items-center justify-center">
            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>
        </section>
    </main>

    {{-- Global Footer --}}
    <footer class="w-full border-t border-slate-200 bg-white px-6 sm:px-10 lg:px-14 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-500">
        <div>
            <span>SIPENA &middot; IDX / Bursa Efek Indonesia</span>
        </div>
        <div class="flex items-center gap-1.5 text-slate-500">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>Jangan bagikan kata sandi atau tautan pemulihan Anda.</span>
        </div>
    </footer>
</body>
</html>
