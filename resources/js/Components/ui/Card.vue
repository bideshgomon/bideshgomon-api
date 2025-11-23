<script setup>
/**
 * Card Component
 * Design System V2 - BideshGomon
 *
 * Usage:
 * <Card>Content</Card>
 * <Card variant="elevated" hoverable>Hover effect</Card>
 * <Card variant="colored" color="success">Success card</Card>
 * <Card padding="none"><img /></Card>
 */

import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'elevated', 'colored'].includes(value),
  },
  color: {
    type: String,
    default: 'gray',
    validator: (value) => ['gray', 'success', 'warning', 'danger', 'info', 'red', 'green'].includes(value),
  },
  padding: {
    type: String,
    default: 'default',
    validator: (value) => ['none', 'sm', 'default', 'lg'].includes(value),
  },
  hoverable: {
    type: Boolean,
    default: false,
  },
  clickable: {
    type: Boolean,
    default: false,
  },
  bordered: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['click'])

const cardClasses = computed(() => {
  const base = [
    'rounded-lg', // Design System V2: rounded-lg default for cards
    'transition-all duration-base',
  ]

  // Variant-specific styles (Design System V2)
  const variants = {
    default: [
      'bg-white',
      'shadow-sm',
      props.bordered ? 'border-2 border-gray-100' : '',
    ],
    elevated: [
      'bg-white',
      'shadow-md',
      props.hoverable ? 'hover:shadow-lg' : '',
    ],
    colored: {
      gray: 'bg-gray-50 border-l-4 border-gray-400',
      success: 'bg-brand-green-50 border-l-4 border-brand-green-400',
      warning: 'bg-yellow-50 border-l-4 border-yellow-400',
      danger: 'bg-red-50 border-l-4 border-red-400',
      info: 'bg-blue-50 border-l-4 border-blue-400',
      red: 'bg-red-50 border-l-4 border-brand-red-400',
      green: 'bg-brand-green-50 border-l-4 border-brand-green-400',
    }
  }

  // Add variant classes
  if (props.variant === 'colored') {
    base.push(variants.colored[props.color])
  } else {
    base.push(...variants[props.variant])
  }

  // Padding (Design System V2: p-6 default)
  const paddings = {
    none: 'p-0',
    sm: 'p-4',
    default: 'p-6',
    lg: 'p-8',
  }
  base.push(paddings[props.padding])

  // Interactive states
  if (props.hoverable || props.clickable) {
    base.push('hover:shadow-lg hover:border-brand-red-400')
  }

  if (props.clickable) {
    base.push('cursor-pointer', 'active:scale-[0.98]')
  }

  return base.filter(Boolean)
})

const handleClick = () => {
  if (props.clickable) {
    emit('click')
  }
}
</script>

<template>
  <div :class="cardClasses" @click="handleClick">
    <slot></slot>
  </div>
</template>
