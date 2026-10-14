<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    notifikasi: Object,
});

const markRead = (n) => {
    router.post(route('user.notifikasi.read', n.id));
};
</script>

<template>
    <UserLayout title="Notifikasi — SIPENA IDX">
        <Head title="Notifikasi" />

        <div class="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Notifikasi</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pemberitahuan terkini seputar pendaftaran dan jadwal kegiatan Anda.</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs divide-y divide-slate-100 overflow-hidden">
                <div v-for="notif in notifikasi.data" :key="notif.id"
                     :class="['p-4 sm:p-5 flex items-start gap-4 transition', notif.is_read ? 'bg-white' : 'bg-rose-50/40']">
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center shrink-0', notif.is_read ? 'bg-slate-100 text-slate-500' : 'bg-[#680A10] text-white']">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h2 :class="['text-xs font-bold text-slate-900', !notif.is_read ? 'text-[#680A10]' : '']">
                                {{ notif.judul }}
                            </h2>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                {{ notif.created_at ? new Date(notif.created_at).toLocaleDateString('id-ID') : '-' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ notif.pesan }}
                        </p>

                        <div v-if="!notif.is_read" class="mt-2.5">
                            <button type="button" @click="markRead(notif)" class="text-[11px] font-semibold text-[#680A10] hover:underline">
                                Tandai Sudah Dibaca &check;
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="!notifikasi.data?.length" class="py-12 text-center text-slate-400 text-xs">
                    Belum ada notifikasi baru untuk Anda.
                </div>
            </div>

            <!-- Pagination -->
            <Pagination
                :links="notifikasi.links"
                :from="notifikasi.from"
                :to="notifikasi.to"
                :total="notifikasi.total"
                item-label="notifikasi"
            />
        </div>
    </UserLayout>
</template>
