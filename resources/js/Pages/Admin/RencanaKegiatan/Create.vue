<script setup>
import { defineAsyncComponent } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const RichTextEditor = defineAsyncComponent(() => import('@/Components/RichTextEditor.vue'));

const props = defineProps({
    rencana: Object,
    detail: Object,
    categories: Array,
    descriptionImageUploadUrl: String,
});

const form = useForm({
    nama_kegiatan: '',
    jenis_kegiatan: 'SPM',
    tanggal_rencana: props.rencana.tanggal_rencana || '',
    lokasi: '',
    waktu_mulai: '09:00',
    waktu_selesai: '15:00',
    target_peserta: 100,
    anggaran: 10000000,
    deskripsi: '',
    keterangan: '',
    status: 'draft',
});

const submit = () => {
    form.post(route('admin.rencana-kegiatan.store'));
};
</script>

<template>
    <AdminLayout title="Tambah Rencana Anggaran — SIPENA IDX">
        <Head title="Tambah Rencana Anggaran" />

        <div class="max-w-4xl mx-auto space-y-5">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('admin.rencana-kegiatan.index')" class="hover:text-slate-900">Anggaran &amp; RKAP</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold">Tambah Rencana Baru</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Penyusunan Rencana Anggaran Baru</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Definisikan alokasi biaya pos program kerja sebelum diajukan ke approval.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Program Rencana *</label>
                        <input v-model="form.nama_kegiatan" type="text" required placeholder="Contoh: Workshop Pengenalan Instrumen Derivatif"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                        <p v-if="form.errors.nama_kegiatan" class="text-[11px] text-red-600 mt-1">{{ form.errors.nama_kegiatan }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Program *</label>
                            <select v-model="form.jenis_kegiatan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white">
                                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Awal</label>
                            <select v-model="form.status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white">
                                <option value="draft">Draft (Disimpan Sementara)</option>
                                <option value="diajukan">Langsung Ajukan Approval</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Rencana Pelaksanaan *</label>
                            <input v-model="form.tanggal_rencana" type="date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                            <p v-if="form.errors.tanggal_rencana" class="text-[11px] text-red-600 mt-1">{{ form.errors.tanggal_rencana }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Mulai *</label>
                            <input v-model="form.waktu_mulai" type="time" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Selesai</label>
                            <input v-model="form.waktu_selesai" type="time" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi Kegiatan *</label>
                            <input v-model="form.lokasi" type="text" required placeholder="Contoh: GI BEI UNIB Bengkulu"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                            <p v-if="form.errors.lokasi" class="text-[11px] text-red-600 mt-1">{{ form.errors.lokasi }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Peserta (Orang)</label>
                            <input v-model="form.target_peserta" type="number" min="1"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Usulan Alokasi Anggaran (Rp) *</label>
                        <input v-model="form.anggaran" type="number" min="0" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-emerald-800" />
                        <p v-if="form.errors.anggaran" class="text-[11px] text-red-600 mt-1">{{ form.errors.anggaran }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Ringkas Kegiatan</label>
                        <RichTextEditor v-model="form.deskripsi" :upload-url="props.descriptionImageUploadUrl" :disabled="form.processing" />
                        <p v-if="form.errors.deskripsi" class="text-[11px] text-red-600 mt-1">{{ form.errors.deskripsi }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <Link :href="route('admin.rencana-kegiatan.index')" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl bg-[#680A10] text-white text-xs font-semibold hover:bg-[#54080D] transition disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Rencana' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
