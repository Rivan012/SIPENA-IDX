<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { DocsIcon, PlusIcon, RefreshIcon, SettingsIcon, TrashIcon } from '@/Icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faArrowDown,
    faArrowUp,
    faCheck,
    faEye,
    faFloppyDisk,
    faRotateLeft,
} from '@fortawesome/free-solid-svg-icons';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({ columns: [], orientation: 'portrait' }),
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, aktif: 0, nonaktif: 0, orientation: 'portrait' }),
    },
});

const columns = ref(props.settings.columns.map((c) => ({ ...c })));
const orientation = ref(props.settings.orientation || 'portrait');
const newColumnName = ref('');
const notificationMsg = ref('');

const activeColumns = computed(() => columns.value.filter((c) => c.enabled));

const addColumn = () => {
    const name = newColumnName.value.trim();
    if (!name) return;

    const upper = name.toUpperCase();
    if (columns.value.some((c) => c.label.toUpperCase() === upper)) {
        notificationMsg.value = `Kolom "${upper}" sudah ada di dalam template.`;
        return;
    }

    columns.value.push({
        id: `custom_${Date.now()}`,
        label: upper,
        enabled: true,
        required: false,
        isDefault: false,
    });

    newColumnName.value = '';
    notificationMsg.value = `Kolom "${upper}" berhasil ditambahkan.`;
    setTimeout(() => { notificationMsg.value = ''; }, 3000);
};

const removeColumn = (index) => {
    columns.value.splice(index, 1);
};

const moveUp = (index) => {
    if (index <= 0) return;
    const temp = columns.value[index];
    columns.value[index] = columns.value[index - 1];
    columns.value[index - 1] = temp;
};

const moveDown = (index) => {
    if (index >= columns.value.length - 1) return;
    const temp = columns.value[index];
    columns.value[index] = columns.value[index + 1];
    columns.value[index + 1] = temp;
};

const isSaving = ref(false);

const saveSettings = () => {
    if (activeColumns.value.length === 0) {
        notificationMsg.value = 'Minimal harus ada 1 kolom yang aktif.';
        return;
    }

    isSaving.value = true;
    router.post(route('admin.setting.presensi.update'), {
        columns: columns.value,
        orientation: orientation.value,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isSaving.value = false;
        },
    });
};

const handleReset = async () => {
    const ok = await confirmDialog({
        title: 'Kembalikan Template ke Standar?',
        text: 'Template kolom presensi akan dikembalikan ke pengaturan bawaan awal sistem SIPENA.',
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal',
        icon: 'warning',
    });

    if (ok) {
        router.post(route('admin.setting.presensi.reset'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                columns.value = props.settings.columns.map((c) => ({ ...c }));
                orientation.value = 'portrait';
            },
        });
    }
};
</script>

