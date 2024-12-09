<template>
    <div>
        <AppLayout title="Create Order" :isLoading="isPageLoading">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Order</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Order', route('Ecommerce.Order')], ['Create', route('Ecommerce.Order.Create')]]" />
                    </div>
                </div>
            </template>
            <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-12 gap-4 p-2">
                    <div class="lg:col-span-9 col-span-12">
                        <div class="bg-white shadow rounded-sm mb-3 p-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="(label, key) in formProfileFields" :key="key">

                                    <InputLabel :for="key">{{ label }} <span class="text-red-500">*</span></InputLabel>
                                    <TextInput :required="true" :id="key" v-model="formProfile[key]" type="text"
                                        class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="errors[key]" />


                                </div>
                                <div class="col-span-2">
                                    <InputLabel for="note">Note </InputLabel>

                                    <TextArea v-model="formProfile.note" rows="3" maxlength="1000" id="note" />
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
                                                                    <button type="button"
                                                                        @click="decrementQuantity(item.sku)"
                                                                        class="bg-gray-200 text-gray-800 px-2 rounded-md hover:bg-primary">
                                                                        -
                                                                    </button>
                                                                    <!-- Display quantity -->
                                                                    <span class="text-lg">{{ item.quantity }}</span>
                                                                    <!-- Button to increase quantity -->
                                                                    <button type="button"
                                                                        @click="incrementQuantity(item.sku)"
                                                                        class="bg-gray-200 text-gray-800 px-2 rounded-md hover:bg-primary">
                                                                        +
                                                                    </button>
                                                                </div>
                                                                <button type="button" @click="removeFromCart(item.sku)"
                                                                    class="h-8 w-8 me-3 flex items-center justify-center bg-red-600 rounded-md">
                                                                    <Icon icon="material-symbols:close-rounded"  class="text-white" />
                                                                </button>
                                                            </div>
                                                        </ProductItem>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="border relative max-h-[600px] overflow-auto px-3">
                                            <div class="sticky top-0 left-0 w-full bg-white p-3">
                                                <TextInput placeholder="Search product"
                                                    @input="searchProducts($event)" />
                                            </div>
                                            <div>
                                                <ul class="py-3">
                                                    <li v-for="(item, index) in products" :key="index">
                                                        <div class="mb-3">
                                                            <ProductItem :product="item">
                                                                <button type="button" @click="addToCart(item)"
                                                                    class="h-8 w-8 me-3 flex items-center justify-center bg-green-600 rounded-md">
                                                                    <Icon icon="material-symbols:add"
                                                                        class="text-white" />
                                                                </button>
                                                            </ProductItem>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white shadow rounded-sm mb-3 p-2 text-black/80">
                            <div class="sticky top-0 left-0 w-full bg-white p-3">
                                <h3 class="font-bold text-lg border-b">
                                    Coupon
                                </h3>
                            </div>

                            <div class="p-3">

                                <Select :options="coupons" v-model="couponCode" lable="code" value="code" />
                            </div>
                        </div>

                        <div class="bg-white shadow rounded-sm mb-3 p-2  text-black/80">
                            <div class="sticky top-0 left-0 w-full bg-white p-3">
                                <h3 class="font-bold text-lg border-b">
                                    Delivery
                                </h3>
                            </div>

                            <div class="mb-5 flex items-center justify-center">
                                <Dropdown type="thinSelect" :options="deliveries" v-model="deliveryCountry"
                                    width="500px" @change="deliveryPrice = {}" />
                            </div>
                            <div>
                                <div v-for="(item, index) in deliveries" :key="index">
                                    <div v-if="item.value == deliveryCountry">
                                        <label v-for="(value, n) in item.deliveries" :key="n" :for="'delivery' + n"
                                            class="mb-3">
                                            <div @click="deliveryPrice = value"
                                                class="grid grid-cols-8 gap-4 border border-gray-300 border-solid justify-center items-center my-4 cursor-pointer"
                                                :class="{ 'border-boldPrimary text-boldPrimary font-bold': deliveryPrice == value }">
                                                <div class="flex items-center justify-center">
                                                    <input :id="'delivery' + n" type="radio" :value="value"
                                                        v-model="deliveryPrice" class="radio-square" />
                                                </div>
                                                <div class="col-span-2 flex items-center justify-center">
                                                    <img alt="image" class="h-20 w-auto" height="70"
                                                        :src="value.image" />
                                                </div>
                                                <div class="col-span-4">
                                                    <span>{{ value.name }}</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm flex items-center justify-center">
                                                        {{ formatCurrency(value.price) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
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
                                        <label :for="'shipingMethod' + index">
                                            <div class="border cursor-pointer border-gray-300"
                                                @click="paymentMethod = item.value"
                                                :class="{ 'border-boldPrimary': item.value == paymentMethod }">
                                                <div class="p-5">
                                                    <div
                                                        class="object-contain max-w-full h-20 flex items-center justify-center">
                                                        <img loading="lazy" alt="image" width="150"
                                                            class="max-w-full max-h-full" :src="item.image" />
                                                    </div>
                                                    <div class="flex items-center justify-center">
                                                        <input type="radio" :id="'shipingMethod' + index"
                                                            :value="item.value" v-model="paymentMethod"
                                                            class="radio-square" />
                                                    </div>
                                                    <div class="text-sm text-center mt-1">{{ item.desc }}</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
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
                                <div class="border p-3 mb-5 bg-slate-50">
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
                                <div class="flex items-center justify-end gap-4">
                                    <button type="submit"
                                        class="flex items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold">
                                        <Icon icon="fa6-solid:floppy-disk"  /> Save
                                    </button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </form>
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

    axios.get(route('Ecommerce.Order.loadOrderFormFileds')).then(res => {
        products.value = res.data.products
        deliveries.value = res.data.deliveries
        payments.value = res.data.payments
        coupons.value = res.data.coupons
    }).catch(error => {
        console.log(error)
    }).finally(() => {
        isPageLoading.value = false;
    });
}
onMounted(() => {
    loadDataFileds();
})
const searchProducts = debounce(async (event) => {
    const query = event.target.value;
    if (query.trim() === '') return; // Nếu không có gì để tìm kiếm, tránh gọi API
    try {
        const response = await axios.get(route('Ecommerce.Order.FindProducts', query));
        products.value = response.data.products;
    } catch (error) {
        console.log(error);
    }
}, 500);

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
    if (Object.keys(deliveryPrice.value).length) {
        orderSubtotal += Number(deliveryPrice.value.price);
    }

    // Đảm bảo `orderTotal` không âm
    orderTotal.value = Math.max(orderSubtotal, 0);
};

