<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';

// Filter & Search State
const searchQuery = ref('');
const selectedCategory = ref('Semua');
const selectedFormat = ref('Semua');
const activeFaq = ref(null);
const selectedAgenda = ref(null);
const mobileMenuOpen = ref(false);

const categories = [
    'Semua',
    'Sekolah Pasar Modal',
    'Pasar Modal Syariah',
    'Analisis & Valuasi',
    'Literasi Publik'
];

const formatOptions = ['Semua', 'Tatap Muka', 'Daring'];

// Realistis: Jadwal kegiatan sosialisasi BEI KP Bengkulu
const agendaItems = [
    {
        id: 1,
        tanggal: '15 Okt 2026',
        hari: 'Kamis',
        jam: '09.00 - 12.00 WIB',
        kategori: 'Sekolah Pasar Modal',
        judul: 'Sekolah Pasar Modal (SPM) Level 1: Pengenalan Efek & Pembukaan Rekening',
        format: 'Tatap Muka',
        lokasi: 'Ruang Edukasi KP BEI Bengkulu, Jl. Jendral Sudirman',
        mitra: 'Phintraco Sekuritas Cabang Bengkulu',
        sasaran: 'Mahasiswa, akademisi, dan pemula non-investor',
        kuotaTotal: 40,
        kuotaTerisi: 32,
        status: 'Pendaftaran Dibuka',
        statusKode: 'open',
        biaya: 'Gratis (Setoran awal saldo saham Rp100.000 menjadi milik peserta)',
        deskripsi: 'Membahas pengantar struktur pasar modal Indonesia, fungsi BEI, KPEI, dan KSEI, cara membaca ticker saham, serta pendampingan langsung pembukaan Rekening Dana Nasabah (RDN).'
    },
    {
        id: 2,
        tanggal: '22 Okt 2026',
        hari: 'Kamis',
        jam: '13.30 - 15.30 WIB',
        kategori: 'Pasar Modal Syariah',
        judul: 'Lokakarya Investasi Syariah: Mengenal Saham DES dan Sistem SOTS',
        format: 'Daring',
        lokasi: 'Zoom Meeting KP BEI Bengkulu',
        mitra: 'FAC Sekuritas & Tim Pasar Modal Syariah BEI',
        sasaran: 'Masyarakat umum, perbankan syariah, dan akademisi',
        kuotaTotal: 150,
        kuotaTerisi: 104,
        status: 'Pendaftaran Dibuka',
        statusKode: 'open',
        biaya: 'Gratis',
        deskripsi: 'Penjelasan prinsip muamalah syariah di bursa efek, kriteria screening Daftar Efek Syariah (DES) oleh OJK, serta transaksi via Sharia Online Trading System (SOTS).'
    },
    {
        id: 3,
        tanggal: '29 Okt 2026',
        hari: 'Kamis',
        jam: '09.00 - 15.00 WIB',
        kategori: 'Sekolah Pasar Modal',
        judul: 'Sekolah Pasar Modal (SPM) Level 2: Praktik Analisis Fundamental Perusahaan',
        format: 'Tatap Muka',
        lokasi: 'Galeri Investasi BEI Universitas Bengkulu (FEB UNIB)',
        mitra: 'Indo Premier Sekuritas (IPOT)',
        sasaran: 'Alumni SPM Level 1 dan investor aktif',
        kuotaTotal: 35,
        kuotaTerisi: 31,
        status: 'Sisa Kuota Terbatas',
        statusKode: 'limited',
        biaya: 'Gratis',
        deskripsi: 'Membongkar laporan keuangan emiten kuartalan dan tahunan, kalkulasi rasio Price to Earnings (PER), Price to Book Value (PBV), Debt to Equity (DER), serta studi kasus valuasi saham perbankan.'
    },
    {
        id: 4,
        tanggal: '05 Nov 2026',
        hari: 'Kamis',
        jam: '09.30 - 12.00 WIB',
        kategori: 'Literasi Publik',
        judul: 'Sosialisasi Cerdas Berinvestasi & Waspada Modus Investasi Ilegal',
        format: 'Tatap Muka',
        lokasi: 'Aula Balai Kota Bengkulu',
        mitra: 'Satgas Pemberantasan Aktivitas Keuangan Ilegal (PAKI)',
        sasaran: 'ASN, organisasi perempuan, dan masyarakat umum',
        kuotaTotal: 100,
        kuotaTerisi: 78,
        status: 'Pendaftaran Dibuka',
        statusKode: 'open',
        biaya: 'Gratis',
        deskripsi: 'Edukasi cara membedakan penawaran investasi legal vs penipuan berkedok robot trading atau skema piramida, serta pemeriksaan status legalitas izin usaha di Otoritas Jasa Keuangan.'
    },
    {
        id: 5,
        tanggal: '12 Nov 2026',
        hari: 'Kamis',
        jam: '14.00 - 16.00 WIB',
        kategori: 'Analisis & Valuasi',
        judul: 'Bedah Teknikal Dasar: Membaca Grafik Candlestick & Batas Risiko',
        format: 'Daring',
        lokasi: 'Live Webinar Streaming YouTube & Zoom',
        mitra: 'Mandiri Sekuritas',
        sasaran: 'Investor retail pemula',
        kuotaTotal: 200,
        kuotaTerisi: 165,
        status: 'Pendaftaran Dibuka',
        statusKode: 'open',
        biaya: 'Gratis',
        deskripsi: 'Dasar membaca support & resistance, volume perdagangan, indikator tren Moving Average, dan disiplin penetapan titik cut-loss untuk memitigasi penurunan modal.'
    },
    {
        id: 6,
        tanggal: '19 Nov 2026',
        hari: 'Kamis',
        jam: '09.00 - 12.00 WIB',
        kategori: 'Literasi Publik',
        judul: 'Kuliah Umum: Peran Pasar Modal dalam Pertumbuhan Ekonomi Daerah',
        format: 'Tatap Muka',
        lokasi: 'Auditorium Kampus IAIN / UINFAS Bengkulu',
        mitra: 'Kelompok Studi Pasar Modal (KSPM) FEBI',
        sasaran: 'Mahasiswa dan dosen',
        kuotaTotal: 80,
        kuotaTerisi: 80,
        status: 'Kuota Penuh',
        statusKode: 'full',
        biaya: 'Gratis',
        deskripsi: 'Kuliah umum mengenai peluang pendanaan usaha lokal melalui pasar modal, obligasi daerah, serta pemanfaatan fasilitas Galeri Investasi BEI di lingkungan kampus.'
    }
];

