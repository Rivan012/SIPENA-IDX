<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    kegiatan: Object,
    isRegistered: Boolean,
    isOwner: Boolean,
});

const edit = () => {
    router.get(route('user.kegiatan.edit', props.kegiatan.id));
};

const batal = async () => {
    const ok = await confirmDialog({
        title: 'Batalkan Proposal?',
        text: 'Apakah Anda yakin ingin membatalkan proposal kegiatan ini?',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tetap Simpan',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });
    if (ok) {
        router.patch(route('user.kegiatan.cancel', props.kegiatan.id));
    }
};

const hapus = async () => {
    const ok = await confirmDialog({
        title: 'Hapus Proposal?',
        text: 'Proposal ini akan dihapus permanen. Lanjutkan?',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });
    if (ok) {
        router.delete(route('user.kegiatan.destroy', props.kegiatan.id));
    }
};
</script>

<template>
    <UserLayout :title="`${kegiatan.nama_kegiatan} — SIPENA IDX`">
        <Head :title="kegiatan.nama_kegiatan" />

        <div class="space-y-6 max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('user.dashboard')" class="hover:text-slate-900">Dashboard</Link>
                <span>&rsaquo;</span>
                <Link :href="route('user.kegiatan.index')" class="hover:text-slate-900">Proposal Saya</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold truncate">{{ kegiatan.nama_kegiatan }}</span>
            </div>

            <!-- Main Header Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ kegiatan.jenis_kegiatan }}
                        </span>
                        <span :class="['px-2.5 py-1 rounded-full text-xs font-bold', kegiatan.status_class]">
                            {{ kegiatan.status_label }}
                        </span>
                    </div>

                    <div v-if="isOwner" class="flex items-center gap-2">
                        <button type="button" @click="edit"
                                class="px-3 py-1.5 rounded-xl bg-[#680A10] text-white text-xs font-semibold transition">
                            Edit Proposal
                        </button>
                        <button type="button" @click="batal"
                                class="px-3 py-1.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                            Batalkan
                        </button>
                        <button type="button" @click="hapus"
                                class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                            Hapus
                        </button>
                    </div>
                    <div v-else class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold">
                        Proposal dibuat oleh maker lain
                    </div>
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
                    {{ kegiatan.nama_kegiatan }}
                </h1>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ kegiatan.deskripsi }}
                </p>

                <!-- Grid Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TANGGAL ACARA</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ kegiatan.tanggal ? new Date(kegiatan.tanggal).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '-' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">WAKTU</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ kegiatan.waktu_mulai ? kegiatan.waktu_mulai.substring(0, 5) : '09:00' }} - {{ kegiatan.waktu_selesai ? kegiatan.waktu_selesai.substring(0, 5) : '15:30' }} WIB
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate" :title="kegiatan.lokasi">{{ kegiatan.lokasi }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KUOTA PESERTA</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ kegiatan.peserta?.length || 0 }} / {{ kegiatan.kuota_peserta || 100 }} Orang
                        </span>
                    </div>
                </div>
            </div>

            <!-- Susunan Agenda -->
            <div v-if="kegiatan.agenda?.length" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900">Susunan Jadwal &amp; Agenda Acara</h2>
                <div class="divide-y divide-slate-100">
                    <div v-for="ag in kegiatan.agenda" :key="ag.id" class="py-3 flex items-start gap-4 text-xs">
                        <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded shrink-0">
                            {{ ag.waktu_mulai ? ag.waktu_mulai.substring(0, 5) : '' }} - {{ ag.waktu_selesai ? ag.waktu_selesai.substring(0, 5) : '' }}
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ ag.judul }}</h3>
                            <p class="text-slate-500 mt-0.5">{{ ag.keterangan }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
