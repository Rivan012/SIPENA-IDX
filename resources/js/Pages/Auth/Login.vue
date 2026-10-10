<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.submit'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-[#680A10] to-[#85131B] p-6 text-white text-center">
                <img src="/images/idx-logo-icon.png" alt="IDX" class="w-12 h-12 mx-auto mb-2 object-contain bg-white/10 p-1 rounded-xl" />
                <h1 class="text-xl font-bold">SIPENA IDX</h1>
                <p class="text-xs text-white/70 mt-1">Portal Peserta Edukasi Pasar Modal BEI Bengkulu</p>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div v-if="form.errors.email" class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                    {{ form.errors.email }}
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Peserta</label>
                    <input v-model="form.email"
                           type="email"
                           required
                           autofocus
                           placeholder="nama@email.com"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <input v-model="form.password"
                           type="password"
                           required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input v-model="form.remember" type="checkbox" class="rounded text-[#680A10] focus:ring-[#680A10]" />
                        <span>Ingat saya</span>
                    </label>
                    <Link :href="route('password.request')" class="text-[#680A10] hover:underline">
                        Lupa password?
                    </Link>
                </div>

                <button type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 bg-[#680A10] hover:bg-[#54080D] text-white font-bold text-xs rounded-xl transition shadow-xs disabled:opacity-50">
                    {{ form.processing ? 'Memproses...' : 'Masuk Akun' }}
                </button>

                <div class="pt-4 border-t border-slate-100 flex flex-col items-center gap-2 text-xs text-slate-500">
                    <div>
                        Belum punya akun peserta?
                        <Link :href="route('register')" class="text-[#680A10] font-bold hover:underline">
                            Daftar di sini
                        </Link>
                    </div>
                    <Link :href="route('admin.login')" class="text-slate-400 hover:text-slate-700">
                        Masuk sebagai Admin BEI &rarr;
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
