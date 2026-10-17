<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichTextContent from '@/Components/RichTextContent.vue';

defineProps({
    kegiatan: Object,
});
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
                        <form v-if="kegiatan.status === 'dipublikasikan'"
                              method="get"
                              :action="route('admin.kegiatan.presensi.download', kegiatan.id)"
                              target="_blank"
                              class="flex flex-wrap items-center gap-2">
                            <label for="presensi-orientation" class="sr-only">Orientasi PDF presensi</label>
                            <select id="presensi-orientation"
                                    name="orientation"
                                    class="min-h-9 rounded-lg border border-slate-300 bg-white px-2.5 text-xs font-medium text-slate-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#680A10]">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                            <button type="submit"
                                    class="min-h-9 rounded-lg bg-[#680A10] px-3.5 text-xs font-semibold text-white transition hover:bg-[#54080D] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#680A10]">
                                Cetak Presensi
                            </button>
                        </form>
                        <Link :href="route('admin.kegiatan.edit', kegiatan.id)"
                              class="px-3.5 py-2 rounded-xl bg-[#680A10] hover:bg-[#54080D] text-white text-xs font-semibold transition shadow-xs">
                            Edit Kegiatan
                        </Link>
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

            <!-- Daftar Peserta Terdaftar -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Peserta Terdaftar ({{ kegiatan.peserta?.length || 0 }})</h2>
                </div>
                <div class="divide-y divide-slate-100 text-xs max-h-64 overflow-y-auto">
                    <div v-for="p in kegiatan.peserta" :key="p.id" class="py-2.5 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-slate-900">{{ p.user?.name || '-' }}</div>
                            <div class="text-[11px] text-slate-400">{{ p.user?.email || '-' }} &bull; {{ p.user?.no_telepon || '-' }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 capitalize">
                            {{ p.status }}
                        </span>
                    </div>
                    <div v-if="!kegiatan.peserta?.length" class="py-8 text-center text-slate-400">
                        Belum ada peserta yang mendaftar pada kegiatan ini.
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
