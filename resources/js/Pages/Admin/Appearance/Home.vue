<template>
    <div>
        <AppLayout title="Edit Home Sections" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Home Sections
                        </h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Appearance', route('Appearance')], [' Edit Footer', route('Appearance.Footer')]]" />
                    </div>
                </div>
            </template>
            <div class="p-5 text-black">
                <div class="mb-4">
                    <div class="bg-white shadow-black p-3 mb-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-medium text-black/80">Home Products </h3>
                            <button @click="updateData()"
                                class="px-5 py-2 bg-purple-500 text-white font-bold rounded-md">Save
                                Update</button>
                        </div>
                        <div class="border p-3">
                            <div class="grid grid-cols-12 gap-6">
                                <div class="col-span-8">
                                    <div class="mb-4">
                                        <InputLabel for="name">Name <span class="text-red-500">*</span></InputLabel>
                                        <TextInput id="name" v-model="form.SectionProduct.name" type="text"
                                            class="mt-1 block w-full" />
                                    </div>
                                    <div class="mb-4">
                                        <InputLabel for="desc">Desciption </InputLabel>
                                        <textarea id="desc" v-model="form.SectionProduct.desc"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            rows="5"></textarea>
                                    </div>
                                    <div>
                                        <InputLabel for="productId">Products </InputLabel>

                                        <Multiselect v-model="form.SectionProduct.products" mode="tags"
                                            :close-on-select="false" :searchable="true" :create-option="true"
                                            :options="productOptions" />
                                    </div>
                                </div>
                                <div class="col-span-4">
                                    <div class="bg-white shadow rounded-sm mb-3 p-2">
                                        <div class="py-2 border-b border-black/10 mb-5">
                                            <InputLabel>Image <span class="text-red-500">*</span></InputLabel>

                                        </div>
                                        <div>
                                            <InputUrlImage v-model="form.SectionProduct.image" />

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow-black p-3 mb-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-medium text-black/80">Section Color </h3>
                            <button class="bg-green-600/90 text-white px-5 py-2 rounded" @click="openCreateModal">Create</button>
                        </div>
                        <div class="border p-3">
                            <div class="mb-4">
                                <InputLabel>Name <span class="text-red-500">*</span></InputLabel>
                                <TextInput v-model="form.SectionColor.name" type="text" class="mt-1 block w-full" />
                            </div>
                            <div class="mb-4">
                                <InputLabel>Items <span class="text-red-500">*</span></InputLabel>
                                <DataTable :headers="headers" :items="form.SectionColor.items" buttons-pagination
                                    show-index>
                                    <template #item-actions="{ item, index }">
                                        <button @click="editItem(index -1)" class="text-blue-500 mr-2">Edit </button>
                                        <button @click="deleteItem(index -1)" class="text-red-500">Delete</button>
                                    </template>
                                </DataTable>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <DialogModal :show="isModalOpen" @close="isModalOpen = false">
                <template #title>
                    <h3 class="text-lg font-medium">{{ isEditing ? 'Edit Item' : 'Create Item' }}</h3>
                </template>

                <template #content>
                    <div class="p-4">
                        <div class="mb-4">
                            <InputLabel>Name <span class="text-red-500">*</span></InputLabel>
                            <TextInput v-model="itemForm.name" type="text" class="mt-1 block w-full" />
                        </div>

                        <div class="mb-4">
                            <InputLabel>Description</InputLabel>
                            <MiniEditor v-model="itemForm.desc" type="text" class="mt-1 block w-full" />
                        </div>

                        <div class="mb-4">
                            <InputLabel>Image</InputLabel>
                            <InputUrlImage v-model="itemForm.image" />
                        </div>

                        <div class="mb-4">
                            <InputLabel>Link</InputLabel>
                            <TextInput v-model="itemForm.link" type="text" class="mt-1 block w-full" />
                        </div>
                    </div>
                </template>

                <template #footer>
                    <button @click="saveItem" class="bg-blue-600 text-white px-3 py-1 rounded mr-2">Save</button>
                    <button @click="closeModal" class="bg-gray-500 text-white px-3 py-1 rounded">Cancel</button>
                </template>
            </DialogModal>
        </AppLayout>
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>

<script setup>
import Multiselect from '@vueform/multiselect';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, reactive, onMounted } from 'vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Editor from '@/Components/Editor.vue';
import InputText from '@/Components/Input/InputText.vue';
import TextInput from '@/Components/TextInput.vue';
import InputUrlImage from '@/Components/Input/InputUrlImage.vue';
import DialogModal from '@/Components/DialogModal.vue';
import MiniEditor from '@/Components/MiniEditor.vue';

const isPageLoading = ref(false);
const isModalOpen = ref(false);
const isEditing = ref(false);
const itemIndex = ref(null);

const form = reactive({
    SectionProduct: {
        name: '',
        desc: '',
        image: '',
        products: []
    },
    SectionColor: {
        name: '',
        items: []
    }
});

const itemForm = reactive({
    name: '',
    desc: '',
    image: '',
    link: ''
});
const productOptions = ref([]);
const headers = [
    { text: "Name", value: "name" },
    { text: "Image", value: "image" },
    { text: "Description", value: "desc" },
    { text: "Link", value: "link" },
    { text: 'Actions', value: 'actions' }

];

const loadData = async () => {
    try {
        isPageLoading.value = true;
        const response = await axios.get('/vnwa/appearance/home/load-json-data');
        Object.assign(form, response.data.data);
        productOptions.value = response.data.products || [];
    } catch (error) {
        console.error('Error loading data:', error);
    } finally {
        isPageLoading.value = false;
    }
};
onMounted(loadData);

const updateData = () => {
    axios.post('/vnwa/appearance/home/update', {
        SectionProduct: form.SectionProduct,
        SectionColor: form.SectionColor
    })
        .then(response => {
            toast.success(response.data.message, { autoClose: 1500 });
        })
        .catch(error => {
            toast.error(error.message, { autoClose: 1500 });
        });
};

const openCreateModal = () => {
    resetItemForm();
    isEditing.value = false;
    isModalOpen.value = true;
};

const editItem = (index) => {
    Object.assign(itemForm, form.SectionColor.items[index]);
    itemIndex.value = index;
    isEditing.value = true;
    isModalOpen.value = true;
};

const saveItem = () => {
    if (isEditing.value) {
        form.SectionColor.items[itemIndex.value] = { ...itemForm };
    } else {
        form.SectionColor.items.push({ ...itemForm });
    }
    closeModal();
};

const deleteItem = (index) => {
    form.SectionColor.items.splice(index, 1);
};

const closeModal = () => {
    isModalOpen.value = false;
    resetItemForm();
};

const resetItemForm = () => {
    Object.assign(itemForm, { name: '', desc: '', image: '', link: '' });
};
</script>
<style></style>
