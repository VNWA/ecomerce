<template>
    <div :class="{ 'bg-sky-500': selected == true }" class="border-black/10 border border-solid  cursor-pointer "
        @contextmenu.prevent="showContextMenu($event)">
        <div class="item_avatar flex items-center justify-center relative">
            <img v-if="dataItem.type === 'dir'" src="/images/folder.png" alt="Folder Image"
                class="max-w-full max-h-full w-auto h-auto">
            <img v-else-if="dataItem.type === 'document'" src="/images/document.png" alt="Document Image"
                class="max-w-full max-h-full w-auto h-auto">
            <img v-else-if="dataItem.type === 'image'" :src="dataItem.path" :alt="dataItem.name"
                class="max-w-full max-h-full w-auto h-auto">
            <img v-else-if="dataItem.type === 'video'" src="/images/video.jpg" alt="Document Image"
                class="max-w-full max-h-full w-auto h-auto">
            <img v-else src="/images/file.png" alt="Document Image" class="max-w-full max-h-full w-auto h-auto">

            <div class="absolute w-full h-full top-0 left-0 text-white"></div>
        </div>
        <div class="border px-3 py-2">
            <div class="item_name  text-black bg-white">
                {{ dataItem.name }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    dataItem: Object,
    selected: {
        type: Boolean,
        default: false
    }
});

const showContextMenu = (event) => {
    event.preventDefault();

    const menuItems = [];

    if (props.dataItem.type === 'dir') {
        menuItems.push({ label: 'View', action: () => viewItem(props.dataItem) });
        menuItems.push({ label: 'Delete', action: () => deleteItem(props.dataItem) });
    } else if (props.dataItem.type === 'image') {
        menuItems.push({ label: 'Edit', action: () => editImage(props.dataItem) });
        menuItems.push({ label: 'Delete', action: () => deleteItem(props.dataItem) });
    }

    // Xử lý hiển thị menu ngữ cảnh
    const contextMenu = document.createElement('div');
    contextMenu.classList.add('context-menu');
    contextMenu.style.top = `${event.clientY}px`;
    contextMenu.style.left = `${event.clientX}px`;

    menuItems.forEach(item => {
        const menuItem = document.createElement('div');
        menuItem.classList.add('context-menu-item');
        menuItem.textContent = item.label;
        menuItem.addEventListener('click', () => {
            item.action();
            contextMenu.remove();
        });
        contextMenu.appendChild(menuItem);
    });

    document.body.appendChild(contextMenu);

    // Xử lý sự kiện click chuột bên ngoài để đóng menu ngữ cảnh
    const closeMenu = () => {
        contextMenu.remove();
        document.removeEventListener('click', closeMenu);
    };

    document.addEventListener('click', closeMenu);
};

const viewItem = (item) => {
    // Xử lý hành động view
    console.log('View item:', item);
};

const editImage = (item) => {
    // Xử lý hành động edit image
    console.log('Edit image:', item);
};

const deleteItem = (item) => {
    // Xử lý hành động delete
    console.log('Delete item:', item);
};
</script>

<style scoped>
.item_avatar {
    width: 100%;
    height: 100px;
    overflow: hidden;
    padding: 5px;
}

.item_name {
    font-size: 14px;
    text-align: center;
    max-height: 25px;
    overflow: hidden;
}

.context-menu {
    position: fixed;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.context-menu-item {
    padding: 8px 16px;
    cursor: pointer;
}

.context-menu-item:hover {
    background-color: #f0f0f0;
}
</style>
