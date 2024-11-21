<template>
    <div>
        <AppLayout title="Product">
            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Media</h2>
                    </div>
                    <div>
                        <!-- <HeaderBreadcrumbs :breadcrumbs="[['Media', route('Media')]]" /> -->
                    </div>
                </div>
            </template>
            <div class="py-2 relative">
                <div class="absolute w-full h-full top-0 left-0 bg-white/50 z-50" v-if="isLoading">
                    <div role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                        <svg aria-hidden="true"
                            class="w-24 h-24 text-gray-200 animate-spin  fill-blue-600"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-if="showCreateDirectoryModal"
                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-20">
                    <div class="bg-white p-6 rounded shadow-md">
                        <h2 class="text-xl mb-4">Create New Directory</h2>
                        <input v-model="newDirectoryName" class="border p-2 mb-4 w-full"
                            placeholder="Enter directory name" />
                        <div class="flex justify-end">
                            <button @click="createDirectory" class="bg-purple-500 text-white px-4 py-2 rounded mr-2">
                                Create
                            </button>
                            <button @click="showCreateDirectoryModal = false"
                                class="bg-gray-500 text-white px-4 py-2 rounded">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="isModalReaname"
                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-20">
                    <div class="bg-white p-6 rounded shadow-md">
                        <h2 class="text-xl mb-4">Reaname</h2>
                        <h5 class="text-sm mb-2 text-black/50">{{ reanameData.currentName }}</h5>
                        <h5 class="text-sm mb-2 text-black/50">{{ reanameData.path }}</h5>
                        <input v-model="reanameData.newName" class="border p-2 mb-4 w-full"
                            placeholder="Enter new name" />
                        <div class="flex justify-end">
                            <button @click="submitChangeName" class="bg-purple-500 text-white px-4 py-2 rounded mr-2">
                                Save
                            </button>
                            <button @click="isModalReaname = false" class="bg-gray-500 text-white px-4 py-2 rounded">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="isModalDelete"
                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-20 no_close_selected">
                    <div class="bg-white p-6 rounded shadow-md">
                        <h2 class="text-xl mb-4">Delete</h2>
                        <div class="py-2">
                            <ul>
                                <li v-for="(item, index) in keySelected" :key="index" class="flex gap-4">
                                    <span v-if="dataFiles[item].type == 'dir'" class="font-bold text-yellow-300">
                                       <Icon icon="fa6-solid:folder-open" />

                                    </span>
                                    <span v-else-if="dataFiles[item].type == 'image'" class="font-bold text-blue-500">
                                        <Icon icon="fa6-solid:image" />
                                    </span>
                                    <span v-else-if="dataFiles[item].type == 'video'" class="font-bold text-sky-500">
                                        <Icon icon="fa6-solid:video"  />
                                    </span>
                                    <span v-else-if="dataFiles[item].type == 'document'"
                                        class="font-bold text-black/80">
                                        <Icon icon="fa6-solid:file-lines"  />
                                    </span>
                                    <span v-else class="font-bold">
                                        <Icon icon="fa6-solid:file"   />
                                    </span>

                                    {{ dataFiles[item].name }}
                                </li>
                            </ul>
                        </div>
                        <hr class="mb-3" />
                        <div class="flex justify-end">
                            <button @click="submitDeleteFiles" class="bg-red-500 text-white px-4 py-2 rounded mr-2"
                                v-if="keySelected.length > 0">
                                Delete
                            </button>
                            <button @click="isModalDelete = false" class="bg-gray-500 text-white px-4 py-2 rounded">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="pathImageEditor"
                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-20">
                    <div class="bg-white p-6 rounded shadow-md w-screen h-screen overflow-y-scroll">
                        <div>
                            tile
                        </div>
                        <div class="relative">
                            <ImageEditor :imageUrl="pathImageEditor" />
                        </div>
                    </div>
                </div>
                <div class="w-full p-3 rounded">
                    <div class="border border-black/20 py-3 bg-white text-black">
                        <div class="flex items-center justify-between">
                            <div class="flex px-3 gap-4">
                                <button
                                    class="flex items-center justify-center gap-2 bg-purple-500 px-3 py-2 text-white rounded font-bold"
                                    @click="openFileDialog">
                                    <Icon icon="fa6-solid:upload"   />
                                    Uploads
                                </button>
                                <input type="file" ref="fileInput" class="hidden" multiple @change="handleFileChange" />

                                <button
                                    class="flex items-center justify-center gap-2 bg-purple-500 px-3 py-2 text-white rounded font-bold"
                                    @click="showCreateDirectoryModal = !showCreateDirectoryModal">
                                     <Icon icon="fa6-solid:folder-plus"  />
                                </button>
                                <button
                                    class="flex items-center justify-center gap-2 bg-purple-500 px-3 py-2 text-white rounded font-bold"
                                    @click="loadData">
                                    <Icon icon="fa6-solid:rotate"  />
                                </button>
                            </div>
                            <div class="flex px-3 gap-4">
                                <button type="button" v-if="keySelected.length > 0"
                                    @click="isModalDelete = !isModalDelete"
                                    class="no_close_selected flex items-center justify-center gap-2 bg-red-500 px-3 py-2 text-white rounded font-bold">
                                    <Icon icon="fa6-solid:trash"  />
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="border border-black/20 py-2 px-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <ul class="flex gap-3 items-center">
                                    <li>
                                        <button @click="basePath = ''"
                                            class="bg-purple-500 text-white px-3 py-1 text-xl">
                                        <Icon icon="fa6-solid:folder"  />
                                        </button>
                                    </li>
                                    <li v-for="(item, index) in basePathArray">
                                        /
                                        <button
                                            class="bg-black/10 px-3 rounded-lg hover:text-white hover:bg-purple-500 ms-2"
                                            @click="basePath = item.path">
                                            {{ item.name }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="flex items-center justify-end gap-4">
                                <div class="relative">
                                    <button
                                        class="bg-purple-500 text-white font-bold px-5 py-2 flex gap-1 items-center justify-center"
                                        @click="isFileterList = !isFileterList">
                                        <Icon icon="fa6-solid:filter"  />

                                        <span v-for="(item, index) in fileterList" :key="index">
                                            <div v-if="item.active">{{ item.name }}</div>
                                        </span>
                                    <Icon icon="fa6-solid:caret-down"  />
                                    </button>
                                    <div class="absolute right-0 top-10 min-w-32 bg-white border border-black/50 z-10"
                                        v-if="isFileterList">
                                        <ul>
                                            <li v-for="(item, index) in fileterList" :key="index">
                                                <button @click="filterData(index, item)"
                                                    class="hover:bg-black/10 w-full px-3 py-1 hover:text-black/80 text-start"
                                                    :class="{ ' bg-black/50 text-white': item.active }">
                                                    {{ item.name }}
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <input type="text" class="min-w-52" v-model="searchTerm" placeholder="Search Name"
                                    @input="handleSearch" />

                                <Icon icon="fa6-solid:circle-info"
                                    :class="{ 'text-purple-500  ': adminStore.isMediaDetailShow }"
                                    @click="adminStore.toggleMediaDetail" class="text-2xl cursor-pointer" />

                            </div>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-full h-screen" id="media_files" @mouseenter="isMouseInside = true"
                            @mouseleave="isMouseInside = false">
                            <ul class="grid grid-cols-12 gap-4 py-5 overflow-y-scroll"
                                v-if="dataFiles && dataFiles.length > 0">
                                <li v-for="(item, index) in filteredFiles" :key="index" class="col-span-2 media_item">
                                    <div class="w-full relative">
                                        <MediaItem :dataItem="item" @click="oneClick(index, item, $event)"
                                            @dblclick="viewFolder(item)" :selected="keySelected.includes(index)"
                                            @contextmenu.prevent="showContextMenu(index, $event)" />

                                        <div v-if="isMouseRightItemKey == index"
                                            class="no_close_selected absolute bottom-5 left-1/4 z-20 cursor-default">
                                            <ul
                                                class="bg-black/80 text-white/80 font-bold w-full text-xs min-w-24 text-center rounded overflow-hidden">
                                                <li v-if="item.type == 'dir'">
                                                    <button
                                                        class="px-3 py-1 hover:bg-black/50 text-white hover:text-white/60 border border-black/50 w-full flex items-center gap-4"
                                                        @click="viewFolder(item)">
                                                       <Icon icon="fa6-solid:folder-open" />
 Open
                                                    </button>
                                                </li>

                                                <li v-else>
                                                    <a @click="isMouseRightItemKey = null" target="_blank"
                                                        :href="item.path"
                                                        class="px-3 py-1 hover:bg-black/50 text-white hover:text-white/60 border border-black/50 w-full flex items-center gap-4">
                                    <Icon icon="fa6-solid:eye"  />
                                                        Preview
                                                    </a>
                                                </li>
                                                <li>
                                                    <button
                                                        class="px-3 py-1 hover:bg-black/50 text-white hover:text-white/60 border border-black/50 w-full flex items-center gap-4"
                                                        @click="showModalReaname(item)">
                                    <Icon icon="fa6-solid:pen-to-square"  />
                                   Rename
                                                    </button>
                                                </li>
                                                <!-- <li v-if="item.type == 'image'">
                                                    <button
                                                        class="px-3 py-1 hover:bg-black/50 hover:text-white/60 border border-black/50 w-full flex items-center gap-4"
                                                        @click="pathImageEditor = item.path, isMouseRightItemKey = null">
                                                       <Icon icon="fa6-solid:folder-open" />
 Edit
                                                    </button>
                                                </li> -->
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div v-else
                                class="bg-white w-full h-full flex py-20 justify-center text-black/80 text-xl font-bold">
                                No Data
                            </div>
                        </div>
                        <div class="w-3/12 media_detail" v-if="adminStore.isMediaDetailShow">
                            <div class="w-full border border-black bg-white px-3 h-full py-5">
                                <div v-if="dataDetail.name">
                                    <div class="border-b-2 border-black border-solid flex items-center justify-center">
                                        <img v-if="dataDetail.type === 'image'" :src="dataDetail.path" alt=""
                                            class="w-auto h-auto max-w-full" />
                                        <img v-else-if="dataDetail.type === 'dir'" src="/images/folder.png" alt=""
                                            class="w-auto h-auto max-w-full" />
                                        <img v-else-if="dataDetail.type === 'document'" src="/images/document.png"
                                            alt="Document Image" class="w-auto h-auto max-w-full" />
                                        <img v-else-if="dataDetail.type === 'video'" src="/images/video.jpg"
                                            alt="Document Image" class="w-auto h-auto max-w-full" />
                                        <img v-else-if="dataDetail.type === 'other'" src="/images/file.png"
                                            alt="Document Image" class="w-auto h-auto max-w-full" />
                                    </div>
                                    <div class="text-sm max-w-full overflow-hidden border-t-2 pt-3 border-black/10 text-black/80">
                                        <ul>
                                            <li class="mb-3 text-wrap max-w-full" v-if="dataDetail.name">
                                                Name : <span class="font-bold"> {{ dataDetail.name }}</span>
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex" v-if="dataDetail.type">
                                                Type : {{ dataDetail.type }}
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex flex-wrap" v-if="dataDetail.path">
                                                Path : {{ dataDetail.path }}
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex" v-if="dataDetail.created_at">
                                                Uploaded at : {{ dataDetail.created_at }}
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex" v-if="dataDetail.updated_at">
                                                Modified at : {{ dataDetail.updated_at }}
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex" v-if="dataDetail.size">
                                                Size : {{ dataDetail.size }} kb
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex" v-if="dataDetail.width">
                                                Width : {{ dataDetail.width }}
                                            </li>
                                            <li class="mb-3 text-wrap max-w-full flex" v-if="dataDetail.height">
                                                Height : {{ dataDetail.height }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div v-else class="text-center">No Data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script setup>
import { useAdminStore } from '@/store/admin';
import { ref, watch, onMounted, onUnmounted } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, usePage } from "@inertiajs/vue3";
import MediaItem from "@/Components/MediaItem.vue";
import ImageEditor from '@/Components/ImageEditor.vue'
const adminStore = useAdminStore();

const isLoading = ref(false);
const basePath = ref("");
const basePathArray = ref({});
const dataFiles = ref({});
const showCreateDirectoryModal = ref(false);
const newDirectoryName = ref("");
const dataDetail = ref({});
const pathImageEditor = ref('');
const keySelected = ref([]);
const fileInput = ref(null);
const isMouseRightItemKey = ref(null);
const isModalDelete = ref(false);
const isModalReaname = ref(false);
const isMouseInside = ref(false);
const reanameData = ref({
    currentName: "",
    type: "",
    path: "",
    newName: "",
});
const searchTerm = ref("");
const filteredFiles = ref({});

const fileterList = ref([
    { active: false, name: "File Name - ASC", type: "name", filter: "ASC" },
    { active: false, name: "File Name - DESC", type: "name", filter: "DESC" },
    { active: false, name: "Size - ASC", type: "size", filter: "ASC" },
    { active: false, name: "Size - DESC", type: "size", filter: "DESC" },
    { active: false, name: "Uploaded - ASC", type: "created_at", filter: "ASC" },
    { active: false, name: "Uploaded - DESC", type: "created_at", filter: "DESC" },
]);
const isFileterList = ref(false);
const loadData = async () => {
    isLoading.value = true;
    searchTerm.value = "";
    isFileterList.value = false;
    fileterList.value.forEach((item, idx) => {
        item.active = false;
    });

    try {
        const response = await axios.post("/vnwa/media/get-data-files", {
            basePath: basePath.value,
        });
        dataFiles.value = response.data;
        filteredFiles.value = dataFiles.value;
        keySelected.value = [];
        toast.success("Tải nội dung thư mục thành công", {
            autoClose: 1500,
        });
    } catch (error) {
        console.error("Error uploading files:", error);
    }
    isLoading.value = false;
};

const openFileDialog = () => {
    fileInput.value.click();
};
const handleFileChange = async (event) => {
    const files = event.target.files;

    if (files.length > 0) {
        isLoading.value = true;

        let totalSize = 0;
        for (let i = 0; i < files.length; i++) {
            totalSize += files[i].size;
        }
        if (totalSize > 41943040) {
            // Nếu tổng kích thước vượt quá giới hạn
            toast.error(`Tổng dung lượng tệp vượt quá giới hạn cho phép (${41943040} bytes).`, {
                autoClose: 1500,
            });
            isLoading.value = false;

            return; // Dừng xử lý tiếp theo
        }

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append("files[]", files[i]);
        }

        try {
            const response = await axios.post("/vnwa/media/upload-files", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                params: {
                    basePath: basePath.value,
                },
            });
            toast.success(response.data.message, {
                autoClose: 1500,
            });
        } catch (error) {
            toast.success(error.response.data.message, {
                autoClose: 1500,
            });
        }
        loadData();
    }
};

