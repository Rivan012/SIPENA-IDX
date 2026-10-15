<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { confirmDialog } from '@/Utils/sweetalert';

const props = defineProps({
    pengajuan: Object,
    rencana: Object,
    detail: Object,
    kegiatan: Object,
});

const form = useForm({
    status: 'disetujui',
    catatan: '',
    catatan_revisi: '',
    terbitkan_kegiatan: true,
});

const updateDecision = async (status) => {
    let title = 'Konfirmasi Keputusan';
    let text = 'Lanjutkan pembaruan status proposal ini?';
    let icon = 'question';
    let confirmBtn = 'Ya, Simpan';
    let btnColor = '#9F0E0F';

    if (status === 'disetujui') {
        title = 'Setujui Proposal Kegiatan?';
        text = 'Proposal akan disetujui dan otomatis diterbitkan ke jadwal kegiatan edukasi.';
        confirmBtn = 'Ya, Setujui Proposal';
        btnColor = '#059669';
        icon = 'question';
    } else if (status === 'ditolak') {
        title = 'Tolak Proposal Kegiatan?';
        text = 'Proposal kegiatan ini akan ditandai ditolak.';
        confirmBtn = 'Ya, Tolak Proposal';
        btnColor = '#DC2626';
        icon = 'warning';
    } else if (status === 'revisi') {
        title = 'Minta Revisi Proposal?';
        text = 'Catatan revisi akan diteruskan kepada tim penyusun proposal.';
        confirmBtn = 'Kirim Permintaan Revisi';
        btnColor = '#D97706';
        icon = 'info';
    } else if (status === 'diproses') {
        title = 'Tandai Sedang Ditinjau?';
        text = 'Status proposal akan diubah menjadi dalam proses review.';
        confirmBtn = 'Tandai Ditinjau';
        btnColor = '#2563EB';
        icon = 'info';
    }

    const ok = await confirmDialog({
        title,
        text,
        confirmButtonText: confirmBtn,
        cancelButtonText: 'Batal',
        icon,
        confirmButtonColor: btnColor,
    });

    if (ok) {
        form.status = status;
        form.patch(route('admin.pengajuan.status', props.pengajuan.id));
    }
};
</script>

<template>
    <AdminLayout :title="`Verifikasi: ${pengajuan.nomor_pengajuan} — SIPENA IDX`">
        <Head title="Detail Verifikasi Approval" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('admin.approval.index')" class="hover:text-slate-900">Approval</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold truncate">{{ pengajuan.nomor_pengajuan }}</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded">
                            {{ pengajuan.nomor_pengajuan }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ pengajuan.jenis_kegiatan }}
                        </span>
                        <span :class="['px-2.5 py-1 rounded-full text-xs font-bold', pengajuan.status_class]">
                            {{ pengajuan.status_label }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-400">
                        Diajukan: {{ pengajuan.tanggal_pengajuan }}
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-slate-900">{{ pengajuan.nama_kegiatan }}</h1>
                <p class="text-xs text-slate-600 leading-relaxed">{{ detail?.deskripsi || '-' }}</p>

                <!-- Grid Pos Anggaran -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">USULAN BIAYA</span>
                        <span class="font-bold text-emerald-900 text-base mt-1 block">{{ pengajuan.formatted_anggaran }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TANGGAL RENCANA</span>
                        <span class="font-bold text-slate-800 mt-1 block">
                            {{ rencana?.tanggal_rencana ? new Date(rencana.tanggal_rencana).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                        </span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI</span>
                        <span class="font-bold text-slate-800 mt-1 block truncate" :title="detail?.lokasi">{{ detail?.lokasi || '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TARGET PESERTA</span>
                        <span class="font-bold text-slate-800 mt-1 block">{{ detail?.target_peserta || 100 }} Peserta</span>
                    </div>
                </div>
            </div>

            <!-- Form Keputusan Verifikasi Approval -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900">Keputusan Verifikasi Approval</h2>
                <p class="text-xs text-slate-500">Berikan rekomendasi persetujuan, permintaan koreksi, atau penolakan proposal.</p>

                <div class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikator / Catatan Revisi</label>
                        <textarea v-model="form.catatan" rows="3" placeholder="Tuliskan catatan verifikasi atau pertimbangan anggaran..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="button" @click="updateDecision('disetujui')" :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition disabled:opacity-50">
                            &check; Setujui Proposal
                        </button>
                        <button type="button" @click="updateDecision('diproses')" :disabled="form.processing"
                                class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition disabled:opacity-50">
                            Tandai Sedang Ditinjau
                        </button>
                        <button type="button" @click="updateDecision('revisi')" :disabled="form.processing"
                                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition disabled:opacity-50">
                            Minta Revisi
                        </button>
                        <button type="button" @click="updateDecision('ditolak')" :disabled="form.processing"
                                class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-xs transition disabled:opacity-50">
                            Tolak Proposal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
