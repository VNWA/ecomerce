<template>
    <div>
        <div class="main-container text-black/80" v-if="isLayoutReady">
            <div v-if="isImage" class="w-full h-full py-2 px-1 flex gap-4 items-center">
                <button @click="openFileManager"
                    class="flex gap-4 items-center px-4 py-2 border border-black hover:bg-purple-500/90 hover:text-white uppercase font-bold">
                    <Icon icon="fa6-solid:image" /> VNWA File Manager
                </button>

            </div>
            <div class="editor-container editor-container_classic-editor editor-container_include-block-toolbar"
                ref="editorContainerElement">
                <div class="editor-container__editor">
                    <div ref="editorElement">
                        <ckeditor v-model="editer_data" :config="config" :editor="editor" @ready="onReady"></ckeditor>
                    </div>
                </div>
            </div>
        </div>
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
import { computed, watch } from 'vue';
import { ref } from 'vue';
import { onMounted, onBeforeUnmount } from 'vue';

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

const isModalShortCode = ref(false)
const shortcodes = ref([
    {
        "name": "Hero Section ",
        "desc": "Helo Section",
        "image": "",
        "url": route('UiBlock.HeroSection')
    },
    {
        "name": "Step Slides ",
        "desc": "Step Slides ",
        "image": "",
        "url": route('UiBlock.StepSlide')
    },
    {
        "name": "Youtube Videos ",
        "desc": "Youtube Videos ",
        "image": "",
        "url": route('UiBlock.YoutubeVideos')
    }
]
)

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


const openFileManager = () => {
    isModalShortCode.value = false;
    const width = window.innerWidth;
    const height = window.innerHeight;
    window.open(
        route('Media.Popup'),
        '_blank',
        `width=${width},height=${height},top=0,left=0`
    );

    const handleMessage = (event) => {
        if (event.origin !== window.location.origin) return; // Kiểm tra bảo mật
        const files = event.data;
        console.log(files)
        if (Array.isArray(files) && files.length > 0) {
            window.removeEventListener('message', handleMessage);


            if (editorInstance.value) {
                console.log()
                editorInstance.value.model.change(writer => {
                    files.forEach(file => {
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




        } else {
            console.log("VNWA File Manager Popup Run ....")
        }
    };

    // Đăng ký sự kiện message
    window.addEventListener('message', handleMessage);
    // Hủy đăng ký sự kiện khi component bị unmount
    onBeforeUnmount(() => {
        window.removeEventListener('message', handleMessage);
    });
};


const openUiBlockPopup = (url) => {
    isModalShortCode.value = false;
    const width = window.innerWidth;
    const height = window.innerHeight;

    window.open(
        url,
        '_blank',
        `width=${width},height=${height},top=0,left=0`
    );

    const handleMessage = (event) => {
        if (event.origin !== window.location.origin) return; // Kiểm tra bảo mật
        const shortcode = event.data; // Lấy shortcode từ event.data
        if (typeof shortcode === 'string' && shortcode.trim()) {
            if (editorInstance.value) {
                editorInstance.value.execute('htmlEmbed', shortcode);
            }

            window.removeEventListener('message', handleMessage);
        } else {
            console.log("VNWA UI Block Popup Running ....");
        }
    };

    // Đăng ký sự kiện message
    window.addEventListener('message', handleMessage);

    // Hủy đăng ký sự kiện khi component bị unmount
    onBeforeUnmount(() => {
        window.removeEventListener('message', handleMessage);
    });
};
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