const createDirectory = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post("/vnwa/media/create-directory", {
            directoryName: newDirectoryName.value,
            basePath: basePath.value, // Đảm bảo basePath đã được định nghĩa và có giá trị hợp lệ
        });
        showCreateDirectoryModal.value = false;
        newDirectoryName.value = "";
        loadData();
        toast.success(response.data.message, {
            autoClose: 1500,
        });
    } catch (error) {
        toast.error(error.response.data.message, {
            autoClose: 1500,
        });
        isLoading.value = false;
    }
};

const submitDeleteFiles = async () => {
    if (keySelected.value.length > 0) {
        const dataDelete = keySelected.value.map((key) => {
            const file = dataFiles.value[key];
            return { name: file.name, type: file.type };
        });

        try {
            const response = await axios.post("/vnwa/media/delete", {
                data: dataDelete,
                path: basePath.value,
            });
            if (response.data.type == "success") {
                toast.success(response.data.message, {
                    autoClose: 1500,
                });
                isModalDelete.value = false;
                loadData();
            } else {
                toast.error("Có lỗi xảy ra", {
                    autoClose: 1500,
                });
                isModalDelete.value = false;
                loadData();
            }

            // Nếu có cần thiết, cập nhật lại danh sách thư mục sau khi tạo thư mục thành công
            // loadData();
        } catch (error) {
            console.error("Error creating directory:", error);
        }
    }
};

