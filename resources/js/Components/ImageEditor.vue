<template>
    <div id="vnwa_edit_image" class="bg-purple-500 p-5">
        <div class="grid grid-cols-12 gap-4">
            <div v-if="imageUrl" class="col-span-8 bg-white/80">
                <img ref="image" :src="currentImageUrl" alt="Source Image" />
            </div>
            <div class="col-span-4 ">
                <div class="bg-white/80 p-5 h-full">
                    <div v-if="isCropping" class="flex items-center justify-start gap-4">
                        <div class="flex items-center">
                            <label class="block text-black/80">Width:</label>
                            <input :value="cropWidth" @input="updateCropWidth($event)" type="number" class="w-24 p-2"
                                min="10" />
                        </div>
                        <div class="flex items-center">
                            <label class="block text-black/80">Height:</label>
                            <input :value="cropHeight" @input="updateCropHeight($event)" type="number" class="w-24 p-2"
                                min="10" />
                        </div>
                        <div class="flex items-center">
                            <button @click="cropImage"
                                class="bg-slate-950 text-white px-6 py-2 hover:bg-black/80 hover:text-white font-bold">Save</button>
                        </div>
                        <div class="flex items-center">
                            <button @click="cancelCropping"
                                class="bg-red-600 text-white px-6 py-2 hover:bg-red-600/80 hover:text-white font-bold">Cancel</button>
                        </div>
                    </div>
                    <div v-if="!isCropping" class="mb-4">
                        <button @click="enableCropping"
                            class="bg-slate-950 text-white mx-3 px-6 py-3 hover:bg-black/80 hover:text-white font-bold">Enable
                            Crop</button>
                        <button @click="convertImage"
                            class="bg-slate-950 text-white mx-3 px-6 py-3 hover:bg-black/80 hover:text-white font-bold">Convert
                            to WebP</button>
                        <button @click="resetImage"
                            class="bg-slate-500 text-white mx-3 px-6 py-3 hover:bg-black/80 hover:text-white font-bold">Reset</button>
                        <!-- <button @click="setDragMode('crop')"
                            class="bg-blue-500 text-white px-6 py-2 hover:bg-blue-600 hover:text-white font-bold">Enable
                            Crop Mode</button>
                        <button @click="setDragMode('move')"
                            class="bg-green-500 text-white px-6 py-2 hover:bg-green-600 hover:text-white font-bold">Move
                            Mode</button>
                        <button @click="setDragMode('none')"
                            class="bg-gray-500 text-white px-6 py-2 hover:bg-gray-600 hover:text-white font-bold">Disable
                            Drag</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Cropper from "cropperjs";
import Compressor from "compressorjs";
import 'cropperjs/dist/cropper.css';

export default {
    props: {
        imageUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            currentImageUrl: this.imageUrl,
            cropper: null,
            cropWidth: 100,
            cropHeight: 100,
            isCropping: false,
        };
    },
    watch: {
        imageUrl(newUrl) {
            this.currentImageUrl = newUrl;
            this.initializeCropper(newUrl);
        },
    },
    mounted() {
        this.initializeCropper(this.imageUrl);
    },
    methods: {
        initializeCropper(url) {
            if (this.cropper) {
                this.cropper.destroy();
            }
            const image = this.$refs.image;
            this.$nextTick(() => {
                this.cropper = new Cropper(image, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCrop: true, // Disable auto cropping
                    dragMode: 'move'
                });
            });
        },
        setCopperItem(name){


            this.cropper.setAspectRadio
        },
        setDragMode(mode) {
            if (this.cropper) {
                this.cropper.setDragMode(mode);
            }
        },
        enableCropping() {
            this.isCropping = true;
            if (this.cropper) {
                this.cropper.setDragMode('crop');
            }
        },
        cancelCropping() {
            this.isCropping = false;
            if (this.cropper) {
                this.cropper.clear();
                this.cropper.setDragMode('none');
            }
        },
        cropImage() {
            if (this.cropper) {
                const canvas = this.cropper.getCroppedCanvas();
                this.currentImageUrl = canvas.toDataURL();
                this.isCropping = false;
                this.initializeCropper(this.currentImageUrl);
            }
        },
        convertImage() {
            if (this.currentImageUrl) {
                new Compressor(this.currentImageUrl, {
                    quality: 0.8,
                    convertSize: 2000000,
                    success: (result) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.currentImageUrl = e.target.result;
                            this.initializeCropper(this.currentImageUrl);
                        };
                        reader.readAsDataURL(result);
                    },
                });
            }
        },
        resetImage() {
            this.currentImageUrl = this.imageUrl;
            this.initializeCropper(this.imageUrl);
        },
        updateCropWidth(event) {
            const value = parseInt(event.target.value, 10);
            if (!isNaN(value) && value >= 10) {
                this.cropWidth = value;
                if (this.isCropping && this.cropper) {
                    this.cropper.setCropBoxData({ width: value });
                }
            } else {
                this.cropWidth = 10;
            }
        },
        updateCropHeight(event) {
            const value = parseInt(event.target.value, 10);
            if (!isNaN(value) && value >= 10) {
                this.cropHeight = value;
                if (this.isCropping && this.cropper) {
                    this.cropper.setCropBoxData({ height: value });
                }
            } else {
                this.cropHeight = 10;
            }
        },
    },
};
</script>

<style scoped>
.vnwa_edit_image img {
    max-width: 100%;
    display: block;
}
</style>
