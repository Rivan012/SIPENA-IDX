<x-layouts.guest title="Lupa Kata Sandi — SIPENA IDX">
    <div class="space-y-6">
        {{-- Header Status Icon & Headings --}}
        <div>
            <div class="w-11 h-11 rounded-xl bg-[#FEECEC] text-[#7A0C14] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-[#0B192C] mt-4">Lupa kata sandi?</h1>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Masukkan email akun Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>
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

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

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

            <div class="pt-1">
                <button
                    type="submit"
                    class="w-full h-11 bg-[#7A0C14] hover:bg-[#600A10] text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center justify-center cursor-pointer"
                >
                    Kirim Tautan Reset
                </button>
            </div>

            <div class="pt-1 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#7A0C14] hover:text-[#5E080F] hover:underline">
                    <span>&larr;</span>
                    <span>Kembali ke Masuk</span>
                </a>
            </div>
        </form>

        {{-- Security Note --}}
        <p class="text-xs text-slate-400 text-center leading-relaxed px-2">
            Jika email tersebut terhubung dengan akun SIPENA, tautan pemulihan akan dikirim. Demi keamanan, status pendaftaran email tidak ditampilkan.
        </p>

        {{-- Notice Callout: Belum menerima email --}}
        <div class="bg-[#F8F9FA] rounded-xl p-4 sm:p-5 border border-slate-200/60">
            <h2 class="text-sm font-bold text-[#0B192C]">Belum menerima email?</h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Periksa folder spam atau junk. Jika masih membutuhkan bantuan, hubungi admin SIPENA di unit Anda.
            </p>
        </div>
    </div>
</x-layouts.guest>
