<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faArrowDown,
    faArrowUp,
    faArrowsRotate,
    faCheck,
    faFloppyDisk,
    faPlus,
    faPrint,
    faTrashCan,
    faXmark,
} from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    kegiatan: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const STORAGE_KEY = 'sipena_presensi_custom_template';

const DEFAULT_COLUMNS = [
    { id: 'name', label: 'NAMA', enabled: true, isDefault: true, required: true },
    { id: 'instansi', label: 'INSTANSI / ASAL', enabled: true, isDefault: true },
    { id: 'email', label: 'EMAIL', enabled: true, isDefault: true },
    { id: 'phone', label: 'NO. HP / WA', enabled: true, isDefault: true },
    { id: 'signature', label: 'TTD', enabled: true, isDefault: true },
    { id: 'keterangan', label: 'KETERANGAN', enabled: false, isDefault: true },
];

const columns = ref([]);
const orientation = ref('portrait');
const newColumnName = ref('');
const notificationMsg = ref('');

const loadTemplate = () => {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) {
                columns.value = parsed.map((col) => ({ ...col }));
                return;
            }
        }
    } catch {
        // ignore parse error and fallback
    }
    columns.value = DEFAULT_COLUMNS.map((col) => ({ ...col }));
};

onMounted(() => {
    loadTemplate();
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        loadTemplate();
        notificationMsg.value = '';
        newColumnName.value = '';
    }
});

const activeColumns = computed(() => columns.value.filter((c) => c.enabled));

const addColumn = () => {
    const name = newColumnName.value.trim();
    if (!name) return;

    const upper = name.toUpperCase();
    if (columns.value.some((c) => c.label.toUpperCase() === upper)) {
        notificationMsg.value = `Kolom "${upper}" sudah ada.`;
        return;
    }

    columns.value.push({
        id: `custom_${Date.now()}`,
        label: upper,
        enabled: true,
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

const saveTemplate = () => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(columns.value));
        notificationMsg.value = 'Pengaturan kolom berhasil disimpan sebagai template bawaan.';
        setTimeout(() => { notificationMsg.value = ''; }, 3000);
    } catch {
        notificationMsg.value = 'Gagal menyimpan template ke browser.';
    }
};

const resetTemplate = () => {
    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch {
        // ignore
    }
    columns.value = DEFAULT_COLUMNS.map((col) => ({ ...col }));
    notificationMsg.value = 'Template dikembalikan ke pengaturan standar.';
    setTimeout(() => { notificationMsg.value = ''; }, 3000);
};

