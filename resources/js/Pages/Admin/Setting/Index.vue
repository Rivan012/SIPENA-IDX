<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PlusIcon, TrashIcon, SettingsIcon, DocsIcon } from '@/Icons';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    kategori: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, aktif: 0, nonaktif: 0 }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const search = ref(props.filters?.search || '');
const showAddModal = ref(false);

const form = useForm({
    nama: '',
    keterangan: '',
});

const filteredKategori = computed(() => {
    if (!search.value) return props.kategori;
    const q = search.value.toLowerCase().trim();
    return props.kategori.filter(
        (item) =>
            item.nama.toLowerCase().includes(q) ||
            (item.keterangan && item.keterangan.toLowerCase().includes(q))
    );
});

const submitTambah = () => {
    form.post(route('admin.setting.kategori.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showAddModal.value = false;
        },
    });
};

const handleToggle = async (item) => {
    const aksi = item.is_active ? 'menonaktifkan' : 'mengaktifkan';
    const ok = await confirmDialog({
        title: `${item.is_active ? 'Nonaktifkan' : 'Aktifkan'} Kategori?`,
        text: `Apakah Anda yakin ingin ${aksi} kategori '${item.nama}'? ${
            item.is_active
                ? 'Kategori yang dinonaktifkan tidak akan muncul pada pilihan kegiatan baru.'
                : 'Kategori akan kembali tersedia pada opsi pemilihan kegiatan.'
        }`,
        confirmButtonText: `Ya, ${item.is_active ? 'Nonaktifkan' : 'Aktifkan'}`,
        cancelButtonText: 'Batal',
        icon: 'question',
    });

    if (ok) {
        router.patch(route('admin.setting.kategori.toggle', item.id), {}, { preserveScroll: true });
    }
};

const handleHapus = async (item) => {
    if (item.kegiatan_count > 0) {
        await confirmDialog({
            title: 'Tidak Dapat Dihapus',
            text: `Kategori '${item.nama}' sedang digunakan oleh ${item.kegiatan_count} kegiatan. Anda dapat menonaktifkannya agar tidak muncul lagi pada pengajuan baru.`,
            confirmButtonText: 'Mengerti',
            cancelButtonText: '',
            icon: 'warning',
        });
        return;
    }

    const ok = await confirmDialog({
        title: 'Hapus Kategori?',
        text: `Kategori '${item.nama}' akan dihapus permanen dari sistem. Tindakan ini tidak dapat dibatalkan.`,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        icon: 'warning',
    });

    if (ok) {
        router.delete(route('admin.setting.kategori.destroy', item.id), { preserveScroll: true });
    }
};
</script>

<template>
    <AdminLayout title="Setting — SIPENA IDX">
        <Head title="Pengaturan Kategori Kegiatan" />

        <div class="space-y-6">
            <!-- Header Page -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <SettingsIcon class="w-6 h-6 text-[#9F0E0F]" />
                        <span>Pengaturan Kategori Kegiatan</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Kelola klasifikasi jenis kegiatan edukasi, sosialisasi, dan literasi pasar modal.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#9F0E0F] hover:bg-[#850C0D] text-white text-xs font-bold transition shadow-xs cursor-pointer focus-visible:outline-2 focus-visible:outline-[#9F0E0F]"
                >
                    <PlusIcon class="w-4 h-4 shrink-0" />
                    <span>Tambah Kategori</span>
                </button>
            </div>

            <!-- Tab Navigation Header -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <div class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#680A10] text-white shadow-xs flex items-center gap-1.5">
                    <SettingsIcon class="w-3.5 h-3.5" />
                    <span>Kategori Kegiatan</span>
                </div>
                <Link
                    :href="route('admin.setting.presensi')"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5"
                >
                    <DocsIcon class="w-3.5 h-3.5" />
                    <span>Template Presensi</span>
                </Link>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL KATEGORI</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ stats.total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Semua klasifikasi terdaftar</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">KATEGORI AKTIF</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ stats.aktif }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Dapat dipilih pada formulir kegiatan</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">KATEGORI NONAKTIF</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ stats.nonaktif }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Disembunyikan dari pengajuan baru</span>
                </div>
            </div>

            <!-- Table & Search Filter -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="w-full sm:w-72">
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari kategori atau keterangan..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#9F0E0F]"
                        />
                    </div>
                    <div class="text-xs text-slate-500 font-medium self-end sm:self-center">
                        Menampilkan {{ filteredKategori.length }} dari {{ kategori.length }} kategori
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Nama Kategori</th>
                                <th class="p-3.5">Keterangan</th>
                                <th class="p-3.5 text-center">Digunakan</th>
                                <th class="p-3.5 text-center">Status</th>
                                <th class="p-3.5 text-right w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr
                                v-for="(item, idx) in filteredKategori"
                                :key="item.id"
                                class="hover:bg-slate-50/60 transition-colors"
                            >
                                <td class="p-3.5 text-center font-medium text-slate-400">
                                    {{ idx + 1 }}
                                </td>
                                <td class="p-3.5 font-bold text-slate-900">
                                    {{ item.nama }}
                                </td>
                                <td class="p-3.5 text-slate-600">
                                    {{ item.keterangan || '-' }}
                                </td>
                                <td class="p-3.5 text-center font-semibold text-slate-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ item.kegiatan_count }} kegiatan
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold',
                                            item.is_active
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                : 'bg-slate-100 text-slate-500 border border-slate-200'
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'w-1.5 h-1.5 rounded-full mr-1.5',
                                                item.is_active ? 'bg-emerald-500' : 'bg-slate-400'
                                            ]"
                                        ></span>
                                        {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right space-x-2">
                                    <!-- Toggle Status Button -->
                                    <button
                                        type="button"
                                        @click="handleToggle(item)"
                                        :class="[
                                            'px-2.5 py-1 rounded-lg text-[11px] font-bold border transition cursor-pointer',
                                            item.is_active
                                                ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100'
                                                : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                        ]"
                                    >
                                        {{ item.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <!-- Delete Button -->
                                    <button
                                        type="button"
                                        @click="handleHapus(item)"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition cursor-pointer"
                                        title="Hapus kategori"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredKategori.length === 0">
                                <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                    Tidak ada kategori yang sesuai dengan kata kunci pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Kategori -->
        <div
            v-if="showAddModal"
            @keydown.escape="showAddModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            role="dialog"
            aria-modal="true"
        >
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Tambah Kategori Baru</h2>
                    <button
                        @click="showAddModal = false"
                        type="button"
                        class="text-slate-400 hover:text-slate-600 text-sm font-bold p-1 rounded-md"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitTambah" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.nama"
                            type="text"
                            required
                            maxlength="100"
                            placeholder="Contoh: Sekolah Pasar Modal, SPM Lanjutan..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#9F0E0F]"
                        />
                        <p v-if="form.errors.nama" class="text-rose-600 text-[11px] mt-1">{{ form.errors.nama }}</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Keterangan / Deskripsi Singkat</label>
                        <textarea
                            v-model="form.keterangan"
                            rows="3"
                            maxlength="255"
                            placeholder="Penjelasan ringkas peruntukan atau format kegiatan..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#9F0E0F]"
                        ></textarea>
                        <p v-if="form.errors.keterangan" class="text-rose-600 text-[11px] mt-1">{{ form.errors.keterangan }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 rounded-xl bg-[#9F0E0F] hover:bg-[#850C0D] text-white font-bold transition shadow-xs disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Kategori' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
