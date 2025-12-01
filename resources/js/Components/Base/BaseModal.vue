<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    maxWidth: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '4xl', '6xl'].includes(value)
    },
    closeable: {
        type: Boolean,
        default: true
    },
    showClose: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['close'])

const maxWidthClass = computed(() => {
    const widths = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
        '4xl': 'max-w-4xl',
        '6xl': 'max-w-6xl'
    }
    return widths[props.maxWidth]
})

const close = () => {
    if (props.closeable) {
        emit('close')
    }
}

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show && props.closeable) {
        close()
    }
}

watch(() => props.show, (value) => {
    if (value) {
        document.body.style.overflow = 'hidden'
    } else {
        document.body.style.overflow = ''
    }
})

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape)
    document.body.style.overflow = ''
})
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="show"
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
                @click="close"
            >
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                
                <!-- Modal panel -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-show="show"
                        class="relative flex items-center justify-center min-h-screen"
                    >
                        <div
                            :class="[maxWidthClass, 'w-full']"
                            @click.stop
                        >
                            <div class="bg-white rounded-rhythm-xl shadow-rhythm-xl overflow-hidden animate-fade-in-up">
                                <!-- Close button -->
                                <button
                                    v-if="showClose && closeable"
                                    type="button"
                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors z-10"
                                    @click="close"
                                >
                                    <XMarkIcon class="h-6 w-6" />
                                </button>
                                
                                <!-- Header slot -->
                                <div v-if="$slots.header" class="px-6 py-4 border-b border-gray-200">
                                    <slot name="header" />
                                </div>
                                
                                <!-- Body slot -->
                                <div class="px-6 py-6">
                                    <slot />
                                </div>
                                
                                <!-- Footer slot -->
                                <div v-if="$slots.footer" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                    <slot name="footer" />
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
