<x-layouts.admin title="Manajemen Pengguna & Monitoring — SIPENA IDX">
    <div class="space-y-5" x-data="{
        activeTab: 'users',
        showHistoryModal: false,
        modalUserName: '',
        modalHistory: [],
        openHistory(name, history) {
            this.modalUserName = name;
            this.modalHistory = history;
            this.showHistoryModal = true;
        }
    }">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider" aria-label="Breadcrumb">
                    <span>SIPENA</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span>DATA PENGGUNA</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-700">PENGGUNA &amp; MONITORING PESERTA</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                    Manajemen Pengguna &amp; Peserta
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 max-w-2xl leading-relaxed">
                    Database peserta terdaftar sosialisasi pasar modal, data administrator KP BEI Bengkulu, serta monitoring riwayat kehadiran kegiatan.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.monitoring.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Data Pengguna (CSV)</span>
                </a>
            </div>
        </div>

        {{-- 4 KPI Metric Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- Card 1: Total Pengguna / Peserta --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PESERTA TERDAFTAR</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700">Akun Publik</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2 font-mono">
                        {{ number_format($stats['total_users'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Mahasiswa &amp; Umum</span>
                    <span class="font-bold text-blue-700">Aktif</span>
                </div>
            </div>

            {{-- Card 2: Administrator BEI --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">ADMINISTRATOR BEI</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-700">Internal</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2 font-mono">
                        {{ $stats['total_admins'] }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Pengelola Sistem</span>
                    <span class="font-bold text-slate-700">Pusat &amp; KP</span>
                </div>
            </div>

            {{-- Card 3: Total Partisipasi --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL PARTISIPASI</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700">Registrasi Acara</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2 font-mono">
                        {{ number_format($stats['total_partisipasi'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Tiket / Pendaftaran</span>
                    <span class="font-bold text-amber-600">Terdata</span>
                </div>
            </div>

            {{-- Card 4: Kehadiran Hadir --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">KEHADIRAN HADIR</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">Presensi</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-700 mt-2 font-mono">
                        {{ number_format($stats['total_hadir'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Telah Mengikuti Acara</span>
                    <span class="font-bold text-emerald-700">Verified</span>
                </div>
            </div>
        </div>

        {{-- Controls Bar & Tabs --}}
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            {{-- Tabs --}}
            <div class="inline-flex p-1 bg-slate-100 rounded-lg text-xs font-semibold text-slate-600">
                <button type="button"
                        @click="activeTab = 'users'"
                        :class="activeTab === 'users' ? 'bg-[#680A10] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 rounded-md transition text-xs font-semibold">
                    Peserta / Pengguna ({{ $stats['total_users'] }})
                </button>
                <button type="button"
                        @click="activeTab = 'admins'"
                        :class="activeTab === 'admins' ? 'bg-[#680A10] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 rounded-md transition text-xs font-semibold">
                    Administrator ({{ $stats['total_admins'] }})
                </button>
            </div>

            {{-- Search Box --}}
            <form method="GET" action="{{ route('admin.monitoring') }}" class="relative w-full sm:w-72">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, email, nomor HP..."
                       class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10]">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
        </div>

        {{-- TAB 1: USERS (PESERTA) --}}
        <div x-show="activeTab === 'users'" class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">PENGGUNA / PESERTA</th>
                            <th class="py-3 px-4">KONTAK (EMAIL &amp; HP)</th>
                            <th class="py-3 px-4 text-center">TOTAL KEIKUTSERTAAN</th>
                            <th class="py-3 px-4">KEGIATAN TERAKHIR</th>
                            <th class="py-3 px-4">TANGGAL REGISTRASI</th>
                            <th class="py-3 px-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            @php
                                $lastKeg = $u->kegiatan->sortByDesc('pivot.created_at')->first();
                                $historyList = $u->kegiatan->map(fn($k) => [
                                    'nama' => $k->nama_kegiatan,
                                    'tanggal' => $k->tanggal ? $k->tanggal->format('d/m/Y') : '-',
                                    'status' => $k->pivot->status ?? 'terdaftar',
                                    'lokasi' => $k->lokasi ?? '-',
                                ])->values();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                {{-- User Name & Avatar --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                            {{ substr($u->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                            <span class="text-[10px] text-slate-400">ID: #USR-{{ sprintf('%04d', $u->id) }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kontak --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-medium text-slate-800">{{ $u->email }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $u->no_telepon ?: '-' }}</div>
                                </td>

                                {{-- Total Keikutsertaan --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $u->kegiatan->count() > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $u->kegiatan->count() }} Kegiatan
                                    </span>
                                </td>

                                {{-- Kegiatan Terakhir --}}
                                <td class="py-3.5 px-4 max-w-xs">
                                    @if ($lastKeg)
                                        <div class="font-semibold text-slate-800 truncate" title="{{ $lastKeg->nama_kegiatan }}">
                                            {{ $lastKeg->nama_kegiatan }}
                                        </div>
                                        <span class="text-[10px] text-emerald-600 font-medium">Status: {{ ucfirst($lastKeg->pivot->status ?? 'terdaftar') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum pernah mendaftar</span>
                                    @endif
                                </td>

                                {{-- Tanggal Registrasi --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $u->created_at?->translatedFormat('d M Y') ?? '-' }}
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="openHistory('{{ addslashes($u->name) }}', {{ Js::from($historyList) }})"
                                            class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-[#680A10] text-xs font-semibold transition shadow-xs">
                                        Riwayat Partisipasi &rarr;
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    Belum ada data peserta yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-3.5 border-t border-slate-200 bg-slate-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- TAB 2: ADMINS (PENGELOLA) --}}
        <div x-show="activeTab === 'admins'" x-cloak class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">NAMA LENGKAP</th>
                            <th class="py-3 px-4">EMAIL AKUN</th>
                            <th class="py-3 px-4">JABATAN / PERAN</th>
                            <th class="py-3 px-4">NO. TELEPON</th>
                            <th class="py-3 px-4">TANGGAL DIBUAT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($admins as $adm)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-[#680A10] text-white flex items-center justify-center font-bold text-xs uppercase shrink-0 shadow-xs">
                                            {{ substr($adm->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $adm->name }}</div>
                                            <span class="text-[10px] text-slate-400 font-mono">ADM-{{ sprintf('%03d', $adm->id) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-slate-800">
                                    {{ $adm->email }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                        {{ $adm->jabatan ?: 'Admin BEI' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $adm->no_telepon ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $adm->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400">
                                    Tidak ada data admin ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Riwayat Keikutsertaan Peserta (Alpine.js) --}}
        <div x-show="showHistoryModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showHistoryModal = false"
                 class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0B63E5] flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Riwayat Partisipasi Kegiatan</h3>
                            <p class="text-[11px] text-slate-500" x-text="'Peserta: ' + modalUserName"></p>
                        </div>
                    </div>
                    <button type="button" @click="showHistoryModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                {{-- History List --}}
                <div class="max-h-72 overflow-y-auto space-y-2 pr-1">
                    <template x-if="modalHistory.length === 0">
                        <p class="text-xs text-slate-400 text-center py-6">Peserta ini belum pernah mendaftar ke kegiatan manapun.</p>
                    </template>

                    <template x-for="(item, idx) in modalHistory" :key="idx">
                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 flex items-center justify-between gap-3 text-xs">
                            <div>
                                <h4 class="font-bold text-slate-900" x-text="item.nama"></h4>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <span x-text="item.tanggal"></span> &bull; <span x-text="item.lokasi"></span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                  :class="item.status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'"
                                  x-text="item.status">
                            </span>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="button"
                            @click="showHistoryModal = false"
                            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
