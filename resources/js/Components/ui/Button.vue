<script setup>
/**
 * Button Component
 * Design System V2 - BideshGomon
 *
 * Usage:
 * <Button variant="primary" size="md" @click="handleClick">Save</Button>
 * <Button variant="outline" :loading="isSubmitting">Submit</Button>
 * <Button variant="ghost" size="sm" :icon="PlusIcon">Add</Button>
 */

import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger', 'success'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  icon: {
    type: [Object, Function], // Heroicon component (can be Function or Object)
    default: null,
  },
  iconOnly: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value),
  },
})

const buttonClasses = computed(() => {
  const base = [
    'inline-flex items-center justify-center gap-2',
    'font-semibold transition-all duration-base',
    'focus:outline-none focus:ring-2 focus:ring-offset-2',
    'disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none',
    'active:scale-95',
  ]

  // Variant styles - Design System V2 (Brand Red + Brand Green)
  const variants = {
    primary: 'bg-brand-red-400 hover:bg-brand-red-600 text-white shadow-sm hover:shadow-brand-red focus:ring-brand-red-400',
    secondary: 'bg-gray-100 hover:bg-gray-200 text-gray-700 shadow-sm focus:ring-gray-300',
    success: 'bg-brand-green-400 hover:bg-brand-green-600 text-white shadow-sm hover:shadow-md focus:ring-brand-green-400',
    outline: 'border-2 border-gray-200 hover:border-brand-red-400 text-gray-700 hover:text-brand-red-400 hover:bg-red-50 focus:ring-brand-red-400',
    ghost: 'text-gray-700 hover:bg-gray-100 active:bg-gray-200 focus:ring-gray-300',
    danger: 'bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-md focus:ring-red-500',
  }

  // Size styles - Rounded (Design System V2: rounded-md default)
  const sizes = {
    sm: props.iconOnly ? 'w-8 h-8 rounded-md' : 'h-9 px-4 text-sm rounded-md',
    md: props.iconOnly ? 'w-10 h-10 rounded-md' : 'h-10 px-6 text-base rounded-md',
    lg: props.iconOnly ? 'w-12 h-12 rounded-lg' : 'h-12 px-8 text-lg rounded-lg',
  }

  return [...base, variants[props.variant], sizes[props.size]]
})

const iconSize = computed(() => ({
  sm: 'w-4 h-4',
  md: 'w-5 h-5',
  lg: 'w-6 h-6',
}[props.size]))
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="buttonClasses"
  >
    <!-- Loading Spinner -->
    <svg
      v-if="loading"
      :class="['animate-spin', iconSize]"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      />
    </svg>

    <!-- Icon (if provided) -->
    <component
      :is="icon"
      v-else-if="icon"
      :class="iconSize"
    />

    <!-- Button Text -->
    <span v-if="!iconOnly && !loading">
      <slot></slot>
    </span>
  </button>
</template>
