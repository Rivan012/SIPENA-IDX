<x-layouts.admin title="Detail Approval Pengajuan — SIPENA IDX">
    <div class="space-y-5">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('admin.pengajuan.index') }}" class="hover:text-slate-900 transition">Approval &amp; Persetujuan</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-semibold">{{ $pengajuan->nomor_pengajuan }}</span>
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
                    <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                        {{ $pengajuan->nomor_pengajuan }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $pengajuan->status_class }}">
                        {{ $pengajuan->status_label }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                        {{ $pengajuan->jenis_kegiatan }}
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 mt-2">
                    {{ $pengajuan->nama_kegiatan }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Diajukan oleh {{ $pengajuan->admin?->name ?? 'Admin BEI' }} &bull; Tanggal Pengajuan: {{ $pengajuan->tanggal_pengajuan?->translatedFormat('d F Y') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pengajuan.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-lg transition shadow-xs">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>

        {{-- Main 2-Column Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Left Column: 7 Cols (Detail Usulan, Pos Anggaran, Deskripsi) --}}
            <div class="lg:col-span-7 space-y-4">
                {{-- Card: Alokasi Pagu Anggaran --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                Rp
                            </div>
                            <h2 class="text-sm font-bold text-slate-800">Usulan Biaya &amp; Pagu Anggaran</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                            Pagu RKAP
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between pt-1">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL ANGGARAN DIAJUKAN</div>
                            <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-1">
                                {{ $pengajuan->formatted_anggaran }}
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 text-right">
                            <span class="block text-[10px] text-slate-400 uppercase font-bold">STATUS ANGGARAN</span>
                            <span class="font-bold {{ $pengajuan->status === 'disetujui' ? 'text-emerald-700' : 'text-amber-600' }}">
                                {{ $pengajuan->status === 'disetujui' ? 'Telah Disetujui' : 'Menunggu Verifikasi' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card: Rincian Pelaksanaan Acara --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100">
                        Rincian Program &amp; Pelaksanaan
                    </h2>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Pelaksanaan Acara</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $rencana?->tanggal_rencana ? $rencana->tanggal_rencana->translatedFormat('l, d F Y') : '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Waktu Pelaksanaan</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $detail?->waktu_mulai ? substr($detail->waktu_mulai, 0, 5) : '09:00' }} -
                                {{ $detail?->waktu_selesai ? substr($detail->waktu_selesai, 0, 5) : '15:30' }} WIB
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tempat / Ruangan</dt>
                            <dd class="text-slate-800 font-semibold mt-1">
                                {{ $detail?->lokasi ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target Kuota Peserta</dt>
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

                    @if ($pengajuan->catatan)
                        <div class="pt-3 border-t border-slate-100 text-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan Pengusul</span>
                            <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $pengajuan->catatan }}</p>
                        </div>
                    @endif

                    @if ($pengajuan->catatan_revisi)
                        <div class="pt-3 border-t border-purple-100 bg-purple-50/50 p-3 rounded-lg text-xs">
                            <span class="text-[10px] font-bold text-purple-700 uppercase tracking-wider block mb-1">Catatan Catatan Revisi / Reviewer</span>
                            <p class="text-purple-900 leading-relaxed whitespace-pre-line">{{ $pengajuan->catatan_revisi }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: 5 Cols (Form Keputusan & Timeline) --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Form Keputusan Approval --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4" x-data="{ decision: '{{ $pengajuan->status === 'disetujui' ? 'disetujui' : ($pengajuan->status === 'ditolak' ? 'ditolak' : 'disetujui') }}' }">
                    <div class="pb-3 border-b border-slate-100">
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                            Keputusan Verifikasi Approval
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tentukan persetujuan anggaran dan kelayakan kegiatan.</p>
                    </div>

                    <form action="{{ route('admin.pengajuan.status', $pengajuan) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">Tindakan Persetujuan</label>
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
                                    <span class="text-[11px]">Revisi</span>
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

                        <div>
                            <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1">
                                <span x-text="decision === 'revisi' ? 'Catatan Arahan Revisi Pengajuan' : (decision === 'ditolak' ? 'Alasan Penolakan' : 'Catatan Persetujuan &amp; Catatan Anggaran')"></span>
                            </label>
                            <textarea name="catatan"
                                      id="catatan"
                                      rows="3"
                                      placeholder="Tuliskan catatan arahan pengajuan..."
                                      class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">{{ old('catatan', $pengajuan->catatan_revisi ?: $pengajuan->catatan) }}</textarea>
                        </div>

                        <div x-show="decision === 'disetujui'" class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 flex items-start gap-2.5">
                            <input type="checkbox"
                                   name="terbitkan_kegiatan"
                                   id="terbitkan_kegiatan"
                                   value="1"
                                   checked
                                   class="mt-0.5 rounded text-[#680A10] focus:ring-[#680A10]">
                            <label for="terbitkan_kegiatan" class="text-xs text-emerald-900 leading-snug">
                                <strong>Terbitkan ke Jadwal Kegiatan</strong>
                                <span class="block text-[11px] text-emerald-700 mt-0.5">Otomatis sinkron ke halaman Kegiatan &amp; Kalender.</span>
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 rounded-lg bg-[#680A10] hover:bg-[#54080D] text-xs font-semibold text-white transition shadow-xs">
                            Simpan Keputusan Approval
                        </button>
                    </form>
                </div>

                {{-- Card: Alur Tahapan & Timeline --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Timeline Pengajuan
                    </h2>

                    <div class="relative pl-5 space-y-4 text-xs before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        {{-- Step 1: Diajukan --}}
                        <div class="relative">
                            <span class="absolute -left-5 top-0.5 w-3 h-3 rounded-full bg-emerald-600 ring-4 ring-white"></span>
                            <div class="font-bold text-slate-900">1. Pengajuan Diajukan</div>
                            <div class="text-[11px] text-slate-500">
                                {{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->translatedFormat('d M Y') : '-' }} &bull; Oleh {{ $pengajuan->admin?->name ?? 'Admin BEI' }}
                            </div>
                        </div>

                        {{-- Step 2: Peninjauan --}}
                        <div class="relative">
                            <span class="absolute -left-5 top-0.5 w-3 h-3 rounded-full {{ in_array($pengajuan->status, ['diproses', 'disetujui', 'revisi', 'ditolak']) ? 'bg-emerald-600' : 'bg-slate-300' }} ring-4 ring-white"></span>
                            <div class="font-bold {{ in_array($pengajuan->status, ['diproses', 'disetujui', 'revisi', 'ditolak']) ? 'text-slate-900' : 'text-slate-400' }}">
                                2. Peninjauan Tim Anggaran
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $pengajuan->diproses_at ? $pengajuan->diproses_at->translatedFormat('d M Y, H:i') : 'Menunggu review' }}
                            </div>
                        </div>

                        {{-- Step 3: Keputusan --}}
                        <div class="relative">
                            <span class="absolute -left-5 top-0.5 w-3 h-3 rounded-full {{ $pengajuan->status === 'disetujui' ? 'bg-emerald-600' : ($pengajuan->status === 'ditolak' ? 'bg-rose-600' : ($pengajuan->status === 'revisi' ? 'bg-purple-600' : 'bg-slate-300')) }} ring-4 ring-white"></span>
                            <div class="font-bold {{ in_array($pengajuan->status, ['disetujui', 'ditolak', 'revisi']) ? 'text-slate-900' : 'text-slate-400' }}">
                                3. Keputusan Final
                            </div>
                            <div class="text-[11px] text-slate-500">
                                Status saat ini: <strong class="capitalize">{{ $pengajuan->status_label }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Entitas Terkait --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Dokumen Terkait
                    </h2>

                    <div class="space-y-2 text-xs">
                        @if ($rencana)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">RENCANA ANGGARAN</div>
                                    <div class="font-semibold text-slate-800">{{ $rencana->kode }}</div>
                                </div>
                                <a href="{{ route('admin.rencana-kegiatan.show', $rencana) }}" class="text-[#680A10] font-bold text-[11px] hover:underline">
                                    Buka &rarr;
                                </a>
                            </div>
                        @endif

                        @if ($kegiatan)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">JADWAL KEGIATAN TERBIT</div>
                                    <div class="font-semibold text-slate-800">{{ $kegiatan->nama_kegiatan }}</div>
                                </div>
                                <a href="{{ route('admin.kegiatan.show', $kegiatan) }}" class="text-[#680A10] font-bold text-[11px] hover:underline">
                                    Lihat &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
