<script setup>
import { ref, computed } from 'vue';
import { BarChartIcon, PieChartIcon } from '@/Icons';

const props = defineProps({
    rkapChart: {
        type: Object,
        default: () => ({}),
    },
});

// Chart mode: 'bar' | 'line' | 'pie'
const chartType = ref('bar');
// Active month selection for bar/line
const activeMonth = ref(null);
const hoveredPoint = ref(null);
// Active slice for pie
const hoveredSlice = ref(null);

const namaBulanDefault = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

const bulanData = computed(() => {
    if (props.rkapChart?.bulan && Array.isArray(props.rkapChart.bulan) && props.rkapChart.bulan.length === 12) {
        return props.rkapChart.bulan;
    }
    return namaBulanDefault.map((nama, idx) => ({
        nama,
        bulan: idx + 1,
        diusulkan: 0,
        terlaksana: 0,
    }));
});

const kategoriData = computed(() => {
    if (props.rkapChart?.kategori && Array.isArray(props.rkapChart.kategori) && props.rkapChart.kategori.length > 0) {
        return props.rkapChart.kategori;
    }
    return [
        { label: 'Sekolah Pasar Modal (SPM)', count: 0, persen: '0%', persen_num: 0, warna: '#0052cc' },
        { label: 'Sosialisasi & Edukasi', count: 0, persen: '0%', persen_num: 0, warna: '#10b981' },
        { label: 'Workshop & Analisis', count: 0, persen: '0%', persen_num: 0, warna: '#f59e0b' },
        { label: 'Literasi Publik & Lainnya', count: 0, persen: '0%', persen_num: 0, warna: '#85131B' },
    ];
});

const summaryData = computed(() => {
    if (props.rkapChart?.summary) {
        return props.rkapChart.summary;
    }

    const totalDiusulkan = bulanData.value.reduce((acc, b) => acc + (b.diusulkan || 0), 0);
    const totalTerlaksana = bulanData.value.reduce((acc, b) => acc + (b.terlaksana || 0), 0);
    const persen = totalDiusulkan > 0 ? Math.round((totalTerlaksana / totalDiusulkan) * 100) : 0;

    return {
        totalDiusulkan,
        totalTerlaksana,
        totalTerjadwal: 0,
        totalMenunggu: 0,
        persenTerlaksana: `${persen}%`,
    };
});

const tahunGrafik = computed(() => props.rkapChart?.tahun || new Date().getFullYear());

// Max value for scaling Y axis
const maxVal = computed(() => {
    let max = 0;
    bulanData.value.forEach(b => {
        if ((b.diusulkan || 0) > max) max = b.diusulkan;
        if ((b.terlaksana || 0) > max) max = b.terlaksana;
    });
    // Default ceiling to at least 5 so empty charts render nicely
    return Math.max(5, Math.ceil(max * 1.25));
});

// SVG Line Chart coordinates math
const lineChartPoints = computed(() => {
    const W = 760;
    const H = 200;
    const PL = 30;
    const PR = 30;
    const PT = 20;
    const PB = 25;
    const usableW = W - PL - PR;
    const usableH = H - PT - PB;

    const diusulkanPoints = [];
    const terlaksanaPoints = [];

    bulanData.value.forEach((b, idx) => {
        const x = PL + (idx * (usableW / 11));
        const yDiusulkan = (H - PB) - (((b.diusulkan || 0) / maxVal.value) * usableH);
        const yTerlaksana = (H - PB) - (((b.terlaksana || 0) / maxVal.value) * usableH);

        diusulkanPoints.push({ x, y: yDiusulkan, ...b });
        terlaksanaPoints.push({ x, y: yTerlaksana, ...b });
    });

    const dDiusulkan = diusulkanPoints.map((p, i) => (i === 0 ? `M ${p.x} ${p.y}` : `L ${p.x} ${p.y}`)).join(' ');
    const dTerlaksana = terlaksanaPoints.map((p, i) => (i === 0 ? `M ${p.x} ${p.y}` : `L ${p.x} ${p.y}`)).join(' ');

    const areaDiusulkan = `${dDiusulkan} L ${diusulkanPoints[diusulkanPoints.length - 1].x} ${H - PB} L ${diusulkanPoints[0].x} ${H - PB} Z`;
    const areaTerlaksana = `${dTerlaksana} L ${terlaksanaPoints[terlaksanaPoints.length - 1].x} ${H - PB} L ${terlaksanaPoints[0].x} ${H - PB} Z`;

    return {
        W,
        H,
        PL,
        PR,
        PT,
        PB,
        baselineY: H - PB,
        diusulkanPoints,
        terlaksanaPoints,
        dDiusulkan,
        dTerlaksana,
        areaDiusulkan,
        areaTerlaksana,
    };
});

