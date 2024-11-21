<template>
    <TreeSelect v-model="value" :options="transformedOptions" placeholder="Select Item"
                class="md:w-80 w-full capitalize bg-white text-black" />
  </template>

  <script setup>
  import { watch, computed, ref } from 'vue';
  import { defineProps, defineEmits } from 'vue';
  import TreeSelect from 'primevue/treeselect';

  const props = defineProps({
    modelValue: [Number, null], // Đảm bảo rằng đây là Number hoặc null
    options: Array, // Đảm bảo options là một mảng các đối tượng
  });

  const emit = defineEmits();

  // Khởi tạo value như một đối tượng dựa trên modelValue nếu modelValue không phải là null
  const value = ref(props.modelValue !== null ? { [props.modelValue]: true } : null);

  // Theo dõi sự thay đổi của modelValue và cập nhật value
  watch(() => props.modelValue, (newValue) => {
    if (newValue !== null) {
      value.value = { [newValue]: true };
    } else {
      value.value = null;
    }
  });

  // Tính toán transformedOptions từ props.options
  const transformedOptions = computed(() => {
    let options = [];

    // Thêm lựa chọn null nếu cần thiết
    options.push({ label: 'None', value: null, key: 'null' });

    if (props.options && props.options.length > 0) {
      options = options.concat(props.options.map(node => ({
        label: node.name, // Tên hiển thị trong dropdown
        value: node.id, // Giá trị được chọn
        key: node.id, // Khóa duy nhất của lựa chọn, có thể là id hoặc một trường khác tương tự
        children: node.children ? transformChildren(node.children) : null, // Nếu có children, tiến hành đệ quy biến đổi
      })));
    }

    return options;
  });

  // Hàm đệ quy để biến đổi các children
  function transformChildren(children) {
    return children.map(child => ({
      label: child.name,
      value: child.id,
      key: child.id,
      children: child.children ? transformChildren(child.children) : null,
    }));
  }

  // Watcher để theo dõi thay đổi của value và emit event
  watch(value, (newValue) => {
    if (newValue !== null) {
      const selectedKey = Object.keys(newValue)[0];
      emit('update:modelValue', parseInt(selectedKey, 10));
    } else {
      emit('update:modelValue', null);
    }
  });
  </script>

  <style></style>
