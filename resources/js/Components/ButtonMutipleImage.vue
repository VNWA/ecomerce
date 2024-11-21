<template>
    <div class=" w-full h-full mb-3">


        <!-- Button to open modal -->
        <div class="flex items-center w-full  mb-3">
            <button @click="openFileManager" style="min-width: 120px;"
                class="flex items-center justify-center gap-2 px-5 py-3 w-auto text-sm font-medium text-center text-white bg-blue-700 rounded-lg cursor-pointer hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <Icon icon="stash:file-import" /> Chọn ảnh
            </button>
        </div>

        <!-- Image gallery with draggable feature -->
        <div class="border rounded p-4">
            <draggable v-model="images" :item-key="customKeyFunction" tag="ul" class="grid grid-cols-12">
                <template #item="item">
                    <li class="col-span-6 p-1 ButtonMutipleImage_item border">
                        <span
                            class="absolute z-10 bg-white text-black w-5 text-center text-xs bottom-0 border-solid border-purple-700 border-2">{{
                                item.index }}</span>
                        <img :src="item.element" width="100%" height="auto" class="w-full h-auto" alt="" />
                        <div class="delete">
                            <button @click="deleteItem(item.index)"
                                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                x
                            </button>
                        </div>
                    </li>
                </template>
            </draggable>
        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';
import draggable from 'vuedraggable';
import { useAttrs, defineEmits } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    }
});

const emit = defineEmits(['update:modelValue']);

// State for controlling modal visibility
const isModal = ref(false);

// Initialize images array from props (modelValue)
const images = ref([...props.modelValue]);

// Watch for changes from parent and update images array
watch(() => props.modelValue, (newVal) => {
    images.value = [...newVal];
});

// Function to delete an image
const deleteItem = (index) => {
    images.value.splice(index, 1);
    emit('update:modelValue', images.value);
};



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
            window.removeEventListener('message', handleMessage);

            files.forEach((file) => {
                images.value.push(file.path);
            });
            emit('update:modelValue', images.value);


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

// Custom key function for draggable component
const customKeyFunction = (item) => {
    return item;
};
</script>

<style>
.ButtonMutipleImage_item {
    width: 100%;
    height: 100%;
    position: relative;
    cursor: move;
}

.ButtonMutipleImage_item:active {
    border: solid 1px rebeccapurple;
}

.ButtonMutipleImage_item .delete {
    position: absolute;
    top: -2px;
    left: -2px;
    display: none;
    z-index: 999;
}

.ButtonMutipleImage_item:hover .delete {
    display: block;
}

.ButtonMutipleImage_item .delete button {
    width: 10px;
    height: 10px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;

}
</style>
