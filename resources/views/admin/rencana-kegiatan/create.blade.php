<x-layouts.admin title="Tambah Rencana Anggaran — SIPENA IDX">
    <div class="space-y-5 max-w-4xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('admin.rencana-kegiatan.index') }}" class="hover:text-slate-900 transition">Anggaran &amp; RKAP</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-semibold">Buat Rencana Anggaran Baru</span>
        </div>

        {{-- Page Header --}}
        <div class="border-b border-slate-200 pb-4">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Buat Rencana Anggaran Baru
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Susun usulan pos kegiatan edukasi, estimasi biaya anggaran, dan jadwal pelaksanaan kerja tahunan.
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.rencana-kegiatan.store') }}">
            @csrf
            @include('admin.rencana-kegiatan._form', [
                'rencana' => $rencana,
                'detail' => $detail,
                'categories' => $categories,
            ])
        </form>
    </div>
</x-layouts.admin>
