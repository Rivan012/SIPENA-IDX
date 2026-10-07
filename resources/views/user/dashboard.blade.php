<x-layouts.user title="Dashboard — SIPENA-IDX">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Pengguna</h1>
            <p class="text-sm text-slate-500">Selamat datang di portal kegiatan edukasi IDX Bengkulu</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <p class="text-slate-600">Jelajahi kegiatan edukasi pasar modal dan daftarkan diri Anda.</p>
            <div class="mt-4">
                <a href="{{ route('user.kegiatan.index') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Lihat Daftar Kegiatan</a>
            </div>
        </div>
    </div>
</x-layouts.user>
