<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: Number,
        default: 404,
    },
    message: {
        type: String,
        default: '',
    },
});

const title = computed(() => {
    return {
        404: 'Halaman Tidak Ditemukan',
        403: 'Akses Dibatasi',
        500: 'Terjadi Gangguan Server',
        503: 'Pemeliharaan Sistem',
        419: 'Sesi Halaman Kedaluwarsa',
        429: 'Terlalu Banyak Permintaan',
    }[props.status] || 'Terjadi Kesalahan';
});

const description = computed(() => {
    if (props.message) return props.message;

    return {
        404: 'Maaf, halaman atau tautan yang Anda tuju tidak ditemukan, telah dihapus, atau sedang tidak tersedia.',
        403: 'Anda tidak memiliki hak akses atau izin yang diperlukan untuk membuka halaman atau data ini.',
        500: 'Terjadi kesalahan sistem internal pada server. Tim teknis sedang menindaklanjuti kendala ini.',
        503: 'Sistem SIPENA sedang dalam proses pemeliharaan berkala. Kami akan segera kembali beroperasi.',
        419: 'Masa aktif sesi telah berakhir demi keamanan data Anda. Silakan muat ulang halaman ini.',
        429: 'Permintaan Anda melebihi batas yang diizinkan dalam waktu singkat. Harap tunggu sejenak.',
    }[props.status] || 'Terjadi kendala saat memproses permintaan Anda.';
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/';
    }
};

const reload = () => {
    window.location.reload();
};
</script>

<template>
    <div class="min-h-screen bg-[#0c0f17] text-slate-100 flex items-center justify-center p-6 relative overflow-hidden font-sans">
        <Head :title="`${status} — ${title}`" />

        <!-- Decorative background glow -->
        <div class="absolute -top-32 -left-32 w-[520px] h-[520px] rounded-full bg-radial from-[#85131B]/30 via-[#680A10]/10 to-transparent pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-[480px] h-[480px] rounded-full bg-radial from-slate-800/60 to-transparent pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-[520px] bg-[#121826]/90 backdrop-blur-xl border border-white/10 rounded-3xl p-8 sm:p-11 text-center shadow-2xl">
            <!-- Brand Tag -->
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 border border-white/10 mb-6">
                <img src="/images/logo-emblem.png" alt="Logo BEI" class="h-6 w-6 object-contain" />
                <span class="text-[11px] font-extrabold tracking-widest text-slate-200 uppercase">SIPENA</span>
                <span class="text-white/20">|</span>
                <span class="text-[11px] font-semibold text-rose-400 uppercase tracking-wider">KP BEI Bengkulu</span>
            </div>

            <!-- Code -->
            <div class="relative mb-3">
                <div class="text-[110px] font-black tracking-tighter text-white/[0.04] leading-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 select-none pointer-events-none">
                    {{ status }}
                </div>
                <div class="text-5xl font-extrabold tracking-tight bg-gradient-to-br from-white via-white to-rose-200 bg-clip-text text-transparent">
                    {{ status }}
                </div>
            </div>

            <!-- Title & Message -->
            <h1 class="text-xl sm:text-2xl font-bold text-white mb-2.5 leading-snug">
                {{ title }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-8 max-w-sm mx-auto">
                {{ description }}
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5">
                <button
                    v-if="status === 419"
                    type="button"
                    @click="reload"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#85131B] hover:bg-[#680A10] text-white text-xs font-bold transition shadow-lg shadow-red-950/40 cursor-pointer"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    Muat Ulang
                </button>

                <Link
                    href="/"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#85131B] hover:bg-[#680A10] text-white text-xs font-bold transition shadow-lg shadow-red-950/40 cursor-pointer"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Halaman Utama
                </Link>

                <button
                    type="button"
                    @click="goBack"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 text-xs font-bold transition cursor-pointer"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Kembali
                </button>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-5 border-t border-white/10 text-[11px] text-slate-500 leading-relaxed">
                SIPENA &bull; Sistem Informasi Perencanaan &amp; Edukasi Pasar Modal<br>
                Kantor Perwakilan PT Bursa Efek Indonesia Bengkulu
            </div>
        </div>
    </div>
</template>
