<template>
    <div>
        <AppLayout title="Product Orders" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Orders</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[['Product Orders', route('Ecommerce.ProductOrder')]]" />
                    </div>
                </div>

            </template>
            <div class="p-2 ">
                <div class="grid grid-cols-12 gap-4">
                    <div class="lg:col-span-12 col-span-12">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg  pb-12 px-2">

                            <div class="my-2 py-10">
                                <DataTable :headers="headers" :items="dataTable" buttons-pagination show-index
                                   >



                                    <template #item-operation="item">
                                        <div class="flex items-center justify-center gap-5">
                                            <button class=" text-xl text-purple-500" @click="showProfile(item)">
                                                <Icon icon="fa6-solid:eye"  />
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

            <DialogModal :show="isProfileModal" @close="isProfileModal = false">
                <template #title>
                    <div>Order Profile</div>

                </template>

                <template #content>
                    <div class=" bg-white  overflow-hidden sm:rounded-lg pt-8 pb-12 px-2">
                        <div class="p-4">
                            <div class="grid grid-cols-12 gap-4 mb-3">
                                <div class="col-span-4">
                                    Name:
                                </div>
                                <div class="col-span-8">
                                    {{ profile.name }}
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-4 mb-3">
                                <div class="col-span-4">
                                    Phone:
                                </div>
                                <div class="col-span-8">
                                    {{ profile.phone }}
                                </div>
                            </div>
                            <div v-show="profile.email" class="grid grid-cols-12 gap-4 mb-3">
                                <div class="col-span-4">
                                    Email:
                                </div>
                                <div class="col-span-8">
                                    {{ profile.email }}
                                </div>
                            </div>
                            <div v-show="profile.message" class="grid grid-cols-12 gap-4 mb-3">
                                <div class="col-span-4">
                                    Message:
                                </div>
                                <div class="col-span-8">
                                    {{ profile.message }}
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-4 mb-3">
                                <div class="col-span-4">
                                    Products:
                                </div>
                                <div class="col-span-12">
                                    <div><img :src="profile.product.images[0]" width="100" alt=""></div>
                                    <div class="">
                                        {{ profile.product.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template #footer>



                    <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded mr-4"
                        @click="isProfileModal = false">
                        Cancel
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
                            <Icon icon="fa6-solid:x" /> class="text-red-600 mr-1" /> <span>{{ item.name }}</span>
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


const dataTable = ref([]);

const loadDataTable = () => {
    isPageLoading.value = true;
    axios.get('/vnwa/ecommerce/orders/load-data-table').then(response => {
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
const isProfileModal = ref(false);
const headers = [
    { text: "Name", value: "name" },
    { text: "Phone", value: "phone" },
    { text: "Email", value: "email" },
    { text: "Message", value: "message" },
    { text: "Action", value: "operation" },
];

const profile = ref({
    name: '',
    phone: '',
    email: '',
    message: '',
    product: [],
});
const showProfile = (item) => {
    profile.value = item;
    isProfileModal.value = true;
}






const deleteItems = async () => {
    isPageLoading.value = true;
    try {
        const dataDelete = [];
        itemsDelete.value.forEach(element => {
            dataDelete.push(element.id)

        });
        axios.post('/vnwa/ecommerce/orders/delete', { dataId: dataDelete }).then(response => {

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
