<template>
    <div class="px-5 py-10 bg-white text-black min-h-screen h-full overscroll-auto">
        <div class="my-3 text-center flex items-center justify-center">
            <AuthenticationCardLogo />
        </div>
        <div class="grid grid-cols-12 gap-4">
            <!-- Left Section -->
            <div class="col-span-8">
                <div class="mb-4">
                    <InputLabel for="title">
                        Title <span class="text-red-500">*</span>
                    </InputLabel>
                    <TextInput id="title" :maxLength="150" v-model="formData.title" type="text"
                        class="mt-1 block w-full" />
                </div>

                <!-- Steps Section -->
                <div class="mb-4">
                    <InputLabel for="steps">Steps</InputLabel>
                    <div class="space-y-2">
                        <div v-for="(step, stepIndex) in formData.steps" :key="stepIndex" class="border p-2 rounded-md">
                            <div>

                                <InputLabel :for="'step-name-' + stepIndex">
                                    Step Name <span class="text-red-500">*</span>
                                </InputLabel>
                                <TextInput :id="'step-name-' + stepIndex" v-model="step.name" placeholder="Step Name"
                                    class="mt-1 block w-full" />
                            </div>
                            <div>

                                <InputLabel :for="'step-link-' + stepIndex">
                                    Step Link <span class="text-red-500">*</span>
                                </InputLabel>
                                <TextInput :id="'step-link-' + stepIndex" v-model="step.link" placeholder="Step link"
                                    class="mt-1 block w-full" />
                            </div>





                        </div>
                        <button @click="addStep"
                            class="mt-2 bg-green-500 hover:bg-green-600 text-white rounded-md px-4 py-2">
                            Add Step
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="col-span-4">
                <div class="sticky top-14 right-0 w-full h-full">
                    <button @click="handleSubmit"
                    class="flex w-full items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold">
                    <Icon icon="fa6-solid:floppy-disk"  /> Save
                </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import Multiselect from '@vueform/multiselect';
import axios from 'axios'; // Nhập axios
import InputUrlImage from '@/Components/Input/InputUrlImage.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

const productOptions = ref([]);

onMounted(async () => {
    try {
        const response = await axios.get('/vnwa/ecommerce/products/get-mini-products');
        productOptions.value = response.data; // Giả sử response.data chứa danh sách sản phẩm
    } catch (error) {
        console.error('Error fetching mini products:', error);
    }
});


// Khởi tạo dữ liệu form
const formData = reactive({
    title: '',
    steps: [] // Danh sách các bước
});
// https://www.youtube.com/watch?app=desktop&v=dCxSsr5xuL8
// https://youtu.be/dCxSsr5xuL8?si=J9C-W1h5a1hOb00_
// Thêm bước mới
const addStep = () => {
    formData.steps.push({
        name: '',
        link: '',
    });
};

// Xóa bước
const removeStep = (index) => {
    formData.steps.splice(index, 1);
};

// Xử lý khi nhấn nút Lưu
const handleSubmit = () => {
    if (!formData.title.trim() || !formData.steps.length) {
        toast.error('Please enter complete data', { autoClose: 1500 });
        return;
    }

    formData.steps.forEach(step => {
        step.slug = step.name.replace(/\s+/g, '-').toLowerCase();
    });
    const steps = JSON.stringify(formData.steps)

    const shortcode = `[YoutubeVideos {title="${formData.title}" data='${steps.replace(/'/g, "\\'")}'}  /]`

    try {
        if (window.opener) {
            // Gửi thông điệp chỉ một lần sau khi chọn file
            window.opener.postMessage(shortcode, window.location.origin);
            window.close(); // Đóng popup sau khi gửi dữ liệu
        } else {
            console.error("Không tìm thấy cửa sổ cha.");
        }
    } catch (error) {
        console.error("Lỗi khi gửi dữ liệu:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại", { autoClose: 1500 });
    }
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
