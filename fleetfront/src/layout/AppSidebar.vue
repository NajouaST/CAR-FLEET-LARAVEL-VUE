<script setup>
import AppMenu from './AppMenu.vue';

defineProps({
    permissions: Object
});
</script>

<template>
    <div class="layout-sidebar">
        <app-menu :permissions="permissions"></app-menu>
    </div>
</template>

<style lang="scss" scoped></style>
