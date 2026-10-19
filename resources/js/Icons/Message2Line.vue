<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faComment } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faComment v-bind="$attrs" />
</template>
