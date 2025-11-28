<template>
    <span 
        :class="[
            'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
            'transition-all duration-300',
            sizeClass,
            statusClass,
            { 'animate-pulse-slow': pulse },
        ]"
    >
        <!-- Status dot indicator -->
        <span 
            v-if="showDot"
            :class="[
                'w-1.5 h-1.5 rounded-full mr-1.5',
                dotClass,
            ]"
        ></span>

        <!-- Icon -->
        <component 
            v-if="icon" 
            :is="icon" 
            :class="['mr-1', iconSizeClass]"
        />

        <!-- Status text -->
        <slot>{{ text }}</slot>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
        // pending, processing, approved, completed, rejected, cancelled, active, inactive
    },
    text: String,
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    icon: Object,
    showDot: {
        type: Boolean,
        default: true,
    },
    pulse: {
        type: Boolean,
        default: false,
    },
});

const sizeClass = computed(() => {
    const sizes = {
        sm: 'text-xs px-2 py-0.5',
        md: 'text-xs px-3 py-1',
        lg: 'text-sm px-4 py-1.5',
    };
    return sizes[props.size] || sizes.md;
});

const iconSizeClass = computed(() => {
    const sizes = {
        sm: 'h-3 w-3',
        md: 'h-3.5 w-3.5',
        lg: 'h-4 w-4',
    };
    return sizes[props.size] || sizes.md;
});

const statusClass = computed(() => {
    const statuses = {
        // Application statuses
        pending: 'bg-gold-100 text-gold-700 border border-gold-200',
        processing: 'bg-sky-100 text-sky-700 border border-sky-200',
        approved: 'bg-growth-100 text-growth-700 border border-growth-200',
        completed: 'bg-growth-100 text-growth-700 border border-growth-200',
        rejected: 'bg-red-100 text-red-700 border border-red-200',
        cancelled: 'bg-gray-100 text-gray-700 border border-gray-200',
        
        // Activity statuses
        active: 'bg-green-100 text-green-700 border border-green-200',
        inactive: 'bg-gray-100 text-gray-700 border border-gray-200',
        
        // Payment statuses
        paid: 'bg-growth-100 text-growth-700 border border-growth-200',
        unpaid: 'bg-orange-100 text-orange-700 border border-orange-200',
        refunded: 'bg-blue-100 text-blue-700 border border-blue-200',
        
        // Priority statuses
        high: 'bg-red-100 text-red-700 border border-red-200',
        medium: 'bg-gold-100 text-gold-700 border border-gold-200',
        low: 'bg-gray-100 text-gray-700 border border-gray-200',
        
        // Custom brand statuses
        ocean: 'bg-ocean-100 text-ocean-700 border border-ocean-200',
        sunrise: 'bg-sunrise-100 text-sunrise-700 border border-sunrise-200',
        heritage: 'bg-heritage-100 text-heritage-700 border border-heritage-200',
    };
    return statuses[props.status.toLowerCase()] || statuses.pending;
});

const dotClass = computed(() => {
    const dots = {
        pending: 'bg-gold-500',
        processing: 'bg-sky-500 animate-pulse',
        approved: 'bg-growth-500',
        completed: 'bg-growth-500',
        rejected: 'bg-red-500',
        cancelled: 'bg-gray-500',
        active: 'bg-green-500 animate-pulse',
        inactive: 'bg-gray-400',
        paid: 'bg-growth-500',
        unpaid: 'bg-orange-500',
        refunded: 'bg-blue-500',
        high: 'bg-red-500',
        medium: 'bg-gold-500',
        low: 'bg-gray-500',
        ocean: 'bg-ocean-500',
        sunrise: 'bg-sunrise-500',
        heritage: 'bg-heritage-500',
    };
    return dots[props.status.toLowerCase()] || dots.pending;
});
</script>
