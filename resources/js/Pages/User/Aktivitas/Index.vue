<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SortableTh from '@/Components/SortableTh.vue';

const props = defineProps({
    aktivitas: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentSort = ref(props.filters?.sort || 'created_at');
const currentDirection = ref(props.filters?.direction || 'desc');

const handleFilter = () => {
    router.get(route('user.aktivitas.index'), {
        search: search.value || undefined,
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    }, { preserveState: true });
};

const handleSort = (field) => {
    if (currentSort.value === field) {
        currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.value = field;
        currentDirection.value = 'asc';
    }
    handleFilter();
};

const getActionBadge = (aksi) => {
    if (aksi.includes('setuju') || aksi.includes('approval')) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    if (aksi.includes('batal') || aksi.includes('hapus') || aksi.includes('tolak')) return 'bg-rose-50 text-rose-700 border-rose-200';
    if (aksi.includes('revisi')) return 'bg-amber-50 text-amber-700 border-amber-200';
    if (aksi.includes('pengajuan') || aksi.includes('daftar')) return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-slate-100 text-slate-700 border-slate-200';
};
</script>

<template>
    <UserLayout title="Riwayat Aktivitas — SIPENA IDX">
        <Head title="Riwayat Aktivitas Saya" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Aktivitas</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat interaksi, pengajuan proposal, dan pembaruan kegiatan Anda.</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL AKTIVITAS</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ stats.total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Log aktivitas Anda</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PENGAJUAN PROPOSAL</span>
                    <span class="text-2xl font-bold text-[#680A10] mt-1 block">{{ stats.proposal }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Tindakan terkait proposal</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">UPDATE APPROVAL</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ stats.approval }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Keputusan dari admin</span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full max-w-md">
                    <input
                        v-model="search"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari riwayat aktivitas..."
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20"
                    />
                    <button
                        type="button"
                        @click="handleFilter"
                        class="px-4 py-2 bg-[#680A10] text-white text-xs font-semibold rounded-xl hover:bg-[#54080D] cursor-pointer"
                    >
                        Cari
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <SortableTh field="created_at" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    WAKTU
                                </SortableTh>
                                <SortableTh field="aksi" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    JENIS TINDAKAN
                                </SortableTh>
                                <th class="py-3 px-4">DESKRIPSI AKTIVITAS</th>
                                <th class="py-3 px-4">PROGRAM TERKAIT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="act in aktivitas.data" :key="act.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500">
                                    <div class="font-semibold text-slate-800">
                                        {{ act.created_at ? new Date(act.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ act.created_at ? new Date(act.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '' }} WIB
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-semibold border', getActionBadge(act.aksi)]">
                                        {{ act.aksi.replace(/_/g, ' ') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 max-w-md">
                                    {{ act.deskripsi }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-slate-600">
                                    <Link v-if="act.kegiatan" :href="route('user.kegiatan.show', act.kegiatan.id)" class="text-[#680A10] hover:underline font-semibold">
                                        {{ act.kegiatan.nama_kegiatan }}
                                    </Link>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                            </tr>
                            <tr v-if="!aktivitas.data?.length">
                                <td colspan="4" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada catatan aktivitas untuk akun Anda.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="aktivitas.links"
                    :from="aktivitas.from"
                    :to="aktivitas.to"
                    :total="aktivitas.total"
                    item-label="aktivitas"
                />
            </div>
        </div>
    </UserLayout>
</template>
