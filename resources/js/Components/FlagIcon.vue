<template>
    <span 
        :class="[
            'fi',
            `fi-${countryCode.toLowerCase()}`,
            sizeClass
        ]"
        :title="countryName"
    ></span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    countryCode: {
        type: String,
        required: true,
        validator: (value) => value && value.length === 2
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
    },
    countryName: {
        type: String,
        default: ''
    }
});

const sizeClass = computed(() => {
    const sizes = {
        'xs': 'text-xs',
        'sm': 'text-sm',
        'md': 'text-lg',
        'lg': 'text-2xl',
        'xl': 'text-3xl',
        '2xl': 'text-5xl'
    };
    return sizes[props.size];
});
</script>

<style scoped>
.fi {
    display: inline-block;
    margin-right: 0.5rem;
    border-radius: 2px;
}
</style>
