<x-layouts.admin title="Detail Kegiatan — SIPENA IDX">
    <div class="space-y-5">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#7A0C14] transition">SIPENA</a>
            <span class="text-slate-300">&rsaquo;</span>
            <a href="{{ route('admin.kegiatan.index') }}" class="hover:text-[#7A0C14] transition">Kegiatan</a>
            <span class="text-slate-300">&rsaquo;</span>
            <span class="text-slate-700 font-medium">Detail Kegiatan</span>
        </div>

        {{-- Header with Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $kegiatan->kode }}</span>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $kegiatan->nama_kegiatan }}</h1>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $kegiatan->status_class }}">
                        {{ $kegiatan->status_label }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Dibuat {{ $kegiatan->created_at->diffForHumans() }} &bull; Diperbarui {{ $kegiatan->updated_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit Kegiatan</span>
                </a>
                <a href="{{ route('admin.kegiatan.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 rounded-lg text-xs font-medium text-slate-700 transition">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Left: Detail Info --}}
            <div class="lg:col-span-2 space-y-5">
                {{-- Info Card --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800">Informasi Utama</h2>
                    </div>
                    <div class="p-5">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori / Jenis</dt>
                                <dd class="mt-1 text-slate-800 font-semibold">{{ $kegiatan->jenis_kegiatan }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lokasi / Platform</dt>
                                <dd class="mt-1 text-slate-800 font-medium">{{ $kegiatan->lokasi }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Pelaksanaan</dt>
                                <dd class="mt-1 text-slate-800 font-medium">{{ $kegiatan->tanggal->format('l, d F Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Waktu Pelaksanaan</dt>
                                <dd class="mt-1 text-slate-800 font-medium">
                                    {{ substr($kegiatan->waktu_mulai, 0, 5) }}
                                    @if($kegiatan->waktu_selesai)
                                        — {{ substr($kegiatan->waktu_selesai, 0, 5) }}
                                    @endif
                                    WIB
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target Peserta</dt>
                                <dd class="mt-1 text-slate-800 font-medium">{{ $kegiatan->kuota_peserta ? $kegiatan->kuota_peserta . ' Peserta' : 'Tidak terbatas' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Realisasi Peserta</dt>
                                <dd class="mt-1 text-slate-800 font-semibold">{{ $kegiatan->peserta->count() }} orang terdaftar</dd>
                            </div>
                        </dl>

                        <div class="mt-5 pt-4 border-t border-slate-100">
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Lengkap</dt>
                            <dd class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $kegiatan->deskripsi }}</dd>
                        </div>
                    </div>
                </div>

                {{-- Agenda Kegiatan --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-800">Susunan Agenda</h2>
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                                {{ $kegiatan->agenda->count() }} Item
                            </span>
                        </div>
                        <a href="{{ route('admin.kegiatan.agenda.create', $kegiatan) }}"
                           class="text-[11px] font-semibold text-[#7A0C14] hover:underline">
                            + Tambah Agenda
                        </a>
                    </div>
                    <div class="p-5">
                        @forelse($kegiatan->agenda as $ag)
                            <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                                <div class="w-6 h-6 rounded-md bg-rose-50 text-[#680A10] flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="text-[10px] font-bold">{{ $loop->iteration }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-semibold text-slate-800">{{ $ag->judul }}</p>
                                        @if($ag->is_h_minus_1)
                                            <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-bold text-[9px]">H-1</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $ag->tanggal->format('d M Y') }} &bull; {{ substr($ag->waktu_mulai, 0, 5) }}@if($ag->waktu_selesai) - {{ substr($ag->waktu_selesai, 0, 5) }}@endif WIB
                                        @if($ag->lokasi) &bull; {{ $ag->lokasi }}@endif
                                    </div>
                                    @if($ag->keterangan)
                                        <p class="text-[11px] text-slate-600 mt-1">{{ $ag->keterangan }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-500">
                                Belum ada susunan agenda untuk kegiatan ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Peserta Terdaftar --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-800">Daftar Peserta</h2>
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                                {{ $kegiatan->peserta->count() }} Peserta
                            </span>
                        </div>
                        <a href="{{ route('admin.kegiatan.peserta.index', $kegiatan) }}"
                           class="text-[11px] font-semibold text-[#7A0C14] hover:underline">
                            Kelola Peserta &rarr;
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/30">
                                    <th class="py-2.5 px-5">Nama</th>
                                    <th class="py-2.5 px-5">Email</th>
                                    <th class="py-2.5 px-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($kegiatan->peserta->take(5) as $p)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="py-2.5 px-5 font-medium text-slate-800">{{ $p->user->name ?? '-' }}</td>
                                        <td class="py-2.5 px-5 text-slate-600">{{ $p->user->email ?? '-' }}</td>
                                        <td class="py-2.5 px-5">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 capitalize">
                                                {{ $p->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-xs text-slate-500">Belum ada peserta terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="space-y-5">
                {{-- Relasi & Anggaran --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800">Relasi &amp; Anggaran</h2>
                    </div>
                    <div class="p-5 space-y-4 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rencana Kegiatan</dt>
                            <dd class="mt-1 text-slate-800 font-medium">
                                @if($kegiatan->rencanaKegiatan)
                                    <a href="{{ route('admin.rencana-kegiatan.show', $kegiatan->rencanaKegiatan) }}" class="text-[#7A0C14] hover:underline font-semibold">
                                        {{ $kegiatan->rencanaKegiatan->nama_kegiatan }}
                                    </a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengajuan Anggaran</dt>
                            <dd class="mt-1 text-slate-800 font-medium">
                                @if($kegiatan->pengajuanKegiatan)
                                    <a href="{{ route('admin.pengajuan.show', $kegiatan->pengajuanKegiatan) }}" class="text-[#7A0C14] hover:underline font-semibold">
                                        {{ $kegiatan->pengajuanKegiatan->nomor_pengajuan ?? 'Pengajuan #' . $kegiatan->pengajuanKegiatan->id }}
                                    </a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </dd>
                        </div>
                        @if($detail = $kegiatan->rencanaKegiatan?->detailKegiatan)
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estimasi Anggaran RKAP</dt>
                            <dd class="mt-1 text-slate-900 font-bold text-sm">
                                Rp {{ number_format($detail->anggaran, 0, ',', '.') }}
                            </dd>
                        </div>
                        @endif
                        @if($kegiatan->laporanKegiatan)
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Laporan Pertanggungjawaban (LPJ)</dt>
                            <dd class="mt-1">
                                <a href="{{ route('admin.laporan-kegiatan.show', $kegiatan->laporanKegiatan) }}"
                                   class="text-[#7A0C14] font-semibold hover:underline inline-flex items-center gap-1">
                                    <span>Lihat Laporan Selesai</span>
                                    <span>&rarr;</span>
                                </a>
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800">Sistem &amp; Metadata</h2>
                    </div>
                    <div class="p-5 space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Kode Unik</span>
                            <span class="font-mono font-semibold text-slate-800">{{ $kegiatan->kode }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Database ID</span>
                            <span class="font-mono text-slate-600">#{{ $kegiatan->id }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Dibuat</span>
                            <span class="text-slate-700">{{ $kegiatan->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Diperbarui</span>
                            <span class="text-slate-700">{{ $kegiatan->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($kegiatan->google_calendar_event_id)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Google Calendar</span>
                            <span class="text-emerald-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Sinkron
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
