<template>
    <div class="custom-select text-black/80" :style="{ width: width }">
        <label v-if="label" class="select-label">{{ label }}</label>

        <div class="select-wrapper" :class="type">
            <select :value="modelValue" @change="onSelect" class="select-box">
                <option value="" selected disabled class="capitalize">
                    Select Delivery
                </option>
                <option v-for="(item, index) in options" :key="index" :value="item.value" class="capitalize">
                    {{ item.title }}
                </option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2"
                stroke="currentColor" class="select-icon">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
            </svg>
        </div>
    </div>
</template>

<script lang="ts" setup>
interface Option {
    value: string | number;
    title: string;
}

const props = defineProps<{
    type: string; // Chỉ định kiểu cho width
    width: string; // Chỉ định kiểu cho width
    label?: string;
    modelValue: string | number;
    options: Option[];
}>();

const emit = defineEmits(['update:modelValue']);

function onSelect(event: Event) {
    const target = event.target as HTMLSelectElement;
    emit('update:modelValue', target.value);
}
</script>

<style scoped>
.custom-select {
    width: 100%;
    position: relative;
}

.select-label {
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
    color: #333;
}

.select-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    overflow: hidden;
}

.select-wrapper.thinSelect {
    border-top: 0;
    border-left: 0;
    border-right: 0;
    border-radius: 0;
}

.select-wrapper.thinSelect .select-box {
    padding: 10px 12px;
}

.select-box {
    width: 100%;
    padding: 14px 12px;
    appearance: none;
    border: none;
    outline: none;
    background-color: transparent;
    font-size: 14px;
    color: #333;
    cursor: pointer;
}

.select-box option {
    width: 100%;
    text-transform: capitalize;
}

.select-box:focus {
    border-color: #666;
}

.select-icon {
    position: absolute;
    right: 8px;
    pointer-events: none;
    width: 20px;
    height: 20px;
    color: #666;
}
</style>
