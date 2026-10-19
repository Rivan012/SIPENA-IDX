<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    laporan: Object,
});
</script>

<template>
    <UserLayout :title="`${laporan.judul_laporan} — SIPENA IDX`">
        <Head :title="laporan.judul_laporan" />

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <Link :href="route('user.dashboard')" class="hover:text-slate-900">Dashboard</Link>
                <span>&rsaquo;</span>
                <Link :href="route('user.laporan.index')" class="hover:text-slate-900">Laporan</Link>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold truncate">{{ laporan.judul_laporan }}</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
                <h1 class="text-2xl font-bold text-slate-900">{{ laporan.judul_laporan }}</h1>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 pb-4 border-b border-slate-100">
                    <span>Program: <strong>{{ laporan.kegiatan?.nama_kegiatan || '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>Tanggal: {{ laporan.tanggal_laporan ? new Date(laporan.tanggal_laporan).toLocaleDateString('id-ID') : '-' }}</span>
                    <span>&bull;</span>
                    <span>Peserta Hadir: <strong>{{ laporan.total_peserta_hadir || 0 }} Orang</strong></span>
                </div>

                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed whitespace-pre-line pt-2">
                    {{ laporan.isi_laporan }}
                </div>

                <div v-if="laporan.file_laporan" class="pt-4 border-t border-slate-100">
                    <a :href="`/storage/${laporan.file_laporan}`" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Unduh Berkas Lampiran LPJ
                    </a>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