// Theo dõi sự thay đổi của giỏ hàng
watch(carts, (newCarts) => {
    cartTotal.value = newCarts.reduce((total, cartItem) => {
        return total + cartItem.price_new * cartItem.quantity; // Sử dụng price_new và số lượng
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
    updateTotal(); // Cập nhật tổng sau khi thay đổi mã giảm giá
});



const addToCart = (item, quantity = 1) => {
    const cartItem = {
        image: item.image,
        name: item.name,
        slug: item.slug,
        sku: item.sku,
        price: item.price,
        price_new: item.price_new,
        quantity: quantity,
    };

    // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
    const existingItemIndex = carts.value.findIndex(cart => cart.sku === cartItem.sku);

    if (existingItemIndex !== -1) {
        // Nếu đã tồn tại, tăng số lượng
        carts.value[existingItemIndex].quantity += quantity;
    } else {
        // Nếu chưa tồn tại, thêm sản phẩm vào giỏ hàng
        carts.value.push(cartItem);
    }
};
const incrementQuantity = (sku) => {
    const existingItemIndex = carts.value.findIndex(cart => cart.sku === sku);

    if (existingItemIndex !== -1) {
        // Nếu đã tồn tại, tăng số lượng
        carts.value[existingItemIndex].quantity += 1;
    }
}
const decrementQuantity = (sku) => {
    const existingItemIndex = carts.value.findIndex(cart => cart.sku === sku);

    if (existingItemIndex !== -1) {
        // Nếu đã tồn tại, tăng số lượng
        if (carts.value[existingItemIndex].quantity > 1) {
            carts.value[existingItemIndex].quantity -= 1;

        }
    }
}
const removeFromCart = (sku) => {
    const existingItemIndex = carts.value.findIndex(cart => cart.sku === sku);

    if (existingItemIndex !== -1) {
        // Xóa phần tử bằng splice
        carts.value.splice(existingItemIndex, 1);
    }
};



const clearError = () => {
    for (const key in errors) {
        if (errors.hasOwnProperty(key)) {
            errors[key] = '';
        }
    }
};



const handleSubmit = () => {
    clearError();
    isPageLoading.value = true;
    axios.post(route('Ecommerce.Order.Store'), {
        first_name: formProfile.first_name,
        last_name: formProfile.last_name,
        email: formProfile.email,
        phone: formProfile.phone,
        post_code: formProfile.post_code,
        city: formProfile.city,
        address: formProfile.address,
        address_number: formProfile.address_number,
        note: formProfile.note,
        paymentMethod: paymentMethod.value,
        delivery: deliveryPrice.value,
        coupon: coupon.value,
        cartItems: carts.value,
        total: orderTotal.value,
    }).then(res => {
        toast.success(res.data.message);

    }).catch(error => {
        if (error.response.data.errors) {
            const errorKeys = Object.keys(error.response.data.errors);
            errorKeys.forEach(key => {
                if (key in errors) {
                    errors[key] = error.response.data.errors[key][0];
                }
            });
            toast.error(error.message);

        }
    }).finally(() => {
        isPageLoading.value = false;
    });
};




</script>

<style scoped>
/* Add any custom styles here if needed */
</style>
