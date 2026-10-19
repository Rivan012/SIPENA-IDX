<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faChevronDown } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faChevronDown v-bind="$attrs" />
</template>
