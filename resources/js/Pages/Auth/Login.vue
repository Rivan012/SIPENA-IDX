<script setup>
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { RefreshIcon } from '@/Icons';

const showPassword = ref(false);
const captchaUrl = ref(route('captcha', { action: 'login', t: Date.now() }));

const refreshCaptcha = () => {
    form.captcha = '';
    captchaUrl.value = route('captcha', { action: 'login', t: Date.now() });
};

const form = useForm({
    email: '',
    password: '',
    captcha: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            refreshCaptcha();
        },
    });
};
</script>

<template>
    <Head title="Masuk — SIPENA IDX Bengkulu" />

    <div class="relative min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased selection:bg-[#85131B] selection:text-white">
        <ThemeToggle class="absolute right-4 top-4" />

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-8 border border-slate-300 rounded-xl shadow-xs">
                <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
                    <!-- Brand Mark -->
                    <Link :href="route('home')" class="inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-xl p-1">
                        <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-16 w-auto mx-auto" />
                    </Link>
                </div>
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- General Error Alert -->
                    <div v-if="form.errors.email" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                        {{ form.errors.email }}
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-900 mb-1">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            placeholder="nama@email.com atau admin@bei.co.id"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-xs font-bold text-slate-900">
                                Kata Sandi
                            </label>
                            <Link :href="route('password.request')" class="text-xs font-semibold text-[#85131B] hover:text-[#680A10] hover:underline">
                                Lupa kata sandi?
                            </Link>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="Masukkan kata sandi akun Anda"
                                class="w-full px-3.5 py-2.5 pr-10 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer focus:outline-none"
                                :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                            >
                                <svg v-if="showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="mt-1 text-xs text-red-600">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- CAPTCHA Field -->
                    <div>
                        <label for="captcha" class="block text-xs font-bold text-slate-900 mb-1">
                            Kode Keamanan (CAPTCHA)
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="shrink-0 overflow-hidden rounded-lg border border-slate-300 bg-slate-50">
                                <img :src="captchaUrl" alt="CAPTCHA" class="h-10 w-[140px] block" />
                            </div>
                            <button
                                type="button"
                                @click="refreshCaptcha"
                                title="Muat ulang kode CAPTCHA"
                                class="p-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-600 transition cursor-pointer"
                            >
                                <RefreshIcon class="h-4 w-4" />
                            </button>
                            <input
                                id="captcha"
                                v-model="form.captcha"
                                type="text"
                                required
                                maxlength="6"
                                placeholder="Ketik kode"
                                class="w-full px-3 py-2.5 text-xs font-mono font-bold uppercase rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                            />
                        </div>
                        <div v-if="form.errors.captcha" class="mt-1 text-xs text-red-600">
                            {{ form.errors.captcha }}
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center text-xs">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-700">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="rounded border-slate-300 text-[#85131B] focus:ring-[#85131B]"
                            />
                            <span>Ingat sesi masuk saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-1">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 bg-[#85131B] hover:bg-[#680A10] text-white font-bold text-xs rounded-lg transition-colors shadow-xs disabled:opacity-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#85131B]"
                        >
                            {{ form.processing ? 'Memverifikasi kredensial...' : 'Masuk ke Sistem' }}
                        </button>
                    </div>

                    <!-- Footer Info -->
                    <div class="pt-4 border-t border-slate-200 text-center space-y-2 text-xs text-slate-600">
                        <div>
                            Belum terdaftar sebagai mitra?
                            <Link :href="route('register')" class="text-[#85131B] font-bold hover:underline">
                                Daftar Mitra
                            </Link>
                        </div>
                        <div>
                            <Link :href="route('home')" class="text-slate-500 hover:text-slate-800">
                                Kembali ke Halaman Utama
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
