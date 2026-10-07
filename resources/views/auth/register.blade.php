<x-layouts.guest title="Daftar Akun — SIPENA-IDX">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight text-center mb-2">Daftar Akun</h1>
    <p class="text-sm text-slate-500 text-center mb-6">Registrasi untuk mengikuti kegiatan edukasi IDX</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label for="no_telepon" class="block text-sm font-medium text-slate-700 mb-1">No. Telepon / WhatsApp</label>
            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow">
            Daftar
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Masuk di sini</a>
    </div>
</x-layouts.guest>
