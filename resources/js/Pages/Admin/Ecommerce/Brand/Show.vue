<template>
    <AppLayout title="Brans">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Brans
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

                        <Link :href="route('Ecommerce.Brand.Create')"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded flex items-center justify-center gap-2 ">
                        <Icon icon="fa6-solid:plus"  /> Thêm dữ liệu
                        </Link>
                    </div>

                    <div class="my-2 py-10">
                        <DataTable :headers="headers" :items="dataPage" buttons-pagination show-index
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
                            <template #item-image="{ image }">
                                <div class="py-3 flex items-center justify-start">
                                    <img :src="image" alt="vinawebapp.com" class="w-20 h-auto mr-3 block" />
                                </div>
                            </template>



                            <template #item-operation="{ id, name }">
                                <div class="py-3 flex items-center justify-center">
                                    <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                        @click="showModalDeleteItem(id, name)">
                                        <Icon icon="fa6-solid:x" />
                                    </button>
                                    <Link :href="route('Ecommerce.Brand.Edit', id)"
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
                    <div class="max-w-xs">
                        <div class="flex items-center h-6 mt-3 cursor-pointer  ">
                            <input id="bordered-checkbox-1" :checked="checkboxDeleteToTrash"
                                @click="checkboxDeleteToTrash = !checkboxDeleteToTrash" type="checkbox" value=""
                                name="bordered-checkbox"
                                class="  cursor-pointer w-4 h-4 text-blue-600 bg-gray-100  focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700">
                            <label for="bordered-checkbox-1"
                                class=" cursor-pointer w-full py-4 ms-2 text-sm font-medium text-black ">Đưa vào thùng
                                rác</label>
                        </div>

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

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import axios from 'axios';
export default {
    props: {
    },

    components: {
        Link, AppLayout,
        Welcome, DialogModal
    },
    data() {
        return {
            inputSearchName: false,
            checkboxDeleteToTrash: true,
            itemsDelete: [],
            modalDelete: false,
            itemsSelected: [],
            headers: [
                { text: "Name", value: "name" },
                { text: "Avatar", value: "image" },
                { text: "Description", value: "description" },
                { text: "Link", value: "link" },
                { text: "Update Time", value: "update_time" },
                { text: "Action", value: "operation" },
            ],

        };
    },
    methods: {

        closeModal() {
            this.modalDelete = false;
        },


        async deleteItems() {


            try {
                const dataDelete = [];
                this.itemsDelete.forEach(element => {
                    dataDelete.push(element.id)

                });
                const checkboxDeleteToTrash = this.checkboxDeleteToTrash;
                // Gửi POST request tới server để thay đổi giá trị status
                const response = await axios.post('/vnwa/ecommerce/brands/delete', { dataId: dataDelete });

                this.loadData();
                this.modalDelete = false;
                toast.success("Delete data success", {
                    autoClose: 1500,
                });

            } catch (error) {
                console.error('Error while changing status:', error);
            }
        },
        showModalDeleteMutipleItem() {

            this.itemsDelete = [];
            this.itemsSelected.forEach(element => {
                // this.itemsDelete['id'] = element.id;
                // this.itemsDelete[id] = element.name;
                this.itemsDelete.push({ id: element.id, name: element.name })

            });
            console.log(this.itemsDelete)
            this.modalDelete = true;

        },
        showModalDeleteItem(deleteId, deleteName) {
            this.itemsDelete = [];
            this.itemsDelete.push({ id: deleteId, name: deleteName })
            this.modalDelete = true;

        },
        async handleStatusChange(id, currentStatus) {
            try {
                const newStatus = !currentStatus ? 1 : 0;
                // Gửi POST request tới server để thay đổi giá trị status
                const response = await axios.post('/vnwa/ecommerce/change-status', {
                    tb: 'brands',
                    id: id,
                    status: newStatus, // Chuyển đổi giá trị status
                });
                if (newStatus == 1) {
                    toast.success("Hiện dữ liệu thành công", {
                        autoClose: 1000,
                    });
                } else {
                    toast.success("Ẩn dữ liệu thành công", {
                        autoClose: 1000,
                    });
                }
            } catch (error) {
                console.error('Error while changing status:', error);
            }
        },

    },

    setup() {
        const dataAll = ref([]);
        const dataPage = ref([]);
        const data = usePage();

        const trashCount = ref();


        trashCount.value = data.props.trashCountNumber;

        dataAll.value = data.props.data;
        dataAll.value = data.props.data.map(item => {
            return { ...item, status: item.is_show === 1 ? true : false };
        });

        const loadData = () => {
            axios.post('/vnwa/ecommerce/brands/load-data-table')
                .then((response) => {
                    let returnData = response.data;
                    dataPage.value = returnData.data;
                    dataPage.value = returnData.data.map(item => {
                        return { ...item, status: item.is_show === 1 ? true : false };
                    });

                    trashCount.value = returnData.trashCount;
                    console.log(trashCount.value)
                })
                .catch((error) => {
                    console.log(error);
                });
        }

        const checkedStatusItems = ref([]);
        dataAll.value.forEach(element => {
            checkedStatusItems.value[element.id] = element.is_show;
        });
        const checkedHighlightItems = ref([]);
        dataAll.value.forEach(element => {
            checkedHighlightItems.value[element.id] = element.highlight;
        });

        dataPage.value = dataAll.value;
        const searchName = ref('');
        const searchDataTable = () => {
            if (searchName.value) {
                const searchResults = dataAll.value.filter(item => item.name.trim().toLowerCase().includes(searchName.value.trim().toLowerCase()));
                dataPage.value = searchResults;
            } else {
                dataPage.value = dataAll.value;

            }

        }


        return {
            dataPage, checkedStatusItems, checkedHighlightItems, trashCount, loadData, searchDataTable, searchName
        }
    },
    // Các phương thức khác của component
}
</script>
