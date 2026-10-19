<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SortableTh from '@/Components/SortableTh.vue';
import { confirmDialog } from '@/Utils/sweetalert';
import { PdfIcon } from '@/Icons';

const props = defineProps({
    approvalItems: Object,
    stats: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');
const currentKategori = ref(props.filters?.kategori || '');
const currentSort = ref(props.filters?.sort || '');
const currentDirection = ref(props.filters?.direction || 'asc');

const exportPdfUrl = computed(() => {
    return route('admin.approval.export', {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    });
});

const handleFilter = () => {
    router.get(route('admin.approval.index'), {
        search: search.value || undefined,
        status: currentStatus.value || undefined,
        kategori: currentKategori.value || undefined,
        sort: currentSort.value || undefined,
        direction: currentDirection.value || undefined,
    }, { preserveState: true });
};

const handleSort = (field) => {
    if (currentSort.value === field) {
        currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.value = field;
        currentDirection.value = 'asc';
    }
    handleFilter();
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

const handleStatusTransition = async (item, nextStatus, actionLabel) => {
    const statusText = nextStatus === 'dipublikasikan'
        ? 'Terjadwal'
        : nextStatus === 'berlangsung'
            ? 'Berlangsung'
            : 'Selesai';

    const ok = await confirmDialog({
        title: `${actionLabel} Kegiatan?`,
        text: `Ubah status "${item.nama_kegiatan}" menjadi ${statusText}?`,
        confirmButtonText: `Ya, ${actionLabel}`,
        cancelButtonText: 'Batal',
        icon: 'question',
        confirmButtonColor: nextStatus === 'dipublikasikan'
            ? '#0284C7'
            : nextStatus === 'berlangsung'
                ? '#E11D48'
                : '#059669',
    });

    if (ok) {
        router.patch(route('admin.approval.kegiatan.status', item.source_id), {
            status: nextStatus,
        });
    }
};
</script>

<template>
    <AdminLayout title="Approval Proposal Mitra — SIPENA IDX">
        <Head title="Approval & Persetujuan" />

        <div class="space-y-5">
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Approval Proposal Mitra</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Verifikasi proposal kegiatan dan catatan revisinya.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="exportPdfUrl"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-xs inline-flex items-center gap-1.5">
                        <PdfIcon class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                        <span>Ekspor PDF</span>
                    </a>
                </div>
            </div>

            <!-- Approval metrics -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL PROPOSAL</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ stats.total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Diajukan mitra</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MENUNGGU REVIEW</span>
                    <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ stats.pending_total }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Perlu ditinjau</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TELAH DISETUJUI</span>
                    <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ stats.disetujui }}</span>
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
                              placeholder="Cari nama kegiatan atau mitra..."
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
                        <option value="dipublikasikan">Terjadwal</option>
                        <option value="berlangsung">Berlangsung</option>
                        <option value="selesai">Selesai</option>
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
                                <SortableTh field="nama_kegiatan" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    PROPOSAL KEGIATAN
                                </SortableTh>
                                <SortableTh field="mitra" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    PENGUSUL
                                </SortableTh>
                                <SortableTh field="jenis_kegiatan" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    KATEGORI
                                </SortableTh>
                                <SortableTh field="created_at" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    TGL PENGAJUAN
                                </SortableTh>
                                <SortableTh field="status" align="center" :current-sort="currentSort" :current-direction="currentDirection" @sort="handleSort">
                                    STATUS
                                </SortableTh>
                                <th class="py-3 px-4 text-right">AKSI KEPUTUSAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in approvalItems.data" :key="item.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <Link :href="route('admin.kegiatan.show', item.source_id)"
                                          class="font-bold text-slate-900 hover:text-[#680A10] leading-snug">
                                        {{ item.nama_kegiatan }}
                                    </Link>
                                    <a v-if="item.proposal_file_path"
                                       :href="route('admin.kegiatan.proposal.download', item.source_id)"
                                       class="block text-[10px] text-[#680A10] hover:underline mt-1">Lihat PDF</a>
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
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', item.status_class]">
                                        {{ item.status_label }}
                                    </span>
                                    <p v-if="item.review_note" class="text-[10px] text-slate-500 mt-1 max-w-48 whitespace-normal">{{ item.review_note }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <template v-if="item.can_review">
                                            <button type="button" @click="openMakerDecision(item, 'disetujui')"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs cursor-pointer">
                                                Setujui
                                            </button>
                                            <button type="button" @click="openMakerDecision(item, 'revisi')"
                                                    class="px-2.5 py-1 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-[11px] cursor-pointer">
                                                Revisi
                                            </button>
                                            <button type="button" @click="openMakerDecision(item, 'ditolak')"
                                                    class="px-2.5 py-1 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-red-700 font-semibold text-[11px] cursor-pointer">
                                                Tolak
                                            </button>
                                        </template>

                                        <button v-else-if="item.status === 'disetujui'"
                                                type="button"
                                                @click="handleStatusTransition(item, 'dipublikasikan', 'Jadwalkan')"
                                                class="px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold text-[11px] shadow-xs cursor-pointer">
                                            Jadwalkan
                                        </button>

                                        <button v-else-if="item.status === 'dipublikasikan'"
                                                type="button"
                                                @click="handleStatusTransition(item, 'berlangsung', 'Mulai Berlangsung')"
                                                class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-[11px] shadow-xs cursor-pointer">
                                            Berlangsung
                                        </button>

                                        <button v-else-if="item.status === 'berlangsung'"
                                                type="button"
                                                @click="handleStatusTransition(item, 'selesai', 'Selesaikan')"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-[11px] shadow-xs cursor-pointer">
                                            Selesai
                                        </button>

                                        <form v-if="['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'].includes(item.status)"
                                              method="get"
                                              :action="route('admin.kegiatan.presensi.download', item.source_id)"
                                              target="_blank"
                                              class="inline-flex items-center gap-1">
                                            <select name="orientation"
                                                    class="px-2 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 font-medium text-[11px] focus:outline-none cursor-pointer">
                                                <option value="portrait">Portrait</option>
                                                <option value="landscape">Landscape</option>
                                            </select>
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-semibold text-[11px] shadow-2xs cursor-pointer"
                                                    title="Cetak Presensi">
                                                Cetak Presensi
                                            </button>
                                        </form>

                                        <Link :href="route('admin.kegiatan.show', item.source_id)"
                                              class="px-2 py-1 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 text-[11px] font-medium" title="Detail Verifikasi">
                                            Detail
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!approvalItems.data?.length">
                                <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
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
