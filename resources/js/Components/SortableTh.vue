<script setup>
defineProps({
    field: {
        type: String,
        required: true,
    },
    currentSort: {
        type: String,
        default: '',
    },
    currentDirection: {
        type: String,
        default: 'asc',
    },
    align: {
        type: String,
        default: 'left',
    },
});

defineEmits(['sort']);
</script>

<template>
    <th
        @click="$emit('sort', field)"
        :class="[
            'py-3 px-4 cursor-pointer select-none transition-colors group',
            align === 'center' ? 'text-center' : align === 'right' ? 'text-right' : 'text-left',
            currentSort === field ? 'text-slate-900 bg-slate-100/60' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50/60'
        ]"
    >
        <div :class="['inline-flex items-center gap-1.5', align === 'center' ? 'justify-center' : align === 'right' ? 'justify-end' : 'justify-start']">
            <span><slot /></span>
            <span class="inline-flex text-slate-400 group-hover:text-slate-600">
                <svg v-if="currentSort === field && currentDirection === 'asc'" class="w-3 h-3 text-[#680A10]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <svg v-else-if="currentSort === field && currentDirection === 'desc'" class="w-3 h-3 text-[#680A10]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                <svg v-else class="w-3 h-3 text-slate-300 opacity-60 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                </svg>
            </span>
        </div>
    </th>
</template>
