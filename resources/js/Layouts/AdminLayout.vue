<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import {
    LayoutDashboardIcon,
    CalenderIcon,
    BoxIcon,
    TableIcon,
    CheckIcon,
    UserGroupIcon,
    LogoutIcon,
    MenuIcon,
    ChevronRightIcon,
    UserCircleIcon,
} from '@/Icons';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { confirmDialog, showToast } from '@/Utils/sweetalert';

defineProps({
    title: {
        type: String,
        default: 'Admin Portal',
    },
});

const page = usePage();
const mobileOpen = ref(false);

const authAdmin = computed(() => page.props.auth?.admin || {});
const pendingApproval = computed(() => page.props.pendingApprovalCount || 0);
const flash = computed(() => page.props.flash || {});

watch(() => flash.value.success, (msg) => {
    if (msg) showToast(msg, 'success');
});

watch(() => flash.value.error, (msg) => {
    if (msg) showToast(msg, 'error');
});

onMounted(() => {
    if (flash.value?.success) showToast(flash.value.success, 'success');
    if (flash.value?.error) showToast(flash.value.error, 'error');
});

const isCurrent = (name) => {
    try {
        return route().current(name);
    } catch {
        return false;
    }
};

const handleLogout = async () => {
    const ok = await confirmDialog({
        title: 'Keluar dari Akun?',
        text: 'Apakah Anda yakin ingin keluar dari sesi administrator SIPENA IDX?',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        icon: 'warning',
    });
    if (ok) {
        router.post(route('logout'));
    }
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
                <div class="p-4 border-b border-white/10 flex items-center justify-between gap-2">
                    <Link :href="route('admin.dashboard')" class="block flex-1 min-w-0">
                        <div class="w-full bg-white rounded-xl p-3 shadow-xs flex items-center justify-center hover:bg-slate-50 transition-colors">
                            <img src="/images/logo.svg" alt="SIPENA IDX" class="w-full h-auto object-contain" />
                        </div>
                    </Link>
                    <button
                        @click="mobileOpen = false"
                        type="button"
                        class="md:hidden text-rose-200 hover:text-white p-1 rounded-md shrink-0"
                        aria-label="Tutup menu navigasi"
                    >
                        ✕
                    </button>
                </div>

                <!-- Navigation Links List (TailAdmin Menu Group) -->
                <div class="p-4 space-y-6">
                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-rose-200/80 uppercase">
                            MENU UTAMA
                        </div>
                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <Link
                                :href="route('admin.dashboard')"
                                :class="[
                                    'menu-item',
                                    isCurrent('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <LayoutDashboardIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Dashboard</span>
                            </Link>

                            <!-- Kalender -->
                            <Link
                                :href="route('admin.kalender.index')"
                                :class="[
                                    'menu-item',
                                    isCurrent('admin.kalender.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <CalenderIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Kalender Kegiatan</span>
                            </Link>

                            <!-- Kegiatan -->
                            <Link
                                :href="route('admin.kegiatan.index')"
                                :class="[
                                    'menu-item',
                                    isCurrent('admin.kegiatan.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <BoxIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Kelola Kegiatan</span>
                            </Link>
                        </nav>
                    </div>

                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-rose-200/80 uppercase">
                            PERENCANAAN & PENGAWASAN
                        </div>
                        <nav class="space-y-1">
                            <!-- Anggaran & RKAP -->
                            <Link
                                :href="route('admin.rencana-kegiatan.index')"
                                :class="[
                                    'menu-item',
                                    (isCurrent('admin.rencana-kegiatan.*') || isCurrent('admin.anggaran.*')) ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <DocsIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Anggaran & RKAP</span>
                            </Link>

                            <!-- Approval Proposal -->
                            <Link
                                :href="route('admin.approval.index')"
                                :class="[
                                    'menu-item justify-between',
                                    (isCurrent('admin.approval.*') || isCurrent('admin.pengajuan.*')) ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <CheckIcon class="w-4 h-4 shrink-0" />
                                    <span class="truncate">Approval Usulan</span>
                                </div>
                                <span
                                    v-if="pendingApproval > 0"
                                    :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0',
                                        (isCurrent('admin.approval.*') || isCurrent('admin.pengajuan.*'))
                                            ? 'bg-[#9F0E0F] text-white'
                                            : 'bg-white text-[#9F0E0F]'
                                    ]"
                                >
                                    {{ pendingApproval }}
                                </span>
                            </Link>

                            <!-- Pengguna & Monitoring -->
                            <Link
                                :href="route('admin.monitoring')"
                                :class="[
                                    'menu-item',
                                    (isCurrent('admin.monitoring*') || isCurrent('admin.pengguna*')) ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <UserGroupIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Monitoring Pengguna</span>
                            </Link>
                        </nav>
                    </div>

                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-rose-200/80 uppercase">
                            PENGATURAN AKUN
                        </div>
                        <nav class="space-y-1">
                            <Link
                                :href="route('admin.profil.edit')"
                                :class="[
                                    'menu-item',
                                    isCurrent('admin.profil.*') ? 'menu-item-active' : 'menu-item-inactive'
                                ]"
                            >
                                <UserCircleIcon class="w-4 h-4 shrink-0" />
                                <span class="truncate">Profil Saya</span>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Footer: Admin Profile Card & Logout -->
            <div class="p-3 border-t border-white/10 bg-black/15">
                <div class="flex items-center justify-between p-2 rounded-xl bg-black/20 border border-white/10 shadow-2xs">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <img v-if="authAdmin.profile_photo_url" :src="authAdmin.profile_photo_url" :alt="`Foto profil ${authAdmin.name}`" class="h-8 w-8 shrink-0 rounded-lg object-cover" />
                        <div v-else class="w-8 h-8 rounded-lg bg-white text-[#9F0E0F] font-black text-xs flex items-center justify-center shrink-0 uppercase shadow-2xs">
                            {{ (authAdmin.name || 'A').substring(0, 2) }}
                        </div>
                        <div class="leading-tight truncate">
                            <div class="text-xs font-bold text-white truncate">{{ authAdmin.name || 'Administrator' }}</div>
                            <div class="text-[10px] text-rose-200 truncate font-medium">{{ authAdmin.jabatan || 'Administrator' }}</div>
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
                        <Link :href="route('admin.dashboard')" class="hover:text-slate-900 transition-colors">Admin</Link>
                        <ChevronRightIcon class="w-3.5 h-3.5 text-slate-400" />
                        <span class="text-slate-900 font-bold">{{ title }}</span>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <ThemeToggle />
                                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <!-- User Card -->
                    <div class="flex items-center gap-2.5">
                        <img v-if="authAdmin.profile_photo_url" :src="authAdmin.profile_photo_url" :alt="`Foto profil ${authAdmin.name}`" class="h-8 w-8 rounded-full object-cover" />
                        <div v-else class="w-8 h-8 rounded-full bg-[#85131B] text-white text-xs font-bold flex items-center justify-center uppercase shadow-2xs">
                            {{ (authAdmin.name || 'A').substring(0, 2) }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <div class="text-xs font-bold text-slate-900">{{ authAdmin.name || 'Admin BEI' }}</div>
                            <div class="text-[10px] font-semibold text-[#85131B]">Administrator</div>
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
