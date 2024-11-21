<template>
    <div>
        <AppLayout title="Edit Logo" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Logo
                        </h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Appearance', route('Appearance')], [' Edit Logo', route('Appearance.Logo')]]" />
                    </div>
                </div>
            </template>
            <div class="p-5">

                <div class="my-3 bg-white p-5 shadow shadow-black">
                    <div class=" flex items-center justify-between w-full">
                        <div class="mb-4">
                            <InputLabel for="name">Logo Full <span class="text-red-500">*</span></InputLabel>

                            <InputUrlImage v-model="data.logo_full">
                                <template #desc>
                                    <h5>
                                        Type: webp, avif, svg, png
                                    </h5>
                                    <h5>
                                        Size: 251 × 35 px
                                    </h5>
                                    <h5>
                                        Intrinsic aspect ratio: 50∶7
                                    </h5>
                                </template>
                            </InputUrlImage>
                        </div>
                        <div class="mb-4">
                            <InputLabel for="name">Logo Icon <span class="text-red-500">*</span></InputLabel>
                            <InputUrlImage v-model="data.logo_icon">
                                <template #desc>
                                    <h5>
                                        Type: webp, avif, svg, png
                                    </h5>
                                    <h5>
                                        Size: 150 × 150 px
                                    </h5>
                                    <h5>
                                        Intrinsic aspect ratio: 1:1
                                    </h5>
                                </template>
                            </InputUrlImage>
                        </div>
                        <div class="mb-4">
                            <InputLabel for="name">Favicon <span class="text-red-500">* </span></InputLabel>
                            <InputUrlImage v-model="data.favicon">
                                <template #desc>
                                    <h5>
                                        Type: .ico
                                    </h5>

                                </template>
                            </InputUrlImage>
                        </div>
                    </div>

                    <div class="mt-6 w-full text-center">
                        <button @click="updateData"
                            class="bg-purple-500  hover:bg-purple-900 hover:text-white  py-2 px-5 rounded-sm text-white text-nowrap ms-3 ">
                            <Icon icon="fa6-solid:save" />
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import InputSelectedIcon from '@/Components/Input/InputSelectedIcon.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import InputUrlImage from '@/Components/Input/InputUrlImage.vue';
import InputDesc from '@/Components/Input/InputDesc.vue';

const isPageLoading = ref(false);

const data = ref({
    logo_full: '',
    logo_icon: '',
    favicon: ''
});
const loadData = () => {
    isPageLoading.value = true;
    axios.get('/vnwa/appearance/logo/load-json-data')
        .then(response => {
            data.value = response.data
            isPageLoading.value = false;
        })
        .catch(error => {
            console.log(error);
            isPageLoading.value = false;
        });

}
loadData();

const updateData = () => {
    axios.post('/vnwa/appearance/logo/update', { data: data.value })
        .then(response => {
            toast.success(response.data.message, {
                autoClose: 1500,
            });
        })
        .catch(error => {
            toast.error(error.message, {
                autoClose: 1500,
            });
        });
}




</script>
<style></style>
