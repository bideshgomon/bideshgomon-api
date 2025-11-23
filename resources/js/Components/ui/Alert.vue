<script setup>
/**
 * Alert Component
 * Design System V2 - BideshGomon
 *
 * Usage:
 * <Alert type="success" title="Success!" dismissible>Your changes have been saved.</Alert>
 * <Alert type="error" :icon="ExclamationTriangleIcon">An error occurred.</Alert>
 */

import { ref, computed } from 'vue'
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XCircleIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
  },
  title: {
    type: String,
    default: '',
  },
  icon: {
    type: Object,
    default: null,
  },
  dismissible: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['dismiss'])

const isVisible = ref(true)

const alertClasses = computed(() => {
  const base = [
    'flex items-start gap-3',
    'px-4 py-3',
    'rounded-md', // Design System V2: rounded-md (6px)
    'border-l-4', // Design System V2: left border accent
  ]

  // Design System V2 colors (brand-green-400 for success, red for error)
  const types = {
    success: 'bg-brand-green-50 border-brand-green-400 text-brand-green-700',
    error: 'bg-red-50 border-red-400 text-red-700',
    warning: 'bg-yellow-50 border-yellow-400 text-yellow-700',
    info: 'bg-blue-50 border-blue-400 text-blue-700',
  }

  base.push(types[props.type])

  return base
})

const iconComponent = computed(() => {
  if (props.icon) {return props.icon}

  return {
    success: CheckCircleIcon,
    error: XCircleIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon,
  }[props.type]
})

const iconColorClasses = computed(() => ({
  success: 'text-brand-green-400',
  error: 'text-red-400',
  warning: 'text-yellow-400',
  info: 'text-blue-400',
}[props.type]))

const dismiss = () => {
  isVisible.value = false
  emit('dismiss')
}
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-200"
    enter-from-class="opacity-0 transform scale-95"
    enter-to-class="opacity-100 transform scale-100"
    leave-active-class="transition-all duration-200"
    leave-from-class="opacity-100 transform scale-100"
    leave-to-class="opacity-0 transform scale-95"
  >
    <div v-if="isVisible" :class="alertClasses" role="alert">
      <!-- Icon -->
      <component
        :is="iconComponent"
        :class="['w-5 h-5 flex-shrink-0', iconColorClasses]"
      />

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <p v-if="title" class="font-semibold mb-1">
          {{ title }}
        </p>
        <div class="text-sm">
          <slot></slot>
        </div>
      </div>

      <!-- Dismiss Button -->
      <button
        v-if="dismissible"
        class="flex-shrink-0 text-current opacity-50 hover:opacity-100 transition-opacity"
        aria-label="Dismiss"
        @click="dismiss"
      >
        <XMarkIcon class="w-5 h-5" />
      </button>
    </div>
  </Transition>
</template>
