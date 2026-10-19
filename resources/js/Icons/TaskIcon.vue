<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faListCheck } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faListCheck v-bind="$attrs" />
</template>
