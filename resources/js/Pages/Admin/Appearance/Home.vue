<template>
    <div>
        <AppLayout title="Edit Home Sections" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Home Sections</h2>
                    <HeaderBreadcrumbs :breadcrumbs="breadcrumbs" />
                </div>
            </template>

            <div class="p-5 text-black">
                <!-- Section Product -->
                 <div class="w-full py-3 sticky top-0 left-0 z-10 bg-white shadow mb-3 px-3 flex items-center justify-end">
                    <button @click="updateData()"
                                class="px-5 py-2 bg-purple-500 text-white font-bold rounded-md">Save
                                Update</button>
                 </div>
                 <div class="mb-6">
                    <Card title="Home Products">
                        <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-8">
                            <InputLabel for="section-product-name" value="Name" />
                            <TextInput id="section-product-name" class="block w-full mb-3"
                                v-model="form.SectionProduct.name" required />
                            <InputLabel for="section-product-desc" value="Description" />
                            <MiniEditor id="section-product-desc" v-model="form.SectionProduct.desc" />
                        </div>
                        <div class="col-span-4">
                            <InputLabel for="section-product-image" value="Image" />
                            <InputUrlImage id="section-product-image" v-model="form.SectionProduct.image" />
                        </div>
                        <div class="col-span-12">
                            <div>
                                <InputLabel for="productId">Products </InputLabel>

                                <Multiselect v-model="form.SectionProduct.products" mode="tags" :close-on-select="false"
                                    :searchable="true" :create-option="true" :options="productOptions" />
                            </div>
                        </div>
                    </div>
                    </Card>
                 </div>


                <!-- Section Color -->
                <div class="border rounded-md shadow-md p-5 mb-6">
                    <h3 class="font-bold text-lg mb-4">Section Color</h3>
                    <InputLabel for="section-color-name" value="Name" />
                    <TextInput id="section-color-name" class="block w-full mb-3" v-model="form.SectionColor.name"
                        required />
                    <div>
                        <button @click="openColorModal" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">Add
                            New</button>
                    </div>
                    <table class="table-auto w-full border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Image</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in form.SectionColor.items" :key="index">
                                <td class="border px-4 py-2">{{ item.name }}</td>
                                <td class="border px-4 py-2">
                                    <img :src="item.image" alt="Image" class="h-10 w-10" />
                                </td>
                                <td class="border px-4 py-2">{{ item.desc }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="editColorItem(index)" class="text-blue-500 mr-2">Edit</button>
                                    <button @click="deleteColorItem(index)" class="text-red-500">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Section Images -->
                <div class="border rounded-md shadow-md p-5">
                    <h3 class="font-bold text-lg mb-4">Section Images</h3>
                    <div>
                        <button @click="openImageModal" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">Add
                            New</button>
                    </div>
                    <table class="table-auto w-full border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2">Image</th>
                                <th class="px-4 py-2">Link</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in form.SectionImages" :key="index">
                                <td class="border px-4 py-2">
                                    <img :src="item.image" alt="Image" class="h-10 w-10" />
                                </td>
                                <td class="border px-4 py-2">{{ item.link }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="editImageItem(index)" class="text-blue-500 mr-2">Edit</button>
                                    <button @click="deleteImageItem(index)" class="text-red-500">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <div class="border rounded-md shadow-md p-5 mb-6">
    <h3 class="font-bold text-lg mb-4">Section Commitments</h3>
    <div>
        <button @click="openCommitmentModal" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">Add New</button>
    </div>
    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Image</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in form.SectionCommitments" :key="index">
                <td class="border px-4 py-2">
                    <img :src="item.image" alt="Image" class="h-10 w-10" />
                </td>
                <td class="border px-4 py-2">{{ item.name }}</td>
                <td class="border px-4 py-2">{{ item.desc }}</td>
                <td class="border px-4 py-2">
                    <button @click="editCommitmentItem(index)" class="text-blue-500 mr-2">Edit</button>
                    <button @click="deleteCommitmentItem(index)" class="text-red-500">Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
            </div>
            <DialogModal :show="isSectionCommitmentModal" @close="closeCommitmentModal" maxWidth="5xl">
    <template #title>
        <h3 class="text-lg font-medium">{{ isSectionCommitmentEditing ? 'Edit Commitment' : 'Create Commitment' }}</h3>
    </template>
    <template #content>
        <div>
            <InputLabel for="commitment-image" value="Image URL" />
            <InputUrlImage id="commitment-image" v-model="sectionCommitmentForm.image" />
            <InputLabel for="commitment-name" value="Name" />
            <TextInput id="commitment-name" class="block w-full mb-3" v-model="sectionCommitmentForm.name" />
            <InputLabel for="commitment-desc" value="Description" />
            <MiniEditor id="commitment-desc" v-model="sectionCommitmentForm.desc" />
        </div>
    </template>
    <template #footer>
        <button @click="saveCommitmentItem" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Save</button>
        <button @click="closeCommitmentModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
    </template>
</DialogModal>
            <DialogModal :show="isSectionColorModal" @close="closeColorModal" maxWidth="5xl">
                <template #title>
                    <h3 class="text-lg font-medium">{{ isSectionColorEditing ? 'Edit Item' : 'Create Item' }}</h3>
                </template>
                <template #content>
                    <div>
                        <InputLabel for="color-name" value="Name" />
                        <TextInput id="color-name" v-model="sectionColorForm.name" class="block w-full mb-3" required />
                        <InputLabel for="color-image" value="Image URL" />
                        <InputUrlImage id="color-image" v-model="sectionColorForm.image" />
                        <InputLabel for="color-desc" value="Description" />
                        <MiniEditor id="color-desc" v-model="sectionColorForm.desc" />
                    </div>
                </template>
                <template #footer>
                    <button @click="saveColorItem" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Save</button>
                    <button @click="closeColorModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                </template>
            </DialogModal>
            <!-- Dialog Modal -->
            <DialogModal :show="isSectionImageModal" @close="closeImageModal" maxWidth="5xl">
                <template #title>
                    <h3 class="text-lg font-medium">{{ isSectionImageEditing ? 'Edit Item' : 'Create Item' }}</h3>
                </template>
                <template #content>
                    <div>
                        <InputLabel for="image-url" value="Image URL" />
                        <InputUrlImage id="image-url" v-model="sectionImageForm.image" />
                        <InputLabel for="image-link" value="Link" />
                        <TextInput id="image-link" class="block w-full mb-3" v-model="sectionImageForm.link" />
                    </div>
                </template>
                <template #footer>
                    <button @click="saveImageItem" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Save</button>
                    <button @click="closeImageModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                </template>
            </DialogModal>
        </AppLayout>
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>

<script setup>
import Multiselect from '@vueform/multiselect';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive, onMounted } from 'vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputUrlImage from '@/Components/Input/InputUrlImage.vue';
import DialogModal from '@/Components/DialogModal.vue';
import MiniEditor from '@/Components/MiniEditor.vue';
import Card from '@/Components/Card.vue';

const isPageLoading = ref(false);
const isSectionImageModal = ref(false);
const isSectionImageEditing = ref(false);
const imageItemIndex = ref(null);

const form = reactive({
    SectionProduct: { name: '', desc: '', image: '', products: [] },
    SectionColor: { name: '', items: [] },
    SectionImages: [],
});
const productOptions = ref([])
const sectionImageForm = reactive({ image: '', link: '' });

const breadcrumbs = [
    ['Appearance', '/appearance'],
    ['Edit Footer', '/appearance/footer'],
];
const isSectionCommitmentModal = ref(false);
const isSectionCommitmentEditing = ref(false);
const commitmentItemIndex = ref(null);

const sectionCommitmentForm = reactive({
    image: '',
    name: '',
    desc: '',
});

form.SectionCommitments = reactive([
    {
        image: '/images/24.webp',
        name: 'Commit 1',
        desc: 'Commit 1',
    },
]);

const openCommitmentModal = () => {
    resetCommitmentForm();
    isSectionCommitmentEditing.value = false;
    isSectionCommitmentModal.value = true;
};

const editCommitmentItem = (index) => {
    Object.assign(sectionCommitmentForm, form.SectionCommitments[index]);
    commitmentItemIndex.value = index;
    isSectionCommitmentEditing.value = true;
    isSectionCommitmentModal.value = true;
};

const saveCommitmentItem = () => {
    if (isSectionCommitmentEditing.value) {
        form.SectionCommitments[commitmentItemIndex.value] = { ...sectionCommitmentForm };
    } else {
        form.SectionCommitments.push({ ...sectionCommitmentForm });
    }
    closeCommitmentModal();
};

const deleteCommitmentItem = (index) => {
    form.SectionCommitments.splice(index, 1);
};

const closeCommitmentModal = () => {
    isSectionCommitmentModal.value = false;
    resetCommitmentForm();
};

const resetCommitmentForm = () => {
    Object.assign(sectionCommitmentForm, { image: '', name: '', desc: '' });
};
const isSectionColorModal = ref(false);
const isSectionColorEditing = ref(false);
const colorItemIndex = ref(null);

const sectionColorForm = reactive({
    name: '',
    image: '',
    desc: '',
});

const openColorModal = () => {
    resetColorForm();
    isSectionColorEditing.value = false;
    isSectionColorModal.value = true;
};

const editColorItem = (index) => {
    Object.assign(sectionColorForm, form.SectionColor.items[index]);
    colorItemIndex.value = index;
    isSectionColorEditing.value = true;
    isSectionColorModal.value = true;
};

const saveColorItem = () => {
    if (isSectionColorEditing.value) {
        form.SectionColor.items[colorItemIndex.value] = { ...sectionColorForm };
    } else {
        form.SectionColor.items.push({ ...sectionColorForm });
    }
    closeColorModal();
};

const deleteColorItem = (index) => {
    form.SectionColor.items.splice(index, 1);
};

const closeColorModal = () => {
    isSectionColorModal.value = false;
    resetColorForm();
};

const resetColorForm = () => {
    Object.assign(sectionColorForm, {
        name: '',
        image: '',
        desc: '',
    });
};
const openImageModal = () => {
    resetImageForm();
    isSectionImageEditing.value = false;
    isSectionImageModal.value = true;
};

const editImageItem = (index) => {
    Object.assign(sectionImageForm, form.SectionImages[index]);
    imageItemIndex.value = index;
    isSectionImageEditing.value = true;
    isSectionImageModal.value = true;
};

const saveImageItem = () => {
    if (isSectionImageEditing.value) {
        form.SectionImages[imageItemIndex.value] = { ...sectionImageForm };
    } else {
        form.SectionImages.push({ ...sectionImageForm });
    }
    closeImageModal();
};

const deleteImageItem = (index) => {
    form.SectionImages.splice(index, 1);
};

const closeImageModal = () => {
    isSectionImageModal.value = false;
    resetImageForm();
};

const resetImageForm = () => {
    Object.assign(sectionImageForm, { image: '', link: '' });
};

const loadData = async () => {
    try {
        isPageLoading.value = true;
        const response = await axios.get(route('Appearance.Home.LoadData'));
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
    axios.post(route('Appearance.Home.Update'), form)
        .then(response => {
            toast.success(response.data.message, { autoClose: 1500 });
        })
        .catch(error => {
            toast.error(error.message, { autoClose: 1500 });
        });
};
</script>
