<template>
    <input ref="input" type="text"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="isFocused ? numericValue : formattedValue"
        @focus="onFocus"
        @input="onInput"
        @blur="updateValue" />
</template>

<script setup>
import { ref, watch, onMounted, defineProps, defineEmits } from 'vue';
import { formatCurrency } from '@/utils/helpers';

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0
    },
    currency: {
        type: String,
        default: 'EUR'
    },
});

const emit = defineEmits(['update:modelValue']);
const input = ref(null);
const formattedValue = ref('');
const numericValue = ref(0);
const isFocused = ref(false);



// Cập nhật giá trị khi người dùng nhấn vào ô nhập
const onInput = (event) => {
    const inputValue = event.target.value;

    // Lấy giá trị nhập vào và loại bỏ các ký tự không phải số
    numericValue.value = parseFloat(inputValue.replace(/[^0-9]+/g, '')) || 0;

    // Cập nhật giá trị hiển thị
    formattedValue.value = inputValue; // Giữ nguyên giá trị nhập vào
};

// Khi ô nhập được focus
const onFocus = () => {
    isFocused.value = true;
    numericValue.value = props.modelValue; // Hiển thị giá trị số nguyên
    formattedValue.value = numericValue.value.toString(); // Chuyển đổi thành chuỗi
};

// Cập nhật giá trị khi mất focus
const updateValue = () => {
    isFocused.value = false;
    const numericValueParsed = parseFloat(formattedValue.value.replace(/[^0-9.-]+/g, ''));
    formattedValue.value = formatCurrency(isNaN(numericValueParsed) ? 0 : numericValueParsed);
    emit('update:modelValue', isNaN(numericValueParsed) ? 0 : numericValueParsed);
};

// Watch để cập nhật giá trị khi modelValue thay đổi
watch(() => props.modelValue, (newValue) => {
    formattedValue.value = formatCurrency(newValue);
    numericValue.value = newValue; // Cập nhật giá trị số
});

// Đặt giá trị ban đầu
formattedValue.value = formatCurrency(props.modelValue);
numericValue.value = props.modelValue;

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<style>
/* Style tùy chọn của bạn */
</style>
