<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'SIPENA — IDX Bengkulu',
    },
});

const page = usePage();
const mobileOpen = ref(false);

const authAdmin = computed(() => page.props.auth?.admin || {});
const pendingApproval = computed(() => page.props.pendingApprovalCount || 0);
const flash = computed(() => page.props.flash || {});

const isCurrent = (name) => {
    try {
        return route().current(name);
    } catch {
        return false;
    }
};

const handleLogout = () => {
    router.post(route('admin.logout'));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 flex antialiased">
        <!-- Sidebar Admin (Desktop) -->
        <aside class="w-64 bg-[#680A10] text-white flex-shrink-0 flex flex-col justify-between hidden md:flex h-screen sticky top-0 z-20">
            <div class="overflow-y-auto flex-1">
                <!-- Header Logo -->
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <Link :href="route('admin.dashboard')" class="flex items-center gap-2.5">
                        <img src="/images/idx-logo-icon.png" alt="IDX" class="w-7 h-7 rounded-md object-contain bg-white/10 p-0.5" />
                        <div>
                            <div class="flex items-center gap-1.5 leading-none">
                                <span class="text-base font-bold tracking-tight text-white">SIPENA</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#85131B] border border-white/20 text-white tracking-wider">ADMIN</span>
                            </div>
                            <p class="text-[10px] text-white/60 font-medium mt-1 leading-none">IDX Bengkulu</p>
                        </div>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <div class="p-3">
                    <div class="px-3 pt-2 pb-1.5 text-[10px] font-bold tracking-wider text-white/50 uppercase">
                        MENU UTAMA
                    </div>
                    <nav class="space-y-1">
                        <!-- Dashboard -->
                        <Link :href="route('admin.dashboard')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition',
                                  isCurrent('admin.dashboard') ? 'bg-[#85131B] text-white shadow-xs font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white'
                              ]">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Dashboard</span>
                        </Link>

                        <!-- Kalender -->
                        <Link :href="route('admin.kalender.index')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition',
                                  isCurrent('admin.kalender.*') ? 'bg-[#85131B] text-white shadow-xs font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white'
                              ]">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Kalender</span>
                        </Link>

                        <!-- Kegiatan -->
                        <Link :href="route('admin.kegiatan.index')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition',
                                  isCurrent('admin.kegiatan.*') ? 'bg-[#85131B] text-white shadow-xs font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white'
                              ]">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Kegiatan</span>
                        </Link>

                        <!-- Anggaran & RKAP -->
                        <Link :href="route('admin.rencana-kegiatan.index')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition',
                                  (isCurrent('admin.rencana-kegiatan.*') || isCurrent('admin.anggaran.*')) ? 'bg-[#85131B] text-white shadow-xs font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white'
                              ]">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Anggaran & RKAP</span>
                        </Link>

                        <!-- Approval -->
                        <Link :href="route('admin.approval.index')"
                              :class="[
                                  'flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium transition',
                                  (isCurrent('admin.approval.*') || isCurrent('admin.pengajuan.*')) ? 'bg-[#85131B] text-white shadow-xs font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white'
                              ]">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Approval</span>
                            </div>
                            <span v-if="pendingApproval > 0"
                                  :class="[
                                      'px-1.5 py-0.5 rounded-full text-[10px] font-bold min-w-[18px] text-center leading-none',
                                      (isCurrent('admin.approval.*') || isCurrent('admin.pengajuan.*')) ? 'bg-white text-[#680A10]' : 'bg-amber-400 text-slate-900'
                                  ]">
                                {{ pendingApproval }}
                            </span>
                        </Link>

                        <!-- Pengguna / Monitoring -->
                        <Link :href="route('admin.monitoring')"
                              :class="[
                                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition',
                                  (isCurrent('admin.monitoring*') || isCurrent('admin.pengguna*')) ? 'bg-[#85131B] text-white shadow-xs font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white'
                              ]">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Pengguna</span>
                        </Link>
                    </nav>
                </div>
            </div>

            <!-- Footer: Admin Info & Logout -->
            <div class="p-3 border-t border-white/10 bg-black/10">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-lg bg-[#85131B] border border-white/20 text-white font-bold text-xs flex items-center justify-center shrink-0 uppercase shadow-xs">
                            {{ (authAdmin.name || 'A').substring(0, 2) }}
                        </div>
                        <div class="leading-tight truncate">
                            <div class="text-xs font-bold text-white truncate">{{ authAdmin.name || 'Administrator' }}</div>
                            <div class="text-[10px] text-white/60 truncate">{{ authAdmin.jabatan || 'Admin IDX' }}</div>
                        </div>
                    </div>
                    <button type="button"
                            @click="handleLogout"
                            title="Keluar"
                            class="p-1.5 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="h-14 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-10 shrink-0">
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-xs font-medium text-slate-500 hidden sm:inline">
                        Sistem Informasi Pengelolaan Kegiatan & Anggaran Edukasi
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right leading-tight hidden sm:block">
                        <div class="text-xs font-bold text-slate-900">{{ authAdmin.name || 'Admin BEI' }}</div>
                        <div class="text-[10px] text-slate-400">{{ authAdmin.email || 'admin@bei.co.id' }}</div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-[#680A10] text-white text-xs font-bold flex items-center justify-center uppercase shadow-xs">
                        {{ (authAdmin.name || 'A').substring(0, 2) }}
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-5 sm:p-6 lg:p-8 overflow-y-auto">
                <!-- Flash Notification Banner -->
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
