<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    kegiatanList: Object,
    categories: Array,
    userKegiatanIds: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const handleFilter = () => {
    router.get(route('user.kegiatan.index'), {
        search: search.value || undefined,
        jenis: props.filters?.jenis || undefined,
    }, { preserveState: true });
};

const filterJenis = (cat) => {
    router.get(route('user.kegiatan.index'), {
        search: search.value || undefined,
        jenis: cat || undefined,
    }, { preserveState: true });
};

const daftar = async (keg) => {
    const ok = await confirmDialog({
        title: 'Daftar Kegiatan Edukasi?',
        text: `Konfirmasi pendaftaran untuk kegiatan "${keg.nama_kegiatan}"?`,
        confirmButtonText: 'Ya, Daftar Sekarang',
        cancelButtonText: 'Batal',
        icon: 'question',
        confirmButtonColor: '#9F0E0F',
    });
    if (ok) {
        router.post(route('user.kegiatan.daftar', keg.id));
    }
};

const batal = async (keg) => {
    const ok = await confirmDialog({
        title: 'Batalkan Pendaftaran?',
        text: `Batalkan keikutsertaan Anda pada kegiatan "${keg.nama_kegiatan}"?`,
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tetap Ikut',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });
    if (ok) {
        router.delete(route('user.kegiatan.batal', keg.id));
    }
};
</script>

<template>
    <UserLayout title="Katalog Kegiatan Edukasi — SIPENA IDX">
        <Head title="Katalog Kegiatan Edukasi" />

        <div class="space-y-6">
            <!-- Header & Search -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog Kegiatan Maker</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kegiatan yang diajukan oleh maker atau mitra dan siap untuk dipelajari, diikuti, atau ditinjau admin.</p>
                </div>

                <div class="flex items-center gap-2">
                    <input v-model="search"
                           @keyup.enter="handleFilter"
                           type="text"
                           placeholder="Cari program kerja, materi..."
                           class="px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#680A10]/20 w-64" />
                    <button type="button" @click="handleFilter"
                            class="px-4 py-2 bg-[#680A10] text-white text-xs font-semibold rounded-xl hover:bg-[#54080D] transition">
                        Cari
                    </button>
                </div>
            </div>

            <!-- Filter Pills -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <button type="button" @click="filterJenis(null)"
                        :class="[
                            'px-3 py-1.5 rounded-full font-semibold transition',
                            !filters?.jenis ? 'bg-[#680A10] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
                        ]">
                    Semua Program
                </button>
                <button v-for="cat in categories" :key="cat"
                        type="button" @click="filterJenis(cat)"
                        :class="[
                            'px-3 py-1.5 rounded-full font-semibold transition',
                            filters?.jenis === cat ? 'bg-[#680A10] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
                        ]">
                    {{ cat }}
                </button>
            </div>

            <!-- Grid Cards Kegiatan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div v-for="keg in kegiatanList.data" :key="keg.id"
                     class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ keg.jenis_kegiatan }}
                            </span>
                            <span v-if="userKegiatanIds.includes(keg.id)"
                                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                &check; Terdaftar
                            </span>
                        </div>

                        <h2 class="text-base font-bold text-slate-900 leading-snug">
                            <Link :href="route('user.kegiatan.show', keg.id)" class="hover:text-[#680A10] transition">
                                {{ keg.nama_kegiatan }}
                            </Link>
                        </h2>

                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ keg.deskripsi }}
                        </p>

                        <div class="space-y-1.5 pt-2 border-t border-slate-100 text-xs text-slate-600">
                            <div>Tanggal: <strong>{{ keg.tanggal ? new Date(keg.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}</strong></div>
                            <div>Waktu: {{ keg.waktu_mulai ? keg.waktu_mulai.substring(0, 5) : '09:00' }} - {{ keg.waktu_selesai ? keg.waktu_selesai.substring(0, 5) : '15:30' }} WIB</div>
                            <div class="truncate" :title="keg.lokasi">Lokasi: {{ keg.lokasi }}</div>
                        </div>

                        <div class="pt-2">
                            <div class="flex justify-between text-[11px] text-slate-500 mb-1">
                                <span>Kuota Terisi: <strong>{{ keg.peserta?.length || 0 }}/{{ keg.kuota_peserta || 100 }}</strong></span>
                                <span>{{ Math.min(100, Math.round(((keg.peserta?.length || 0) / (keg.kuota_peserta || 100)) * 100)) }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#680A10] h-1.5 rounded-full"
                                     :style="{ width: `${Math.min(100, Math.round(((keg.peserta?.length || 0) / (keg.kuota_peserta || 100)) * 100))}%` }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <Link :href="route('user.kegiatan.show', keg.id)" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                            Lihat Rincian
                        </Link>

                        <button v-if="userKegiatanIds.includes(keg.id)"
                                type="button" @click="batal(keg)"
                                class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                            Batalkan
                        </button>
                        <button v-else-if="(keg.peserta?.length || 0) >= (keg.kuota_peserta || 100)"
                                disabled
                                class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed">
                            Kuota Penuh
                        </button>
                        <button v-else
                                type="button" @click="daftar(keg)"
                                class="px-4 py-1.5 rounded-lg bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold transition shadow-xs">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>

                <div v-if="!kegiatanList.data?.length" class="col-span-full py-12 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                    Tidak ada kegiatan edukasi yang tersedia saat ini.
                </div>
            </div>

            <!-- Pagination -->
            <Pagination
                :links="kegiatanList.links"
                :from="kegiatanList.from"
                :to="kegiatanList.to"
                :total="kegiatanList.total"
                item-label="kegiatan"
            />
        </div>
    </UserLayout>
</template>
