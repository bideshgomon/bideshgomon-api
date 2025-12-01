<script setup>
import { computed } from 'vue'
import { CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon, InformationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    variant: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
        type: String,
        default: null
    },
    dismissible: {
        type: Boolean,
        default: false
    },
    show: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['dismiss'])

const alertClasses = computed(() => {
    const classes = [
        'rounded-rhythm-md p-4 shadow-rhythm-sm transition-all duration-300 animate-fade-in-down'
    ]
    
    switch (props.variant) {
        case 'success':
            classes.push('bg-growth-50 border-l-4 border-growth-500')
            break
        case 'error':
            classes.push('bg-red-50 border-l-4 border-red-500')
            break
        case 'warning':
            classes.push('bg-sunrise-50 border-l-4 border-sunrise-500')
            break
        case 'info':
            classes.push('bg-sky-50 border-l-4 border-sky-500')
            break
    }
    
    return classes.join(' ')
})

const iconComponent = computed(() => {
    switch (props.variant) {
        case 'success': return CheckCircleIcon
        case 'error': return XCircleIcon
        case 'warning': return ExclamationTriangleIcon
        case 'info': return InformationCircleIcon
        default: return InformationCircleIcon
    }
})

const iconColorClass = computed(() => {
    switch (props.variant) {
        case 'success': return 'text-growth-500'
        case 'error': return 'text-red-500'
        case 'warning': return 'text-sunrise-500'
        case 'info': return 'text-sky-500'
        default: return 'text-sky-500'
    }
})

const textColorClass = computed(() => {
    switch (props.variant) {
        case 'success': return 'text-growth-900'
        case 'error': return 'text-red-900'
        case 'warning': return 'text-sunrise-900'
        case 'info': return 'text-sky-900'
        default: return 'text-sky-900'
    }
})

const handleDismiss = () => {
    emit('dismiss')
}
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform -translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform -translate-y-2"
    >
        <div v-if="show" :class="alertClasses">
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <component
                    :is="iconComponent"
                    :class="[iconColorClass, 'h-5 w-5 flex-shrink-0 mt-0.5']"
                />
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <h3
                        v-if="title"
                        :class="[textColorClass, 'text-sm font-semibold mb-1']"
                    >
                        {{ title }}
                    </h3>
                    <div :class="[textColorClass, 'text-sm']">
                        <slot />
                    </div>
                </div>
                
                <!-- Dismiss button -->
                <button
                    v-if="dismissible"
                    type="button"
                    :class="[iconColorClass, 'flex-shrink-0 hover:opacity-75 transition-opacity']"
                    @click="handleDismiss"
                >
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>
        </div>
    </Transition>
</template>
