<template>
    <AppLayout title="Orders" :isLoading="isPageLoading">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
                </div>
                <div>
                    <HeaderBreadcrumbs :breadcrumbs="[['Orders', route('Ecommerce.Order')]]" />
                </div>
            </div>
        </template>

        <div class="p-2">
            <div class="grid grid-cols-12 gap-4">
                <div class="lg:col-span-12 col-span-12">
                    <div class="bg-white  shadow-xl sm:rounded-lg pt-8 pb-12 px-2 ">
                        <div class="flex items-center justify-between w-full mb-5">

                            <div class="">
                                <button :disabled="itemsSelected.length === 0"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs flex items-center justify-center gap-2"
                                    :class="{ 'bg-red-600/60 hover:bg-red-600/60': itemsSelected.length === 0 }"
                                    @click="showisModalDeleteMutipleItem">
                                    <Icon icon="fa6-solid:x" class="mr-1" /> Clear data selection
                                </button>
                            </div>
                            <div>

                            </div>

                            <div class=" text-xs uppercase">

                                <div class="flex items-center justify-end gap-4">
                                    <button @click="loadFromServer"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-2 rounded ">
                                                                                    <Icon   icon="fa6-solid:rotate-right"  />
 Load Data
                                    </button>
                                    <Link :href="route('Ecommerce.Product.Create')"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded flex items-center justify-center gap-2 text-nowrap flex items-center justify-center gap-3 ">
                                    <Icon icon="fa6-solid:plus"  /> Create
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div class="border px-3 py-3">
                            <div class="flex items-center justify-center gap-4">
                                <div class="">
                                    <InputLabel for="code">Code </InputLabel>
                                    <TextInput placeholder="Search Order Code" id="code" v-model="serverOptions.code"
                                        type="text" class="mt-1 block w-full" />
                                </div>
                                <div class="">
                                    <InputLabel for="phone">Phone </InputLabel>
                                    <TextInput placeholder="Search Order Phone" id="phone" v-model="serverOptions.phone"
                                        type="text" class="mt-1 block w-full" />

                                </div>
                                <div class="">
                                    <InputLabel for="email">Email </InputLabel>
                                    <TextInput placeholder="Search Order Email" id="email" type="email"
                                        v-model="serverOptions.email" class="mt-1 block w-full" />
                                </div>
                                <div class="">
                                    <InputLabel for="city">City </InputLabel>
                                    <TextInput placeholder="Search Order City" id="city" v-model="serverOptions.city"
                                        type="text" class="mt-1 block w-full" />
                                </div>
                                <div class="">
                                    <InputLabel for="phone">Payment Method </InputLabel>
                                    <Select :options="paymentMethods" v-model="serverOptions.payment_method" />
                                </div>
                                <div class="">
                                    <InputLabel for="phone">Payment Status </InputLabel>
                                    <Select :options="paymentStatuss" v-model="serverOptions.payment_status" />
                                </div>
                                <div class="">
                                    <InputLabel for="phone">Order Status </InputLabel>
                                    <Select :options="orderStatuss" v-model="serverOptions.status" />
                                </div>
                            </div>
                        </div>
                        <div class="my-2 ">
                            <DataTable :key="reRender" v-model:server-options="serverOptions" :headers="headers"
                                :items="items" :server-items-length="serverItemsLength" :isTableLoading="isTableLoading"
                                buttons-pagination show-index v-model:items-selected="itemsSelected"
                                @expand-row="loadIntroduction">

                                <template #item-total="{ total }">
                                    <div>
                                        <span class="text-green-800 font-bold text-lg">
                                            {{ formatCurrency(total) }}
                                        </span>
                                    </div>
                                </template>
                                <template #item-operation="{ id, code }">
                                    <div class="py-3 flex items-center justify-center">
                                        <Link :href="route('Ecommerce.Order.Copy', id)"
                                            class="bg-gray-600 text-white px-2 py-1 rounded-md mr-5">
                                        <Icon  icon="fa6-solid:copy"   />
                                        </Link>
                                        <Link :href="route('Ecommerce.Order.Edit', id)"
                                            class="bg-yellow-600 text-white px-2 py-1 rounded-md mr-5">
                                        <Icon icon="fa6-solid:pen-to-square"  />
                                        </Link>
                                        <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                            @click="showModalDeleteItem(id, code)">
                                            <Icon icon="fa6-solid:x" />
                                        </button>

                                    </div>
                                </template>
                                <template #expand="item">
                                    <div v-if="item.logs" style="padding: 15px">
                                        {{ item.logs }}
                                    </div>
                                </template>
                            </DataTable>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import DialogModal from '@/Components/DialogModal.vue';
