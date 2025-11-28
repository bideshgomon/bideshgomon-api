<template>
    <div 
        :class="[
            'card-rhythm group',
            'hover:shadow-rhythm-xl hover:-translate-y-1',
            paddingClass,
            borderClass,
            bgClass,
        ]"
    >
        <!-- Icon/Badge slot -->
        <div v-if="$slots.icon || badge" class="flex items-start justify-between mb-rhythm-md">
            <div 
                v-if="$slots.icon" 
                :class="[
                    'inline-flex items-center justify-center rounded-rhythm-lg',
                    'transition-all duration-400 group-hover:scale-110',
                    iconBgClass,
                    iconSizeClass,
                ]"
            >
                <slot name="icon"></slot>
            </div>
            <span 
                v-if="badge" 
                :class="[
                    'px-3 py-1 text-xs font-semibold rounded-full',
                    badgeClass,
                ]"
            >
                {{ badge }}
            </span>
        </div>

        <!-- Title -->
        <h3 
            v-if="title" 
            :class="[
                'font-display font-semibold text-rhythm mb-rhythm-sm',
                titleSizeClass,
                titleColorClass,
            ]"
        >
            {{ title }}
        </h3>

        <!-- Description -->
        <p 
            v-if="description" 
            :class="[
                'text-rhythm mb-rhythm-md',
                descriptionClass,
            ]"
        >
            {{ description }}
        </p>

        <!-- Content slot -->
        <div v-if="$slots.default" class="flex-1">
            <slot></slot>
        </div>

        <!-- Footer/Action slot -->
        <div v-if="$slots.footer" class="mt-rhythm-lg pt-rhythm-md border-t border-gray-100">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    description: String,
    badge: String,
    variant: {
        type: String,
        default: 'default', // ocean, sky, growth, sunrise, gold, heritage
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    hoverable: {
        type: Boolean,
        default: true,
    },
});

const paddingClass = computed(() => {
    const sizes = {
        sm: 'p-rhythm-md',
        md: 'p-rhythm-lg',
        lg: 'p-rhythm-xl',
    };
    return sizes[props.size] || sizes.md;
});

const iconSizeClass = computed(() => {
    const sizes = {
        sm: 'w-10 h-10',
        md: 'w-12 h-12',
        lg: 'w-16 h-16',
    };
    return sizes[props.size] || sizes.md;
});

const titleSizeClass = computed(() => {
    const sizes = {
        sm: 'text-lg',
        md: 'text-xl',
        lg: 'text-2xl',
    };
    return sizes[props.size] || sizes.md;
});

const bgClass = computed(() => {
    if (props.variant === 'default') return 'bg-white';
    
    const variants = {
        ocean: 'bg-gradient-to-br from-ocean-50 to-sky-50',
        sky: 'bg-gradient-to-br from-sky-50 to-blue-50',
        growth: 'bg-gradient-to-br from-growth-50 to-emerald-50',
        sunrise: 'bg-gradient-to-br from-sunrise-50 to-orange-50',
        gold: 'bg-gradient-to-br from-gold-50 to-yellow-50',
        heritage: 'bg-gradient-to-br from-heritage-50 to-pink-50',
    };
    return variants[props.variant] || 'bg-white';
});

const borderClass = computed(() => {
    const variants = {
        default: 'border border-gray-100',
        ocean: 'border border-ocean-200',
        sky: 'border border-sky-200',
        growth: 'border border-growth-200',
        sunrise: 'border border-sunrise-200',
        gold: 'border border-gold-200',
        heritage: 'border border-heritage-200',
    };
    return variants[props.variant] || variants.default;
});

const iconBgClass = computed(() => {
    const variants = {
        default: 'bg-gray-100',
        ocean: 'bg-ocean-100',
        sky: 'bg-sky-100',
        growth: 'bg-growth-100',
        sunrise: 'bg-sunrise-100',
        gold: 'bg-gold-100',
        heritage: 'bg-heritage-100',
    };
    return variants[props.variant] || variants.default;
});

const titleColorClass = computed(() => {
    const variants = {
        default: 'text-gray-900',
        ocean: 'text-ocean-900',
        sky: 'text-sky-900',
        growth: 'text-growth-900',
        sunrise: 'text-sunrise-900',
        gold: 'text-gold-900',
        heritage: 'text-heritage-900',
    };
    return variants[props.variant] || variants.default;
});

const descriptionClass = computed(() => {
    return 'text-gray-600';
});

const badgeClass = computed(() => {
    const variants = {
        default: 'bg-gray-100 text-gray-700',
        ocean: 'bg-ocean-100 text-ocean-700',
        sky: 'bg-sky-100 text-sky-700',
        growth: 'bg-growth-100 text-growth-700',
        sunrise: 'bg-sunrise-100 text-sunrise-700',
        gold: 'bg-gold-100 text-gold-700',
        heritage: 'bg-heritage-100 text-heritage-700',
    };
    return variants[props.variant] || variants.default;
});
</script>
