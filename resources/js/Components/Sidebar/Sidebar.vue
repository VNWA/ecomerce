<!-- resources/js/Components/Sidebar.vue -->
<template>
    <aside id="sidebar" :class="{ 'active': adminStore.isSidebarShow || isMobile() }"
        class="w-full p-1 bg-gray-900 text-gray-100 min-h-screen  h-screen max-h-screen sticky top-0 left-0 ease-in duration-300">
        <div class="flex flex-col justify-between align-middle h-full">
            <div>
                <div class="sidebar-header">
                    <div class="logo">
                        <img src="/images/vnwaLogoFull.png" class="show" alt="Vinawebapp Logo">
                        <img src="/images/vnwaLogoIcon.png" class="hide" alt="Vinawebapp Logo">
                    </div>
                    <div class="btn" @click="adminStore.toggleSidebar" v-if="!isMobile()">
                        <span class="relative inline-block overflow-hidden rounded-full p-[1px]">
                            <span
                                class="absolute inset-[-1000%] animate-[spin_2s_linear_infinite] bg-[conic-gradient(from_90deg_at_50%_50%,#E2CBFF_0%,#393BB2_50%,#E2CBFF_100%)]" />
                            <div
                                class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-full bg-slate-950/90 px-3 py-1 text-sm font-medium text-white backdrop-blur-3xl">
                                <Icon icon="material-symbols:close" class="show" />
                                <Icon icon="fa6-solid:bars-progress" class="hide" />
                            </div>
                        </span>
                    </div>
                </div>
                <div class="sidebar-content">
                    <ul id="sidebar-content-list">
                        <li class="mb-3 ">
                            <SideBarLink :href="route('dashboard')" :active="check('/dashboard')">
                                <Icon icon="uil:analytics" :class="{ 'me-2': !adminStore.isSidebarShow }" />

                                <span class="show font-semibold  text-slate-200">Dashboard</span>
                            </SideBarLink>

                        </li>
                        <!-- <li class="mb-3">
                    <div class=" cursor-pointer relative w-100 py-2 px-4 rounded sidebar-item-list "
                        :class="{ 'active': isBlogsActive || check('/blog') }">
                        <div class="flex items-center justify-start sidebar-item-list-title"
                            @click=" isBlogsActive = !isBlogsActive;">
                            <Icon icon="fa6-solid:blog" class="me-2" />
                            <span class="show  font-semibold  text-slate-200">Blog</span>
                            <Icon icon="fa6-solid:chevron-down" class="ms-2 show" />
                        </div>

                        <div class="sidebar-item-list-content  mt-3">
                            <ul class=" border-l border-purple-100">
                                <li class="mb-1">
                                    <SideBarMenuItemLink :href="route('Blog.Categories')"
                                        :active="check('/blog/categories')">
                                        Categories
                                    </SideBarMenuItemLink>
                                    <SideBarMenuItemLink :href="route('Blog.Tags')" :active="check('/blog/tags')">
                                        Tags
                                    </SideBarMenuItemLink>
                                    <SideBarMenuItemLink :href="route('Blog.Posts')" :active="check('/blog/posts')">
                                        Post
                                    </SideBarMenuItemLink>
                                </li>

                            </ul>
                        </div>
                    </div>
                </li> -->
                        <li class="mb-3">
                            <div class=" cursor-pointer relative w-100 py-2 px-4 rounded sidebar-item-list "
                                :class="{ 'active': isEcommerceActive || check('/ecommerce') }">
                                <div class="flex items-center justify-start sidebar-item-list-title"
                                    @click=" isEcommerceActive = !isEcommerceActive;">
                                    <Icon icon="fa6-solid:bag-shopping" class=""
                                        :class="{ 'me-2': !adminStore.isSidebarShow }" />
                                    <span class="show  font-semibold  text-slate-200">Ecommerce</span>
                                    <Icon icon="fa6-solid:chevron-down" class="ms-2 show" />
                                </div>

                                <div class="sidebar-item-list-content  mt-3">
                                    <ul class=" border-l border-purple-100">
                                        <li class="mb-1">
                                            <SideBarMenuItemLink :href="route('Ecommerce.ProductCategories')"
                                                :active="check('/ecommerce/product-categories')">
                                                Categories
                                            </SideBarMenuItemLink>
                                            <SideBarMenuItemLink :href="route('Ecommerce.Color')"
                                                :active="check('/ecommerce/colors')">
                                                Colors
                                            </SideBarMenuItemLink>
                                            <SideBarMenuItemLink :href="route('Ecommerce.Brand')"
                                                :active="check('/ecommerce/brands')">
                                                Brands
                                            </SideBarMenuItemLink>
                                            <SideBarMenuItemLink :href="route('Ecommerce.Product')"
                                                :active="check('/ecommerce/products')">
                                                Products
                                            </SideBarMenuItemLink>
                                            <SideBarMenuItemLink :href="route('Ecommerce.Coupon')"
                                                :active="check('/ecommerce/coupons')">
                                                Coupons
                                            </SideBarMenuItemLink>
                                            <SideBarMenuItemLink :href="route('Ecommerce.Delivery')"
                                                :active="check('/ecommerce/deliveries')">
                                                Deliveries
                                            </SideBarMenuItemLink>
                                            <SideBarMenuItemLink :href="route('Ecommerce.Order')"
                                                :active="check('/ecommerce/orders')">
                                                Orders
                                            </SideBarMenuItemLink>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3 ">
                            <SideBarLink :href="route('Banner')" :active="check('/banners')">
                                <Icon icon="fa6-solid:images" :class="{ 'me-2': !adminStore.isSidebarShow }" />


                                <span class="show font-semibold  text-slate-200">Banners</span>
                            </SideBarLink>
                        </li>
                        <li class="mb-3 ">
                            <SideBarLink :href="route('StaticPage')" :active="check('/static-pages')">
                                <Icon icon="fa6-solid:pager" :class="{ 'me-2': !adminStore.isSidebarShow }" />
                                <span class="show font-semibold  text-slate-200">Static Pages</span>
                            </SideBarLink>
                        </li>
                        <li class="mb-3 ">
                            <SideBarLink :href="route('Appearance')" :active="check('/appearance')">
                                <Icon icon="fa6-solid:paintbrush" :class="{ 'me-2': !adminStore.isSidebarShow }" />
                                <span class="show font-semibold  text-slate-200">Appearance</span>
                            </SideBarLink>

                        </li>
                        <li class="mb-3 ">
                            <SideBarLink :href="route('Setting')" :active="check('/settings')">
                                <Icon icon="material-symbols-light:settings-heart-rounded" :class="{ 'me-2': !adminStore.isSidebarShow }" />
                                <span class="show font-semibold  text-slate-200">Setting</span>
                            </SideBarLink>

                        </li>
                    </ul>
                </div>
            </div>
            <div>
                <div class=" py-3  border-t border-gray-500/50 flex items-center justify-center">
                    <img :src="'/images/nation/' + localeImage + '.webp'" width="100" alt="vinawebapp.com">
                </div>
            </div>
        </div>


    </aside>
</template>

<script setup>
import { useAdminStore } from '@/store/admin';
import SideBarLink from './SideBarLink.vue';
import SideBarMenuItemLink from './SideBarMenuItemLink.vue';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watchEffect } from 'vue';

const localeImage = ref('en_db');
const page = usePage();
function removeDbSuffix() {
    localeImage.value = localeImage.value.replace('_db', '');
}
watchEffect(async () => {
    localeImage.value = page.props.current_db
    removeDbSuffix();
});



const isMobile = () => {
    if (window.innerWidth <= 500) {
        return true;
    }
    else {
        return false;
    }
}
const adminStore = useAdminStore();
const check = (path) => {
    const { url } = usePage();
    if (url.includes(path)) {
        return true;
    } else {
        return false;

    }
    // return isActive;
};
const isEcommerceActive = ref(false);
const isBlogsActive = ref(false);
</script>
