<template>
    <div>
        <span :class="badgeClass" class="px-2 py-1 rounded text-sm font-semibold capitalize">
            {{ value }}
        </span>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

// Định nghĩa props
const props = defineProps({
    type: {
        type: String, // Loại trạng thái (order, payment, etc.)
        required: true,
    },
    value: {
        type: String, // Giá trị của trạng thái (new, completed, etc.)
        required: true,
    },
});

// Định nghĩa các màu sắc dựa trên `type` và `value`
const badgeClass = computed(() => {
    const statusColors: Record<string, Record<string, string>> = {
        order: {
            new: "bg-blue-500 text-white",
            processing: "bg-yellow-500 text-white",
            shipped: "bg-purple-500 text-white",
            completed: "bg-green-500 text-white",
            cancelled: "bg-red-500 text-white",
            returned: "bg-gray-500 text-white",
        },
        payment: {
            pending: "bg-yellow-500 text-white",
            completed: "bg-green-500 text-white",
            cancelled: "bg-red-500 text-white",
            failed: "bg-gray-500 text-white",
        },
    };

    // Trả về lớp CSS phù hợp, mặc định nếu không có
    return statusColors[props.type]?.[props.value] || "bg-gray-300 text-black";
});
</script>
