<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCalendarCheck } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faCalendarCheck v-bind="$attrs" />
</template>
