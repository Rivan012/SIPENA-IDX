<script setup>
// ponytail: toggle visual 2-state ID/EN sync via v-model + localStorage. upgrade ke vue-i18n jika seluruh app butuh i18n.
const lang = defineModel({
    default: () => {
        try {
            return localStorage.getItem('sipena-lang') || 'id';
        } catch {
            return 'id';
        }
    },
});

const toggleLang = () => {
    lang.value = lang.value === 'id' ? 'en' : 'id';
    document.documentElement.lang = lang.value;
    try {
        localStorage.setItem('sipena-lang', lang.value);
    } catch {}
};
</script>

<template>
    <button
        type="button"
        @click="toggleLang"
        class="inline-flex h-10 items-center rounded-lg border border-slate-300 bg-white p-1 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#85131B]"
        :aria-label="lang === 'id' ? 'Ganti ke Bahasa Inggris' : 'Switch to Indonesian'"
        :title="lang === 'id' ? 'Bahasa Indonesia (Klik untuk English)' : 'English (Click for Indonesian)'"
    >
        <span
            :class="[
                'rounded-md px-2 py-1 transition-all',
                lang === 'id' ? 'bg-[#85131B] text-white shadow-xs' : 'text-slate-500'
            ]"
        >ID</span>
        <span
            :class="[
                'rounded-md px-2 py-1 transition-all',
                lang === 'en' ? 'bg-[#85131B] text-white shadow-xs' : 'text-slate-500'
            ]"
        >EN</span>
    </button>
</template>
