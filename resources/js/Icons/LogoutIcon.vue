<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faRightFromBracket } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
  <FontAwesomeIcon :icon=faRightFromBracket v-bind="$attrs" />
</template>
