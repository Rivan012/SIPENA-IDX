<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SortableTh from '@/Components/SortableTh.vue';
import PresensiModal from '@/Components/PresensiModal.vue';
import { confirmDialog } from '@/Utils/sweetalert';
import {
    BoxIcon,
    RefreshIcon,
    CheckIcon,
    DocsIcon,
    TrashIcon,
    PdfIcon
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
const currentSort = ref(props.filters?.sort || '');
const currentDirection = ref(props.filters?.direction || 'asc');

const showPresensiModal = ref(false);
const selectedKegiatanForPresensi = ref(null);

const openPresensi = (item) => {
    selectedKegiatanForPresensi.value = item;
    showPresensiModal.value = true;
};

const exportPdfUrl = computed(() => {
    return route('admin.kegiatan.index', {
        export: 'pdf',
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    });
});

const handleFilter = () => {
    router.get(route('admin.kegiatan.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    }, { preserveState: true });
};

const filterByStatus = (status) => {
    if (currentStatus.value === status) {
        currentStatus.value = '';
    } else {
        currentStatus.value = status;
    }
    handleFilter();
};

const statusCards = computed(() => [
    {
        key: '',
        title: 'Semua Status',
        count: props.stats?.total ?? props.stats?.all ?? 0,
        badgeText: `Tahun ${props.stats?.year || 2026}`,
        badgeClass: 'bg-rose-50 text-[#85131B] border-rose-200/80',
        dotClass: 'bg-[#85131B]',
        desc: 'Agenda kegiatan tercatat',
    },
    {
        key: 'disetujui',
        title: 'Disetujui',
        count: props.stats?.disetujui ?? 0,
        badgeText: 'Disetujui',
        badgeClass: 'bg-cyan-50 text-cyan-800 border-cyan-200/80',
        dotClass: 'bg-cyan-600',
        desc: 'Siap dijadwalkan',
    },
    {
        key: 'dipublikasikan',
        title: 'Terjadwal',
        count: props.stats?.terjadwal ?? 0,
        badgeText: 'Terjadwal',
        badgeClass: 'bg-sky-50 text-sky-800 border-sky-200/80',
        dotClass: 'bg-sky-600',
        desc: 'Siap dilaksanakan',
    },
    {
        key: 'berlangsung',
        title: 'Berjalan',
        count: props.stats?.berjalan ?? 0,
        badgeText: 'Aktif',
        badgeClass: 'bg-blue-50 text-blue-800 border-blue-200/80',
        dotClass: 'bg-blue-600',
        desc: 'Sedang berlangsung',
    },
    {
        key: 'selesai',
        title: 'Selesai',
        count: props.stats?.selesai ?? 0,
        badgeText: 'Realisasi',
        badgeClass: 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
        dotClass: 'bg-emerald-600',
        desc: 'Telah terlaksana',
    },
    {
        key: 'menunggu_approval',
        title: 'Review Usulan',
        count: props.stats?.menunggu ?? 0,
        badgeText: 'Menunggu',
        badgeClass: 'bg-amber-50 text-amber-800 border-amber-200/80',
        dotClass: 'bg-amber-600',
        desc: 'Perlu verifikasi admin',
    },
]);

const handleSort = (field) => {
    if (currentSort.value === field) {
        currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.value = field;
        currentDirection.value = 'asc';
    }
    handleFilter();
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

const isHariH = (tanggal) => {
    if (!tanggal) return false;
    const d = new Date();
    const today = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    return String(tanggal).substring(0, 10) === today;
};

const changeStatus = async (k, newStatus) => {
    if (newStatus === 'selesai' && (k.status !== 'berlangsung' || !isHariH(k.tanggal))) {
        return;
    }
    const labels = {
        dipublikasikan: 'Terjadwal',
        berlangsung: 'Berlangsung',
        selesai: 'Selesai',
    };
    const ok = await confirmDialog({
        title: `Ubah Status ke ${labels[newStatus]}?`,
        text: `Ubah status kegiatan "${k.nama_kegiatan}" menjadi ${labels[newStatus]}?`,
        confirmButtonText: `Ya, ${labels[newStatus]}`,
        cancelButtonText: 'Batal',
        icon: 'question',
        confirmButtonColor: newStatus === 'selesai' ? '#059669' : (newStatus === 'berlangsung' ? '#DC2626' : '#0284C7'),
    });
    if (ok) {
        router.patch(route('admin.kegiatan.status', k.id), { status: newStatus });
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
                        :href="exportPdfUrl"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-2xs inline-flex items-center gap-1.5"
                    >
                        <PdfIcon class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                        <span>Ekspor PDF</span>
                    </a>
                    <Link
                        :href="route('admin.kegiatan.create')"
                        class="px-4 py-2 bg-[#85131B] hover:bg-[#680A10] text-white text-xs font-bold rounded-xl transition shadow-2xs"
                    >
                        + Tambah Kegiatan
                    </Link>
                </div>
            </div>

            <!-- Status KPI Cards (Interactive) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                <button
                    v-for="card in statusCards"
                    :key="card.key"
                    type="button"
                    @click="filterByStatus(card.key)"
                    :class="[
                        'text-left w-full transition-all duration-150 cursor-pointer select-none rounded-2xl border p-4 sm:p-5 flex flex-col justify-between hover:-translate-y-0.5 hover:shadow-md focus:outline-none',
                        (currentStatus === card.key || (!currentStatus && card.key === ''))
                            ? 'border-[#85131B] ring-2 ring-[#85131B]/20 bg-rose-50/20 shadow-xs'
                            : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs'
                    ]"
                >
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">{{ card.title }}</span>
                        <span :class="['shrink-0 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-semibold border', card.badgeClass]">
                            <span :class="['w-1.5 h-1.5 rounded-full', card.dotClass]"></span>
                            {{ card.badgeText }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ card.count }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1.5 truncate">{{ card.desc }}</p>
                    </div>
                </button>
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
                                <SortableTh field="nama_kegiatan" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    NAMA KEGIATAN
                                </SortableTh>
                                <th class="py-3 px-4">KATEGORI</th>
                                <SortableTh field="tanggal" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    JADWAL
                                </SortableTh>
                                <SortableTh field="lokasi" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    LOKASI
                                </SortableTh>
                                <SortableTh field="kuota_peserta" align="center" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    KUOTA
                                </SortableTh>
                                <SortableTh field="mitra" align="center" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    MITRA
                                </SortableTh>
                                <SortableTh field="status" align="center" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    STATUS
                                </SortableTh>
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
                                        <button
                                            v-if="k.status === 'disetujui'"
                                            type="button"
                                            @click="changeStatus(k, 'dipublikasikan')"
                                            class="px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold text-[11px] shadow-2xs cursor-pointer transition"
                                            title="Ubah status ke Terjadwal"
                                        >
                                            Terjadwal
                                        </button>
                                        <button
                                            v-if="k.status === 'dipublikasikan'"
                                            type="button"
                                            @click="changeStatus(k, 'berlangsung')"
                                            class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-[11px] shadow-2xs cursor-pointer transition"
                                            title="Ubah status ke Berlangsung"
                                        >
                                            Berlangsung
                                        </button>
                                        <button
                                            v-if="k.status === 'berlangsung' && isHariH(k.tanggal)"
                                            type="button"
                                            @click="changeStatus(k, 'selesai')"
                                            class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-2xs cursor-pointer transition"
                                            title="Tandai kegiatan Selesai"
                                        >
                                            Selesai
                                        </button>

                                        <button
                                            v-if="['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(k.status)"
                                            type="button"
                                            @click="openPresensi(k)"
                                            class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-semibold text-[11px] shadow-2xs cursor-pointer"
                                            title="Cetak Presensi"
                                        >
                                            Cetak Presensi
                                        </button>
                                        <Link
                                            :href="route('admin.kegiatan.show', k.id)"
                                            class="px-2 py-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 font-semibold text-[11px]"
                                            title="Detail"
                                        >
                                            Detail
                                        </Link>
                                        <Link
                                            v-if="!['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(k.status)"
                                            :href="route('admin.kegiatan.edit', k.id)"
                                            class="px-2 py-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-blue-600 font-semibold text-[11px]"
                                            title="Edit"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="!['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(k.status)"
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

            <!-- Presensi Modal -->
            <PresensiModal
                :show="showPresensiModal"
                :kegiatan="selectedKegiatanForPresensi"
                @close="showPresensiModal = false"
            />
        </div>
    </AdminLayout>
</template>
