<template>
    <div class="px-5 py-4 bg-white min-h-screen overflow-y-scroll text-black">
        <div class="my-3 text-center flex items-center justify-center">
            <AuthenticationCardLogo />
        </div>
        <div class="p-3">
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

                    <div class="mb-4">
                        <InputLabel for="desc">
                            Description <span class="text-red-500">*</span>
                        </InputLabel>
                        <textarea id="desc" v-model="formData.desc"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="5"></textarea>
                    </div>

                    <!-- Links Section -->
                    <div class="mb-4">
                        <InputLabel for="links">Links</InputLabel>
                        <div class="space-y-2">
                            <div v-for="(link, index) in formData.links" :key="index"
                                class="flex items-center space-x-2">
                                <input type="text" v-model="link.name" placeholder="Link Name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                <input type="url" v-model="link.value" placeholder="Link URL"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                <button @click="removeLink(index)"
                                    class="bg-red-500 hover:bg-red-600 text-white rounded-md px-2 py-1">
                                    Remove
                                </button>
                            </div>
                            <button @click="addLink"
                                class="mt-2 bg-green-500 hover:bg-green-600 text-white rounded-md px-4 py-2">
                                Add Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-span-4">
                    <div class="mb-4">
                        <InputLabel for="InputUrlImage" value="Image" />
                        <InputUrlImage id="InputUrlImage" class="mt-1" v-model="formData.image" />
                    </div>

                    <button @click="handleSubmit"
                        class="flex items-center justify-center bg-purple-500 hover:bg-purple-500/80 rounded-md px-5 py-2 min-w-24 text-white text-lg font-bold">
                                                <Icon icon="fa6-solid:floppy-disk" />
                        <Icon icon="fa6-solid:floppy-disk"  /> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, defineEmits } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputUrlImage from '@/Components/Input/InputUrlImage.vue'
import TextInput from '@/Components/TextInput.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';



// Khởi tạo dữ liệu form
const formData = reactive({
    title: '',
    image: '',
    desc: '',
    links: [] // Danh sách các link
})

// Thêm link mới
const addLink = () => {
    formData.links.push({ name: '', value: '' })
}

// Xóa link
const removeLink = (index) => {
    formData.links.splice(index, 1)
}

// Xử lý khi nhấn nút Lưu
const handleSubmit = () => {
    // Kiểm tra dữ liệu đầu vào
    if (!formData.title.trim() || !formData.image.trim() || !formData.desc.trim()) {
        toast.error('Please enter complete data', { autoClose: 1500 })
        return false
    }

    // Tạo shortcode với dữ liệu đã loại bỏ khoảng trắng thừa
    const linksString = JSON.stringify(formData.links)
    const cleanedDesc = formData.desc.trim().replace(/\n/g, '')


    const shortcode = `[HeroSectionWithLinks {title="${formData.title}" desc="${cleanedDesc}" image="${formData.image}" links='${linksString}'} /]`

    // Emit sự kiện và gửi shortcode ra component cha
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
}
</script>

<style scoped>
/* Các style tùy chỉnh nếu cần */
</style>
