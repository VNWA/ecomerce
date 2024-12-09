<template>
    <div>
        <AppLayout title="Tag Blog" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tags Blog</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[['Tags Blog', route('Blog.Tags')]]" />
                    </div>
                </div>

            </template>
            <div class="p-2 ">
                <div class="grid grid-cols-12 gap-4">
                    <div class="lg:col-span-12 col-span-12">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2">
                            <div class="float-left">
                                <button v-if="itemsSelected.length > 0"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                                    @click="showisModalDeleteMutipleItem">
                                    <Icon icon="material-symbols:close-rounded"   class="mr-1" /> Clear data selection

                                </button>
                            </div>
                            <div class="float-right text-xs uppercase">
                                <button @click="showFormCreate"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded flex items-center justify-center gap-2 ">
                                    <Icon icon="fa6-solid:plus" /> Create
                                </button>
                            </div>

                            <div class="my-2 py-10">
                                <DataTable :headers="headers" :items="dataTable" buttons-pagination show-index
                                    v-model:items-selected="itemsSelected">



                                    <template #item-operation="item">
                                        <div class="flex items-center justify-center gap-5">
                                            <button class=" text-xl text-yellow-600" @click="showFormEdit(item)">
                                                <Icon icon="fa6-solid:pen" />
                                            </button>
                                            <button class=" text-xl text-red-500"
                                                @click="showisModalDeleteItem(item.id, item.name)">
                                                <Icon icon="fa6-solid:trash" />
                                            </button>
                                        </div>
                                    </template>

                                </DataTable>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <DialogModal :show="isFormModal" @close="isFormModal = false">
                <template #title>
                    <div v-if="!form.id">Create Tag Blog</div>
                    <div v-else>Edit Tag Blog</div>
                </template>

                <template #content>
                    <div class=" bg-white  overflow-hidden sm:rounded-lg pt-8 pb-12 px-2">
                        <div class="p-4">
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
                                                    viewBox="0 0 100 101" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
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

                        </div>
                    </div>
                </template>

                <template #footer>


                    <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-2 rounded mr-4"
                        @click="submit">
                        Save
                    </button>
                    <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded mr-4"
                        @click="isFormModal = false">
                        Hủy lệnh
                    </button>
                </template>
            </DialogModal>

            <DialogModal :show="isModalDelete" @close="isModalDelete = false">
                <template #title>
                    Xóa dữ liệu
                </template>

                <template #content>
                    Chắc chắn xóa các dữ liệu này!
                    <div class="mt-4">
                    </div>
                    <div v-if="itemsDelete.length > 0">
                        <div class="flex items-center" v-for="item in itemsDelete">
                            <Icon icon="material-symbols:close-rounded"   /> class="text-red-600 mr-1" /> <span>{{ item.name }}</span>
                        </div>
                    </div>

                    <div class="mt-3 mb-1">
                        <div class="text-xs text-gray-600">Lưu ý :
                            <ul class="pl-4">
                                <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Các
                                    dữ liệu được xóa sẽ tự động đưa vào thùng rác </li>
                                <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Các
                                    dữ liệu trong thùng rác được tự động xóa sau 30 </li>
                                <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Muốn
                                    xóa trực tiếp hãy bỏ chọn checkbox bên dưới</li>
                            </ul>
                        </div>

                    </div>
                </template>

                <template #footer>


                    <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                        @click="deleteItems">
                        Xóa dữ liệu
                    </button>
                    <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                        @click="isModalDelete = false">
                        Hủy lệnh
                    </button>
                </template>
            </DialogModal>

        </AppLayout>

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';

import { onMounted, ref } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputText from '@/Components/Input/InputText.vue';
import InputError from '@/Components/InputError.vue';
import { convertToSlug } from '@/utils/helpers';
const url_web = new URL(window.location.href)
const form = ref({
    id: null,
    name: '',
    slug: '',
})
const errors = ref({
    name: '',
    slug: '',
})
const clearError = () => {
    errors.value = {
        name: '',
        slug: '',
    }
}


const dataTable = ref([]);

const loadDataTable = () => {
    isPageLoading.value = true;
    axios.get('/vnwa/blog/tags/load-data-table').then(response => {
        dataTable.value = response.data
    }).catch(error => {
        console.error(error);
    });
    isPageLoading.value = false;
}
onMounted(() => {
    loadDataTable();
})


const isPageLoading = ref(false);
const itemsDelete = ref([])
const isModalDelete = ref(false)
const itemsSelected = ref([]);
const isFormModal = ref(false);
const headers = [
    { text: "Name", value: "name" },
    { text: "Qnt Post", value: "qnt_post" },
    { text: "Action", value: "operation" },
];




const showFormEdit = (item) => {
    clearError();
    isPageLoading.value = true;
    form.value = {
        id: item.id,
        name: item.name,
        slug: item.slug,
    }
    isFormModal.value = true;
    isPageLoading.value = false;

}
const showFormCreate = () => {
    clearError();
    isFormModal.value = true;
    form.value = {
        id: null,
        name: '',
        slug: '',
    };
}

const submit = () => {
    isPageLoading.value = true;
    clearError();
    const url = ref('');
    if (form.value.id) {
        url.value = '/vnwa/blog/tags/update/' + form.value.id;
    } else {
        url.value = '/vnwa/blog/tags/create';
    }

    axios.post(url.value, form.value).then(response => {
        isFormModal.value = false;
        toast.success(response.data.message)
        loadDataTable();

    }).catch(error => {
        const errorKeys = Object.keys(error.response.data.errors);
        errorKeys.forEach(key => {
            if (key in errors.value) {
                errors.value[key] = error.response.data.errors[key][0]; // Lấy giá trị lỗi đầu tiên (nếu có)
            }
        });
        toast.error(error.message, {
            autoClose: 1500,
        });
    })
    isPageLoading.value = false;



}






const deleteItems = async () => {
    isPageLoading.value = true;
    try {
        const dataDelete = [];
        itemsDelete.value.forEach(element => {
            dataDelete.push(element.id)

        });
        axios.post('/vnwa/blog/tags/delete', { dataId: dataDelete }).then(response => {

            toast.success(response.data.message);
            isModalDelete.value = false;
            loadDataTable();

        }).catch(error => {
            toast.success(error.message);

        })
    } catch (error) {
        console.error('Error while changing status:', error);
    }
    isPageLoading.value = false;

};



const showisModalDeleteMutipleItem = () => {

    itemsDelete.value = [];
    itemsSelected.value.forEach(element => {
        // itemsDelete.value['id'] = element.id;
        // itemsDelete.value[id] = element.name;
        itemsDelete.value.push({ id: element.id, name: element.name })

    });
    console.log(itemsDelete.value)
    isModalDelete.value = true;

};
const showisModalDeleteItem = async (deleteId, deleteName) => {
    itemsDelete.value = [];
    itemsDelete.value.push({ id: deleteId, name: deleteName })
    isModalDelete.value = true;

};

const isSlugLoading = ref(false);

const checkSlug = (slug) => {
    clearError();
    if (slug) {
        const model_type = 'App\Models\TagBlog';
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

</script>

<style></style>
