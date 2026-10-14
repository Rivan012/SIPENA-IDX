<script setup>
import { useForm, Link } from '@inertiajs/vue3';

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
    form.post(route('password.update'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="bg-[#680A10] p-6 text-white text-center">
                <div class="bg-white p-2 rounded-xl inline-block mb-3 shadow-xs">
                    <img src="/images/logo.svg" alt="SIPENA IDX" class="h-10 w-auto" />
                </div>
                <h1 class="text-xl font-bold">Atur Ulang Kata Sandi</h1>
                <p class="text-xs text-rose-100/90 mt-1">SIPENA — IDX Bengkulu</p>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input v-model="form.email" type="email" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200" />
                    <p v-if="form.errors.email" class="text-[11px] text-red-600 mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                    <input v-model="form.password" type="password" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200" />
                    <p v-if="form.errors.password" class="text-[11px] text-red-600 mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password</label>
                    <input v-model="form.password_confirmation" type="password" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200" />
                </div>

                <button type="submit" :disabled="form.processing"
                        class="w-full py-2.5 bg-[#680A10] hover:bg-[#54080D] text-white font-bold text-xs rounded-xl transition shadow-xs disabled:opacity-50">
                    {{ form.processing ? 'Menyimpan...' : 'Perbarui Password' }}
                </button>
            </form>
        </div>
    </div>
</template>
