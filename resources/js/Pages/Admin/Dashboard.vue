<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    metrics: Object,
    kegiatanTerbaru: Array,
    pengajuanTerbaru: Array,
});
</script>

<template>
    <AdminLayout title="Dashboard Administrator">
        <Head title="Dashboard Admin" />

        <div class="space-y-6">
            <!-- Hero Maroon Banner -->
            <div class="bg-[#680A10] text-white rounded-2xl p-6 shadow-xs relative overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="px-2.5 py-1 rounded bg-[#54080D] text-white font-bold text-[10px] tracking-wider uppercase">
                                TAHUN ANGGARAN 2026
                            </span>
                            <span class="text-rose-200/80 text-xs">Kantor Perwakilan BEI Bengkulu</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mt-2">
                            Selamat Datang di Portal SIPENA
                        </h1>
                        <p class="text-xs sm:text-sm text-rose-100/90 mt-1 leading-relaxed">
                            Sistem Informasi Pengelolaan Kegiatan Edukasi, Sosialisasi Pasar Modal, dan Anggaran RKAP Terpadu.
                        </p>
                    </div>

                    <div class="flex flex-wrap lg:flex-col gap-2 shrink-0">
                        <Link :href="route('admin.kegiatan.create')"
                              class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs">
                            + Tambah Kegiatan
                        </Link>
                        <Link :href="route('admin.approval.index')"
                              class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium text-xs rounded-xl border border-white/20 transition">
                            Tinjau Approval
                        </Link>
                    </div>
                </div>
            </div>

            <!-- KPI Metric Cards Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL KEGIATAN</span>
                    <span class="text-2xl font-bold text-slate-900 mt-2 block">{{ metrics.total_kegiatan }}</span>
                    <span class="text-[11px] text-slate-500 mt-1 block">Program Kerja</span>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TERLAKSANA</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-2 block">{{ metrics.kegiatan_terlaksana }}</span>
                    <span class="text-[11px] text-slate-500 mt-1 block">Selesai Evaluasi</span>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL PESERTA</span>
                    <span class="text-2xl font-bold text-blue-600 mt-2 block">{{ metrics.total_peserta }}</span>
                    <span class="text-[11px] text-slate-500 mt-1 block">Masyarakat & Mhs</span>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PAGU RKAP</span>
                    <span class="text-sm font-bold text-slate-900 mt-2 block truncate" :title="metrics.pagu_anggaran">{{ metrics.pagu_anggaran }}</span>
                    <span class="text-[11px] text-slate-500 mt-1 block">Alokasi Tahunan</span>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">REALISASI BIAYA</span>
                    <span class="text-sm font-bold text-emerald-700 mt-2 block truncate" :title="metrics.realisasi_anggaran">{{ metrics.realisasi_anggaran }}</span>
                    <span class="text-[11px] text-slate-500 mt-1 block">Terserap Pos</span>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">% SERAPAN</span>
                    <span class="text-2xl font-bold text-[#680A10] mt-2 block">{{ metrics.persentase_realisasi }}%</span>
                    <span class="text-[11px] text-slate-500 mt-1 block">Efisiensi Anggaran</span>
                </div>
            </div>

            <!-- 2 Columns Grid: Kegiatan Terbaru & Pengajuan Terbaru -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kegiatan Terbaru -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Agenda Kegiatan Terbaru</h2>
                        <Link :href="route('admin.kegiatan.index')" class="text-xs font-semibold text-[#680A10] hover:underline">
                            Lihat Semua &rarr;
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div v-for="k in kegiatanTerbaru" :key="k.id" class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <Link :href="route('admin.kegiatan.show', k.id)" class="font-bold text-slate-900 hover:text-[#680A10] truncate block">
                                    {{ k.nama_kegiatan }}
                                </Link>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">
                                    {{ k.tanggal }} &bull; {{ k.lokasi }}
                                </span>
                            </div>
                            <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap', k.status_class]">
                                {{ k.status_label }}
                            </span>
                        </div>
                        <div v-if="!kegiatanTerbaru.length" class="py-8 text-center text-xs text-slate-400">
                            Belum ada data kegiatan.
                        </div>
                    </div>
                </div>

                <!-- Pengajuan Terbaru -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Pengajuan Anggaran Menunggu Review</h2>
                        <Link :href="route('admin.approval.index')" class="text-xs font-semibold text-[#680A10] hover:underline">
                            Halaman Approval &rarr;
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div v-for="p in pengajuanTerbaru" :key="p.id" class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <Link :href="route('admin.pengajuan.show', p.id)" class="font-bold text-slate-900 hover:text-[#680A10] truncate block">
                                    {{ p.nomor_pengajuan }} &mdash; {{ p.nama_kegiatan }}
                                </Link>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">
                                    Anggaran: <strong class="text-slate-800">{{ p.formatted_anggaran }}</strong>
                                </span>
                            </div>
                            <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap', p.status_class]">
                                {{ p.status_label }}
                            </span>
                        </div>
                        <div v-if="!pengajuanTerbaru.length" class="py-8 text-center text-xs text-slate-400">
                            Tidak ada pengajuan pending.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
