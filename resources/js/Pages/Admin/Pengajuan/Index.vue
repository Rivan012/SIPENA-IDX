<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    approvalItems: Object,
    stats: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');
const currentKategori = ref(props.filters?.kategori || '');

const handleFilter = () => {
    router.get(route('admin.approval.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
    }, { preserveState: true });
};

// Quick action modal
const showModal = ref(false);
const modalAction = ref('');
const modalTarget = ref(null);

const actionForm = useForm({
    status: '',
    catatan: '',
    catatan_revisi: '',
    terbitkan_kegiatan: true,
});

const openDecision = (item, action) => {
    modalTarget.value = item;
    modalAction.value = action;
    actionForm.status = action;
    actionForm.catatan = '';
    actionForm.catatan_revisi = '';
    showModal.value = true;
};

const submitDecision = () => {
    if (!modalTarget.value) return;
    actionForm.patch(route('admin.pengajuan.status', modalTarget.value.source_id), {
        onFinish: () => {
            showModal.value = false;
            modalTarget.value = null;
        },
    });
};

const showMakerModal = ref(false);
const makerTarget = ref(null);
const makerForm = useForm({ status: '', catatan: '' });

const openMakerDecision = (item, status) => {
    makerTarget.value = item;
    makerForm.status = status;
    makerForm.catatan = '';
    showMakerModal.value = true;
};

const submitMakerDecision = () => {
    if (!makerTarget.value) return;

    makerForm.patch(route('admin.approval.kegiatan.status', makerTarget.value.source_id), {
        onSuccess: () => {
            showMakerModal.value = false;
            makerTarget.value = null;
        },
    });
};
</script>

