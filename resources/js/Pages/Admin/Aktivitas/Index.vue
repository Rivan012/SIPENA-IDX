<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SortableTh from '@/Components/SortableTh.vue';
import { PdfIcon } from '@/Icons';

const props = defineProps({
    aktivitas: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentRole = ref(props.filters?.role || '');
const currentSort = ref(props.filters?.sort || 'created_at');
const currentDirection = ref(props.filters?.direction || 'desc');

const exportPdfUrl = computed(() => {
    return route('admin.aktivitas.export', {
        search: search.value || undefined,
        role: currentRole.value || undefined,
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    });
});

const handleFilter = () => {
    router.get(route('admin.aktivitas.index'), {
        search: search.value || undefined,
        role: currentRole.value || undefined,
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
    if (aksi.includes('tambah') || aksi.includes('pengajuan')) return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-slate-100 text-slate-700 border-slate-200';
};
</script>

<template>
    <AdminLayout title="Riwayat Aktivitas — SIPENA IDX">
        <Head title="Riwayat Aktivitas" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Aktivitas</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Audit log dan catatan riwayat tindakan administrator serta mitra.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="exportPdfUrl"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs inline-flex items-center gap-1.5">
                        <PdfIcon class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                        <span>Ekspor PDF</span>
                    </a>
                </div>
            </div>

            <!-- KPI Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL AKTIVITAS</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ stats.total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Log tercatat</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TINDAKAN ADMIN</span>
                    <span class="text-2xl font-bold text-[#85131B] mt-1 block">{{ stats.admin }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Oleh tim administrator</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">AKTIVITAS MITRA</span>
                    <span class="text-2xl font-bold text-blue-600 mt-1 block">{{ stats.mitra }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Oleh mitra pengusul</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">HARI INI</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ stats.today }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Aktivitas 24 jam terakhir</span>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-md">
                    <input
                        v-model="search"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari aktivitas, nama aktor, aksi..."
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#85131B]/20"
                    />
                    <button
                        type="button"
                        @click="handleFilter"
                        class="px-4 py-2 bg-[#85131B] text-white text-xs font-bold rounded-xl hover:bg-[#680A10] cursor-pointer"
                    >
                        Cari
                    </button>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select
                        v-model="currentRole"
                        @change="handleFilter"
                        class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none"
                    >
                        <option value="">Semua Peran</option>
                        <option value="admin">Administrator</option>
                        <option value="mitra">Mitra</option>
                        <option value="sistem">Sistem</option>
                    </select>
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
                                <SortableTh field="aktor_nama" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    AKTOR / PELAKU
                                </SortableTh>
                                <th class="py-3 px-4">PERAN</th>
                                <SortableTh field="aksi" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    AKSI
                                </SortableTh>
                                <th class="py-3 px-4">DESKRIPSI AKTIVITAS</th>
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
                                <td class="py-3.5 px-4 whitespace-nowrap font-bold text-slate-900">
                                    {{ act.aktor_nama }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider',
                                        act.aktor_role === 'admin' ? 'bg-rose-50 text-[#85131B] border-rose-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                                    ]">
                                        {{ act.aktor_role }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span :class="['px-2 py-0.5 rounded-full text-[10px] font-semibold border', getActionBadge(act.aksi)]">
                                        {{ act.aksi.replace(/_/g, ' ') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 max-w-md">
                                    {{ act.deskripsi }}
                                </td>
                            </tr>
                            <tr v-if="!aktivitas.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada riwayat aktivitas yang tercatat.
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
    </AdminLayout>
</template>
