<template>
    <div>
        <AppLayout title="Edit Footer" :isLoading="isPageLoading">

            <template #header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Footer
                        </h2>
                    </div>
                    <div>
                        <HeaderBreadcrumbs
                            :breadcrumbs="[['Appearance', route('Appearance')], [' Edit Footer', route('Appearance.Footer')]]" />
                    </div>
                </div>
            </template>
            <div class="p-5">

                <div class="my-3 bg-white p-5 shadow shadow-black">
                    <div class="  w-full">

                        <div class="mb-4">
                            <div class="bg-white shadow-black border p-3">

                                <InputLabel for="name">Footer SideBar <span class="text-red-500">*</span></InputLabel>
                                <div>
                                    <div class="flex items-center justify-end">
                                        <button v-if="!isFormAddColumn" @click="isFormAddColumn = !isFormAddColumn"
                                            class="mb-3 bg-white border border-purple-500 hover:text-white hover:bg-purple-500 px-5 py-2 text-md">
                                            Add New Column
                                        </button>
                                        <div v-if="isFormAddColumn">
                                            <div class="">
                                                <InputLabel for="'name_column" class="text-black">
                                                    Name
                                                </InputLabel>
                                                <div class="flex items-center justify-end gap-4">
                                                    <InputText :maxLength="50" id="name_column"
                                                        v-model="formAddColumn.name" />
                                                    <button @click="addColumn"
                                                        class=" bg-green-500 text-white   px-5 py-2 text-md">
                                                        Add New Column
                                                    </button>
                                                    <button @click="isFormAddColumn = false"
                                                        class=" bg-black text-white   px-5 py-2 text-md">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <ul class="my-5 flex items-start  justify-between flex-wrap gap-6  border p-5">
                                        <li v-for="(item, index) in data.footer_sidebar" :key="index">
                                            <div>
                                                <div class="flex items-center justify-between mb-5">
                                                    <input type="text" v-model="item.name"
                                                        class="border-none font-bold text-black/90 w-full">
                                                    <Icon icon="fa6-solid:x" />
                                                        class="hover:text-red-500 cursor-pointer ms-3"
                                                        @click="removeColumn(index)" />
                                                </div>
                                                <ul class="w-full text-sm text-black/80">

                                                    <li class="mb-2 ps-3" v-for="(value, n) in item.value" :key="n" >
                                                        <div class="flex items-center justify-between gap-5 ">
                                                            <h3> {{ value.name }} </h3>
                                                            <div>
                                                                <Icon icon="fa6-solid:pen"
                                                                    class="hover:text-yellow-500 cursor-pointer"
                                                                    @click="openEditChild(index + '_' + n)"
                                                                    :class="{ 'text-yellow-500': isShowFormEdit == index + '_' + n }" />
                                                                <Icon icon="fa6-solid:x" />
                                                                    class="hover:text-red-500 cursor-pointer ms-3"
                                                                    @click="removeChild(index, n)" />
                                                            </div>
                                                        </div>
                                                        <div class="bg-black/50 p-2"
                                                            v-if="isShowFormEdit == index + '_' + n">
                                                            <div class="mb-4">
                                                                <InputLabel :for="'name' + index + '_' + n"
                                                                    class="text-white">
                                                                    Name
                                                                </InputLabel>
                                                                <InputText :maxLength="50"
                                                                    :id="'name' + index + '_' + n"
                                                                    v-model="value.name" />
                                                            </div>
                                                            <div class="mb-4">
                                                                <InputLabel :for="'link' + index + '_' + n"
                                                                    class="text-white">Link
                                                                </InputLabel>
                                                                <InputText :id="'link' + index + '_' + n"
                                                                    v-model="value.link" />
                                                            </div>



                                                        </div>
                                                    </li>
                                                    <li class="mb-2 ps-3">
                                                        <div class="flex items-center justify-between gap-5  hover:text-green-500 cursor-pointer"
                                                            @click="showFormAddChild(index)"
                                                            :class="{ 'text-green-500': isFormAddChild == index }">
                                                            <h3> Add New Child </h3>
                                                            <div>
                                                                <Icon icon="fa6-solid:plus"  />

                                                            </div>
                                                        </div>
                                                        <div class="bg-black/50 p-2" v-if="isFormAddChild == index">
                                                            <div class="mb-4">
                                                                <InputLabel :for="'add_child_name' + index"
                                                                    class="text-white">
                                                                    Name
                                                                </InputLabel>
                                                                <InputText :maxLength="50"
                                                                    :id="'add_child_name' + index"
                                                                    v-model="formAddChild.name" />
                                                            </div>
                                                            <div class="mb-4">
                                                                <InputLabel :for="'add_child_link' + index"
                                                                    class="text-white">Link
                                                                </InputLabel>
                                                                <InputText :id="'add_child_link' + index"
                                                                    v-model="formAddChild.link" />
                                                            </div>
                                                            <div class="mb-4 text-center">
                                                                <button @click="createChild(index)"
                                                                    class="bg-green-500 px-3 py-2 font-bold text-white">Create</button>
                                                            </div>



                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="bg-white shadow-black border p-3 w-full max-w-full">

                                <InputLabel for="name">Footer Bottom <span class="text-red-500">*</span></InputLabel>
                                <Editor v-model="data.footer_bottom" />
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 w-full text-center">
                        <button @click="updateData"
                            class="bg-purple-500  hover:bg-purple-900 hover:text-white  py-2 px-5 rounded-sm text-white text-nowrap ms-3 ">
                            <Icon icon="fa6-solid:save" />
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
import HeaderBreadcrumbs from '@/Components/HeaderBreadcrumbs.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Editor from '@/Components/Editor.vue';
import InputText from '@/Components/Input/InputText.vue';

