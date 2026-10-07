<x-layouts.user title="Kegiatan Saya — SIPENA IDX">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kegiatan Saya</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh program edukasi dan sosialisasi pasar modal yang Anda ikuti.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">PROGRAM KEGIATAN</th>
                            <th class="py-3 px-4">TANGGAL &amp; WAKTU</th>
                            <th class="py-3 px-4">LOKASI</th>
                            <th class="py-3 px-4 text-center">STATUS KEHADIRAN</th>
                            <th class="py-3 px-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kegiatanList as $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('user.kegiatan.show', $item) }}" class="hover:text-[#680A10] transition">
                                            {{ $item->nama_kegiatan }}
                                        </a>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 mt-1 inline-block">
                                        {{ $item->jenis_kegiatan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        {{ $item->tanggal ? $item->tanggal->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ substr($item->waktu_mulai, 0, 5) }} - {{ substr($item->waktu_selesai ?? '15:30', 0, 5) }} WIB
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-[200px] truncate" title="{{ $item->lokasi }}">
                                    {{ $item->lokasi }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $statusPresensi = $item->pivot->status ?? 'terdaftar';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $statusPresensi === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($statusPresensi) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('user.kegiatan.show', $item) }}" class="font-semibold text-xs text-[#680A10] hover:underline">
                                            Rincian &rarr;
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-700">Belum ada kegiatan yang diikuti</div>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Anda belum mendaftar pada program edukasi pasar modal manapun.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('user.kegiatan.index') }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#680A10] text-white font-semibold text-xs transition shadow-xs">
                                            Jelajahi Kegiatan Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kegiatanList->hasPages())
                <div class="p-3.5 border-t border-slate-200 bg-slate-50/50">
                    {{ $kegiatanList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.user>
