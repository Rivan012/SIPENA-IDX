<x-layouts.guest title="Masuk — SIPENA-IDX">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight text-center mb-2">Masuk Pengguna</h1>
    <p class="text-sm text-slate-500 text-center mb-6">Akses portal kegiatan edukasi IDX Bengkulu</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center space-x-2 text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                <span>Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow">
            Masuk
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-200 text-center text-xs text-slate-500 space-y-2">
        <div>Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Daftar sekarang</a></div>
        <div>Petugas IDX? <a href="{{ route('admin.login') }}" class="text-slate-700 hover:underline">Masuk sebagai Admin</a></div>
    </div>
</x-layouts.guest>
