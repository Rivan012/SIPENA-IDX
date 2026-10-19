<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faInbox } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faInbox v-bind="$attrs" />
</template>