const oneClick = (key, item, event) => {
    if (event.ctrlKey) {
        if (keySelected.value.includes(key)) {
            // Nếu key đã tồn tại trong keySelected, loại bỏ nó
            keySelected.value = keySelected.value.filter((k) => k !== key);
        } else {
            // Nếu key chưa tồn tại trong keySelected, thêm vào
            keySelected.value.push(key);
        }
        dataDetail.value = {};
    } else {
        keySelected.value = [];
        keySelected.value.push(key);
        dataDetail.value = item;
    }
};
const showContextMenu = (index, event) => {
    isMouseRightItemKey.value = index;
};
const updateBasePathArray = () => {
    const parts = basePath.value.split("/").filter((item) => item !== "");
    basePathArray.value = parts.map((part, index) => ({
        name: part,
        path: "/" + parts.slice(0, index + 1).join("/"),
    }));
};

const viewFolder = (item) => {
    if (item.type && item.type == "dir") {
        basePath.value += "/" + item.name;
    }
    isMouseRightItemKey.value = null;
};


const editImage = (imagePath) => {
    pathImageEditor.value = imagePath;
    isMouseRightItemKey.value = null;
}


const handleClickOutside = (event) => {
    if (
        !event.target.closest("li.media_item") &&
        !event.target.closest(".media_detail") &&
        !event.target.closest(".no_close_selected")
    ) {
        keySelected.value = [];
        isMouseRightItemKey.value = null;
        dataDetail.value = {};
    }
};

