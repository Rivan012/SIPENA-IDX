<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk — SIPENA IDX Bengkulu" />

    <div class="min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased selection:bg-[#85131B] selection:text-white">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
            <!-- Brand Mark -->
            <Link :href="route('home')" class="inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-xl p-1">
                <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-16 w-auto mx-auto" />
            </Link>

            <h1 class="mt-6 text-xl font-bold tracking-tight text-slate-900">
                Masuk ke Sistem
            </h1>
            <p class="mt-1 text-xs text-slate-600 leading-relaxed max-w-sm mx-auto">
                Satu akses masuk untuk Peserta Edukasi dan Administrator BEI Bengkulu.
            </p>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-8 border border-slate-300 rounded-xl shadow-xs">
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
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            placeholder="Masukkan kata sandi akun Anda"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-xs text-red-600">
                            {{ form.errors.password }}
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
                            Belum memiliki akun peserta?
                            <Link :href="route('register')" class="text-[#85131B] font-bold hover:underline">
                                Daftar Akun Peserta
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
