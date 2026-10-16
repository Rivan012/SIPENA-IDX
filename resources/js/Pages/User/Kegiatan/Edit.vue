<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    kegiatan: Object,
});

const form = useForm({
    nama_kegiatan: props.kegiatan.nama_kegiatan || '',
    deskripsi: props.kegiatan.deskripsi || '',
    jenis_kegiatan: props.kegiatan.jenis_kegiatan || 'SPM',
    tanggal: props.kegiatan.tanggal ? props.kegiatan.tanggal.substring(0, 10) : '',
    waktu_mulai: props.kegiatan.waktu_mulai ? props.kegiatan.waktu_mulai.substring(0, 5) : '09:00',
    waktu_selesai: props.kegiatan.waktu_selesai ? props.kegiatan.waktu_selesai.substring(0, 5) : '12:00',
    lokasi: props.kegiatan.lokasi || '',
    mitra: props.kegiatan.mitra || '',
    kuota_peserta: props.kegiatan.kuota_peserta || 80,
    proposal_file: null,
});

const submit = () => {
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(route('user.kegiatan.update', props.kegiatan.id));
};
</script>

<template>
    <UserLayout title="Edit Proposal Kegiatan — SIPENA IDX">
        <Head title="Edit Proposal Kegiatan" />

        <div class="max-w-4xl mx-auto space-y-5">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('user.kegiatan-saya')" class="hover:text-slate-900">Proposal Saya</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold">Edit Proposal</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Perbarui Proposal Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Edit data kegiatan yang Anda ajukan sebelum approval admin.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kegiatan *</label>
                        <input v-model="form.nama_kegiatan" type="text" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        <p v-if="form.errors.nama_kegiatan" class="text-[11px] text-red-600 mt-1">{{ form.errors.nama_kegiatan }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kegiatan *</label>
                            <select v-model="form.jenis_kegiatan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white">
                                <option value="SPM">SPM</option>
                                <option value="Sosialisasi">Sosialisasi</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Edukasi">Edukasi</option>
                                <option value="Webinar">Webinar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pelaksanaan *</label>
                            <input v-model="form.tanggal" type="date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Kegiatan *</label>
                        <textarea v-model="form.deskripsi" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Mulai *</label>
                            <input v-model="form.waktu_mulai" type="time" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Selesai</label>
                            <input v-model="form.waktu_selesai" type="time" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kuota Peserta</label>
                            <input v-model="form.kuota_peserta" type="number" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi *</label>
                            <input v-model="form.lokasi" type="text" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Mitra / Pembuat</label>
                            <input v-model="form.mitra" type="text" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label for="proposal_file" class="block text-xs font-bold text-slate-700 mb-1">Dokumen Proposal (PDF, maks. 10 MB)</label>
                        <p v-if="kegiatan.proposal_file_path" class="text-[11px] text-slate-500 mb-2">PDF saat ini tersimpan. Unggah file baru jika ingin menggantinya.</p>
                        <input id="proposal_file" type="file" accept="application/pdf,.pdf"
                               @input="form.proposal_file = $event.target.files[0] || null"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700" />
                        <p v-if="form.errors.proposal_file" class="text-[11px] text-red-600 mt-1">{{ form.errors.proposal_file }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <Link :href="route('user.kegiatan.show', kegiatan.id)" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl bg-[#680A10] text-white text-xs font-semibold hover:bg-[#54080D] transition disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>
