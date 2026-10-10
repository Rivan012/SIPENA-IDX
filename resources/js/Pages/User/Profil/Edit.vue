<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    no_telepon: props.user?.no_telepon || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('user.profil.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <UserLayout title="Profil Saya — SIPENA IDX">
        <Head title="Pengaturan Profil" />

        <div class="max-w-2xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengaturan Profil</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola informasi data diri akun peserta Anda.</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                        <input v-model="form.name" type="text" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                        <p v-if="form.errors.name" class="text-[11px] text-red-600 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                        <input v-model="form.email" type="email" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                        <p v-if="form.errors.email" class="text-[11px] text-red-600 mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / Telepon</label>
                        <input v-model="form.no_telepon" type="text" placeholder="08xxxxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                        <p v-if="form.errors.no_telepon" class="text-[11px] text-red-600 mt-1">{{ form.errors.no_telepon }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <h2 class="text-xs font-bold text-slate-900 mb-1">Ganti Password (Opsional)</h2>
                        <p class="text-[11px] text-slate-400 mb-3">Kosongkan jika Anda tidak ingin mengganti password akun.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Password Baru</label>
                                <input v-model="form.password" type="password"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                                <p v-if="form.errors.password" class="text-[11px] text-red-600 mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                                <input v-model="form.password_confirmation" type="password"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl bg-[#680A10] text-white font-semibold text-xs hover:bg-[#54080D] transition shadow-xs disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>
