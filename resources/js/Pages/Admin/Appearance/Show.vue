<template>
    <div>
        <AppLayout title="Appearance" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appearance
                        </h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[['Appearance', route('Appearance')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5 text-black/70">
                <div class="flex items-center justify-start flex-wrap gap-5">

                    <div class="mb-6 p-5 shadow-2xl w-full">
                        <h3 class="mb-3 text-xl font-bold">Layout</h3>
                        <div class="grid grid-cols-5 gap-4">
                            <div>
                                <Link :href="route('Appearance.Logo')">
                                <div
                                    class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                    Logo
                                </div>
                                </Link>
                            </div>
                            <div>
                                <Link :href="route('Appearance.Footer')">
                                <div
                                    class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                    Footer Content
                                </div>
                                </Link>
                            </div>
                            <div>
                                <Link :href="route('Appearance.TopNav')">
                                <div
                                    class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                    Top Nav Link
                                </div>
                                </Link>
                            </div>
                            <div>
                                <Link :href="route('Appearance.Profile')">
                                <div
                                    class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                    Profile
                                </div>
                                </Link>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>


                    <div class="mb-6 p-5 shadow-2xl w-full">
                        <h3 class="mb-3 text-xl font-bold">Page </h3>
                        <div class="grid grid-cols-5 gap-4 ">
                            <div>
                                <Link :href="route('Appearance.Home')">
                                <div
                                    class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                    Home
                                </div>
                                </Link>
                            </div>
                            <div>
                                <Link :href="route('Appearance.About')">
                                <div
                                    class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                    About
                                </div>
                                </Link>
                            </div>
                        </div>
                    </div>









                </div>
            </div>

        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue'
import { Head, Link, useForm } from '@inertiajs/vue3';

const isPageLoading = ref(false)


</script>

<style></style>
