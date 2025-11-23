<script setup>
/**
 * Badge Component
 * Design System V2 - BideshGomon
 *
 * Usage:
 * <Badge variant="success">Active</Badge>
 * <Badge variant="error" size="sm" :icon="XMarkIcon">Rejected</Badge>
 * <Badge variant="primary">Primary</Badge>
 */

import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'success', 'error', 'warning', 'info', 'primary'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  icon: {
    type: Object,
    default: null,
  },
  dot: {
    type: Boolean,
    default: false,
  },
  rounded: {
    type: Boolean,
    default: true, // Design System V2: rounded by default
  },
})

const badgeClasses = computed(() => {
  const base = [
    'inline-flex items-center gap-1.5',
    'font-medium',
    'whitespace-nowrap',
  ]

  // Size
  const sizes = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-0.5 text-sm',
    lg: 'px-3 py-1 text-base',
  }
  base.push(sizes[props.size])

  // Border radius (Design System V2: rounded-full for badges)
  base.push(props.rounded ? 'rounded-full' : 'rounded-md')

  // Variant colors - Design System V2 (brand-red-400, brand-green-400)
  const variants = {
    default: 'bg-gray-100 text-gray-700 border border-gray-200',
    success: 'bg-brand-green-100 text-brand-green-700 border border-brand-green-200',
    error: 'bg-red-100 text-red-700 border border-red-200',
    warning: 'bg-yellow-100 text-yellow-700 border border-yellow-200',
    info: 'bg-blue-100 text-blue-700 border border-blue-200',
    primary: 'bg-red-100 text-brand-red-700 border border-red-200',
  }
  base.push(variants[props.variant])

  return base
})

const iconSize = computed(() => ({
  sm: 'w-3 h-3',
  md: 'w-3.5 h-3.5',
  lg: 'w-4 h-4',
}[props.size]))

const dotSize = computed(() => ({
  sm: 'w-1.5 h-1.5',
  md: 'w-2 h-2',
  lg: 'w-2.5 h-2.5',
}[props.size]))
</script>

<template>
  <span :class="badgeClasses">
    <!-- Dot indicator -->
    <span
      v-if="dot"
      :class="['rounded-full', dotSize]"
      class="bg-current"
    ></span>

    <!-- Icon -->
    <component
      :is="icon"
      v-if="icon"
      :class="iconSize"
    />

    <!-- Content -->
    <slot></slot>
  </span>
</template>
