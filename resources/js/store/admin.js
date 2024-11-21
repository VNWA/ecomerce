// src/store/admin.js
import { defineStore } from 'pinia';

export const useAdminStore = defineStore('admin', {
    state: () => ({
        isSidebarShow: false,
        isMediaDetailShow: true,
        isMetaSeoFormShow: true,
        isMetaSeoFormShow: true,
        SearchEnigeType: 'Google',
    }),
    actions: {
        toggleMediaDetail() {
            this.isMediaDetailShow = !this.isMediaDetailShow;
        },
        toggleSidebar() {
            this.isSidebarShow = !this.isSidebarShow;
        },
        toggleMetaSeoForm() {
            this.isMetaSeoFormShow = !this.isMetaSeoFormShow;
        },
        changeSearchEnigeType(type) {
            this.SearchEnigeType = type
        }
    },
    persist: true, // Sử dụng pinia-plugin-persistedstate để lưu trạng thái
});
