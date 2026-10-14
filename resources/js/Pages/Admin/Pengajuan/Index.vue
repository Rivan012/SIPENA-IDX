<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    pengajuanList: Object,
    stats: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');
const currentKategori = ref(props.filters?.kategori || '');

const handleFilter = () => {
    router.get(route('admin.approval.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
    }, { preserveState: true });
};

// Quick action modal
const showModal = ref(false);
const modalAction = ref('');
const modalTarget = ref(null);

const actionForm = useForm({
    status: '',
    catatan: '',
    catatan_revisi: '',
    terbitkan_kegiatan: true,
});

const openDecision = (item, action) => {
    modalTarget.value = item;
    modalAction.value = action;
    actionForm.status = action;
    actionForm.catatan = '';
    actionForm.catatan_revisi = '';
    showModal.value = true;
};

const submitDecision = () => {
    if (!modalTarget.value) return;
    actionForm.patch(route('admin.pengajuan.status', modalTarget.value.id), {
        onFinish: () => {
            showModal.value = false;
            modalTarget.value = null;
        },
    });
};
</script>

<template>
    <AdminLayout title="Approval Kegiatan & Anggaran — SIPENA IDX">
        <Head title="Approval & Persetujuan" />

        <div class="space-y-5">
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Approval &amp; Persetujuan Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Verifikasi usulan program, tinjau kesesuaian anggaran, dan terbitkan izin pelaksanaan.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="route('admin.pengajuan.export', filters)"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs">
                        &darr; Ekspor CSV
                    </a>
                </div>
            </div>

            <!-- 4 KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MENUNGGU REVIEW</span>
                    <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ stats.pending_total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">{{ stats.menunggu }} Baru, {{ stats.diproses }} Ditinjau</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ANGGARAN DALAM REVIEW</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block truncate" :title="stats.anggaran_review">{{ stats.anggaran_review }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Usulan menunggu putusan</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TELAH DISETUJUI</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ stats.disetujui }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Siap diselenggarakan</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PERLU REVISI / DITOLAK</span>
                    <span class="text-2xl font-bold text-red-600 mt-1 block">{{ stats.revisi + stats.ditolak }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">{{ stats.revisi }} Revisi, {{ stats.ditolak }} Ditolak</span>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-md">
                    <input v-model="search"
                           @keyup.enter="handleFilter"
                           type="text"
                           placeholder="Cari nomor proposal, nama kegiatan..."
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <button type="button" @click="handleFilter"
                            class="px-4 py-2 bg-[#680A10] text-white text-xs font-semibold rounded-xl hover:bg-[#54080D]">
                        Cari
                    </button>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select v-model="currentStatus" @change="handleFilter"
                            class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="diajukan">Menunggu Approval</option>
                        <option value="diproses">Sedang Ditinjau</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="revisi">Perlu Revisi</option>
                        <option value="ditolak">Ditolak</option>
                    </select>

                    <select v-model="currentKategori" @change="handleFilter"
                            class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none">
                        <option value="">Semua Kategori</option>
                        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                    </select>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">PROPOSAL & PROGRAM</th>
                                <th class="py-3 px-4">KATEGORI</th>
                                <th class="py-3 px-4">TGL PENGAJUAN</th>
                                <th class="py-3 px-4 text-right">ANGGARAN</th>
                                <th class="py-3 px-4 text-center">STATUS</th>
                                <th class="py-3 px-4 text-right">AKSI KEPUTUSAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="p in pengajuanList.data" :key="p.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-mono text-[10px] font-bold text-slate-400 block">{{ p.nomor_pengajuan }}</span>
                                    <Link :href="route('admin.pengajuan.show', p.id)" class="font-bold text-slate-900 hover:text-[#680A10] leading-snug">
                                        {{ p.nama_kegiatan }}
                                    </Link>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ p.jenis_kegiatan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ p.tanggal_pengajuan ? new Date(p.tanggal_pengajuan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-slate-900">
                                    {{ p.formatted_anggaran }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', p.status_class]">
                                        {{ p.status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <template v-if="p.status === 'diajukan' || p.status === 'diproses'">
                                            <button type="button" @click="openDecision(p, 'disetujui')"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs">
                                                Setujui &check;
                                            </button>
                                            <button type="button" @click="openDecision(p, 'revisi')"
                                                    class="px-2 py-1 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-[11px]">
                                                Revisi
                                            </button>
                                        </template>
                                        <Link :href="route('admin.pengajuan.show', p.id)"
                                              class="p-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900" title="Detail Verifikasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!pengajuanList.data?.length">
                                <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                    Tidak ada pengajuan yang memerlukan tindakan review saat ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="pengajuanList.links"
                    :from="pengajuanList.from"
                    :to="pengajuanList.to"
                    :total="pengajuanList.total"
                    item-label="usulan"
                />
            </div>
        </div>

        <!-- Decision Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">
                        {{ modalAction === 'disetujui' ? 'Setujui Pengajuan' : 'Minta Revisi Proposal' }}
                    </h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <div class="text-xs text-slate-600 space-y-1">
                    <p>Proposal: <strong>{{ modalTarget?.nomor_pengajuan }}</strong></p>
                    <p>Kegiatan: <strong>{{ modalTarget?.nama_kegiatan }}</strong></p>
                    <p>Alokasi: <strong class="text-emerald-700">{{ modalTarget?.formatted_anggaran }}</strong></p>
                </div>

                <form @submit.prevent="submitDecision" class="space-y-3 pt-2">
                    <div v-if="modalAction === 'disetujui'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Persetujuan (Opsional)</label>
                        <textarea v-model="actionForm.catatan" rows="3" placeholder="Disetujui sesuai alokasi RKAP..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>

                        <label class="flex items-center gap-2 text-xs text-slate-700 mt-2 cursor-pointer">
                            <input v-model="actionForm.terbitkan_kegiatan" type="checkbox" class="rounded text-[#680A10]" />
                            <span>Otomatis publikasikan jadwal kegiatan ke kalender</span>
                        </label>
                    </div>

                    <div v-else>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uraian Catatan Revisi *</label>
                        <textarea v-model="actionForm.catatan_revisi" rows="3" required placeholder="Sebutkan hal yang perlu disesuaikan..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showModal = false" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="actionForm.processing"
                                :class="[
                                    'w-1/2 py-2.5 rounded-xl text-xs font-semibold text-white transition disabled:opacity-50',
                                    modalAction === 'disetujui' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700'
                                ]">
                            {{ actionForm.processing ? 'Memproses...' : 'Kirim Keputusan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
