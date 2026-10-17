<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { confirmDialog } from '@/Utils/sweetalert';
import {
    BoxIcon,
    RefreshIcon,
    CheckIcon,
    DocsIcon,
    TrashIcon
} from '@/Icons';

const props = defineProps({
    kegiatan: Object,
    stats: Object,
    kategori: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');
const currentKategori = ref(props.filters?.kategori || '');

const handleFilter = () => {
    router.get(route('admin.kegiatan.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
    }, { preserveState: true });
};

const confirmDelete = async (k) => {
    const ok = await confirmDialog({
        title: 'Hapus Kegiatan Ini?',
        text: `Kegiatan "${k.nama_kegiatan}" akan dihapus secara permanen dari sistem.`,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });
    if (ok) {
        router.delete(route('admin.kegiatan.destroy', k.id));
    }
};
</script>

<template>
    <AdminLayout title="Kelola Kegiatan">
        <Head title="Kelola Kegiatan — SIPENA IDX" />

        <div class="space-y-6">
            <!-- Header & Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar pelaksanaan sosialisasi, seminar, dan sekolah pasar modal IDX.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="route('admin.kegiatan.index', { export: 'csv', ...filters })"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-2xs inline-flex items-center gap-1.5"
                    >
                        <span>Unduh CSV</span>
                    </a>
                    <Link
                        :href="route('admin.kegiatan.create')"
                        class="px-4 py-2 bg-[#85131B] hover:bg-[#680A10] text-white text-xs font-bold rounded-xl transition shadow-2xs"
                    >
                        + Tambah Kegiatan
                    </Link>
                </div>
            </div>

            <!-- 4 KPI Cards (TailAdmin Metric Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Total Kegiatan Pertahun -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kegiatan Pertahun</span>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#85131B] flex items-center justify-center border border-red-100">
                            <BoxIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-slate-900 leading-none">{{ stats.total }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Tahun {{ stats.year }}</p>
                    </div>
                </div>

                <!-- Card 2: Kegiatan Berjalan -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Berjalan</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-100">
                            <RefreshIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-blue-600 leading-none">{{ stats.berjalan }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Sedang berlangsung</p>
                    </div>
                </div>

                <!-- Card 3: Selesai -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                            <CheckIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-emerald-600 leading-none">{{ stats.selesai }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Telah terlaksana</p>
                    </div>
                </div>

                <!-- Card 4: Kegiatan Menunggu Approval -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Menunggu Approval</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-100">
                            <DocsIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-amber-600 leading-none">{{ stats.menunggu }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Perlu verifikasi</p>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-md">
                    <input
                        v-model="search"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari nama kegiatan atau lokasi..."
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
                        v-model="currentStatus"
                        @change="handleFilter"
                        class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none"
                    >
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="menunggu_approval">Menunggu Approval</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="dipublikasikan">Dipublikasikan</option>
                        <option value="berlangsung">Berlangsung</option>
                        <option value="selesai">Selesai</option>
                    </select>

                    <select
                        v-model="currentKategori"
                        @change="handleFilter"
                        class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none"
                    >
                        <option value="">Semua Kategori</option>
                        <option v-for="kat in kategori" :key="kat" :value="kat">{{ kat }}</option>
                    </select>
                </div>
            </div>

            <!-- Data Table (TailAdmin Clean Table Structure) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">NAMA KEGIATAN</th>
                                <th class="py-3 px-4">KATEGORI</th>
                                <th class="py-3 px-4">JADWAL</th>
                                <th class="py-3 px-4">LOKASI</th>
                                <th class="py-3 px-4 text-center">KUOTA</th>
                                <th class="py-3 px-4 text-center">MITRA</th>
                                <th class="py-3 px-4 text-center">STATUS</th>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="k in kegiatan.data" :key="k.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <Link :href="route('admin.kegiatan.show', k.id)" class="font-bold text-slate-900 hover:text-[#85131B] leading-snug">
                                        {{ k.nama_kegiatan }}
                                    </Link>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ k.jenis_kegiatan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">{{ k.tanggal ? new Date(k.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ k.waktu_mulai ? k.waktu_mulai.substring(0, 5) : '' }} WIB</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-[200px] truncate" :title="k.lokasi">
                                    {{ k.lokasi }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap font-semibold text-slate-800">
                                    {{ k.kuota_peserta || '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-[200px] truncate" :title="k.mitra || '-'">
                                    {{ k.mitra || '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold border', k.status_class]">
                                        {{ k.status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <Link
                                            :href="route('admin.kegiatan.show', k.id)"
                                            class="px-2 py-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 font-semibold text-[11px]"
                                            title="Detail"
                                        >
                                            Detail
                                        </Link>
                                        <Link
                                            :href="route('admin.kegiatan.edit', k.id)"
                                            class="px-2 py-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-blue-600 font-semibold text-[11px]"
                                            title="Edit"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            type="button"
                                            @click="confirmDelete(k)"
                                            class="p-1 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-600 cursor-pointer"
                                            title="Hapus"
                                        >
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!kegiatan.data?.length">
                                <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data kegiatan yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="kegiatan.links"
                    :from="kegiatan.from"
                    :to="kegiatan.to"
                    :total="kegiatan.total"
                    item-label="kegiatan"
                />
            </div>
        </div>
    </AdminLayout>
</template>
