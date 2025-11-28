<template>
    <section 
        :class="[
            'section-rhythm overflow-hidden',
            bgClass,
            { 'animate-fade-in-up': animated },
        ]"
    >
        <!-- Background pattern (optional) -->
        <div v-if="pattern" class="absolute inset-0 opacity-5" :style="patternStyle"></div>

        <!-- Gradient overlay (optional) -->
        <div v-if="gradient" :class="gradientClass"></div>

        <!-- Floating blobs for visual rhythm (optional) -->
        <div v-if="blobs" class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute top-10 left-10 w-72 h-72 bg-ocean-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-growth-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-sunrise-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob" style="animation-delay: 4s;"></div>
        </div>

        <!-- Container -->
        <div :class="[containerClass, 'relative z-10']">
            <!-- Section header -->
            <div v-if="$slots.header || title || subtitle" :class="['text-center mb-rhythm-2xl', headerAlignClass]">
                <!-- Badge/Label -->
                <div v-if="badge" class="inline-flex items-center px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-gray-200 mb-rhythm-md">
                    <component v-if="badgeIcon" :is="badgeIcon" class="h-4 w-4 text-ocean-600 mr-2" />
                    <span class="text-sm font-semibold text-ocean-900">{{ badge }}</span>
                </div>

                <!-- Custom header slot -->
                <slot name="header"></slot>

                <!-- Title -->
                <h2 
                    v-if="title" 
                    :class="[
                        'font-display font-bold text-rhythm mb-rhythm-md',
                        titleSizeClass,
                        titleColorClass,
                    ]"
                >
                    {{ title }}
                </h2>

                <!-- Subtitle -->
                <p 
                    v-if="subtitle" 
                    :class="[
                        'text-rhythm max-w-3xl',
                        subtitleSizeClass,
                        subtitleColorClass,
                        { 'mx-auto': centered },
                    ]"
                >
                    {{ subtitle }}
                </p>
            </div>

            <!-- Content slot -->
            <div :class="contentClass">
                <slot></slot>
            </div>

            <!-- Footer slot -->
            <div v-if="$slots.footer" class="mt-rhythm-2xl">
                <slot name="footer"></slot>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    subtitle: String,
    badge: String,
    badgeIcon: Object,
    variant: {
        type: String,
        default: 'light', // light, dark, ocean, gradient
    },
    container: {
        type: String,
        default: 'default', // default, narrow, wide, full
    },
    centered: {
        type: Boolean,
        default: true,
    },
    pattern: Boolean,
    gradient: Boolean,
    blobs: Boolean,
    animated: {
        type: Boolean,
        default: true,
    },
    contentSpacing: {
        type: String,
        default: 'default', // tight, default, relaxed
    },
});

const bgClass = computed(() => {
    const variants = {
        light: 'bg-white',
        dark: 'bg-gray-900',
        ocean: 'bg-ocean-50',
        sky: 'bg-sky-500',
        growth: 'bg-growth-500',
        sunrise: 'bg-sunrise-50',
        heritage: 'bg-heritage-50',
    };
    return `relative ${variants[props.variant] || variants.light}`;
});

const containerClass = computed(() => {
    const containers = {
        default: 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8',
        narrow: 'mx-auto max-w-5xl px-4 sm:px-6 lg:px-8',
        wide: 'mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8',
        full: 'px-4 sm:px-6 lg:px-8',
    };
    return containers[props.container] || containers.default;
});

const headerAlignClass = computed(() => {
    return props.centered ? 'text-center' : 'text-left';
});

const titleSizeClass = computed(() => {
    return 'text-3xl sm:text-4xl lg:text-5xl';
});

const titleColorClass = computed(() => {
    const variants = {
        light: 'text-gray-900',
        dark: 'text-white',
        ocean: 'text-ocean-900',
        sky: 'text-white',
        growth: 'text-white',
        sunrise: 'text-sunrise-900',
        heritage: 'text-heritage-900',
    };
    return variants[props.variant] || variants.light;
});

const subtitleSizeClass = computed(() => {
    return 'text-lg sm:text-xl';
});

const subtitleColorClass = computed(() => {
    const variants = {
        light: 'text-gray-600',
        dark: 'text-gray-300',
        ocean: 'text-ocean-700',
        sky: 'text-white/90',
        growth: 'text-white/90',
        sunrise: 'text-sunrise-700',
        heritage: 'text-heritage-700',
    };
    return variants[props.variant] || variants.light;
});

const contentClass = computed(() => {
    const spacing = {
        tight: 'space-y-rhythm-md',
        default: 'space-y-rhythm-lg',
        relaxed: 'space-y-rhythm-2xl',
    };
    return spacing[props.contentSpacing] || spacing.default;
});

const gradientClass = computed(() => {
    return 'absolute inset-0 bg-gradient-to-b from-transparent via-white/50 to-white pointer-events-none';
});

const patternStyle = computed(() => {
    return {
        backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
    };
});
</script>
