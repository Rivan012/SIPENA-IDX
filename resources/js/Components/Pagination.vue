<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon } from '@/Icons';

defineProps({
    links: {
        type: Array,
        default: () => [],
    },
    from: Number,
    to: Number,
    total: Number,
    itemLabel: {
        type: String,
        default: 'data',
    },
});

const isPrevious = (label) => {
    if (!label) return false;
    return label.includes('Previous') || label.includes('&laquo;') || label.includes('«');
};

const isNext = (label) => {
    if (!label) return false;
    return label.includes('Next') || label.includes('&raquo;') || label.includes('»');
};
</script>

<template>
    <div v-if="links?.length > 3" class="p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <span v-if="total !== undefined" class="text-slate-500">
            Menampilkan {{ from || 0 }} - {{ to || 0 }} dari {{ total || 0 }} {{ itemLabel }}
        </span>
        <div class="flex items-center gap-1">
            <template v-for="(link, i) in links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :title="isPrevious(link.label) ? 'Sebelumnya' : (isNext(link.label) ? 'Selanjutnya' : `Halaman ${link.label}`)"
                    :class="[
                        'min-w-8 h-8 px-2.5 rounded-lg text-xs font-semibold inline-flex items-center justify-center transition-colors shadow-2xs',
                        link.active
                            ? 'bg-[#9F0E0F] text-white font-bold'
                            : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900'
                    ]"
                >
                    <ChevronLeftIcon v-if="isPrevious(link.label)" class="w-4 h-4 shrink-0" />
                    <ChevronRightIcon v-else-if="isNext(link.label)" class="w-4 h-4 shrink-0" />
                    <span v-else v-html="link.label"></span>
                </Link>
                <span
                    v-else
                    :class="[
                        'min-w-8 h-8 px-2.5 rounded-lg text-xs font-medium inline-flex items-center justify-center text-slate-300 border border-transparent select-none'
                    ]"
                >
                    <ChevronLeftIcon v-if="isPrevious(link.label)" class="w-4 h-4 shrink-0 text-slate-300" />
                    <ChevronRightIcon v-else-if="isNext(link.label)" class="w-4 h-4 shrink-0 text-slate-300" />
                    <span v-else v-html="link.label"></span>
                </span>
            </template>
        </div>
    </div>
</template>
