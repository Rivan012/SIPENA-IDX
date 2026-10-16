<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
    CheckIcon,
    Calendar2Line,
    DocsIcon,
} from '@/Icons';

const props = defineProps({
    proposedCount: Number,
    otherStatusCount: Number,
    completedCount: Number,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});
</script>

<template>
    <UserLayout title="Dashboard Mitra">
        <Head title="Dashboard Mitra — SIPENA IDX" />

        <div class="space-y-6">
            <!-- HERO BANNER (TailAdmin Highlight Banner with IDX Tone) -->
            <div class="rounded-2xl bg-gradient-to-r from-[#680A10] to-[#85131B] text-white p-6 sm:p-8 shadow-xs relative overflow-hidden border border-red-950">
                <div class="max-w-2xl space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-black/20 text-white text-xs font-bold tracking-wider uppercase border border-white/10">
                        <span>PORTAL EDUKASI RESMI</span>
                        <span class="text-white/40">|</span>
                        <span class="text-rose-100 font-medium">KP BEI Bengkulu</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Selamat Datang, {{ authUser.name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-rose-100/90 leading-relaxed">
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
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-colors"
                        >
                            <span>Proposal Saya ({{ proposedCount + otherStatusCount + completedCount }})</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- METRIC CARDS (TailAdmin Structure) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Diusulkan</span>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#85131B] flex items-center justify-center border border-red-100">
                            <DocsIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-[#85131B] leading-none">{{ proposedCount }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Menunggu persetujuan admin</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disetujui / Status Lainnya</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                            <CheckIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-emerald-600 leading-none">{{ otherStatusCount }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Disetujui, terjadwal, berjalan, atau status lainnya</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Selesai</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-100">
                            <Calendar2Line class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-blue-600 leading-none">{{ completedCount }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Proposal yang telah selesai dilaksanakan</p>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
