<template>
    <div>
        <AppLayout title="Stripe Config" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stripe Config</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Setting', route('Setting')], ['  Config', route('Setting')], ['  Stripe Config', route('Setting.Security.FrontendUrls')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5">
                <div class="mb-4">
                    <Card>
                        <form @submit.prevent="update">
                            <div class="grid lg:grid-cols-3 grid-cols-1  gap-4 ">
                                <div>
                                    <InputLabel for="publish_key">Publish Key <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="publish_key" v-model="form.publish_key" type="text"
                                        class="mt-1 block w-full" required />
                                    <span v-if="errors.publish_key" class="text-red-500">
                                        {{ errors.publish_key }}
                                    </span>
                                </div>
                                <div>
                                    <InputLabel for="secret_key">Secret Key <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="secret_key" v-model="form.secret_key" type="text"
                                        class="mt-1 block w-full" required />
                                    <span v-if="errors.secret_key" class="text-red-500">
                                        {{ errors.secret_key }}
                                    </span>
                                </div>
                                <div>
                                    <InputLabel for="webhook_key">Webhook Key <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="webhook_key" v-model="form.webhook_key" type="text"
                                        class="mt-1 block w-full" required />
                                    <span v-if="errors.webhook_key" class="text-red-500">
                                        {{ errors.webhook_key }}
                                    </span>
                                </div>
                            </div>


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
import Button from '@/Components/Button.vue';
import { useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import InputText from '@/Components/Input/InputText.vue';
import InputLabel from '@/Components/InputLabel.vue';

const isPageLoading = ref(false);

const props = defineProps({
    data: Object
})
const form = reactive({
    secret_key: '',
    publish_key: '',
    webhook_key: '',

})
const errors = reactive({
    secret_key: '',
    publish_key: '',
    webhook_key: '',
})
onMounted(() => {
    form.secret_key = props.data.secret_key
    form.publish_key = props.data.publish_key
    form.webhook_key = props.data.webhook_key
})
const update = () => {
    const vnwa = useForm(form);
    vnwa.post(route('Setting.Config.Stripe.Update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Updata Stripe Config Success');
        },
    });
};


</script>
