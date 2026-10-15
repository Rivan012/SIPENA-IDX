<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    CalenderIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    GridIcon,
    ListIcon,
} from '@/Icons';

const props = defineProps({
    year: Number,
    month: Number,
    currentMonthName: String,
    prevMonthParams: Object,
    nextMonthParams: Object,
    calendarWeeks: Array,
    eventsClient: Array,
    eventsByDate: Object,
    stats: Object,
    filters: Object,
});

// View mode state: 'bulan' | 'minggu' | 'list'
const activeView = ref(props.filters?.view || 'bulan');

const setView = (view) => {
    activeView.value = view;
    try {
        const url = new URL(window.location.href);
        url.searchParams.set('view', view);
        window.history.replaceState({}, '', url.toString());
    } catch {
        // ignore
    }
};

// Status filter state
const selectedStatus = ref(props.filters?.status || 'all');

const statusOptions = [
    { value: 'all', label: 'Semua Status' },
    { value: 'dipublikasikan', label: 'Dipublikasikan' },
    { value: 'berjalan', label: 'Berjalan' },
    { value: 'approval', label: 'Menunggu Approval' },
    { value: 'selesai', label: 'Selesai' },
];

const dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

const parseDateParts = (dateString) => {
    if (!dateString) return { year: 0, month: 0, day: 0 };
    const parts = dateString.split('-');
    return {
        year: parseInt(parts[0], 10),
        month: parseInt(parts[1], 10),
        day: parseInt(parts[2], 10),
    };
};

const getDayNumber = (dateString) => {
    return parseDateParts(dateString).day || '';
};

const getMonthShort = (dateString) => {
    const m = parseDateParts(dateString).month;
    return monthNamesShort[m - 1] || '';
};

// Filtered events based on status
const filteredEvents = computed(() => {
    if (selectedStatus.value === 'all') {
        return props.eventsClient || [];
    }
    if (selectedStatus.value === 'approval') {
        return (props.eventsClient || []).filter(e => e.status === 'menunggu_approval');
    }
    return (props.eventsClient || []).filter(e => e.status === selectedStatus.value);
});

// Filtered weeks for monthly & weekly view
const displayCalendarWeeks = computed(() => {
    if (selectedStatus.value === 'all') {
        return props.calendarWeeks || [];
    }
    return (props.calendarWeeks || []).map(week => {
        return week.map(day => {
            const evs = (day.events || []).filter(ev => {
                if (selectedStatus.value === 'approval') {
                    return ev.status === 'menunggu_approval';
                }
                return ev.status === selectedStatus.value;
            });
            return {
                ...day,
                events: evs,
            };
        });
    });
});

// Initial selection
const initialDate = computed(() => {
    if (filteredEvents.value?.length > 0) {
        return filteredEvents.value[0].tanggal;
    }
    return `${props.year}-${String(props.month).padStart(2, '0')}-01`;
});

const selectedDate = ref(initialDate.value);
const selectedEvent = ref(filteredEvents.value?.length > 0 ? filteredEvents.value[0] : null);

// Week navigation
const customWeekIndex = ref(null);

const currentWeekIndex = computed({
    get() {
        if (customWeekIndex.value !== null) {
            return customWeekIndex.value;
        }
        if (displayCalendarWeeks.value?.length > 0) {
            const idx = displayCalendarWeeks.value.findIndex(w => w.some(d => d.date_string === selectedDate.value));
            return idx !== -1 ? idx : 0;
        }
        return 0;
    },
    set(val) {
        customWeekIndex.value = val;
    }
});

const currentWeek = computed(() => {
    if (!displayCalendarWeeks.value || displayCalendarWeeks.value.length === 0) return [];
    return displayCalendarWeeks.value[currentWeekIndex.value] || displayCalendarWeeks.value[0] || [];
});

const formatWeekRange = (week) => {
    if (!week || week.length === 0) return '';
    const first = week[0];
    const last = week[week.length - 1];
    const fParts = parseDateParts(first.date_string);
    const lParts = parseDateParts(last.date_string);
    const fMonth = monthNamesShort[fParts.month - 1] || '';
    const lMonth = monthNamesShort[lParts.month - 1] || '';
    if (fMonth === lMonth) {
        return `${fParts.day} - ${lParts.day} ${fMonth} ${fParts.year}`;
    }
    return `${fParts.day} ${fMonth} - ${lParts.day} ${lMonth} ${lParts.year}`;
};

