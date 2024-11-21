<template>
    <div>
        <AppLayout title=" Edit Color" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Color</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Color', route('Ecommerce.Color')], [' Edit', route('Ecommerce.Color.Edit',props.color.id)]]" />
                    </div>
                </div>
            </template>

            <div class="grid grid-cols-12 gap-4 p-2">
                <div class="lg:col-span-8 col-span-12">
                    <div class="bg-white shadow rounded-sm mb-3 p-2">
                        <div class="mb-4">
                            <InputLabel for="name">Name <span class="text-red-500">*</span></InputLabel>
                            <TextInput id="name" v-model="form.name" type="text"
                                class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="errors.name" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="slug">Link </InputLabel>
                            <TextInput id="slug" v-model="form.slug" type="text"
                                class="mt-1 block w-full"  />
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
const props =defineProps({
    color:Object
})

const isPageLoading = ref(false);
const form = ref({

    image: props.color.image,
    name: props.color.name,
    desc: props.color.desc,
    slug: props.color.slug,

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
        const model_type = 'App\Models\Color';
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
    axios.post('/vnwa/ecommerce/colors/update/'+props.color.id, form.value).then(response => {
        toast.success(response.data.message);
        isPageLoading.value = false;
        if (!isRollBack) {
            window.history.back();
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
