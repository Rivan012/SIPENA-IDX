<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPENA — Portal Admin IDX' }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex antialiased font-sans text-slate-800 bg-slate-50">
    {{-- Left Sidebar: Deep Maroon (#680A10) --}}
    <aside class="w-64 bg-[#680A10] text-white flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-[#54080D] h-screen sticky top-0">
        <div class="overflow-y-auto">
            {{-- Brand Header --}}
            <div class="px-5 py-4 border-b border-[#54080D] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/idx-logo-icon.png') }}" alt="IDX" class="w-7 h-7 rounded-md object-contain shadow-xs">
                    <div>
                        <div class="flex items-center gap-1.5 leading-none">
                            <span class="text-base font-bold tracking-tight text-white">SIPENA</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#85131B] text-white border border-[#9A1923] uppercase">IDX</span>
                        </div>
                        <p class="text-[10px] text-rose-200/70 font-medium mt-1 leading-none">Perencanaan &amp; Edukasi</p>
                    </div>
                </div>
                <button type="button" class="text-rose-200/60 hover:text-white p-1 transition" title="Tutup / Perkecil Sidebar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            {{-- Main Navigation --}}
            <div class="p-3">
                <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold tracking-wider text-rose-200/50 uppercase">
                    NAVIGASI UTAMA
                </div>
                <nav class="space-y-1">
                    {{-- Dashboard (Active) --}}
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ (request()->routeIs('admin.dashboard') || request()->is('preview-dashboard')) ? 'bg-white/12 text-white shadow-xs' : 'text-rose-100/70 hover:bg-[#780D14] hover:text-white' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Kegiatan --}}
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kegiatan</span>
                    </a>

                    {{-- Kalender --}}
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kalender</span>
                    </a>

                    {{-- Anggaran --}}
                    <a href="{{ route('admin.rencana-kegiatan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Anggaran</span>
                    </a>

                    {{-- Approval (with Badge 3) --}}
                    <a href="{{ route('admin.pengajuan.index') }}"
                       class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>Approval</span>
                        </div>
                        <span class="w-4.5 h-4.5 rounded-full bg-white text-[#680A10] font-bold text-[11px] flex items-center justify-center">3</span>
                    </a>

                    {{-- Pengguna --}}
                    <a href="{{ route('admin.monitoring') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Pengguna</span>
                    </a>

                    {{-- Rangkuman --}}
                    <a href="{{ route('admin.laporan-kegiatan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Rangkuman</span>
                    </a>

                    {{-- Pengaturan --}}
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-100/70 hover:bg-[#780D14] hover:text-white transition">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- Sidebar Footer: Admin Profile Box --}}
        <div class="p-3 border-t border-[#54080D]">
            <div class="bg-[#54080D] rounded-xl p-2.5 flex items-center justify-between cursor-pointer hover:bg-[#48070B] transition">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white text-[#680A10] font-black text-xs flex items-center justify-center shadow-xs">
                        A
                    </div>
                    <div class="leading-tight text-left">
                        <div class="text-xs font-bold text-white">Admin BEI</div>
                        <div class="text-[10px] text-rose-200/70 font-medium">Pusat &amp; KP</div>
                    </div>
                </div>
                <svg class="w-3.5 h-3.5 text-rose-200/70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </aside>

    {{-- Main Column --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Top Navigation Bar --}}
        <header class="h-16 bg-white border-b border-slate-200 px-6 sm:px-8 flex items-center justify-between gap-4 sticky top-0 z-10 shrink-0">
            {{-- Search Bar --}}
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Cari kegiatan, nomor anggaran, atau PIC..."
                        class="w-full h-10 pl-9 pr-4 bg-slate-100 border-0 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:bg-white transition"
                    >
                </div>
            </div>

            {{-- Right Controls --}}
            <div class="flex items-center gap-4">
                {{-- Notification Button --}}
                <div class="relative">
                    <button type="button" class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-600 ring-2 ring-white"></span>
                    </button>
                </div>

                {{-- User Profile Pill --}}
                <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                    <div class="w-9 h-9 rounded-full bg-[#680A10] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        AD
                    </div>
                    <div class="hidden sm:block leading-tight text-left">
                        <div class="text-xs font-semibold text-slate-800">Administrator</div>
                        <div class="text-[11px] text-slate-500">Admin BEI</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Body --}}
        <main class="flex-1 p-5 sm:p-6 lg:p-7 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</body>
</html>