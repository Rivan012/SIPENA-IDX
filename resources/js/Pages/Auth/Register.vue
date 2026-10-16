<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const form = useForm({
    name: '',
    email: '',
    no_telepon: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register.submit'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="relative min-h-screen bg-slate-100 flex items-center justify-center p-4">
        <ThemeToggle class="absolute right-4 top-4" />
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-[#680A10] to-[#85131B] p-6 text-white text-center">
                <div class="bg-white p-2 rounded-xl inline-block mb-3 shadow-xs">
                    <img src="/images/logo.svg" alt="SIPENA IDX" class="h-10 w-auto" />
                </div>
                <h1 class="text-xl font-bold">Daftar Mitra</h1>
                <p class="text-xs text-rose-100/90 mt-1">Sistem Informasi Pengelolaan Edukasi Pasar Modal</p>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input v-model="form.name"
                           type="text"
                           required
                           autofocus
                           placeholder="Nama sesuai identitas"
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <p v-if="form.errors.name" class="text-[11px] text-red-600 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                    <input v-model="form.email"
                           type="email"
                           required
                           placeholder="nama@email.com"
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <p v-if="form.errors.email" class="text-[11px] text-red-600 mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / Telepon</label>
                    <input v-model="form.no_telepon"
                           type="text"
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <p v-if="form.errors.no_telepon" class="text-[11px] text-red-600 mt-1">{{ form.errors.no_telepon }}</p>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                        <input v-model="form.password"
                               type="password"
                               required
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                        <p v-if="form.errors.password" class="text-[11px] text-red-600 mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi</label>
                        <input v-model="form.password_confirmation"
                               type="password"
                               required
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    </div>
                </div>

                <button type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 bg-[#680A10] hover:bg-[#54080D] text-white font-bold text-xs rounded-xl transition shadow-xs disabled:opacity-50 mt-2">
                    {{ form.processing ? 'Mendaftarkan...' : 'Daftar Sekarang' }}
                </button>

                <div class="pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
                    Sudah punya akun?
                    <Link :href="route('login')" class="text-[#680A10] font-bold hover:underline">
                        Masuk di sini
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
