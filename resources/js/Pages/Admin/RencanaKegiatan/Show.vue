<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    rencana: Object,
    detail: Object,
    pengajuan: Object,
});

const ajukan = async () => {
    const ok = await confirmDialog({
        title: 'Ajukan Rencana Kegiatan?',
        text: 'Ajukan rencana kegiatan ini ke bagian persetujuan anggaran?',
        confirmButtonText: 'Ya, Ajukan',
        cancelButtonText: 'Batal',
        icon: 'question',
        confirmButtonColor: '#9F0E0F',
    });
    if (ok) {
        router.post(route('admin.rencana-kegiatan.ajukan', props.rencana.id));
    }
};
</script>

<template>
    <AdminLayout :title="`Detail Rencana: ${rencana.nama_kegiatan} — SIPENA IDX`">
        <Head :title="rencana.nama_kegiatan" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('admin.rencana-kegiatan.index')" class="hover:text-slate-900">Anggaran &amp; RKAP</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold truncate">{{ rencana.nama_kegiatan }}</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                            {{ rencana.kode }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ rencana.jenis_kegiatan }}
                        </span>
                        <span :class="['px-2.5 py-1 rounded-full text-xs font-bold', rencana.status_class]">
                            {{ rencana.status_label }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button v-if="rencana.status === 'draft'"
                                type="button"
                                @click="ajukan"
                                class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition shadow-xs">
                            Ajukan Review Anggaran &uarr;
                        </button>
                        <Link :href="route('admin.rencana-kegiatan.edit', rencana.id)"
                              class="px-3.5 py-2 rounded-xl bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold transition shadow-xs">
                            Edit Rencana
                        </Link>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-slate-900">{{ rencana.nama_kegiatan }}</h1>
                <p class="text-xs text-slate-600 leading-relaxed">{{ detail?.deskripsi || '-' }}</p>

                <!-- Grid Pos Anggaran -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                    <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">ALOKASI BIAYA</span>
                        <span class="font-bold text-emerald-900 text-base mt-1 block">{{ rencana.formatted_anggaran }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TANGGAL RENCANA</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ rencana.tanggal_rencana ? new Date(rencana.tanggal_rencana).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '-' }}
                        </span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI KEGIATAN</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate" :title="detail?.lokasi">{{ detail?.lokasi || '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TARGET AUDIENS</span>
                        <span class="font-bold text-slate-800 mt-1 block">{{ detail?.target_peserta || 100 }} Peserta</span>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pengajuan Proposal -->
            <div v-if="pengajuan" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Berkas Pengajuan Proposal Terkait</h2>
                    <Link :href="route('admin.pengajuan.show', pengajuan.id)" class="text-xs font-semibold text-[#680A10] hover:underline">
                        Lihat Berkas Approval &rarr;
                    </Link>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-mono font-bold text-slate-800">{{ pengajuan.nomor_pengajuan }}</span>
                        <span class="text-slate-500 block mt-0.5">Tanggal Pengajuan: {{ pengajuan.tanggal_pengajuan }}</span>
                    </div>
                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', pengajuan.status_class]">
                        {{ pengajuan.status_label }}
                    </span>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
