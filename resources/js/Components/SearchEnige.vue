<template>
    <div>
        <div class="bg-black/80 text-white p-2 w-full">
            <div>{{ $page.props.frontend_url }}/{{ props.slug }} </div>
            <div>
                {{ computedMetaTitle }}
            </div>
            <div>
                {{ computedMetaDesc }}
            </div>
            <div>
                {{ computedMetaImage }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAdminStore } from '@/store/admin';


const adminStore = useAdminStore();

const props = defineProps({

    slug: String,
    metaTitle: String,
    metaDesc: String,
    metaImage: String,
})

const computedMetaTitle = computed(() => props.metaTitle || 'Undefine ');
const computedMetaDesc = computed(() => props.metaDesc || 'Undefine');
const computedMetaImage = computed(() => props.metaImage || 'Undefine');
</script>

<style></style>
