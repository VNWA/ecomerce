<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'font-bold block border-l pl-4 -ml-px border-transparent text-purple-400 border-purple-400   text-purple-500  hover:from-purple-700 hover:via-purple-800 hover:to-purple-900 '
        : ' block border-l pl-4 -ml-px border-transparent  hover:from-purple-700 hover:via-purple-800 hover:to-purple-900';
});
</script>

<template>
    <Link :href="href" :class="classes">
    <slot />

    </Link>
</template>
