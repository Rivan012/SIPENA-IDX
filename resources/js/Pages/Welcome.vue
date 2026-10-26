<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import RichTextContent from '@/Components/RichTextContent.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import LanguageToggle from '@/Components/LanguageToggle.vue';

// ponytail: toggle bilingual ID/EN lokal landing page. upgrade ke vue-i18n jika seluruh portal butuh i18n global.
const lang = ref(typeof window !== 'undefined' ? localStorage.getItem('sipena-lang') || 'id' : 'id');
const t = (idText, enText) => (lang.value === 'en' ? enText : idText);

const formatStatus = (statusKode, fallback) => {
    if (lang.value !== 'en') return fallback;
    if (statusKode === 'full') return 'Quota Full';
    if (statusKode === 'limited') return 'Limited Seats Left';
    return 'Registration Open';
};

// Filter & Search State
const searchQuery = ref('');
const selectedCategory = ref('Semua');
const activeFaq = ref(null);
const selectedAgenda = ref(null);
const mobileMenuOpen = ref(false);

const props = defineProps({
    agendaItems: { type: Array, default: () => [] },
    categories: { type: Array, default: () => ['Semua'] },
});

const categories = computed(() => {
    return props.categories.map(cat => (cat === 'Semua' ? t('Semua', 'All') : cat));
});

// Jejaring Galeri Investasi Resmi di Bengkulu
const galeriInvestasiList = computed(() => [
    {
        nama: 'GI BEI Fakultas Ekonomi & Bisnis Universitas Bengkulu',
        mitra: 'Indo Premier Sekuritas',
        lokasi: 'Kandang Limun, Muara Bangka Hulu, Kota Bengkulu',
        layanan: t('SPM Reguler, Lab Simulasi Trading, Sertifikasi WPPE', 'Regular SPM, Trading Simulation Lab, WPPE Certification'),
    },
    {
        nama: 'GI BEI FEBI UIN Fatmawati Sukarno (UINFAS) Bengkulu',
        mitra: 'Phintraco Sekuritas',
        lokasi: 'Pagar Dewa, Selebar, Kota Bengkulu',
        layanan: t('Pusat Edukasi Pasar Modal Syariah & Klinik RDN', 'Sharia Capital Market Education Center & RDN Clinic'),
    },
    {
        nama: 'GI BEI Universitas Dehasen (UNIVED) Bengkulu',
        mitra: 'MNC Sekuritas',
        lokasi: 'Jl. Meranti Raya No. 32, Sawah Lebar, Kota Bengkulu',
        layanan: t('Konsultasi Saham Pemula & Kuliah Tamu Emiten', 'Beginner Stock Consultation & Guest Issuer Lectures'),
    },
    {
        nama: 'GI BEI Universitas Muhammadiyah Bengkulu (UMB)',
        mitra: 'RHB Sekuritas Indonesia',
        lokasi: 'Kampus IV UMB, Jl. Adam Malik, Kota Bengkulu',
        layanan: t('Edukasi KSPM & Riset Pasar Modal Mahasiswa', 'KSPM Education & Student Capital Market Research'),
    },
]);

const filteredAgenda = computed(() => {
    return props.agendaItems.filter(item => {
        const isAll = selectedCategory.value === 'Semua' || selectedCategory.value === 'All';
        const matchCategory = isAll || item.kategori === selectedCategory.value;
        const query = searchQuery.value.trim().toLowerCase();
        const matchQuery = !query ||
            item.judul.toLowerCase().includes(query) ||
            item.kategori.toLowerCase().includes(query) ||
            item.lokasi.toLowerCase().includes(query) ||
            item.mitra.toLowerCase().includes(query);

        return matchCategory && matchQuery;
    });
});

const formatDate = (date, options) => {
    if (!date) return '-';

    return new Intl.DateTimeFormat(lang.value === 'en' ? 'en-US' : 'id-ID', options).format(new Date(`${date}T00:00:00`));
};

const formatWeekday = (date) => formatDate(date, { weekday: 'long' });

const resetFilter = () => {
    searchQuery.value = '';
    selectedCategory.value = t('Semua', 'All');
};

