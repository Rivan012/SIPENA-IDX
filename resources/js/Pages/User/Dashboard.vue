<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

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
    <UserLayout title="Dashboard Peserta — SIPENA IDX">
        <Head title="Dashboard Peserta" />

        <div class="space-y-6">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-[#680A10] to-[#85131B] rounded-2xl p-6 sm:p-8 text-white shadow-xs relative overflow-hidden">
                <div class="max-w-2xl">
                    <span class="px-2.5 py-1 rounded-full bg-white/20 text-white font-bold text-[10px] tracking-wider uppercase">
                        PORTAL EDUKASI IDX BENGKULU
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold mt-2.5 tracking-tight">
                        Selamat Datang, {{ authUser.name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-rose-100/90 mt-1 leading-relaxed">
                        Tingkatkan literasi investasi Anda melalui sekolah pasar modal dan workshop resmi BEI secara gratis dan bersertifikat.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2.5">
                        <Link :href="route('user.kegiatan.index')"
                              class="px-4 py-2 rounded-xl bg-white text-[#680A10] font-bold text-xs hover:bg-rose-50 transition shadow-xs">
                            Jelajahi Kegiatan &rarr;
                        </Link>
                        <Link :href="route('user.kegiatan-saya')"
                              class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition">
                            Kegiatan Saya ({{ kegiatanDiikuti }})
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PROGRAM DIIKUTI</span>
                    <span class="text-2xl font-bold text-[#680A10] mt-1 block">{{ kegiatanDiikuti }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Pendaftaran aktif</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KEHADIRAN DI ACARA</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ kegiatanHadir }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Sertifikat valid</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">AGENDA TERSEDIA</span>
                    <span class="text-2xl font-bold text-blue-600 mt-1 block">{{ upcomingEvents.length }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Bisa didaftarkan</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">STATUS AKUN</span>
                    <span class="text-sm font-bold text-emerald-700 mt-2 block">Terverifikasi</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Peserta Resmi</span>
                </div>
            </div>

            <!-- 2 Columns: Agenda Mendatang & Kegiatan Terdaftar -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Agenda Mendatang -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Program Edukasi Mendatang</h2>
                        <Link :href="route('user.kegiatan.index')" class="text-xs font-semibold text-[#680A10] hover:underline">
                            Katalog Lengkap &rarr;
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div v-for="ev in upcomingEvents" :key="ev.id" class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <Link :href="route('user.kegiatan.show', ev.id)" class="font-bold text-slate-900 hover:text-[#680A10] truncate block">
                                    {{ ev.nama_kegiatan }}
                                </Link>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">
                                    {{ ev.tanggal ? new Date(ev.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }} &bull; {{ ev.lokasi }}
                                </span>
                            </div>
                            <Link :href="route('user.kegiatan.show', ev.id)"
                                  class="px-3 py-1 rounded-lg bg-[#680A10] text-white text-[11px] font-semibold hover:bg-[#54080D] transition shrink-0 shadow-xs">
                                Daftar
                            </Link>
                        </div>
                        <div v-if="!upcomingEvents.length" class="py-8 text-center text-slate-400 text-xs">
                            Belum ada jadwal kegiatan baru yang dipublikasikan.
                        </div>
                    </div>
                </div>

                <!-- Kegiatan Terdaftar Saya -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Kegiatan Terdaftar Saya</h2>
                        <Link :href="route('user.kegiatan-saya')" class="text-xs font-semibold text-[#680A10] hover:underline">
                            Lihat Semua &rarr;
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div v-for="my in myRecentEvents" :key="my.id" class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <Link :href="route('user.kegiatan.show', my.id)" class="font-bold text-slate-900 hover:text-[#680A10] truncate block">
                                    {{ my.nama_kegiatan }}
                                </Link>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">
                                    {{ my.tanggal ? new Date(my.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                </span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 capitalize">
                                {{ my.pivot?.status || 'terdaftar' }}
                            </span>
                        </div>
                        <div v-if="!myRecentEvents.length" class="py-8 text-center text-slate-400 text-xs">
                            Anda belum mendaftar pada program manapun.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
