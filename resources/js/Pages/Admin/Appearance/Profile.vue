<template>
    <div>
        <AppLayout title="Edit Profile" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Profile</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Appearance', route('Appearance')], [' Edit Profile', route('Appearance.Profile')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5 text-black/80">
                <form @submit.prevent="saveUpdate">

                    <div class="mb-8 bg-white p-5 shadow shadow-black">
                        <h3 class="font-medium text-xl">Contact</h3>

                        <div class="mb-5">
                            <div class="mb-4">
                                <InputLabel for="facebook">Facebook</InputLabel>
                                <TextInput
                                    id="facebook"
                                    v-model="form.contact.facebook"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Facebook link"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="instagram">Instagram</InputLabel>
                                <TextInput
                                    id="instagram"
                                    v-model="form.contact.instagram"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Instagram link"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="youtube">YouTube</InputLabel>
                                <TextInput
                                    id="youtube"
                                    v-model="form.contact.youtube"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="YouTube link"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="pinterest">Pinterest</InputLabel>
                                <TextInput
                                    id="pinterest"
                                    v-model="form.contact.pinterest"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Pinterest link"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="tiktok">TikTok</InputLabel>
                                <TextInput
                                    id="tiktok"
                                    v-model="form.contact.tiktok"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="TikTok link"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="whatsapp">WhatsApp</InputLabel>
                                <TextInput
                                    id="whatsapp"
                                    v-model="form.contact.whatsapp"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="WhatsApp link"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="phone">Phone <span class="text-red-500">*</span></InputLabel>
                                <TextInput
                                    id="phone"
                                    v-model="form.contact.phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="0123456789"
                                    required
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="email">Email <span class="text-red-500">*</span></InputLabel>
                                <TextInput
                                    id="email"
                                    v-model="form.contact.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    placeholder="example@gmail.com"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-sm mb-4 p-2">
                        <div>
                            <SeoMetaForm v-model="form.seo" />
                        </div>
                    </div>

                    <div class="mt-6 w-full text-center">
                        <button type="submit" class="bg-purple-500 hover:bg-purple-900 hover:text-white py-2 px-5 rounded-sm text-white text-nowrap ms-3">
                            <Icon icon="fa6-solid:save" />
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref, reactive } from 'vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SeoMetaForm from '@/Components/SeoMetaForm.vue';

const isPageLoading = ref(false);
const form = reactive({
    contact: [],
    seo: {
        meta_image: '',
        meta_desc: '',
        meta_title: '',
    },
});

const loadData = async () => {
    isPageLoading.value = true;
    await axios.get('/vnwa/appearance/profile/load-json-data')
        .then(response => {
            // Giả sử response.data đã có định dạng tương tự như form.contact
            form.contact = response.data.contact; // Cập nhật dữ liệu
            form.seo = response.data.seo; // Cập nhật dữ liệu SEO
            isPageLoading.value = false;
        })
        .catch(error => {
            console.log(error);
            isPageLoading.value = false;
        });
};

onMounted(loadData);

const saveUpdate = async () => {
    if (!form.seo.meta_image || !form.seo.meta_desc || !form.seo.meta_title) {
        toast.error('The fields meta_image, meta_desc, and meta_title cannot be empty!', { autoClose: 1500 });
        return; // Dừng hàm nếu có trường bị rỗng
    }
    try {
        const response = await axios.post('/vnwa/appearance/profile/update', { data: form });
        toast.success(response.data.message, { autoClose: 1500 });
    } catch (error) {
        toast.error(error.message, { autoClose: 1500 });
    }
};
</script>
