<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token: props.token,
    email: props.email || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Atur Ulang Kata Sandi — SIPENA IDX Bengkulu" />

    <div class="relative min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased selection:bg-[#85131B] selection:text-white">
        <ThemeToggle class="absolute right-4 top-4" />

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-8 border border-slate-300 rounded-xl shadow-xs">
                <!-- Brand Mark & Header -->
                <div class="text-center mb-6">
                    <Link :href="route('home')" class="inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-xl p-1 mb-3">
                        <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-16 w-auto mx-auto" />
                    </Link>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight">Atur Ulang Kata Sandi</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Buat kata sandi baru untuk akun SIPENA IDX Anda.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Error Alert -->
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
                            readonly
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-slate-50 text-slate-600 cursor-not-allowed focus:outline-none"
                        />
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-900 mb-1">
                            Kata Sandi Baru
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            autofocus
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-xs text-red-600">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-900 mb-1">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            placeholder="Ulangi kata sandi baru"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
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
                            <span>{{ form.processing ? 'Menyimpan kata sandi...' : 'Perbarui Kata Sandi' }}</span>
                        </button>
                    </div>

                    <!-- Back to Login Link -->
                    <div class="pt-4 border-t border-slate-200 text-center text-xs">
                        <Link :href="route('login')" class="font-semibold text-slate-600 hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                            <span>&larr;</span>
                            <span>Batal dan kembali ke halaman masuk</span>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
