{{-- Kegiatan Form Partial: used by create & edit --}}
@props(['kegiatan' => null, 'rencanaKegiatan' => collect(), 'pengajuanKegiatan' => collect()])

@php $isEdit = !is_null($kegiatan) && $kegiatan->exists; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Left: Main Form --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Informasi Kegiatan --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800">Informasi Kegiatan</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Data utama kegiatan edukasi &amp; sosialisasi</p>
            </div>
            <div class="p-5 space-y-4">
                {{-- Nama Kegiatan --}}
                <div>
                    <label for="nama_kegiatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan"
                           value="{{ old('nama_kegiatan', $kegiatan?->nama_kegiatan) }}"
                           placeholder="Contoh: Sekolah Pasar Modal Level 1"
                           class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('nama_kegiatan') border-red-300 ring-1 ring-red-200 @enderror">
                    @error('nama_kegiatan')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                              placeholder="Deskripsikan kegiatan secara lengkap..."
                              class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition resize-y @error('deskripsi') border-red-300 ring-1 ring-red-200 @enderror">{{ old('deskripsi', $kegiatan?->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Row: Jenis + Lokasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="jenis_kegiatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Kegiatan / Kategori <span class="text-red-500">*</span></label>
                        <select name="jenis_kegiatan" id="jenis_kegiatan"
                                class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('jenis_kegiatan') border-red-300 ring-1 ring-red-200 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach(['SPM', 'Sosialisasi', 'Workshop', 'Seminar', 'Webinar', 'Edukasi'] as $jenis)
                                <option value="{{ $jenis }}" {{ strtolower(old('jenis_kegiatan', $kegiatan?->jenis_kegiatan)) == strtolower($jenis) ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_kegiatan')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="lokasi" class="block text-xs font-semibold text-slate-700 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" id="lokasi"
                               value="{{ old('lokasi', $kegiatan?->lokasi) }}"
                               placeholder="Contoh: GI BEI Univ. Riau / Hybrid"
                               class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('lokasi') border-red-300 ring-1 ring-red-200 @enderror">
                        @error('lokasi')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Row: Tanggal + Waktu Mulai + Waktu Selesai --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="tanggal" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal"
                               value="{{ old('tanggal', $kegiatan?->tanggal ? $kegiatan->tanggal->format('Y-m-d') : '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('tanggal') border-red-300 ring-1 ring-red-200 @enderror">
                        @error('tanggal')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="waktu_mulai" class="block text-xs font-semibold text-slate-700 mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai"
                               value="{{ old('waktu_mulai', $kegiatan?->waktu_mulai ? substr($kegiatan->waktu_mulai, 0, 5) : '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('waktu_mulai') border-red-300 ring-1 ring-red-200 @enderror">
                        @error('waktu_mulai')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-xs font-semibold text-slate-700 mb-1.5">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai"
                               value="{{ old('waktu_selesai', $kegiatan?->waktu_selesai ? substr($kegiatan->waktu_selesai, 0, 5) : '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('waktu_selesai') border-red-300 ring-1 ring-red-200 @enderror">
                        @error('waktu_selesai')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Kuota Peserta --}}
                <div class="max-w-xs">
                    <label for="kuota_peserta" class="block text-xs font-semibold text-slate-700 mb-1.5">Target Peserta</label>
                    <input type="number" name="kuota_peserta" id="kuota_peserta" min="1"
                           value="{{ old('kuota_peserta', $kegiatan?->kuota_peserta) }}"
                           placeholder="Kosongkan jika tidak dibatasi"
                           class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('kuota_peserta') border-red-300 ring-1 ring-red-200 @enderror">
                    @error('kuota_peserta')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">
        {{-- Status & Publish --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800">Status &amp; Publikasi</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status"
                            class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition @error('status') border-red-300 ring-1 ring-red-200 @enderror">
                        @foreach(\App\Models\Kegiatan::STATUS as $val => [$label])
                            <option value="{{ $val }}" {{ old('status', $kegiatan?->status ?? 'menunggu_approval') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs rounded-lg transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}</span>
                    </button>
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </a>
                </div>
            </div>
        </div>

        {{-- Relasi (Rencana & Pengajuan) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800">Relasi &amp; RKAP</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Kaitkan ke rencana atau pengajuan anggaran</p>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label for="rencana_kegiatan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Rencana Kegiatan</label>
                    <select name="rencana_kegiatan_id" id="rencana_kegiatan_id"
                            class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition">
                        <option value="">— Tidak ada —</option>
                        @foreach($rencanaKegiatan as $rk)
                            <option value="{{ $rk->id }}" {{ old('rencana_kegiatan_id', $kegiatan?->rencana_kegiatan_id) == $rk->id ? 'selected' : '' }}>
                                {{ $rk->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="pengajuan_kegiatan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Pengajuan Anggaran</label>
                    <select name="pengajuan_kegiatan_id" id="pengajuan_kegiatan_id"
                            class="w-full h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14]/30 transition">
                        <option value="">— Tidak ada —</option>
                        @foreach($pengajuanKegiatan as $pk)
                            <option value="{{ $pk->id }}" {{ old('pengajuan_kegiatan_id', $kegiatan?->pengajuan_kegiatan_id) == $pk->id ? 'selected' : '' }}>
                                {{ $pk->nomor_pengajuan }} — {{ $pk->rencanaKegiatan?->nama_kegiatan ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
