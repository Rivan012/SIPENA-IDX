<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPaperclip } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faPaperclip v-bind="$attrs" />
</template>
