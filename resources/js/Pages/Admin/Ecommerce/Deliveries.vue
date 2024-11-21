<template>
    <div>
        <AppLayout title="Deliveries" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Deliveries</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[['Deliveries', route('Blog.Tags')]]" />
                    </div>
                </div>

            </template>
            <div class="p-2 ">
                <div class="bg-white text-black/80 overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2 mb-5">
                    <h3 class="text-black/80 font-bold text-xl">Deliveries</h3>
                    <div class="grid grid-cols-5 gap-4">
                        <div class="col-span-2">
                            <form @submit.prevent="saveDeliveryForm">
                                <div class="mb-4">
                                    <InputLabel for="name">Name <span class="text-red-500">*</span></InputLabel>
                                    <TextInput id="name" v-model="deliveryForm.name" type="text"
                                        class="mt-1 block w-full" required />
                                </div>
                                <div class="mb-4">
                                    <InputLabel for="Image">Image <span class="text-red-500">*</span></InputLabel>
                                    <InputUrlImage id="Image" v-model="deliveryForm.image" type="text"
                                        class="mt-1 block w-full" required />
                                </div>
                                <div class="flex items-center justify-center mt-5">
                                    <button type="submit"
                                        class="px-5 py-2 rounded-lg text-white font-bold bg-purple-500">Save</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-span-3">
                            <table
                                class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md overflow-hidden">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Image</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in deliveries" :key="index"
                                        class="border-b hover:bg-gray-100 transition-colors">
                                        <td class="px-6 py-4 text-gray-700">{{ index + 1 }}</td>
                                        <td class="px-6 py-4 text-gray-700">{{ item.name }}</td>
                                        <td class="px-6 py-4">
                                            <img :src="item.image" alt="Delivery Image"
                                                class="w-32 h-auto object-cover rounded-lg shadow-sm" />
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-4">
                                                <button
                                                    class="p-2 rounded-full bg-purple-100 hover:bg-yellow-200 text-yellow-500 transition"
                                                    @click="() => {
                                                        deliveryForm.id = item.id;
                                                        deliveryForm.name = item.name;
                                                        deliveryForm.image = item.image;
                                                    }">
                                                    <Icon icon="fa6-solid:pen" />
                                                </button>
                                                <button
                                                    class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-500 transition"
                                                    @click="deleteDeliveryItem(item.id, item.name)">
                                                    <Icon icon="fa6-solid:trash" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>




                </div>
                <div class="bg-white text-black/80 overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2 mb-5">
                    <h3 class="text-black/80 font-bold text-xl">Delivery Countries</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <form @submit.prevent="saveDeliveryCountryForm">
                                <div class="mb-4">
                                    <InputLabel for="name">Name <span class="text-red-500">*</span></InputLabel>
                                    <TextInput id="name" v-model="deliveryCountryForm.name" type="text"
                                        class="mt-1 block w-full" required />
                                </div>

                                <div class="flex items-center justify-center mt-5">
                                    <button type="submit"
                                        class="px-5 py-2 rounded-lg text-white font-bold bg-purple-500">Save</button>
                                </div>
                            </form>
                        </div>
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md overflow-hidden">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in deliveryCountries" :key="index"
                                    class="border-b hover:bg-gray-100 transition-colors">
                                    <td class="px-6 py-4 text-gray-700">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ item.name }}</td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-4">
                                            <button
                                                class="p-2 rounded-full bg-purple-100 hover:bg-yellow-200 text-yellow-500 transition"
                                                @click="() => {
                                                    deliveryCountryForm.id = item.id;
                                                    deliveryCountryForm.name = item.name;
                                                }">
                                                <Icon icon="fa6-solid:pen" />
                                            </button>
                                            <button
                                                class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-500 transition"
                                                @click="deleteDeliveryCountryItem(item.id, item.name)">
                                                <Icon icon="fa6-solid:trash" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>


                <div class="bg-white text-black/80 overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2 mb-5">
                    <h3 class="text-black/80 font-bold text-xl">Delivery Prices</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="py-4">
                            <form @submit.prevent="saveDeliveryPriceForm">
                                <div class="mb-4">
                                    <InputLabel for="delivery_id">Delivery <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <select required id="delivery_id" v-model="deliveryPriceForm.delivery_id"
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option selected value=""> Select Option</option>
                                        <option v-for="(item, index) in deliveries" :key="index" :value="item.id">
                                            {{ item.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <InputLabel for="delivery_country_id">Delivery Country <span
                                            class="text-red-500">*</span>
                                    </InputLabel>
                                    <select required id="delivery_country_id"
                                        v-model="deliveryPriceForm.delivery_country_id"
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option selected value=""> Select Option</option>
                                        <option v-for="(item, index) in deliveryCountries" :key="index"
                                            :value="item.id">
                                            {{ item.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <InputLabel for="price">Price <span class="text-red-500">*</span>
                                    </InputLabel>
                                    <InputPrice id="price" required="true" v-model="deliveryPriceForm.price" />

                                </div>

                                <div class="flex items-center justify-center mt-5">
                                    <button type="submit"
                                        class="px-5 py-2 rounded-lg text-white font-bold bg-purple-500">Save</button>
                                </div>
                            </form>
                        </div>
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md overflow-hidden">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Delivery</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Delivery Country
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Price</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in deliveryPrices" :key="index"
                                    class="border-b hover:bg-gray-100 transition-colors">
                                    <td class="px-6 py-4 text-gray-700">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ item.delivery.name }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ item.delivery_country.name }}</td>
                                    <td class="px-6 py-4 text-gray-700">
                                        <InputPrice v-model="item.price" disabled />

                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-4">
                                            <button
                                                class="p-2 rounded-full bg-purple-100 hover:bg-yellow-200 text-yellow-500 transition"
                                                @click="() => {
                                                    deliveryPriceForm.id = item.id;
                                                    deliveryPriceForm.delivery_id = item.delivery_id;
                                                    deliveryPriceForm.delivery_country_id = item.delivery_country_id;
                                                    deliveryPriceForm.price = item.price;
                                                }">
                                                <Icon icon="fa6-solid:pen" />
                                            </button>
                                            <button
                                                class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-500 transition"
                                                @click="deleteDeliveryPriceItem(item.id, item.name)">
                                                <Icon icon="fa6-solid:trash" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>

            </div>



        </AppLayout>

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import { onMounted, ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputText from '@/Components/Input/InputText.vue';
import InputError from '@/Components/InputError.vue';
import InputUrlImage from '@/Components/Input/InputUrlImage.vue';
import InputPrice from '@/Components/Input/InputPrice.vue';

const isPageLoading = ref(false);
const deliveries = ref([]);
const deliveryCountries = ref([]);
const deliveryPrices = ref([]);
const deliveryForm = reactive({
    id: null,
    name: '',
    image: ''
})

const deliveryCountryForm = reactive({
    id: null,
    name: '',
})
const deliveryPriceForm = reactive({
    id: null,
    delivery_id: '',
    delivery_country_id: '',
    price: '',
})




const loadDataTable = () => {
    isPageLoading.value = true;
    axios.get(route('Ecommerce.Delivery.Data')).then(response => {
        deliveries.value = response.data.deliveries
        deliveryCountries.value = response.data.deliveryCountries
        deliveryPrices.value = response.data.deliveryPrices
    }).catch(error => {
        console.error(error);
    });
    isPageLoading.value = false;
}
onMounted(() => {
    loadDataTable();
})






const saveDeliveryForm = () => {
    let url = '';
    if (deliveryForm.id) {
        url = route('Ecommerce.Delivery.Update', deliveryForm.id)
    } else {
        url = route('Ecommerce.Delivery.Store')
    }

    axios.post(url, deliveryForm).then((response) => {
        toast.success(response.data.message, {
            autoClose: 1500,
        });
        deliveryForm.id = null
        deliveryForm.name = ''
        deliveryForm.image = ''
        loadDataTable();
    }).catch((e) => {
        toast.error(e.response.data.message, {
            autoClose: 1500,
        });
        console.log(e.message);
    })


}
const deleteDeliveryItem = (id, name) => {
    const isDelete = window.confirm(`Are you sure you want to delete ${name}?`);

    if (isDelete) {
        axios.post(route('Ecommerce.Delivery.Delete', id)).then((response) => {
            toast.success(response.data.message, {
                autoClose: 1500,
            });
            deliveryForm.id = null
            deliveryForm.name = ''
            deliveryForm.image = ''
            loadDataTable();
        }).catch((e) => {
            toast.error(e.response.data.message, {
                autoClose: 1500,
            });
            console.log(e.message);
        })
    }
};
const saveDeliveryCountryForm = () => {
    let url = '';
    if (deliveryCountryForm.id) {
        url = route('Ecommerce.Delivery.Country.Update', deliveryCountryForm.id)
    } else {
        url = route('Ecommerce.Delivery.Country.Store')
    }

    axios.post(url, deliveryCountryForm).then((response) => {
        toast.success(response.data.message, {
            autoClose: 1500,
        });
        deliveryCountryForm.id = null
        deliveryCountryForm.name = ''
        loadDataTable();

    }).catch((e) => {
        toast.error(e.response.data.message, {
            autoClose: 1500,
        });
        console.log(e.message);
    })


}
const deleteDeliveryCountryItem = (id, name) => {
    const isDelete = window.confirm(`Are you sure you want to delete ${name}?`);

    if (isDelete) {
        axios.post(route('Ecommerce.Delivery.Country.Delete', id)).then((response) => {
            toast.success(response.data.message, {
                autoClose: 1500,
            });
            deliveryForm.id = null
            deliveryForm.name = ''
            deliveryForm.image = ''
            loadDataTable();
        }).catch((e) => {
            toast.error(e.response.data.message, {
                autoClose: 1500,
            });
            console.log(e.message);
        })
    }
};





const saveDeliveryPriceForm = () => {
    let url = '';
    if (deliveryPriceForm.id) {
        url = route('Ecommerce.Delivery.Price.Update', deliveryPriceForm.id)
    } else {
        url = route('Ecommerce.Delivery.Price.Store')
    }

    axios.post(url, deliveryPriceForm).then((response) => {
        toast.success(response.data.message, {
            autoClose: 1500,
        });
        deliveryPriceForm.id = null
        deliveryPriceForm.delivery_country_id = ''
        deliveryPriceForm.delivery_id = ''
        deliveryPriceForm.price = ''
        loadDataTable();

    }).catch((e) => {
        toast.error(e.response.data.message, {
            autoClose: 1500,
        });
        console.log(e.message);
    })


}
const deleteDeliveryPriceItem = (id, name) => {
    const isDelete = window.confirm(`Are you sure you want to delete ${name}?`);

    if (isDelete) {
        axios.post(route('Ecommerce.Delivery.Price.Delete', id)).then((response) => {
            toast.success(response.data.message, {
                autoClose: 1500,
            });
            deliveryForm.id = null
            deliveryForm.name = ''
            deliveryForm.image = ''
            loadDataTable();
        }).catch((e) => {
            toast.error(e.response.data.message, {
                autoClose: 1500,
            });
            console.log(e.message);
        })
    }
};




</script>

<style></style>
