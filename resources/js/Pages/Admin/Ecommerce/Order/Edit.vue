<template>
    <div>
        <AppLayout title="Edit Info Order" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Info Order</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Order', route('Ecommerce.Order')], ['Create', route('Ecommerce.Order.Create')]]" />
                    </div>
                </div>
            </template>
            <div class="grid grid-cols-12 gap-4 p-2">
                <div class="lg:col-span-9 col-span-12">
                    <div class="bg-white shadow rounded-sm mb-3 p-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="(label, key) in formProfileFields" :key="key">

                                <InputLabel :for="key">{{ label }} <span class="text-red-500">*</span></InputLabel>
                                <TextInput disabled :required="true" :id="key" v-model="formProfile[key]" type="text"
                                    class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="errors[key]" />


                            </div>
                            <div class="col-span-2">
                                <InputLabel for="note">Note </InputLabel>

                                <TextArea disabled v-model="formProfile.note" rows="3" maxlength="1000" id="note" />
                            </div>

                        </div>

                    </div>
                    <div class="bg-white shadow rounded-sm mb-3 p-2 text-black/80">
                        <div class="min-h-[500px]">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="border h-full max-h-[600px] overflow-auto ">
                                        <div class="sticky top-0 left-0 w-full bg-white p-3">
                                            <h3 class="font-bold text-lg border-b">
                                                Cart ({{ carts.length }})
                                            </h3>
                                        </div>

                                        <ul class="mt-3 p-3">
                                            <li v-for="(item, index) in carts" :key="index">
                                                <div class="mb-3">
                                                    <ProductItem :product="item">
                                                        <div class="flex items-center justify-center gap-4 me-3">
                                                            <div class="flex items-center space-x-3">

                                                                <!-- Display quantity -->
                                                                <span class="text-lg">{{ item.quantity }}</span>

                                                            </div>

                                                        </div>
                                                    </ProductItem>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div>
                                    <div class="bg-white shadow rounded-sm mb-3 p-2 text-black/80">
                                        <div class="sticky top-0 left-0 w-full bg-white p-3">
                                            <h3 class="font-bold text-lg border-b">
                                                Coupon
                                            </h3>
                                        </div>

                                        <div class="p-3">
                                            <div class="flex  justify-center items-center  text-black/80"
                                                v-if="order.coupon && order.coupon.code && order.coupon.type && order.coupon.value">

                                                <div
                                                    class="border  border-solid bg-orange-500 text-white px-5 rounded-md text-lg mt-3 flex items-center justify-center gap-4  font-normal ">
                                                    {{ order.coupon.code }}
                                                    <div v-if="order.coupon.type == 'percentage'">
                                                        - {{ order.coupon.value }} %
                                                    </div>
                                                    <div v-else-if="order.coupon.type == 'amount'">
                                                        - {{ $currency(order.coupon.value) }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white shadow rounded-sm mb-3 p-2  text-black/80">
                                        <div class="sticky top-0 left-0 w-full bg-white p-3">
                                            <h3 class="font-bold text-lg border-b">
                                                Delivery
                                            </h3>
                                        </div>


                                        <label aria-disabled="true" :for="'delivery'" class="mb-3">
                                            <div class="grid grid-cols-8 gap-4 border border-gray-300 border-solid justify-center items-center my-4 cursor-pointer"
                                                :class="{ 'border-boldPrimary text-boldPrimary font-bold': true }">

                                                <div class="col-span-2 flex items-center justify-center">
                                                    <img alt="image" class="h-20 w-auto" height="70"
                                                        :src="order.delivery.image" />
                                                </div>
                                                <div class="col-span-4">
                                                    <span>{{ order.delivery.name }}</span>

                                                </div>
                                                <div>
                                                    <div class="text-sm flex items-center justify-center">
                                                        {{ formatCurrency(order.delivery.price) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="bg-white shadow rounded-sm mb-3 p-2 text-black/80">
                                        <div class="sticky top-0 left-0 w-full bg-white p-3">
                                            <h3 class="font-bold text-lg border-b">
                                                Payment
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="flex justify-center items-center gap-4">
                                                <div v-for="(item, index) in payments" :key="index">
                                                    <label v-if="item.value == paymentMethod">
                                                        <div class="border cursor-pointer border-gray-300"
                                                            :class="{ 'border-boldPrimary': item.value == paymentMethod }">
                                                            <div class="p-5">
                                                                <div
                                                                    class="object-contain max-w-full h-20 flex items-center justify-center">
                                                                    <img loading="lazy" alt="image" width="150"
                                                                        class="max-w-full max-h-full"
                                                                        :src="item.image" />
                                                                </div>
                                                                <div class="flex items-center justify-center">
                                                                    <input type="radio" :id="'shipingMethod' + index"
                                                                        :value="item.value" v-model="paymentMethod"
                                                                        class="radio-square" />
                                                                </div>
                                                                <div class="text-sm text-center mt-1">{{ item.desc }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>


                </div>

                <div class="lg:col-span-3 col-span-12 ">
                    <div class="min-h-screen h-full  relative bg-red-50">

                        <div class="sticky z-10 top-0 left-0 bg-white shadow rounded-sm mb-3 p-2"
                            v-if="carts.length > 0 && paymentMethod && Object.keys(deliveryPrice).length">
                            <div class="py-2 border-b border-black/10 mb-5 text-black">
                                <h2 class="text-lg font-bold">Publish</h2>
                            </div>
                            <div class="border p-3 mb-5 bg-slate-50 ">
                                <div class="mb-4 flex items-center justify-center" v-if="order.status != 'completed'">
                                    <button @click="updateOrder('status.cancel')"
                                        class="border px-3 py-2 text-center text-lg bg-red-400 text-black/50 hover:bg-red-500 hover:text-white rounded-md font-bold">
                                        Confirm cancelled
                                    </button>
                                </div>
                                <div class="flex items-center justify-between gap-4 px-3 mb-3">
                                    <h5 class="text-black/70 text-lg">
                                        Cart Total
                                    </h5>
                                    <h5 class="text-black/80 text-lg font-medium">
                                        {{ formatCurrency(cartTotal) }}

                                    </h5>
                                </div>
                                <div class="flex items-center justify-between gap-4 px-3 mb-3" v-if="coupon">
                                    <h5 class="text-black/70 text-lg">
                                        Coupon:
                                    </h5>
                                    <h5 class="text-black/80 text-lg font-medium">
                                        <span v-if="coupon.type == 'percentage'">- {{ coupon.value }} %</span>
                                        <span v-else-if="coupon.type == 'amount'">- {{ formatCurrency(coupon.value
                                        ) }} </span>

                                    </h5>
                                </div>
                                <div v-if="deliveryPrice != {}"
                                    class="flex items-center justify-between gap-4 px-3 mb-3">
                                    <h5 class="text-black/70 text-lg">
                                        Delivery Price
                                    </h5>
                                    <h5 class="text-black/80 text-lg font-medium">
                                        {{ formatCurrency(deliveryPrice.price) }}
                                    </h5>
                                </div>
                                <div class="flex items-center justify-between gap-4 px-3 mb-3 pt-4 border-t">
                                    <h5 class="text-black/70 text-lg">
                                        Order Total
                                    </h5>
                                    <h5 class="text-black/80 text-lg font-medium">
                                        {{ formatCurrency(orderTotal) }}
                                    </h5>
                                </div>

                            </div>
                            <div class="my-3">
                                <ul>
                                    <li class="flex items-center justify-start gap-4 text-black mb-3">
                                        Order Status:
                                        <StatusBadges type="order" :value="order.status" />
                                    </li>
                                    <li class="flex items-center justify-start gap-4 text-black mb-3">
                                        Payment Status:
                                        <StatusBadges type="payment" :value="order.payment_status" />
                                    </li>
                                </ul>
                            </div>
                            <div class="py-3 mb-10 flex items-center justify-center"
                                v-if="!isPageLoading && order.status != 'completed'">
                                <!-- Kiểm tra nếu đơn hàng đang ở trạng thái "new" và thanh toán đang "pending" -->
                                <div v-if="order.payment_status == 'pending' && order.status == 'new'">
                                    <button @click="updateOrder('payment.completed')"
                                        class="border px-3 py-2 text-center text-lg bg-green-500  text-white rounded-md font-bold">
                                        <Icon icon="hugeicons:payment-success-02"
                                            class="text-white font-bold text-xl" /> Confirm
                                        Payment

                                    </button>
                                </div>

                                <div v-if="order.status == 'processing'">
                                    <button @click="updateOrder('status.shipped')"
                                        class="border px-3 py-2 text-center text-lg bg-orange-500 text-white rounded-md font-bold">
                                        <Icon icon="iconamoon:delivery-fill" class="text-white font-bold text-xl" />
                                        Confirm Shipment
                                    </button>
                                </div>

                                <div v-if="order.status == 'shipped'">
                                    <button @click="updateOrder('status.completed')"
                                        class="border px-3 py-2 text-center text-lg bg-blue-500 text-white rounded-md font-bold">
                                        <Icon icon="hugeicons:safe-delivery-01" class="text-white font-bold text-xl" />
                                        Confirm Delivery
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
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import { ref, reactive, watch, onMounted } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import InputPrice from '@/Components/Input/InputPrice.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { formatCurrency } from '@/utils/helpers';
import Dropdown from '@/Components/Input/Dropdown.vue';
import ProductItem from '@/Components/ProductItem.vue';
import { debounce } from 'lodash';
import Select from '@/Components/Select.vue';
import StatusBadges from '@/Components/StatusBadges.vue';
const props = defineProps({
    order: {
        type: Object,
        default: {}
    }
})
const isPageLoading = ref(false);
const formProfile = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    post_code: '',
    city: '',
    address: '',
    address_number: '',
    note: ''
});
const formProfileFields = {
    first_name: 'First name',
    last_name: 'Last name',
    post_code: 'Post code',
    address: 'Address ',
    address_number: 'Number Address',
    city: 'City',
    phone: 'Phone',
    email: 'E-mail'
};
const errors = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    post_code: '',
    city: '',
    address: '',
    address_number: '',
    note: ''
});
const products = ref([]);
const deliveries = ref([]);
const payments = ref([]);
const paymentMethod = ref('');
const deliveryPrice = ref({});
const deliveryCountry = ref('');
const coupons = ref([]);
const couponCode = ref('');
const coupon = ref(null);
const carts = ref([]);
const cartTotal = ref(0);
const orderTotal = ref(0);

