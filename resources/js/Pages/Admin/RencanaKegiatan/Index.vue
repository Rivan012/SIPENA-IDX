<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    rencanaList: Object,
    stats: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');
const currentJenis = ref(props.filters?.jenis || '');

const handleFilter = () => {
    router.get(route('admin.rencana-kegiatan.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        jenis: currentJenis.value || undefined,
    }, { preserveState: true });
};

const showDeleteModal = ref(false);
const deleteTarget = ref(null);

const confirmDelete = (item) => {
    deleteTarget.value = item;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (deleteTarget.value) {
        router.delete(route('admin.rencana-kegiatan.destroy', deleteTarget.value.id), {
            onFinish: () => {
                showDeleteModal.value = false;
                deleteTarget.value = null;
            },
        });
    }
};

const ajukanRencana = (item) => {
    if (confirm(`Ajukan rencana "${item.nama_kegiatan}" untuk peninjauan anggaran?`)) {
        router.post(route('admin.rencana-kegiatan.ajukan', item.id));
    }
};
</script>

<template>
    <AdminLayout title="Anggaran & RKAP — SIPENA IDX">
        <Head title="Anggaran & RKAP" />

        <div class="space-y-5">
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Anggaran &amp; Rencana Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring pagu alokasi RKAP tahunan dan realisasi biaya pos kegiatan.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="route('admin.rencana-kegiatan.export', filters)"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs">
                        &darr; Ekspor CSV
                    </a>
                    <Link :href="route('admin.rencana-kegiatan.create')"
                          class="px-4 py-2 bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold rounded-xl transition shadow-xs">
                        + Tambah Rencana
                    </Link>
                </div>
            </div>

            <!-- 4 KPI Cards Pagu RKAP -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PAGU RKAP TAHUNAN</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block truncate" :title="stats.pagu_rkap_formatted">{{ stats.pagu_rkap_formatted }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Tahun Anggaran {{ stats.selected_year }}</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">DISETUJUI / TERPAKAI</span>
                    <span class="text-xl font-bold text-emerald-600 mt-1 block truncate" :title="stats.total_disetujui_formatted">{{ stats.total_disetujui_formatted }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">{{ stats.count_disetujui }} Pos disetujui</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SISA PAGU ANGGARAN</span>
                    <span class="text-xl font-bold text-blue-600 mt-1 block truncate" :title="stats.sisa_pagu_formatted">{{ stats.sisa_pagu_formatted }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Alokasi tersisa</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">% SERAPAN ANGGARAN</span>
                    <span class="text-xl font-bold text-[#680A10] mt-1 block">{{ stats.persen_penyerapan }}%</span>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-[#680A10] h-1.5 rounded-full" :style="{ width: `${stats.persen_penyerapan}%` }"></div>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-md">
                    <input v-model="search"
                           @keyup.enter="handleFilter"
                           type="text"
                           placeholder="Cari program kerja, pos belanja..."
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
                        <option value="draft">Draft</option>
                        <option value="diajukan">Diajukan</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>

                    <select v-model="currentJenis" @change="handleFilter"
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
                                <th class="py-3 px-4">KODE & PROGRAM</th>
                                <th class="py-3 px-4">KATEGORI</th>
                                <th class="py-3 px-4">TANGGAL RENCANA</th>
                                <th class="py-3 px-4">LOKASI & TARGET</th>
                                <th class="py-3 px-4 text-right">ALOKASI BIAYA</th>
                                <th class="py-3 px-4 text-center">STATUS</th>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="r in rencanaList.data" :key="r.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-mono text-[10px] font-bold text-slate-400 block">{{ r.kode }}</span>
                                    <Link :href="route('admin.rencana-kegiatan.show', r.id)" class="font-bold text-slate-900 hover:text-[#680A10] leading-snug">
                                        {{ r.nama_kegiatan }}
                                    </Link>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ r.jenis_kegiatan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        {{ r.tanggal_rencana ? new Date(r.tanggal_rencana).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-[180px] truncate">
                                    <div>{{ r.detail_kegiatan?.lokasi || '-' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Target: {{ r.detail_kegiatan?.target_peserta || 100 }} Peserta</div>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-slate-900">
                                    {{ r.formatted_anggaran }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', r.status_class]">
                                        {{ r.status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button v-if="r.status === 'draft'"
                                                type="button"
                                                @click="ajukanRencana(r)"
                                                class="px-2 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-[10px] border border-amber-200">
                                            Ajukan &uarr;
                                        </button>
                                        <Link :href="route('admin.rencana-kegiatan.show', r.id)"
                                              class="p-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </Link>
                                        <Link :href="route('admin.rencana-kegiatan.edit', r.id)"
                                              class="p-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-blue-600" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </Link>
                                        <button type="button" @click="confirmDelete(r)"
                                                class="p-1 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-600" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!rencanaList.data?.length">
                                <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                    Tidak ada rencana kegiatan atau alokasi anggaran yang sesuai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="rencanaList.links"
                    :from="rencanaList.from"
                    :to="rencanaList.to"
                    :total="rencanaList.total"
                    item-label="pos anggaran"
                />
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="text-center">
                    <h3 class="text-base font-bold text-slate-900">Hapus Rencana Anggaran?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Rencana <strong>{{ deleteTarget?.nama_kegiatan }}</strong> akan dihapus permanen.
                    </p>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showDeleteModal = false" class="w-1/2 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="button" @click="executeDelete" class="w-1/2 py-2 rounded-xl bg-red-600 text-xs font-semibold text-white hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
