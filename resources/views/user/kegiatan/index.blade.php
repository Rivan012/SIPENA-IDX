<x-layouts.user title="Daftar Kegiatan Edukasi — SIPENA IDX">
    <div class="space-y-6">
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

        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog Kegiatan Edukasi</h1>
                <p class="text-xs text-slate-500 mt-0.5">Ikuti seminar, sekolah pasar modal, dan workshop investasi secara gratis dan bersertifikat.</p>
            </div>

            <form method="GET" action="{{ route('user.kegiatan.index') }}" class="flex items-center gap-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari program kerja, materi..."
                       class="px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 w-64">
                <button type="submit" class="px-4 py-2 bg-[#680A10] text-white text-xs font-semibold rounded-xl hover:bg-[#54080D] transition">
                    Cari
                </button>
            </form>
        </div>

        {{-- Filter Pills --}}
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('user.kegiatan.index') }}"
               class="px-3 py-1.5 rounded-full font-semibold transition {{ !request('jenis') ? 'bg-[#680A10] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua Program
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('user.kegiatan.index', ['jenis' => $cat]) }}"
                   class="px-3 py-1.5 rounded-full font-semibold transition {{ request('jenis') == $cat ? 'bg-[#680A10] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- Grid Cards Kegiatan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($kegiatanList as $keg)
                @php
                    $isJoined = in_array($keg->id, $userKegiatanIds);
                    $registeredCount = $keg->peserta->count();
                    $kuota = $keg->kuota_peserta ?: 100;
                    $isFull = $registeredCount >= $kuota;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $keg->jenis_kegiatan }}
                            </span>
                            @if ($isJoined)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Terdaftar
                                </span>
                            @endif
                        </div>

                        <h2 class="text-base font-bold text-slate-900 leading-snug">
                            <a href="{{ route('user.kegiatan.show', $keg) }}" class="hover:text-[#680A10] transition">
                                {{ $keg->nama_kegiatan }}
                            </a>
                        </h2>

                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $keg->deskripsi }}
                        </p>

                        <div class="space-y-1.5 pt-2 border-t border-slate-100 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $keg->tanggal ? $keg->tanggal->translatedFormat('l, d F Y') : '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ substr($keg->waktu_mulai, 0, 5) }} - {{ substr($keg->waktu_selesai ?? '15:30', 0, 5) }} WIB</span>
                            </div>
                            <div class="flex items-center gap-2 truncate">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span class="truncate">{{ $keg->lokasi }}</span>
                            </div>
                        </div>

                        {{-- Kuota Progress --}}
                        <div class="pt-2">
                            <div class="flex justify-between text-[11px] text-slate-500 mb-1">
                                <span>Kuota Terisi: <strong>{{ $registeredCount }}/{{ $kuota }}</strong></span>
                                <span>{{ min(100, round(($registeredCount / $kuota) * 100)) }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#680A10] h-1.5 rounded-full" style="width: {{ min(100, round(($registeredCount / $kuota) * 100)) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('user.kegiatan.show', $keg) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                            Lihat Rincian
                        </a>

                        @if ($isJoined)
                            <form method="POST" action="{{ route('user.kegiatan.batal', $keg) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                                    Batalkan
                                </button>
                            </form>
                        @elseif ($isFull)
                            <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 text-xs font-semibold">
                                Kuota Penuh
                            </span>
                        @else
                            <form method="POST" action="{{ route('user.kegiatan.daftar', $keg) }}">
                                @csrf
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold transition shadow-xs">
                                    Daftar Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                    Tidak ada kegiatan edukasi yang tersedia saat ini.
                </div>
            @endforelse
        </div>

        @if ($kegiatanList->hasPages())
            <div class="pt-4">
                {{ $kegiatanList->links() }}
            </div>
        @endif
    </div>
</x-layouts.user>
