<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'SIPENA — Portal Peserta IDX',
    },
});

const page = usePage();
const mobileOpen = ref(false);

const authUser = computed(() => page.props.auth?.user || {});
const unreadNotif = computed(() => page.props.userUnreadCount || 0);
const flash = computed(() => page.props.flash || {});

const isCurrent = (name) => {
    try {
        return route().current(name);
    } catch {
        return false;
    }
};

const handleLogout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 flex antialiased">
        <!-- Sidebar User (Desktop) -->
        <aside class="w-64 bg-white border-r border-slate-200 text-slate-700 flex-shrink-0 flex flex-col justify-between hidden md:flex h-screen sticky top-0 z-20">
            <div class="overflow-y-auto flex-1">
                <!-- Brand Header -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <Link :href="route('user.dashboard')" class="flex items-center gap-2.5">
                        <img src="/images/idx-logo-icon.png" alt="IDX" class="w-7 h-7 rounded-md object-contain shadow-xs" />
                        <div>
                            <div class="flex items-center gap-1.5 leading-none">
                                <span class="text-base font-bold tracking-tight text-slate-900">SIPENA</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#680A10] text-white uppercase tracking-wider">PESERTA</span>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium mt-1 leading-none">Edukasi Pasar Modal</p>
                        </div>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <div class="p-3">
                    <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                        MENU UTAMA
                    </div>
                    <nav class="space-y-1">
                        <!-- Dashboard -->
                        <Link :href="route('user.dashboard')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition',
                                  isCurrent('user.dashboard') ? 'bg-[#680A10] text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-rose-50 hover:text-[#680A10] font-medium'
                              ]">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Dashboard</span>
                        </Link>

                        <!-- Katalog Kegiatan -->
                        <Link :href="route('user.kegiatan.index')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition',
                                  isCurrent('user.kegiatan.*') ? 'bg-[#680A10] text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-rose-50 hover:text-[#680A10] font-medium'
                              ]">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Katalog Kegiatan</span>
                        </Link>

                        <!-- Kegiatan Saya -->
                        <Link :href="route('user.kegiatan-saya')"
                              :class="[
                                  'flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition',
                                  isCurrent('user.kegiatan-saya*') ? 'bg-[#680A10] text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-rose-50 hover:text-[#680A10] font-medium'
                              ]">
                            <div class="flex items-center gap-3">
                                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                <span>Kegiatan Saya</span>
                            </div>
                        </Link>

                        <!-- Laporan Edukasi -->
                        <Link :href="route('user.laporan.index')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition',
                                  isCurrent('user.laporan.*') ? 'bg-[#680A10] text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-rose-50 hover:text-[#680A10] font-medium'
                              ]">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Laporan Edukasi</span>
                        </Link>

                        <!-- Notifikasi -->
                        <Link :href="route('user.notifikasi.index')"
                              :class="[
                                  'flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition',
                                  isCurrent('user.notifikasi.*') ? 'bg-[#680A10] text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-rose-50 hover:text-[#680A10] font-medium'
                              ]">
                            <div class="flex items-center gap-3">
                                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span>Notifikasi</span>
                            </div>
                            <span v-if="unreadNotif > 0"
                                  :class="[
                                      'px-1.5 py-0.5 rounded-full font-bold text-[10px] min-w-[18px] text-center leading-none',
                                      isCurrent('user.notifikasi.*') ? 'bg-white text-[#680A10]' : 'bg-red-500 text-white'
                                  ]">
                                {{ unreadNotif }}
                            </span>
                        </Link>

                        <!-- Profil Saya -->
                        <Link :href="route('user.profil.edit')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition',
                                  isCurrent('user.profil.*') ? 'bg-[#680A10] text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-rose-50 hover:text-[#680A10] font-medium'
                              ]">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Profil Saya</span>
                        </Link>
                    </nav>
                </div>
            </div>

            <!-- Footer: User Info & Logout -->
            <div class="p-3 border-t border-slate-100 bg-slate-50/60">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200">
                    <Link :href="route('user.profil.edit')" class="flex items-center gap-2.5 min-w-0 flex-1 hover:opacity-80 transition">
                        <div class="w-8 h-8 rounded-lg bg-[#680A10] text-white font-bold text-xs flex items-center justify-center shrink-0 uppercase shadow-xs">
                            {{ (authUser.name || 'P').substring(0, 2) }}
                        </div>
                        <div class="leading-tight truncate">
                            <div class="text-xs font-bold text-slate-900 truncate">{{ authUser.name || 'Peserta' }}</div>
                            <div class="text-[10px] text-slate-400 truncate">{{ authUser.email || 'peserta@idx.id' }}</div>
                        </div>
                    </Link>
                    <button type="button"
                            @click="handleLogout"
                            title="Keluar"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 sticky top-0 z-10 shrink-0">
                <div class="flex items-center gap-3 md:hidden">
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="font-bold text-slate-900 text-sm">SIPENA</span>
                </div>

                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                    <span class="font-semibold text-slate-700">Kantor Perwakilan BEI Bengkulu</span>
                    <span>&bull;</span>
                    <span>Portal Edukasi &amp; Sosialisasi Pasar Modal</span>
                </div>

                <div class="flex items-center gap-3 ml-auto">
                    <Link :href="route('user.notifikasi.index')"
                          class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span v-if="unreadNotif > 0" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-600 ring-2 ring-white"></span>
                    </Link>

                    <Link :href="route('user.profil.edit')" class="flex items-center gap-2.5 pl-2 border-l border-slate-200 hover:opacity-80 transition">
                        <div class="w-9 h-9 rounded-xl bg-[#680A10] text-white flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                            {{ (authUser.name || 'P').substring(0, 2) }}
                        </div>
                        <div class="hidden sm:block leading-tight text-left">
                            <div class="text-xs font-semibold text-slate-800">{{ authUser.name || 'Peserta' }}</div>
                            <div class="text-[10px] text-slate-400">Peserta Terdaftar</div>
                        </div>
                    </Link>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-5 sm:p-6 lg:p-8 overflow-y-auto">
                <div v-if="flash.success"
                     class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-xs">
                    <span>{{ flash.success }}</span>
                </div>
                <div v-if="flash.error"
                     class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-xs">
                    <span>{{ flash.error }}</span>
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>
