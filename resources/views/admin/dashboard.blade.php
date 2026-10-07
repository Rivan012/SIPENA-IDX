<x-layouts.admin title="Dashboard — SIPENA IDX">
    <div class="space-y-5">
        {{-- Hero Maroon Banner (#6B0E14) --}}
        <div class="bg-[#6B0E14] text-white rounded-xl p-5 sm:p-6 shadow-xs relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                {{-- Left Text Column --}}
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-2.5 py-1 rounded bg-[#54080D] text-white font-bold text-[10px] tracking-wider uppercase">
                            TAHUN ANGGARAN 2026
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-rose-100/80 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Data diperbarui: 27 Feb 2026, 08:30 WIB</span>
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mt-2.5">
                        Selamat Datang di Portal SIPENA
                    </h1>
                    <p class="text-xs sm:text-sm text-rose-100/90 mt-1.5 leading-relaxed">
                        Sistem Informasi Perencanaan Kegiatan Edukasi dan Sosialisasi &mdash; Bursa Efek Indonesia Periode 2025.<br class="hidden sm:inline">
                        Terintegrasi dengan 34 Kantor Perwakilan &amp; 900+ Galeri Investasi.
                    </p>
                </div>

                {{-- Right Vertically Stacked Action Buttons --}}
                <div class="flex flex-col sm:flex-row lg:flex-col gap-2 shrink-0 lg:w-48">
                    <a href="{{ route('admin.kegiatan.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#1D4ED8] hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Rencana</span>
                    </a>
                    <a href="{{ route('admin.pengajuan.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium text-xs rounded-lg border border-white/15 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                        </svg>
                        <span>Ajukan Anggaran</span>
                    </a>
                    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium text-xs rounded-lg border border-white/15 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Ekspor Laporan</span>
                    </a>
                </div>
            </div>

            {{-- Ticker Strip (#4A070C) --}}
            <div class="mt-4 bg-[#4A070C] rounded-lg px-4 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border border-white/5">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="px-2 py-0.5 rounded bg-[#B91C1C] text-white font-bold text-[10px] tracking-wide shrink-0">
                        PENGINGAT H-1
                    </span>
                    <span class="text-rose-100 font-medium">
                        Sekolah Pasar Modal (SPM) Level 1 di Galeri Investasi UNIB besok pukul 09:00 WIB (245 Peserta Terdaftar)
                    </span>
                </div>
                <a href="{{ route('admin.kegiatan.index') }}" class="text-rose-200 hover:text-white font-medium inline-flex items-center gap-1 shrink-0 transition">
                    <span>Lihat Brief Acara &amp; Kelengkapan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- 6 KPI Metric Cards Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
            {{-- 1. TOTAL KEGIATAN --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL KEGIATAN</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">148</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Rencana TA 2025</p>
                </div>
                <div class="mt-3 flex items-center gap-1.5 pt-2 border-t border-slate-100 text-[10px]">
                    <span class="font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">+12% YoY</span>
                    <span class="text-slate-500">85% Kuota</span>
                </div>
            </div>

            {{-- 2. MENDATANG --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">MENDATANG</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">24</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Terjadwal Maret-April</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100">
                    <span class="inline-block text-[10px] font-medium text-blue-700 bg-sky-50 px-2 py-0.5 rounded-full">
                        &bull; 8 Kegiatan H-7
                    </span>
                </div>
            </div>

            {{-- 3. BERLANGSUNG --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">BERLANGSUNG</span>
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.828a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M12 12h.01" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2 flex items-center">
                        <span>3</span>
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-600 animate-pulse ml-2"></span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Live streaming &amp; On-site</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100">
                    <span class="text-[10px] text-slate-600 font-medium truncate block">
                        KP Jabar &amp; Dosen IDX IT
                    </span>
                </div>
            </div>

            {{-- 4. SELESAI --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">SELESAI</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">116</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kegiatan rampung</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100">
                    <span class="inline-block text-[10px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        &#10003; LPJ Terdokumentasi
                    </span>
                </div>
            </div>

            {{-- 5. REALISASI --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">REALISASI</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-xl font-bold text-slate-900 mt-2">Rp 1.425 M</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Dari pagu Rp 1.817 M</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100">
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-[#1D4ED8] h-1.5 rounded-full" style="width: 78.4%"></div>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-1 flex justify-between">
                        <span>78.4%</span>
                        <span>Sisa 21.6%</span>
                    </div>
                </div>
            </div>

            {{-- 6. PERLU APPROVAL --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PERLU APPROVAL</span>
                        <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-red-600 mt-2">3</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Proposal kegiatan &amp; biaya</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                    <span class="text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded font-medium">Review Kadiv</span>
                    <a href="{{ route('admin.pengajuan.index') }}" class="font-bold text-[#7A0C14] hover:underline">Lihat &rarr;</a>
                </div>
            </div>
        </div>

        {{-- Chart Card: Tren Kegiatan Edukasi & Sosialisasi per Bulan --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Grafik Tren Kegiatan Edukasi &amp; Sosialisasi per Bulan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Komparasi Target Rencana Kerja Anggaran Perusahaan (RKAP) vs Realisasi TA 2025</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#BFDBFE]"></span>
                        <span class="text-slate-600">Target RKAP</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#1D4ED8]"></span>
                        <span class="text-slate-600 font-medium">Realisasi</span>
                    </div>
                </div>
            </div>

            {{-- Monthly Bar Chart Container: Q1 has dual bars, Q2-Q4 Target only --}}
            <div class="pt-6 pb-2">
                <div class="h-56 w-full flex items-end justify-between gap-2 sm:gap-4 px-2">
                    @php
                        $months = [
                            ['label' => 'Jan', 'target' => 12, 'realisasi' => 11, 'has_real' => true],
                            ['label' => 'Feb', 'target' => 14, 'realisasi' => 15, 'has_real' => true],
                            ['label' => 'Mar', 'target' => 16, 'realisasi' => 12, 'has_real' => true],
                            ['label' => 'Apr', 'target' => 14, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Mei', 'target' => 15, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Jun', 'target' => 18, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Jul', 'target' => 14, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Agu', 'target' => 16, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Sep', 'target' => 20, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Okt', 'target' => 18, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Nov', 'target' => 16, 'realisasi' => 0,  'has_real' => false],
                            ['label' => 'Des', 'target' => 12, 'realisasi' => 0,  'has_real' => false],
                        ];
                        $maxVal = 22;
                    @endphp

                    @foreach ($months as $m)
                        @php
                            $targetHeight = round(($m['target'] / $maxVal) * 100);
                            $realHeight = $m['has_real'] ? round(($m['realisasi'] / $maxVal) * 100) : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group">
                            {{-- Bars Group --}}
                            <div class="w-full flex items-end justify-center gap-1.5 h-44 relative">
                                {{-- Target Bar (Light Blue) --}}
                                <div
                                    class="w-3 sm:w-4 bg-[#BFDBFE] rounded-t-xs transition-all duration-300 group-hover:bg-blue-300"
                                    style="height: {{ $targetHeight }}%"
                                    title="Target: {{ $m['target'] }}"
                                ></div>
                                {{-- Realisasi Bar (Deep Blue - only Q1) --}}
                                @if ($m['has_real'])
                                    <div
                                        class="w-3 sm:w-4 bg-[#1D4ED8] rounded-t-xs transition-all duration-300 group-hover:brightness-110"
                                        style="height: {{ $realHeight }}%"
                                        title="Realisasi: {{ $m['realisasi'] }}"
                                    ></div>
                                @endif
                            </div>
                            {{-- Label Month --}}
                            <div class="mt-2 text-center">
                                <span class="text-[11px] font-semibold text-slate-500">
                                    {{ $m['label'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Segment Audiens: 4 Columns matching reference --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 mt-2 border-t border-slate-100 text-xs">
                {{-- Segment 1 --}}
                <div class="bg-slate-50/60 rounded-lg p-3 border border-slate-100">
                    <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-xs">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Mahasiswa &amp; Pelajar</span>
                    </div>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-xl font-bold text-slate-900">54.2%</span>
                        <span class="text-[11px] text-slate-500">28.410 Peserta</span>
                    </div>
                </div>

                {{-- Segment 2 --}}
                <div class="bg-slate-50/60 rounded-lg p-3 border border-slate-100">
                    <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span>ASN &amp; Birokrasi</span>
                    </div>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-xl font-bold text-slate-900">18.6%</span>
                        <span class="text-[11px] text-slate-500">9.750 Peserta</span>
                    </div>
                </div>

                {{-- Segment 3 --}}
                <div class="bg-slate-50/60 rounded-lg p-3 border border-slate-100">
                    <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-xs">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <span>Komunitas &amp; Publik</span>
                    </div>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-xl font-bold text-slate-900">15.1%</span>
                        <span class="text-[11px] text-slate-500">7.912 Peserta</span>
                    </div>
                </div>

                {{-- Segment 4 --}}
                <div class="bg-slate-50/60 rounded-lg p-3 border border-slate-100">
                    <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-700"></span>
                        <span>Profesional / Korporat</span>
                    </div>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-xl font-bold text-slate-900">12.1%</span>
                        <span class="text-[11px] text-slate-500">6.340 Peserta</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lower Grid: 2 Columns (65% / 35%) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Left Column (65% -> col-span-8): Table Kegiatan Terbaru & Monitoring --}}
            <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Kegiatan Terbaru &amp; Monitoring</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Update real-time entitas kantor perwakilan &amp; galeri investasi</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.kegiatan.index') }}" class="text-xs font-semibold text-[#7A0C14] hover:underline">
                                Lihat Semua (148) &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto mt-2">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-2">KEGIATAN &amp; WAKTU</th>
                                    <th class="py-3 px-2">LOKASI / PLATFORM</th>
                                    <th class="py-3 px-2">ANGGARAN</th>
                                    <th class="py-3 px-2">STATUS</th>
                                    <th class="py-3 px-2 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                {{-- Row 1 --}}
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-2">
                                        <div class="font-semibold text-slate-800 text-xs">Sekolah Pasar Modal Level 1</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">28 Feb 2025 &bull; 09:00 WIB</div>
                                    </td>
                                    <td class="py-3 px-2 text-slate-600">
                                        GI BEI Universitas Muhammadiyah Bengkulu
                                    </td>
                                    <td class="py-3 px-2 font-medium text-slate-800">
                                        Rp 12.500.000
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#E0F2FE] text-[#0369A1]">
                                            Dipublikasikan
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <button type="button" class="p-1 text-slate-400 hover:text-slate-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Row 2 --}}
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-2">
                                        <div class="font-semibold text-slate-800 text-xs">Workshop Pasar Modal Syariah</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">05 Mar 2025 &bull; 13:00 WIB</div>
                                    </td>
                                    <td class="py-3 px-2 text-slate-600">
                                        Aula FEB Universitas Bengkulu
                                    </td>
                                    <td class="py-3 px-2 font-medium text-slate-800">
                                        Rp 18.200.000
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#DCFCE7] text-[#15803D]">
                                            Disetujui
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <button type="button" class="p-1 text-slate-400 hover:text-slate-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Row 3 --}}
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-2">
                                        <div class="font-semibold text-slate-800 text-xs">Edukasi Publik &amp; Komunitas Saham</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">12 Mar 2025 &bull; 10:00 WIB</div>
                                    </td>
                                    <td class="py-3 px-2 text-slate-600">
                                        Zoom Webinar &amp; Live YouTube
                                    </td>
                                    <td class="py-3 px-2 font-medium text-slate-800">
                                        Rp 6.800.000
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FEF3C7] text-[#B45309]">
                                            Menunggu Review
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <button type="button" class="p-1 text-slate-400 hover:text-slate-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Row 4 --}}
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-2">
                                        <div class="font-semibold text-slate-800 text-xs">Training of Trainers (ToT) GI IDX</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">19 Mar 2025 &bull; 08:30 WIB</div>
                                    </td>
                                    <td class="py-3 px-2 text-slate-600">
                                        Ruang Rapat KP BEI Bengkulu
                                    </td>
                                    <td class="py-3 px-2 font-medium text-slate-800">
                                        Rp 14.500.000
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            Draft Rencana
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <button type="button" class="p-1 text-slate-400 hover:text-slate-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Column (35% -> col-span-4): Agenda Terdekat & Antrean Tiket --}}
            <div class="lg:col-span-4 bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-red-50 text-red-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Agenda Terdekat &amp; Antrean Tiket</h2>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-[#E0F2FE] text-[#0369A1] font-bold text-[10px]">
                        3 Item
                    </span>
                </div>

                {{-- Agenda Cards List --}}
                <div class="mt-4 space-y-3">
                    {{-- Item 1 --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded bg-[#DC2626] text-white font-bold text-[10px] tracking-wide">
                                H-1 REMINDER
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">Besok, 09:00 WIB</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mt-2 leading-snug">
                            Sekolah Pasar Modal (SPM) Level 1 &ndash; GI BEI UNPAD
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Ruang Multimedia Lt. 2 Kampus Dipatiukur. Pemateri Kantor Perwakilan Bengkulu. 245 Mahasiswa terkonfirmasi hadir.
                        </p>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-end text-[11px]">
                            <a href="{{ route('admin.kegiatan.index') }}" class="font-semibold text-[#7A0C14] hover:underline">Lihat Detail Agenda &rarr;</a>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold text-[10px] tracking-wide">
                                H-3
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">02 Mar 2025, 13:30 WIB</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mt-2 leading-snug">
                            Sosialisasi Cerdas Investasi Saham &amp; Reksadana
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Hybrid / Aula Balai Kota Bengkulu. Kolaborasi bersama OJK Bengkulu. 120 Peserta terdaftar.
                        </p>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-end text-[11px]">
                            <a href="{{ route('admin.kegiatan.index') }}" class="font-semibold text-[#7A0C14] hover:underline">Lihat Detail Agenda &rarr;</a>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px] tracking-wide">
                                H-5
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">05 Mar 2025, 08:30 WIB</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mt-2 leading-snug">
                            Klinik Investasi Pasar Modal Syariah
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Galeri Investasi Syariah IAIN Curup. Konsultasi portofolio &amp; pembukaan rekening efek. 85 Peserta terdaftar.
                        </p>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-end text-[11px]">
                            <a href="{{ route('admin.kegiatan.index') }}" class="font-semibold text-[#7A0C14] hover:underline">Lihat Detail Agenda &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
