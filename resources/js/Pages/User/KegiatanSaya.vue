<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { confirmDialog } from '@/Utils/sweetalert';

defineProps({
    kegiatanList: Object,
});

const cancelProposal = async (item) => {
    const confirmed = await confirmDialog({
        title: 'Batalkan Proposal?',
        text: `Proposal "${item.nama_kegiatan}" akan dibatalkan.`,
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Kembali',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });

    if (confirmed) {
        router.patch(route('user.kegiatan.cancel', item.id));
    }
};

const deleteProposal = async (item) => {
    const confirmed = await confirmDialog({
        title: 'Hapus Proposal?',
        text: `Proposal "${item.nama_kegiatan}" akan dihapus permanen.`,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Kembali',
        icon: 'warning',
        confirmButtonColor: '#DC2626',
    });

    if (confirmed) {
        router.delete(route('user.kegiatan.destroy', item.id));
    }
};
</script>

<template>
    <UserLayout title="Proposal Kegiatan Saya — SIPENA IDX">
        <Head title="Proposal Kegiatan Saya" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Proposal Kegiatan Saya</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar kegiatan yang Anda ajukan, lengkap dengan status approval.</p>
                </div>
                <Link :href="route('user.kegiatan.create')"
                      class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#680A10] text-white text-xs font-semibold hover:bg-[#54080D] transition">
                    Ajukan Kegiatan
                </Link>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">PROGRAM KEGIATAN</th>
                                <th class="py-3 px-4">TANGGAL &amp; WAKTU</th>
                                <th class="py-3 px-4">LOKASI</th>
                                <th class="py-3 px-4 text-center">STATUS APPROVAL</th>
                                <th class="py-3 px-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in kegiatanList.data" :key="item.id" class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="font-bold text-slate-900 leading-snug">
                                        <Link :href="route('user.kegiatan.show', item.id)" class="hover:text-[#680A10] transition">
                                            {{ item.nama_kegiatan }}
                                        </Link>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 mt-1 inline-block">
                                        {{ item.jenis_kegiatan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">
                                        {{ item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ item.waktu_mulai ? item.waktu_mulai.substring(0, 5) : '09:00' }} - {{ item.waktu_selesai ? item.waktu_selesai.substring(0, 5) : '15:30' }} WIB
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-[200px] truncate" :title="item.lokasi">
                                    {{ item.lokasi }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span :class="[
                                        'inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold capitalize',
                                        item.status === 'dipublikasikan' ? 'bg-emerald-100 text-emerald-800' :
                                        item.status === 'ditolak' ? 'bg-red-100 text-red-800' :
                                        item.status === 'dibatalkan' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-800'
                                    ]">
                                        {{ item.status_label || item.status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link :href="route('user.kegiatan.show', item.id)" class="font-semibold text-xs text-slate-600 hover:text-slate-900">
                                            Rincian
                                        </Link>
                                        <Link :href="route('user.kegiatan.edit', item.id)" class="font-semibold text-xs text-[#680A10] hover:underline">
                                            Edit
                                        </Link>
                                        <button v-if="!['dibatalkan', 'selesai'].includes(item.status)"
                                                type="button" @click="cancelProposal(item)"
                                                class="font-semibold text-xs text-amber-700 hover:underline">
                                            Batalkan
                                        </button>
                                        <button type="button" @click="deleteProposal(item)"
                                                class="font-semibold text-xs text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!kegiatanList.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Belum ada proposal kegiatan yang Anda ajukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
        </div>
    </UserLayout>
</template>
