<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faUsers } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faUsers v-bind="$attrs" />
</template>