const prevWeek = () => {
    if (currentWeekIndex.value > 0) {
        currentWeekIndex.value--;
        const week = displayCalendarWeeks.value[currentWeekIndex.value];
        if (week && week.length > 0) {
            const withEv = week.find(d => d.events?.length > 0);
            selectDate(withEv ? withEv.date_string : week[0].date_string);
        }
    }
};

const nextWeek = () => {
    if (currentWeekIndex.value < displayCalendarWeeks.value.length - 1) {
        currentWeekIndex.value++;
        const week = displayCalendarWeeks.value[currentWeekIndex.value];
        if (week && week.length > 0) {
            const withEv = week.find(d => d.events?.length > 0);
            selectDate(withEv ? withEv.date_string : week[0].date_string);
        }
    }
};

const selectDate = (dateString, firstEvent = null) => {
    selectedDate.value = dateString;
    const dayEvs = props.eventsByDate?.[dateString] || [];
    if (firstEvent) {
        selectedEvent.value = firstEvent;
    } else if (dayEvs.length > 0) {
        if (selectedStatus.value === 'all') {
            selectedEvent.value = dayEvs[0];
        } else {
            const match = dayEvs.find(e => selectedStatus.value === 'approval' ? e.status === 'menunggu_approval' : e.status === selectedStatus.value);
            selectedEvent.value = match || dayEvs[0];
        }
    } else {
        selectedEvent.value = null;
    }
};

const selectEvent = (event) => {
    selectedEvent.value = event;
    selectedDate.value = event.tanggal;
};
</script>

