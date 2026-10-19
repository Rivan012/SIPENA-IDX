<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTableCells } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faTableCells v-bind="$attrs" />
</template>
