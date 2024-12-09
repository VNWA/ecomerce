<template>
    <div>
        <AppLayout title="Setting" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Setting
                        </h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs :breadcrumbs="[['Setting', route('Setting')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5 text-black/70">
                <div>

                    <div class="mb-6 p-5 ">
                        <Card title="Security">
                            <div class="grid grid-cols-5 gap-4">
                                <div>
                                    <Link :href="route('Setting.Security.FrontendUrls')">
                                    <div
                                        class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                        Frontend Urls
                                    </div>
                                    </Link>
                                </div>
                            </div>
                        </Card>

                    </div>
                    <div class="p-5 ">
                        <div class='mb-6 '>

                            <Card title="Config">
                                <div class="grid grid-cols-5 gap-4">
                                    <div>
                                        <Link :href="route('Setting.Config.Stripe')">
                                        <div
                                            class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                            Stripe
                                        </div>
                                        </Link>
                                    </div>
                                    <div>
                                        <Link :href="route('Setting.Config.Paypal')">
                                        <div
                                            class="px-10 py-5 text-center border-black bg-purple-600/90 hover:bg-purple-900 cursor-pointer rounded-md shadow-md font-bold text-xl text-white">
                                            Paypal
                                        </div>
                                        </Link>
                                    </div>
                                </div>
                            </Card>
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
import Card from '@/Components/Card.vue';




const isPageLoading = ref(false)


</script>

<style></style>
