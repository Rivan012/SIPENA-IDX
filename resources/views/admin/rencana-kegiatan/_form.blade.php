{{-- Form Partial: Rencana Kegiatan & Anggaran --}}
@props(['rencana' => null, 'detail' => null, 'categories' => ['SPM', 'Sosialisasi', 'Workshop', 'Edukasi', 'Webinar']])

@php
    $isEdit = !is_null($rencana) && $rencana->exists;
    $detailData = $detail ?? $rencana?->detailKegiatan;
@endphp

<div class="space-y-6" x-data="{
    anggaranInput: '{{ old('anggaran', $detailData?->anggaran ?? 10000000) }}',
    formatRupiah(val) {
        let num = parseInt(val) || 0;
        return 'Rp ' + num.toLocaleString('id-ID');
    }
}">
    {{-- Validation Error Alerts --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-1.5 text-red-900">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Mohon periksa kembali input formulir berikut:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5 text-red-700">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SECTION 1: IDENTITAS & RENCANA ACARA --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-rose-50 text-[#680A10] flex items-center justify-center font-bold text-xs">
                    1
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Identitas Rencana Kegiatan</h3>
                    <p class="text-[11px] text-slate-500">Nama program kerja dan klasifikasi edukasi pasar modal.</p>
                </div>
            </div>
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Langkah 1 dari 2</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Nama Kegiatan --}}
            <div class="md:col-span-2">
                <label for="nama_kegiatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Kegiatan / Program Kerja <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="nama_kegiatan"
                       id="nama_kegiatan"
                       value="{{ old('nama_kegiatan', $rencana?->nama_kegiatan) }}"
                       placeholder="Contoh: Sekolah Pasar Modal (SPM) Level 1 &amp; 2 - GI BEI UNIB"
                       required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">
            </div>

            {{-- Jenis Kegiatan --}}
            <div>
                <label for="jenis_kegiatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jenis Kegiatan <span class="text-red-500">*</span>
                </label>
                <select name="jenis_kegiatan"
                        id="jenis_kegiatan"
                        required
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition bg-white">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ old('jenis_kegiatan', $rencana?->jenis_kegiatan) == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Tanggal Rencana --}}
            <div>
                <label for="tanggal_rencana" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       name="tanggal_rencana"
                       id="tanggal_rencana"
                       value="{{ old('tanggal_rencana', $rencana?->tanggal_rencana ? $rencana->tanggal_rencana->format('Y-m-d') : '') }}"
                       required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">
            </div>

            {{-- Waktu Mulai --}}
            <div>
                <label for="waktu_mulai" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Waktu Mulai <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="waktu_mulai"
                       id="waktu_mulai"
                       value="{{ old('waktu_mulai', $detailData?->waktu_mulai ? substr($detailData->waktu_mulai, 0, 5) : '09:00') }}"
                       placeholder="09:00"
                       required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">
            </div>

            {{-- Waktu Selesai --}}
            <div>
                <label for="waktu_selesai" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Waktu Selesai
                </label>
                <input type="text"
                       name="waktu_selesai"
                       id="waktu_selesai"
                       value="{{ old('waktu_selesai', $detailData?->waktu_selesai ? substr($detailData->waktu_selesai, 0, 5) : '15:30') }}"
                       placeholder="15:30"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">
            </div>

            {{-- Target Peserta --}}
            <div>
                <label for="target_peserta" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Target Peserta (Orang)
                </label>
                <input type="number"
                       name="target_peserta"
                       id="target_peserta"
                       value="{{ old('target_peserta', $detailData?->target_peserta ?? 100) }}"
                       min="1"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">
            </div>
        </div>

        {{-- Lokasi / Tempat --}}
        <div>
            <label for="lokasi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Lokasi / Tempat Pelaksanaan <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="lokasi"
                   id="lokasi"
                   value="{{ old('lokasi', $detailData?->lokasi ?? 'Gedung B Ruang Seminar GI UNIB Bengkulu') }}"
                   placeholder="Contoh: Gedung B Ruang Seminar GI UNIB Bengkulu / Zoom Webinar"
                   required
                   class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">
        </div>
    </div>

    {{-- SECTION 2: POS ANGGARAN & JUSTIFIKASI --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#0B63E5] flex items-center justify-center font-bold text-xs">
                    2
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Alokasi Anggaran &amp; Keterangan</h3>
                    <p class="text-[11px] text-slate-500">Nominal belanja anggaran yang dibebankan ke RKAP serta catatan rencana.</p>
                </div>
            </div>
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Langkah 2 dari 2</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Nominal Anggaran --}}
            <div class="space-y-1.5">
                <label for="anggaran" class="block text-xs font-semibold text-slate-700">
                    Alokasi Pagu Anggaran (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number"
                           name="anggaran"
                           id="anggaran"
                           x-model="anggaranInput"
                           step="1000"
                           min="0"
                           required
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] font-mono font-bold text-slate-900 transition">
                </div>
                <div class="text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Terbilang Format:</span>
                    <span class="font-bold text-emerald-700 font-mono" x-text="formatRupiah(anggaranInput)"></span>
                </div>
            </div>

            {{-- Status Rencana --}}
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Status Rencana
                </label>
                <select name="status"
                        id="status"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition bg-white">
                    <option value="draft" {{ old('status', $rencana?->status ?? 'draft') == 'draft' ? 'selected' : '' }}>
                        Simpan sebagai Draft Rencana
                    </option>
                    <option value="diajukan" {{ old('status', $rencana?->status) == 'diajukan' ? 'selected' : '' }}>
                        Langsung Ajukan ke Review Anggaran
                    </option>
                    @if ($isEdit)
                        <option value="disetujui" {{ old('status', $rencana?->status) == 'disetujui' ? 'selected' : '' }}>
                            Disetujui
                        </option>
                        <option value="ditolak" {{ old('status', $rencana?->status) == 'ditolak' ? 'selected' : '' }}>
                            Ditolak
                        </option>
                    @endif
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilih "Langsung Ajukan" agar otomatis masuk ke modul Approval.</p>
            </div>
        </div>

        {{-- Deskripsi Kegiatan --}}
        <div>
            <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Deskripsi / Rincian Program Kegiatan
            </label>
            <textarea name="deskripsi"
                      id="deskripsi"
                      rows="3"
                      placeholder="Jelaskan tujuan, materi, atau pos belanja pelaksanaan program..."
                      class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">{{ old('deskripsi', $detailData?->deskripsi) }}</textarea>
        </div>

        {{-- Keterangan / Catatan Tambahan --}}
        <div>
            <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Catatan Rencana Kerja / Justifikasi RKAP
            </label>
            <textarea name="keterangan"
                      id="keterangan"
                      rows="2"
                      placeholder="Contoh: Rencana kegiatan edukasi pasar modal TA 2025 bersama Galeri Investasi BEI UNIB."
                      class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 focus:border-[#680A10] transition">{{ old('keterangan', $rencana?->keterangan) }}</textarea>
        </div>
    </div>

    {{-- Form Submit Actions --}}
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('admin.rencana-kegiatan.index') }}"
           class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
            &larr; Kembali ke Daftar Anggaran
        </a>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs transition shadow-xs">
                {{ $isEdit ? 'Simpan Perubahan Anggaran' : 'Simpan Rencana Anggaran' }}
            </button>
        </div>
    </div>
</div>
