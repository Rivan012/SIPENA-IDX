<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faBoxesStacked } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faBoxesStacked v-bind="$attrs" />
</template>
