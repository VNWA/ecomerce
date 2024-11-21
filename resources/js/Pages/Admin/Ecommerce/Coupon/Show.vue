<template>
    <AppLayout title="Coupons" :isLoading="isPageLoading">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Coupons
            </h2>
        </template>

        <div class="py-2">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2">
                    <div class="float-left">
                        <button :disabled="itemsSelected.length <= 0"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                            @click="showModalDeleteMutipleItem">
                            <Icon icon="fa6-solid:x" class="mr-1" /> Xóa dữ liệu chọn
                        </button>
                    </div>
                    <div class="float-right text-xs uppercase">

                        <Link :href="route('Ecommerce.Coupon.Create')"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded flex items-center justify-center gap-2 ">
                        <Icon icon="fa6-solid:plus"  /> Thêm dữ liệu
                        </Link>
                    </div>

                    <div class="my-2 py-10">
                        <DataTable :headers="headers" :items="tableItems" buttons-pagination show-index
                            v-model:items-selected="itemsSelected">
                            <template #header-name="header">
                                <div class="filter-column  flex items-center">
                                    <div>
                                        <button class="p-2 text-center  mr-2 border-none "
                                            :class="{ 'bg-purple-400 text-white': searchName.trim() }"
                                            @click="inputSearchName = !inputSearchName">
                                            <Icon icon="fa6-solid:filter"  />
                                        </button>
                                        <div class="filter-menu absolute z-30 top-9 w-52 flex items-center justify-center"
                                            v-if="inputSearchName">
                                            <input style="height: 30px;" type="text" class="text-xs h-8 border-r-0"
                                                v-model="searchName" @input="searchDataTable()"
                                                placeholder="Tìm kiếm" />

                                            <button style="height: 30px; width: 30px;"
                                                class="bg-black text-white hover:text-red-400"
                                                @click="inputSearchName = false">
                                                <Icon icon="fa6-solid:x" />
                                            </button>
                                        </div>
                                    </div>
                                    {{ header.text }}

                                </div>
                            </template>
                            <template #item-dis="{ type, value }">
                                <div class="py-3 flex items-center justify-start">
                                    <div v-if="type == 'amount'">
                                        {{ formatCurrency(value) }}

                                    </div>
                                    <div v-else-if="type == 'percentage'">
                                        {{ value }} %
                                    </div>
                                </div>
                            </template>
                            <template #item-remaining="{ qnt, is_duration, start_time, end_time }">
                                <div class="py-3">
                                    <div>
                                        Quanity: {{ qnt }}
                                    </div>
                                    <div class="mt-3" v-if="is_duration == 1">
                                        <span class="px-2 bg-gray-100 border border-solid border-blue-500">
                                            {{ start_time }}
                                        </span>
                                        ->
                                        <span class="px-2 bg-gray-100 border border-solid border-blue-500">
                                            {{ end_time }}
                                        </span>
                                    </div>
                                </div>
                            </template>



                            <template #item-operation="{ id, code }">
                                <div class="py-3 flex items-center justify-center">

                                    <Link :href="route('Ecommerce.Coupon.Edit', id)"
                                        class="bg-yellow-600 text-white px-2 py-1 rounded-md mr-5">
                                    <Icon icon="fa6-solid:pen-to-square"  />
                                    </Link>
                                    <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                        @click="showModalDeleteItem(id, code)">
                                        <Icon icon="fa6-solid:x" />
                                    </button>

                                </div>
                            </template>

                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <DialogModal :show="isModalDelete" @close="closeModal">
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
                            <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Các dữ
                                liệu được xóa sẽ tự động đưa vào thùng rác </li>
                            <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Các dữ
                                liệu trong thùng rác được tự động xóa sau 30 </li>
                            <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Muốn xóa
                                trực tiếp hãy bỏ chọn checkbox bên dưới</li>
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
                    @click="closeModal">
                    Hủy lệnh
                </button>
            </template>
        </DialogModal>

    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import { formatCurrency } from '@/utils/helpers';

const inputSearchName = ref(false);
const isPageLoading = ref(false)
const itemsDelete = ref([]);
const isModalDelete = ref(false);
const itemsSelected = ref([]);
const headers = [
    { text: "Code", value: "code" },
    { text: "Discount", value: "dis" },
    { text: "Remaining", value: "remaining" },
    { text: "Action", value: "operation" },
];
const tableItems = ref([])
const loadData = async () => {
    await axios.get(route('Ecommerce.Coupon.LoadData'))
        .then((response) => {
            tableItems.value = response.data.data;

        }).catch((error) => { console.log(error); });
}

onMounted(async () => {
    await loadData();
})

const closeModal = () => {
    isModalDelete.value = false;
};


const deleteItems = async () => {


    const dataDelete = [];
    itemsDelete.value.forEach(element => {
        dataDelete.push(element.id)
    });

    axios.post(route('Ecommerce.Coupon.Delete'), { dataId: dataDelete }).then(res => {

        toast.success(res.data.message);
        closeModal()

    }).catch(error => {
        toast.error(error.message);

    }).finally(() => {
        loadData();
        isPageLoading.value = false;
    });



}

const showModalDeleteMutipleItem = () => {

    itemsDelete.value = [];
    itemsSelected.value.forEach(element => {

        itemsDelete.value.push({ id: element.id, name: element.code })

    });

    isModalDelete.value = true;

};
const showModalDeleteItem = (deleteId, deleteName) => {
    itemsDelete.value = [];
    itemsDelete.value.push({ id: deleteId, name: deleteName })
    isModalDelete.value = true;

};

const handleStatusChange = async (envet) => {

};

</script>