// Jejaring Galeri Investasi Resmi di Bengkulu
const galeriInvestasiList = [
    {
        nama: 'GI BEI Fakultas Ekonomi & Bisnis Universitas Bengkulu',
        mitra: 'Indo Premier Sekuritas',
        lokasi: 'Kandang Limun, Muara Bangka Hulu, Kota Bengkulu',
        layanan: 'SPM Reguler, Lab Simulasi Trading, Sertifikasi WPPE'
    },
    {
        nama: 'GI BEI FEBI UIN Fatmawati Sukarno (UINFAS) Bengkulu',
        mitra: 'Phintraco Sekuritas',
        lokasi: 'Pagar Dewa, Selebar, Kota Bengkulu',
        layanan: 'Pusat Edukasi Pasar Modal Syariah & Klinik RDN'
    },
    {
        nama: 'GI BEI Universitas Dehasen (UNIVED) Bengkulu',
        mitra: 'MNC Sekuritas',
        lokasi: 'Jl. Meranti Raya No. 32, Sawah Lebar, Kota Bengkulu',
        layanan: 'Konsultasi Saham Pemula & Kuliah Tamu Emiten'
    },
    {
        nama: 'GI BEI Universitas Muhammadiyah Bengkulu (UMB)',
        mitra: 'RHB Sekuritas Indonesia',
        lokasi: 'Kampus IV UMB, Jl. Adam Malik, Kota Bengkulu',
        layanan: 'Edukasi KSPM & Riset Pasar Modal Mahasiswa'
    }
];