const showModalReaname = (item) => {
    reanameData.value.currentName = item.name;
    reanameData.value.type = item.type;
    reanameData.value.path = basePath.value;
    reanameData.value.newName = "";
    isModalReaname.value = true;
};

const submitChangeName = async () => {
    try {
        const response = await axios.post("/vnwa/media/rename", reanameData.value);
        isModalReaname.value = false;
        reanameData.value = {};
        loadData();
    } catch (error) {
        console.error("Failed to rename object:", error.response.data.error);
        // Xử lý lỗi và hiển thị thông báo lỗi cho người dùng
    }
};

const handleKeyDown = (event) => {
    // Bắt sự kiện khi người dùng nhấn phím
    if (event.key === "a" && event.ctrlKey && isMouseInside.value) {
        // Ctrl + A được nhấn, đẩy tất cả các chỉ số hiện có vào keySelected
        event.preventDefault();
        if (filteredFiles.value && filteredFiles.value.length > 0) {
            keySelected.value = filteredFiles.value.map((item, index) => index);
        } else {
            keySelected.value = [];
        }
    }
};

const handleSearch = () => {
    if (!searchTerm.value.trim()) {
        filteredFiles.value = dataFiles.value;
        return;
    }

    filteredFiles.value = dataFiles.value.filter((file) =>
        file.name.toLowerCase().includes(searchTerm.value.trim().toLowerCase())
    );
};

