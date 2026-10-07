<x-layouts.admin title="Detail Rencana Anggaran — SIPENA IDX">
    <div class="space-y-5" x-data="{ showDelete: false }">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('admin.rencana-kegiatan.index') }}" class="hover:text-slate-900 transition">Anggaran &amp; RKAP</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-semibold">{{ $rencana->kode }}</span>
        </div>

        {{-- Flash Notification --}}
        @if (session('success'))
            <div role="status" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold leading-none">&times;</button>
            </div>
        @endif

        {{-- Header Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                        {{ $rencana->kode }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $rencana->status_class }}">
                        {{ $rencana->status_label }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                        {{ $rencana->jenis_kegiatan }}
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 mt-2">
                    {{ $rencana->nama_kegiatan }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Dibuat oleh {{ $rencana->admin?->name ?? 'Admin BEI' }} &bull; {{ $rencana->created_at?->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Quick Ajukan Button (if draft) --}}
                @if ($rencana->status === 'draft')
                    <form method="POST" action="{{ route('admin.rencana-kegiatan.ajukan', $rencana) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-lg transition shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Ajukan ke Review</span>
                        </button>
                    </form>
                @endif

                {{-- Edit --}}
                <a href="{{ route('admin.rencana-kegiatan.edit', $rencana) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit</span>
                </a>

                {{-- Delete --}}
                <button type="button"
                        @click="showDelete = true"
                        class="p-2 rounded-lg border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                        title="Hapus Rencana">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Main 2-Column Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Left Column (2 Cols): Detail & Pos Anggaran --}}
            <div class="lg:col-span-2 space-y-4">
                {{-- Card: Alokasi Pagu Anggaran --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                Rp
                            </div>
                            <h2 class="text-sm font-bold text-slate-800">Alokasi Anggaran Belanja</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                            Pagu RKAP
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 pt-1">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NOMINAL DIUSULKAN</div>
                            <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-1">
                                {{ $rencana->formatted_anggaran }}
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 text-right sm:max-w-xs">
                            @php
                                $paguTotal = \App\Http\Controllers\Admin\RencanaKegiatanController::PAGU_RKAP_DEFAULT;
                                $porsi = $paguTotal > 0 ? round(($rencana->anggaran / $paguTotal) * 100, 2) : 0;
                            @endphp
                            <span>Porsi terhadap total pagu RKAP: <strong class="text-slate-800 font-mono">{{ $porsi }}%</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Card: Pelaksanaan & Operasional --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100">
                        Rincian Pelaksanaan Acara
                    </h2>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Pelaksanaan</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $rencana->tanggal_rencana ? $rencana->tanggal_rencana->translatedFormat('l, d F Y') : '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Waktu Acara</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $detail?->waktu_mulai ? substr($detail->waktu_mulai, 0, 5) : '09:00' }} -
                                {{ $detail?->waktu_selesai ? substr($detail->waktu_selesai, 0, 5) : '15:30' }} WIB
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tempat / Lokasi</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $detail?->lokasi ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target Peserta</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $detail?->target_peserta ? number_format($detail->target_peserta, 0, ',', '.') : '-' }} Orang
                            </dd>
                        </div>
                    </dl>

                    @if ($detail?->deskripsi)
                        <div class="pt-3 border-t border-slate-100 text-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Deskripsi Kegiatan</span>
                            <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $detail->deskripsi }}</p>
                        </div>
                    @endif

                    @if ($rencana->keterangan)
                        <div class="pt-3 border-t border-slate-100 text-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan Tambahan</span>
                            <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $rencana->keterangan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column (1 Col): Status & Entitas Terkait --}}
            <div class="space-y-4">
                {{-- Card: Alur Tahapan Anggaran --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Status &amp; Alur Persetujuan
                    </h2>

                    <div class="relative pl-5 space-y-4 text-xs before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        {{-- Step 1: Draft --}}
                        <div class="relative">
                            <span class="absolute -left-5 top-0.5 w-3 h-3 rounded-full bg-emerald-600 ring-4 ring-white"></span>
                            <div class="font-bold text-slate-900">1. Draft Rencana Anggaran</div>
                            <div class="text-[11px] text-slate-500">{{ $rencana->created_at?->translatedFormat('d M Y') }}</div>
                        </div>

                        {{-- Step 2: Diajukan --}}
                        <div class="relative">
                            <span class="absolute -left-5 top-0.5 w-3 h-3 rounded-full {{ in_array($rencana->status, ['diajukan', 'disetujui']) ? 'bg-emerald-600' : 'bg-slate-300' }} ring-4 ring-white"></span>
                            <div class="font-bold {{ in_array($rencana->status, ['diajukan', 'disetujui']) ? 'text-slate-900' : 'text-slate-400' }}">
                                2. Pengajuan ke Review Anggaran
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $rencana->status === 'draft' ? 'Belum diajukan' : 'Telah diverifikasi sistem' }}
                            </div>
                        </div>

                        {{-- Step 3: Keputusan --}}
                        <div class="relative">
                            <span class="absolute -left-5 top-0.5 w-3 h-3 rounded-full {{ $rencana->status === 'disetujui' ? 'bg-emerald-600' : ($rencana->status === 'ditolak' ? 'bg-red-600' : 'bg-slate-300') }} ring-4 ring-white"></span>
                            <div class="font-bold {{ in_array($rencana->status, ['disetujui', 'ditolak']) ? 'text-slate-900' : 'text-slate-400' }}">
                                3. Persetujuan Pagu RKAP
                            </div>
                            <div class="text-[11px] text-slate-500">
                                @if ($rencana->status === 'disetujui')
                                    <span class="text-emerald-700 font-semibold">Disetujui untuk realisasi</span>
                                @elseif ($rencana->status === 'ditolak')
                                    <span class="text-rose-700 font-semibold">Ditolak / Revisi diperlukan</span>
                                @else
                                    Menunggu proses review
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Dokumen & Hubungan Relasi --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Dokumen Terkait
                    </h2>

                    <div class="space-y-2 text-xs">
                        {{-- Pengajuan --}}
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">BERKAS PENGAJUAN</div>
                                <div class="font-semibold text-slate-800">
                                    {{ $pengajuan?->nomor_pengajuan ?? 'Belum ada nomor pengajuan' }}
                                </div>
                            </div>
                            @if ($pengajuan)
                                <a href="{{ route('admin.pengajuan.show', $pengajuan) }}" class="text-[#680A10] font-bold text-[11px] hover:underline">
                                    Buka &rarr;
                                </a>
                            @endif
                        </div>

                        {{-- Kegiatan Terbit --}}
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">AGENDA KEGIATAN</div>
                                <div class="font-semibold text-slate-800">
                                    {{ $rencana->kegiatan ? $rencana->kegiatan->nama_kegiatan : 'Belum diterbitkan ke kegiatan' }}
                                </div>
                            </div>
                            @if ($rencana->kegiatan)
                                <a href="{{ route('admin.kegiatan.show', $rencana->kegiatan) }}" class="text-[#680A10] font-bold text-[11px] hover:underline">
                                    Lihat &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDelete"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showDelete = false"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Hapus Rencana Anggaran?</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Anda yakin ingin menghapus rencana anggaran <strong>{{ $rencana->nama_kegiatan }}</strong>?
                    </p>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button"
                            @click="showDelete = false"
                            class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <form action="{{ route('admin.rencana-kegiatan.destroy', $rencana) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-xs font-semibold text-white transition shadow-xs">
                            Ya, Hapus Rencana
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
