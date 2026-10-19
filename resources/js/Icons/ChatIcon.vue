<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faComments } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faComments v-bind="$attrs" />
</template>
