<script setup>
import { ref } from 'vue';

const theme = ref(document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');

const toggleTheme = () => {
    theme.value = theme.value === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = theme.value;

    try {
        localStorage.setItem('sipena-theme', theme.value);
    } catch {
        return;
    }
};
</script>

<template>
    <button
        type="button"
        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
        :aria-label="theme === 'dark' ? 'Aktifkan tema terang' : 'Aktifkan tema gelap'"
        :aria-pressed="theme === 'dark'"
        :title="theme === 'dark' ? 'Tema terang' : 'Tema gelap'"
        @click="toggleTheme"
    >
        <svg v-if="theme === 'dark'" aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
        </svg>
        <svg v-else aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z" />
        </svg>
    </button>
</template>