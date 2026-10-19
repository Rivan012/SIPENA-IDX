<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faFlag } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faFlag v-bind="$attrs" />
</template>
