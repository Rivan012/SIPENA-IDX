<x-layouts.admin title="Tambah Kegiatan — SIPENA IDX">
    <div class="space-y-5">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#7A0C14] transition">SIPENA</a>
            <span class="text-slate-300">&rsaquo;</span>
            <a href="{{ route('admin.kegiatan.index') }}" class="hover:text-[#7A0C14] transition">Kegiatan</a>
            <span class="text-slate-300">&rsaquo;</span>
            <span class="text-slate-700 font-medium">Tambah Kegiatan</span>
        </div>

        {{-- Page Header --}}
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Tambah Kegiatan Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi informasi kegiatan edukasi &amp; sosialisasi pasar modal</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.kegiatan.store') }}">
            @csrf
            @include('admin.kegiatan._form', [
                'rencanaKegiatan' => $rencanaKegiatan,
                'pengajuanKegiatan' => $pengajuanKegiatan,
            ])
        </form>
    </div>
</x-layouts.admin>
