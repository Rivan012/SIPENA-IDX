<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import {
    LayoutDashboardIcon,
    BoxIcon,
    Calendar2Line,
    DocsIcon,
    BellIcon,
    MenuIcon,
    LogoutIcon,
    ChevronRightIcon,
    UserCircleIcon
} from '@/Icons';

defineProps({
    title: {
        type: String,
        default: 'Portal Peserta',
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
    <div class="min-h-screen bg-slate-50 text-slate-800 flex font-sans antialiased selection:bg-[#85131B] selection:text-white">
        <!-- BACKDROP MOBILE (TailAdmin Backdrop) -->
        <div
            v-if="mobileOpen"
            @click="mobileOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden"
        ></div>

        <!-- SIDEBAR (TailAdmin AppSidebar Architecture with #9F0E0F) -->
        <aside
            :class="[
                'fixed md:sticky top-0 start-0 z-50 md:z-20 h-screen w-64 bg-[#9F0E0F] text-white border-r border-[#850C0D] flex flex-col justify-between transition-transform duration-300 ease-in-out shrink-0',
                mobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
            ]"
        >
            <div class="flex-1 overflow-y-auto no-scrollbar">
                <!-- Brand Header -->
                <div class="h-16 px-4 border-b border-white/10 flex items-center justify-between">
                    <Link :href="route('user.dashboard')" class="flex items-center gap-2.5 min-w-0">
                        <div class="bg-white px-2 py-1 rounded-lg flex items-center shadow-xs shrink-0">
                            <img src="/images/logo.svg" alt="SIPENA IDX" class="h-7 w-auto" />
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-white/15 text-white border border-white/20 uppercase tracking-wider shrink-0">
                            PESERTA
                        </span>
                    </Link>
                    <button
                        @click="mobileOpen = false"
                        type="button"
                        class="md:hidden text-rose-200 hover:text-white p-1 rounded-md"
                        aria-label="Tutup menu navigasi"
                    >
                        ✕
                    </button>
                </div>

                <!-- Navigation Links List (TailAdmin Menu Group) -->
                <div class="p-4 space-y-6">
                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-rose-200/80 uppercase">
                            AKTIVITAS BELAJAR
                        </div>
                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <Link
                                :href="route('user.dashboard')"
                                :class="[
                                    'menu-item',
                                    isCurrent('user.dashboard') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <LayoutDashboardIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Dashboard Peserta</span>
                            </Link>

                            <!-- Katalog Kegiatan -->
                            <Link
                                :href="route('user.kegiatan.index')"
                                :class="[
                                    'menu-item',
                                    isCurrent('user.kegiatan.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <BoxIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Katalog Kegiatan</span>
                            </Link>

                            <!-- Kegiatan Saya -->
                            <Link
                                :href="route('user.kegiatan-saya')"
                                :class="[
                                    'menu-item',
                                    isCurrent('user.kegiatan-saya*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <Calendar2Line class="w-4 h-4 shrink-0" />
                                <span class="truncate">Kegiatan Terdaftar</span>
                            </Link>

                            <!-- Laporan Edukasi -->
                            <Link
                                :href="route('user.laporan.index')"
                                :class="[
                                    'menu-item',
                                    isCurrent('user.laporan.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <DocsIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Laporan Pelaksanaan</span>
                            </Link>
                        </nav>
                    </div>

                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-rose-200/80 uppercase">
                            PENGATURAN AKUN
                        </div>
                        <nav class="space-y-1">
                            <!-- Notifikasi -->
                            <Link
                                :href="route('user.notifikasi.index')"
                                :class="[
                                    'menu-item justify-between',
                                    isCurrent('user.notifikasi.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <BellIcon class="w-4 h-4 shrink-0" />
                                    <span class="truncate">Notifikasi Peserta</span>
                                </div>
                                <span
                                    v-if="unreadNotif > 0"
                                    :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0',
                                        isCurrent('user.notifikasi.*')
                                            ? 'bg-[#9F0E0F] text-white'
                                            : 'bg-white text-[#9F0E0F]'
                                    ]"
                                >
                                    {{ unreadNotif }}
                                </span>
                            </Link>

                            <!-- Profil -->
                            <Link
                                :href="route('user.profil.edit')"
                                :class="[
                                    'menu-item',
                                    isCurrent('user.profil.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <UserCircleIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Profil Saya</span>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Footer: User Profile Card & Logout -->
            <div class="p-3 border-t border-white/10 bg-black/15">
                <div class="flex items-center justify-between p-2 rounded-xl bg-black/20 border border-white/10 shadow-2xs">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-lg bg-white text-[#9F0E0F] font-black text-xs flex items-center justify-center shrink-0 uppercase shadow-2xs">
                            {{ (authUser.name || 'U').substring(0, 2) }}
                        </div>
                        <div class="leading-tight truncate">
                            <div class="text-xs font-bold text-white truncate">{{ authUser.name || 'Peserta' }}</div>
                            <div class="text-[10px] text-rose-200 truncate">{{ authUser.email || 'peserta@idx.co.id' }}</div>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="handleLogout"
                        title="Keluar dari akun"
                        class="p-1.5 rounded-lg text-rose-200 hover:text-white hover:bg-white/15 transition-colors cursor-pointer"
                    >
                        <LogoutIcon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </aside>

        <!-- MAIN LAYOUT WRAPPER (TailAdmin Header & Body) -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- HEADER (TailAdmin AppHeader) -->
            <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 cursor-pointer"
                        aria-label="Buka navigasi sidebar"
                    >
                        <MenuIcon class="w-5 h-5" />
                    </button>

                    <!-- Breadcrumbs TailAdmin -->
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                        <Link :href="route('user.dashboard')" class="hover:text-slate-900 transition-colors">Portal</Link>
                        <ChevronRightIcon class="w-3.5 h-3.5 text-slate-400" />
                        <span class="text-slate-900 font-bold">{{ title }}</span>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <Link
                        :href="route('home')"
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-[#85131B] bg-slate-50 hover:bg-red-50 border border-slate-200 rounded-lg transition-colors"
                    >
                        <span>Jadwal Edukasi Publik</span>
                    </Link>

                    <!-- Notification Pill Icon -->
                    <Link
                        :href="route('user.notifikasi.index')"
                        class="relative p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                        title="Notifikasi"
                    >
                        <BellIcon class="w-4 h-4" />
                        <span
                            v-if="unreadNotif > 0"
                            class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#85131B]"
                        ></span>
                    </Link>

                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <!-- User Avatar & Identity -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-[#85131B] text-white text-xs font-bold flex items-center justify-center uppercase shadow-2xs">
                            {{ (authUser.name || 'U').substring(0, 2) }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <div class="text-xs font-bold text-slate-900">{{ authUser.name || 'Peserta' }}</div>
                            <div class="text-[10px] text-slate-500">{{ authUser.email || 'peserta@idx.co.id' }}</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">
                <!-- Flash Notification Banners (TailAdmin Alert Style) -->
                <div
                    v-if="flash.success"
                    class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-2xs"
                >
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span class="font-medium">{{ flash.success }}</span>
                    </div>
                </div>
                <div
                    v-if="flash.error"
                    class="bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-2xs"
                >
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        <span class="font-medium">{{ flash.error }}</span>
                    </div>
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>
