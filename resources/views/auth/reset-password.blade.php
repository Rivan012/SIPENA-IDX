<x-layouts.guest title="Reset Kata Sandi — SIPENA IDX">
    <div class="space-y-6">
        {{-- Header Status Icon & Headings --}}
        <div>
            <div class="w-11 h-11 rounded-xl bg-[#FEECEC] text-[#7A0C14] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-[#0B192C] mt-4">Reset kata sandi</h1>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Buat kata sandi baru untuk akun Anda. Pilih kata sandi yang kuat dan belum pernah digunakan.</p>
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

        {{-- Reset Password Form --}}
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-800 mb-1.5">Kata sandi baru</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Masukkan kata sandi baru"
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

            {{-- Requirements Box --}}
            <div class="bg-[#F8F9FA] rounded-lg p-3.5 border border-slate-200/60 text-xs text-slate-600 space-y-1.5">
                <p class="font-semibold text-slate-700">Kata sandi harus memenuhi ketentuan berikut:</p>
                <ul class="space-y-1 text-slate-500 pl-3">
                    <li class="list-disc">Minimal 8 karakter.</li>
                    <li class="list-disc">Mengandung huruf besar dan huruf kecil.</li>
                    <li class="list-disc">Mengandung setidaknya satu angka dan satu simbol.</li>
                </ul>
            </div>

            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-800 mb-1.5">Konfirmasi kata sandi</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        placeholder="Ulangi kata sandi baru"
                        class="w-full h-11 pl-3.5 pr-10 rounded-lg border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7A0C14]/20 focus:border-[#7A0C14] transition"
                    >
                    <button
                        type="button"
                        onclick="const p = document.getElementById('password_confirmation'); p.type = p.type === 'password' ? 'text' : 'password';"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                        aria-label="Toggle password confirmation visibility"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Masukkan kembali kata sandi yang sama.</p>
            </div>

            {{-- Submit Button --}}
            <div class="pt-1">
                <button
                    type="submit"
                    class="w-full h-11 bg-[#7A0C14] hover:bg-[#600A10] text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center justify-center cursor-pointer"
                >
                    Simpan Kata Sandi
                </button>
            </div>

            <div class="pt-1 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#7A0C14] hover:text-[#5E080F] hover:underline">
                    <span>&larr;</span>
                    <span>Kembali ke Masuk</span>
                </a>
            </div>
        </form>

        {{-- Notice --}}
        <p class="text-xs text-slate-400 text-center leading-relaxed">
            Setelah kata sandi disimpan, gunakan kata sandi baru untuk masuk ke SIPENA.
        </p>
    </div>
</x-layouts.guest>