<template>
    <AdminLayout title="Kalender Kegiatan — SIPENA IDX">
        <Head title="Kalender Kegiatan" />

        <div class="space-y-5">
            <!-- Header Kalender & Controls -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kalender Edukasi</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Jadwal pelaksanaan sosialisasi dan sekolah pasar modal terpadu.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Segmented View Toggle: Bulan | Minggu | List -->
                    <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200">
                        <button
                            type="button"
                            @click="setView('bulan')"
                            :class="[
                                'px-3 py-1.5 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer',
                                activeView === 'bulan' ? 'bg-white text-[#9F0E0F] shadow-xs' : 'text-slate-600 hover:text-slate-900'
                            ]"
                        >
                            <CalenderIcon class="w-3.5 h-3.5" />
                            <span>Bulan</span>
                        </button>
                        <button
                            type="button"
                            @click="setView('minggu')"
                            :class="[
                                'px-3 py-1.5 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer',
                                activeView === 'minggu' ? 'bg-white text-[#9F0E0F] shadow-xs' : 'text-slate-600 hover:text-slate-900'
                            ]"
                        >
                            <GridIcon class="w-3.5 h-3.5" />
                            <span>Minggu</span>
                        </button>
                        <button
                            type="button"
                            @click="setView('list')"
                            :class="[
                                'px-3 py-1.5 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer',
                                activeView === 'list' ? 'bg-white text-[#9F0E0F] shadow-xs' : 'text-slate-600 hover:text-slate-900'
                            ]"
                        >
                            <ListIcon class="w-3.5 h-3.5" />
                            <span>List</span>
                        </button>
                    </div>

                    <!-- Navigasi Bulan -->
                    <div class="flex items-center bg-white rounded-xl border border-slate-200 p-1 shadow-xs">
                        <Link :href="route('admin.kalender.index', prevMonthParams)"
                              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
                              title="Bulan Sebelumnya">
                            <ChevronLeftIcon class="w-4 h-4" />
                        </Link>
                        <span class="px-3 text-xs font-bold text-slate-800">
                            {{ currentMonthName }}
                        </span>
                        <Link :href="route('admin.kalender.index', nextMonthParams)"
                              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
                              title="Bulan Selanjutnya">
                            <ChevronRightIcon class="w-4 h-4" />
                        </Link>
                    </div>

                    <Link :href="route('admin.kegiatan.create')"
                          class="px-4 py-2 bg-[#9F0E0F] hover:bg-[#850C0D] text-white text-xs font-semibold rounded-xl transition shadow-xs">
                        + Tambah Agenda
                    </Link>
                </div>
            </div>

            <!-- Filter Status Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Status:</span>
                    <button
                        v-for="opt in statusOptions"
                        :key="opt.value"
                        type="button"
                        @click="selectedStatus = opt.value"
                        :class="[
                            'px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors cursor-pointer',
                            selectedStatus === opt.value
                                ? 'bg-[#9F0E0F] text-white shadow-2xs'
                                : 'bg-slate-100 hover:bg-slate-200 text-slate-600'
                        ]"
                    >
                        {{ opt.label }}
                    </button>
                </div>
                <div class="text-xs font-medium text-slate-500">
                    <span class="font-bold text-slate-900">{{ filteredEvents.length }}</span> kegiatan ditemukan
                </div>
            </div>

            <!-- Main Split Layout 8:4 (Calendar/Week/List Content : Detail Agenda Terpilih) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Kolom Kiri: Tampilan Sesuai View Mode (8 Kolom) -->
                <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <!-- VIEW 1: BULAN (Matriks Kalender Bulanan) -->
                    <div v-if="activeView === 'bulan'" class="space-y-2">
                        <!-- Hari Header -->
                        <div class="grid grid-cols-6 gap-1 text-center font-bold text-[11px] text-slate-500 pb-2 border-b border-slate-100">
                            <span>Senin</span>
                            <span>Selasa</span>
                            <span>Rabu</span>
                            <span>Kamis</span>
                            <span>Jumat</span>
                            <span class="text-red-500">Sabtu</span>
                        </div>

                        <!-- Weeks Grid -->
                        <div class="divide-y divide-slate-100">
                            <div v-for="(week, wIdx) in displayCalendarWeeks" :key="wIdx" class="grid grid-cols-6 gap-1 py-1">
                                <div v-for="day in week" :key="day.date_string"
                                     @click="selectDate(day.date_string)"
                                     :class="[
                                         'min-h-[92px] p-2 rounded-xl border transition cursor-pointer flex flex-col justify-between',
                                         selectedDate === day.date_string ? 'border-[#9F0E0F] ring-2 ring-[#9F0E0F]/20 bg-rose-50/20' : 'border-slate-100 hover:border-slate-300 bg-white',
                                         !day.is_current_month ? 'opacity-40 bg-slate-50/50' : ''
                                     ]">
                                    <div class="flex items-center justify-between">
                                        <span :class="[
                                            'text-xs font-bold leading-none',
                                            day.is_today ? 'w-5 h-5 rounded-full bg-[#9F0E0F] text-white flex items-center justify-center text-[10px]' : 'text-slate-800'
                                        ]">
                                            {{ day.day }}
                                        </span>
                                        <span v-if="day.events && day.events.length > 0"
                                              class="w-1.5 h-1.5 rounded-full bg-[#9F0E0F]"></span>
                                    </div>

                                    <!-- Chips Event Harian -->
                                    <div class="space-y-1 mt-1 overflow-hidden">
                                        <div v-for="ev in day.events.slice(0, 2)" :key="ev.id"
                                             @click.stop="selectEvent(ev)"
                                             :class="[
                                                 'px-1.5 py-0.5 rounded text-[10px] font-semibold truncate transition',
                                                 selectedEvent?.id === ev.id ? 'bg-[#9F0E0F] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'
                                             ]">
                                            {{ ev.waktu_mulai }} {{ ev.nama_kegiatan }}
                                        </div>
                                        <span v-if="day.events.length > 2" class="text-[9px] text-slate-400 block">
                                            +{{ day.events.length - 2 }} lainnya
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW 2: MINGGU (Grid Mingguan Terinci) -->
                    <div v-else-if="activeView === 'minggu'" class="space-y-4">
                        <!-- Navigasi Minggu -->
                        <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                            <button
                                type="button"
                                @click="prevWeek"
                                :disabled="currentWeekIndex <= 0"
                                :class="[
                                    'p-1.5 rounded-lg border transition text-xs font-medium flex items-center gap-1',
                                    currentWeekIndex <= 0
                                        ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400 border-slate-200'
                                        : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200 cursor-pointer shadow-xs'
                                ]"
                            >
                                <ChevronLeftIcon class="w-3.5 h-3.5" />
                                <span class="hidden sm:inline">Minggu Sebelumnya</span>
                            </button>

                            <div class="text-center">
                                <span class="text-xs font-bold text-slate-900 block">
                                    Minggu ke-{{ currentWeekIndex + 1 }} dari {{ displayCalendarWeeks.length }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-medium">
                                    {{ formatWeekRange(currentWeek) }}
                                </span>
                            </div>

                            <button
                                type="button"
                                @click="nextWeek"
                                :disabled="currentWeekIndex >= displayCalendarWeeks.length - 1"
                                :class="[
                                    'p-1.5 rounded-lg border transition text-xs font-medium flex items-center gap-1',
                                    currentWeekIndex >= displayCalendarWeeks.length - 1
                                        ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400 border-slate-200'
                                        : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200 cursor-pointer shadow-xs'
                                ]"
                            >
                                <span class="hidden sm:inline">Minggu Berikutnya</span>
                                <ChevronRightIcon class="w-3.5 h-3.5" />
                            </button>
                        </div>

                        <!-- 6 Kolom Hari Mingguan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                            <div
                                v-for="(day, dIdx) in currentWeek"
                                :key="day.date_string"
                                @click="selectDate(day.date_string)"
                                :class="[
                                    'rounded-xl border p-2.5 flex flex-col min-h-[300px] transition cursor-pointer',
                                    selectedDate === day.date_string
                                        ? 'border-[#9F0E0F] ring-2 ring-[#9F0E0F]/20 bg-rose-50/15'
                                        : 'border-slate-200 bg-white hover:border-slate-300',
                                    !day.is_current_month ? 'opacity-50 bg-slate-50/60' : ''
                                ]"
                            >
                                <!-- Header Hari -->
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider block text-slate-400">
                                            {{ dayNames[dIdx] }}
                                        </span>
                                        <span class="text-xs font-black text-slate-800">
                                            {{ day.day }} {{ monthNamesShort[parseDateParts(day.date_string).month - 1] }}
                                        </span>
                                    </div>
                                    <span
                                        v-if="day.is_today"
                                        class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#9F0E0F] text-white"
                                    >
                                        Hari Ini
                                    </span>
                                </div>

                                <!-- Event Cards Dalam Hari -->
                                <div class="flex-1 space-y-1.5 overflow-y-auto no-scrollbar">
                                    <div
                                        v-for="ev in day.events"
                                        :key="ev.id"
                                        @click.stop="selectEvent(ev)"
                                        :class="[
                                            'p-2 rounded-lg border text-left cursor-pointer transition shadow-2xs',
                                            selectedEvent?.id === ev.id
                                                ? 'bg-[#9F0E0F] text-white border-[#850C0D]'
                                                : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-800'
                                        ]"
                                    >
                                        <div class="flex items-center justify-between gap-1 mb-1">
                                            <span :class="[
                                                'text-[9px] font-bold px-1.5 py-0.2 rounded',
                                                selectedEvent?.id === ev.id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'
                                            ]">
                                                {{ ev.waktu_mulai }}
                                            </span>
                                            <span :class="[
                                                'text-[9px] font-semibold px-1 rounded truncate max-w-[65px]',
                                                selectedEvent?.id === ev.id ? 'text-rose-100' : 'text-slate-500'
                                            ]">
                                                {{ ev.jenis_kegiatan }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-bold line-clamp-2 leading-snug">
                                            {{ ev.nama_kegiatan }}
                                        </div>
                                        <div :class="[
                                            'text-[9px] mt-1 truncate',
                                            selectedEvent?.id === ev.id ? 'text-rose-200' : 'text-slate-500'
                                        ]">
                                            {{ ev.lokasi }}
                                        </div>
                                    </div>

                                    <div v-if="!day.events || day.events.length === 0" class="h-24 flex items-center justify-center text-center">
                                        <span class="text-[10px] text-slate-400 italic">Tidak ada agenda</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW 3: LIST (Daftar Agenda Kronologis) -->
                    <div v-else-if="activeView === 'list'" class="space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Agenda Periode {{ currentMonthName }}</h2>
                                <p class="text-[11px] text-slate-500">Menampilkan {{ filteredEvents.length }} kegiatan</p>
                            </div>
                        </div>

                        <!-- Daftar Kartu Agenda -->
                        <div v-if="filteredEvents.length > 0" class="space-y-2.5">
                            <div
                                v-for="ev in filteredEvents"
                                :key="ev.id"
                                @click="selectEvent(ev)"
                                :class="[
                                    'p-3.5 rounded-xl border transition cursor-pointer flex flex-col sm:flex-row sm:items-center justify-between gap-3',
                                    selectedEvent?.id === ev.id
                                        ? 'border-[#9F0E0F] ring-2 ring-[#9F0E0F]/20 bg-rose-50/20'
                                        : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 shadow-2xs'
                                ]"
                            >
                                <div class="flex items-start gap-3 min-w-0">
                                    <!-- Tanggal Badge -->
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex flex-col items-center justify-center shrink-0 text-slate-900">
                                        <span class="text-base font-black leading-none">{{ getDayNumber(ev.tanggal) }}</span>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase mt-0.5 leading-none">{{ getMonthShort(ev.tanggal) }}</span>
                                    </div>

                                    <!-- Informasi Kegiatan -->
                                    <div class="min-w-0 space-y-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                {{ ev.jenis_kegiatan }}
                                            </span>
                                            <span :class="['px-2 py-0.5 rounded text-[10px] font-bold', ev.status_class]">
                                                {{ ev.status_label }}
                                            </span>
                                            <span class="text-[11px] text-slate-500 font-medium">
                                                {{ ev.waktu_mulai }} - {{ ev.waktu_selesai }} WIB
                                            </span>
                                        </div>

                                        <h3 class="text-sm font-bold text-slate-900 leading-snug truncate">
                                            {{ ev.nama_kegiatan }}
                                        </h3>

                                        <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500">
                                            <span class="truncate max-w-[200px]">📍 {{ ev.lokasi }}</span>
                                            <span>👤 {{ ev.pic }}</span>
                                            <span class="font-semibold text-emerald-700">{{ ev.anggaran }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                    <Link
                                        :href="route('admin.kegiatan.show', ev.id)"
                                        @click.stop
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-[#9F0E0F] hover:text-white text-slate-700 text-xs font-semibold transition"
                                    >
                                        Detail
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State List -->
                        <div v-else class="py-12 text-center bg-white rounded-2xl border border-slate-200 space-y-2">
                            <CalenderIcon class="w-10 h-10 mx-auto text-slate-300" />
                            <p class="text-xs font-bold text-slate-700">Tidak ada agenda pada periode ini</p>
                            <p class="text-[11px] text-slate-400">Pilih status lain atau navigasikan ke bulan sebelumnya/selanjutnya.</p>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Panel Detail Agenda Terpilih (4 Kolom) -->
                <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-20 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">DETAIL AGENDA TERPILIH</span>
                        <span class="text-xs font-bold text-[#9F0E0F]">{{ selectedDate }}</span>
                    </div>

                    <div v-if="selectedEvent" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ selectedEvent.jenis_kegiatan }}
                            </span>
                            <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', selectedEvent.status_class]">
                                {{ selectedEvent.status_label }}
                            </span>
                        </div>

                        <h2 class="text-base font-bold text-slate-900 leading-snug">
                            {{ selectedEvent.nama_kegiatan }}
                        </h2>

                        <div class="space-y-2 text-xs text-slate-600 border-y border-slate-100 py-3">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 w-16">WAKTU:</span>
                                <span class="font-semibold text-slate-800">{{ selectedEvent.waktu_mulai }} - {{ selectedEvent.waktu_selesai }} WIB</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 w-16">TEMPAT:</span>
                                <span class="text-slate-800 truncate" :title="selectedEvent.lokasi">{{ selectedEvent.lokasi }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 w-16">PIC:</span>
                                <span class="text-slate-800 truncate">{{ selectedEvent.pic }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 w-16">ANGGARAN:</span>
                                <span class="font-bold text-emerald-700">{{ selectedEvent.anggaran }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-400 w-16">KUOTA:</span>
                                <span class="text-slate-800">{{ selectedEvent.peserta_count }} / {{ selectedEvent.kuota_peserta }} Peserta</span>
                            </div>
                        </div>

                        <Link :href="route('admin.kegiatan.show', selectedEvent.id)"
                              class="w-full inline-flex items-center justify-center gap-1 py-2.5 rounded-xl bg-[#9F0E0F] hover:bg-[#850C0D] text-white font-semibold text-xs transition shadow-xs">
                            <span>Buka Rincian Kegiatan</span>
                            <ChevronRightIcon class="w-4 h-4" />
                        </Link>
                    </div>

                    <!-- State Jika Tidak Ada Event di Tanggal Terpilih -->
                    <div v-else class="py-10 text-center text-slate-400 text-xs space-y-2">
                        <CalenderIcon class="w-8 h-8 mx-auto text-slate-300" />
                        <p class="font-semibold text-slate-600">Tidak ada agenda pada tanggal ini</p>
                        <p class="text-[11px] text-slate-400">Pilih tanggal atau kegiatan lain pada kalender untuk melihat rincian.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
