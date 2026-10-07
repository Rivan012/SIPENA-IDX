<x-layouts.admin title="Anggaran & Rencana Kegiatan — SIPENA IDX">
    <div class="space-y-5" x-data="{ showDelete: false, deleteUrl: '', deleteName: '' }">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div role="status" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-xs animate-in fade-in">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold leading-none">&times;</button>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider" aria-label="Breadcrumb">
                    <span>SIPENA</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span>MANAJEMEN KEUANGAN</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-700">ANGGARAN &amp; RKAP</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                    Rencana Anggaran &amp; Alokasi RKAP
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 max-w-2xl leading-relaxed">
                    Pengelolaan pagu anggaran tahunan, penyusunan pos belanja kegiatan edukasi, serta tracking persetujuan anggaran KP BEI Bengkulu.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                {{-- Export CSV --}}
                <a href="{{ route('admin.rencana-kegiatan.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export CSV</span>
                </a>

                {{-- Primary Action --}}
                <a href="{{ route('admin.rencana-kegiatan.create') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Buat Rencana Anggaran</span>
                </a>
            </div>
        </div>

        {{-- 4 KPI Metric Cards (RKAP & Realisasi) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- Card 1: Pagu RKAP --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PAGU ANGGARAN RKAP</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700">TA {{ $stats['selected_year'] }}</span>
                    </div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2 font-mono">
                        Rp {{ number_format($stats['pagu_rkap'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Plafon Operasional</span>
                    <span class="font-bold text-slate-700">{{ $stats['total_kegiatan'] }} Usulan</span>
                </div>
            </div>

            {{-- Card 2: Total Alokasi Rencana --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL TERENCANA</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700">Draft + Review</span>
                    </div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2 font-mono">
                        Rp {{ number_format($stats['total_alokasi'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Pos Diajukan</span>
                    <span class="font-bold text-amber-600">{{ $stats['count_diajukan'] }} Menunggu</span>
                </div>
            </div>

            {{-- Card 3: Anggaran Disetujui --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">ANGGARAN DISETUJUI</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">Disetujui</span>
                    </div>
                    <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-2 font-mono">
                        Rp {{ number_format($stats['total_disetujui'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Realisasi Kegiatan</span>
                    <span class="font-bold text-emerald-700">{{ $stats['persen_penyerapan'] }}% Terpakai</span>
                </div>
            </div>

            {{-- Card 4: Sisa Pagu Anggaran --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">SISA PAGU RKAP</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700">Saldo</span>
                    </div>
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2 font-mono">
                        Rp {{ number_format($stats['sisa_pagu'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100">
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-[#0B63E5] h-1.5 rounded-full" style="width: {{ $stats['persen_penyerapan'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Controls Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-xs space-y-3">
            <form method="GET" action="{{ route('admin.rencana-kegiatan.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1 max-w-md">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari kegiatan, pos anggaran, atau lokasi..."
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                {{-- Dropdowns & Controls --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Year Selector --}}
                    <select name="year" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 px-2.5 py-1.5 bg-white text-slate-700 font-medium focus:outline-none focus:ring-1 focus:ring-[#680A10]">
                        <option value="2025" {{ request('year', $stats['selected_year']) == '2025' ? 'selected' : '' }}>Tahun 2025</option>
                        <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>Tahun 2026</option>
                        <option value="all" {{ request('year') == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    </select>

                    {{-- Category Selector --}}
                    <select name="jenis" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 px-2.5 py-1.5 bg-white text-slate-700 font-medium focus:outline-none focus:ring-1 focus:ring-[#680A10]">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request('jenis') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    @if(request()->anyFilled(['search', 'status', 'jenis', 'year']))
                        <a href="{{ route('admin.rencana-kegiatan.index') }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>

            {{-- Status Filter Badges (Pills) --}}
            <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-100 text-xs">
                @php
                    $curStatus = request('status', '');
                    $makeUrl = fn($st) => route('admin.rencana-kegiatan.index', array_merge(request()->except('page'), ['status' => $st]));
                @endphp

                <a href="{{ $makeUrl('') }}"
                   class="px-3 py-1 rounded-full font-semibold transition {{ $curStatus === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua ({{ $stats['total_kegiatan'] }})
                </a>

                <a href="{{ $makeUrl('draft') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'draft' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Draft ({{ $stats['count_draft'] }})
                </a>

                <a href="{{ $makeUrl('diajukan') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'diajukan' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'diajukan' ? 'bg-white' : 'bg-amber-500' }}"></span>
                    Diajukan Review ({{ $stats['count_diajukan'] }})
                </a>

                <a href="{{ $makeUrl('disetujui') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'disetujui' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                    Disetujui ({{ $stats['count_disetujui'] }})
                </a>

                <a href="{{ $makeUrl('ditolak') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'ditolak' ? 'bg-white' : 'bg-rose-500' }}"></span>
                    Ditolak ({{ $stats['count_ditolak'] }})
                </a>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">KODE / NO. RK</th>
                            <th class="py-3 px-4">NAMA KEGIATAN &amp; KATEGORI</th>
                            <th class="py-3 px-4">TANGGAL &amp; LOKASI</th>
                            <th class="py-3 px-4 text-center">TARGET PESERTA</th>
                            <th class="py-3 px-4 text-right">ALOKASI ANGGARAN</th>
                            <th class="py-3 px-4 text-center">STATUS</th>
                            <th class="py-3 px-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rencanaList as $r)
                            @php
                                $detail = $r->detailKegiatan;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                {{-- Kode Rencana --}}
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-600 whitespace-nowrap">
                                    {{ $r->kode }}
                                </td>

                                {{-- Nama Kegiatan & Kategori --}}
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('admin.rencana-kegiatan.show', $r) }}" class="hover:text-[#680A10] transition">
                                            {{ $r->nama_kegiatan }}
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $r->jenis_kegiatan }}
                                        </span>
                                        @if ($r->kegiatan)
                                            <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Terdaftar di Acara
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tanggal & Lokasi --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        {{ $r->tanggal_rencana ? $r->tanggal_rencana->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 truncate max-w-[200px]" title="{{ $detail?->lokasi }}">
                                        {{ $detail?->lokasi ?? '-' }}
                                    </div>
                                </td>

                                {{-- Target Peserta --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="font-semibold text-slate-700">
                                        {{ $detail?->target_peserta ? number_format($detail->target_peserta, 0, ',', '.') : '-' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">Orang</span>
                                </td>

                                {{-- Alokasi Anggaran --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="font-bold text-slate-900 font-mono text-sm">
                                        {{ $r->formatted_anggaran }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        SPK Pagu RKAP
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $r->status_class }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Quick Ajukan (if draft) --}}
                                        @if ($r->status === 'draft')
                                            <form method="POST" action="{{ route('admin.rencana-kegiatan.ajukan', $r) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        title="Ajukan ke Peninjauan"
                                                        class="px-2 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-[11px] border border-amber-200 transition">
                                                    Ajukan
                                                </button>
                                            </form>
                                        @endif

                                        {{-- View Detail --}}
                                        <a href="{{ route('admin.rencana-kegiatan.show', $r) }}"
                                           title="Lihat Detail"
                                           class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.rencana-kegiatan.edit', $r) }}"
                                           title="Edit Rencana"
                                           class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-[#680A10] hover:bg-rose-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Delete Button --}}
                                        <button type="button"
                                                @click="showDelete = true; deleteUrl = '{{ route('admin.rencana-kegiatan.destroy', $r) }}'; deleteName = '{{ addslashes($r->nama_kegiatan) }}'"
                                                title="Hapus Rencana"
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-700">Tidak ada rencana anggaran ditemukan</div>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Belum ada data anggaran yang sesuai kriteria pencarian atau filter yang dipilih.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.rencana-kegiatan.create') }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs transition shadow-xs">
                                            + Tambah Rencana Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            @if ($rencanaList->hasPages())
                <div class="p-3.5 border-t border-slate-200 bg-slate-50/50">
                    {{ $rencanaList->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal (Alpine.js) --}}
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
                        Anda yakin ingin menghapus rencana anggaran <strong class="text-slate-800" x-text="deleteName"></strong>? Seluruh pos rincian anggaran kegiatan ini akan dihapus dari sistem.
                    </p>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button"
                            @click="showDelete = false"
                            class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST" class="inline">
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
