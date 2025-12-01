<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger', 'ghost', 'outline'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    href: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'button'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    icon: {
        type: Boolean,
        default: false
    },
    fullWidth: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
    const classes = [
        'inline-flex items-center justify-center font-medium transition-all duration-300',
        'focus:outline-none focus:ring-4 focus:ring-offset-2',
        'disabled:opacity-50 disabled:cursor-not-allowed'
    ]
    
    // Size variants
    const sizeMap = {
        xs: props.icon ? 'p-1.5 text-xs' : 'px-3 py-1.5 text-xs rounded-rhythm-sm',
        sm: props.icon ? 'p-2 text-sm' : 'px-4 py-2 text-sm rounded-rhythm-md',
        md: props.icon ? 'p-2.5 text-base' : 'px-6 py-3 text-base rounded-rhythm-md',
        lg: props.icon ? 'p-3 text-lg' : 'px-8 py-4 text-lg rounded-rhythm-lg',
        xl: props.icon ? 'p-4 text-xl' : 'px-10 py-5 text-xl rounded-rhythm-lg'
    }
    classes.push(sizeMap[props.size])
    
    if (props.icon) {
        classes.push('rounded-rhythm-md')
    }
    
    // Variant styles
    switch (props.variant) {
        case 'primary':
            classes.push(
                'bg-ocean-500 text-white hover:bg-ocean-600',
                'focus:ring-ocean-300 shadow-rhythm hover:shadow-rhythm-lg',
                'active:scale-95'
            )
            break
        case 'secondary':
            classes.push(
                'bg-gray-200 text-gray-900 hover:bg-gray-300',
                'focus:ring-gray-300 shadow-rhythm-sm hover:shadow-rhythm',
                'active:scale-95'
            )
            break
        case 'success':
            classes.push(
                'bg-growth-500 text-white hover:bg-growth-600',
                'focus:ring-growth-300 shadow-rhythm hover:shadow-rhythm-lg',
                'active:scale-95'
            )
            break
        case 'warning':
            classes.push(
                'bg-sunrise-500 text-white hover:bg-sunrise-600',
                'focus:ring-sunrise-300 shadow-rhythm hover:shadow-rhythm-lg',
                'active:scale-95'
            )
            break
        case 'danger':
            classes.push(
                'bg-red-500 text-white hover:bg-red-600',
                'focus:ring-red-300 shadow-rhythm hover:shadow-rhythm-lg',
                'active:scale-95'
            )
            break
        case 'ghost':
            classes.push(
                'bg-transparent text-gray-700 hover:bg-gray-100',
                'focus:ring-gray-200'
            )
            break
        case 'outline':
            classes.push(
                'bg-transparent border-2 border-ocean-500 text-ocean-600 hover:bg-ocean-50',
                'focus:ring-ocean-200'
            )
            break
    }
    
    if (props.fullWidth) {
        classes.push('w-full')
    }
    
    return classes.join(' ')
})

const handleClick = (e) => {
    if (!props.disabled && !props.loading) {
        emit('click', e)
    }
}
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :class="buttonClasses"
        :disabled="disabled || loading"
    >
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
        <slot />
    </Link>
    <button
        v-else
        :type="type"
        :class="buttonClasses"
        :disabled="disabled || loading"
        @click="handleClick"
    >
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
        <slot />
    </button>
</template>
