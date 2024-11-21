<template>
    <div class="card justify-center">

        <Tree
            v-model:selectionKeys="value"
            :value="transformedOptions"
            :expandedKeys="expandedKeys"
            selectionMode="checkbox"
            class="w-full md:w-[30rem]"
        >
        </Tree>
    </div>
</template>

<script setup>
import { watch, computed, ref, nextTick } from 'vue';  // Thêm nextTick ở đây
import Tree from 'primevue/tree';
const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    data: {
        type: Array,
        default: () => []
    },
});

const value = ref({});

// Đồng bộ hóa giá trị lựa chọn với modelValue
const isUpdatingFromModelValue = ref(false);

watch(() => props.modelValue, (newValue) => {
    if (!isUpdatingFromModelValue.value) {
        value.value = newValue.reduce((acc, id) => {
            acc[id] = { "checked": true };
            return acc;
        }, {});
    }
}, { immediate: true, deep: true });

// Đồng bộ hóa giá trị value với modelValue
// watch(value, (newValue) => {
//     isUpdatingFromModelValue.value = true;
//     const selectedKeys = Object.keys(newValue).filter(key => newValue[key].checked);
//     emit('update:modelValue', selectedKeys);
//     // Sử dụng nextTick để đảm bảo rằng việc cập nhật không gây ra vòng lặp vô hạn
//     nextTick(() => {
//         isUpdatingFromModelValue.value = false;
//     });
// }, { deep: true });
watch(value, (newValue) => {
    isUpdatingFromModelValue.value = true;

    // Lấy cả những key có checked là true hoặc partialChecked là true
    const selectedKeys = Object.keys(newValue).filter(key => {
        return newValue[key].checked || newValue[key].partialChecked;
    });

    emit('update:modelValue', selectedKeys);

    // Sử dụng nextTick để đảm bảo rằng việc cập nhật không gây ra vòng lặp vô hạn
    nextTick(() => {
        isUpdatingFromModelValue.value = false;
    });
}, { deep: true });



// Biến đổi dữ liệu thành cấu trúc cây
const transformedOptions = computed(() => {
    const transformChildren = (children) => {
        return children.map(child => ({
            label: child.name,
            value: child.id,
            key: child.id,
            children: child.children ? transformChildren(child.children) : null,
        }));
    };

    return props.data.map(node => ({
        label: node.name,
        value: node.id,
        key: node.id,
        children: node.children ? transformChildren(node.children) : null,
    }));
});

// Tính toán expandedKeys để tự động mở rộng tất cả các nút
const expandedKeys = computed(() => {
    const keys = {};

    const gatherKeys = (nodes) => {
        nodes.forEach(node => {
            keys[node.key] = true;
            if (node.children) {
                gatherKeys(node.children);
            }
        });
    };

    gatherKeys(transformedOptions.value);
    return keys;
});

</script>
