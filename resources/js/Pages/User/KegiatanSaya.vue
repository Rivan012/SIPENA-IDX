<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faBan, faCircleInfo, faPenToSquare, faTrashCan } from '@fortawesome/free-solid-svg-icons';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SortableTh from '@/Components/SortableTh.vue';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    kegiatanList: Object,
    counts: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
});

const currentSort = ref(props.filters?.sort || '');
const currentDirection = ref(props.filters?.direction || 'asc');
const currentStatus = ref(props.filters?.status || '');
const searchQuery = ref(props.filters?.search || '');

const tabs = computed(() => [
    { key: '', label: 'Semua', count: props.counts?.semua ?? props.kegiatanList?.total ?? 0 },
    { key: 'menunggu_approval', label: 'Menunggu Review', count: props.counts?.menunggu_approval ?? 0 },
    { key: 'disetujui', label: 'Disetujui', count: props.counts?.disetujui ?? 0 },
    { key: 'revisi', label: 'Perlu Revisi', count: props.counts?.revisi ?? 0 },
    { key: 'ditolak', label: 'Ditolak', count: props.counts?.ditolak ?? 0 },
    { key: 'selesai', label: 'Selesai', count: props.counts?.selesai ?? 0 },
]);

const applyFilters = () => {
    router.get(route('user.kegiatan.index'), {
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
        status: currentStatus.value || undefined,
        search: searchQuery.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const handleSort = (field) => {
    if (currentSort.value === field) {
        currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.value = field;
        currentDirection.value = 'asc';
    }
    applyFilters();
};

const handleStatusFilter = (status) => {
    currentStatus.value = status;
    applyFilters();
};

const handleSearch = () => {
    applyFilters();
};

const resetFilter = () => {
    currentStatus.value = '';
    searchQuery.value = '';
    applyFilters();
};

const cancelProposal = async (item) => {
    const confirmed = await confirmDialog({
        title: 'Batalkan Proposal?',
        text: `Proposal "${item.nama_kegiatan}" akan dibatalkan.`,
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Kembali',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });

    if (confirmed) {
        router.patch(route('user.kegiatan.cancel', item.id));
    }
};

const deleteProposal = async (item) => {
    const confirmed = await confirmDialog({
        title: 'Hapus Proposal?',
        text: `Proposal "${item.nama_kegiatan}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Kembali',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });

    if (confirmed) {
        router.delete(route('user.kegiatan.destroy', item.id));
    }
};
</script>

<template>
    <UserLayout title="Proposal Kegiatan Saya — SIPENA IDX">
        <Head title="Proposal Kegiatan Saya" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Proposal Kegiatan Saya</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar kegiatan yang Anda ajukan, lengkap dengan status approval.</p>
                </div>
                <Link :href="route('user.kegiatan.create')"
                      class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#680A10] text-white text-xs font-semibold hover:bg-[#54080D] transition">
                    Ajukan Kegiatan
                </Link>
            </div>

            <!-- FILTER TABS & SEARCH -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-semibold">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        type="button"
                        @click="handleStatusFilter(tab.key)"
                        :class="[
                            'px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors flex items-center gap-1.5',
                            currentStatus === tab.key
                                ? 'bg-[#680A10] text-white shadow-xs'
                                : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
                        ]"
                    >
                        <span>{{ tab.label }}</span>
                        <span
                            :class="[
                                'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
                                currentStatus === tab.key
                                    ? 'bg-white/20 text-white'
                                    : 'bg-slate-100 text-slate-600'
                            ]"
                        >
                            {{ tab.count }}
                        </span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Cari proposal..."
                        class="w-full sm:w-60 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#680A10]/20"
                    />
                    <button
                        v-if="searchQuery || currentStatus"
                        type="button"
                        @click="resetFilter"
                        class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-500 hover:bg-slate-50"
                        title="Reset Filter"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <SortableTh field="nama_kegiatan" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    PROGRAM KEGIATAN
                                </SortableTh>
                                <SortableTh field="tanggal" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    TANGGAL &amp; WAKTU
                                </SortableTh>
                                <SortableTh field="lokasi" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    LOKASI
                                </SortableTh>
                                <SortableTh field="status" align="center" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    STATUS APPROVAL
                                </SortableTh>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in kegiatanList.data" :key="item.id" class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <Link
                                        :href="route('user.kegiatan.show', item.id)"
                                        class="group block rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                                        :aria-label="`Buka rincian proposal ${item.nama_kegiatan}`"
                                    >
                                        <div class="font-bold text-slate-900 leading-snug group-hover:text-[#680A10] transition-colors">
                                            {{ item.nama_kegiatan }}
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 mt-1 inline-block">
                                            {{ item.jenis_kegiatan }}
                                        </span>
                                    </Link>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        {{ item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ item.waktu_mulai ? item.waktu_mulai.substring(0, 5) : '09:00' }} - {{ item.waktu_selesai ? item.waktu_selesai.substring(0, 5) : '15:30' }} WIB
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-[200px] truncate" :title="item.lokasi">
                                    {{ item.lokasi }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1">
                                        <span :class="[
                                            'inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold',
                                            ['disetujui', 'dipublikasikan'].includes(item.status) ? 'bg-emerald-100 text-emerald-800' :
                                            ['diproses', 'berlangsung'].includes(item.status) ? 'bg-blue-100 text-blue-800' :
                                            item.status === 'revisi' ? 'bg-amber-100 text-amber-800 border border-amber-200' :
                                            item.status === 'ditolak' ? 'bg-red-100 text-red-800 border border-red-200' :
                                            ['dibatalkan', 'selesai'].includes(item.status) ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-800'
                                        ]">
                                            {{ item.status_label || item.status }}
                                        </span>
                                        <span v-if="item.reviewed_by_admin?.name" class="text-[10px] text-slate-500">{{ item.reviewed_by_admin.name }}</span>
                                    </div>
                                    <p v-if="item.approval_note" class="mt-1 max-w-[220px] mx-auto whitespace-normal text-[10px] text-slate-500" :title="item.approval_note">
                                        {{ item.approval_note }}
                                    </p>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('user.kegiatan.show', item.id)"
                                            :title="`Rincian ${item.nama_kegiatan}`"
                                            :aria-label="`Buka rincian ${item.nama_kegiatan}`"
                                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                                        >
                                            <FontAwesomeIcon :icon="faCircleInfo" aria-hidden="true" />
                                        </Link>
                                        <Link
                                            v-if="!['disetujui', 'dipublikasikan', 'berlangsung', 'selesai', 'dibatalkan'].includes(item.status)"
                                            :href="route('user.kegiatan.edit', item.id)"
                                            :title="`Edit ${item.nama_kegiatan}`"
                                            :aria-label="`Edit proposal ${item.nama_kegiatan}`"
                                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-[#680A10] transition-colors hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                                        >
                                            <FontAwesomeIcon :icon="faPenToSquare" aria-hidden="true" />
                                        </Link>
                                        <button
                                            v-if="!['disetujui', 'dipublikasikan', 'berlangsung', 'selesai', 'dibatalkan'].includes(item.status)"
                                            type="button"
                                            :title="`Batalkan ${item.nama_kegiatan}`"
                                            :aria-label="`Batalkan proposal ${item.nama_kegiatan}`"
                                            @click="cancelProposal(item)"
                                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-amber-700 transition-colors hover:bg-amber-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                                        >
                                            <FontAwesomeIcon :icon="faBan" aria-hidden="true" />
                                        </button>
                                        <button
                                            v-if="!['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(item.status)"
                                            type="button"
                                            :title="`Hapus ${item.nama_kegiatan}`"
                                            :aria-label="`Hapus proposal ${item.nama_kegiatan}`"
                                            @click="deleteProposal(item)"
                                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-red-600 transition-colors hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                                        >
                                            <FontAwesomeIcon :icon="faTrashCan" aria-hidden="true" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!kegiatanList.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Belum ada proposal kegiatan yang Anda ajukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="kegiatanList.links"
                    :from="kegiatanList.from"
                    :to="kegiatanList.to"
                    :total="kegiatanList.total"
                    item-label="kegiatan"
                />
            </div>
        </div>
    </UserLayout>
</template>
