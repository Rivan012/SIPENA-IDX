<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DashboardTrendChart from '@/Components/DashboardTrendChart.vue';
import {
    BoxIcon,
    CheckIcon,
    UserGroupIcon,
    ChevronRightIcon
} from '@/Icons';

defineProps({
    metrics: Object,
    kegiatanTerbaru: Array,
    proposalMenungguReview: Array,
    rkapChart: Object,
});
</script>

<template>
    <AdminLayout title="Dashboard Administrator">
        <Head title="Dashboard Administrator — SIPENA IDX" />

        <div class="space-y-6">
            <!-- HERO BANNER (TailAdmin Highlight Header with IDX Maroon Tone) -->
            <div class="rounded-2xl text-white p-6 sm:p-8 shadow-xs relative overflow-hidden border border-red-950 bg-[#680A10]">
                <!-- Background Image & Overlay -->
                <img
                    src="/images/dashboard-banner.jpg"
                    alt="Banner Dashboard SIPENA IDX"
                    class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-black/10 pointer-events-none"></div>

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <div class="max-w-2xl space-y-2">
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-black/40 backdrop-blur-xs text-white text-xs font-bold tracking-wider uppercase border border-white/15">
                            <span>Agenda Edukasi</span>
                            <span class="text-white/40">|</span>
                            <span class="text-rose-100 font-medium">KP BEI Bengkulu</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white drop-shadow-xs">
                            Portal Pengelolaan SIPENA IDX
                        </h1>
                        <p class="text-xs sm:text-sm text-rose-100/95 leading-relaxed drop-shadow-xs">
                            Pusat kendali jadwal kegiatan edukasi pasar modal dan partisipasi masyarakat.
                        </p>
                    </div>

                    <div class="flex flex-wrap lg:flex-col gap-2.5 shrink-0">
                        <Link
                            :href="route('admin.kegiatan.create')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#85131B] hover:bg-rose-50 font-bold text-xs rounded-xl shadow-xs transition-colors"
                        >
                            <span>+ Tambah Kegiatan Baru</span>
                        </Link>
                        <Link
                            :href="route('admin.approval.index')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white font-semibold text-xs rounded-xl border border-white/20 backdrop-blur-xs transition-colors"
                        >
                            <span>Tinjau Proposal Mitra</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- METRIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Card 1: Total Kegiatan / Jadwal Mendatang -->
                <Link
                    :href="route('admin.kegiatan.index')"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition group"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jadwal Mendatang</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-[#85131B] border border-rose-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#85131B]"></span>
                            Terjadwal
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ metrics.total_kegiatan }}</h3>
                            <p class="text-[11px] text-slate-500 mt-1.5">Agenda edukasi aktif</p>
                        </div>
                        <span class="text-xs font-semibold text-[#85131B] group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Kelola &rarr;
                        </span>
                    </div>
                </Link>

                <!-- Card 2: Terlaksana -->
                <Link
                    :href="route('admin.kegiatan.index', { status: 'selesai' })"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition group"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlaksana</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Realisasi
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ metrics.kegiatan_terlaksana }}</h3>
                            <p class="text-[11px] text-slate-500 mt-1.5">Kegiatan selesai</p>
                        </div>
                        <span class="text-xs font-semibold text-emerald-800 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Lihat &rarr;
                        </span>
                    </div>
                </Link>

                <!-- Card 3: Total Mitra -->
                <Link
                    :href="route('admin.monitoring')"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition group"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Mitra</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Mitra Aktif
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ metrics.total_mitra ?? metrics.total_peserta ?? 0 }}</h3>
                            <p class="text-[11px] text-slate-500 mt-1.5">Mitra terdaftar</p>
                        </div>
                        <span class="text-xs font-semibold text-blue-700 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Data &rarr;
                        </span>
                    </div>
                </Link>
            </div>

            <!-- GRAFIK TREN KEGIATAN & PROPOSAL (BATANG, GARIS, LINGKARAN) -->
            <DashboardTrendChart :rkap-chart="rkapChart" />

            <!-- TWO COLUMNS SECTION (TailAdmin ComponentCard Architecture) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Column 1: Kegiatan Terbaru -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Agenda Kegiatan Terbaru</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Daftar agenda edukasi yang baru dijadwalkan</p>
                        </div>
                        <Link :href="route('admin.kegiatan.index')" class="text-xs font-bold text-[#85131B] hover:text-[#680A10] hover:underline flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        <div v-for="k in kegiatanTerbaru" :key="k.id" class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 space-y-1">
                                <Link :href="route('admin.kegiatan.show', k.id)" class="font-bold text-slate-900 hover:text-[#85131B] truncate block">
                                    {{ k.nama_kegiatan }}
                                </Link>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2">
                                    <span>{{ k.tanggal }}</span>
                                    <span>&bull;</span>
                                    <span class="truncate">{{ k.lokasi }}</span>
                                </div>
                            </div>
                            <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold whitespace-nowrap shrink-0 border', k.status_class]">
                                {{ k.status_label }}
                            </span>
                        </div>

                        <div v-if="!kegiatanTerbaru.length" class="py-8 text-center text-xs text-slate-400">
                            Belum ada agenda kegiatan yang terdaftar.
                        </div>
                    </div>
                </div>

                <!-- Column 2: Pengajuan Menunggu Review -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Proposal Mitra Menunggu Review</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Pengajuan proposal kegiatan yang membutuhkan persetujuan</p>
                        </div>
                        <Link :href="route('admin.approval.index')" class="text-xs font-bold text-[#85131B] hover:text-[#680A10] hover:underline flex items-center gap-1">
                            <span>Halaman Approval</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        <div v-for="proposal in proposalMenungguReview" :key="proposal.id" class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 space-y-1">
                                <Link :href="route('admin.kegiatan.show', proposal.id)" class="font-bold text-slate-900 hover:text-[#85131B] truncate block">
                                    {{ proposal.nama_kegiatan }}
                                </Link>
                                <div class="text-[11px] text-slate-500">
                                    Pengusul: <strong class="text-slate-800">{{ proposal.pengusul || 'Mitra' }}</strong>
                                </div>
                            </div>
                            <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold whitespace-nowrap shrink-0 border', proposal.status_class]">
                                {{ proposal.status_label }}
                            </span>
                        </div>

                        <div v-if="!proposalMenungguReview.length" class="py-8 text-center text-xs text-slate-400">
                            Tidak ada proposal mitra yang membutuhkan verifikasi saat ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
