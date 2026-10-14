<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
    BoxIcon,
    CheckIcon,
    Calendar2Line,
    UserCircleIcon,
    ChevronRightIcon
} from '@/Icons';

defineProps({
    kegiatanDiikuti: Number,
    kegiatanHadir: Number,
    upcomingEvents: Array,
    myRecentEvents: Array,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});
</script>

<template>
    <UserLayout title="Dashboard Peserta">
        <Head title="Dashboard Peserta — SIPENA IDX" />

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
                        Tingkatkan literasi investasi Anda melalui sekolah pasar modal dan workshop resmi BEI secara gratis dan bersertifikat.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-2.5">
                        <Link
                            :href="route('user.kegiatan.index')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#85131B] hover:bg-rose-50 font-bold text-xs rounded-xl shadow-xs transition-colors"
                        >
                            <span>Jelajahi Katalog Kegiatan</span>
                        </Link>
                        <Link
                            :href="route('user.kegiatan-saya')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-colors"
                        >
                            <span>Kegiatan Terdaftar ({{ kegiatanDiikuti }})</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- METRIC CARDS (TailAdmin Structure) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Program Diikuti -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Program Diikuti</span>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#85131B] flex items-center justify-center border border-red-100">
                            <BoxIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-[#85131B] leading-none">{{ kegiatanDiikuti }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Pendaftaran aktif</p>
                    </div>
                </div>

                <!-- Card 2: Kehadiran di Acara -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kehadiran Acara</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                            <CheckIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-emerald-600 leading-none">{{ kegiatanHadir }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Sertifikat valid</p>
                    </div>
                </div>

                <!-- Card 3: Agenda Tersedia -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Agenda Tersedia</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-100">
                            <Calendar2Line class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-blue-600 leading-none">{{ upcomingEvents.length }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Dapat didaftarkan</p>
                    </div>
                </div>

                <!-- Card 4: Status Akun -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Akun</span>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-100">
                            <UserCircleIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-base font-bold text-emerald-700 leading-none">Terverifikasi</h3>
                        <p class="text-[11px] text-slate-500 mt-1">Peserta Resmi SIPENA</p>
                    </div>
                </div>
            </div>

            <!-- TWO COLUMNS SECTION (TailAdmin ComponentCard Architecture) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Column 1: Program Edukasi Mendatang -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Program Edukasi Mendatang</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jadwal kegiatan yang akan segera dilaksanakan</p>
                        </div>
                        <Link :href="route('user.kegiatan.index')" class="text-xs font-bold text-[#85131B] hover:text-[#680A10] hover:underline flex items-center gap-1">
                            <span>Katalog Lengkap</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        <div v-for="ev in upcomingEvents" :key="ev.id" class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 space-y-1">
                                <Link :href="route('user.kegiatan.show', ev.id)" class="font-bold text-slate-900 hover:text-[#85131B] truncate block">
                                    {{ ev.nama_kegiatan }}
                                </Link>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2">
                                    <span>{{ ev.tanggal ? new Date(ev.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}</span>
                                    <span>&bull;</span>
                                    <span class="truncate">{{ ev.lokasi }}</span>
                                </div>
                            </div>
                            <Link
                                :href="route('user.kegiatan.show', ev.id)"
                                class="px-3 py-1.5 rounded-lg bg-[#85131B] text-white text-[11px] font-bold hover:bg-[#680A10] transition shrink-0 shadow-2xs"
                            >
                                Daftar
                            </Link>
                        </div>

                        <div v-if="!upcomingEvents.length" class="py-8 text-center text-xs text-slate-400">
                            Belum ada jadwal kegiatan baru yang dipublikasikan.
                        </div>
                    </div>
                </div>

                <!-- Column 2: Kegiatan Terdaftar Saya -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Kegiatan Terdaftar Saya</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Status kepesertaan kegiatan yang Anda ikuti</p>
                        </div>
                        <Link :href="route('user.kegiatan-saya')" class="text-xs font-bold text-[#85131B] hover:text-[#680A10] hover:underline flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        <div v-for="my in myRecentEvents" :key="my.id" class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="min-w-0 space-y-1">
                                <Link :href="route('user.kegiatan.show', my.id)" class="font-bold text-slate-900 hover:text-[#85131B] truncate block">
                                    {{ my.nama_kegiatan }}
                                </Link>
                                <div class="text-[11px] text-slate-500">
                                    {{ my.tanggal ? new Date(my.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200 capitalize shrink-0">
                                {{ my.pivot?.status || 'terdaftar' }}
                            </span>
                        </div>

                        <div v-if="!myRecentEvents.length" class="py-8 text-center text-xs text-slate-400">
                            Anda belum mendaftar pada program manapun saat ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
