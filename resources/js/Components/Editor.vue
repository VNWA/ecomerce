<template>
    <div class="w-full h-full mb-3 text-black">


        <div v-if="isModalShortCode"
            class="fixed w-full h-full top-0 left-0 bg-black/10 backdrop-blur-sm flex items-center justify-center z-50 p-10">
            <div class="min-w-52 min-h-52 max-h-full max-w-full w-full h-full">
                <div class="flex items-center justify-between px-3 pt-2 bg-white rounded-t-lg">
                    <h3 class="font-bold text-xl">UI Block Manager</h3>
                    <button type="button" class="text-3xl font-bold me-5 hover:text-red-500"
                        @click="isModalShortCode = false">
                        <icon :icon="['fas', 'x']" />
                    </button>
                </div>
                <div class="bg-white min-h-96 w-full p-3 overflow-y-scroll">

                    <div class="grid grid-cols-4 gap-4">
                        <div v-for="(item, index) in shortcodes" :key="index" class="col-span-1">
                            <div class="border border-black text-center cursor-pointer"
                                @click="openUiBlockPopup(item.url)">
                                <div class="flex items-center justify-center">
                                    <img :src="item.image || '/images/no_img.jpg'" width="200" alt="Vinawebapp.com">
                                </div>
                                <div class="">
                                    <h3>{{ item.name }}</h3>
                                    <h5>{{ item.desc }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="w-full h-full py-2 px-1 flex gap-4 items-center">
            <button @click="openFileManager"
                class="flex gap-4 items-center px-4 py-2 border border-black hover:bg-purple-500/90 hover:text-white uppercase font-bold">
                <icon :icon="['fas', 'image']" /> Insert Image
            </button>
            <button @click="isModalShortCode = true"
                class="flex gap-4 items-center px-4 py-2 border border-black hover:bg-purple-500/90 hover:text-white uppercase font-bold">
                <icon :icon="['fas', 'image']" /> UI Block
            </button>
        </div>

        <ckeditor v-model="editer_data" :config="config" :editor="editor" @ready="onReady"></ckeditor>
    </div>

</template>

<script setup>
import {
    ClassicEditor,
    AccessibilityHelp,
    Autoformat,
    AutoImage,
    AutoLink,
    Autosave,
    BalloonToolbar,
    Base64UploadAdapter,
    BlockToolbar,
    Bold,
    CloudServices,
    Code,
    CodeBlock,
    Essentials,
    FullPage,
    GeneralHtmlSupport,
    Heading,
    HtmlComment,
    HtmlEmbed,
    ImageBlock,
    ImageCaption,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    Paragraph,
    PasteFromOffice,
    SelectAll,
    ShowBlocks,
    SourceEditing,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Undo
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    isImage: {
        type: Boolean,
        default: false
    }
});


const emit = defineEmits(['update:modelValue']);
const computedModelValue = computed(() => props.modelValue);
const editer_data = ref(props.modelValue);

watch(computedModelValue, (newData) => {
    editer_data.value = newData;
});

watch(editer_data, (newData) => {
    emit('update:modelValue', newData);
});
const isLayoutReady = ref(false)
const config = ref(null)
const editor = ClassicEditor
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
onMounted(() => {
    config.value = {
        toolbar: {
            items: [
                'undo',
                'redo',
                '|',
                'sourceEditing',
                'showBlocks',
                '|',
                'heading',
                '|',
                'bold',
                'italic',
                'code',
                '|',
                'link',
                'insertImage',
                'insertImageViaUrl',
                'insertTable',
                'codeBlock',
                'htmlEmbed',
                '|',
                'bulletedList',
                'numberedList',
                'todoList'
            ],
            shouldNotGroupWhenFull: false
        },
        plugins: [
            AccessibilityHelp,
            Autoformat,
            AutoImage,
            AutoLink,
            Autosave,
            BalloonToolbar,
            Base64UploadAdapter,
            BlockToolbar,
            Bold,
            CloudServices,
            Code,
            CodeBlock,
            Essentials,
            FullPage,
            GeneralHtmlSupport,
            Heading,
            HtmlComment,
            HtmlEmbed,
            ImageBlock,
            ImageCaption,
            ImageInsert,
            ImageInsertViaUrl,
            ImageResize,
            ImageStyle,
            ImageTextAlternative,
            ImageToolbar,
            ImageUpload,
            Italic,
            Link,
            LinkImage,
            List,
            ListProperties,
            Paragraph,
            PasteFromOffice,
            SelectAll,
            ShowBlocks,
            SourceEditing,
            Table,
            TableCaption,
            TableCellProperties,
            TableColumnResize,
            TableProperties,
            TableToolbar,
            TextTransformation,
            TodoList,
            Undo
        ],
        balloonToolbar: ['bold', 'italic', '|', 'link', 'insertImage', '|', 'bulletedList', 'numberedList'],
        blockToolbar: ['bold', 'italic', '|', 'link', 'insertImage', 'insertTable', '|', 'bulletedList', 'numberedList'],
        heading: {
            options: [
                {
                    model: 'paragraph',
                    title: 'Paragraph',
                    class: 'ck-heading_paragraph'
                },
                {
                    model: 'heading1',
                    view: 'h1',
                    title: 'Heading 1',
                    class: 'ck-heading_heading1'
                },
                {
                    model: 'heading2',
                    view: 'h2',
                    title: 'Heading 2',
                    class: 'ck-heading_heading2'
                },
                {
                    model: 'heading3',
                    view: 'h3',
                    title: 'Heading 3',
                    class: 'ck-heading_heading3'
                },
                {
                    model: 'heading4',
                    view: 'h4',
                    title: 'Heading 4',
                    class: 'ck-heading_heading4'
                },
                {
                    model: 'heading5',
                    view: 'h5',
                    title: 'Heading 5',
                    class: 'ck-heading_heading5'
                },
                {
                    model: 'heading6',
                    view: 'h6',
                    title: 'Heading 6',
                    class: 'ck-heading_heading6'
                }
            ]
        },
        htmlSupport: {
            allow: [
                {
                    name: /^.*$/,
                    styles: true,
                    attributes: true,
                    classes: true
                }
            ]
        },
        image: {
            toolbar: [
                'toggleImageCaption',
                'imageTextAlternative',
                '|',
                'imageStyle:alignBlockLeft',
                'imageStyle:block',
                'imageStyle:alignBlockRight',
                '|',
                'resizeImage'
            ],
            styles: {
                options: ['alignBlockLeft', 'block', 'alignBlockRight']
            }
        },
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
            decorators: {
                toggleDownloadable: {
                    mode: 'manual',
                    label: 'Downloadable',
                    attributes: {
                        download: 'file'
                    }
                }
            }
        },
        list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
        },
        menuBar: {
            isVisible: true
        },
        placeholder: 'Type or paste your content here!',
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
        }
    };

    isLayoutReady.value = true;
})


</script>
