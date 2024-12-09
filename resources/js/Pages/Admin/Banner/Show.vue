<template>
    <AppLayout title="Banners">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Banners</h2>
        </template>

        <div class="py-2">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2">
                    <div class="float-left">
                        <button :disabled="itemsSelected.length <= 0"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                            @click="showModalDeleteMultipleItem">
                            <Icon icon="material-symbols:close-rounded"   class="mr-1" /> Delete Selection
                        </button>
                    </div>
                    <div class="float-right text-xs uppercase">
                        <Link :href="route('Banner.Create')"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded flex items-center justify-center gap-2">
                        <Icon icon="fa6-solid:plus"  />Create
                        </Link>
                    </div>

                    <div class="my-2 py-10">
                        <DataTable :headers="headers" :items="dataPage" buttons-pagination show-index
                            v-model:items-selected="itemsSelected">
                            <template #header-name="header">
                                <div class="filter-column flex items-center">
                                    <div>
                                        <button class="p-2 text-center mr-2 border-none"
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
                                                <Icon icon="material-symbols:close-rounded"   />
                                            </button>
                                        </div>
                                    </div>
                                    {{ header.text }}
                                </div>
                            </template>
                            <template #item-image="{ image }">
                                <div class="py-3 flex items-center justify-start">
                                    <img :src="image" alt="vinawebapp.com" class="w-20 h-auto mr-3 block" />
                                </div>
                            </template>
                            <template #item-ord="{ id, name, ord }">
                                <div class="py-3 flex items-center justify-center">
                                    <input type="number" class="text-black rounded text-center px-1" :value="ord"
                                        style="max-width: 50px;" @input="changeORD(id, name, $event)" />
                                </div>
                            </template>
                            <template #item-is_show="{ id, is_show }">
                                <div class="flex items-center cursor-pointer">
                                    <div class="relative" @click="handleStatusChange(id)">
                                        <div class="toggle-path bg-gray-300 w-9 h-5 rounded-full p-0">
                                            <div class="toggle-circle w-5 h-5 rounded-full shadow-md"
                                                :class="{ 'transform translate-x-full bg-purple-500': is_show == 1, 'bg-white': is_show == 0 }">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template #item-operation="{ id, name }">
                                <div class="py-3 flex items-center justify-center">
                                    <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                        @click="showModalDeleteItem(id, name)">
                                        <Icon icon="material-symbols:close-rounded"   />
                                    </button>
                                    <Link :href="route('Banner.Edit', id)"
                                        class="bg-yellow-600 text-white px-2 py-1 rounded-md mr-5">
                                    <Icon icon="fa6-solid:pen-to-square"  />
                                    </Link>
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <DialogModal :show="modalDelete" @close="closeModal">
            <template #title>Xóa dữ liệu</template>
            <template #content>
                Chắc chắn xóa các dữ liệu này!
                <div class="mt-4"></div>
                <div v-if="itemsDelete.length > 0">
                    <div class="flex items-center" v-for="item in itemsDelete" :key="item.id">
                        <Icon icon="material-symbols:close-rounded"   /> class="text-red-600 mr-1" /> <span>{{ item.name }}</span>
                    </div>
                </div>
                <!-- <div class="mt-3 mb-1">
                    <div class="text-xs text-gray-600">Lưu ý:
                        <ul class="pl-4">
                            <li class="font-bold list-disc">Dữ liệu được xóa sẽ tự động đưa vào thùng rác</li>
                            <li class="font-bold list-disc">Dữ liệu trong thùng rác sẽ tự động xóa sau 30 ngày</li>
                            <li class="font-bold list-disc">Muốn xóa trực tiếp hãy bỏ chọn checkbox bên dưới</li>
                        </ul>
                    </div>
                    <div class="max-w-xs">
                        <div class="flex items-center h-6 mt-3 cursor-pointer">
                            <input id="bordered-checkbox-1" type="checkbox" v-model="checkboxDeleteToTrash"
                                class="w-4 h-4 text-blue-600 bg-gray-100 cursor-pointer focus:ring-2" />
                            <label for="bordered-checkbox-1" class="cursor-pointer w-full py-4 ms-2 text-sm">Đưa vào
                                thùng rác</label>
                        </div>
                    </div>
                </div> -->
            </template>
            <template #footer>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                    @click="deleteItems">Xóa dữ liệu</button>
                <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                    @click="closeModal">Hủy lệnh</button>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';

const dataPage = ref([]);
const itemsDelete = ref([]);
const modalDelete = ref(false);
const itemsSelected = ref([]);
const inputSearchName = ref(false);
const searchName = ref('');
const checkboxDeleteToTrash = ref(true);
const headers = ref([
    { text: "ORD", value: "ord", sortable: true },
    { text: "Name", value: "name" },
    { text: "Avatar", value: "image" },
    { text: "Description", value: "description" },
    { text: "Is Show", value: "is_show" },
    { text: "Update Time", value: "update_time" },
    { text: "Action", value: "operation" }
]);

const { props } = usePage();
const dataAll = ref(props.data.map(item => ({ ...item, status: item.is_show === 1 })));

const loadData = () => {
    axios.post('/vnwa/banners/load-data-table').then((response) => {
        dataPage.value = response.data.data.map(item => ({ ...item, status: item.is_show === 1 }));
    }).catch(error => console.error(error));
};

const closeModal = () => {
    modalDelete.value = false;
};

const changeORD = (id, name, event) => {
    const ordValue = event.target.value;
    if (ordValue) {
        axios.post('/vnwa/change-ord', { tb: 'banners', id, value: ordValue }).then(() => {
            toast.success(`Update ORD  ${name} thành ${ordValue} thành công!`, { autoClose: 1000 });
        }).catch(error => console.error(error));
    }
};

const showModalDeleteItem = (id, name) => {
    itemsDelete.value = [{ id, name }];
    modalDelete.value = true;
};

const showModalDeleteMultipleItem = () => {
    const commonItems = dataPage.value.filter(item1 =>
        itemsSelected.value.some(item2 => item1.id === item2.id)
    );
    itemsDelete.value = commonItems
    modalDelete.value = true;
};

const deleteItems = () => {
    const ids = itemsDelete.value.map(item => item.id);
    const body = { ids, deleteToTrash: checkboxDeleteToTrash.value };
    axios.post(route('Banner.Delete'), body).then(() => {
        loadData();
        itemsSelected.value = [];
        modalDelete.value = false;
        toast.success('Delete Success', { autoClose: 1000 });
    }).catch(error => console.error(error));
};

const searchDataTable = () => {
    axios.post('/vnwa/banners/load-data-table', { search_name: searchName.value }).then((response) => {
        dataPage.value = response.data.data;
    }).catch(error => console.error(error));
};

const handleStatusChange = (id, status) => {
    axios.post('/vnwa/change-status', { tb: 'banners', id }).then(() => {
        loadData();

        toast.success('Update  Status Success!', { autoClose: 1000 });
    }).catch(error => console.error(error));
};

loadData();
</script>
