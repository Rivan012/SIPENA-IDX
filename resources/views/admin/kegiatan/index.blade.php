<x-layouts.admin title="Kegiatan — SIPENA IDX">
    <div class="space-y-5" x-data="{ showDelete: false, deleteUrl: '', deleteName: '' }">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div role="status" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-xs font-medium flex items-center justify-between" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 text-sm leading-none">&times;</button>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
                    <span>SIPENA</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span>Kegiatan</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-700 font-medium">Daftar Kegiatan Edukasi</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1.5">Manajemen &amp; Perencanaan Kegiatan Edukasi</h1>
                <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                    Kelola agenda edukasi pasar modal, sekolah pasar modal (SPM), dan sosialisasi Kantor Perwakilan (KP) BEI se-Indonesia secara terintegrasi dan akuntabel.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 rounded-lg text-xs font-medium text-slate-700 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Ekspor Jadwal</span>
                </a>
                <a href="{{ route('admin.kegiatan.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Buat Kegiatan Baru</span>
                </a>
            </div>
        </div>

        {{-- 4 KPI Metric Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- Card 1: Total Kegiatan --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL KEGIATAN {{ $stats['year'] ?? 2025 }}</span>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['total'] }}</div>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                    @if ($stats['rencana'] > 0)
                        <span class="font-bold text-blue-700">{{ round(($stats['total'] / $stats['rencana']) * 100) }}%</span> Target dari RKAP
                    @else
                        <span>Rencana TA {{ $stats['year'] ?? 2025 }}</span>
                    @endif
                </div>
            </div>

            {{-- Card 2: Jadwal Bulan Ini --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">JADWAL BULAN INI</span>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['bulan_ini'] }}</div>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                    <span class="font-semibold text-slate-700">{{ $stats['lokasi_bulan_ini'] }}</span> lokasi aktif
                </div>
            </div>

            {{-- Card 3: Selesai & LPJ --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">SELESAI &amp; LPJ</span>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['selesai'] }}</div>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                    <span class="text-emerald-700 font-semibold">{{ $stats['lpj'] }} LPJ</span> verified &bull; {{ max(0, $stats['selesai'] - $stats['lpj']) }} pending
                </div>
            </div>

            {{-- Card 4: Menunggu Approval --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">MENUNGGU APPROVAL</span>
                    <div class="text-2xl font-bold {{ $stats['menunggu'] > 0 ? 'text-red-600' : 'text-slate-900' }} mt-2">{{ $stats['menunggu'] }}</div>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[11px]">
                    <a href="{{ route('admin.kegiatan.index', ['status' => 'menunggu_approval']) }}" class="font-semibold text-[#7A0C14] hover:underline inline-flex items-center gap-1">
                        <span>Butuh Review</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            {{-- Filter Bar --}}
            <div class="p-4 border-b border-slate-100">
                <form method="GET" action="{{ route('admin.kegiatan.index') }}" class="flex flex-col sm:flex-row gap-3">
                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama kegiatan, kode agenda, atau PIC KP..."
                               class="w-full h-9 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:bg-white focus:border-[#7A0C14]/30 transition">
                    </div>

                    {{-- Status Dropdown --}}
                    <select name="status" onchange="this.form.submit()"
                            class="h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition">
                        <option value="">Status: Semua</option>
                        @foreach (\App\Models\Kegiatan::STATUS as $val => [$label])
                            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Kategori Dropdown --}}
                    <select name="kategori" onchange="this.form.submit()"
                            class="h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition">
                        <option value="">Kategori: Semua</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ ucfirst($k) }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="h-9 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg text-xs font-medium text-slate-700 transition">
                        Cari
                    </button>

                    @if (request()->hasAny(['search', 'status', 'kategori']))
                        <a href="{{ route('admin.kegiatan.index') }}"
                           class="h-9 px-3 inline-flex items-center rounded-lg text-xs font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3 px-4">KODE &amp; NAMA KEGIATAN</th>
                            <th class="py-3 px-4">KATEGORI</th>
                            <th class="py-3 px-4">LOKASI &amp; WAKTU</th>
                            <th class="py-3 px-4">ESTIMASI BIAYA</th>
                            <th class="py-3 px-4">TARGET PESERTA</th>
                            <th class="py-3 px-4">STATUS</th>
                            <th class="py-3 px-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $group = null; @endphp
                        @forelse ($kegiatan as $item)
                            @php
                                $g = in_array($item->status, \App\Models\Kegiatan::SELESAI) ? 'SELESAI & LPJ' : 'BERJALAN & MENDATANG';
                            @endphp
                            @if ($g !== $group)
                                @php $group = $g; @endphp
                                <tr class="bg-slate-50/80">
                                    <td colspan="7" class="py-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        {{ $g }}
                                    </td>
                                </tr>
                            @endif
                            @php $detail = $item->rencanaKegiatan?->detailKegiatan; @endphp
                            <tr class="hover:bg-slate-50/80 transition group">
                                {{-- Kode & Nama Kegiatan --}}
                                <td class="py-3 px-4 max-w-xs">
                                    <div class="text-[10px] font-mono font-semibold text-slate-500 tracking-tight">{{ $item->kode }}</div>
                                    <a href="{{ route('admin.kegiatan.show', $item) }}" class="font-semibold text-slate-900 hover:text-[#7A0C14] transition line-clamp-1">
                                        {{ $item->nama_kegiatan }}
                                    </a>
                                    @if ($pic = $item->rencanaKegiatan?->admin?->name)
                                        <div class="text-[11px] text-slate-500 mt-0.5">PIC: {{ $pic }}</div>
                                    @endif
                                </td>

                                {{-- Kategori --}}
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-semibold uppercase">
                                        {{ $item->jenis_kegiatan }}
                                    </span>
                                </td>

                                {{-- Lokasi & Waktu --}}
                                <td class="py-3 px-4 text-slate-600">
                                    <div class="max-w-[200px] truncate font-medium text-slate-800" title="{{ $item->lokasi }}">
                                        {{ $item->lokasi }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->tanggal->format('d M Y') }} &bull; {{ substr($item->waktu_mulai, 0, 5) }} WIB
                                    </div>
                                </td>

                                {{-- Estimasi Biaya --}}
                                <td class="py-3 px-4 font-medium text-slate-800 whitespace-nowrap">
                                    @if ($detail?->anggaran)
                                        <div>Rp {{ number_format($detail->anggaran, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">RKAP: Disetujui</div>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                {{-- Target Peserta --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">{{ $item->kuota_peserta ? $item->kuota_peserta . ' Peserta' : 'Tidak terbatas' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $item->peserta_count }} terdaftar</div>
                                </td>

                                {{-- Status --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $item->status_class }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.kegiatan.show', $item) }}"
                                           class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-[#7A0C14] hover:bg-rose-50 transition">
                                            {{ $item->status === 'menunggu_approval' ? 'Review' : 'Detail' }}
                                        </a>
                                        <a href="{{ route('admin.kegiatan.edit', $item) }}"
                                           class="px-2 py-1 rounded-md text-[11px] font-medium text-slate-600 hover:bg-slate-100 transition">
                                            Edit
                                        </a>
                                        <button type="button"
                                                @click="showDelete = true; deleteUrl = '{{ route('admin.kegiatan.destroy', $item) }}'; deleteName = '{{ addslashes($item->nama_kegiatan) }}'"
                                                class="px-2 py-1 rounded-md text-[11px] font-medium text-red-600 hover:bg-red-50 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">Belum ada kegiatan</p>
                                        <p class="text-xs text-slate-500">
                                            {{ request()->hasAny(['search', 'status', 'kategori']) ? 'Tidak ada kegiatan yang cocok dengan filter.' : 'Mulai dengan membuat kegiatan baru.' }}
                                        </p>
                                        <a href="{{ route('admin.kegiatan.create') }}"
                                           class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold rounded-lg transition">
                                            + Buat Kegiatan Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($kegiatan->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $kegiatan->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDelete" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
             @keydown.escape.window="showDelete = false">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-sm mx-4 p-6"
                 @click.away="showDelete = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Hapus Kegiatan?</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Data agenda dan relasi peserta juga akan terhapus.</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 mb-5 bg-slate-50 rounded-lg p-3 border border-slate-100">
                    Kegiatan: <strong x-text="deleteName" class="text-slate-800"></strong>
                </p>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="showDelete = false"
                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-xs font-semibold text-white bg-red-600 hover:bg-red-700 transition">
                            Hapus Kegiatan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
