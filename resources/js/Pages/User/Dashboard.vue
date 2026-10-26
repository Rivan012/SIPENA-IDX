<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
    CheckIcon,
    Calendar2Line,
    DocsIcon,
} from '@/Icons';

const props = defineProps({
    proposedCount: { type: Number, default: 0 },
    approvedCount: { type: Number, default: 0 },
    revisionCount: { type: Number, default: 0 },
    rejectedCount: { type: Number, default: 0 },
    completedCount: { type: Number, default: 0 },
    otherStatusCount: { type: Number, default: 0 },
    totalProposalsCount: { type: Number, default: 0 },
    recentNotifications: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});

const markRead = (n) => {
    router.patch(route('user.notifikasi.baca', n.id), {}, { preserveScroll: true });
};
</script>

<template>
    <UserLayout title="Dashboard Mitra">
        <Head title="Dashboard Mitra — SIPENA IDX" />

        <div class="space-y-6">
            <!-- HERO BANNER (TailAdmin Highlight Banner with IDX Tone) -->
            <div class="rounded-2xl text-white p-6 sm:p-8 shadow-xs relative overflow-hidden border border-red-950 bg-[#680A10]">
                <!-- Background Image & Overlay -->
                <img
                    src="/images/dashboard-banner.jpg"
                    alt="Banner Dashboard SIPENA IDX"
                    class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-black/10 pointer-events-none"></div>

                <div class="max-w-2xl space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-black/40 backdrop-blur-xs text-white text-xs font-bold tracking-wider uppercase border border-white/15">
                        <span>PORTAL EDUKASI RESMI</span>
                        <span class="text-white/40">|</span>
                        <span class="text-rose-100 font-medium">KP BEI Bengkulu</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white drop-shadow-xs">
                        Selamat Datang, {{ authUser.name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-rose-100/95 leading-relaxed drop-shadow-xs">
                        Ajukan dan pantau proposal kegiatan edukasi pasar modal bersama BEI Bengkulu.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-2.5">
                        <Link
                            :href="route('user.kegiatan.create')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#85131B] hover:bg-rose-50 font-bold text-xs rounded-xl shadow-xs transition-colors"
                        >
                            <span>Ajukan Kegiatan Baru</span>
                        </Link>
                        <Link
                            :href="route('user.kegiatan.index')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white font-semibold text-xs rounded-xl border border-white/20 backdrop-blur-xs transition-colors"
                        >
                            <span>Proposal Saya ({{ proposedCount + otherStatusCount + completedCount }})</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- METRIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Card 1: Kegiatan Diusulkan -->
                <Link
                    :href="route('user.kegiatan.index', { status: 'menunggu_approval' })"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition group"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Diusulkan</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-[#85131B] border border-rose-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#85131B]"></span>
                            Menunggu Review
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ proposedCount }}</h3>
                            <p class="text-[11px] text-slate-500 mt-1.5">Proposal menunggu persetujuan admin</p>
                        </div>
                        <span class="text-xs font-semibold text-[#85131B] group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Lihat &rarr;
                        </span>
                    </div>
                </Link>

                <!-- Card 2: Disetujui / Terjadwal -->
                <Link
                    :href="route('user.kegiatan.index', { status: 'disetujui' })"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition group"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disetujui / Terjadwal</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Disetujui
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ otherStatusCount }}</h3>
                            <p class="text-[11px] text-slate-500 mt-1.5">Disetujui &amp; terjadwal diselenggarakan</p>
                        </div>
                        <span class="text-xs font-semibold text-emerald-800 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Lihat &rarr;
                        </span>
                    </div>
                </Link>

                <!-- Card 3: Kegiatan Selesai -->
                <Link
                    :href="route('user.kegiatan.index', { status: 'selesai' })"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition group"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Selesai</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Terlaksana
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">{{ completedCount }}</h3>
                            <p class="text-[11px] text-slate-500 mt-1.5">Proposal yang telah selesai</p>
                        </div>
                        <span class="text-xs font-semibold text-blue-700 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Arsip &rarr;
                        </span>
                    </div>
                </Link>
            </div>

            <!-- PEMBERITAHUAN & NOTIFIKASI TERKINI -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-rose-50 text-[#85131B] border border-rose-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Pemberitahuan Terkini</h2>
                            <p class="text-[11px] text-slate-500">Update status proposal dan kegiatan Anda</p>
                        </div>
                    </div>
                    <Link
                        :href="route('user.notifikasi.index')"
                        class="text-xs font-semibold text-[#85131B] hover:underline flex items-center gap-1"
                    >
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </Link>
                </div>

                <div v-if="recentNotifications && recentNotifications.length > 0" class="divide-y divide-slate-100">
                    <div
                        v-for="notif in recentNotifications"
                        :key="notif.id"
                        :class="['p-4 sm:p-5 flex items-start gap-3.5 transition', notif.is_read ? 'bg-white' : 'bg-rose-50/30']"
                    >
                        <div :class="['w-2.5 h-2.5 rounded-full mt-1.5 shrink-0', notif.is_read ? 'bg-slate-300' : 'bg-[#85131B] ring-4 ring-rose-100']"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h3 :class="['text-xs font-bold truncate', notif.is_read ? 'text-slate-800' : 'text-[#85131B]']">
                                    {{ notif.judul }}
                                </h3>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap shrink-0">
                                    {{ notif.created_at ? new Date(notif.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                {{ notif.pesan }}
                            </p>
                            <div v-if="!notif.is_read" class="mt-2.5 flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="markRead(notif)"
                                    class="text-[11px] font-semibold text-[#85131B] hover:underline"
                                >
                                    Tandai Sudah Dibaca &check;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="py-8 text-center text-slate-400 text-xs">
                    Belum ada pemberitahuan baru untuk akun Anda.
                </div>
            </div>
        </div>
    </UserLayout>
</template>
