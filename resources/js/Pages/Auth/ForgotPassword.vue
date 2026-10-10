<script setup>
import { useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="bg-[#680A10] p-6 text-white text-center">
                <h1 class="text-xl font-bold">Pemulihan Password</h1>
                <p class="text-xs text-white/70 mt-1">SIPENA — BEI Bengkulu</p>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div v-if="status" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800">
                    {{ status }}
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Terdaftar</label>
                    <input v-model="form.email"
                           type="email"
                           required
                           autofocus
                           placeholder="nama@email.com"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <p v-if="form.errors.email" class="text-[11px] text-red-600 mt-1">{{ form.errors.email }}</p>
                </div>

                <button type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 bg-[#680A10] hover:bg-[#54080D] text-white font-bold text-xs rounded-xl transition shadow-xs disabled:opacity-50">
                    {{ form.processing ? 'Mengirim...' : 'Kirim Tautan Reset' }}
                </button>

                <div class="pt-3 border-t border-slate-100 text-center text-xs">
                    <Link :href="route('login')" class="text-slate-500 hover:text-slate-800">
                        &larr; Kembali ke halaman login
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
