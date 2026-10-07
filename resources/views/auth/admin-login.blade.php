<x-layouts.guest title="Masuk Admin — SIPENA IDX">
    <div class="space-y-6">
        {{-- Header Status Icon & Headings --}}
        <div>
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-xl bg-[#FEECEC] text-[#7A0C14] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900 text-white tracking-wide uppercase">
                    Admin Workspace
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#0B192C] mt-3.5">Masuk Portal Admin</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">Akses khusus tim manajemen dan petugas Bursa Efek Indonesia.</p>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="p-3.5 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf

            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-800 mb-1.5">Email Admin</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="admin@idx-bengkulu.id"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14] transition"
                >
                <p class="text-[11px] text-slate-400 mt-1">Gunakan email internal resmi BEI.</p>
            </div>

            {{-- Password Field --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-800 mb-1.5">Kata sandi</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Masukkan kata sandi admin"
                        class="w-full pl-3.5 pr-10 py-2.5 rounded-lg border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14] transition"
                    >
                    <button
                        type="button"
                        onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                        aria-label="Toggle password visibility"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between pt-0.5">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="w-4 h-4 rounded border-slate-300 text-[#7A0C14] focus:ring-[#7A0C14] focus:ring-offset-0"
                    >
                    <span class="text-xs sm:text-sm text-slate-600">Ingat Sesi Ini</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-1">
                <button
                    type="submit"
                    class="w-full bg-[#7A0C14] hover:bg-[#600A10] text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition text-sm cursor-pointer"
                >
                    Masuk Portal Admin
                </button>
            </div>
        </form>

        <div class="pt-2 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#7A0C14] hover:underline">
                <span>&larr;</span>
                <span>Masuk sebagai Pengguna Biasa</span>
            </a>
        </div>
    </div>
</x-layouts.guest>
