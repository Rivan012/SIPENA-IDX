<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    BoxIcon,
    CheckIcon,
    UserGroupIcon,
    DocsIcon,
    BarChartIcon,
    PieChartIcon,
    ChevronRightIcon
} from '@/Icons';

defineProps({
    metrics: Object,
    kegiatanTerbaru: Array,
    pengajuanTerbaru: Array,
});
</script>

<template>
    <AdminLayout title="Dashboard Administrator">
        <Head title="Dashboard Administrator — SIPENA IDX" />

        <div class="space-y-6">
            <!-- HERO BANNER (TailAdmin Highlight Header with IDX Maroon Tone) -->
            <div class="rounded-2xl bg-gradient-to-r from-[#680A10] to-[#85131B] text-white p-6 sm:p-8 shadow-xs relative overflow-hidden border border-red-950">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <div class="max-w-2xl space-y-2">
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-black/20 text-white text-xs font-bold tracking-wider uppercase border border-white/10">
                            <span>Tahun Anggaran 2026</span>
                            <span class="text-white/40">|</span>
                            <span class="text-rose-100 font-medium">KP BEI Bengkulu</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                            Portal Pengelolaan SIPENA IDX
                        </h1>
                        <p class="text-xs sm:text-sm text-rose-100/90 leading-relaxed">
                            Pusat kendali perencanaan kegiatan edukasi pasar modal, pemantauan partisipasi masyarakat, dan realisasi alokasi belanja RKAP.
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
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-colors"
                        >
                            <span>Tinjau Usulan Anggaran</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- METRIC CARDS (TailAdmin EcommerceMetrics Structure) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <!-- Card 1: Total Kegiatan -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kegiatan</span>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#85131B] flex items-center justify-center border border-red-100">
                            <BoxIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-slate-900 leading-none">{{ metrics.total_kegiatan }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Agenda edukasi 2026</p>
                    </div>
                </div>

                <!-- Card 2: Terlaksana -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlaksana</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                            <CheckIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-emerald-600 leading-none">{{ metrics.kegiatan_terlaksana }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Selesai dievaluasi</p>
                    </div>
                </div>

                <!-- Card 3: Total Peserta -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Peserta</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-100">
                            <UserGroupIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-blue-600 leading-none">{{ metrics.total_peserta }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Peserta terdaftar</p>
                    </div>
                </div>

                <!-- Card 4: Pagu RKAP -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pagu RKAP</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-100">
                            <DocsIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-base font-bold text-slate-900 leading-none truncate" :title="metrics.pagu_anggaran">
                            {{ metrics.pagu_anggaran }}
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Alokasi pagu tahunan</p>
                    </div>
                </div>

                <!-- Card 5: Realisasi Biaya -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Realisasi</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center border border-indigo-100">
                            <BarChartIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-base font-bold text-emerald-700 leading-none truncate" :title="metrics.realisasi_anggaran">
                            {{ metrics.realisasi_anggaran }}
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Anggaran terserap</p>
                    </div>
                </div>

                <!-- Card 6: Serapan RKAP -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">% Serapan</span>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#85131B] flex items-center justify-center border border-rose-100">
                            <PieChartIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-[#85131B] leading-none">{{ metrics.persentase_realisasi }}%</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Tingkat efisiensi biaya</p>
                    </div>
                </div>
            </div>

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
                            <h2 class="text-sm font-bold text-slate-900">Usulan Anggaran Menunggu Review</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Pengajuan proposal kegiatan yang membutuhkan persetujuan</p>
                        </div>
                        <Link :href="route('admin.approval.index')" class="text-xs font-bold text-[#85131B] hover:text-[#680A10] hover:underline flex items-center gap-1">
                            <span>Halaman Approval</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        <div v-for="p in pengajuanTerbaru" :key="p.id" class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 space-y-1">
                                <Link :href="route('admin.pengajuan.show', p.id)" class="font-bold text-slate-900 hover:text-[#85131B] truncate block">
                                    {{ p.nomor_pengajuan }}: {{ p.nama_kegiatan }}
                                </Link>
                                <div class="text-[11px] text-slate-500">
                                    Estimasi Anggaran: <strong class="text-slate-800">{{ p.formatted_anggaran }}</strong>
                                </div>
                            </div>
                            <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold whitespace-nowrap shrink-0 border', p.status_class]">
                                {{ p.status_label }}
                            </span>
                        </div>

                        <div v-if="!pengajuanTerbaru.length" class="py-8 text-center text-xs text-slate-400">
                            Tidak ada pengajuan yang membutuhkan verifikasi saat ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
