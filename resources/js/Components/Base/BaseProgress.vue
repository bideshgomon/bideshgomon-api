<script setup>
import { computed } from 'vue'

const props = defineProps({
    value: {
        type: Number,
        required: true
    },
    max: {
        type: Number,
        default: 100
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'warning', 'danger', 'gradient'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showLabel: {
        type: Boolean,
        default: false
    },
    animated: {
        type: Boolean,
        default: false
    }
})

const percentage = computed(() => {
    return Math.min(Math.max((props.value / props.max) * 100, 0), 100)
})

const barClasses = computed(() => {
    const classes = ['transition-all duration-500 ease-out rounded-full']
    
    if (props.animated) {
        classes.push('animate-pulse')
    }
    
    switch (props.variant) {
        case 'primary':
            classes.push('bg-ocean-500')
            break
        case 'success':
            classes.push('bg-growth-500')
            break
        case 'warning':
            classes.push('bg-sunrise-500')
            break
        case 'danger':
            classes.push('bg-red-500')
            break
        case 'gradient':
            classes.push('gradient-ocean')
            break
    }
    
    return classes.join(' ')
})

const heightClass = computed(() => {
    const heights = {
        sm: 'h-1.5',
        md: 'h-2.5',
        lg: 'h-4'
    }
    return heights[props.size]
})
</script>

<template>
    <div class="w-full">
        <div v-if="showLabel" class="flex justify-between items-center mb-2">
            <slot name="label">
                <span class="text-sm font-medium text-gray-700">Progress</span>
            </slot>
            <span class="text-sm font-semibold text-gray-900">{{ Math.round(percentage) }}%</span>
        </div>
        
        <div :class="['w-full bg-gray-200 rounded-full overflow-hidden', heightClass]">
            <div
                :class="barClasses"
                :style="{ width: `${percentage}%` }"
                role="progressbar"
                :aria-valuenow="value"
                :aria-valuemin="0"
                :aria-valuemax="max"
            ></div>
        </div>
    </div>
</template>
