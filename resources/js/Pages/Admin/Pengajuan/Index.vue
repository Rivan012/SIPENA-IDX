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
    makerProposals: Object,
    makerStats: Object,
    makerFilters: Object,
    priorityProposal: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');
const currentKategori = ref(props.filters?.kategori || '');
const makerSearch = ref(props.makerFilters?.maker_search || '');
const makerStatus = ref(props.makerFilters?.maker_status || '');

const handleFilter = () => {
    router.get(route('admin.approval.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
        maker_search: props.makerFilters?.maker_search || undefined,
        maker_status: props.makerFilters?.maker_status || undefined,
    }, { preserveState: true });
};

const handleMakerFilter = () => {
    router.get(route('admin.approval.index'), {
        search: props.filters?.search || undefined,
        status: props.filters?.status || undefined,
        kategori: props.filters?.kategori || undefined,
        maker_search: makerSearch.value || undefined,
        maker_status: makerStatus.value || undefined,
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

const showMakerModal = ref(false);
const makerTarget = ref(null);
const makerForm = useForm({ status: '', catatan: '' });

const openMakerDecision = (item, status) => {
    makerTarget.value = item;
    makerForm.status = status;
    makerForm.catatan = '';
    showMakerModal.value = true;
};

const submitMakerDecision = () => {
    if (!makerTarget.value) return;

    makerForm.patch(route('admin.approval.kegiatan.status', makerTarget.value.id), {
        onSuccess: () => {
            showMakerModal.value = false;
            makerTarget.value = null;
        },
    });
};

const makerStatusLabel = (status) => ({
    menunggu_approval: 'Menunggu Review',
    diproses: 'Sedang Ditinjau',
    disetujui: 'Disetujui',
    dipublikasikan: 'Terjadwal',
    berlangsung: 'Berlangsung',
    revisi: 'Perlu Revisi',
    ditolak: 'Ditolak',
    dibatalkan: 'Dibatalkan',
    selesai: 'Selesai',
}[status] || status);

const makerStatusClass = (status) => ({
    menunggu_approval: 'bg-amber-50 text-amber-800',
    diproses: 'bg-sky-50 text-sky-800',
    disetujui: 'bg-emerald-50 text-emerald-800',
    dipublikasikan: 'bg-blue-50 text-blue-800',
    berlangsung: 'bg-rose-50 text-rose-800',
    revisi: 'bg-orange-50 text-orange-800',
    ditolak: 'bg-red-50 text-red-800',
    dibatalkan: 'bg-slate-100 text-slate-700',
    selesai: 'bg-slate-100 text-slate-700',
}[status] || 'bg-slate-100 text-slate-700');
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

            <section class="space-y-4" aria-labelledby="maker-approval-heading">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <h2 id="maker-approval-heading" class="text-lg font-bold text-slate-900">Persetujuan Proposal Maker</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tinjau usulan kegiatan langsung dari maker.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MENUNGGU TINDAKAN</span>
                        <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ makerStats.menunggu }}</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 block">Proposal baru dan sedang ditinjau</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">DISETUJUI / BERJALAN</span>
                        <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ makerStats.disetujui }}</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 block">Termasuk terjadwal dan selesai</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">REVISI / DITOLAK</span>
                        <span class="text-2xl font-bold text-red-600 mt-1 block">{{ makerStats.masalah }}</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 block">Catatan keputusan tersedia untuk maker</span>
                    </div>
                </div>

                <div v-if="priorityProposal" class="bg-white rounded-xl border border-blue-100 overflow-hidden">
                    <div class="px-4 py-3 bg-blue-50/70 border-b border-blue-100 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            <h3 class="text-xs font-bold text-slate-900">PROPOSAL PRIORITAS UNTUK DIREVIEW</h3>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500">
                            Masuk {{ new Date(priorityProposal.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </span>
                    </div>
                    <div class="p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-semibold text-slate-500">{{ priorityProposal.mitra || priorityProposal.user?.name || 'Maker' }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px]">{{ priorityProposal.jenis_kegiatan }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">{{ priorityProposal.nama_kegiatan }}</h3>
                            <p class="text-[11px] text-slate-500">{{ priorityProposal.user?.name || 'Maker' }} · {{ priorityProposal.tanggal ? new Date(priorityProposal.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Tanggal belum ditentukan' }} · {{ priorityProposal.lokasi }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <a v-if="priorityProposal.proposal_file_path" :href="route('admin.kegiatan.proposal.download', priorityProposal.id)"
                               class="px-3 py-2 rounded-md border border-slate-200 text-slate-700 text-[11px] font-semibold hover:bg-slate-50">
                                Lihat PDF
                            </a>
                            <button type="button" @click="openMakerDecision(priorityProposal, 'disetujui')"
                                    class="px-3 py-2 rounded-md bg-blue-700 text-white text-[11px] font-semibold hover:bg-blue-800">
                                Setujui
                            </button>
                            <button type="button" @click="openMakerDecision(priorityProposal, 'revisi')"
                                    class="px-3 py-2 rounded-md text-red-700 text-[11px] font-semibold hover:bg-red-50">
                                Minta Revisi
                            </button>
                            <button type="button" @click="openMakerDecision(priorityProposal, 'ditolak')"
                                    class="px-3 py-2 rounded-md border border-red-200 text-red-700 text-[11px] font-semibold hover:bg-red-50">
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <div class="flex items-center gap-1 overflow-x-auto" role="tablist" aria-label="Filter status proposal maker">
                            <button v-for="tab in [
                                { value: '', label: `Semua (${makerStats.menunggu + makerStats.disetujui + makerStats.masalah})` },
                                { value: 'menunggu', label: `Menunggu (${makerStats.menunggu})` },
                                { value: 'disetujui', label: `Disetujui (${makerStats.disetujui})` },
                                { value: 'masalah', label: `Revisi / Ditolak (${makerStats.masalah})` },
                            ]" :key="tab.value" type="button" @click="makerStatus = tab.value; handleMakerFilter()"
                               :aria-selected="makerStatus === tab.value"
                               :class="[
                                   'px-3 py-2 text-[11px] font-semibold whitespace-nowrap border-b-2',
                                   makerStatus === tab.value ? 'border-[#9F0E0F] text-[#85131B]' : 'border-transparent text-slate-500 hover:text-slate-800'
                               ]">
                                {{ tab.label }}
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input v-model="makerSearch" @keyup.enter="handleMakerFilter" type="search" placeholder="Cari maker atau kegiatan"
                                   class="w-full lg:w-56 px-3 py-2 rounded-md border border-slate-200 text-xs" />
                            <button type="button" @click="handleMakerFilter" class="px-3 py-2 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">Cari</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase">
                                    <th class="py-3 px-4">MAKER &amp; KEGIATAN</th>
                                    <th class="py-3 px-4">TANGGAL</th>
                                    <th class="py-3 px-4">STATUS</th>
                                    <th class="py-3 px-4">REVIEWER / CATATAN</th>
                                    <th class="py-3 px-4 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="proposal in makerProposals.data" :key="proposal.id" class="hover:bg-slate-50/60">
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-900">{{ proposal.nama_kegiatan }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">{{ proposal.user?.name }} · {{ proposal.mitra || 'Mitra tidak dicantumkan' }}</div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-slate-600">{{ proposal.tanggal ? new Date(proposal.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span :class="['px-2 py-1 rounded text-[10px] font-semibold', makerStatusClass(proposal.status)]">{{ makerStatusLabel(proposal.status) }}</span>
                                    </td>
                                    <td class="py-3 px-4 max-w-xs">
                                        <div class="text-slate-700">{{ proposal.reviewed_by_admin?.name || '-' }}</div>
                                        <div v-if="proposal.approval_note" class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">{{ proposal.approval_note }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div v-if="['menunggu_approval', 'diproses'].includes(proposal.status)" class="inline-flex gap-1.5">
                                            <button type="button" @click="openMakerDecision(proposal, 'disetujui')" class="px-2.5 py-1 rounded bg-blue-700 text-white text-[10px] font-semibold">Setujui</button>
                                            <button type="button" @click="openMakerDecision(proposal, 'revisi')" class="px-2.5 py-1 rounded border border-slate-200 text-slate-700 text-[10px] font-semibold">Review</button>
                                        </div>
                                        <span v-else class="text-[10px] text-slate-500">Selesai direview</span>
                                    </td>
                                </tr>
                                <tr v-if="!makerProposals.data?.length">
                                    <td colspan="5" class="py-10 text-center text-xs text-slate-400">Belum ada proposal maker pada status ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :links="makerProposals.links" :from="makerProposals.from" :to="makerProposals.to" :total="makerProposals.total" item-label="proposal maker" />
                </div>
            </section>

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

        <div v-if="showMakerModal" class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ makerForm.status === 'disetujui' ? 'Setujui Proposal Maker' : makerForm.status === 'revisi' ? 'Minta Revisi Proposal' : 'Tolak Proposal Maker' }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ makerTarget?.nama_kegiatan }}</p>
                    </div>
                    <button type="button" @click="showMakerModal = false" aria-label="Tutup dialog" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>
                <form @submit.prevent="submitMakerDecision" class="space-y-4">
                    <div>
                        <label for="maker-approval-note" class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan {{ ['revisi', 'ditolak'].includes(makerForm.status) ? '*' : '(opsional)' }}
                        </label>
                        <textarea id="maker-approval-note" v-model="makerForm.catatan" rows="4" :required="['revisi', 'ditolak'].includes(makerForm.status)"
                                  class="w-full px-3 py-2 rounded-md border border-slate-200 text-xs" placeholder="Tuliskan keputusan atau hal yang perlu diperbaiki..."></textarea>
                        <p v-if="makerForm.errors.catatan" class="text-[11px] text-red-600 mt-1">{{ makerForm.errors.catatan }}</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showMakerModal = false" class="px-4 py-2 rounded-md border border-slate-200 text-xs font-semibold">Batal</button>
                        <button type="submit" :disabled="makerForm.processing" class="px-4 py-2 rounded-md bg-blue-700 text-white text-xs font-semibold disabled:opacity-50">
                            {{ makerForm.processing ? 'Menyimpan...' : 'Simpan Keputusan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
