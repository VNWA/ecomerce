<template>
    <div>
        <AppLayout title="Frontend Urls" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Frontend Urls</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Setting', route('Setting')], ['  Security', route('Setting')], ['  Frontend Urls', route('Setting.Security.FrontendUrls')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5">
                <div class="mb-4">
                    <Card title="Frontend Urls">
                        <form @submit.prevent="update">
                            <TextArea required v-model="content" />
                            <div class="mt-3 flex justify-center">
                                <Button icon="fa6-solid:floppy-disk" type="submit" text="save" />
                            </div>
                        </form>
                    </Card>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';

import Card from '@/Components/Card.vue';
import TextArea from '@/Components/TextArea.vue';
import Button from '@/Components/Button.vue';
import { useForm } from '@inertiajs/vue3';

const isPageLoading = ref(false);

const props = defineProps({
    data: String
})
const content = ref('');
onMounted(() => {
    content.value = props.data
})
const update = () => {
    const form = useForm({ content: content.value });
    form.post(route('Setting.Security.FrontendUrls.Update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Updata Frontend Urls Success');
        },
    });
};


</script>
