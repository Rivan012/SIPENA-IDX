<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Search & Filter State
const searchQuery = ref('');
const selectedCategory = ref('Semua');
const selectedWilayah = ref('Semua');
const selectedFormat = ref('Semua');
const activeFaq = ref(null);
const selectedModalAgenda = ref(null);

const categories = [
    'Semua',
    'Dasar pasar modal',
    'Sekolah Pasar Modal',
    'Pasar modal syariah',
    'Literasi investasi',
    'Edukasi komunitas'
];

const wilayahList = ['Semua', 'Jakarta', 'Bandung', 'Surabaya', 'Makassar', 'Jayapura', 'Bengkulu'];
const formatList = ['Semua', 'Daring', 'Tatap Muka'];

// 6 Agenda items exactly as in reference design
const agendaList = [
    {
        id: 1,
        day: '15',
        month: 'OKT 2026',
        category: 'Dasar pasar modal',
        categoryClass: 'bg-rose-50 text-rose-700 border-rose-200',
        title: 'Langkah pertama mengenal pasar modal',
        waktu: '10.00 - 12.00 WIB (UTC+7)',
        format: 'Daring webinar',
        formatType: 'Daring',
        wilayah: 'Jakarta',
        lokasi: 'Daring webinar via Zoom',
        penyelenggara: 'Tim edukasi Jakarta (contoh)',
        peserta: 'Masyarakat umum & pemula',
        status: 'Tersedia',
        statusType: 'tersedia',
        deskripsi: 'Pengenalan instrumen dasar pasar modal, cara kerja bursa efek, serta pemahaman risiko awal bagi calon investor baru.'
    },
    {
        id: 2,
        day: '22',
        month: 'OKT 2026',
        category: 'Sekolah Pasar Modal',
        categoryClass: 'bg-amber-50 text-amber-700 border-amber-200',
        title: 'Sekolah Pasar Modal: mengenal saham',
        waktu: '09.00 - 12.00 WIB (UTC+7)',
        format: 'Bandung - ruang edukasi (contoh)',
        formatType: 'Tatap Muka',
        wilayah: 'Bandung',
        lokasi: 'Gedung Edukasi IDX Bandung, Lt. 3',
        penyelenggara: 'Tim edukasi Bandung (contoh)',
        peserta: 'Mahasiswa & calon investor',
        status: 'Terbatas',
        statusType: 'terbatas',
        deskripsi: 'Pembelajaran terstruktur mengenai analisa fundamental sederhana, membaca grafik pergerakan harga saham, dan mekanisme pembukaan rekening efek.'
    },
    {
        id: 3,
        day: '29',
        month: 'OKT 2026',
        category: 'Pasar modal syariah',
        categoryClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        title: 'Memahami investasi saham syariah',
        waktu: '13.00 - 15.00 WITA (UTC+8)',
        format: 'Daring webinar',
        formatType: 'Daring',
        wilayah: 'Makassar',
        lokasi: 'Daring webinar via Live Streaming',
        penyelenggara: 'Tim edukasi Makassar (contoh)',
        peserta: 'Pemula & komunitas',
        status: 'Tersedia',
        statusType: 'tersedia',
        deskripsi: 'Mengenal prinsip syariah di pasar modal, kriteria saham masuk Daftar Efek Syariah (DES), dan akad-akad investasi syariah.'
    },
    {
        id: 4,
        day: '05',
        month: 'NOV 2026',
        category: 'Literasi investasi',
        categoryClass: 'bg-indigo-50 text-indigo-700 border-indigo-200',
        title: 'Kenali risiko, rencanakan investasi',
        waktu: '10.00 - 12.00 WIB (UTC+7)',
        format: 'Surabaya - ruang edukasi (contoh)',
        formatType: 'Tatap Muka',
        wilayah: 'Surabaya',
        lokasi: 'Kantor Perwakilan IDX Surabaya',
        penyelenggara: 'Tim edukasi Surabaya (contoh)',
        peserta: 'Masyarakat umum',
        status: 'Tersedia',
        statusType: 'tersedia',
        deskripsi: 'Manajemen risiko portofolio, diversifikasi aset keuangan, dan strategi menyusun rencana keuangan jangka panjang.'
    },
    {
        id: 5,
        day: '12',
        month: 'NOV 2026',
        category: 'Literasi investasi',
        categoryClass: 'bg-indigo-50 text-indigo-700 border-indigo-200',
        title: 'Membaca informasi perusahaan tercatat',
        waktu: '14.00 - 16.00 WIB (UTC+7)',
        format: 'Daring webinar',
        formatType: 'Daring',
        wilayah: 'Jakarta',
        lokasi: 'Daring webinar via Zoom',
        penyelenggara: 'Tim edukasi nasional (contoh)',
        peserta: 'Investor pemula',
        status: 'Segera dibuka',
        statusType: 'segera',
        deskripsi: 'Cara mengakses dan membedah laporan keuangan publik serta keterbukaan informasi emiten pada situs resmi Bursa Efek Indonesia.'
    },
    {
        id: 6,
        day: '19',
        month: 'NOV 2026',
        category: 'Edukasi komunitas',
        categoryClass: 'bg-sky-50 text-sky-700 border-sky-200',
        title: 'Pasar modal untuk generasi kampus',
        waktu: '09.00 - 11.00 WIT (UTC+9)',
        format: 'Jayapura - ruang edukasi (contoh)',
        formatType: 'Tatap Muka',
        wilayah: 'Jayapura',
        lokasi: 'Galeri Investasi Universitas Cenderawasih',
        penyelenggara: 'Tim edukasi Jayapura (contoh)',
        peserta: 'Mahasiswa & dosen',
        status: 'Terbatas',
        statusType: 'terbatas',
        deskripsi: 'Sosialisasi pengenalan galeri investasi BEI di lingkungan perguruan tinggi serta kompetisi pasar modal antar mahasiswa.'
    }
];