const filteredAgenda = computed(() => {
    return agendaItems.filter(item => {
        const matchCategory = selectedCategory.value === 'Semua' || item.kategori === selectedCategory.value;
        const matchFormat = selectedFormat.value === 'Semua' || item.format === selectedFormat.value;
        const query = searchQuery.value.trim().toLowerCase();
        const matchQuery = !query ||
            item.judul.toLowerCase().includes(query) ||
            item.kategori.toLowerCase().includes(query) ||
            item.lokasi.toLowerCase().includes(query) ||
            item.mitra.toLowerCase().includes(query);

        return matchCategory && matchFormat && matchQuery;
    });
});

const resetFilter = () => {
    searchQuery.value = '';
    selectedCategory.value = 'Semua';
    selectedFormat.value = 'Semua';
};

const faqs = [
    {
        q: 'Apakah Sekolah Pasar Modal (SPM) di KP BEI Bengkulu dipungut biaya pelatihan?',
        a: 'Tidak. Seluruh materi pembinaan dan fasilitas pelatihan diselenggarakan tanpa biaya oleh BEI. Pada sesi SPM Level 1, peserta hanya menyetorkan saldo awal pembukaan rekening efek (biasanya Rp100.000) yang sepenuhnya menjadi saldo investasi milik peserta sendiri.'
    },
    {
        q: 'Dokumen apa saja yang diperlukan untuk pembukaan Rekening Dana Nasabah (RDN)?',
        a: 'Peserta cukup menyiapkan e-KTP fisik, buku tabungan bank atas nama pribadi (atau m-banking untuk verifikasi nomor rekening), serta NPWP (apabila sudah memiliki). Pendampingan pengisian formulir dilakukan langsung bersama perwakilan sekuritas mitra di lokasi.'
    },
    {
        q: 'Apakah peserta akan memperoleh sertifikat kepesertaan setelah mengikuti SPM?',
        a: 'Ya. Peserta yang hadir penuh pada sesi SPM Level 1 dan Level 2 akan memperoleh sertifikat elektronik resmi dari Bursa Efek Indonesia melalui akun portal peserta SIPENA.'
    },
    {
        q: 'Bagaimana prosedur bagi universitas atau instansi yang ingin mengundang narasumber BEI?',
        a: 'Pengelola instansi atau perguruan tinggi dapat mengajukan surat permohonan resmi kepada Kepala Kantor Perwakilan BEI Bengkulu atau berkoordinasi langsung melalui Galeri Investasi BEI terdekat minimal 14 hari sebelum rencana kegiatan.'
    }
];

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
    <Head title="SIPENA: Portal Edukasi & Kegiatan Pasar Modal IDX Bengkulu" />

    <div class="min-h-screen bg-white text-slate-800 font-sans antialiased selection:bg-[#85131B] selection:text-white">
        <!-- TOP INSTITUTIONAL IDENTITY BAR -->
        <div class="bg-slate-900 text-slate-300 text-xs border-b border-slate-800 hidden sm:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <span class="font-medium text-slate-300">
                        PT Bursa Efek Indonesia (BEI) Kantor Perwakilan Bengkulu
                    </span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-400">
                        Senin - Jumat 08.00 - 17.00 WIB
                    </span>
                </div>
                <div class="flex items-center gap-4 text-slate-400">
                    <a href="https://www.idx.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">
                        idx.co.id
                    </a>
                    <span class="text-slate-700">|</span>
                    <Link :href="route('login')" class="hover:text-white transition-colors">
                        Masuk ke Sistem
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
                    <a href="#jadwal" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">Jadwal Kegiatan</a>
                    <a href="#kurikulum" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">Kurikulum Edukasi</a>
                    <a href="#galeri-investasi" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">Jejaring Galeri</a>
                    <a href="#faq" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">Tanya Jawab</a>
                    <a href="#kontak" class="hover:text-[#85131B] transition-colors focus-visible:outline-2 focus-visible:outline-[#85131B]">Kontak Kantor</a>
                </nav>

                <ThemeToggle />

                <!-- Auth Navigation -->
                <div class="hidden sm:flex items-center gap-2.5">
                    <Link :href="route('login')" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2.5 rounded-md border border-slate-300 hover:bg-slate-50 transition-colors focus-visible:ring-2 focus-visible:ring-[#85131B]">
                        Masuk Portal
                    </Link>
                    <Link :href="route('register')" class="text-xs font-bold text-white bg-[#85131B] hover:bg-[#680A10] px-4 py-2.5 rounded-md transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#85131B]">
                        Daftar Mitra
                    </Link>
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
                <a @click="mobileMenuOpen = false" href="#jadwal" class="block py-2 text-slate-800">Jadwal Kegiatan</a>
                <a @click="mobileMenuOpen = false" href="#kurikulum" class="block py-2 text-slate-800">Kurikulum Edukasi</a>
                <a @click="mobileMenuOpen = false" href="#galeri-investasi" class="block py-2 text-slate-800">Jejaring Galeri</a>
                <a @click="mobileMenuOpen = false" href="#faq" class="block py-2 text-slate-800">Tanya Jawab</a>
                <a @click="mobileMenuOpen = false" href="#kontak" class="block py-2 text-slate-800">Kontak Kantor</a>
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <Link :href="route('login')" class="w-full text-center py-2.5 text-slate-800 border border-slate-300 rounded-md">Masuk Portal</Link>
                    <Link :href="route('register')" class="w-full text-center py-2.5 font-bold text-white bg-[#85131B] rounded-md">Daftar Mitra</Link>
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
                            Bursa Efek Indonesia · Kantor Perwakilan Bengkulu
                        </p>
                        <h1 class="text-4xl font-black leading-tight text-white sm:text-5xl">
                            SIPENA Bengkulu
                        </h1>
                        <p class="max-w-2xl text-sm leading-relaxed text-white/90 sm:text-base">
                            Temukan Sekolah Pasar Modal, edukasi investasi syariah, dan kegiatan literasi pasar modal dari BEI Bengkulu.
                        </p>
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <a
                                href="#jadwal"
                                class="inline-flex min-h-11 items-center rounded-md bg-[#9F0E0F] px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-[#680A10] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                Lihat Jadwal Kegiatan
                            </a>
                            <Link
                                :href="route('register')"
                                class="inline-flex min-h-11 items-center rounded-md border border-white/70 bg-slate-950/30 px-5 py-2.5 text-xs font-bold text-white transition-colors hover:bg-white hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                Daftar Mitra
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
                            Kalender Pelaksanaan
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                            Jadwal Edukasi Pasar Modal
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">
                            Pilih sesi pembelajaran tatap muka maupun daring yang sedang membuka pendaftaran.
                        </p>
                    </div>

                    <!-- Search Bar -->
                    <div class="w-full md:w-80">
                        <label for="search-input" class="sr-only">Cari kegiatan</label>
                        <input
                            id="search-input"
                            v-model="searchQuery"
                            type="search"
                            placeholder="Cari topik, narasumber, atau lokasi..."
                            class="min-h-11 w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                        />
                    </div>
                </div>

                <!-- Filter Controls: Categories & Formats -->
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

                    <!-- Format Filter & Clear -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">Format:</span>
                            <select
                                v-model="selectedFormat"
                                class="min-h-11 bg-white border border-slate-300 rounded-md px-3 py-2 text-xs font-semibold text-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                            >
                                <option v-for="f in formatOptions" :key="f" :value="f">{{ f }}</option>
                            </select>
                        </div>
                        <button
                            v-if="searchQuery || selectedCategory !== 'Semua' || selectedFormat !== 'Semua'"
                            @click="resetFilter"
                            type="button"
                            class="text-xs font-bold text-[#85131B] hover:underline cursor-pointer"
                        >
                            Reset Filter
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
                                <div class="text-xs font-semibold text-slate-600">{{ item.hari }}</div>
                                <div class="text-lg font-black text-slate-900 leading-none my-0.5">{{ item.tanggal.split(' ')[0] }}</div>
                                <div class="text-[10px] font-bold uppercase text-[#85131B]">{{ item.tanggal.split(' ')[1] }} {{ item.tanggal.split(' ')[2] }}</div>
                            </div>

                            <!-- Details -->
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-700 rounded border border-slate-200">
                                        {{ item.kategori }}
                                    </span>
                                    <span
                                        :class="[
                                            'px-2 py-0.5 text-[10px] font-bold rounded',
                                            item.format === 'Tatap Muka' ? 'bg-amber-100 text-amber-900' : 'bg-sky-100 text-sky-900'
                                        ]"
                                    >
                                        {{ item.format }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        {{ item.jam }}
                                    </span>
                                </div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                    {{ item.judul }}
                                </h3>
                                <div class="text-xs text-slate-600 flex flex-wrap items-center gap-x-4 gap-y-1 pt-1">
                                    <div class="break-words"><strong>Lokasi:</strong> {{ item.lokasi }}</div>
                                    <div class="break-words"><strong>Mitra:</strong> {{ item.mitra }}</div>
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
                                    {{ item.status }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    {{ item.kuotaTerisi }} dari {{ item.kuotaTotal }} kursi terisi
                                </div>
                            </div>
                            <button
                                @click="openModal(item)"
                                type="button"
                                class="min-h-11 px-3.5 py-2 text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 rounded-md shadow-2xs transition-colors cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                            >
                                Rincian Sesi
                            </button>
                        </div>
                    </article>
                </div>

                <!-- Empty State (R-27 Requirement) -->
                <div v-else class="mt-6 bg-slate-50 border border-dashed border-slate-300 rounded-lg p-10 text-center space-y-3">
                    <div class="text-sm font-bold text-slate-900">
                        Tidak ada agenda sosialisasi yang sesuai dengan kriteria
                    </div>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Silakan periksa kata kunci pencarian atau bersihkan filter kategori dan format yang sedang aktif.
                    </p>
                    <button
                        @click="resetFilter"
                        type="button"
                        class="px-4 py-2 bg-white border border-slate-300 text-xs font-bold text-slate-800 rounded hover:bg-slate-100 cursor-pointer"
                    >
                        Hapus Filter Pencarian
                    </button>
                </div>
            </section>

            <!-- KURIKULUM & SILABUS BERJENJANG (Editorial Syllabus Table) -->
            <section id="kurikulum" class="border-t border-b border-slate-200 bg-slate-50/70 py-12 lg:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl space-y-2 mb-8">
                        <div class="text-xs font-bold uppercase tracking-widest text-[#85131B]">
                            Jenjang Pendidikan Pasar Modal
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                            Silabus Edukasi Resmi Bursa Efek Indonesia
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600">
                            Pembinaan literasi dan inklusi keuangan pasar modal dirancang secara bertahap untuk memastikan pemahaman risiko mendahului keputusan investasi.
                        </p>
                    </div>

                    <!-- Syllabus Table -->
                    <div class="syllabus-scroll overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-xs">
                        <table class="syllabus-table w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-200 text-slate-900 font-bold uppercase tracking-wider text-[11px]">
                                    <th class="p-4 w-44">Program</th>
                                    <th class="p-4 w-40">Tingkat Peserta</th>
                                    <th class="p-4">Cakupan Kompetensi</th>
                                    <th class="p-4 w-48">Luaran & Sertifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-700">
                                <tr>
                                    <td data-label="Program" class="p-4 font-bold text-slate-900">
                                        Dasar Pasar Modal
                                    </td>
                                    <td data-label="Tingkat peserta" class="p-4">
                                        Umum & Pemula
                                    </td>
                                    <td data-label="Cakupan kompetensi" class="p-4 leading-relaxed">
                                        Pengenalan OJK, BEI, KPEI, KSEI; konsep dasar investasi versus spekulasi; instrumen saham, reksa dana, dan obligasi; pencegahan penipuan berkedok investasi ilegal.
                                    </td>
                                    <td data-label="Luaran & sertifikasi" class="p-4 font-medium text-slate-800">
                                        Pemahaman literasi keuangan awal
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Program" class="p-4 font-bold text-slate-900">
                                        Sekolah Pasar Modal Level 1
                                    </td>
                                    <td data-label="Tingkat peserta" class="p-4">
                                        Calon Investor Baru
                                    </td>
                                    <td data-label="Cakupan kompetensi" class="p-4 leading-relaxed">
                                        Mekanisme transaksi bursa efek, siklus settlement T+2, struktur biaya transaksi, hak pemegang saham (RUPS & dividen), serta pembukaan Rekening Dana Nasabah (RDN).
                                    </td>
                                    <td data-label="Luaran & sertifikasi" class="p-4 font-medium text-slate-800">
                                        Sertifikat SPM 1 & Akun Efek Aktif
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Program" class="p-4 font-bold text-slate-900">
                                        Sekolah Pasar Modal Level 2
                                    </td>
                                    <td data-label="Tingkat peserta" class="p-4">
                                        Investor Retail Aktif
                                    </td>
                                    <td data-label="Cakupan kompetensi" class="p-4 leading-relaxed">
                                        Analisis fundamental laporan keuangan, evaluasi profitabilitas dan solvabilitas emiten, pengenalan analisis teknikal candlestick, serta penyusunan portofolio jangka panjang.
                                    </td>
                                    <td data-label="Luaran & sertifikasi" class="p-4 font-medium text-slate-800">
                                        Sertifikat Kelulusan SPM Level 2
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Program" class="p-4 font-bold text-slate-900">
                                        Pasar Modal Syariah
                                    </td>
                                    <td data-label="Tingkat peserta" class="p-4">
                                        Peminat Investasi Syariah
                                    </td>
                                    <td data-label="Cakupan kompetensi" class="p-4 leading-relaxed">
                                        Fatwa DSN-MUI tentang pasar modal, metode penyaringan saham syariah (DES), indeks saham syariah (ISSI, JII), dan mekanisme transaksi tanpa margin/short-selling di SOTS.
                                    </td>
                                    <td data-label="Luaran & sertifikasi" class="p-4 font-medium text-slate-800">
                                        Sertifikat Edukasi Syariah BEI
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
                        Jejaring Edukasi Daerah
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                        Galeri Investasi BEI di Perguruan Tinggi Bengkulu
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Galeri Investasi BEI merupakan kerja sama sinergis antara PT Bursa Efek Indonesia, perguruan tinggi setempat, dan perusahaan sekuritas anggota bursa untuk menghadirkan laboratorium pasar modal langsung di lingkungan kampus.
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
                            <strong>Sekuritas Mitra:</strong> {{ gi.mitra }}
                        </div>
                        <div class="text-slate-600">
                            <strong>Lokasi:</strong> {{ gi.lokasi }}
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
                                Pertanyaan Umum
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                                Panduan Partisipasi Edukasi Pasar Modal
                            </h2>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Rangkuman informasi praktis mengenai biaya, persyaratan dokumen, sertifikat, dan pengajuan narasumber di KP BEI Bengkulu.
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
                                Pelayanan Publik & Konsultasi
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold">
                                Kunjungi Kantor Perwakilan BEI Bengkulu
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                Masyarakat, investor, mahasiswa, dan media dipersilakan berkonsultasi langsung mengenai informasi pasar modal, pembukaan akun efek, maupun agenda literasi di kantor perwakilan kami.
                            </p>
                            <div class="pt-2 space-y-1.5 text-xs text-slate-300">
                                <div><strong>Alamat:</strong> Jl. Jendral Sudirman, Kota Bengkulu, Provinsi Bengkulu 38118</div>
                                <div><strong>Jam Operasional:</strong> Senin sampai Jumat pukul 08.00 hingga 17.00 WIB</div>
                                <div><strong>Layanan:</strong> Edukasi publik, Sekolah Pasar Modal, koordinasi riset mahasiswa</div>
                            </div>
                        </div>

                        <div class="space-y-4 border-t border-white/20 pt-6 text-xs sm:pt-8 lg:col-span-5 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
                            <div class="font-bold text-white uppercase tracking-wider">
                                Akses Portal SIPENA
                            </div>
                            <p class="max-w-md text-slate-300">
                                Daftarkan akun mitra untuk mengajukan proposal dan mengikuti kegiatan edukasi BEI.
                            </p>
                            <div class="flex flex-col gap-2.5 sm:flex-row">
                                <Link
                                    :href="route('register')"
                                    class="w-full text-center px-4 py-2.5 bg-[#85131B] hover:bg-[#680A10] text-white font-bold rounded shadow-xs"
                                >
                                    Daftar Akun Baru
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="w-full text-center px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded"
                                >
                                    Masuk Portal
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
                            Sistem Informasi Perencanaan dan Edukasi Pasar Modal pada PT Bursa Efek Indonesia Kantor Perwakilan Bengkulu.
                        </p>
                    </div>

                    <!-- Navigation Links -->
                    <div class="md:col-span-4 space-y-1.5">
                        <div class="font-bold text-white uppercase tracking-wider text-[11px] mb-2">Navigasi Halaman</div>
                        <ul class="space-y-1 text-slate-400">
                            <li><a href="#jadwal" class="hover:text-white">Jadwal Sosialisasi</a></li>
                            <li><a href="#kurikulum" class="hover:text-white">Silabus Kurikulum SPM</a></li>
                            <li><a href="#galeri-investasi" class="hover:text-white">Galeri Investasi Kampus</a></li>
                            <li><a href="#faq" class="hover:text-white">Tanya Jawab Peserta</a></li>
                            <li><a href="#kontak" class="hover:text-white">Lokasi & Kontak Kantor</a></li>
                        </ul>
                    </div>

                    <!-- Regulatory / Official Links -->
                    <div class="md:col-span-3 space-y-1.5">
                        <div class="font-bold text-white uppercase tracking-wider text-[11px] mb-2">Regulasi & Institusi</div>
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
                        Hak Cipta &copy; 2026 PT Bursa Efek Indonesia Kantor Perwakilan Bengkulu. Seluruh hak cipta dilindungi.
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('login')" class="hover:text-slate-300">Masuk ke Sistem</Link>
                        <span>|</span>
                        <Link :href="route('register')" class="hover:text-slate-300">Pendaftaran Mitra</Link>
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
                            <span class="text-xs text-slate-500 font-medium">
                                {{ selectedAgenda.format }}
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
                        aria-label="Tutup jendela rincian"
                    >
                        ✕
                    </button>
                </div>

                <!-- Detail Specification -->
                <div class="space-y-2 text-xs text-slate-700">
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Jadwal:</span>
                        <span class="col-span-2 font-bold text-slate-900">{{ selectedAgenda.hari }}, {{ selectedAgenda.tanggal }} ({{ selectedAgenda.jam }})</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Lokasi:</span>
                        <span class="col-span-2 font-semibold text-slate-800">{{ selectedAgenda.lokasi }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Mitra Penyelenggara:</span>
                        <span class="col-span-2 text-slate-800">{{ selectedAgenda.mitra }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Sasaran Peserta:</span>
                        <span class="col-span-2 text-slate-800">{{ selectedAgenda.sasaran }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Ketentuan Biaya:</span>
                        <span class="col-span-2 text-slate-800">{{ selectedAgenda.biaya }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Status Kuota:</span>
                        <span class="col-span-2 font-bold text-slate-900">{{ selectedAgenda.kuotaTerisi }} dari {{ selectedAgenda.kuotaTotal }} peserta terdaftar ({{ selectedAgenda.status }})</span>
                    </div>
                    <div class="pt-1">
                        <span class="text-slate-500 font-medium block mb-1">Materi & Pembahasan:</span>
                        <p class="text-slate-600 leading-relaxed bg-slate-50 p-2.5 rounded border border-slate-200">
                            {{ selectedAgenda.deskripsi }}
                        </p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button
                        @click="closeModal"
                        type="button"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded cursor-pointer"
                    >
                        Tutup
                    </button>
                    <Link
                        :href="route('login')"
                        class="px-4 py-2 bg-[#85131B] hover:bg-[#680A10] text-white text-xs font-bold rounded shadow-xs"
                    >
                        Masuk untuk Mendaftar
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
