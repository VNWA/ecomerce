<script setup>
import { ref, watchEffect, computed, watch, nextTick } from 'vue';
import InputLabel from './InputLabel.vue';
import TextInput from './TextInput.vue';
import InputDesc from './Input/InputDesc.vue';
import InputUrlImage from './Input/InputUrlImage.vue';
import SearchEnige from '@/Components/SearchEnige.vue'
import { useAdminStore } from '@/store/admin';
const adminStore = useAdminStore();

const props = defineProps({
    modelValue: {
        type: Object,
        default: ''
    },
    slug: String
});
const emit = defineEmits(['update:modelValue']);

const dataForm = ref({ ...props.modelValue });

watch(() => props.modelValue, (newValue) => {
    dataForm.value = newValue;
});

watch(dataForm, (newValue) => {
    emit('update:modelValue', newValue);
}, { deep: true });

const formContainer = ref(null);
const formHeight = ref('0px');

const updateFormHeight = async () => {
    await nextTick(); // Đảm bảo DOM đã được cập nhật trước khi truy cập thuộc tính

    if (formContainer.value) {
        formHeight.value = adminStore.isMetaSeoFormShow ? `${formContainer.value.scrollHeight}px` : '0px';
    }
};

const formStyle = computed(() => ({
    height: formHeight.value,
}));

const changeInputUrlImage = async () => {
    await updateFormHeight();
    if (formContainer.value) {
        formStyle.value.height = `${formContainer.value.scrollHeight}px`;
    }
};

watchEffect(async () => {
    await updateFormHeight();
});

watch(() => adminStore.isMetaSeoFormShow, async () => {
    await updateFormHeight();
});
</script>

<template>
    <div class="w-full">
        <div class="flex items-center justify-between py-3">
            <h3 class="uppercase text-base font-bold text-black/70">
                Search Engine Optimize
            </h3>
            <h3 @click="adminStore.toggleMetaSeoForm"
                class="text-base text-purple-500 hover:text-purple-600 cursor-pointer">
                Edit SEO meta
            </h3>

        </div>
        <div>
            <SearchEnige :slug="props.slug"
                v-if="dataForm.meta_title && dataForm.meta_image && dataForm.meta_desc && dataForm.meta_title.trim().length > 0 && dataForm.meta_desc.trim().length > 0 && dataForm.meta_image.trim().length > 0"
                :metaTitle="dataForm.meta_title" :metaImage="dataForm.meta_image" :metaDesc="dataForm.meta_desc"
                class="mb-4" />

        </div>
        <div ref="formContainer" class="overflow-hidden transition-all ease-in-out duration-300" :style="formStyle">
            <hr class="mb-2 h-1 bg-black/20">

            <div class="px-3">
                <div class="mb-4">
                    <InputLabel for="InputUrlImage" value="Meta Image" />
                    <InputUrlImage id="InputUrlImage" @change="changeInputUrlImage" class="mt-1"
                        v-model="dataForm.meta_image" />
                </div>
                <div class="mb-4">
                    <InputLabel for="meta_title">Meta Title </InputLabel>
                    <TextInput id="meta_title" v-model="dataForm.meta_title" type="text" class="mt-1 block w-full"
                        required />
                </div>
                <div class="mb-4">
                    <InputLabel for="meta_desc">Meta Desc </InputLabel>
                    <InputDesc id="meta_desc" v-model="dataForm.meta_desc" :maxLength="150"
                        class="mt-1 block w-full min-h-14" />
                </div>
            </div>
        </div>
    </div>
</template>