const loadDataFileds = async () => {
    isPageLoading.value = true;

    await axios.get(route('Ecommerce.Order.loadOrderFormFileds')).then(res => {
        products.value = res.data.products
        deliveries.value = res.data.deliveries
        payments.value = res.data.payments
        coupons.value = res.data.coupons
    }).catch(error => {
        console.log(error)
    }).finally(() => {
        isPageLoading.value = false;
    });


    formProfile.first_name = props.order.first_name
    formProfile.last_name = props.order.last_name
    formProfile.email = props.order.email
    formProfile.phone = props.order.phone
    formProfile.post_code = props.order.post_code
    formProfile.city = props.order.city
    formProfile.address = props.order.address
    formProfile.address_number = props.order.address_number
    formProfile.note = props.order.note
    paymentMethod.value = props.order.payment_method
    deliveryPrice.value = props.order.delivery
    coupon.value = props.order.coupon
    carts.value = props.order.items
    if (props.order.coupon) {

        couponCode.value = props.order.coupon.code
    }

    updateTotal();
}
onMounted(() => {

    loadDataFileds();
})

const updateTotal = () => {
    let orderSubtotal = cartTotal.value;
    // Áp dụng mã giảm giá (nếu có)
    if (coupon.value && coupon.value.code) {
        const discountValue = Number(coupon.value.value);
        if (coupon.value.type === 'percentage') {
            orderSubtotal -= (orderSubtotal * discountValue) / 100;
        } else if (coupon.value.type === 'fixed') {
            orderSubtotal -= discountValue;
        }
    }

    // Cộng phí vận chuyển (nếu có)
    if (Object.keys(deliveryPrice).length) {
        orderSubtotal += Number(deliveryPrice.value.price);
    }

    // Đảm bảo `orderTotal` không âm
    orderTotal.value = Math.max(orderSubtotal, 0);
};

