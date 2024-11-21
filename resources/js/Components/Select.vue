<template>

    <select :value="modelValue" @change="$emit('update:modelValue', $event.target.value)"
        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-black/80">
        <option selected value=""> Null</option>
        <option v-for="(item, index) in options" :key="index" :value="item[value]" class="capitalize">
            {{ item[lable] }}
        </option>
    </select>
</template>

<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    options: {
        type: Array,
        default: []
    },
    lable: {
        type: String,
        default: 'name'
    },
    value: {
        type: String,
        default: 'value'
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);



defineExpose({ focus: () => input.value.focus() });
</script>

<style></style>
