<template>
    <div class="inline-block" :class="props.class">


        <div class="relative border min-w-32 w-auto h-44 border-purple-400 flex items-center justify-center rounded-md rounded-b-none bg-black/5 hover:bg-black/10 cursor-pointer"
            @click="openFileManager">
            <img v-if="url_image" :src="url_image" class="h-44 max-h-32 w-auto" alt="Photo does not exist" />
            <div v-else class="inline-flex items-center justify-center">
                <Icon icon="fa6-solid:image" class="text-4xl text-black/80" />
            </div>
        </div>

        <div class="relative formkit-field">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <Icon icon="fa6-solid:image" />
            </div>
            <input :id="props.id" v-model="url_image" @input="emit('update:modelValue', url_image)"
                class="formkit-input bg-gray-50 border border-gray-300 w-full text-gray-900 text-sm rounded-lg focus:ring-stone-500 focus:border-stone-500 block pl-10 p-2.5     rounded-t-none"
                name="email_address" aria-label="Đường dẫn ảnh" placeholder="Đường dẫn ảnh" required="" type="text" />

        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount, ref, watch } from "vue";
const props = defineProps({
    modelValue: String,
    id: String,
    class: String,
    desc: String,
});

const emit = defineEmits(['update:modelValue']);

// Khởi tạo biến url_image với giá trị ban đầu từ modelValue
const url_image = ref(props.modelValue);

// Đồng bộ giá trị từ props.modelValue vào url_image
watch(() => props.modelValue, (newValue) => {
    url_image.value = newValue;
});

// Đồng bộ ngược từ url_image ra ngoài thông qua emit
watch(url_image, (newValue) => {
    emit('update:modelValue', newValue);
});

const openFileManager = () => {
    const width = window.innerWidth;
    const height = window.innerHeight;
    window.open(
        route('Media.Popup'),
        '_blank',
        `width=${width},height=${height},top=0,left=0`
    );

    const handleMessage = (event) => {
        if (event.origin !== window.location.origin) return; // Kiểm tra bảo mật
        const files = event.data;
        if (Array.isArray(files) && files.length > 0) {
            url_image.value = files[0].path; // Lấy đường dẫn của file đầu tiên
            window.removeEventListener('message', handleMessage);
        } else {
            console.log("VNWA File Manager Popup Run ....")
        }
    };

    // Đăng ký sự kiện message
    window.addEventListener('message', handleMessage);

    // Hủy đăng ký sự kiện khi component bị unmount
    onBeforeUnmount(() => {
        window.removeEventListener('message', handleMessage);
    });
};
</script>