const filterData = (index, item) => {
    fileterList.value.forEach((item, idx) => {
        item.active = false;
    });
    fileterList.value[index].active = true;

    filteredFiles.value.sort((a, b) => {
        if (item.filter === "ASC") {
            if (a[item.type] < b[item.type]) return -1;
            if (a[item.type] > b[item.type]) return 1;
        } else if (item.filter === "DESC") {
            if (a[item.type] > b[item.type]) return -1;
            if (a[item.type] < b[item.type]) return 1;
        }
        return 0;
    });
    isFileterList.value = false;
};

onMounted(() => {
    loadData();
    document.addEventListener("keydown", handleKeyDown);
    // Thêm sự kiện click cho document khi component được mount
    document.addEventListener("click", handleClickOutside);
});

// Xóa sự kiện click khi component bị unmount để tránh memory leak
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
    // document.removeEventListener('keydown', handleKeyDown);
});

watch(basePath, (newBasePath, oldBasePath) => {
    if (newBasePath !== oldBasePath) {
        updateBasePathArray();
        loadData();
    }
});
watch(pathImageEditor, (newVal) => {
    if (newVal) {
        document.body.classList.add('no-scroll');
    } else {
        document.body.classList.remove('no-scroll');
    }
});


</script>

<style>
.path_item {}
</style>
