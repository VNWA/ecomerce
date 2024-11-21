<template>
    <div>
        <AppLayout title=" Convert Editor">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Convert Editor</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[
                            ['Product', route('Ecommerce.Product')],
                            ['Convert  Editor', route('Ecommerce.Product.Convert')]
                        ]" />
                    </div>
                </div>
            </template>

            <div class="p-5">
                <div class="bg-white shadow rounded-sm mb-3 p-5 text-black/80">
                    <div class="mb-4 flex items-center justify-end">

                    </div>
                    <div class="mb-4 border">
                        <div class="bg-white shadow rounded-sm mb-3 p-2 text-black">

                            <div class="mb-4">
                               <div>
                                <InputLabel for="Brand">Brand : {{ brand_id }}</InputLabel>
                               </div>
                                <select name="brand" id="Brand" v-model="brand_id">
                                    <option v-for="(item, index) in brandsData" :key="index" :value="item.id">{{
                                        item.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="mb-4 border">
                        <div class="bg-white shadow rounded-sm mb-3 p-2">
                            <div class="py-2 border-b border-black/10 mb-5">
                                <h2 class="text-md flex items-center gap-2">Category: {{ categoryIds }}</h2>
                            </div>
                            <div>
                                <CheckBoxTree v-model="categoryIds" :data="categoriesData" />
                            </div>
                        </div>

                    </div>


                    <div class="mb-4 border">
                        <InputLabel for="desc" value="Description" />
                        <Editor id="desc" v-model="data" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class=" bg-white shadow rounded-sm mb-3 p-5 text-black/80">
                    <div class="mb-4 float-end">
                        <button @click="copyData"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-3 flex items-center gap-2">
                            <Icon  icon="fa6-solid:copy"   />
                            Copy String
                        </button>
                    </div>
                    <div>
                        {{ data }}
                    </div>

                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Editor from '@/Components/Editor.vue'
import CheckBoxTree from '@/Components/CheckBoxTree.vue';

import InputLabel from '@/Components/InputLabel.vue';
const categoryIds = ref();
const brand_id = ref(null)
const data = ref('');
const copyData = () => {
    navigator.clipboard.writeText(data.value)
        .then(() => {
            alert('Data copied to clipboard!');
        })
        .catch(err => {
            console.error('Failed to copy: ', err);
        });
};
const categoriesData = ref([]);
const brandsData = ref([]);
const loadDataCategoriesAndBrands = () => {
    axios.get('/vnwa/ecommerce/products/load-data-categories-tree-and-brands').then(response => {
        categoriesData.value = response.data.categories;
        brandsData.value = response.data.brands;
    }).catch(error => {
        console.error(error)
    })
}

onMounted(() => {
    loadDataCategoriesAndBrands();
})

</script>
