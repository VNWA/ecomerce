<template>
    <div class="w-full h-full mb-3 text-black">



        <div v-if="isImage" class="w-full h-full py-2 px-1 flex gap-4 items-center">
            <button @click="isModal = true"
                class="flex gap-4 items-center px-4 py-2 border border-black hover:bg-purple-500/90 hover:text-white uppercase font-bold">
                <Icon icon="fa6-solid:image" /> Insert Image
            </button>

        </div>

        <ckeditor v-model="editer_data" :config="editorConfig" :editor="Editor" @ready="onReady"></ckeditor>
    </div>
</template>
<script setup>
import { ref, watch, computed } from 'vue';
import Editor from 'ckeditor5-custom-build';

const isModal = ref(false);


const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    isImage:{
         type: Boolean,
        default: false
    }
});




const emit = defineEmits(['update:modelValue']);

// Use a computed property to watch changes to modelValue
const computedModelValue = computed(() => props.modelValue);
const editer_data = ref(props.modelValue);

watch(computedModelValue, (newData) => {
    editer_data.value = newData;
});

watch(editer_data, (newData) => {
    emit('update:modelValue', newData);
});

const editorConfig = {
    htmlEmbed: {
        showPreviews: true,
        allowedProviders: ['*'],
    },
    toolbar: {
        items: [

            'heading', '|','fontSize', 'fontFamily', '|',
            'fontColor', 'fontBackgroundColor', '|', 'bold', 'italic', 'underline', 'strikethrough', '|',
            'alignment', '|', 'numberedList', 'bulletedList', '|', 'outdent', 'indent', '|',
            'todoList', 'link', 'blockQuote', 'imageUpload', 'insertTable', 'mediaEmbed', '|',

        ]
    },
    language: 'en',
    image: {
        toolbar: [
            'imageTextAlternative', 'toggleImageCaption', 'imageStyle:inline',
            'imageStyle:block', 'imageStyle:side', 'linkImage'
        ]
    },
    table: {
        contentToolbar: [
            'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties'
        ]
    }
};


const editorInstance = ref(null);

const onReady = (editor) => {
    editorInstance.value = editor;
    editor.ui.getEditableElement().parentElement.insertBefore(
        editor.ui.view.toolbar.element,
        editor.ui.getEditableElement()
    );
};

const handleDataFromChild = (data) => {
    if (data.length > 0) {
        if (editorInstance.value) {
            editorInstance.value.model.change(writer => {
                data.forEach(file => {
                    const imageElement = writer.createElement('imageBlock', {
                        src: file.path,
                        alt: 'vinawebapp.com'
                    });

                    const selection = editorInstance.value.model.document.selection;

                    if (selection.isCollapsed) {
                        editorInstance.value.model.insertContent(imageElement, selection);
                    } else {
                        const endPosition = editorInstance.value.model.createPositionAt(editorInstance.value.model.document.getRoot(), 'end');
                        writer.setSelection(endPosition);
                        editorInstance.value.model.insertContent(imageElement, endPosition);
                    }
                });
            });
        } else {
            console.log('Editor instance is not ready');
        }
        isModal.value = false;
    } else {
        console.warn("Received data is empty.");
    }
};


const handleShortcodeSubmit = (shortcode) => {
    isModalShortCode.value = false
    selectedComponent.value = null
    if (editorInstance.value) {
        editorInstance.value.execute('htmlEmbed', shortcode);
    }
}
</script>


<style src="@/../css/editor.css"></style>
