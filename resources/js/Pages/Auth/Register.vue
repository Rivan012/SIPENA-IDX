<script setup>
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { RefreshIcon } from '@/Icons';

const captchaUrl = ref(route('captcha', { action: 'register', t: Date.now() }));

const refreshCaptcha = () => {
    form.captcha = '';
    captchaUrl.value = route('captcha', { action: 'register', t: Date.now() });
};

const form = useForm({
    name: '',
    email: '',
    no_telepon: '',
    password: '',
    password_confirmation: '',
    captcha: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('register.submit'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            refreshCaptcha();
        },
    });
};
</script>

<template>
    <Head title="Daftar Mitra — SIPENA IDX Bengkulu" />

    <div class="relative min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased selection:bg-[#85131B] selection:text-white">
        <ThemeToggle class="absolute right-4 top-4" />

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-8 border border-slate-300 rounded-xl shadow-xs">
                <!-- Brand Mark & Header -->
                <div class="text-center mb-6">
                    <Link :href="route('home')" class="inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-xl p-1 mb-3">
                        <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-16 w-auto mx-auto" />
                    </Link>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight">Daftar Akun Mitra</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Sistem Informasi Perencanaan & Pelaksanaan Edukasi Pasar Modal
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- General Error Alert -->
                    <div v-if="form.errors.email || form.errors.name || form.errors.password" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                        <span v-if="form.errors.email">{{ form.errors.email }}</span>
                        <span v-else-if="form.errors.password">{{ form.errors.password }}</span>
                        <span v-else-if="form.errors.name">{{ form.errors.name }}</span>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-900 mb-1">
                            Nama Lengkap
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            autofocus
                            placeholder="Nama sesuai KTP / Instansi"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-900 mb-1">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="nama@email.com"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
                    </div>

                    <!-- Nomor WhatsApp / Telepon -->
                    <div>
                        <label for="no_telepon" class="block text-xs font-bold text-slate-900 mb-1">
                            Nomor WhatsApp / Telepon
                        </label>
                        <input
                            id="no_telepon"
                            v-model="form.no_telepon"
                            type="tel"
                            placeholder="0812xxxxxxxx"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
                        <p v-if="form.errors.no_telepon" class="mt-1 text-xs text-red-600">{{ form.errors.no_telepon }}</p>
                    </div>

                    <!-- Password Field with Toggle -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-900 mb-1">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="Minimal 8 karakter"
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
                    </div>

                    <!-- Konfirmasi Password Field with Toggle -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-900 mb-1">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                required
                                placeholder="Ulangi kata sandi"
                                class="w-full px-3.5 py-2.5 pr-10 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                            />
                            <button
                                type="button"
                                @click="showPasswordConfirmation = !showPasswordConfirmation"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer focus:outline-none"
                                :title="showPasswordConfirmation ? 'Sembunyikan password' : 'Lihat password'"
                            >
                                <svg v-if="showPasswordConfirmation" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
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

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 bg-[#85131B] hover:bg-[#680A10] text-white font-bold text-xs rounded-lg transition-colors shadow-xs disabled:opacity-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#85131B] inline-flex items-center justify-center gap-2"
                        >
                            <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Mendaftarkan akun...' : 'Daftar Sekarang' }}</span>
                        </button>
                    </div>

                    <!-- Footer Link -->
                    <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                        Sudah memiliki akun mitra?
                        <Link :href="route('login')" class="text-[#85131B] font-bold hover:underline ml-1">
                            Masuk di sini
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