// SVG Donut Chart calculation
const donutSlices = computed(() => {
    const r = 70;
    const circumference = 2 * Math.PI * r;
    const total = kategoriData.value.reduce((acc, k) => acc + (k.count || 0), 0);

    let cumulativeOffset = 0;

    return kategoriData.value.map(item => {
        const count = item.count || 0;
        const ratio = total > 0 ? count / total : 0;
        const dash = ratio * circumference;
        const offset = -cumulativeOffset;

        cumulativeOffset += dash;

        return {
            ...item,
            ratio,
            dash: `${dash} ${circumference}`,
            offset,
            circumference,
            r,
        };
    });
});

const totalKategoriCount = computed(() => {
    return kategoriData.value.reduce((acc, k) => acc + (k.count || 0), 0);
});
</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs">
        <!-- HEADER & CHART CONTROLS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-50 text-[#85131B] border border-red-100">
                        Modul Kegiatan SIPENA
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500">TA {{ tahunGrafik }}</span>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight mt-1">
                    <template v-if="chartType === 'pie'">Distribusi Kategori Kegiatan Edukasi</template>
                    <template v-else-if="chartType === 'line'">Tren Garis Realisasi Proposal Kegiatan</template>
                    <template v-else>Grafik Tren Proposal Diusulkan vs Terlaksana</template>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    <template v-if="chartType === 'pie'">
                        Proporsi kegiatan berdasarkan jenis modul program edukasi pasar modal
                    </template>
                    <template v-else>
                        Komparasi jumlah usulan mitra dengan kegiatan yang telah selesai dilaksanakan per bulan
                    </template>
                </p>
            </div>

            <!-- TYPE SELECTOR BUTTONS (Bar, Line, Pie) -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Legend info -->
                <div v-if="chartType !== 'pie'" class="hidden md:flex items-center gap-4 text-xs shrink-0 mr-1">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#93c5fd] shrink-0 border border-blue-300"></span>
                        <span class="font-medium text-slate-600">Diusulkan</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#0052cc] shrink-0"></span>
                        <span class="font-medium text-slate-600">Terlaksana</span>
                    </div>
                </div>

                <!-- Toggle segmented control -->
                <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200">
                    <button
                        type="button"
                        @click="chartType = 'bar'"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                            chartType === 'bar'
                                ? 'bg-white text-slate-900 shadow-xs'
                                : 'text-slate-500 hover:text-slate-800'
                        ]"
                        title="Tampilkan Diagram Batang"
                    >
                        <BarChartIcon class="w-3.5 h-3.5" />
                        <span>Batang</span>
                    </button>
                    <button
                        type="button"
                        @click="chartType = 'line'"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                            chartType === 'line'
                                ? 'bg-white text-slate-900 shadow-xs'
                                : 'text-slate-500 hover:text-slate-800'
                        ]"
                        title="Tampilkan Diagram Garis"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <span>Garis</span>
                    </button>
                    <button
                        type="button"
                        @click="chartType = 'pie'"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                            chartType === 'pie'
                                ? 'bg-white text-slate-900 shadow-xs'
                                : 'text-slate-500 hover:text-slate-800'
                        ]"
                        title="Tampilkan Diagram Lingkaran"
                    >
                        <PieChartIcon class="w-3.5 h-3.5" />
                        <span>Lingkaran</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. BAR CHART VIEW -->
        <div v-if="chartType === 'bar'" class="pt-8 pb-3">
            <div class="relative h-64 sm:h-72 w-full flex items-end">
                <!-- Horizontal grid lines -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-40">
                    <div class="border-b border-dashed border-slate-200 w-full"></div>
                    <div class="border-b border-dashed border-slate-200 w-full"></div>
                    <div class="border-b border-dashed border-slate-200 w-full"></div>
                    <div class="border-b border-slate-200 w-full"></div>
                </div>

                <!-- 12 Month Columns -->
                <div class="grid grid-cols-12 gap-1 sm:gap-2.5 w-full h-full items-end relative z-10">
                    <div
                        v-for="b in bulanData"
                        :key="b.nama"
                        @click="activeMonth = activeMonth === b.nama ? null : b.nama"
                        class="flex flex-col items-center justify-end h-full group cursor-pointer relative"
                    >
                        <!-- Tooltip on Hover / Active -->
                        <div
                            v-if="activeMonth === b.nama"
                            class="absolute -top-10 z-30 px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-[10px] font-semibold whitespace-nowrap shadow-md pointer-events-none"
                        >
                            <div class="font-bold border-b border-slate-700 pb-0.5 mb-0.5 text-center">{{ b.nama }} {{ tahunGrafik }}</div>
                            <div class="flex items-center gap-2">
                                <span class="text-blue-300">Diusulkan: {{ b.diusulkan || 0 }}</span>
                                <span class="text-slate-500">|</span>
                                <span class="text-emerald-300">Terlaksana: {{ b.terlaksana || 0 }}</span>
                            </div>
                        </div>

                        <!-- Bar Pair Container -->
                        <div class="flex items-end gap-1 sm:gap-1.5 h-[190px] sm:h-[220px] w-full justify-center">
                            <!-- Bar 1: Diusulkan -->
                            <div
                                class="w-2.5 sm:w-4 rounded-t-sm sm:rounded-t-md bg-[#93c5fd] group-hover:bg-[#60a5fa] transition-all duration-300"
                                :style="{ height: b.diusulkan > 0 ? `${Math.max(6, Math.round((b.diusulkan / maxVal) * 100))}%` : '4px' }"
                                :class="{ 'opacity-30': b.diusulkan === 0 }"
                                :title="`Bulan ${b.nama}: ${b.diusulkan || 0} proposal diusulkan`"
                            ></div>

                            <!-- Bar 2: Terlaksana -->
                            <div
                                class="w-2.5 sm:w-4 rounded-t-sm sm:rounded-t-md bg-[#0052cc] group-hover:bg-[#0041a8] transition-all duration-300"
                                :style="{ height: b.terlaksana > 0 ? `${Math.max(6, Math.round((b.terlaksana / maxVal) * 100))}%` : '4px' }"
                                :class="{ 'opacity-20': b.terlaksana === 0 }"
                                :title="`Bulan ${b.nama}: ${b.terlaksana || 0} kegiatan terlaksana`"
                            ></div>
                        </div>

                        <!-- Month Label -->
                        <div class="mt-3">
                            <span
                                :class="[
                                    'text-[10px] sm:text-xs font-semibold px-1.5 py-0.5 rounded transition-colors',
                                    activeMonth === b.nama
                                        ? 'bg-blue-100 text-[#0052cc] font-bold ring-1 ring-blue-300'
                                        : 'text-slate-500 group-hover:text-slate-900'
                                ]"
                            >
                                {{ b.nama }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. LINE CHART VIEW -->
        <div v-else-if="chartType === 'line'" class="pt-6 pb-2">
            <div class="w-full overflow-x-auto">
                <div class="min-w-[640px] relative">
                    <!-- Tooltip floating for line point -->
                    <div
                        v-if="hoveredPoint"
                        class="absolute z-20 px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-[10px] font-semibold whitespace-nowrap shadow-md pointer-events-none transition-all"
                        :style="{ left: `${(hoveredPoint.x / lineChartPoints.W) * 100}%`, top: `${(hoveredPoint.y / lineChartPoints.H) * 100 - 15}%`, transform: 'translate(-50%, -100%)' }"
                    >
                        <div class="font-bold border-b border-slate-700 pb-0.5 mb-0.5">{{ hoveredPoint.nama }} {{ tahunGrafik }}</div>
                        <div>{{ hoveredPoint.series }}: <strong class="text-white">{{ hoveredPoint.value }}</strong></div>
                    </div>

                    <svg
                        :viewBox="`0 0 ${lineChartPoints.W} ${lineChartPoints.H}`"
                        class="w-full h-64 sm:h-72 overflow-visible"
                    >
                        <defs>
                            <linearGradient id="gradDiusulkan" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
                            </linearGradient>
                            <linearGradient id="gradTerlaksana" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0052cc" stop-opacity="0.35" />
                                <stop offset="100%" stop-color="#0052cc" stop-opacity="0.0" />
                            </linearGradient>
                        </defs>

                        <!-- Gridlines -->
                        <line :x1="lineChartPoints.PL" :y1="lineChartPoints.PT" :x2="lineChartPoints.W - lineChartPoints.PR" :y2="lineChartPoints.PT" stroke="#e2e8f0" stroke-dasharray="4 4" />
                        <line :x1="lineChartPoints.PL" :y1="lineChartPoints.PT + 55" :x2="lineChartPoints.W - lineChartPoints.PR" :y2="lineChartPoints.PT + 55" stroke="#e2e8f0" stroke-dasharray="4 4" />
                        <line :x1="lineChartPoints.PL" :y1="lineChartPoints.PT + 110" :x2="lineChartPoints.W - lineChartPoints.PR" :y2="lineChartPoints.PT + 110" stroke="#e2e8f0" stroke-dasharray="4 4" />
                        <line :x1="lineChartPoints.PL" :y1="lineChartPoints.baselineY" :x2="lineChartPoints.W - lineChartPoints.PR" :y2="lineChartPoints.baselineY" stroke="#cbd5e1" stroke-width="1.5" />

                        <!-- Area Fills -->
                        <path :d="lineChartPoints.areaDiusulkan" fill="url(#gradDiusulkan)" />
                        <path :d="lineChartPoints.areaTerlaksana" fill="url(#gradTerlaksana)" />

                        <!-- Lines -->
                        <path :d="lineChartPoints.dDiusulkan" fill="none" stroke="#60a5fa" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path :d="lineChartPoints.dTerlaksana" fill="none" stroke="#0052cc" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                        <!-- Data Points Diusulkan -->
                        <g v-for="p in lineChartPoints.diusulkanPoints" :key="`d-${p.nama}`">
                            <circle
                                :cx="p.x"
                                :cy="p.y"
                                r="4"
                                fill="#ffffff"
                                stroke="#3b82f6"
                                stroke-width="2.5"
                                class="cursor-pointer hover:r-6 transition-all"
                                @mouseenter="hoveredPoint = { ...p, series: 'Diusulkan', value: p.diusulkan }"
                                @mouseleave="hoveredPoint = null"
                            />
                        </g>

                        <!-- Data Points Terlaksana -->
                        <g v-for="p in lineChartPoints.terlaksanaPoints" :key="`t-${p.nama}`">
                            <circle
                                :cx="p.x"
                                :cy="p.y"
                                r="4.5"
                                fill="#0052cc"
                                stroke="#ffffff"
                                stroke-width="2"
                                class="cursor-pointer hover:r-6 transition-all"
                                @mouseenter="hoveredPoint = { ...p, series: 'Terlaksana', value: p.terlaksana }"
                                @mouseleave="hoveredPoint = null"
                            />
                        </g>

                        <!-- Month Labels -->
                        <text
                            v-for="p in lineChartPoints.diusulkanPoints"
                            :key="`lbl-${p.nama}`"
                            :x="p.x"
                            :y="lineChartPoints.H - 4"
                            text-anchor="middle"
                            font-size="11"
                            font-weight="600"
                            fill="#64748b"
                        >
                            {{ p.nama }}
                        </text>
                    </svg>
                </div>
            </div>
        </div>

        <!-- 3. PIE / DONUT CHART VIEW -->
        <div v-else class="py-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <!-- Left: Donut SVG with Center Metric -->
                <div class="md:col-span-5 flex flex-col items-center justify-center relative">
                    <div class="relative w-56 h-56 sm:w-64 sm:h-64 flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 200 200">
                            <!-- Background track circle -->
                            <circle
                                cx="100"
                                cy="100"
                                r="70"
                                fill="transparent"
                                stroke="#f1f5f9"
                                stroke-width="26"
                            />
                            <!-- Slices -->
                            <circle
                                v-for="(slice, i) in donutSlices"
                                :key="`slice-${i}`"
                                cx="100"
                                cy="100"
                                :r="slice.r"
                                fill="transparent"
                                :stroke="slice.warna"
                                stroke-width="26"
                                :stroke-dasharray="slice.dash"
                                :stroke-dashoffset="slice.offset"
                                class="transition-all duration-300 cursor-pointer"
                                :class="{ 'opacity-90 stroke-[30px]': hoveredSlice === slice.label }"
                                @mouseenter="hoveredSlice = slice.label"
                                @mouseleave="hoveredSlice = null"
                            />
                        </svg>

                        <!-- Center Info Text -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none p-4">
                            <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">
                                {{ hoveredSlice ? 'Kategori' : 'Total Kegiatan' }}
                            </span>
                            <div class="text-2xl sm:text-3xl font-black text-slate-900 leading-none mt-1">
                                {{ hoveredSlice ? (kategoriData.find(k => k.label === hoveredSlice)?.count || 0) : totalKategoriCount }}
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 mt-1">
                                {{ hoveredSlice ? (kategoriData.find(k => k.label === hoveredSlice)?.persen || '0%') : `Kegiatan TA ${tahunGrafik}` }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Categories Breakdown List -->
                <div class="md:col-span-7 space-y-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                        Rincian Berdasarkan Modul / Jenis Kegiatan
                    </div>

                    <div class="space-y-2.5">
                        <div
                            v-for="k in kategoriData"
                            :key="k.label"
                            class="p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-100/70 transition-colors flex items-center justify-between gap-4 cursor-pointer"
                            @mouseenter="hoveredSlice = k.label"
                            @mouseleave="hoveredSlice = null"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-3 h-3 rounded-md shrink-0 shadow-2xs" :style="{ backgroundColor: k.warna }"></span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-900 truncate">{{ k.label }}</div>
                                    <div class="text-[11px] text-slate-500">{{ k.count }} Kegiatan &bull; {{ k.kuota || 0 }} Kuota Peserta</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-black text-slate-900">{{ k.persen }}</div>
                                <div class="w-16 sm:w-20 bg-slate-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                    <div class="h-1.5 rounded-full" :style="{ width: k.persen, backgroundColor: k.warna }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 REAL METRIC CARDS (Direct from Database) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-100">
            <!-- 1. Total Proposal Diusulkan -->
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-600 truncate">Proposal Masuk</span>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                        {{ summaryData.totalDiusulkan }} <span class="text-xs font-normal text-slate-500">Proposal</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 mt-0.5">Diusulkan oleh mitra / terjadwal</div>
                </div>
            </div>

            <!-- 2. Kegiatan Terlaksana -->
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-700 shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-600 truncate">Kegiatan Terlaksana</span>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black text-emerald-700 leading-tight">
                        {{ summaryData.totalTerlaksana }} <span class="text-xs font-normal text-slate-500">Selesai</span>
                    </div>
                    <div class="text-[11px] font-semibold text-emerald-800 mt-0.5">
                        {{ summaryData.persenTerlaksana }} dari total proposal
                    </div>
                </div>
            </div>

            <!-- 3. Terjadwal / Siap -->
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-600 truncate">Disetujui / Terjadwal</span>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                        {{ summaryData.totalTerjadwal }} <span class="text-xs font-normal text-slate-500">Kegiatan</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 mt-0.5">Siap &amp; sedang berlangsung</div>
                </div>
            </div>

            <!-- 4. Menunggu Review -->
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-600 truncate">Menunggu Persetujuan</span>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black text-amber-700 leading-tight">
                        {{ summaryData.totalMenunggu }} <span class="text-xs font-normal text-slate-500">Proposal</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 mt-0.5">Perlu diverifikasi admin</div>
                </div>
            </div>
        </div>
    </div>
</template>
