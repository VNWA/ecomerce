<template>
    <div>
        <AppLayout title="Categories Blog" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories Blog2
                        </h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[['Categories', route('Blog.Categories')]]" />
                    </div>
                </div>
            </template>

            <DialogModal :show="isModalDeleteForm" @close="closeModal">
                <template #title>
                    <div>
                    </div>
                    <div class=" text-red-500   text-center">
                        <div>
                            <Icon icon="fa6-solid:triangle-exclamation"   class="text-4xl" />

                        </div>
                        <div>
                            Delete Category
                        </div>
                    </div>
                </template>

                <template #content>
                    <div class="text-center" v-if="deleteDetailItem.name">
                        <span class="text-base font-bold text-black/80 ">Are you sure you want to delete category :
                            {{ deleteDetailItem.name }} ?
                        </span>
                    </div>
                    <div class="text-center ">
                        <span>
                            Deleting this category will also delete subcategories that depend on it
                        </span>

                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton v-if="deleteDetailItem.id" class="ms-3" @click="deleteItem(deleteDetailItem.id)">
                        Delete
                    </DangerButton>
                </template>
            </DialogModal>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-4">
                        <div class="bg-black/80 p-5">
                            <div class=" mb-5 flex items-center justify-between font-bold text-xl bg-white py-2 px-2">
                                <h3 class="text-black ">List</h3>
                                <button @click="showFormCreate"
                                    class="bg-sky-700 hover:bg-sky-900 py-1 px-5 rounded-sm text-white flex items-center justify-center gap-4 ">
                                         <Icon   icon="fa6-solid:plus"  />
                                    Create
                                </button>
                            </div>
                            <Draggable class="mtl-tree capitalize" v-model="treeData" treeLine :defaultOpen="false"
                                @update:modelValue="onTreeChange">
                                <template #default="{ node, stat }">
                                    <div class=" mx-3 flex cursor-pointer items-center gap-4 "
                                        :class="{ 'font-bold': id_active === node.id }">

                                        <div @click="showFormEdit(node.id)"> {{ node.name }} ({{ node.children.length
                                            }})</div>
                                        <button v-if="id_active === node.id" @click="showFormDelete(node.id)"
                                            class="bg-red-500 hover:bg-red-600 px-2 py-1 text-white rounded-md">
                                            <Icon icon="fa6-solid:trash" />
                                        </button>
                                    </div>
                                    <OpenIcon v-if="stat.children.length" :open="stat.open" class="float-end"
                                        @click.native="stat.open = !stat.open" />
                                </template>
                            </Draggable>
                        </div>
                    </div>
                    <div class="col-span-8 ">
                        <div class=" bg-white relative">
                            <Loading v-if="isLoading" />
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
                                        <div class="relative mt-1">
                                            <div class="flex items-center justify-start">
                                                <div
                                                    class=" block border-r-0 rounded-r-none border h-full py-2 px-2 text-black bg-gray-50">
                                                    {{ url_web.origin }}/
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
                                <div class="mb-4 relative">
                                    <InputLabel for="parentId">Parent Id : {{ form.parentId }} </InputLabel>
                                    <InputTreeSelect id="parentId" :options=treeData v-model="form.parentId"
                                        class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="errors.parentId" />
                                </div>
                                <div class="mb-4">
                                    <InputLabel for="desc" value="Description" />
                                    <InputDesc id="desc" v-model="form.desc" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="errors.desc" />
                                </div>
                                <div class="mb-4">
                                    <InputLabel for="InputSelectedIcon" value="Icon" />
                                    <InputSelectedIcon id="InputSelectedIcon"
                                        class="mt-1 block w-full border border-solid border-black h-10"
                                        v-model="form.icon" />
                                    <InputError class="mt-2" :message="errors.icon" />
                                </div>
                                <div class="mb-4">
                                    <InputLabel for="InputUrlImage" value="Image" />
                                    <InputUrlImage id="InputUrlImage" class="mt-1 block   " v-model="form.image" />
                                    <InputError class="mt-2" :message="errors.image" />
                                </div>
                                <div class="mb-4">
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded ">
                                        <Checkbox v-model:checked="form.is_show" id="bordered-checkbox-1"
                                            type="checkbox" name="bordered-checkbox"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500   focus:ring-2  " />

                                        <label for="bordered-checkbox-1"
                                            class="w-full py-4 ms-2 text-sm font-medium text-gray-900 ">
                                            Status: <span v-if="form.is_show"> Published </span>
                                            <span v-else>
                                                Draft
                                            </span>

                                        </label>
                                    </div>
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded ">

                                        <Checkbox v-model:checked="form.is_highlight" id="bordered-checkbox-1"
                                            type="checkbox" name="bordered-checkbox"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500   focus:ring-2  " />
                                        <label for="bordered-checkbox-2"
                                            class="w-full py-4 ms-2 text-sm font-medium text-gray-900 ">Nổi
                                            bật
                                        </label>
                                    </div>

                                </div>
                                <div
                                    class="flex items-center px-4 border border-gray-200 rounded  mb-4">
                                    <SeoMetaForm v-model="form.seo_meta" />

                                </div>
                                <div class="flex items-center justify-end gap-4  mt-3 ">


                                    <button @click="submit"
                                        class="flex-items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold mt-5">
                                        <Icon icon="fa6-solid:floppy-disk"  /> Save
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BaseTree, Draggable, pro, OpenIcon } from '@he-tree/vue'
import '@he-tree/vue/style/default.css'
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputText from '@/Components/Input/InputText.vue';
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputSelectedIcon from '@/Components/Input/InputSelectedIcon.vue';
import InputDesc from '@/Components/Input/InputDesc.vue'
import InputUrlImage from '@/Components/Input/InputUrlImage.vue'
import InputTreeSelect from '@/Components/Input/InputTreeSelect.vue'
import Loading from '@/Components/Loading.vue'
import SeoMetaForm from '@/Components/SeoMetaForm.vue'
import DialogModal from '@/Components/DialogModal.vue';
import { convertToSlug } from '@/utils/helpers';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue'
const url_web = new URL(window.location.href)
const props = defineProps({
    data: Object
})
const treeData = ref(props.data);
const isLoading = ref(false)
const id_active = ref(null);
const isModalDeleteForm = ref(false)
const deleteDetailItem = ref({});
const isPageLoading = ref(false)
const form = ref({
    type: 'create',
    id: null,
    parentId: null,
    name: '',
    slug: '',
    icon: [],
    image: '',
    desc: '',
    is_show: true,
    is_highlight: false,
    seo_meta: {
        meta_title: "",
        meta_desc: "",
        meta_image: "",
    }
});

