<x-layouts.user title="{{ $kegiatan->nama_kegiatan }} — SIPENA IDX">
    <div class="space-y-6 max-w-4xl mx-auto">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div role="status" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-xs">
                <span>{{ session('success') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">&times;</button>
            </div>
        @endif
        @if (session('error'))
            <div role="status" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-xs">
                <span>{{ session('error') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-800 font-bold">&times;</button>
            </div>
        @endif

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-xs text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('user.dashboard') }}" class="hover:text-slate-900">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('user.kegiatan.index') }}" class="hover:text-slate-900">Katalog Kegiatan</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-semibold truncate">{{ $kegiatan->nama_kegiatan }}</span>
        </div>

        {{-- Main Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $kegiatan->jenis_kegiatan }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $kegiatan->status_class }}">
                        {{ $kegiatan->status_label }}
                    </span>
                </div>

                <div>
                    @if ($isRegistered)
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Anda Terdaftar Sebagai Peserta
                            </span>
                            <form method="POST" action="{{ route('user.kegiatan.batal', $kegiatan) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                                    Batalkan
                                </button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('user.kegiatan.daftar', $kegiatan) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#680A10] hover:bg-[#54080D] text-white text-xs sm:text-sm font-bold transition shadow-xs">
                                Daftarkan Diri Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
                {{ $kegiatan->nama_kegiatan }}
            </h1>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                {{ $kegiatan->deskripsi }}
            </p>

            {{-- 4 Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TANGGAL ACARA</span>
                    <span class="font-bold text-slate-800 mt-1 block">{{ $kegiatan->tanggal ? $kegiatan->tanggal->translatedFormat('l, d F Y') : '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">WAKTU</span>
                    <span class="font-bold text-slate-800 mt-1 block">{{ substr($kegiatan->waktu_mulai, 0, 5) }} - {{ substr($kegiatan->waktu_selesai ?? '15:30', 0, 5) }} WIB</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI</span>
                    <span class="font-bold text-slate-800 mt-1 block truncate" title="{{ $kegiatan->lokasi }}">{{ $kegiatan->lokasi }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KUOTA PESERTA</span>
                    <span class="font-bold text-slate-800 mt-1 block">{{ $kegiatan->peserta->count() }} / {{ $kegiatan->kuota_peserta ?: 100 }} Orang</span>
                </div>
            </div>
        </div>

        {{-- Agenda List --}}
        @if ($kegiatan->agenda->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900">Susunan Jadwal &amp; Agenda Acara</h2>
                <div class="divide-y divide-slate-100">
                    @foreach ($kegiatan->agenda as $ag)
                        <div class="py-3 flex items-start gap-4 text-xs">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded shrink-0">
                                {{ substr($ag->waktu_mulai, 0, 5) }} - {{ substr($ag->waktu_selesai ?? '12:00', 0, 5) }}
                            </span>
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $ag->judul }}</h3>
                                <p class="text-slate-500 mt-0.5">{{ $ag->keterangan }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.user>
