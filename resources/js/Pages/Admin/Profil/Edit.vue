<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ProfilePhotoField from '@/Components/ProfilePhotoField.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    admin: Object,
});

const form = useForm({
    name: props.admin?.name || '',
    email: props.admin?.email || '',
    no_telepon: props.admin?.no_telepon || '',
    jabatan: props.admin?.jabatan || '',
    avatar: null,
});

const submit = () => {
    form.put(route('admin.profil.update'), {
        onSuccess: () => form.reset('avatar'),
    });
};
</script>

<template>
    <AdminLayout title="Profil Saya">
        <Head title="Profil Administrator" />

        <section class="mx-auto max-w-2xl space-y-6">
            <header>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Profil Administrator</h1>
                <p class="mt-1 text-xs text-slate-500">Perbarui identitas dan foto akun administrator.</p>
            </header>

            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:p-6">
                <ProfilePhotoField
                    id="admin-profile-photo"
                    v-model="form.avatar"
                    :current-url="admin.profile_photo_url"
                    :error="form.errors.avatar"
                />

                <div>
                    <label for="admin-profile-name" class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap</label>
                    <input id="admin-profile-name" v-model="form.name" type="text" required class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]" />
                    <p v-if="form.errors.name" class="mt-1 text-[11px] text-red-600" role="alert">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="admin-profile-email" class="mb-1 block text-xs font-bold text-slate-700">Alamat Email</label>
                    <input id="admin-profile-email" v-model="form.email" type="email" required class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]" />
                    <p v-if="form.errors.email" class="mt-1 text-[11px] text-red-600" role="alert">{{ form.errors.email }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="admin-profile-phone" class="mb-1 block text-xs font-bold text-slate-700">Nomor Telepon</label>
                        <input id="admin-profile-phone" v-model="form.no_telepon" type="tel" class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]" />
                        <p v-if="form.errors.no_telepon" class="mt-1 text-[11px] text-red-600" role="alert">{{ form.errors.no_telepon }}</p>
                    </div>
                    <div>
                        <label for="admin-profile-role" class="mb-1 block text-xs font-bold text-slate-700">Jabatan</label>
                        <input id="admin-profile-role" v-model="form.jabatan" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]" />
                        <p v-if="form.errors.jabatan" class="mt-1 text-[11px] text-red-600" role="alert">{{ form.errors.jabatan }}</p>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-4">
                    <button type="submit" :disabled="form.processing" class="min-h-11 rounded-lg bg-[#680A10] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#54080D] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B] disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </section>
    </AdminLayout>
</template>