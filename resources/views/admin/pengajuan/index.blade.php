<x-layouts.admin title="Approval Pengajuan Kegiatan — SIPENA IDX">
    <div class="space-y-5" x-data="{
        showModal: false,
        modalAction: '',
        modalId: null,
        modalNomor: '',
        modalNama: '',
        modalAnggaran: '',
        openReview(id, nomor, nama, anggaran) {
            this.modalId = id;
            this.modalNomor = nomor;
            this.modalNama = nama;
            this.modalAnggaran = anggaran;
            this.modalAction = '{{ url('admin/pengajuan') }}/' + id + '/status';
            this.showModal = true;
        }
    }">
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
                    <span>TATA KELOLA</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-700">VERIFIKASI &amp; PERSETUJUAN</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                    Approval &amp; Persetujuan Kegiatan
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 max-w-2xl leading-relaxed">
                    Pemeriksaan usulan program kerja, verifikasi kelayakan anggaran RKAP, serta penerbitan jadwal edukasi KP BEI Bengkulu.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                {{-- Export CSV --}}
                <a href="{{ route('admin.pengajuan.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export CSV</span>
                </a>

                {{-- Link ke Rencana Anggaran --}}
                <a href="{{ route('admin.rencana-kegiatan.create') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Usulkan Pengajuan Baru</span>
                </a>
            </div>
        </div>

        {{-- 4 KPI Metric Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- Card 1: Menunggu Approval (Highlight Priority) --}}
            <div class="bg-white rounded-xl border {{ $stats['menunggu'] > 0 ? 'border-amber-300 ring-2 ring-amber-100' : 'border-slate-200' }} p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">MENUNGGU REVIEW</span>
                        @if ($stats['menunggu'] > 0)
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                        @endif
                    </div>
                    <div class="text-2xl font-bold {{ $stats['menunggu'] > 0 ? 'text-amber-700' : 'text-slate-900' }} mt-2 font-mono">
                        {{ $stats['menunggu'] }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Perlu Tindakan</span>
                    <span class="font-bold text-amber-600">Prioritas</span>
                </div>
            </div>

            {{-- Card 2: Sedang Ditinjau --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">SEDANG DITINJAU</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-sky-50 text-sky-700">Proses</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2 font-mono">
                        {{ $stats['diproses'] }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Tahap Verifikasi</span>
                    <span class="font-bold text-slate-700">{{ $stats['pending_total'] }} Total Antrean</span>
                </div>
            </div>

            {{-- Card 3: Disetujui --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TELAH DISETUJUI</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">Diterbitkan</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-700 mt-2 font-mono">
                        {{ $stats['disetujui'] }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Masuk Kalender</span>
                    <span class="font-bold text-emerald-700">Siap Laksana</span>
                </div>
            </div>

            {{-- Card 4: Total Anggaran dalam Review --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">ANGGARAN DALAM REVIEW</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700">Pagu Usulan</span>
                    </div>
                    <div class="text-xl font-bold text-slate-900 mt-2 font-mono truncate" title="Rp {{ number_format($stats['anggaran_review'], 0, ',', '.') }}">
                        Rp {{ number_format($stats['anggaran_review'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Revisi / Ditolak</span>
                    <span class="font-bold text-slate-700">{{ $stats['revisi'] + $stats['ditolak'] }} Berkas</span>
                </div>
            </div>
        </div>

        {{-- Filters & Controls Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-xs space-y-3">
            <form method="GET" action="{{ route('admin.pengajuan.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1 max-w-md">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nomor pengajuan, nama program, atau lokasi..."
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                {{-- Category Selector --}}
                <div class="flex flex-wrap items-center gap-2">
                    <select name="kategori" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 px-2.5 py-1.5 bg-white text-slate-700 font-medium focus:outline-none focus:ring-1 focus:ring-[#680A10]">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    @if(request()->anyFilled(['search', 'status', 'kategori']))
                        <a href="{{ route('admin.pengajuan.index') }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>

            {{-- Status Filter Badges (Pills) --}}
            <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-100 text-xs">
                @php
                    $curStatus = request('status', '');
                    $makeUrl = fn($st) => route('admin.pengajuan.index', array_merge(request()->except('page'), ['status' => $st]));
                @endphp

                <a href="{{ $makeUrl('') }}"
                   class="px-3 py-1 rounded-full font-semibold transition {{ $curStatus === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua ({{ $stats['total'] }})
                </a>

                <a href="{{ $makeUrl('diajukan') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'diajukan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'diajukan' ? 'bg-white' : 'bg-amber-500' }}"></span>
                    Menunggu Approval ({{ $stats['menunggu'] }})
                </a>

                <a href="{{ $makeUrl('diproses') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'diproses' ? 'bg-sky-600 text-white shadow-xs' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'diproses' ? 'bg-white' : 'bg-sky-500' }}"></span>
                    Sedang Ditinjau ({{ $stats['diproses'] }})
                </a>

                <a href="{{ $makeUrl('disetujui') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'disetujui' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'disetujui' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                    Disetujui ({{ $stats['disetujui'] }})
                </a>

                <a href="{{ $makeUrl('revisi') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'revisi' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'revisi' ? 'bg-white' : 'bg-purple-500' }}"></span>
                    Perlu Revisi ({{ $stats['revisi'] }})
                </a>

                <a href="{{ $makeUrl('ditolak') }}"
                   class="px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $curStatus === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $curStatus === 'ditolak' ? 'bg-white' : 'bg-rose-500' }}"></span>
                    Ditolak ({{ $stats['ditolak'] }})
                </a>
            </div>
        </div>

        {{-- Main Approval Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">NO. PENGAJUAN</th>
                            <th class="py-3 px-4">PROGRAM KERJA &amp; KATEGORI</th>
                            <th class="py-3 px-4">TGL PENGAJUAN &amp; ACARA</th>
                            <th class="py-3 px-4 text-right">ANGGARAN DIAJUKAN</th>
                            <th class="py-3 px-4">PENGUSUL</th>
                            <th class="py-3 px-4 text-center">STATUS</th>
                            <th class="py-3 px-4 text-right">AKSI VERIFIKASI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengajuanList as $p)
                            @php
                                $rencana = $p->rencanaKegiatan;
                                $detail = $rencana?->detailKegiatan;
                                $isPending = in_array($p->status, ['diajukan', 'diproses']);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition {{ $p->status === 'diajukan' ? 'bg-amber-50/15' : '' }}">
                                {{-- Nomor Pengajuan --}}
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-700 whitespace-nowrap">
                                    <a href="{{ route('admin.pengajuan.show', $p) }}" class="hover:text-[#680A10] transition">
                                        {{ $p->nomor_pengajuan }}
                                    </a>
                                </td>

                                {{-- Program Kerja & Kategori --}}
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('admin.pengajuan.show', $p) }}" class="hover:text-[#680A10] transition">
                                            {{ $p->nama_kegiatan }}
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $p->jenis_kegiatan }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 truncate max-w-[160px]" title="{{ $detail?->lokasi }}">
                                            &bull; {{ $detail?->lokasi ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        {{ $p->tanggal_pengajuan ? $p->tanggal_pengajuan->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Acara: {{ $rencana?->tanggal_rencana ? $rencana->tanggal_rencana->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>

                                {{-- Anggaran --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="font-bold text-slate-900 font-mono text-sm">
                                        {{ $p->formatted_anggaran }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        SPK Anggaran
                                    </div>
                                </td>

                                {{-- Pengusul --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-medium text-slate-800">
                                        {{ $p->admin?->name ?? 'Admin BEI' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $p->admin?->jabatan ?? 'Koordinator' }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $p->status_class }}">
                                        {{ $p->status_label }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Quick Review / Keputusan Button --}}
                                        <button type="button"
                                                @click="openReview({{ $p->id }}, '{{ $p->nomor_pengajuan }}', '{{ addslashes($p->nama_kegiatan) }}', '{{ $p->formatted_anggaran }}')"
                                                class="px-2.5 py-1 rounded-md text-xs font-semibold transition shadow-xs {{ $isPending ? 'bg-[#680A10] hover:bg-[#54080D] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                                            {{ $isPending ? 'Proses &rarr;' : 'Ubah Status' }}
                                        </button>

                                        {{-- View Details --}}
                                        <a href="{{ route('admin.pengajuan.show', $p) }}"
                                           title="Lihat Detail Berkas"
                                           class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-700">Tidak ada pengajuan ditemukan</div>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Belum ada pengajuan kegiatan yang memerlukan verifikasi pada kriteria filter ini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            @if ($pengajuanList->hasPages())
                <div class="p-3.5 border-t border-slate-200 bg-slate-50/50">
                    {{ $pengajuanList->links() }}
                </div>
            @endif
        </div>

        {{-- Verification & Decision Modal (Alpine.js) --}}
        <div x-show="showModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModal = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-[#680A10] flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Form Keputusan Approval</h3>
                            <p class="text-[11px] text-slate-500 font-mono" x-text="modalNomor"></p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                {{-- Summary Box --}}
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <div class="font-bold text-slate-900" x-text="modalNama"></div>
                    <div class="text-slate-500 flex items-center justify-between pt-1">
                        <span>Alokasi Anggaran:</span>
                        <span class="font-bold text-slate-900 font-mono" x-text="modalAnggaran"></span>
                    </div>
                </div>

                {{-- Action Form --}}
                <form :action="modalAction" method="POST" class="space-y-4" x-data="{ decision: 'disetujui' }">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Pilih Keputusan Persetujuan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1"
                                   :class="decision === 'disetujui' ? 'border-emerald-600 bg-emerald-50 text-emerald-800 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                <input type="radio" name="status" value="disetujui" x-model="decision" class="sr-only">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-[11px]">Setujui</span>
                            </label>

                            <label class="cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1"
                                   :class="decision === 'revisi' ? 'border-purple-600 bg-purple-50 text-purple-800 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                <input type="radio" name="status" value="revisi" x-model="decision" class="sr-only">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="text-[11px]">Minta Revisi</span>
                            </label>

                            <label class="cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1"
                                   :class="decision === 'ditolak' ? 'border-rose-600 bg-rose-50 text-rose-800 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                <input type="radio" name="status" value="ditolak" x-model="decision" class="sr-only">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="text-[11px]">Tolak</span>
                            </label>
                        </div>
                    </div>

                    {{-- Catatan / Alasan --}}
                    <div>
                        <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1">
                            <span x-text="decision === 'revisi' ? 'Catatan Perbaikan yang Perlu Direvisi' : (decision === 'ditolak' ? 'Alasan Penolakan Pengajuan' : 'Catatan Persetujuan &amp; Arahan Pelaksanaan')"></span>
                        </label>
                        <textarea name="catatan"
                                  id="catatan"
                                  rows="3"
                                  placeholder="Tuliskan catatan arahan, pos belanja, atau perbaikan bila ada..."
                                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition"></textarea>
                    </div>

                    {{-- Option: Otomatis Terbitkan ke Kegiatan (only if disetujui) --}}
                    <div x-show="decision === 'disetujui'" class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 flex items-start gap-2.5">
                        <input type="checkbox"
                               name="terbitkan_kegiatan"
                               id="terbitkan_kegiatan"
                               value="1"
                               checked
                               class="mt-0.5 rounded text-[#680A10] focus:ring-[#680A10]">
                        <label for="terbitkan_kegiatan" class="text-xs text-emerald-900 leading-snug">
                            <strong>Otomatis Terbitkan ke Jadwal Kegiatan</strong>
                            <span class="block text-[11px] text-emerald-700 mt-0.5">Jadwal akan langsung muncul pada modul Kegiatan dan Kalender Acara Edukasi.</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button"
                                @click="showModal = false"
                                class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#680A10] hover:bg-[#54080D] text-xs font-semibold text-white transition shadow-xs">
                            Simpan Keputusan Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
