<x-layouts.guest title="Masuk Admin — SIPENA-IDX">
    <div class="text-center mb-6">
        <span class="inline-block px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-slate-800 bg-slate-200 rounded-md mb-2">Internal IDX</span>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Portal Admin</h1>
        <p class="text-sm text-slate-500 mt-1">Masuk untuk mengelola kegiatan dan pengajuan</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Admin</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-700 text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-700 text-sm">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center space-x-2 text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                <span>Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-sm shadow">
            Masuk Portal Admin
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
        Kembali ke <a href="{{ route('login') }}" class="text-slate-700 hover:underline font-medium">Halaman Utama Pengguna</a>
    </div>
</x-layouts.guest>