const syllabusItems = computed(() => [
    {
        program: t('Dasar Pasar Modal', 'Capital Market Basics'),
        level: t('Umum & Pemula', 'General & Beginners'),
        scope: t(
            'Pengenalan OJK, BEI, KPEI, KSEI; konsep dasar investasi versus spekulasi; instrumen saham, reksa dana, dan obligasi; pencegahan penipuan berkedok investasi ilegal.',
            'Introduction to OJK, IDX, KPEI, KSEI; fundamental concepts of investment vs speculation; stocks, mutual funds, and bonds; prevention of illegal investment scams.'
        ),
        outcome: t('Pemahaman literasi keuangan awal', 'Basic financial literacy understanding'),
    },
    {
        program: t('Sekolah Pasar Modal Level 1', 'Capital Market School Level 1'),
        level: t('Calon Investor Baru', 'Prospective New Investors'),
        scope: t(
            'Mekanisme transaksi bursa efek, siklus settlement T+2, struktur biaya transaksi, hak pemegang saham (RUPS & dividen), serta pembukaan Rekening Dana Nasabah (RDN).',
            'Stock exchange transaction mechanisms, T+2 settlement cycle, transaction fee structures, shareholder rights (AGM & dividends), and Customer Fund Account (RDN) opening.'
        ),
        outcome: t('Sertifikat SPM 1 & Akun Efek Aktif', 'SPM 1 Certificate & Active Securities Account'),
    },
    {
        program: t('Sekolah Pasar Modal Level 2', 'Capital Market School Level 2'),
        level: t('Investor Retail Aktif', 'Active Retail Investors'),
        scope: t(
            'Analisis fundamental laporan keuangan, evaluasi profitabilitas dan solvabilitas emiten, pengenalan analisis teknikal candlestick, serta penyusunan portofolio jangka panjang.',
            'Fundamental analysis of financial statements, evaluation of issuer profitability and solvency, introduction to candlestick technical analysis, and long-term portfolio construction.'
        ),
        outcome: t('Sertifikat Kelulusan SPM Level 2', 'SPM Level 2 Certificate of Completion'),
    },
    {
        program: t('Pasar Modal Syariah', 'Islamic Capital Market'),
        level: t('Peminat Investasi Syariah', 'Sharia Investment Enthusiasts'),
        scope: t(
            'Fatwa DSN-MUI tentang pasar modal, metode penyaringan saham syariah (DES), indeks saham syariah (ISSI, JII), dan mekanisme transaksi tanpa margin/short-selling di SOTS.',
            'DSN-MUI fatwas on capital markets, sharia stock screening methods (DES), Islamic stock indexes (ISSI, JII), and non-margin/short-selling transaction mechanisms on SOTS.'
        ),
        outcome: t('Sertifikat Edukasi Syariah BEI', 'IDX Sharia Education Certificate'),
    },
]);

const faqs = computed(() => [
    {
        q: t(
            'Apakah Sekolah Pasar Modal (SPM) di KP BEI Bengkulu dipungut biaya pelatihan?',
            'Is Capital Market School (SPM) at IDX Bengkulu charged any training fee?'
        ),
        a: t(
            'Tidak. Seluruh materi pembinaan dan fasilitas pelatihan diselenggarakan tanpa biaya oleh BEI. Pada sesi SPM Level 1, peserta hanya menyetorkan saldo awal pembukaan rekening efek (biasanya Rp100.000) yang sepenuhnya menjadi saldo investasi milik peserta sendiri.',
            'No. All coaching materials and training facilities are provided free of charge by IDX. In SPM Level 1, participants only deposit an initial balance to open a securities account (usually IDR 100,000) which remains 100% their own investment balance.'
        ),
    },
    {
        q: t(
            'Dokumen apa saja yang diperlukan untuk pembukaan Rekening Dana Nasabah (RDN)?',
            'What documents are required to open a Customer Fund Account (RDN)?'
        ),
        a: t(
            'Peserta cukup menyiapkan e-KTP fisik, buku tabungan bank atas nama pribadi (atau m-banking untuk verifikasi nomor rekening), serta NPWP (apabila sudah memiliki). Pendampingan pengisian formulir dilakukan langsung bersama perwakilan sekuritas mitra di lokasi.',
            'Participants only need an e-KTP ID, bank passbook under their own name (or mobile banking for account verification), and tax ID/NPWP (if held). On-site assistance is provided by partner brokerage representatives.'
        ),
    },
    {
        q: t(
            'Apakah peserta akan memperoleh sertifikat kepesertaan setelah mengikuti SPM?',
            'Will participants receive an attendance certificate after attending SPM?'
        ),
        a: t(
            'Ya. Peserta yang hadir penuh pada sesi SPM Level 1 dan Level 2 akan memperoleh sertifikat elektronik resmi dari Bursa Efek Indonesia melalui akun portal peserta SIPENA.',
            'Yes. Participants who attend SPM Level 1 and Level 2 sessions in full will receive an official electronic certificate from the Indonesia Stock Exchange via the SIPENA portal.'
        ),
    },
    {
        q: t(
            'Bagaimana prosedur bagi universitas atau instansi yang ingin mengundang narasumber BEI?',
            'What is the procedure for institutions wishing to invite IDX speakers?'
        ),
        a: t(
            'Pengelola instansi atau perguruan tinggi dapat mengajukan surat permohonan resmi kepada Kepala Kantor Perwakilan BEI Bengkulu atau berkoordinasi langsung melalui Galeri Investasi BEI terdekat minimal 14 hari sebelum rencana kegiatan.',
            'Institutions or universities can submit an official request letter to the Head of IDX Bengkulu Representative Office or coordinate directly through the nearest IDX Investment Gallery at least 14 days before the event.'
        ),
    },
]);

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