import { Link } from '@inertiajs/vue3';
import { formatCurrency } from '@/utils/helpers';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
const paymentMethods = [
    {
        name: 'Stripe',
        value: 'stripe',
    },
    {
        name: 'Paypal',
        value: 'paypal',
    }
]
const paymentStatuss = [
    {
        name: 'pending',
        value: 'pending',
    },
    {
        name: 'completed',
        value: 'completed',
    }, {
        name: 'cancelled',
        value: 'cancelled',
    }, {
        name: 'failed',
        value: 'failed',
    },
]
const orderStatuss = [
    {
        name: 'new',
        value: 'new',
    },
    {
        name: 'processing',
        value: 'processing',
    },
    {
        name: 'shipped',
        value: 'shipped',
    },
    {
        name: 'completed',
        value: 'completed',
    },
    {
        name: 'cancelled',
        value: 'cancelled',
    },
    {
        name: 'returned',
        value: 'returned',
    }

]


const isFormFilter = ref(false);
const items = ref([]);
const headers = [
    { text: "Code", value: "code" },
    { text: "Email", value: "email" },
    { text: "Phone", value: "phone" },
    { text: "City", value: "city" },
    { text: "Payment Method", value: "payment_method" },
    { text: "Payment Status", value: "payment_status" },
    { text: "Total", value: "total", sortable: true },
    { text: "Update Time", value: "updated_at", sortable: true },
    { text: "Action", value: "operation" },
];

const serverItemsLength = ref(0);
const serverOptions = ref({
    page: 1,
    rowsPerPage: 20,
    sortBy: 'created_at',
    sortType: 'desc',
    code: '',
    phone: '',
    email: '',
    city: '',
    payment_method: '',
    payment_status: '',
    status: '',
});


const loadIntroduction = async (index) => {
    const expandedItem = items.value[index];
    if (!expandedItem.logs) {
        expandedItem.expandLoading = true;
        const response = await axios.get(route('Ecommerce.Order.Logs', expandedItem.code));
        expandedItem.logs = response.data.logs;
        expandedItem.expandLoading = false;
    }
};


const isTableLoading = ref(false);
const isPageLoading = ref(false);
const isModalDelete = ref(false);
const itemsSelected = ref([]);
const itemsDelete = ref([]);
const reRender = ref(1);
const restApiUrl = computed(() => {
    const { page,
        rowsPerPage,
        sortBy,
        sortType,
        code,
        phone,
        email,
        city,
        payment_method,
        payment_status,
        status } = serverOptions.value;
    let url = route('Ecommerce.Order.LoadData') + `?page=${page}&per_page=${rowsPerPage}&sortBy=${sortBy}&sortType=${sortType}`;
    if (code) {
        url += `&code=${code}`
    }
    if (phone) {
        url += `&phone=${phone}`
    }
    if (email) {
        url += `&email=${email}`
    }
    if (city) {
        url += `&city=${city}`
    }
    if (payment_method) {
        url += `&payment_method=${payment_method}`
    }
    if (payment_status) {
        url += `&payment_status=${payment_status}`
    }
    if (status) {
        url += `&status=${status}`
    }

    return url;
});

const loadFromServer = async () => {
    isTableLoading.value = true;
    reRender.value++;
    try {
        const response = await axios.get(restApiUrl.value);
        items.value = response.data.data;
        serverItemsLength.value = response.data.total;
    } catch (error) {
        console.error(error);
    } finally {
        isTableLoading.value = false;
    }

};
onMounted(() => {
    loadFromServer();
})
watch(serverOptions, () => {
    loadFromServer();
}, { deep: true });



</script>

<style>
/* Add any styles you need here */
</style>
