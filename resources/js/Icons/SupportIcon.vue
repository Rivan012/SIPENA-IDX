<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHeadset } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faHeadset v-bind="$attrs" />
</template>