const errors = ref({
    name: '',
    slug: '',
    image: '',
    icon: '',
    parentId: '',
    desc: '',
    is_show: '',
    is_highlight: '',
})
const clearError = () => {
    errors.value = {
        name: '',
        slug: '',
        image: '',
        icon: '',
        parentId: '',
        desc: '',
        is_show: '',
        is_highlight: '',

    }
}
const closeModal = () => {
    isModalDeleteForm.value = false
    deleteDetailItem.value = {};
};
const loadNewTreeCategories = () => {
    isPageLoading.value = true;
    axios.get('/vnwa/blog/categories/load-new-tree-data')
        .then(response => {
            treeData.value = response.data
            isPageLoading.value = false;
            console.log(response.data)
        })
        .catch(error => {
            console.log(error);
        });
}



const onTreeChange = (newData) => {
    // Cập nhật dữ liệu cây
    treeData.value = newData;

    // Gọi API để cập nhật dữ liệu vào cơ sở dữ liệu
    axios.post('/vnwa/blog/categories/update-tree', { treeData: newData })
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
};



const showFormCreate = () => {
    clearError();
    isLoading.value = true
    id_active.value = null
    setTimeout(() => {
        form.value = {
            type: 'create',
            id: null,
            parentId: null,
            name: '',
            slug: '',
            icon: [],
            image: '',
            desc: '',
            is_show: true,
            is_highlight: false,
            seo_meta: {
                meta_title: "",
                meta_desc: "",
                meta_image: "",
            }
        }
        isLoading.value = false

    }, 100);

}
const showFormEdit = (id) => {

    isLoading.value = true
    id_active.value = id
    clearError();

    axios.get('/vnwa/blog/categories/get-detail-category/' + id)
        .then(response => {
            form.value = {
                type: 'update',
                id: response.data.id,
                parentId: response.data.parent_id,
                name: response.data.name || '',
                slug: response.data.slug || '',
                icon: response.data.icon,
                image: response.data.image || '',
                desc: response.data.desc || '',
                is_show: response.data.is_show === 1 ? true : false,
                is_highlight: response.data.is_highlight === 1 ? true : false,
                seo_meta: {
                    meta_title: response.data.meta_title || '',
                    meta_desc: response.data.meta_desc || '',
                    meta_image: response.data.meta_image || '',
                }
            }

            isLoading.value = false;

        })
        .catch(error => {
            isLoading.value = false;
            console.error('Error fetching category:', error);
        });


};
const isSlugLoading = ref(false);
const checkSlug = (slug) => {
    clearError();
    if (slug) {
        const model_type = 'App\Models\CategoryBlog';
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

const submit = () => {
    isLoading.value = true;
    clearError();
    const url = ref('');
    if (form.value.type === 'create') {
        url.value = '/vnwa/blog/categories/create';
    } else if (form.value.type === 'update') {
        url.value = '/vnwa/blog/categories/update/' + form.value.id;
    }
    axios.post(url.value, form.value).then(response => {
        toast.success(response.data.message, {
            autoClose: 1500,
        });
        loadNewTreeCategories();
        setTimeout(() => {
            showFormEdit(response.data.id);

        }, 1000);

    })
        .catch(error => {
            const errorKeys = Object.keys(error.response.data.errors);
            errorKeys.forEach(key => {
                if (key in errors.value) {
                    errors.value[key] = error.response.data.errors[key][0]; // Lấy giá trị lỗi đầu tiên (nếu có)
                }
            });
            toast.error(error.message, {
                autoClose: 1500,
            });

        });
    isLoading.value = false;




};
const showFormDelete = (id) => {
    isModalDeleteForm.value = true;

    deleteDetailItem.value = form.value;
}
const deleteItem = (id) => {
    isPageLoading.value = true;

    axios.post('/vnwa/blog/categories/delete/' + id).then(response => {
        toast.success(response.data.message, {
            autoClose: 1500,
        });
        isModalDeleteForm.value = false
        showFormCreate();
        loadNewTreeCategories();

    }).catch(error => {
        toast.error(error.response.data.message, {
            autoClose: 1500,
        });
    })
    isPageLoading.value = false;

}




</script>

<style>
.tree-node--with-tree-line {
    position: relative
}

.tree-line {
    position: absolute;
    background-color: #bbb
}

.tree-vline {
    width: 1px;
    top: 0;
    bottom: 0
}

.tree-hline {
    height: 1px;
    top: 50%;
    width: 10px
}

.he-tree--rtl {
    direction: rtl
}

.he-tree-drag-placeholder {
    background: #ddf2f9;
    border: 1px dashed #00d9ff;
    height: 22px;
    width: 100%
}

.he-tree__open-icon {
    cursor: pointer;
    user-select: none;
    display: inline-block
}

.he-tree__open-icon.open {
    transform: rotate(90deg)
}

.he-tree__open-icon svg {
    width: 1em
}

.tree-node-inner {
    cursor: grab;
    background: #dedede;
    border: 1px solid #19293c;
    border-radius: 3px;
    box-sizing: border-box;
    color: inherit;
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 4;
    font-weight: 400;
    height: 40px;
    margin: 5px 0;
    padding: 5px 10px 5px 20px;
    text-decoration: none;
}
</style>
