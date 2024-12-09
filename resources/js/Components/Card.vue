<template>
    <div class="bg-white shadow rounded-sm p-2 text-black/80">
        <div class="py-2 border-b border-black/10 mb-5 capitalize" v-if="title">
            <h2 class="text-lg font-bold">
                {{ title }}
            </h2>
        </div>

        <slot />
    </div>
</template>

<script lang="ts" setup>
defineProps({
    title: {
        type: String,
        default: ''
    }
})
</script>

<style></style>
