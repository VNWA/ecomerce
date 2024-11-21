<template>
    <div>
        <div class="w-full border bg-white">
            <div class="flex items-center justify-between w-full px-2">
                <div class="flex items-center justify-start ">
                    <img width="100" :src="product.image" loading="lazy" />
                    <div>
                        <h3 class="font-bold text-md capitalize">{{ product.name }}</h3>
                        <h5 class="text-sm leading-4 font-normal mb-1">
                            <span class="text-xs leading-4 font-normal text-gray-500 ">
                                SKU:
                            </span>
                            <span class=" ">{{ product.sku }}</span>
                        </h5>
                        <h5 class="text-sm leading-4 font-normal" v-if="product.price_new">
                            <span class="text-xs leading-4 font-normal text-gray-500">
                                Price:
                            </span>
                            <span class="font-medium "> {{ formatCurrency(product.price_new) }}
                            </span>
                            <span v-if="product.price != product.price_new" class="ms-5 line-through text-gray-500 "> {{ formatCurrency(product.price) }}
                            </span>
                        </h5>
                        <h5 class="text-sm leading-4 font-normal" v-else>
                            <span class="text-xs leading-4 font-normal text-gray-500">
                                Price:
                            </span>
                            <span class="font-medium "> {{ formatCurrency(product.price) }}
                            </span>
                        </h5>
                    </div>

                </div>
                <div>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { formatCurrency } from '@/utils/helpers';


defineProps({
    product: Object
})
</script>

<style></style>
