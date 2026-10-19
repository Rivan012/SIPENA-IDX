<script setup>
defineProps({
    html: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="rich-text-content" v-html="html"></div>
</template>
