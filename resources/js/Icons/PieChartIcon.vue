<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faChartPie } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faChartPie v-bind="$attrs" />
</template>
