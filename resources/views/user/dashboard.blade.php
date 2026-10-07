<x-layouts.user title="Dashboard Pengguna — SIPENA IDX">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-[#680A10] to-[#85131B] rounded-2xl p-6 sm:p-8 text-white shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-rose-100 text-xs font-semibold uppercase tracking-wider">Portal Edukasi Publik</span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Selamat Datang, {{ $user?->name ?? 'Peserta' }}!</h1>
                <p class="text-xs sm:text-sm text-rose-100/90 max-w-xl leading-relaxed">
                    Tingkatkan literasi investasi Anda melalui program Sekolah Pasar Modal (SPM), workshop analisis, dan sosialisasi resmi BEI Bengkulu.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('user.kegiatan.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#680A10] font-bold text-xs sm:text-sm rounded-xl hover:bg-rose-50 transition shadow-sm">
                    <span>Jelajahi Kegiatan &rarr;</span>
                </a>
            </div>
        </div>

        {{-- 3 User KPI Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kegiatan Diikuti</span>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5 font-mono">{{ $kegiatanDiikuti }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kehadiran Terverifikasi</span>
                    <div class="text-2xl font-bold text-emerald-700 mt-0.5 font-mono">{{ $kegiatanHadir }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-[#680A10] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Agenda Mendatang</span>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5 font-mono">{{ $upcomingEvents->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Upcoming Events --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Agenda Edukasi Pasar Modal Terdekat</h2>
                    <p class="text-xs text-slate-500">Pilih program edukasi dan daftarkan diri Anda sebelum kuota penuh.</p>
                </div>
                <a href="{{ route('user.kegiatan.index') }}" class="text-xs font-semibold text-[#680A10] hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse ($upcomingEvents as $item)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition group">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $item->jenis_kegiatan }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-[#680A10] transition leading-snug line-clamp-2">
                                {{ $item->nama_kegiatan }}
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2">
                                {{ $item->deskripsi }}
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-[11px] text-slate-400 truncate max-w-[140px]">{{ $item->lokasi }}</span>
                            <a href="{{ route('user.kegiatan.show', $item) }}" class="font-bold text-[#680A10] hover:underline">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-slate-400 text-xs bg-white rounded-xl border border-slate-200">
                        Belum ada kegiatan edukasi mendatang saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.user>
