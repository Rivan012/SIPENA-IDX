<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { MailIcon } from '@/Icons';

const props = defineProps({
    email: String,
    status: String,
});

const form = useForm({});

const resendLink = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <Head title="Aktivasi Akun — SIPENA IDX Bengkulu" />

    <div class="relative min-h-screen bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans text-slate-800 antialiased selection:bg-[#85131B] selection:text-white">
        <ThemeToggle class="absolute right-4 top-4" />

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-8 border border-slate-300 rounded-xl shadow-xs">
                <!-- Brand Mark & Header -->
                <div class="text-center mb-6">
                    <Link :href="route('home')" class="inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-xl p-1 mb-3">
                        <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-16 w-auto mx-auto" />
                    </Link>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-100 text-[#85131B] text-xs font-bold mb-2">
                        <MailIcon class="h-3.5 w-3.5" />
                        <span>Verifikasi Email</span>
                    </div>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight">Aktivasi Akun Mitra</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Satu langkah lagi untuk mulai menggunakan portal SIPENA IDX.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Status Flash Alert -->
                    <div v-if="status" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 flex items-start gap-2.5">
                        <svg class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Email Target Card -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">
                        <p class="text-xs text-slate-600">
                            Tautan aktivasi telah dikirimkan ke alamat email:
                        </p>
                        <div class="mt-2 inline-block max-w-full truncate px-3 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-xs text-[#85131B] shadow-2xs">
                            {{ email || 'email akun Anda' }}
                        </div>
                    </div>

                    <!-- Petunjuk Aktivasi -->
                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-3.5 text-xs text-slate-600 space-y-2">
                        <div class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">
                            Langkah Aktivasi:
                        </div>
                        <ol class="list-decimal pl-4 space-y-1.5 text-[11px] leading-relaxed text-slate-600">
                            <li>Buka kotak masuk email Anda (periksa juga folder <strong>Spam</strong> / <strong>Promosi</strong>).</li>
                            <li>Buka pesan dari <strong>SIPENA IDX Bengkulu</strong>.</li>
                            <li>Klik tombol <strong>Aktivasi Akun Sekarang</strong> untuk mengaktifkan akun dan langsung masuk ke dashboard.</li>
                        </ol>
                    </div>

                    <!-- Kirim Ulang Action Button -->
                    <div class="pt-2">
                        <button
                            type="button"
                            @click="resendLink"
                            :disabled="form.processing"
                            class="w-full py-2.5 bg-[#85131B] hover:bg-[#680A10] text-white font-bold text-xs rounded-lg transition-colors shadow-xs disabled:opacity-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#85131B] inline-flex items-center justify-center gap-2"
                        >
                            <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <MailIcon v-else class="h-4 w-4" />
                            <span>{{ form.processing ? 'Mengirim ulang tautan...' : 'Kirim Ulang Email Aktivasi' }}</span>
                        </button>
                    </div>

                    <!-- Logout / Ganti Akun -->
                    <div class="pt-4 border-t border-slate-200 text-center text-xs">
                        <Link :href="route('logout')" method="post" as="button" class="text-slate-500 hover:text-slate-800 transition-colors font-medium cursor-pointer">
                            &larr; Keluar dan gunakan akun lain
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
