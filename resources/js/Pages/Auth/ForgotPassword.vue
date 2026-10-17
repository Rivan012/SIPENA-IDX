<script setup>
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { MailIcon, RefreshIcon } from '@/Icons';

defineProps({
    status: String,
});

const captchaUrl = ref(route('captcha', { action: 'forgot_password', t: Date.now() }));

const refreshCaptcha = () => {
    form.captcha = '';
    captchaUrl.value = route('captcha', { action: 'forgot_password', t: Date.now() });
};

const form = useForm({
    email: '',
    captcha: '',
});

const submit = () => {
    form.post(route('password.email'), {
        onFinish: () => refreshCaptcha(),
    });
};
</script>

<template>
    <Head title="Pemulihan Kata Sandi — SIPENA IDX Bengkulu" />

    <div class="relative min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased selection:bg-[#85131B] selection:text-white">
        <ThemeToggle class="absolute right-4 top-4" />

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-8 border border-slate-300 rounded-xl shadow-xs">
                <!-- Brand Mark & Header -->
                <div class="text-center mb-6">
                    <Link :href="route('home')" class="inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-xl p-1 mb-3">
                        <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-16 w-auto mx-auto" />
                    </Link>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight">Pemulihan Kata Sandi</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Masukkan email Anda untuk menerima tautan atur ulang kata sandi.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Status Flash Message -->
                    <div v-if="status" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 flex items-start gap-2.5">
                        <svg class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- General Error Alert -->
                    <div v-if="form.errors.email" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                        {{ form.errors.email }}
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-900 mb-1">
                            Alamat Email Terdaftar
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            placeholder="nama@email.com atau akun@bei.co.id"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
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
                    <div class="pt-1">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 bg-[#85131B] hover:bg-[#680A10] text-white font-bold text-xs rounded-lg transition-colors shadow-xs disabled:opacity-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#85131B] inline-flex items-center justify-center gap-2"
                        >
                            <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <MailIcon v-else class="h-4 w-4" />
                            <span>{{ form.processing ? 'Mengirim tautan pemulihan...' : 'Kirim Tautan Pemulihan' }}</span>
                        </button>
                    </div>

                    <!-- Back to Login Link -->
                    <div class="pt-4 border-t border-slate-200 text-center text-xs">
                        <Link :href="route('login')" class="font-semibold text-slate-600 hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                            <span>&larr;</span>
                            <span>Kembali ke halaman masuk</span>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
