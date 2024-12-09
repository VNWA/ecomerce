<template>
    <div>
        <AppLayout title=" Create New Brand" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Brand</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Brand', route('Ecommerce.Brand')], [' Create', route('Ecommerce.Brand.Create')]]" />
                    </div>
                </div>
            </template>

            <div class="grid grid-cols-12 gap-4 p-2">
                <div class="lg:col-span-8 col-span-12">
                    <div class="bg-white shadow rounded-sm mb-3 p-2">
                        <div class="mb-4">
                            <InputLabel for="name">Name <span class="text-red-500">*</span></InputLabel>
                            <InputText id="name" :maxLength="150" v-model="form.name" type="text"
                                class="mt-1 block w-full" @change="nameChange" required />
                            <InputError class="mt-2" :message="errors.name" />
                        </div>
                        <div class="mb-4">
                            <InputLabel for="slug">Slug <span class="text-red-500">*</span></InputLabel>
                            <div>
                                <div class="relative">

                                    <div class="flex items-center justify-start">
                                        <div
                                            class=" block border-r-0 rounded-r-none border h-full py-2 px-2 bg-gray-50 text-nowrap text-black ">
                                            {{ url_web.origin }} /
                                        </div>
                                        <TextInput id="slug" v-model="form.slug" type="text"
                                            class=" block w-full pe-14 rounded-l-none"
                                            :class="{ 'border-red-500 focus:ring-red-500': errors.slug.trim().length > 0 }"
                                            required @change="checkSlug($event.target.value)" />
                                    </div>
                                    <div class="absolute top-2 right-5">
                                        <div role="status" v-if="isSlugLoading">
                                            <svg aria-hidden="true"
                                                class="w-6 h-6 text-gray-200 animate-spin  fill-purple-600"
                                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                    fill="currentFill" />
                                            </svg>
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <Icon
                                            v-if="!isSlugLoading && form.slug.trim().length > 0 && errors.name.trim().length <= 0 && errors.slug.trim().length > 0"
                                            class="text-red-500 font-bold text-lg" icon="material-symbols:close-rounded"   />
                                        <Icon
                                            v-else-if="!isSlugLoading && form.slug.trim().length > 0 && errors.name.trim().length <= 0 && errors.slug.trim().length <= 0"
                                            class="text-green-500 font-bold text-lg" icon="fa6-solid:check" />
                                    </div>
                                </div>

                            </div>
                            <InputError class="mt-2" :message="errors.slug" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="desc">Desciption </InputLabel>
                            <textarea id="desc" v-model="form.desc"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="5"   ></textarea>
                            <InputError class="mt-2" :message="errors.desc" />
                        </div>



                    </div>

                </div>
                <div class="lg:col-span-4 col-span-12">
                    <div class="flex flex-col">

                        <div class="bg-white shadow rounded-sm mb-3 p-2 sticky top-0">
                            <div class="py-2 border-b border-black/10 mb-5 text-black">
                                <h2 class="text-lg font-bold">Publish</h2>
                            </div>

                            <div class="flex items-center justify-end gap-4  ">
                                <button @click="submit(false)"
                                    class="flex-items-center justify-center bg-white hover:bg-white/80 rounded-md px-5 py-2 min-w-24 text-black/80 border text-lg font-bold">
                                    <Icon icon="fa6-solid:floppy-disk"  /> Save & Exit
                                </button>
                                <button @click="submit"
                                    class="flex-items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold">
                                    <Icon icon="fa6-solid:floppy-disk"  /> Save
                                </button>

                            </div>
                            <div class="bg-white shadow rounded-sm mb-3 p-2">
                                <div class="py-2 border-b border-black/10 mb-5">
                                    <InputLabel>Image <span class="text-red-500">*</span></InputLabel>

                                </div>
                                <div>
                                    <InputUrlImage v-model="form.image" />
                                    <InputError class="mt-2" :message="errors.image" />

                                </div>
                            </div>
                        </div>





                    </div>
                </div>
            </div>
            {{ route }}
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputUrlImage from '@/Components/Input/InputUrlImage.vue';
import InputText from '@/Components/Input/InputText.vue';
import { convertToSlug } from '@/utils/helpers';

const url_web = new URL(window.location.href)
const isSlugLoading = ref(false);

const isPageLoading = ref(false);
const form = ref({

    image: '',
    name: '',
    desc: '',
    slug: '',

});
const errors = ref({
    image: '',
    name: '',
    desc: '',
    slug: '',

})


const clearError = () => {
    errors.value = {
        image: '',
        name: '',
        desc: '',
        slug: ''
    }
}
const checkSlug = (slug) => {
    clearError();
    if (slug) {
        const model_type = 'App\Models\Brand';
        isSlugLoading.value = true;
        const url = ref('');
        if (form.value.id) {
            url.value = '/vnwa/check-slug/' + slug + '/' + model_type + '/' + form.value.id;
        } else {
            url.value = '/vnwa/check-slug/' + slug;
        }
        // Gửi request kiểm tra slug
        axios.get(url.value)
            .then(response => {
                // Xử lý kết quả trả về từ server
                if (response.data.type === 'error') {
                    // Xử lý khi slug đã tồn tại
                    errors.value.slug = response.data.message;

                    // Hiển thị thông báo hoặc xử lý người dùng
                } else {
                    // Nếu slug hợp lệ, có thể thực hiện các bước tiếp theo
                    // Ví dụ: cập nhật meta_title

                }
            })
            .catch(error => {
                errors.value.slug = error.response.data.message;
            });
        isSlugLoading.value = false;
    }

}

const nameChange = (value) => {
    form.value.slug = convertToSlug(value);
    checkSlug(form.value.slug);
}



const submit = (isRollBack = true) => {
    clearError();
    isPageLoading.value = true;
    axios.post('/vnwa/ecommerce/brands/create', form.value).then(response => {
        toast.success(response.data.message);
        isPageLoading.value = false;
        if (!isRollBack) {
            window.close();
        } else {
            form.value.name = ''
            form.value.image = ''
        }
    }).catch(error => {
        isPageLoading.value = false;

        if (error.response.data.errors) {
            const errorKeys = Object.keys(error.response.data.errors);
            errorKeys.forEach(key => {
                if (key in errors.value) {
                    errors.value[key] = error.response.data.errors[key][0]; // Lấy giá trị lỗi đầu tiên (nếu có)
                }
            });
        }

        toast.error(error.message, {
            autoClose: 1500,
        });
    })

}

</script>

<style></style>