const isPageLoading = ref(false);
const isFormAddColumn = ref(false);
const isFormAddChild = ref(null)
const formAddColumn = ref({
    name: '',
    value: []
})
const formAddChild = ref({
    name: '',
    link: ''
})
const addColumn = () => {
    if (formAddColumn.value.name.trim().length > 0) {
        data.value.footer_sidebar.push({ ...formAddColumn.value });
        formAddColumn.value = {
            name: '',
            value: []
        };
        isFormAddColumn.value = false

    }
}
const removeColumn = (id) => {
    data.value.footer_sidebar = data.value.footer_sidebar.filter((_, index) => index !== id);
}
const showFormAddChild = (id) => {
    if (isFormAddChild.value == id) {
        isFormAddChild.value = null;
    } else {
        isFormAddChild.value = id;

    }
}
const createChild = (id) => {
    if (id !== null && formAddChild.value.name && formAddChild.value.link) {
        data.value.footer_sidebar[id].value.push({ ...formAddChild.value });

        // Reset form after adding the child
        formAddChild.value = {
            name: '',
            link: ''
        };

        // Optionally hide the form after adding the child
        isFormAddChild.value = null;
    } else {
        console.warn('Form is incomplete or id is null');
    }
};
const openEditChild = (id) => {
    if (isShowFormEdit.value == id) {
        isShowFormEdit.value = null;
    } else {
        isShowFormEdit.value = id;

    }
}
const removeChild = (parentId, childId) => {

    data.value.footer_sidebar[parentId].value = data.value.footer_sidebar[parentId].value.filter((_, index) => index !== childId);
}
const isShowFormEdit = ref(null)
const data = ref({
    footer_sidebar: [],
    footer_bottom: '',

});
const loadData = () => {
    isPageLoading.value = true;
    axios.get('/vnwa/appearance/footer/load-json-data')
        .then(response => {
            data.value = response.data
            isPageLoading.value = false;
        })
        .catch(error => {
            console.log(error);
            isPageLoading.value = false;
        });

}
loadData();

const updateData = () => {
    axios.post('/vnwa/appearance/footer/update', { data: data.value })
        .then(response => {
            toast.success(response.data.message, {
                autoClose: 1500,
            });
        })
        .catch(error => {
            toast.error(error.message, {
                autoClose: 1500,
            });
        });
}




</script>
<style></style>