<template>
    <AdminLayout title="Setting Template Presensi — SIPENA IDX">
        <Head title="Pengaturan Template Presensi" />

        <div class="space-y-6">
            <!-- Header Page -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <DocsIcon class="w-6 h-6 text-[#680A10]" />
                        <span>Pengaturan Template Presensi</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Sesuaikan format kolom bawaan daftar hadir peserta yang akan dicetak pada seluruh kegiatan BEI.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="handleReset"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer flex items-center gap-1.5"
                    >
                        <FontAwesomeIcon :icon="faRotateLeft" class="text-xs text-slate-400" />
                        <span>Reset Standar</span>
                    </button>
                    <button
                        type="button"
                        @click="saveSettings"
                        :disabled="isSaving || activeColumns.length === 0"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-bold transition shadow-xs cursor-pointer disabled:opacity-50"
                    >
                        <FontAwesomeIcon :icon="faFloppyDisk" />
                        <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Template' }}</span>
                    </button>
                </div>
            </div>

            <!-- Tab Navigation Header -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <Link
                    :href="route('admin.setting.index')"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5"
                >
                    <SettingsIcon class="w-3.5 h-3.5" />
                    <span>Kategori Kegiatan</span>
                </Link>
                <div class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#680A10] text-white shadow-xs flex items-center gap-1.5">
                    <DocsIcon class="w-3.5 h-3.5" />
                    <span>Template Presensi</span>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL KOLOM TEMPLATE</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ columns.length }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Kolom terdaftar dalam sistem</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">KOLOM AKTIF DICETAK</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ activeColumns.length }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Akan muncul di formulir cetak presensi</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">ORIENTASI BAWAAN</span>
                    <span class="text-2xl font-black text-[#680A10] mt-1 block uppercase">{{ orientation }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">{{ orientation === 'portrait' ? '25 Baris Peserta' : '15 Baris Peserta' }}</span>
                </div>
            </div>

            <!-- Alert notification -->
            <div
                v-if="notificationMsg"
                class="p-3 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs flex items-center gap-2"
            >
                <FontAwesomeIcon :icon="faCheck" class="text-amber-600" />
                <span>{{ notificationMsg }}</span>
            </div>

            <!-- Main Layout (Grid 2 Kolom: Pengaturan & Live Preview) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Kolom Kiri: Form & Daftar Kolom -->
                <div class="lg:col-span-6 space-y-4">
                    <!-- Card Pengaturan Orientasi -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                        <label class="block text-xs font-bold text-slate-700">Orientasi Kertas Bawaan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                @click="orientation = 'portrait'"
                                :class="[
                                    'p-3 rounded-xl border text-left flex items-center justify-between transition cursor-pointer',
                                    orientation === 'portrait'
                                        ? 'border-[#680A10] bg-rose-50/40 ring-1 ring-[#680A10]'
                                        : 'border-slate-200 hover:bg-slate-50'
                                ]"
                            >
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Portrait</span>
                                    <span class="text-[11px] text-slate-500 block">A4 Tegak (25 Baris)</span>
                                </div>
                                <span v-if="orientation === 'portrait'" class="h-2 w-2 rounded-full bg-[#680A10]"></span>
                            </button>

                            <button
                                type="button"
                                @click="orientation = 'landscape'"
                                :class="[
                                    'p-3 rounded-xl border text-left flex items-center justify-between transition cursor-pointer',
                                    orientation === 'landscape'
                                        ? 'border-[#680A10] bg-rose-50/40 ring-1 ring-[#680A10]'
                                        : 'border-slate-200 hover:bg-slate-50'
                                ]"
                            >
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Landscape</span>
                                    <span class="text-[11px] text-slate-500 block">A4 Mendatar (15 Baris)</span>
                                </div>
                                <span v-if="orientation === 'landscape'" class="h-2 w-2 rounded-full bg-[#680A10]"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Card Manajemen Kolom -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">Daftar Kolom Presensi</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Centang untuk mengaktifkan, uncentang untuk menonaktifkan.</p>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                {{ activeColumns.length }} Terpilih
                            </span>
                        </div>

                        <!-- List Items -->
                        <div class="space-y-2">
                            <div
                                v-for="(col, idx) in columns"
                                :key="col.id"
                                :class="[
                                    'flex items-center justify-between p-3 rounded-xl border transition',
                                    col.enabled
                                        ? 'bg-white border-slate-200 shadow-2xs'
                                        : 'bg-slate-50/80 border-dashed border-slate-200 opacity-60'
                                ]"
                            >
                                <label class="flex items-center gap-3 flex-1 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        v-model="col.enabled"
                                        class="rounded border-slate-300 text-[#680A10] focus:ring-[#680A10]/20 h-4 w-4 cursor-pointer"
                                    />
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-slate-900">{{ col.label }}</span>
                                            <span
                                                v-if="col.required"
                                                class="text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.2 rounded"
                                            >
                                                Wajib
                                            </span>
                                            <span
                                                v-else-if="!col.isDefault"
                                                class="text-[9px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded"
                                            >
                                                Kustom
                                            </span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Action Buttons: Up, Down, Delete -->
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        @click="moveUp(idx)"
                                        :disabled="idx === 0"
                                        class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 disabled:opacity-20 cursor-pointer"
                                        title="Pindah ke atas"
                                    >
                                        <FontAwesomeIcon :icon="faArrowUp" class="text-xs" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="moveDown(idx)"
                                        :disabled="idx === columns.length - 1"
                                        class="h-7 w-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 disabled:opacity-20 cursor-pointer"
                                        title="Pindah ke bawah"
                                    >
                                        <FontAwesomeIcon :icon="faArrowDown" class="text-xs" />
                                    </button>
                                    <button
                                        v-if="!col.required"
                                        type="button"
                                        @click="removeColumn(idx)"
                                        class="h-7 w-7 rounded-lg flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 transition cursor-pointer"
                                        title="Hapus kolom ini"
                                    >
                                        <TrashIcon class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Input Tambah Kolom Baru -->
                        <div class="pt-2 border-t border-slate-100">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Tambah Kolom Tambahan</label>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model="newColumnName"
                                    @keyup.enter="addColumn"
                                    type="text"
                                    placeholder="Contoh: JABATAN / NIK / ASAL KAMPUS..."
                                    class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#680A10]/20"
                                />
                                <button
                                    type="button"
                                    @click="addColumn"
                                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
                                >
                                    <PlusIcon class="w-3.5 h-3.5" />
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Live Preview Formulir Presensi -->
                <div class="lg:col-span-6 space-y-3">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <FontAwesomeIcon :icon="faEye" class="text-slate-400" />
                                <h3 class="text-xs font-bold text-slate-800">Pratinjau Hasil Cetak Formulir</h3>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono uppercase">
                                Format: {{ orientation }} (A4)
                            </span>
                        </div>

                        <!-- Paper Sheet Mockup -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 font-sans text-[10px] text-slate-800 space-y-3 shadow-inner">
                            <!-- Kop Surat Mockup -->
                            <div class="border border-slate-700 p-2.5 flex items-center gap-3 bg-white">
                                <div class="w-12 h-12 border-r border-slate-700 pr-2 flex items-center justify-center shrink-0">
                                    <img src="/images/logo-emblem.png" alt="IDX Logo" class="h-9 w-9 object-contain" />
                                </div>
                                <div class="flex-1 text-center">
                                    <div class="font-bold text-[11px] uppercase tracking-wide">FORMULIR DAFTAR HADIR PESERTA</div>
                                    <div class="text-[9px] text-slate-500 mt-0.5">KANTOR PERWAKILAN BEI BENGKULU</div>
                                </div>
                            </div>

                            <!-- Tabel Roster Mockup -->
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-700 text-[9px] bg-white text-center">
                                    <thead>
                                        <tr class="bg-slate-100">
                                            <th class="border border-slate-700 py-1 px-1.5 w-6">No.</th>
                                            <th
                                                v-for="col in activeColumns"
                                                :key="col.id"
                                                class="border border-slate-700 py-1 px-2"
                                            >
                                                {{ col.label }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="r in (orientation === 'landscape' ? 8 : 12)" :key="r" class="h-4.5">
                                            <td class="border border-slate-700 text-slate-400">{{ r }}</td>
                                            <td v-for="col in activeColumns" :key="col.id" class="border border-slate-700"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer PDP Mockup -->
                            <div class="border border-slate-400 p-2 bg-white text-[8px] text-slate-500 space-y-1">
                                <div class="font-bold text-slate-700">PERLINDUNGAN DATA PRIBADI (PDPA)</div>
                                <p class="line-clamp-2">Data pribadi yang diisi pada formulir ini hanya digunakan untuk keperluan administrasi kegiatan BEI Bengkulu dan tidak disebarluaskan kepada pihak ketiga.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
