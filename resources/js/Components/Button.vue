<script setup>
defineProps({
    text: String,
    icon: {
        type: String,
        default: ''
    },
});
</script>

<template>
    <button
        class="flex-items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold">
        <Icon :icon="icon" v-if="icon" /> {{ text }}
    </button>

</template>