<template>
    <AdminLayout title="Approval Kegiatan & Anggaran — SIPENA IDX">
        <Head title="Approval & Persetujuan" />

        <div class="space-y-5">
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Approval &amp; Persetujuan Kegiatan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Verifikasi usulan program, tinjau kesesuaian anggaran, dan terbitkan izin pelaksanaan.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="route('admin.pengajuan.export', filters)"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs">
                        &darr; Ekspor CSV
                    </a>
                </div>
            </div>

            <!-- 4 KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MENUNGGU REVIEW</span>
                    <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ stats.pending_total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Mitra dan anggaran</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ANGGARAN DALAM REVIEW</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block truncate" :title="stats.anggaran_review">{{ stats.anggaran_review }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Usulan menunggu putusan</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TELAH DISETUJUI</span>
                    <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ stats.disetujui }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Siap diselenggarakan</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PERLU REVISI / DITOLAK</span>
                    <span class="text-2xl font-bold text-red-600 mt-1 block">{{ stats.perhatian }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Perlu revisi atau ditolak</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-md">
                    <input v-model="search"
                           @keyup.enter="handleFilter"
                           type="text"
                           placeholder="Cari nomor, nama kegiatan, mitra..."
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#680A10]/20" />
                    <button type="button" @click="handleFilter"
                            class="px-4 py-2 bg-[#680A10] text-white text-xs font-semibold rounded-xl hover:bg-[#54080D]">
                        Cari
                    </button>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select v-model="currentStatus" @change="handleFilter"
                            class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu Review</option>
                        <option value="diproses">Sedang Ditinjau</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="revisi">Perlu Revisi</option>
                        <option value="ditolak">Ditolak</option>
                    </select>

                    <select v-model="currentKategori" @change="handleFilter"
                            class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none">
                        <option value="">Semua Kategori</option>
                        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">PROPOSAL / KEGIATAN</th>
                                <th class="py-3 px-4">SUMBER</th>
                                <th class="py-3 px-4">PENGUSUL</th>
                                <th class="py-3 px-4">KATEGORI</th>
                                <th class="py-3 px-4">TGL PENGAJUAN</th>
                                <th class="py-3 px-4 text-right">ANGGARAN</th>
                                <th class="py-3 px-4 text-center">STATUS</th>
                                <th class="py-3 px-4 text-right">AKSI KEPUTUSAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in approvalItems.data" :key="item.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-mono text-[10px] font-bold text-slate-400 block">{{ item.identifier }}</span>
                                    <Link :href="item.source === 'maker' ? route('admin.kegiatan.show', item.source_id) : route('admin.pengajuan.show', item.source_id)"
                                          class="font-bold text-slate-900 hover:text-[#680A10] leading-snug">
                                        {{ item.nama_kegiatan }}
                                    </Link>
                                    <a v-if="item.source === 'maker' && item.proposal_file_path"
                                       :href="route('admin.kegiatan.proposal.download', item.source_id)"
                                       class="block text-[10px] text-[#680A10] hover:underline mt-1">Lihat PDF</a>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 rounded text-[10px] font-semibold',
                                        item.source === 'maker' ? 'bg-blue-50 text-blue-800' : 'bg-slate-100 text-slate-700'
                                    ]">{{ item.source_label }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">{{ item.submitter || item.mitra || '-' }}</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ item.jenis_kegiatan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ item.submitted_at ? new Date(item.submitted_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-slate-900">
                                    {{ item.formatted_anggaran || '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', item.status_class]">
                                        {{ item.status_label }}
                                    </span>
                                    <p v-if="item.review_note" class="text-[10px] text-slate-500 mt-1 max-w-48 whitespace-normal">{{ item.review_note }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <template v-if="item.can_review && item.source === 'maker'">
                                            <button type="button" @click="openMakerDecision(item, 'disetujui')"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs">
                                                Setujui
                                            </button>
                                            <button type="button" @click="openMakerDecision(item, 'revisi')"
                                                    class="px-2 py-1 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-[11px]">
                                                Revisi
                                            </button>
                                            <button type="button" @click="openMakerDecision(item, 'ditolak')"
                                                    class="px-2 py-1 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-red-700 font-semibold text-[11px]">
                                                Tolak
                                            </button>
                                        </template>
                                        <template v-else-if="item.can_review && item.source === 'anggaran'">
                                            <button type="button" @click="openDecision(item, 'disetujui')"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs">
                                                Setujui &check;
                                            </button>
                                            <button type="button" @click="openDecision(item, 'revisi')"
                                                    class="px-2 py-1 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-[11px]">
                                                Revisi
                                            </button>
                                        </template>
                                        <Link :href="item.source === 'maker' ? route('admin.kegiatan.show', item.source_id) : route('admin.pengajuan.show', item.source_id)"
                                              class="px-2 py-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 text-[11px]" title="Detail Verifikasi">
                                            Detail
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!approvalItems.data?.length">
                                <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                    Tidak ada proposal yang cocok dengan filter saat ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :links="approvalItems.links" :from="approvalItems.from" :to="approvalItems.to" :total="approvalItems.total" item-label="approval" />
            </div>
        </div>

        <!-- Decision Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">
                        {{ modalAction === 'disetujui' ? 'Setujui Pengajuan' : 'Minta Revisi Proposal' }}
                    </h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <div class="text-xs text-slate-600 space-y-1">
                    <p>Proposal: <strong>{{ modalTarget?.identifier }}</strong></p>
                    <p>Kegiatan: <strong>{{ modalTarget?.nama_kegiatan }}</strong></p>
                    <p>Alokasi: <strong class="text-emerald-700">{{ modalTarget?.formatted_anggaran }}</strong></p>
                </div>

                <form @submit.prevent="submitDecision" class="space-y-3 pt-2">
                    <div v-if="modalAction === 'disetujui'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Persetujuan (Opsional)</label>
                        <textarea v-model="actionForm.catatan" rows="3" placeholder="Disetujui sesuai alokasi RKAP..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>

                        <label class="flex items-center gap-2 text-xs text-slate-700 mt-2 cursor-pointer">
                            <input v-model="actionForm.terbitkan_kegiatan" type="checkbox" class="rounded text-[#680A10]" />
                            <span>Otomatis publikasikan jadwal kegiatan ke kalender</span>
                        </label>
                    </div>

                    <div v-else>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uraian Catatan Revisi *</label>
                        <textarea v-model="actionForm.catatan_revisi" rows="3" required placeholder="Sebutkan hal yang perlu disesuaikan..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showModal = false" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="actionForm.processing"
                                :class="[
                                    'w-1/2 py-2.5 rounded-xl text-xs font-semibold text-white transition disabled:opacity-50',
                                    modalAction === 'disetujui' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700'
                                ]">
                            {{ actionForm.processing ? 'Memproses...' : 'Kirim Keputusan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showMakerModal" class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ makerForm.status === 'disetujui' ? 'Setujui Proposal Mitra' : makerForm.status === 'revisi' ? 'Minta Revisi Proposal' : 'Tolak Proposal Mitra' }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ makerTarget?.nama_kegiatan }}</p>
                    </div>
                    <button type="button" @click="showMakerModal = false" aria-label="Tutup dialog" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>
                <form @submit.prevent="submitMakerDecision" class="space-y-4">
                    <div>
                        <label for="maker-approval-note" class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan {{ ['revisi', 'ditolak'].includes(makerForm.status) ? '*' : '(opsional)' }}
                        </label>
                        <textarea id="maker-approval-note" v-model="makerForm.catatan" rows="4" :required="['revisi', 'ditolak'].includes(makerForm.status)"
                                  class="w-full px-3 py-2 rounded-md border border-slate-200 text-xs" placeholder="Tuliskan keputusan atau hal yang perlu diperbaiki..."></textarea>
                        <p v-if="makerForm.errors.catatan" class="text-[11px] text-red-600 mt-1">{{ makerForm.errors.catatan }}</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showMakerModal = false" class="px-4 py-2 rounded-md border border-slate-200 text-xs font-semibold">Batal</button>
                        <button type="submit" :disabled="makerForm.processing" class="px-4 py-2 rounded-md bg-blue-700 text-white text-xs font-semibold disabled:opacity-50">
                            {{ makerForm.processing ? 'Menyimpan...' : 'Simpan Keputusan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
