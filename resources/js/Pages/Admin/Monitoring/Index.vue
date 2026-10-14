<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    users: Object,
    admins: Array,
    stats: Object,
    role: String,
    filters: Object,
});

const activeTab = ref('users');
const search = ref(props.filters?.search || '');

const handleFilter = () => {
    router.get(route('admin.monitoring'), {
        search: search.value || undefined,
        role: activeTab.value,
    }, { preserveState: true });
};

// Modal riwayat kegiatan user
const showHistoryModal = ref(false);
const selectedUser = ref(null);

const viewHistory = (u) => {
    selectedUser.value = u;
    showHistoryModal.value = true;
};
</script>

<template>
    <AdminLayout title="Manajemen Pengguna — SIPENA IDX">
        <Head title="Manajemen Pengguna & Peserta" />

        <div class="space-y-5">
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna &amp; Peserta</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring data akun peserta edukasi pasar modal dan jajaran tim administrator.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="route('admin.monitoring.export')"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs">
                        &darr; Ekspor Data CSV
                    </a>
                </div>
            </div>

            <!-- 4 KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PESERTA TERDAFTAR</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ stats.total_users }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Akun masyarakat & mhs</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ADMINISTRATOR</span>
                    <span class="text-2xl font-bold text-blue-600 mt-1 block">{{ stats.total_admins }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Tim KP BEI Bengkulu</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL PARTISIPASI</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ stats.total_partisipasi }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Pendaftaran kegiatan</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KEHADIRAN TERVERIFIKASI</span>
                    <span class="text-2xl font-bold text-[#680A10] mt-1 block">{{ stats.total_hadir }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Peserta hadir di acara</span>
                </div>
            </div>

            <!-- Tabs Switcher & Search -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                    <button type="button" @click="activeTab = 'users'"
                            :class="[
                                'px-4 py-1.5 rounded-lg text-xs font-semibold transition',
                                activeTab === 'users' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
                            ]">
                        Peserta Edukasi ({{ users.total || 0 }})
                    </button>
                    <button type="button" @click="activeTab = 'admins'"
                            :class="[
                                'px-4 py-1.5 rounded-lg text-xs font-semibold transition',
                                activeTab === 'admins' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
                            ]">
                        Tim Administrator ({{ admins?.length || 0 }})
                    </button>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-sm">
                    <input v-model="search"
                           @keyup.enter="handleFilter"
                           type="text"
                           placeholder="Cari nama, email..."
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <button type="button" @click="handleFilter"
                            class="px-4 py-2 bg-[#680A10] text-white text-xs font-semibold rounded-xl hover:bg-[#54080D]">
                        Cari
                    </button>
                </div>
            </div>

            <!-- Tab 1: Peserta Table -->
            <div v-show="activeTab === 'users'" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">NAMA PESERTA</th>
                                <th class="py-3 px-4">EMAIL & KONTAK</th>
                                <th class="py-3 px-4 text-center">KEGIATAN DIIKUTI</th>
                                <th class="py-3 px-4">REGISTRASI</th>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="u in users.data" :key="u.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ u.name }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    <div>{{ u.email }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ u.no_telepon || '-' }}</div>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-800">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">
                                        {{ u.kegiatan?.length || 0 }} Acara
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                    {{ u.created_at ? new Date(u.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <button type="button" @click="viewHistory(u)"
                                            class="text-xs font-semibold text-[#680A10] hover:underline">
                                        Riwayat Kegiatan &rarr;
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!users.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data peserta ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="users.links"
                    :from="users.from"
                    :to="users.to"
                    :total="users.total"
                    item-label="pengguna"
                />
            </div>

            <!-- Tab 2: Administrator Table -->
            <div v-show="activeTab === 'admins'" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">NAMA LENGKAP</th>
                                <th class="py-3 px-4">EMAIL RESMI</th>
                                <th class="py-3 px-4">JABATAN</th>
                                <th class="py-3 px-4">HAK AKSES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="a in admins" :key="a.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 font-bold text-slate-900">{{ a.name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ a.email }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ a.jabatan || 'Admin Edukasi' }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                                        Super Administrator
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Modal -->
        <div v-if="showHistoryModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Partisipasi Kegiatan</h3>
                        <p class="text-xs text-slate-500">{{ selectedUser?.name }} ({{ selectedUser?.email }})</p>
                    </div>
                    <button type="button" @click="showHistoryModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto text-xs divide-y divide-slate-100">
                    <div v-for="k in selectedUser?.kegiatan" :key="k.id" class="pt-2 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-900">{{ k.nama_kegiatan }}</div>
                            <div class="text-[11px] text-slate-400">{{ k.tanggal ? new Date(k.tanggal).toLocaleDateString('id-ID') : '-' }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 capitalize">
                            {{ k.pivot?.status || 'terdaftar' }}
                        </span>
                    </div>
                    <div v-if="!selectedUser?.kegiatan?.length" class="py-8 text-center text-slate-400">
                        Peserta ini belum terdaftar pada kegiatan manapun.
                    </div>
                </div>

                <button type="button" @click="showHistoryModal = false"
                        class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