// Theo dõi sự thay đổi của giỏ hàng
watch(carts, async (newCarts) => {
    cartTotal.value = newCarts.reduce((total, cartItem) => {
        if (cartItem.price_new) {

            return total + cartItem.price_new * cartItem.quantity; // Sử dụng price_new và số lượng
        } else {
            return total + cartItem.price * cartItem.quantity; // Sử dụng price_new và số lượng

        }
    }, 0);
    updateTotal();
}, { deep: true }); // Thêm deep: true để theo dõi thay đổi sâu trong mảng

// Theo dõi sự thay đổi của couponCode
watch(couponCode, (newValue) => {
    const existingItem = coupons.value.find(couponItem => couponItem.code === newValue);
    if (existingItem) {
        coupon.value = existingItem; // Cập nhật coupon nếu tìm thấy
    } else {
        coupon.value = null; // Nếu không tìm thấy coupon
    }
    updateTotal(); // Cập nhật tổng sau khi thay đổi mã giảm giá
});
watch(deliveryPrice, (newValue) => {
    console.log(newValue)
    updateTotal(); // Cập nhật tổng sau khi thay đổi mã giảm giá
});


const updateOrder = (type) => {
    isPageLoading.value = true;

    const confirmationMessages = {
        'payment.completed': 'Confirm this order has been paid and move to preparation status.',
        'status.cancelled': 'Confirm this order has been cancelled.',
        'status.shipped': 'Confirm this order is in transit.',
        'status.completed': 'Confirm this order has been completed.',
    };

    if (confirmationMessages[type] && confirm(confirmationMessages[type])) {
        updateOrderAction(type);
    } else {
        isPageLoading.value = false;
    }
}

const updateOrderAction = (type) => {
    isPageLoading.value = true;
    axios.post(route('Ecommerce.Order.Update', [props.order.id, type])).then(res => {
        location.reload();
    }).catch(e => {
        console.error(e);
    }).finally(() => {
        isPageLoading.value = false;
    });
}


</script>

<style scoped>
/* Add any custom styles here if needed */
</style>