// Reactive filtered list
const filteredAgenda = computed(() => {
    return agendaList.filter(item => {
        const matchesCategory = selectedCategory.value === 'Semua' || item.category === selectedCategory.value;
        const matchesWilayah = selectedWilayah.value === 'Semua' || item.wilayah === selectedWilayah.value;
        const matchesFormat = selectedFormat.value === 'Semua' || item.formatType === selectedFormat.value;
        const matchesQuery = !searchQuery.value ||
            item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.category.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.wilayah.toLowerCase().includes(searchQuery.value.toLowerCase());

        return matchesCategory && matchesWilayah && matchesFormat && matchesQuery;
    });
});

const faqs = [
    {
        q: 'Untuk siapa kegiatan edukasi ini?',
        a: 'Target peserta berbeda pada setiap kegiatan. Periksa keterangan agenda untuk melihat apakah topiknya sesuai bagi pemula, mahasiswa, komunitas, atau peserta lainnya.'
    },
    {
        q: 'Apakah agenda di halaman ini merupakan jadwal resmi?',
        a: 'Tidak. Semua agenda yang ditampilkan adalah contoh konseptual setelah 7 Oktober 2026, bukan pengumuman resmi atau komitmen penyelenggara.'
    },
    {
        q: 'Bagaimana cara mengikuti atau mendaftar kegiatan?',
        a: 'Pada agenda resmi, ikuti ketentuan dan kanal penyelenggara yang tercantum dalam detail kegiatan. Anda dapat masuk melalui portal Peserta SIPENA untuk mengakses agenda pembelajaran aktif.'
    },
    {
        q: 'Apakah semua kegiatan daring dan tanpa biaya?',
        a: 'Format dan ketentuan biaya dapat berbeda. Pastikan informasi lokasi, format, dan biaya melalui kanal resmi penyelenggara sebelum mengikuti kegiatan.'
    }
];

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

