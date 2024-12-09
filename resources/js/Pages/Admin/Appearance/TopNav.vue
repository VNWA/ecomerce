<template>
    <div>
        <AppLayout title="Edit Top Nav" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Top Nav</h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Appearance', route('Appearance')], [' Edit Top Nav', route('Appearance.TopNav')]]" />
                    </div>
                </div>
            </template>

            <div class="p-5">
                <div class="flex">
                    <div class="w-full h-20" id="top-nav">
                        <ul class="px-3 gap-4">
                            <li v-for="(item, index) in data" :key="index">
                                <div class="flex items-center justify-between gap-2">
                                    <button v-if="index > 0" @click="moveItem(index, -1)"
                                        class="hover:text-sky-500/80 text-sky-500/50 font-bold">
                                        <Icon icon="fa6-solid:arrow-left" />
                                    </button>
                                    <div class="border px-2 py-1 rounded bg-white text-black cursor-pointer"
                                        @click="showEdit(index)" :class="{ 'text-purple-500': form.index === index }">
                                        {{ item.name }}
                                    </div>
                                    <button v-if="index < data.length - 1" @click="moveItem(index, 1)"
                                        class="hover:text-sky-500/80 text-sky-500/50 font-bold">
                                        <Icon icon="fa6-solid:arrow-right" />
                                    </button>
                                    <button @click="removeItem(index)"
                                        class="text-red-500 hover:text-red-700 font-bold">
                                        <Icon icon="fa6-solid:trash" />
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <button @click="showCreate"
                            class="bg-white border border-purple-500 hover:bg-purple-900 hover:text-white py-2 px-5 rounded-sm text-black text-nowrap ms-3">
                                            <Icon   icon="fa6-solid:plus"  />
                            Create
                        </button>
                    </div>
                </div>

                <div class="my-3 bg-white p-5 shadow shadow-black">
                    <div class="mb-4">
                        <InputLabel for="name">Name <span class="text-red-500">*</span></InputLabel>
                        <InputText id="name" :maxLength="150" v-model="form.name" type="text" class="mt-1 block w-full"
                            required />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="InputSelectedIcon">Icon </InputLabel>
                        <InputSelectedIcon id="InputSelectedIcon"
                            class="mt-1 block w-full border border-solid border-black h-10" v-model="form.icon" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="link">Link <span class="text-red-500">*</span></InputLabel>
                        <InputDesc id="link" v-model="form.link" class="mt-1 block w-full" />
                    </div>
                    <div class="mt-6 w-full text-center">
                        <button @click="submit"
                            class="bg-purple-500 hover:bg-purple-900 hover:text-white py-2 px-5 rounded-sm text-white text-nowrap ms-3">
                            <Icon icon="material-symbols:sync-saved-locally" />
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import InputSelectedIcon from '@/Components/Input/InputSelectedIcon.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import InputText from '@/Components/Input/InputText.vue';
import InputDesc from '@/Components/Input/InputDesc.vue';

const isPageLoading = ref(false);
const form = ref({
    type: 'create',
    index: null,
    name: '',
    icon: [],
    link: '',
});
const data = ref([]);

const loadData = () => {
    isPageLoading.value = true;
    axios.get('/vnwa/appearance/top-nav/load-json-data')
        .then(response => {
            data.value = response.data;
            isPageLoading.value = false;
        })
        .catch(error => {
            console.log(error);
            isPageLoading.value = false;
        });
};

loadData();

const updateData = () => {
    axios.post('/vnwa/appearance/top-nav/update', { data: data.value })
        .then(response => {
            toast.success(response.data.message, { autoClose: 1500 });
        })
        .catch(error => {
            toast.error(error.message, { autoClose: 1500 });
        });
};

const showCreate = () => {
    form.value = {
        type: 'create',
        index: null,
        name: '',
        icon: [],
        link: '',
    };
};

const showEdit = (index) => {
    form.value.type = 'edit';
    form.value.index = index;
    form.value.name = data.value[index]?.name;
    form.value.icon = data.value[index]?.icon;
    form.value.link = data.value[index]?.link;
};

const submit = () => {
    if (!form.value.name.trim() || !form.value.link.trim()) {
        toast.error('Please enter complete data', { autoClose: 1500 });
        return false;
    }

    const newData = {
        name: form.value.name,
        icon: form.value.icon,
        link: form.value.link,
    };

    if (form.value.index == null) {
        data.value.push(newData);
    } else {
        data.value[form.value.index] = newData;
    }

    showCreate();
    updateData();
};

const moveItem = (index, direction) => {
    const newIndex = index + direction;
    if (newIndex >= 0 && newIndex < data.value.length) {
        const item = data.value.splice(index, 1)[0];
        data.value.splice(newIndex, 0, item);
        updateData();
    }
};

const removeItem = (index) => {
    data.value.splice(index, 1);
    showCreate();
    updateData();
};
</script>
