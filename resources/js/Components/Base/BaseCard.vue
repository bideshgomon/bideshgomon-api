<script setup>
import { computed } from 'vue'

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'elevated', 'bordered', 'flat', 'interactive'].includes(value)
    },
    padding: {
        type: String,
        default: 'md',
        validator: (value) => ['none', 'xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    hover: {
        type: Boolean,
        default: false
    },
    clickable: {
        type: Boolean,
        default: false
    }
})

const cardClasses = computed(() => {
    const classes = ['rounded-rhythm-lg transition-all duration-400']
    
    // Variant styles
    switch (props.variant) {
        case 'elevated':
            classes.push('bg-white shadow-rhythm-lg hover:shadow-rhythm-xl')
            break
        case 'bordered':
            classes.push('bg-white border-2 border-gray-200 hover:border-ocean-400')
            break
        case 'flat':
            classes.push('bg-gray-50')
            break
        case 'interactive':
            classes.push('bg-white shadow-rhythm hover:shadow-rhythm-lg transform hover:-translate-y-1 cursor-pointer')
            break
        default:
            classes.push('bg-white shadow-rhythm')
    }
    
    // Padding variants
    const paddingMap = {
        none: '',
        xs: 'p-2',
        sm: 'p-4',
        md: 'p-6',
        lg: 'p-8',
        xl: 'p-12'
    }
    classes.push(paddingMap[props.padding])
    
    // Interactive states
    if (props.hover) {
        classes.push('hover:shadow-rhythm-lg')
    }
    
    if (props.clickable) {
        classes.push('cursor-pointer')
    }
    
    return classes.join(' ')
})
</script>

<template>
    <div :class="cardClasses">
        <slot />
    </div>
</template>
