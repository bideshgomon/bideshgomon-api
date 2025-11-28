<template>
    <component
        :is="href ? 'a' : 'button'"
        :href="href"
        :type="!href ? type : undefined"
        :class="[
            'inline-flex items-center justify-center',
            'font-semibold text-rhythm transition-all duration-400',
            'focus:outline-none focus:ring-4 focus:ring-offset-2',
            'disabled:opacity-50 disabled:cursor-not-allowed',
            sizeClass,
            variantClass,
            roundedClass,
            shadowClass,
            { 'transform hover:-translate-y-0.5': !disabled },
        ]"
        :disabled="disabled || loading"
    >
        <!-- Loading spinner -->
        <svg 
            v-if="loading" 
            class="animate-spin -ml-1 mr-2 h-4 w-4" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <!-- Icon before -->
        <span v-if="$slots.iconBefore && !loading" :class="['inline-flex', iconBeforeClass]">
            <slot name="iconBefore"></slot>
        </span>

        <!-- Button text -->
        <slot></slot>

        <!-- Icon after -->
        <span v-if="$slots.iconAfter && !loading" :class="['inline-flex transition-transform duration-300', iconAfterClass, { 'group-hover:translate-x-1': !disabled }]">
            <slot name="iconAfter"></slot>
        </span>
    </component>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'ocean', // ocean, sky, growth, sunrise, gold, outline, ghost
    },
    size: {
        type: String,
        default: 'md', // xs, sm, md, lg, xl
    },
    rounded: {
        type: String,
        default: 'xl', // sm, md, lg, xl, full
    },
    href: String,
    type: {
        type: String,
        default: 'button',
    },
    disabled: Boolean,
    loading: Boolean,
    fullWidth: Boolean,
});

const sizeClass = computed(() => {
    const sizes = {
        xs: 'px-3 py-1.5 text-xs',
        sm: 'px-4 py-2 text-sm',
        md: 'px-6 py-3 text-base',
        lg: 'px-8 py-4 text-lg',
        xl: 'px-10 py-5 text-xl',
    };
    return `${sizes[props.size] || sizes.md} ${props.fullWidth ? 'w-full' : ''}`;
});

const roundedClass = computed(() => {
    const rounded = {
        sm: 'rounded-rhythm-sm',
        md: 'rounded-rhythm-md',
        lg: 'rounded-rhythm-lg',
        xl: 'rounded-rhythm-xl',
        full: 'rounded-full',
    };
    return rounded[props.rounded] || rounded.xl;
});

const variantClass = computed(() => {
    const variants = {
        ocean: 'gradient-ocean text-white hover:shadow-glow-ocean focus:ring-ocean-300',
        sky: 'gradient-sky text-white hover:shadow-glow-ocean focus:ring-sky-300',
        growth: 'gradient-growth text-white hover:shadow-glow-growth focus:ring-growth-300',
        sunrise: 'gradient-sunrise text-white hover:shadow-glow-sunrise focus:ring-sunrise-300',
        gold: 'bg-gradient-to-r from-gold-400 to-gold-600 text-white hover:from-gold-500 hover:to-gold-700 focus:ring-gold-300',
        outline: 'bg-white border-2 border-ocean-500 text-ocean-600 hover:bg-ocean-50 focus:ring-ocean-300',
        ghost: 'bg-transparent text-ocean-600 hover:bg-ocean-50 focus:ring-ocean-300',
        danger: 'bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 focus:ring-red-300',
    };
    return variants[props.variant] || variants.ocean;
});

const shadowClass = computed(() => {
    if (props.variant === 'ghost') return '';
    if (props.variant === 'outline') return 'shadow-rhythm-sm hover:shadow-rhythm';
    return 'shadow-rhythm-lg hover:shadow-rhythm-xl';
});

const iconBeforeClass = computed(() => {
    const sizes = {
        xs: 'mr-1',
        sm: 'mr-1.5',
        md: 'mr-2',
        lg: 'mr-2.5',
        xl: 'mr-3',
    };
    return sizes[props.size] || sizes.md;
});

const iconAfterClass = computed(() => {
    const sizes = {
        xs: 'ml-1',
        sm: 'ml-1.5',
        md: 'ml-2',
        lg: 'ml-2.5',
        xl: 'ml-3',
    };
    return sizes[props.size] || sizes.md;
});
</script>

<style scoped>
.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}
</style>
