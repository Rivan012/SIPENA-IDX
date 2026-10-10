<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

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

// Initial selection
const initialDate = computed(() => {
    if (props.eventsClient?.length > 0) {
        return props.eventsClient[0].tanggal;
    }
    return `${props.year}-${String(props.month).padStart(2, '0')}-01`;
});

const selectedDate = ref(initialDate.value);
const selectedEvent = ref(props.eventsClient?.length > 0 ? props.eventsClient[0] : null);

const currentDayEvents = computed(() => {
    return props.eventsByDate[selectedDate.value] || [];
});

const selectDate = (dateString, firstEvent = null) => {
    selectedDate.value = dateString;
    const dayEvs = props.eventsByDate[dateString] || [];
    if (firstEvent) {
        selectedEvent.value = firstEvent;
    } else if (dayEvs.length > 0) {
        selectedEvent.value = dayEvs[0];
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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kalender Edukasi</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Jadwal pelaksanaan sosialisasi dan sekolah pasar modal terpadu.</p>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Navigasi Bulan -->
                    <div class="flex items-center bg-white rounded-xl border border-slate-200 p-1 shadow-xs">
                        <Link :href="route('admin.kalender.index', prevMonthParams)"
                              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                            &larr;
                        </Link>
                        <span class="px-3 text-xs font-bold text-slate-800">
                            {{ currentMonthName }}
                        </span>
                        <Link :href="route('admin.kalender.index', nextMonthParams)"
                              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                            &rarr;
                        </Link>
                    </div>

                    <Link :href="route('admin.kegiatan.create')"
                          class="px-4 py-2 bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold rounded-xl transition shadow-xs">
                        + Tambah Agenda
                    </Link>
                </div>
            </div>

            <!-- 4 Quick Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL AGENDA TAHUN INI</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block">{{ stats.total }} Acara</span>
                </div>
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">DIPUBLIKASIKAN</span>
                    <span class="text-xl font-bold text-emerald-600 mt-1 block">{{ stats.dipublikasikan }} Acara</span>
                </div>
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MENUNGGU APPROVAL</span>
                    <span class="text-xl font-bold text-amber-600 mt-1 block">{{ stats.approval }} Acara</span>
                </div>
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TELAH SELESAI</span>
                    <span class="text-xl font-bold text-slate-700 mt-1 block">{{ stats.selesai }} Acara</span>
                </div>
            </div>

            <!-- Split Panel 60:40 (Kalender Grid : Detail Agenda Terpilih) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Kolom Kiri: Matriks Kalender 6 Hari (Senin - Sabtu) -->
                <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
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
                    <div class="divide-y divide-slate-100 mt-1">
                        <div v-for="(week, wIdx) in calendarWeeks" :key="wIdx" class="grid grid-cols-6 gap-1 py-1">
                            <div v-for="day in week" :key="day.date_string"
                                 @click="selectDate(day.date_string)"
                                 :class="[
                                     'min-h-[90px] p-2 rounded-xl border transition cursor-pointer flex flex-col justify-between',
                                     selectedDate === day.date_string ? 'border-[#680A10] ring-2 ring-[#680A10]/20 bg-rose-50/20' : 'border-slate-100 hover:border-slate-300 bg-white',
                                     !day.is_current_month ? 'opacity-40 bg-slate-50/50' : ''
                                 ]">
                                <div class="flex items-center justify-between">
                                    <span :class="[
                                        'text-xs font-bold leading-none',
                                        day.is_today ? 'w-5 h-5 rounded-full bg-[#680A10] text-white flex items-center justify-center text-[10px]' : 'text-slate-800'
                                    ]">
                                        {{ day.day }}
                                    </span>
                                    <span v-if="day.events && day.events.length > 0"
                                          class="w-1.5 h-1.5 rounded-full bg-[#680A10]"></span>
                                </div>

                                <!-- Chips Event Harian -->
                                <div class="space-y-1 mt-1 overflow-hidden">
                                    <div v-for="ev in day.events.slice(0, 2)" :key="ev.id"
                                         @click.stop="selectEvent(ev)"
                                         :class="[
                                             'px-1.5 py-0.5 rounded text-[10px] font-semibold truncate transition',
                                             selectedEvent?.id === ev.id ? 'bg-[#680A10] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'
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

                <!-- Kolom Kanan: Panel Detail Agenda Terpilih (Reaktif Client-side) -->
                <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-20 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">DETAIL AGENDA TERPILIH</span>
                        <span class="text-xs font-bold text-[#680A10]">{{ selectedDate }}</span>
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
                              class="w-full inline-flex items-center justify-center py-2.5 rounded-xl bg-[#680A10] hover:bg-[#54080D] text-white font-semibold text-xs transition shadow-xs">
                            Buka Rincian Kegiatan &rarr;
                        </Link>
                    </div>

                    <!-- State Jika Tidak Ada Event di Tanggal Terpilih -->
                    <div v-else class="py-10 text-center text-slate-400 text-xs space-y-2">
                        <svg class="w-8 h-8 mx-auto text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <p class="font-semibold text-slate-600">Tidak ada agenda pada tanggal ini</p>
                        <p class="text-[11px] text-slate-400">Pilih tanggal lain pada kalender untuk melihat rincian acara.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
