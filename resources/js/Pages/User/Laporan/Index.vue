<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SortableTh from '@/Components/SortableTh.vue';

const props = defineProps({
    laporanList: Object,
    filters: Object,
});

const currentSort = ref(props.filters?.sort || '');
const currentDirection = ref(props.filters?.direction || 'asc');

const handleSort = (field) => {
    if (currentSort.value === field) {
        currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.value = field;
        currentDirection.value = 'asc';
    }
    router.get(route('user.laporan.index'), {
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <UserLayout title="Laporan Edukasi — SIPENA IDX">
        <Head title="Laporan Pelaksanaan Kegiatan" />

        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Pelaksanaan Kegiatan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daftar publikasi dan rekapitulasi pelaksanaan edukasi pasar modal BEI Bengkulu.</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <SortableTh field="judul_laporan" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    JUDUL LAPORAN
                                </SortableTh>
                                <th class="py-3 px-4">PROGRAM KEGIATAN</th>
                                <SortableTh field="tanggal_laporan" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    TANGGAL PELAKSANAAN
                                </SortableTh>
                                <SortableTh field="total_peserta_hadir" align="center" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    PESERTA HADIR
                                </SortableTh>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="lap in laporanList.data" :key="lap.id" class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ lap.judul_laporan }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ lap.kegiatan?.nama_kegiatan || '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ lap.tanggal_laporan ? new Date(lap.tanggal_laporan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ lap.total_peserta_hadir || 0 }} Orang
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <Link :href="route('user.laporan.show', lap.id)" class="font-semibold text-xs text-[#680A10] hover:underline">
                                        Lihat &rarr;
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!laporanList.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Belum ada laporan kegiatan yang dipublikasikan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :links="laporanList.links"
                    :from="laporanList.from"
                    :to="laporanList.to"
                    :total="laporanList.total"
                    item-label="laporan"
                />
            </div>
        </div>
    </UserLayout>
</template>
