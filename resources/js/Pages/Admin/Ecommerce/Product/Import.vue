<template>
    <div>
        <AppLayout title=" Import Products" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Import Products</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[
                            ['Product', route('Ecommerce.Product')],
                            ['Import ', route('Ecommerce.Product.Import')]
                        ]" />
                    </div>
                </div>
            </template>

            <div class="p-5">
                <div class="bg-white shadow rounded-sm mb-3 p-5 text-black/80">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <a target="_blank" class="px-3 py-2 bg-green-600 text-white rounded-lg"
                                :href="route('Ecommerce.Product.ExportTemplate')">Download Template</a>
                            <Link :href="route('Ecommerce.Product.Convert')">
                            <button class="px-3 py-2 bg-purple-500 text-white">Convert</button>
                            </Link>
                        </div>
                        <div>
                            <form @submit.prevent="uploadFile"
                                class="flex items-center justify-center gap-5 border px-3 py-3">
                                <input type="file" ref="file" class="border" accept=".xlsx" required />
                                <button class="px-3 py-2 bg-blue-500 text-white" type="submit">Import File</button>
                            </form>
                        </div>
                    </div>





                </div>
                <div class="hidden bg-white shadow rounded-sm mb-3 p-5 text-black/80">
                    <div class="flex items-center justify-between">
                        <div>
                            <!-- <button :disabled="itemsSelected.length === 0"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                            :class="{ 'bg-red-600/60 hover:bg-red-600/60': itemsSelected.length === 0 }"
                            @click="remove">
                            <Icon icon="fa6-solid:x" class="mr-1" /> Clear data selection
                        </button> -->
                        </div>
                        <div>
                            <button @click="importData" class="px-3 py-2 bg-pink-500 text-white">Import</button>
                        </div>
                    </div>
                    <div>
                        <DataTable :headers="headers" :items="table" :isTableLoading="isTableLoading" buttons-pagination
                            show-index alternating border-cell>
                            <template #item-profile="{ name, origin, sku, stock, brand_name }">
                                <div class="w-36">
                                    <ul>
                                        <li>Name: {{ name }}</li>
                                        <li>SKU: {{ sku }}</li>
                                        <li>Origin: {{ origin }}</li>
                                        <li>Stock: {{ stock }}</li>
                                        <li>Brand: {{ brand_name }}</li>
                                    </ul>
                                </div>

                            </template>
                            <template #item-images="{ images }">
                                <div class="w-40 py-4">
                                    <ul class="flex items-center justify-start gap-4 flex-wrap">
                                        <li v-for="(item, index) in images" :key="index"> <img width="50" :src="item"
                                                alt=""> </li>
                                    </ul>
                                </div>

                            </template>
                            <template #item-description="{ description }">
                                <div class="max-w-96 p-3 max-h-96 overflow-y-scroll">
                                    <div v-html="description"></div>
                                </div>

                            </template>
                            <template #item-tech_info="{ tech_info }">
                                <div class="max-w-96 p-3 max-h-96 overflow-y-scroll">
                                    <div v-html="tech_info"></div>
                                </div>

                            </template>
                        </DataTable>
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
const isPageLoading = ref(false)
const file = ref(null);
const table = ref([]);

const uploadFile = async () => {
    isPageLoading.value = true;
    const formData = new FormData();
    formData.append('file', file.value.files[0]);

    try {
        const response = await axios.post('/vnwa/ecommerce/products/excel/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        isPageLoading.value = false;
        toast.success("Uloads sản phẩm thành công", {
            autoClose: 1500,
        });
    } catch (error) {
        console.error('Error uploading file:', error);
    }
};
const isTableLoading = ref(false);
const isModalDelete = ref(false);
const itemsSelected = ref([]);

const headers = [
    { text: "Profile", value: "profile", sortable: true },
    { text: "Categories", value: "categories_name" },
    { text: "Desction", value: "description" },
    { text: "Tech Info", value: "tech_info" },
    { text: "Avatar", value: "images", width: 200 },
    { text: "Catalogs", value: "catalogs", width: 200 },
];
const deleteItem = (index) => {
    table.value.splice(index, 1); // Xóa phần tử ở chỉ số tương ứng
};

const remove = () => {
    // Duyệt qua tất cả các phần tử đã chọn
    itemsSelected.value.forEach((selectedItem) => {
        const index = table.value.indexOf(selectedItem);
        console.log(index + 1);
        // if (index !== -1) {
        //     deleteItem(index); // Xóa mục theo chỉ số
        // }
    });
};

const importData = () => {
    table.forEach(element => {
        ax
    });
}
</script>
