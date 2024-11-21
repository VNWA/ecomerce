<script setup>
import { onMounted, ref, computed } from 'vue';

// Định nghĩa props
const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    class: String,
    id: String,
    maxLength: {
        type: Number,
        default: 400
    }
});

// Định nghĩa emits
const emit = defineEmits(['update:modelValue']);

// Sử dụng ref cho textarea
const input = ref('');

// Đảm bảo textarea được focus khi có thuộc tính autofocus
onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

// Expose hàm focus ra ngoài
defineExpose({ focus: () => input.value?.focus() });

// Computed property để xử lý modelValue không bao giờ là null
const safeModelValue = computed(() => props.modelValue ?? '');
</script>

<template>
    <div class="relative">
        <div v-show="safeModelValue.length > 0" :class="{ 'text-red-500': safeModelValue.length >= props.maxLength }"
            class="absolute right-0 -top-1 bg-white px-1 text-xs">
            {{ safeModelValue.length }} / {{ props.maxLength }}
        </div>
        <textarea ref="input"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm min-h-32"
            :class="props.class" :id="props.id" :value="safeModelValue"
            @input="$emit('update:modelValue', $event.target.value)">
        </textarea>
    </div>
</template>
