<template>
    <div>
        <AppLayout title="Create New Coupon" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Coupon</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Coupon', route('Ecommerce.Coupon')], ['Create', route('Ecommerce.Coupon.Create')]]" />
                    </div>
                </div>
            </template>
            <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-12 gap-4 p-2">
                    <div class="lg:col-span-8 col-span-12">
                        <div class="bg-white shadow rounded-sm mb-3 p-2">
                            <div class="mb-4">
                                <InputLabel for="code">Code <span class="text-red-500">*</span></InputLabel>
                                <TextInput id="code" v-model="form.code" type="text" class="mt-1 block w-full"
                                    required />
                                <InputError class="mt-2" :message="errors.code" />
                            </div>
                            <div class="mb-4">
                                <InputLabel for="qnt">Quanity <span class="text-red-500">*</span>
                                </InputLabel>
                                <TextInput type="number" :min="0" v-model="form.qnt" />

                                <InputError class="mt-2" :message="errors.qnt" />
                            </div>
                            <div class="mb-4 text-black/70">
                                <InputLabel for="discount_type">Discount Type</InputLabel>
                                <select id="discount_type" v-model="form.type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option :value="null" disabled>Select Discount Type</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="amount">Amount</option>
                                </select>
                                <InputError class="mt-2" :message="errors.type" />
                            </div>

                            <div class="mb-4" v-if="form.type == 'amount'">
                                <InputLabel for="discount_price">Discount Amount <span class="text-red-500">*</span>
                                </InputLabel>
                                <InputPrice id="discount_price" required v-model="form.value" class="w-full" />
                                <InputError class="mt-2" :message="errors.value" />
                            </div>

                            <div class="mb-4" v-else-if="form.type == 'percentage'">
                                <InputLabel for="discount_price">Percentage Discount <span class="text-red-500">*</span>
                                </InputLabel>
                                <input maxlength="100" id="discount_price" v-model="form.value" type="number"
                                    placeholder="Enter discount percentage" max="100"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required />
                                <InputError class="mt-2" :message="errors.value" />
                            </div>

                            <div class="mb-3">
                                <InputLabel for="is_duration"
                                    class="border py-4 w-full flex items-center justify-start gap-4 px-3">
                                    <span class="me-4">Is Duration</span>
                                    <input type="checkbox" id="is_duration"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                        v-model="form.is_duration" :true-value="1" :false-value="0" />
                                </InputLabel>
                            </div>

                            <div v-if="form.is_duration == 1">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mb-3">
                                        <InputLabel for="start_time">Start Time <span class="text-red-500">*</span>
                                        </InputLabel>
                                        <TextInput type="datetime-local" id="start_time" v-model="form.start_time"
                                            @change="onStartTimeChange" required />
                                        <InputError class="mt-2" :message="errors.start_time" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel for="end_time">End Time <span class="text-red-500">*</span>
                                        </InputLabel>
                                        <TextInput type="datetime-local" id="end_time" v-model="form.end_time"
                                            :min="form.start_time" required />
                                        <InputError class="mt-2" :message="errors.end_time" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4 col-span-12">
                        <div class="flex flex-col">
                            <div class="bg-white shadow rounded-sm mb-3 p-2 sticky top-0">
                                <div class="py-2 border-b border-black/10 mb-5 text-black">
                                    <h2 class="text-lg font-bold">Publish</h2>
                                </div>
                                <div class="flex items-center justify-end gap-4">
                                    <button type="submit"
                                        class="flex items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold">
                                        <Icon icon="fa6-solid:floppy-disk"  /> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import { ref, reactive, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import InputPrice from '@/Components/Input/InputPrice.vue';
import TextInput from '@/Components/TextInput.vue';

const isPageLoading = ref(false);
const form = reactive({
    code: '',
    qnt: 1000,
    type: 'percentage',
    value: 0,
    is_duration: 0,
    start_time: '',
    end_time: '',
});
const errors = reactive({
    code: '',
    qnt: '',
    type: '',
    value: '',
    start_time: '',
    end_time: '',
});

const clearError = () => {
    for (const key in errors) {
        if (errors.hasOwnProperty(key)) {
            errors[key] = '';
        }
    }
};

const handleSubmit = () => {
    clearError();
    isPageLoading.value = true;
    axios.post(route('Ecommerce.Coupon.Store'), form).then(res => {

        toast.success(res.data.message);


    }).catch(error => {
        if (error.response.data.errors) {
            const errorKeys = Object.keys(error.response.data.errors);
            errorKeys.forEach(key => {
                if (key in errors) {
                    errors[key] = error.response.data.errors[key][0];
                }
            });
            toast.error(error.message);

        }
    }).finally(() => {
        isPageLoading.value = false;
    });
};

const onStartTimeChange = () => {
    form.end_time = null;
};

watch(() => form.start_time, (newStartTime) => {
    if (newStartTime && form.end_time && new Date(form.end_time) <= new Date(newStartTime)) {
        form.end_time = '';
    }
});
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>
