<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    kegiatan: Object,
    rencanaKegiatan: Array,
    pengajuanKegiatan: Array,
    kategori: Array,
});

const form = useForm({
    nama_kegiatan: '',
    deskripsi: '',
    jenis_kegiatan: 'SPM',
    tanggal: '',
    waktu_mulai: '09:00',
    waktu_selesai: '15:30',
    lokasi: '',
    kuota_peserta: 100,
    status: 'menunggu_approval',
    rencana_kegiatan_id: '',
    pengajuan_kegiatan_id: '',
});

const submit = () => {
    form.post(route('admin.kegiatan.store'));
};
</script>

<template>
    <AdminLayout title="Tambah Kegiatan Baru — SIPENA IDX">
        <Head title="Tambah Kegiatan" />

        <div class="max-w-4xl mx-auto space-y-5">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('admin.kegiatan.index')" class="hover:text-slate-900">Kegiatan</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold">Tambah Kegiatan Baru</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Tambah Kegiatan Edukasi Baru</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Form pembuatan jadwal program kerja dan sosialisasi pasar modal.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kegiatan *</label>
                        <input v-model="form.nama_kegiatan" type="text" required placeholder="Contoh: Sekolah Pasar Modal Level 1"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                        <p v-if="form.errors.nama_kegiatan" class="text-[11px] text-red-600 mt-1">{{ form.errors.nama_kegiatan }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori / Jenis Kegiatan *</label>
                            <select v-model="form.jenis_kegiatan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white">
                                <option value="SPM">SPM (Sekolah Pasar Modal)</option>
                                <option value="Sosialisasi">Sosialisasi</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Edukasi">Edukasi Publik</option>
                                <option value="Webinar">Webinar Online</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Kegiatan *</label>
                            <select v-model="form.status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white">
                                <option value="draft">Draft</option>
                                <option value="menunggu_approval">Menunggu Approval</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="dipublikasikan">Dipublikasikan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Materi Acara *</label>
                        <textarea v-model="form.deskripsi" rows="3" required placeholder="Jelaskan tujuan dan cakupan materi..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20"></textarea>
                        <p v-if="form.errors.deskripsi" class="text-[11px] text-red-600 mt-1">{{ form.errors.deskripsi }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Acara *</label>
                            <input v-model="form.tanggal" type="date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" />
                            <p v-if="form.errors.tanggal" class="text-[11px] text-red-600 mt-1">{{ form.errors.tanggal }}</p>
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
                            <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi Tempat Pelaksanaan *</label>
                            <input v-model="form.lokasi" type="text" required placeholder="Contoh: Galeri Investasi BEI UNIB"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                            <p v-if="form.errors.lokasi" class="text-[11px] text-red-600 mt-1">{{ form.errors.lokasi }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kuota Target Peserta</label>
                            <input v-model="form.kuota_peserta" type="number" min="1" placeholder="100"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <Link :href="route('admin.kegiatan.index')" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl bg-[#680A10] text-white text-xs font-semibold hover:bg-[#54080D] transition disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Kegiatan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