const openModal = (item) => {
    selectedAgenda.value = item;
};

const closeModal = () => {
    selectedAgenda.value = null;
};
</script>

<template>
    <Head :title="t('SIPENA: Portal Edukasi & Kegiatan Pasar Modal IDX Bengkulu', 'SIPENA: IDX Bengkulu Capital Market Education Portal')" />

    <div class="min-h-screen bg-white text-slate-800 font-sans antialiased selection:bg-[#85131B] selection:text-white">
        <!-- TOP INSTITUTIONAL IDENTITY BAR -->
        <div class="bg-slate-900 text-slate-300 text-xs border-b border-slate-800 hidden sm:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <span class="font-medium text-slate-300">
                        {{ t('PT Bursa Efek Indonesia (BEI) Kantor Perwakilan Bengkulu', 'PT Indonesia Stock Exchange (IDX) Bengkulu Representative Office') }}
                    </span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-400">
                        {{ t('Senin - Jumat 08.00 - 17.00 WIB', 'Monday - Friday 08:00 - 17:00 WIB') }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-slate-400">
                    <a href="https://www.idx.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">
                        idx.co.id
                    </a>
                    <span class="text-slate-700">|</span>
                    <Link :href="route('login')" class="hover:text-white transition-colors">
                        {{ t('Masuk ke Sistem', 'Log in to System') }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- MAIN HEADER / NAVBAR -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
                <!-- Brand Lockup -->
                <a href="#beranda" class="flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#85131B] rounded-lg">
                    <img src="/images/logo.svg" alt="SIPENA — Bursa Efek Indonesia" class="h-10 w-auto" />
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-7 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <a href="#jadwal" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">{{ t('Jadwal Kegiatan', 'Event Schedule') }}</a>
                    <a href="#kurikulum" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">{{ t('Kurikulum Edukasi', 'Curriculum') }}</a>
                    <a href="#galeri-investasi" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">{{ t('Jejaring Galeri', 'Gallery Network') }}</a>
                    <a href="#faq" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">{{ t('Tanya Jawab', 'FAQ') }}</a>
                    <a href="#kontak" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">{{ t('Kontak Kantor', 'Office Contact') }}</a>
                </nav>

                <div class="flex items-center gap-2">
                    <LanguageToggle v-model="lang" />
                    <ThemeToggle />
                </div>

                <!-- Auth Navigation -->
                <div class="hidden sm:flex items-center gap-2.5">
                    <template v-if="$page.props.auth?.admin">
                        <Link :href="route('admin.dashboard')" class="text-xs font-bold text-white bg-[#85131B] hover:bg-[#680A10] px-4 py-2.5 rounded-md transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#85131B]">
                            {{ t('Dashboard Admin →', 'Admin Dashboard →') }}
                        </Link>
                    </template>
                    <template v-else-if="$page.props.auth?.user">
                        <Link :href="route('user.dashboard')" class="text-xs font-bold text-white bg-[#85131B] hover:bg-[#680A10] px-4 py-2.5 rounded-md transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#85131B]">
                            {{ t('Dashboard Mitra →', 'Partner Dashboard →') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2.5 rounded-md border border-slate-300 hover:bg-slate-50 transition-colors focus-visible:ring-2 focus-visible:ring-[#85131B]">
                            {{ t('Masuk Portal', 'Log in') }}
                        </Link>
                        <Link :href="route('register')" class="text-xs font-bold text-white bg-[#85131B] hover:bg-[#680A10] px-4 py-2.5 rounded-md transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#85131B]">
                            {{ t('Daftar Mitra', 'Register Partner') }}
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button"
                    class="md:hidden p-2 rounded-md text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[#85131B]"
                    :aria-expanded="mobileMenuOpen"
                    :aria-label="mobileMenuOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi'"
                >
                    <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Drawer -->
            <div v-show="mobileMenuOpen" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2 text-xs font-semibold">
                <a @click="mobileMenuOpen = false" href="#jadwal" class="block py-2 text-slate-800">{{ t('Jadwal Kegiatan', 'Event Schedule') }}</a>
                <a @click="mobileMenuOpen = false" href="#kurikulum" class="block py-2 text-slate-800">{{ t('Kurikulum Edukasi', 'Curriculum') }}</a>
                <a @click="mobileMenuOpen = false" href="#galeri-investasi" class="block py-2 text-slate-800">{{ t('Jejaring Galeri', 'Gallery Network') }}</a>
                <a @click="mobileMenuOpen = false" href="#faq" class="block py-2 text-slate-800">{{ t('Tanya Jawab', 'FAQ') }}</a>
                <a @click="mobileMenuOpen = false" href="#kontak" class="block py-2 text-slate-800">{{ t('Kontak Kantor', 'Office Contact') }}</a>
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <template v-if="$page.props.auth?.admin">
                        <Link :href="route('admin.dashboard')" class="w-full text-center py-2.5 font-bold text-white bg-[#85131B] rounded-md">{{ t('Dashboard Admin', 'Admin Dashboard') }}</Link>
                    </template>
                    <template v-else-if="$page.props.auth?.user">
                        <Link :href="route('user.dashboard')" class="w-full text-center py-2.5 font-bold text-white bg-[#85131B] rounded-md">{{ t('Dashboard Mitra', 'Partner Dashboard') }}</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="w-full text-center py-2.5 text-slate-800 border border-slate-300 rounded-md">{{ t('Masuk Portal', 'Log in') }}</Link>
                        <Link :href="route('register')" class="w-full text-center py-2.5 font-bold text-white bg-[#85131B] rounded-md">{{ t('Daftar Mitra', 'Register Partner') }}</Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- MAIN EDITORIAL CONTENT -->
        <main id="beranda">
            <section class="relative isolate min-h-[470px] overflow-hidden bg-slate-950 sm:min-h-[520px] lg:min-h-[560px]">
                <img
                    src="/images/auth-illustration.png"
                    alt="Ilustrasi kegiatan edukasi pasar modal"
                    class="absolute inset-0 h-full w-full object-cover object-center"
                />
                <div aria-hidden="true" class="absolute inset-0 bg-slate-950/80"></div>

                <div class="relative mx-auto flex min-h-[inherit] max-w-7xl items-end px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                    <div class="max-w-4xl space-y-5">
                        <p class="text-xs font-bold uppercase tracking-widest text-red-200">
                            {{ t('Bursa Efek Indonesia · Kantor Perwakilan Bengkulu', 'Indonesia Stock Exchange · Bengkulu Representative Office') }}
                        </p>
                        <h1 class="text-4xl font-black leading-tight text-white sm:text-5xl">
                            SIPENA Bengkulu
                        </h1>
                        <p class="max-w-2xl text-sm leading-relaxed text-white/90 sm:text-base">
                            {{ t('Temukan Sekolah Pasar Modal, edukasi investasi syariah, dan kegiatan literasi pasar modal dari BEI Bengkulu.', 'Discover Capital Market School, Islamic investment education, and financial literacy programs from IDX Bengkulu.') }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <a
                                href="#jadwal"
                                class="inline-flex min-h-11 items-center rounded-md bg-[#9F0E0F] px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-[#680A10] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                {{ t('Lihat Jadwal Kegiatan', 'View Event Schedule') }}
                            </a>
                            <Link
                                :href="route('register')"
                                class="inline-flex min-h-11 items-center rounded-md border border-white/70 bg-slate-950/30 px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-white hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                {{ t('Daftar Mitra', 'Register Partner') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- JADWAL LEDGER / AGENDA TABLE (Primary Content) -->
            <section id="jadwal" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <!-- Section Header with Functional Filters -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-5">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-widest text-[#85131B]">
                            {{ t('Kalender Pelaksanaan', 'Event Calendar') }}
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                            {{ t('Jadwal Edukasi Pasar Modal', 'Capital Market Education Schedule') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">
                            {{ t('Pilih sesi pembelajaran tatap muka maupun daring yang sedang membuka pendaftaran.', 'Choose in-person or online learning sessions currently open for registration.') }}
                        </p>
                    </div>

                    <!-- Search Bar -->
                    <div class="w-full md:w-80">
                        <label for="search-input" class="sr-only">{{ t('Cari kegiatan', 'Search event') }}</label>
                        <input
                            id="search-input"
                            v-model="searchQuery"
                            type="search"
                            :placeholder="t('Cari topik, narasumber, atau lokasi...', 'Search topic, speaker, or location...')"
                            class="min-h-11 w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                        />
                    </div>
                </div>

                <!-- Filter Controls: Categories -->
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs">
                    <!-- Category Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button
                            v-for="cat in categories"
                            :key="cat"
                            @click="selectedCategory = cat"
                            type="button"
                            :class="[
                                'min-h-11 rounded-md px-3 py-2 text-xs font-bold transition-colors cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]',
                                selectedCategory === cat
                                    ? 'bg-[#85131B] text-white'
                                    : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-300'
                            ]"
                        >
                            {{ cat }}
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            v-if="searchQuery || (selectedCategory !== 'Semua' && selectedCategory !== 'All')"
                            @click="resetFilter"
                            type="button"
                            class="text-xs font-bold text-[#85131B] hover:underline cursor-pointer"
                        >
                            {{ t('Reset Filter', 'Reset Filter') }}
                        </button>
                    </div>
                </div>

                <!-- Structured Agenda Ledger List -->
                <div v-if="filteredAgenda.length > 0" class="mt-6 border border-slate-200 rounded-lg overflow-hidden divide-y divide-slate-200 bg-white">
                    <article
                        v-for="item in filteredAgenda"
                        :key="item.id"
                        class="p-4 sm:p-5 hover:bg-slate-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <!-- Date & Main Info -->
                        <div class="flex items-start gap-4">
                            <!-- Date Box -->
                            <div class="w-16 shrink-0 text-center border border-slate-300 rounded bg-slate-50 p-2">
                                <div class="text-xs font-semibold text-slate-600">{{ formatWeekday(item.tanggal) }}</div>
                                <div class="text-lg font-black text-slate-900 leading-none my-0.5">{{ formatDate(item.tanggal, { day: '2-digit' }) }}</div>
                                <div class="text-[10px] font-bold uppercase text-[#85131B]">{{ formatDate(item.tanggal, { month: 'short', year: 'numeric' }) }}</div>
                            </div>

                            <!-- Details -->
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-700 rounded border border-slate-200">
                                        {{ item.kategori }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        {{ item.jam }}
                                    </span>
                                </div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                    {{ item.judul }}
                                </h3>
                                <div class="text-xs text-slate-600 flex flex-wrap items-center gap-x-4 gap-y-1 pt-1">
                                    <div class="break-words"><strong>{{ t('Lokasi:', 'Location:') }}</strong> {{ item.lokasi }}</div>
                                    <div class="break-words"><strong>{{ t('Mitra:', 'Partner:') }}</strong> {{ item.mitra }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Action Button -->
                        <div class="flex items-center md:flex-col md:items-end justify-between md:justify-center gap-3 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100">
                            <div class="text-left md:text-right">
                                <div
                                    :class="[
                                        'text-xs font-bold',
                                        item.statusKode === 'full' ? 'text-rose-700' :
                                        item.statusKode === 'limited' ? 'text-amber-700' : 'text-emerald-700'
                                    ]"
                                >
                                    {{ formatStatus(item.statusKode, item.status) }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    <template v-if="item.kuotaTotal !== null">
                                        {{ t('Kuota:', 'Quota:') }} {{ item.kuotaTotal }} {{ t('peserta', 'participants') }}
                                    </template>
                                    <template v-else>
                                        {{ t('Kuota: Bebas', 'Quota: Unlimited') }}
                                    </template>
                                </div>
                            </div>
                            <button
                                @click="openModal(item)"
                                type="button"
                                class="min-h-11 px-3.5 py-2 text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 rounded-md shadow-2xs transition-colors cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                            >
                                {{ t('Rincian Sesi', 'Session Details') }}
                            </button>
                        </div>
                    </article>
                </div>

                <!-- Empty State (R-27 Requirement) -->
                <div v-else class="mt-6 bg-slate-50 border border-dashed border-slate-300 rounded-lg p-10 text-center space-y-3">
                    <div class="text-sm font-bold text-slate-900">
                        {{ t('Tidak ada agenda sosialisasi yang sesuai dengan kriteria', 'No education sessions match your criteria') }}
                    </div>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        {{ t('Silakan periksa kata kunci pencarian atau bersihkan filter kategori yang sedang aktif.', 'Please check your search keywords or clear active category filters.') }}
                    </p>
                    <button
                        @click="resetFilter"
                        type="button"
                        class="px-4 py-2 bg-white border border-slate-300 text-xs font-bold text-slate-800 rounded hover:bg-slate-100 cursor-pointer"
                    >
                        {{ t('Hapus Filter Pencarian', 'Clear Search Filter') }}
                    </button>
                </div>
            </section>

            <!-- KURIKULUM & SILABUS BERJENJANG (Editorial Syllabus Table) -->
            <section id="kurikulum" class="border-t border-b border-slate-200 bg-slate-50/70 py-12 lg:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl space-y-2 mb-8">
                        <div class="text-xs font-bold uppercase tracking-widest text-[#85131B]">
                            {{ t('Jenjang Pendidikan Pasar Modal', 'Capital Market Education Tiers') }}
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                            {{ t('Silabus Edukasi Resmi Bursa Efek Indonesia', 'Official Education Syllabus of Indonesia Stock Exchange') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600">
                            {{ t('Pembinaan literasi dan inklusi keuangan pasar modal dirancang secara bertahap untuk memastikan pemahaman risiko mendahului keputusan investasi.', 'Financial literacy and capital market inclusion programs are designed in stages to ensure risk awareness precedes investment decisions.') }}
                        </p>
                    </div>

                    <!-- Syllabus Table -->
                    <div class="syllabus-scroll overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-xs">
                        <table class="syllabus-table w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-200 text-slate-900 font-bold uppercase tracking-wider text-[11px]">
                                    <th class="p-4 w-44">{{ t('Program', 'Program') }}</th>
                                    <th class="p-4 w-40">{{ t('Tingkat Peserta', 'Participant Tier') }}</th>
                                    <th class="p-4">{{ t('Cakupan Kompetensi', 'Competency Scope') }}</th>
                                    <th class="p-4 w-48">{{ t('Luaran & Sertifikasi', 'Outcome & Certification') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-700">
                                <tr v-for="(item, idx) in syllabusItems" :key="idx">
                                    <td :data-label="t('Program', 'Program')" class="p-4 font-bold text-slate-900">
                                        {{ item.program }}
                                    </td>
                                    <td :data-label="t('Tingkat peserta', 'Participant tier')" class="p-4">
                                        {{ item.level }}
                                    </td>
                                    <td :data-label="t('Cakupan kompetensi', 'Competency scope')" class="p-4 leading-relaxed">
                                        {{ item.scope }}
                                    </td>
                                    <td :data-label="t('Luaran & sertifikasi', 'Outcome & certification')" class="p-4 font-medium text-slate-800">
                                        {{ item.outcome }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- JEJARING GALERI INVESTASI BEI DI BENGKULU -->
            <section id="galeri-investasi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="max-w-3xl space-y-2 mb-8">
                    <div class="text-xs font-bold uppercase tracking-widest text-[#85131B]">
                        {{ t('Jejaring Edukasi Daerah', 'Regional Education Network') }}
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                        {{ t('Galeri Investasi BEI di Perguruan Tinggi Bengkulu', 'IDX Investment Galleries at Bengkulu Universities') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600">
                        {{ t('Galeri Investasi BEI merupakan kerja sama sinergis antara PT Bursa Efek Indonesia, perguruan tinggi setempat, dan perusahaan sekuritas anggota bursa untuk menghadirkan laboratorium pasar modal langsung di lingkungan kampus.', 'IDX Investment Gallery is a synergistic collaboration between PT Indonesia Stock Exchange, local universities, and exchange member brokerage firms to establish capital market laboratories on campus.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="(gi, idx) in galeriInvestasiList"
                        :key="idx"
                        class="p-5 border border-slate-200 rounded-lg bg-white shadow-2xs space-y-2 text-xs"
                    >
                        <div class="font-bold text-sm text-slate-900 leading-snug">
                            {{ gi.nama }}
                        </div>
                        <div class="text-slate-600">
                            <strong>{{ t('Sekuritas Mitra:', 'Partner Brokerage:') }}</strong> {{ gi.mitra }}
                        </div>
                        <div class="text-slate-600">
                            <strong>{{ t('Lokasi:', 'Location:') }}</strong> {{ gi.lokasi }}
                        </div>
                        <div class="text-slate-500 pt-1 border-t border-slate-100">
                            {{ gi.layanan }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- TANYA JAWAB TEKNIS (Real FAQ) -->
            <section id="faq" class="border-t border-slate-200 bg-slate-50/60 py-12 lg:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-4 space-y-3">
                            <div class="text-xs font-bold uppercase tracking-widest text-[#85131B]">
                                {{ t('Pertanyaan Umum', 'Frequently Asked Questions') }}
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                                {{ t('Panduan Partisipasi Edukasi Pasar Modal', 'Guide to Capital Market Education Participation') }}
                            </h2>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ t('Rangkuman informasi praktis mengenai biaya, persyaratan dokumen, sertifikat, dan pengajuan narasumber di KP BEI Bengkulu.', 'Summary of practical information regarding fees, required documents, certificates, and inviting speakers at IDX Bengkulu.') }}
                            </p>
                        </div>

                        <div class="lg:col-span-8 space-y-3">
                            <div
                                v-for="(faq, idx) in faqs"
                                :key="idx"
                                class="border border-slate-200 rounded-lg bg-white overflow-hidden shadow-2xs"
                            >
                                <button
                                    @click="toggleFaq(idx)"
                                    type="button"
                                    class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-[#85131B] flex items-center justify-between gap-4 transition-colors cursor-pointer"
                                    :aria-expanded="activeFaq === idx"
                                >
                                    <span>{{ faq.q }}</span>
                                    <span class="text-slate-500 font-bold text-sm shrink-0">
                                        {{ activeFaq === idx ? '−' : '+' }}
                                    </span>
                                </button>
                                <div
                                    v-show="activeFaq === idx"
                                    class="px-4 pb-4 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3"
                                >
                                    {{ faq.a }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KONTAK & LOKASI KANTOR PERWAKILAN -->
            <section id="kontak" class="bg-slate-900 text-white">
                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                    <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12 lg:items-center">
                        <div class="lg:col-span-7 space-y-3">
                            <div class="text-xs font-bold uppercase tracking-wider text-red-400">
                                {{ t('Pelayanan Publik & Konsultasi', 'Public Services & Consultation') }}
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold">
                                {{ t('Kunjungi Kantor Perwakilan BEI Bengkulu', 'Visit the IDX Bengkulu Representative Office') }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                {{ t('Masyarakat, investor, mahasiswa, dan media dipersilakan berkonsultasi langsung mengenai informasi pasar modal, pembukaan akun efek, maupun agenda literasi di kantor perwakilan kami.', 'The public, investors, students, and media are welcome to consult directly regarding capital market information, securities account opening, and literacy agendas.') }}
                            </p>
                            <div class="pt-2 space-y-1.5 text-xs text-slate-300">
                                <div><strong>{{ t('Alamat:', 'Address:') }}</strong> {{ t('Jl. Jendral Sudirman, Kota Bengkulu, Provinsi Bengkulu 38118', 'Jl. Jendral Sudirman, Bengkulu City, Bengkulu Province 38118') }}</div>
                                <div><strong>{{ t('Jam Operasional:', 'Operating Hours:') }}</strong> {{ t('Senin sampai Jumat pukul 08.00 hingga 17.00 WIB', 'Monday to Friday 08:00 to 17:00 WIB') }}</div>
                                <div><strong>{{ t('Layanan:', 'Services:') }}</strong> {{ t('Edukasi publik, Sekolah Pasar Modal, koordinasi riset mahasiswa', 'Public education, Capital Market School, student research coordination') }}</div>
                            </div>
                        </div>

                        <div class="space-y-4 border-t border-white/20 pt-6 text-xs sm:pt-8 lg:col-span-5 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
                            <div class="font-bold text-white uppercase tracking-wider">
                                {{ t('Akses Portal SIPENA', 'Access SIPENA Portal') }}
                            </div>
                            <p class="max-w-md text-slate-300">
                                {{ t('Daftarkan akun mitra untuk mengajukan proposal dan mengikuti kegiatan edukasi BEI.', 'Register a partner account to submit proposals and join IDX education events.') }}
                            </p>
                            <div class="flex flex-col gap-2.5 sm:flex-row">
                                <Link
                                    :href="route('register')"
                                    class="w-full text-center px-4 py-2.5 bg-[#85131B] hover:bg-[#680A10] text-white font-bold rounded shadow-xs"
                                >
                                    {{ t('Daftar Akun Baru', 'Register New Account') }}
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="w-full text-center px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded"
                                >
                                    {{ t('Masuk Portal', 'Log in') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- INSTITUTIONAL FOOTER -->
        <footer class="bg-slate-950 text-slate-400 border-t border-slate-900 text-xs py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <!-- Brand Column -->
                    <div class="md:col-span-5 space-y-2">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-white px-2.5 py-1.5 rounded-lg inline-flex items-center shadow-xs">
                                <img src="/images/logo.svg" alt="SIPENA IDX Bengkulu" class="h-8 w-auto" />
                            </div>
                        </div>
                        <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                            {{ t('Sistem Informasi Perencanaan dan Edukasi Pasar Modal pada PT Bursa Efek Indonesia Kantor Perwakilan Bengkulu.', 'Information System for Capital Market Planning and Education at PT Indonesia Stock Exchange Bengkulu Representative Office.') }}
                        </p>
                    </div>

                    <!-- Navigation Links -->
                    <div class="md:col-span-4 space-y-1.5">
                        <div class="font-bold text-white uppercase tracking-wider text-[11px] mb-2">{{ t('Navigasi Halaman', 'Page Navigation') }}</div>
                        <ul class="space-y-1 text-slate-400">
                            <li><a href="#jadwal" class="hover:text-white">{{ t('Jadwal Sosialisasi', 'Event Schedule') }}</a></li>
                            <li><a href="#kurikulum" class="hover:text-white">{{ t('Silabus Kurikulum SPM', 'SPM Curriculum Syllabus') }}</a></li>
                            <li><a href="#galeri-investasi" class="hover:text-white">{{ t('Galeri Investasi Kampus', 'Campus Investment Galleries') }}</a></li>
                            <li><a href="#faq" class="hover:text-white">{{ t('Tanya Jawab Peserta', 'Participant FAQ') }}</a></li>
                            <li><a href="#kontak" class="hover:text-white">{{ t('Lokasi & Kontak Kantor', 'Office Location & Contact') }}</a></li>
                        </ul>
                    </div>

                    <!-- Regulatory / Official Links -->
                    <div class="md:col-span-3 space-y-1.5">
                        <div class="font-bold text-white uppercase tracking-wider text-[11px] mb-2">{{ t('Regulasi & Institusi', 'Regulations & Institutions') }}</div>
                        <ul class="space-y-1 text-slate-400">
                            <li><a href="https://www.idx.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-white">PT Bursa Efek Indonesia (BEI)</a></li>
                            <li><a href="https://ojk.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-white">Otoritas Jasa Keuangan (OJK)</a></li>
                            <li><a href="https://www.ksei.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-white">Kustodian Sentral Efek Indonesia (KSEI)</a></li>
                            <li><a href="https://www.kpei.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-white">Kliring Penjaminan Efek Indonesia (KPEI)</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright & Disclaimer -->
                <div class="pt-6 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-500 text-[11px]">
                    <div>
                        {{ t('Hak Cipta © 2026 PT Bursa Efek Indonesia Kantor Perwakilan Bengkulu. Seluruh hak cipta dilindungi.', 'Copyright © 2026 PT Indonesia Stock Exchange Bengkulu Representative Office. All rights reserved.') }}
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('login')" class="hover:text-slate-300">{{ t('Masuk ke Sistem', 'Log in to System') }}</Link>
                        <span>|</span>
                        <Link :href="route('register')" class="hover:text-slate-300">{{ t('Pendaftaran Mitra', 'Partner Registration') }}</Link>
                    </div>
                </div>
            </div>
        </footer>

        <!-- MODAL RINCIAN KEGIATAN (Accessible Modal Dialog) -->
        <div
            v-if="selectedAgenda"
            @keydown.escape.window="closeModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70"
            role="dialog"
            aria-modal="true"
        >
            <div class="bg-white rounded-lg max-w-xl w-full border border-slate-300 shadow-2xl p-6 space-y-4">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-800 rounded">
                                {{ selectedAgenda.kategori }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-1 leading-snug">
                            {{ selectedAgenda.judul }}
                        </h3>
                    </div>
                    <button
                        @click="closeModal"
                        type="button"
                        class="w-7 h-7 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#85131B]"
                        :aria-label="t('Tutup jendela rincian', 'Close details modal')"
                    >
                        ✕
                    </button>
                </div>

                <!-- Detail Specification -->
                <div class="space-y-2 text-xs text-slate-700">
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">{{ t('Jadwal:', 'Schedule:') }}</span>
                        <span class="col-span-2 font-bold text-slate-900">{{ formatWeekday(selectedAgenda.tanggal) }}, {{ formatDate(selectedAgenda.tanggal, { day: '2-digit', month: 'short', year: 'numeric' }) }} ({{ selectedAgenda.jam }})</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">{{ t('Lokasi:', 'Location:') }}</span>
                        <span class="col-span-2 font-semibold text-slate-800">{{ selectedAgenda.lokasi }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">{{ t('Mitra Penyelenggara:', 'Partner Organizer:') }}</span>
                        <span class="col-span-2 text-slate-800">{{ selectedAgenda.mitra }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">{{ t('Kuota Peserta:', 'Participant Quota:') }}</span>
                        <span class="col-span-2 font-bold text-slate-900">
                            <template v-if="selectedAgenda.kuotaTotal !== null">
                                {{ selectedAgenda.kuotaTotal }} {{ t('peserta', 'participants') }} ({{ formatStatus(selectedAgenda.statusKode, selectedAgenda.status) }})
                            </template>
                            <template v-else>
                                {{ t('Kuota Bebas', 'Unlimited Quota') }} ({{ formatStatus(selectedAgenda.statusKode, selectedAgenda.status) }})
                            </template>
                        </span>
                    </div>
                    <div class="pt-1">
                        <span class="text-slate-500 font-medium block mb-1">{{ t('Materi & Pembahasan:', 'Material & Discussion:') }}</span>
                        <RichTextContent :html="selectedAgenda.deskripsiHtml" class="text-slate-600 leading-relaxed bg-slate-50 p-2.5 rounded border border-slate-200" />
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button
                        @click="closeModal"
                        type="button"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded cursor-pointer"
                    >
                        {{ t('Tutup', 'Close') }}
                    </button>
                    
                </div>
            </div>
        </div>
    </div>
</template>
