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
                                    <Icon icon="material-symbols:close-rounded"   class="mr-1" /> Clear data selection
                                </button>
                            </div>
                            <div>

                            </div>

                            <div class=" text-xs uppercase">

                                <div class="flex items-center justify-end gap-4">
                                    <button @click="loadFromServer"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-2 rounded ">
                                        <Icon icon="fa6-solid:rotate-right" />
                                        Load Data
                                    </button>
                                    <Link :href="route('Ecommerce.Order.Create')"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded text-nowrap flex items-center justify-center gap-3 ">
                                    <Icon icon="fa6-solid:plus" /> Create
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
                                :items="items" :server-items-length="serverItemsLength" :loading="isTableLoading"
                                buttons-pagination show-index v-model:items-selected="itemsSelected"
                                @expand-row="loadIntroduction">

                                <template #item-total="{ total }">
                                    <div>
                                        <span class="text-green-800 font-bold text-lg">
                                            {{ formatCurrency(total) }}
                                        </span>
                                    </div>
                                </template>
                                <template #item-status="{ status }">
                                    <div>
                                        <StatusBadges type="order" :value="status" />
                                    </div>
                                </template>
                                <template #item-payment_status="{ payment_status }">
                                    <div>
                                        <StatusBadges type="payment" :value="payment_status" />
                                    </div>
                                </template>
                                <template #item-updated_at="{ update_time }">
                                    <div>
                                        {{ update_time }}
                                    </div>
                                </template>


                                <template #item-operation="{ id, code }">
                                    <div class="py-3 flex items-center justify-center">
                                        <Link :href="route('Ecommerce.Order.Copy', id)"
                                            class="bg-gray-600 text-white px-2 py-1 rounded-md mr-5">
                                        <Icon icon="fa6-solid:copy" />
                                        </Link>
                                        <a target="_blank" :href="route('Ecommerce.Order.Edit', id)"
                                            class="bg-yellow-600 text-white px-2 py-1 rounded-md mr-5">
                                            <Icon icon="fa6-solid:pen-to-square" />
                                        </a>
                                        <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                            @click="showModalDeleteItem(id, code)">
                                            <Icon icon="material-symbols:close-rounded"   />
                                        </button>

                                    </div>
                                </template>
                                <template #expand="item">
                                    <div class="p-10 bg-black/50">
                                        <div class=" bg-white border min-h-full">
                                            <div class="mb-3 px-3 py-3" v-if="item.payment_status == 'pending'">
                                                <a :href="$page.props.frontend_url + '/order/checkout/' + item.code"
                                                    target="_blank" rel="noopener noreferrer">
                                                    <button
                                                        class="bg-blue-500 text-white px-3 py-2 flex items-center justify-center">
                                                        Payment Link
                                                        <Icon icon="material-symbols:add-link" class="text-xl" />
                                                    </button>
                                                </a>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="max-h-[500px] p-3 overflow-auto">
                                                    <div class="p-3 border">
                                                        <ol
                                                            class="relative border-s border-gray-200 dark:border-gray-700">
                                                            <li class="mb-10 ms-4" v-for="(item, index) in item.logs"
                                                                :key="index">
                                                                <div
                                                                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                                                                </div>
                                                                <time
                                                                    class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{
                                                                        item.update_time }}
                                                                </time>
                                                                <p class="mb-4 text-base font-normal text-black/70 ">{{
                                                                    item.message }}</p>
                                                            </li>
                                                        </ol>
                                                    </div>

                                                </div>
                                                <div class="max-h-[500px] p-3 overflow-auto">
                                                    <div class="p-3 border">
                                                        <ul class="">
                                                            <li class="mb-10 ms-4" v-for="(item, index) in item.items"
                                                                :key="index">
                                                                <div>
                                                                    <ProductItem :product="item">
                                                                        <div>
                                                                            <span class="text-lg font-bold px-3">
                                                                                {{ item.quantity }}
                                                                            </span>
                                                                        </div>
                                                                    </ProductItem>
                                                                </div>
                                                            </li>

                                                        </ul>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>

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
import StatusBadges from '@/Components/StatusBadges.vue';
import ProductItem from '@/Components/ProductItem.vue';
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
    { text: "Status", value: "status" },
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
    status: 'new',
});


const loadIntroduction = async (index) => {
    const expandedItem = items.value[index];
    if (!expandedItem.logs) {
        expandedItem.expandLoading = true;
        const response = await axios.get(route('Ecommerce.Order.Data', expandedItem.code));
        expandedItem.logs = response.data.logs;
        expandedItem.items = response.data.items;
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
