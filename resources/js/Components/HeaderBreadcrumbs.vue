<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({

    breadcrumbs: {
        type: Object,
        default: () => { },
    },
});
</script>
<template>
    <div>

        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <Link :href="route('dashboard')" class="inline-flex cursor-pointer items-center text-sm font-medium text-gray-700   hover:text-black">
                    <Icon icon="fa6-solid:building" class="me-1" />
                    Home
                    </Link>

                </li>
                <li v-for="(item, index) in breadcrumbs" :key="index" class="">
                    <Link :href="item[1]" :class="{ 'text-purple-600  capitalize  hover:text-purple-600 font-semibold': index === breadcrumbs.length - 1 }" class="flex cursor-pointer items-center text-sm font-medium text-gray-700  hover:text-black">
                    <Icon icon="fa6-solid:chevron-right" class="me-2" />
                    {{ item[0] }}
                    </Link>
                </li>

            </ol>
        </nav>
    </div>
</template>



<style></style>
