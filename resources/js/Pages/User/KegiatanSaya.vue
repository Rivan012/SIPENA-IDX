<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    kegiatanList: Object,
});
</script>

<template>
    <UserLayout title="Kegiatan Saya — SIPENA IDX">
        <Head title="Kegiatan Saya" />

        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kegiatan Saya</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh program edukasi dan sosialisasi pasar modal yang Anda ikuti.</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">PROGRAM KEGIATAN</th>
                                <th class="py-3 px-4">TANGGAL &amp; WAKTU</th>
                                <th class="py-3 px-4">LOKASI</th>
                                <th class="py-3 px-4 text-center">STATUS KEHADIRAN</th>
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
                                        item.pivot?.status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'
                                    ]">
                                        {{ item.pivot?.status || 'terdaftar' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <Link :href="route('user.kegiatan.show', item.id)" class="font-semibold text-xs text-[#680A10] hover:underline">
                                        Rincian &rarr;
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!kegiatanList.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Belum ada kegiatan yang Anda ikuti.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="kegiatanList.links?.length > 3" class="p-3.5 border-t border-slate-200 bg-slate-50/50 flex justify-end gap-1">
                    <template v-for="(link, i) in kegiatanList.links" :key="i">
                        <Link v-if="link.url"
                              :href="link.url"
                              v-html="link.label"
                              :class="[
                                  'px-3 py-1.5 rounded-xl text-xs font-semibold transition',
                                  link.active ? 'bg-[#680A10] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
                              ]" />
                        <span v-else v-html="link.label" class="px-3 py-1.5 text-slate-300 text-xs"></span>
                    </template>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