const submitPrint = () => {
    if (!props.kegiatan || !props.kegiatan.id) return;
    if (activeColumns.value.length === 0) {
        notificationMsg.value = 'Pilih minimal satu kolom untuk dicetak.';
        return;
    }

    const params = new URLSearchParams();
    params.append('orientation', orientation.value);
    activeColumns.value.forEach((col) => {
        params.append('columns[]', col.label);
    });

    const targetUrl = route('admin.kegiatan.presensi.download', props.kegiatan.id) + '?' + params.toString();
    window.open(targetUrl, '_blank');
    emit('close');
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        @keydown.esc="emit('close')"
    >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <!-- Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <FontAwesomeIcon :icon="faPrint" class="text-[#680A10]" />
                        Cetak Formulir Presensi
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5 truncate max-w-sm">
                        {{ kegiatan?.nama_kegiatan || 'Pilih format dan kolom yang dicetak' }}
                    </p>
                </div>
                <button
                    type="button"
                    @click="emit('close')"
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                >
                    <FontAwesomeIcon :icon="faXmark" />
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                <!-- Alert Feedback -->
                <div
                    v-if="notificationMsg"
                    class="p-2.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200/80 text-[11px] flex items-center gap-2"
                >
                    <FontAwesomeIcon :icon="faCheck" class="text-amber-600" />
                    <span>{{ notificationMsg }}</span>
                </div>

                <!-- Orientasi Kertas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Orientasi Kertas</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <button
                            type="button"
                            @click="orientation = 'portrait'"
                            :class="[
                                'p-2.5 rounded-xl border text-left flex items-center justify-between transition cursor-pointer',
                                orientation === 'portrait'
                                    ? 'border-[#680A10] bg-rose-50/40 ring-1 ring-[#680A10]'
                                    : 'border-slate-200 hover:bg-slate-50'
                            ]"
                        >
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Portrait</span>
                                <span class="text-[10px] text-slate-500 block">A4 Tegak (25 Baris)</span>
                            </div>
                            <span
                                v-if="orientation === 'portrait'"
                                class="h-2 w-2 rounded-full bg-[#680A10]"
                            ></span>
                        </button>

                        <button
                            type="button"
                            @click="orientation = 'landscape'"
                            :class="[
                                'p-2.5 rounded-xl border text-left flex items-center justify-between transition cursor-pointer',
                                orientation === 'landscape'
                                    ? 'border-[#680A10] bg-rose-50/40 ring-1 ring-[#680A10]'
                                    : 'border-slate-200 hover:bg-slate-50'
                            ]"
                        >
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Landscape</span>
                                <span class="text-[10px] text-slate-500 block">A4 Mendatar (15 Baris)</span>
                            </div>
                            <span
                                v-if="orientation === 'landscape'"
                                class="h-2 w-2 rounded-full bg-[#680A10]"
                            ></span>
                        </button>
                    </div>
                </div>

                <!-- Pengaturan Kolom (Checklist / Tambah / Kurang) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">
                            Pilih Kolom Presensi ({{ activeColumns.length }} aktif)
                        </label>
                        <div class="flex items-center gap-1.5 text-[11px]">
                            <button
                                type="button"
                                @click="saveTemplate"
                                class="text-slate-600 hover:text-[#680A10] font-medium flex items-center gap-1 px-1.5 py-0.5 rounded hover:bg-slate-100 transition cursor-pointer"
                                title="Simpan sebagai template default"
                            >
                                <FontAwesomeIcon :icon="faFloppyDisk" class="text-[10px]" />
                                Simpan Setting
                            </button>
                            <span class="text-slate-300">|</span>
                            <button
                                type="button"
                                @click="resetTemplate"
                                class="text-slate-500 hover:text-slate-700 font-medium flex items-center gap-1 px-1.5 py-0.5 rounded hover:bg-slate-100 transition cursor-pointer"
                                title="Kembalikan ke kolom standar"
                            >
                                <FontAwesomeIcon :icon="faArrowsRotate" class="text-[10px]" />
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Column list items -->
                    <div class="space-y-1.5 border border-slate-200 rounded-xl p-2 bg-slate-50/40">
                        <div
                            v-for="(col, idx) in columns"
                            :key="col.id"
                            :class="[
                                'flex items-center justify-between p-2 rounded-lg border transition',
                                col.enabled
                                    ? 'bg-white border-slate-200 shadow-2xs'
                                    : 'bg-slate-100/70 border-dashed border-slate-200 opacity-60'
                            ]"
                        >
                            <label class="flex items-center gap-2.5 flex-1 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    v-model="col.enabled"
                                    class="rounded border-slate-300 text-[#680A10] focus:ring-[#680A10]/20 h-4 w-4 cursor-pointer"
                                />
                                <span class="text-xs font-semibold text-slate-800">{{ col.label }}</span>
                                <span v-if="col.required" class="text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">
                                    Utama
                                </span>
                            </label>

                            <!-- Actions: Reorder & Delete -->
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    @click="moveUp(idx)"
                                    :disabled="idx === 0"
                                    class="h-6 w-6 rounded flex items-center justify-center text-slate-400 hover:text-slate-700 disabled:opacity-30 cursor-pointer"
                                    title="Pindah ke atas"
                                >
                                    <FontAwesomeIcon :icon="faArrowUp" class="text-[10px]" />
                                </button>
                                <button
                                    type="button"
                                    @click="moveDown(idx)"
                                    :disabled="idx === columns.length - 1"
                                    class="h-6 w-6 rounded flex items-center justify-center text-slate-400 hover:text-slate-700 disabled:opacity-30 cursor-pointer"
                                    title="Pindah ke bawah"
                                >
                                    <FontAwesomeIcon :icon="faArrowDown" class="text-[10px]" />
                                </button>
                                <button
                                    v-if="!col.required"
                                    type="button"
                                    @click="removeColumn(idx)"
                                    class="h-6 w-6 rounded flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 transition cursor-pointer"
                                    title="Hapus kolom ini"
                                >
                                    <FontAwesomeIcon :icon="faTrashCan" class="text-[10px]" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Input Tambah Kolom Baru -->
                    <div class="mt-2.5 flex items-center gap-2">
                        <input
                            v-model="newColumnName"
                            @keyup.enter="addColumn"
                            type="text"
                            placeholder="Ketik nama kolom baru (contoh: JABATAN / NIK)..."
                            class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#680A10]/20"
                        />
                        <button
                            type="button"
                            @click="addColumn"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
                        >
                            <FontAwesomeIcon :icon="faPlus" class="text-[10px]" />
                            Tambah
                        </button>
                    </div>
                </div>

                <!-- Preview Kolom Yang Akan Dicetak -->
                <div class="p-2.5 rounded-xl bg-slate-100 border border-slate-200/80">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                        Urutan Kolom Dicetak:
                    </span>
                    <div class="flex flex-wrap items-center gap-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                            No.
                        </span>
                        <template v-for="col in activeColumns" :key="col.id">
                            <span class="text-slate-400 text-xs">&rarr;</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-white border border-slate-200 text-slate-800 shadow-2xs">
                                {{ col.label }}
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    @click="emit('close')"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    @click="submitPrint"
                    :disabled="activeColumns.length === 0"
                    class="px-5 py-2 rounded-xl bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold flex items-center gap-1.5 transition disabled:opacity-50 cursor-pointer shadow-xs"
                >
                    <FontAwesomeIcon :icon="faPrint" />
                    Cetak Presensi
                </button>
            </div>
        </div>
    </div>
</template>
