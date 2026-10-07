<x-layouts.admin title="Edit Rencana Anggaran — SIPENA IDX">
    <div class="space-y-5 max-w-4xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('admin.rencana-kegiatan.index') }}" class="hover:text-slate-900 transition">Anggaran &amp; RKAP</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-semibold">Edit Rencana {{ $rencana->kode }}</span>
        </div>

        {{-- Page Header --}}
        <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Edit Rencana Anggaran
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Perbarui rincian usulan kegiatan, pos belanja, atau jadwal pelaksanaan program kerja.
                </p>
            </div>
            <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-slate-100 font-bold text-slate-700">
                {{ $rencana->kode }}
            </span>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.rencana-kegiatan.update', $rencana) }}">
            @csrf
            @method('PUT')
            @include('admin.rencana-kegiatan._form', [
                'rencana' => $rencana,
                'detail' => $detail,
                'categories' => $categories,
            ])
        </form>
    </div>
</x-layouts.admin>
