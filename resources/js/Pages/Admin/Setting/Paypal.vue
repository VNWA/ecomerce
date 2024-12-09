<template>
    <div>
        <AppLayout title="Paypal Config" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Paypal Config</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Setting', route('Setting')], ['  Config', route('Setting')], ['  Paypal Config', route('Setting.Security.FrontendUrls')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5">
                <div class="mb-4">
                    <Card>
                        <form @submit.prevent="update">
                            <div class="grid lg:grid-cols-3 grid-cols-1  gap-4 ">

                                <div>
                                    <InputLabel for="client_id">Client Id <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="client_id" v-model="form.client_id" type="text"
                                        class="mt-1 block w-full" required />
                                    <span v-if="errors.client_id" class="text-red-500">
                                        {{ errors.client_id }}
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
                                    <InputLabel for="webhook_id">Webhook Key <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="webhook_id" v-model="form.webhook_id" type="text"
                                        class="mt-1 block w-full" required />
                                    <span v-if="errors.webhook_id" class="text-red-500">
                                        {{ errors.webhook_id }}
                                    </span>
                                </div>
                                <div>
                                    <InputLabel for="mode">Mode <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <Select :options="modes" v-model="form.mode" required class="mt-1" />
                                    <span v-if="errors.mode" class="text-red-500">
                                        {{ errors.mode }}
                                    </span>
                                </div>
                                <div>
                                    <InputLabel for="currency">currency <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="currency" v-model="form.currency" type="text"
                                        class="mt-1 block w-full" required />
                                    <span v-if="errors.currency" class="text-red-500">
                                        {{ errors.currency }}
                                    </span>
                                </div>
                                <div>
                                    <InputLabel for="locale">Locale <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="locale" v-model="form.locale" type="text" class="mt-1 block w-full"
                                        required />
                                    <span v-if="errors.locale" class="text-red-500">
                                        {{ errors.locale }}
                                    </span>
                                </div>
                                <div>
                                    <InputLabel for="app_id">App Id <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputText id="app_id" v-model="form.app_id" type="text" class="mt-1 block w-full"
                                        required />
                                    <span v-if="errors.app_id" class="text-red-500">
                                        {{ errors.app_id }}
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
import Select from '@/Components/Select.vue';

const modes = ref([
    {
        name: 'Sandbox',
        value: 'sandbox',
    },
    {
        name: 'Live',
        value: 'live',
    },
])


const isPageLoading = ref(false);

const props = defineProps({
    data: Object
})
const form = reactive({
    mode: '',
    app_id: '',
    currency: '',
    locale: '',
    secret_key: '',
    client_id: '',
    webhook_id: '',

})
const errors = ref({})
const validateForm = () => {
    errors.value = {}; // Reset errors
    let isValid = true;

    if (!form.client_id) {
        errors.value.client_id = 'Client ID is required.';
        isValid = false;
    }
    if (!form.secret_key) {
        errors.value.secret_key = 'Secret Key is required.';
        isValid = false;
    }
    if (!form.webhook_id) {
        errors.value.webhook_id = 'Webhook Key is required.';
        isValid = false;
    }
    if (!form.mode) {
        errors.value.mode = 'Mode is required.';
        isValid = false;
    }
    if (!form.currency) {
        errors.value.currency = 'Currency is required.';
        isValid = false;
    }
    if (!form.locale) {
        errors.value.locale = 'Locale is required.';
        isValid = false;
    }
    if (!form.app_id) {
        errors.value.app_id = 'App ID is required.';
        isValid = false;
    }
    return isValid;
};

onMounted(() => {
    form.mode = props.data.mode
    form.app_id = props.data.app_id
    form.currency = props.data.currency
    form.locale = props.data.locale
    form.secret_key = props.data.secret_key
    form.client_id = props.data.client_id
    form.webhook_id = props.data.webhook_id

})
const update = () => {
    if (!validateForm()) return; // Nếu không hợp lệ, không gửi yêu cầu
    const vnwa = useForm(form);
    vnwa.post(route('Setting.Config.Paypal.Update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Updata Paypal Config Success');
        },
    });
};


</script>
