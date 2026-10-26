<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichTextContent from '@/Components/RichTextContent.vue';
import PresensiModal from '@/Components/PresensiModal.vue';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    kegiatan: Object,
});

const showPresensiModal = ref(false);

const isHariH = (tanggal) => {
    if (!tanggal) return false;
    const d = new Date();
    const today = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    return String(tanggal).substring(0, 10) === today;
};

const changeStatus = async (newStatus) => {
    if (newStatus === 'selesai' && (props.kegiatan.status !== 'berlangsung' || !isHariH(props.kegiatan.tanggal))) {
        return;
    }
    const labels = {
        dipublikasikan: 'Terjadwal',
        berlangsung: 'Berlangsung',
        selesai: 'Selesai',
    };
    const ok = await confirmDialog({
        title: `Ubah Status ke ${labels[newStatus]}?`,
        text: `Ubah status kegiatan "${props.kegiatan.nama_kegiatan}" menjadi ${labels[newStatus]}?`,
        confirmButtonText: `Ya, ${labels[newStatus]}`,
        cancelButtonText: 'Batal',
        icon: 'question',
        confirmButtonColor: newStatus === 'selesai' ? '#059669' : (newStatus === 'berlangsung' ? '#DC2626' : '#0284C7'),
    });
    if (ok) {
        router.patch(route('admin.kegiatan.status', props.kegiatan.id), { status: newStatus });
    }
};
</script>

<template>
    <AdminLayout :title="`${kegiatan.nama_kegiatan} — SIPENA IDX`">
        <Head :title="kegiatan.nama_kegiatan" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('admin.kegiatan.index')" class="hover:text-slate-900">Kegiatan</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold truncate">{{ kegiatan.nama_kegiatan }}</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                            {{ kegiatan.nama_kegiatan }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ kegiatan.jenis_kegiatan }}
                        </span>
                        <span :class="['px-2.5 py-1 rounded-full text-xs font-bold', kegiatan.status_class]">
                            {{ kegiatan.status_label }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Tombol Cepat Status: dipublikasikan, berlangsung, selesai -->
                        <button
                            v-if="kegiatan.status === 'disetujui'"
                            type="button"
                            @click="changeStatus('dipublikasikan')"
                            class="min-h-9 px-3 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs transition cursor-pointer shadow-2xs"
                        >
                            Terjadwal
                        </button>
                        <button
                            v-if="kegiatan.status === 'dipublikasikan'"
                            type="button"
                            @click="changeStatus('berlangsung')"
                            class="min-h-9 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition cursor-pointer shadow-2xs"
                        >
                            Berlangsung
                        </button>
                        <button
                            v-if="kegiatan.status === 'berlangsung' && isHariH(kegiatan.tanggal)"
                            type="button"
                            @click="changeStatus('selesai')"
                            class="min-h-9 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition cursor-pointer shadow-2xs"
                        >
                            Selesai
                        </button>

                        <Link
                            v-if="!['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(kegiatan.status)"
                            :href="route('admin.kegiatan.edit', kegiatan.id)"
                            class="min-h-9 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition inline-flex items-center shadow-2xs"
                        >
                            Edit Kegiatan
                        </Link>

                        <button
                            v-if="['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(kegiatan.status)"
                            type="button"
                            @click="showPresensiModal = true"
                            class="min-h-9 rounded-lg bg-[#680A10] px-3.5 text-xs font-semibold text-white transition hover:bg-[#54080D] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#680A10] cursor-pointer"
                        >
                            Cetak Presensi
                        </button>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-slate-900">{{ kegiatan.nama_kegiatan }}</h1>
                <RichTextContent :html="kegiatan.deskripsi_html" />

                <div v-if="kegiatan.proposal_file_path" class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                    <span class="text-xs font-semibold text-slate-700">Dokumen Proposal</span>
                    <a :href="route('admin.kegiatan.proposal.download', kegiatan.id)"
                       class="text-xs font-semibold text-[#680A10] hover:underline">
                        Unduh PDF
                    </a>
                </div>

                <!-- Grid Info Detail -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 pt-4 border-t border-slate-100 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TANGGAL</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ kegiatan.tanggal ? new Date(kegiatan.tanggal).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '-' }}
                        </span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">WAKTU</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ kegiatan.waktu_mulai ? kegiatan.waktu_mulai.substring(0, 5) : '' }} - {{ kegiatan.waktu_selesai ? kegiatan.waktu_selesai.substring(0, 5) : '15:30' }} WIB
                        </span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate" :title="kegiatan.lokasi">{{ kegiatan.lokasi }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MITRA</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate" :title="kegiatan.mitra || '-'">{{ kegiatan.mitra || '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KUOTA PESERTA</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ kegiatan.kuota_peserta || 100 }} Orang
                        </span>
                    </div>
                </div>
            </div>

            <!-- Agenda & Susunan Acara -->
            <div v-if="kegiatan.agenda?.length" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900">Susunan Jadwal &amp; Agenda Acara</h2>
                <div class="divide-y divide-slate-100 text-xs">
                    <div v-for="ag in kegiatan.agenda" :key="ag.id" class="py-3 flex items-start gap-3">
                        <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                            {{ ag.waktu_mulai ? ag.waktu_mulai.substring(0, 5) : '' }} - {{ ag.waktu_selesai ? ag.waktu_selesai.substring(0, 5) : '' }}
                        </span>
                        <div>
                            <div class="font-bold text-slate-900">{{ ag.judul }}</div>
                            <div class="text-slate-500 mt-0.5">{{ ag.keterangan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Presensi Modal -->
            <PresensiModal
                :show="showPresensiModal"
                :kegiatan="kegiatan"
                @close="showPresensiModal = false"
            />
        </div>
    </AdminLayout>
</template>
