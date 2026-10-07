<x-layouts.guest title="Masuk ke SIPENA — IDX">
    <div class="space-y-6">
        {{-- Header Status Icon & Headings --}}
        <div>
            <div class="w-11 h-11 rounded-xl bg-[#FEECEC] text-[#7A0C14] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l4-4m0 0l-4-4m4 4H3m8-8h7a2 2 0 012 2v12a2 2 0 01-2 2h-7" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-[#0B192C] mt-4">Masuk ke SIPENA</h1>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Selamat datang kembali. Gunakan akun Anda untuk mengakses ruang kerja SIPENA.</p>
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

        {{-- Session Status --}}
        @if (session('status'))
            <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-800 mb-1.5">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="nama@idx.co.id"
                    class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14] transition"
                >
                <p class="text-[11px] text-slate-500 mt-1">Gunakan email yang terhubung dengan akun Anda.</p>
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
                        placeholder="Masukkan kata sandi"
                        class="w-full h-11 pl-3.5 pr-10 rounded-lg border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14] transition"
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

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="w-4 h-4 rounded border-slate-300 text-[#7A0C14] focus:ring-[#7A0C14] focus:ring-offset-0"
                    >
                    <span class="text-sm text-slate-600">Ingat Saya</span>
                </label>

                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#7A0C14] hover:text-[#5E080F] hover:underline">
                    Lupa Kata Sandi?
                </a>
            </div>

            {{-- Submit Button --}}
            <div class="pt-1">
                <button
                    type="submit"
                    class="w-full h-11 bg-[#7A0C14] hover:bg-[#600A10] text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center justify-center cursor-pointer"
                >
                    Masuk
                </button>
            </div>
        </form>

        {{-- Notice Callout: Belum memiliki akun --}}
        <div class="bg-[#F8F9FA] rounded-xl p-4 sm:p-5 border border-slate-200/60">
            <h2 class="text-sm font-bold text-[#0B192C]">Belum memiliki akun?</h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Hubungi admin SIPENA di unit Anda untuk mendapatkan akun dan akses. Tidak tersedia pendaftaran mandiri.
            </p>
        </div>
    </div>
</x-layouts.guest>
