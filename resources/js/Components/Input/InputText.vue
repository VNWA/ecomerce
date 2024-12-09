<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: String,
    class: String,
    id: String,
    type: String,
    maxLength: {
        type: Number,
        default: 400
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

const handleChange = (event) => {
    emit('change', event.target.value);
};

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input ref="input"
        :class="props.class, { 'border-red-500 focus:ring-red-500': modelValue && props.maxLength && modelValue.length >= props.maxLength }"
        :id="props.id" :type="props.type"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pe-10 w-full"
        :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" @change="handleChange">
</template>
