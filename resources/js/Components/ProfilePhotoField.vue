<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    modelValue: {
        type: File,
        default: null,
    },
    currentUrl: {
        type: String,
        default: null,
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);
const previewUrl = ref(null);
let previewObjectUrl = null;

watch(() => [props.modelValue, props.currentUrl], ([file, currentUrl]) => {
    if (previewObjectUrl) {
        URL.revokeObjectURL(previewObjectUrl);
        previewObjectUrl = null;
    }

    if (file) {
        previewObjectUrl = URL.createObjectURL(file);
        previewUrl.value = previewObjectUrl;
        return;
    }

    previewUrl.value = currentUrl;
}, { immediate: true });

onBeforeUnmount(() => {
    if (previewObjectUrl) {
        URL.revokeObjectURL(previewObjectUrl);
    }
});

const updatePhoto = (event) => {
    emit('update:modelValue', event.target.files?.[0] ?? null);
};
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <img
            v-if="previewUrl"
            :src="previewUrl"
            alt="Pratinjau foto profil"
            class="h-20 w-20 shrink-0 rounded-full border border-slate-200 object-cover"
        />
        <div v-else class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full border border-dashed border-slate-300 bg-slate-50 text-center text-[10px] font-semibold text-slate-500">
            Belum ada foto
        </div>

        <div class="min-w-0 flex-1">
            <label :for="id" class="mb-1 block text-xs font-bold text-slate-700">Foto Profil</label>
            <input
                :id="id"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="min-h-11 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
                @change="updatePhoto"
            />
            <p class="mt-1 text-[11px] text-slate-500">JPG, PNG, atau WebP, maksimal 5 MB.</p>
            <p v-if="error" class="mt-1 text-[11px] text-red-600" role="alert">{{ error }}</p>
        </div>
    </div>
</template>