const scrollToSection = (id) => {
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <Head title="SIPENA — Sistem Perencanaan & Edukasi Pasar Modal IDX" />

    <div class="min-h-screen bg-[#FBFBFA] text-[#1E293B] antialiased selection:bg-[#85131B] selection:text-white font-sans">
        <!-- TOP NAVBAR -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-[#85131B] text-white flex items-center justify-center font-black text-sm tracking-wider shadow-sm border border-red-900/20">
                        IDX
                    </div>
                    <div>
                        <div class="font-extrabold text-base tracking-tight text-slate-900 leading-none">SIPENA</div>
                        <div class="text-[11px] font-semibold text-slate-500 tracking-wide mt-0.5">Perencanaan & Edukasi</div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-600">
                    <a href="#beranda" @click.prevent="scrollToSection('beranda')" class="hover:text-[#85131B] transition-colors">Beranda</a>
                    <a href="#tentang-idx" @click.prevent="scrollToSection('tentang-idx')" class="hover:text-[#85131B] transition-colors">Tentang IDX</a>
                    <a href="#tentang-sipena" @click.prevent="scrollToSection('tentang-sipena')" class="hover:text-[#85131B] transition-colors">Tentang SIPENA</a>
                    <a href="#jadwal" @click.prevent="scrollToSection('jadwal')" class="hover:text-[#85131B] transition-colors">Jadwal</a>
                    <a href="#faq" @click.prevent="scrollToSection('faq')" class="hover:text-[#85131B] transition-colors">FAQ</a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-2.5">
                    <button @click="scrollToSection('jadwal')" class="text-xs font-bold bg-[#85131B] hover:bg-[#680A10] text-white px-4 py-2.5 rounded-lg shadow-sm transition-all border border-red-900/30 flex items-center gap-1.5 cursor-pointer">
                        <span>Lihat Jadwal</span>
                    </button>
                    <Link :href="route('login')" class="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200/80 px-3 py-2.5 rounded-lg transition-colors border border-slate-200">
                        Masuk
                    </Link>
                </div>
            </div>
        </header>

        <!-- HERO SECTION -->
        <section id="beranda" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 lg:py-20 border-b border-slate-200/70">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Column -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-[#85131B] text-[11px] font-bold tracking-wider uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#85131B]"></span>
                        EDUKASI PASAR MODAL, LEBIH DEKAT
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-[1.15]">
                        Kenali pasar modal.<br>
                        Temukan agenda<br>
                        <span class="text-[#85131B]">belajar Anda.</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 max-w-xl leading-relaxed">
                        SIPENA adalah pusat informasi perencanaan kegiatan edukasi dan sosialisasi pasar modal. Jelajahi topik, wilayah, dan jadwal kegiatan dalam satu tempat.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <button @click="scrollToSection('jadwal')" class="px-5 py-3 rounded-xl bg-[#85131B] hover:bg-[#680A10] text-white font-bold text-xs shadow-md shadow-red-900/20 transition-all border border-red-900/40 cursor-pointer">
                            Lihat Jadwal
                        </button>
                        <button @click="scrollToSection('tentang-sipena')" class="px-5 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-sm transition-all cursor-pointer">
                            Tentang SIPENA
                        </button>
                    </div>
                </div>

                <!-- Right Column: Hero Floating Preview Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-6 relative overflow-hidden">
                        <!-- Date Badge & Tag -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-13 h-13 rounded-xl bg-red-50 border border-red-200 text-[#85131B] flex flex-col items-center justify-center p-1 font-sans">
                                    <span class="text-xl font-black leading-none">15</span>
                                    <span class="text-[9px] font-extrabold tracking-wider uppercase mt-0.5">OKT 2026</span>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Jadwal Unggulan</div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold uppercase tracking-wider">CONTOH AGENDA</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase tracking-wider">DARING</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <h3 class="text-base font-extrabold text-slate-900 leading-snug">
                            Langkah pertama mengenal pasar modal
                        </h3>

                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                            <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                10.00-12.00 WIB
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1 font-medium text-slate-600">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Untuk pemula
                            </span>
                        </div>

                        <!-- Card Footer Disclaimers -->
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] space-y-1">
                            <div class="font-semibold text-slate-500 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                Pratinjau publik — agenda ilustratif bukan pendaftaran aktif
                            </div>
                            <div class="text-[10px] text-slate-400 leading-normal">
                                Ilustrasi kawasan bisnis Jakarta, bukan dokumentasi kegiatan BEI.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DUAL CARDS SECTION: TENTANG SIPENA & MENGENAL IDX BEI -->
        <section id="tentang-sipena" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200/70">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Card 1: TENTANG SIPENA -->
                <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-extrabold tracking-widest uppercase text-[#85131B] mb-2">TENTANG SIPENA</div>
                        <h2 class="text-2xl font-black text-slate-900 leading-snug">
                            Satu pintu untuk menemukan kegiatan edukasi.
                        </h2>
                        <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            SIPENA menghadirkan informasi perencanaan dan jadwal sosialisasi pasar modal dalam tampilan yang mudah dipahami publik. Mulai dari memilih topik hingga membaca detail agenda, temukan konteks yang Anda perlukan sebelum berpartisipasi.
                        </p>

                        <!-- 3 Mini Feature Bullets -->
                        <div class="mt-6 space-y-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#85131B] flex items-center justify-center shrink-0 border border-red-100 font-bold text-xs">
                                    01
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Jadwal yang mudah ditelusuri</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Lihat tanggal, waktu, dan format kegiatan agar Anda dapat merencanakan waktu belajar.</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#85131B] flex items-center justify-center shrink-0 border border-red-100 font-bold text-xs">
                                    02
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Topik sesuai kebutuhan</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Temukan pengenalan dasar hingga pembahasan tematik untuk berbagai tahap belajar.</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#85131B] flex items-center justify-center shrink-0 border border-red-100 font-bold text-xs">
                                    03
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Informasi dalam satu tempat</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Kenali penyelenggara, target peserta, dan lokasi sebelum mengikuti kegiatan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: MENGENAL IDX BEI -->
                <div id="tentang-idx" class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-extrabold tracking-widest uppercase text-slate-400 mb-2">MENGENAL IDX BEI</div>
                        <h2 class="text-2xl font-black text-slate-900 leading-snug">
                            Bursa Efek Indonesia, bagian dari pasar modal Indonesia.
                        </h2>
                        <div class="mt-4 space-y-3 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            <p>
                                Bursa Efek Indonesia (BEI), atau Indonesia Stock Exchange (IDX), menyelenggarakan perdagangan efek di Indonesia. BEI menyediakan sarana bagi transaksi efek serta informasi mengenai perusahaan tercatat dan aktivitas pasar.
                            </p>
                            <p>
                                Edukasi pasar modal membantu masyarakat memahami instrumen investasi, mekanisme transaksi, dan risiko sebelum mengambil keputusan.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <a href="https://www.idx.co.id" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-bold text-[#85131B] hover:text-[#680A10] group">
                            <span>Kenali BEI di idx.co.id</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- PREVIEW JADWAL SOSIALISASI MENDATANG SECTION -->
        <section id="jadwal" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200/70">
            <!-- Header Title -->
            <div class="space-y-2">
                <div class="text-[10px] font-extrabold tracking-widest uppercase text-[#85131B]">
                    CONTOH AGENDA OKTOBER-NOVEMBER 2026
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    Preview Jadwal Sosialisasi Mendatang
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Temukan kegiatan yang sesuai dengan minat, wilayah, dan waktu belajar Anda.
                </p>
            </div>

            <!-- Disclaimer Notice Box -->
            <div class="mt-6 rounded-xl bg-amber-50/70 border border-amber-200/80 p-4 flex items-start gap-3 text-amber-900 text-xs">
                <div class="w-5 h-5 rounded-full bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 font-bold text-[11px] mt-0.5">
                    i
                </div>
                <div>
                    <div class="font-bold text-amber-950">Contoh agenda, bukan jadwal resmi BEI</div>
                    <div class="text-[11px] text-amber-800/90 mt-0.5 leading-relaxed">
                        Judul, tanggal, lokasi, penyelenggara, dan ketersediaan berikut bersifat konseptual. Filter dan tombol hanya pratinjau desain: tidak membuka detail atau pendaftaran.
                    </div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="mt-8 space-y-4">
                <!-- Search Input -->
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari topik atau nama kegiatan..."
                        class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#85131B] focus:border-[#85131B]"
                    />
                </div>

                <!-- Category Pills Filter -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="selectedCategory = cat"
                        :class="[
                            'px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors cursor-pointer',
                            selectedCategory === cat
                                ? 'bg-[#85131B] text-white shadow-sm'
                                : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'
                        ]"
                    >
                        {{ cat === 'Semua' ? 'Semua kategori' : cat }}
                    </button>
                </div>

                <!-- Secondary Filter Selects & Counter -->
                <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Wilayah Select -->
                        <div class="flex items-center gap-1.5 text-xs text-slate-600">
                            <span class="font-medium text-slate-400">Wilayah:</span>
                            <select v-model="selectedWilayah" class="bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-[#85131B]">
                                <option v-for="w in wilayahList" :key="w" :value="w">{{ w === 'Semua' ? 'Semua wilayah' : w }}</option>
                            </select>
                        </div>

                        <!-- Format Select -->
                        <div class="flex items-center gap-1.5 text-xs text-slate-600">
                            <span class="font-medium text-slate-400">Format:</span>
                            <select v-model="selectedFormat" class="bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-[#85131B]">
                                <option v-for="f in formatList" :key="f" :value="f">{{ f === 'Semua' ? 'Semua format' : f }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <strong class="text-slate-800">{{ filteredAgenda.length }}</strong> contoh agenda
                    </div>
                </div>
            </div>

            <!-- 6 Event Cards Grid -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="item in filteredAgenda"
                    :key="item.id"
                    class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between"
                >
                    <div>
                        <!-- Header: Date Box & Category Badge -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 flex flex-col items-center justify-center p-1">
                                <span class="text-lg font-black leading-none">{{ item.day }}</span>
                                <span class="text-[8px] font-extrabold tracking-wider uppercase text-slate-500 mt-0.5">{{ item.month }}</span>
                            </div>
                            <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold border tracking-wide uppercase', item.categoryClass]">
                                {{ item.category }}
                            </span>
                        </div>

                        <!-- Time & Title -->
                        <div class="text-[11px] font-medium text-slate-400 mb-1">
                            {{ item.waktu }}
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900 leading-snug">
                            {{ item.title }}
                        </h3>

                        <!-- Details List -->
                        <div class="mt-4 space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-3">
                            <div class="flex items-start gap-1.5">
                                <span class="text-slate-400 shrink-0">Lokasi:</span>
                                <span class="font-medium text-slate-700">{{ item.format }}</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <span class="text-slate-400 shrink-0">Penyelenggara:</span>
                                <span class="font-medium text-slate-700">{{ item.penyelenggara }}</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <span class="text-slate-400 shrink-0">Peserta:</span>
                                <span class="font-medium text-slate-700">{{ item.peserta }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Status Badge & Action -->
                    <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between">
                        <span :class="[
                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                            item.statusType === 'tersedia' ? 'bg-emerald-50 text-emerald-700' :
                            item.statusType === 'terbatas' ? 'bg-amber-50 text-amber-700' : 'bg-sky-50 text-sky-700'
                        ]">
                            ● {{ item.status }} — ilustrasi
                        </span>
                        <button
                            @click="selectedModalAgenda = item"
                            class="text-xs font-bold text-[#85131B] hover:text-[#680A10] cursor-pointer"
                        >
                            Lihat Detail &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Timezone Note -->
            <div class="mt-8 text-center text-xs text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Waktu mengikuti zona pada setiap agenda: WIB (UTC+7), WITA (UTC+8), atau WIT (UTC+9). Pastikan kembali jadwal melalui kanal resmi penyelenggara.
            </div>
        </section>

        <!-- PILIH TOPIK BELAJAR (4 Learning Topics) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200/70">
            <div class="text-center max-w-2xl mx-auto space-y-2 mb-12">
                <div class="text-[10px] font-extrabold tracking-widest uppercase text-[#85131B]">
                    PILIH TOPIK BELAJAR
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    Ada langkah belajar untuk setiap tahap.
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Kenali tema edukasi sebelum menentukan kegiatan yang ingin Anda ikuti.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Topic 1 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#85131B] flex items-center justify-center font-bold text-xs mb-4">
                            01
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Dasar pasar modal</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Instrumen, pelaku pasar, dan mekanisme transaksi.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold text-[#85131B]">Mulai dari sini &rarr;</span>
                    </div>
                </div>

                <!-- Topic 2 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs mb-4">
                            02
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Sekolah Pasar Modal</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Pembelajaran terstruktur untuk calon investor.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold text-amber-700">Bangun fondasi &rarr;</span>
                    </div>
                </div>

                <!-- Topic 3 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs mb-4">
                            03
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Pasar modal syariah</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Prinsip dan pengenalan instrumen syariah.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold text-emerald-700">Perluas wawasan &rarr;</span>
                    </div>
                </div>

                <!-- Topic 4 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs mb-4">
                            04
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Literasi investasi</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Informasi perusahaan, risiko, dan keputusan investasi.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold text-indigo-700">Belajar lebih lanjut &rarr;</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- DARI RASA INGIN TAHU KE LANGKAH PERTAMA (3 Steps) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200/70">
            <div class="text-center max-w-2xl mx-auto space-y-2 mb-12">
                <div class="text-[10px] font-extrabold tracking-widest uppercase text-[#85131B]">
                    DARI RASA INGIN TAHU KE LANGKAH PERTAMA
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    Temukan. Pahami. Ikuti.
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="text-3xl font-black text-[#85131B]/20 mb-3">01</div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Temukan kegiatan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Telusuri agenda berdasarkan kategori, wilayah, atau format yang Anda pilih.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="text-3xl font-black text-[#85131B]/20 mb-3">02</div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Baca detail agenda</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Periksa waktu, zona waktu, lokasi, target peserta, dan ketentuan penyelenggara.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="text-3xl font-black text-[#85131B]/20 mb-3">03</div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Ikuti arahan penyelenggara</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Untuk agenda resmi, konfirmasi keikutsertaan melalui kanal yang tercantum pada detail kegiatan.
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center text-xs text-slate-400">
                Pada pratinjau ini, alur informatif. Belum ada proses konfirmasi atau pendaftaran yang aktif.
            </div>
        </section>

        <!-- FAQ SECTION -->
        <section id="faq" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200/70">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Left Column -->
                <div class="lg:col-span-4 space-y-3">
                    <div class="text-[10px] font-extrabold tracking-widest uppercase text-[#85131B]">
                        PERTANYAAN UMUM
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug">
                        Sebelum Anda<br>mulai.
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Beberapa hal yang perlu diketahui saat menelusuri kegiatan di SIPENA.
                    </p>
                </div>

                <!-- Right Column: Accordions -->
                <div class="lg:col-span-8 space-y-3">
                    <div
                        v-for="(faq, idx) in faqs"
                        :key="idx"
                        class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm"
                    >
                        <button
                            @click="toggleFaq(idx)"
                            class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-xs sm:text-sm text-slate-900 hover:text-[#85131B] transition-colors cursor-pointer"
                        >
                            <span>{{ faq.q }}</span>
                            <span class="text-slate-400 text-base font-normal">
                                {{ activeFaq === idx ? '−' : '+' }}
                            </span>
                        </button>
                        <div v-show="activeFaq === idx" class="px-5 pb-4 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BOTTOM CALL TO ACTION BANNER -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <div class="bg-gradient-to-b from-white to-slate-50 rounded-3xl border border-slate-200/80 p-10 sm:p-14 shadow-sm max-w-4xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-[#85131B] text-[10px] font-bold tracking-wider uppercase">
                    MULAI DENGAN MEMAHAMI
                </div>

                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    Langkah kecil hari ini.<br>
                    Wawasan lebih luas untuk esok.
                </h2>

                <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto">
                    Jelajahi contoh agenda dan temukan topik pasar modal yang ingin Anda pelajari.
                </p>

                <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                    <button @click="scrollToSection('jadwal')" class="px-6 py-3 rounded-xl bg-[#85131B] hover:bg-[#680A10] text-white font-bold text-xs shadow-md shadow-red-900/20 transition-all cursor-pointer">
                        Jelajahi Jadwal
                    </button>
                    <Link :href="route('register')" class="px-6 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 shadow-sm transition-all">
                        Daftar Akun Peserta
                    </Link>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="bg-white border-t border-slate-200 pt-12 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-slate-100">
                    <!-- Brand Column -->
                    <div class="md:col-span-5 space-y-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#85131B] text-white flex items-center justify-center font-bold text-xs">
                                IDX
                            </div>
                            <div>
                                <span class="font-extrabold text-sm text-slate-900">SIPENA</span>
                                <span class="text-xs text-slate-400 block font-medium">Perencanaan & Edukasi</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                            Pusat informasi perencanaan kegiatan edukasi dan sosialisasi pasar modal.
                        </p>
                        <div class="text-[11px] text-slate-400">
                            IDX / Bursa Efek Indonesia • Konsep landing page publik
                        </div>
                    </div>

                    <!-- Links Column 1 -->
                    <div class="md:col-span-4 space-y-2">
                        <div class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Jelajahi SIPENA</div>
                        <ul class="space-y-2 text-xs text-slate-500">
                            <li><a href="#tentang-sipena" @click.prevent="scrollToSection('tentang-sipena')" class="hover:text-[#85131B]">Tentang SIPENA</a></li>
                            <li><a href="#jadwal" @click.prevent="scrollToSection('jadwal')" class="hover:text-[#85131B]">Jadwal sosialisasi</a></li>
                            <li><a href="#tentang-idx" @click.prevent="scrollToSection('tentang-idx')" class="hover:text-[#85131B]">Tentang IDX</a></li>
                            <li><a href="#faq" @click.prevent="scrollToSection('faq')" class="hover:text-[#85131B]">Pertanyaan umum</a></li>
                        </ul>
                    </div>

                    <!-- Links Column 2 -->
                    <div class="md:col-span-3 space-y-2">
                        <div class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Sumber Resmi</div>
                        <ul class="space-y-2 text-xs text-slate-500">
                            <li><a href="https://www.idx.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-[#85131B]">Bursa Efek Indonesia</a></li>
                            <li><a href="https://www.idx.co.id" target="_blank" rel="noopener noreferrer" class="hover:text-[#85131B]">idx.co.id</a></li>
                            <li><Link :href="route('admin.login')" class="hover:text-[#85131B]">Portal Administrator</Link></li>
                        </ul>
                    </div>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                    <div>
                        &copy; 2026 PT Bursa Efek Indonesia. Agenda ilustratif, bukan jadwal resmi.
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('login')" class="hover:text-slate-600">Portal Peserta</Link>
                        <Link :href="route('admin.login')" class="hover:text-slate-600">Portal Admin</Link>
                    </div>
                </div>
            </div>
        </footer>

        <!-- MODAL DETAIL AGENDA (Interactive pop-up when clicking "Lihat Detail") -->
        <div v-if="selectedModalAgenda" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl p-6 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <span :class="['px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider', selectedModalAgenda.categoryClass]">
                            {{ selectedModalAgenda.category }}
                        </span>
                        <h3 class="text-base font-extrabold text-slate-900 mt-2 leading-snug">
                            {{ selectedModalAgenda.title }}
                        </h3>
                    </div>
                    <button @click="selectedModalAgenda = null" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-2">
                        <span class="w-28 text-slate-400 shrink-0">Waktu Pelaksanaan:</span>
                        <span class="font-bold text-slate-800">{{ selectedModalAgenda.day }} {{ selectedModalAgenda.month }} • {{ selectedModalAgenda.waktu }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-28 text-slate-400 shrink-0">Lokasi / Format:</span>
                        <span class="font-medium text-slate-700">{{ selectedModalAgenda.lokasi }} ({{ selectedModalAgenda.formatType }})</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-28 text-slate-400 shrink-0">Penyelenggara:</span>
                        <span class="font-medium text-slate-700">{{ selectedModalAgenda.penyelenggara }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-28 text-slate-400 shrink-0">Sasaran Peserta:</span>
                        <span class="font-medium text-slate-700">{{ selectedModalAgenda.peserta }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-28 text-slate-400 shrink-0">Deskripsi:</span>
                        <span class="text-slate-600 leading-relaxed">{{ selectedModalAgenda.deskripsi }}</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">
                        Pratinjau desain ilustratif
                    </span>
                    <div class="flex gap-2">
                        <button @click="selectedModalAgenda = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg cursor-pointer">
                            Tutup
                        </button>
                        <Link :href="route('register')" class="px-4 py-2 bg-[#85131B] hover:bg-[#680A10] text-white text-xs font-bold rounded-lg shadow-sm">
                            Daftar di Portal
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
