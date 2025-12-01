<script setup>
import { computed } from 'vue'

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'primary', 'success', 'warning', 'danger', 'info'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    rounded: {
        type: Boolean,
        default: false
    },
    removable: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['remove'])

const badgeClasses = computed(() => {
    const classes = [
        'inline-flex items-center gap-1 font-medium transition-all duration-300'
    ]
    
    // Size variants
    const sizeMap = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-3 py-1 text-sm',
        lg: 'px-4 py-1.5 text-base'
    }
    classes.push(sizeMap[props.size])
    
    // Rounded
    if (props.rounded) {
        classes.push('rounded-full')
    } else {
        classes.push('rounded-rhythm-sm')
    }
    
    // Variant colors
    switch (props.variant) {
        case 'primary':
            classes.push('bg-ocean-100 text-ocean-800 border border-ocean-200')
            break
        case 'success':
            classes.push('bg-growth-100 text-growth-800 border border-growth-200')
            break
        case 'warning':
            classes.push('bg-sunrise-100 text-sunrise-800 border border-sunrise-200')
            break
        case 'danger':
            classes.push('bg-red-100 text-red-800 border border-red-200')
            break
        case 'info':
            classes.push('bg-sky-100 text-sky-800 border border-sky-200')
            break
        default:
            classes.push('bg-gray-100 text-gray-800 border border-gray-200')
    }
    
    return classes.join(' ')
})

const handleRemove = (e) => {
    e.stopPropagation()
    emit('remove')
}
</script>

<template>
    <span :class="badgeClasses">
        <slot />
        <button
            v-if="removable"
            type="button"
            class="hover:bg-black/10 rounded-full p-0.5 transition-colors"
            @click="handleRemove"
        >
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </span>
</template>
