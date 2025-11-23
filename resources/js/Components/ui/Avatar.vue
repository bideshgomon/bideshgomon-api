<script setup>
/**
 * Avatar Component
 * Material 3 Design System
 *
 * Usage:
 * <Avatar src="/avatar.jpg" alt="John Doe" />
 * <Avatar :fallback="user.name" size="lg" />
 * <Avatar :online="true" />
 */

import { computed } from 'vue'
import { UserIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  src: {
    type: String,
    default: '',
  },
  alt: {
    type: String,
    default: '',
  },
  fallback: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value),
  },
  online: {
    type: Boolean,
    default: false,
  },
  rounded: {
    type: Boolean,
    default: true,
  },
})

const avatarClasses = computed(() => {
  const base = [
    'relative inline-flex items-center justify-center',
    'bg-gray-200 dark:bg-gray-700',
    'text-gray-600 dark:text-gray-300',
    'font-semibold',
    'overflow-hidden',
  ]

  // Size
  const sizes = {
    xs: 'w-6 h-6 text-xs',
    sm: 'w-8 h-8 text-sm',
    md: 'w-10 h-10 text-base',
    lg: 'w-12 h-12 text-lg',
    xl: 'w-16 h-16 text-xl',
    '2xl': 'w-24 h-24 text-2xl',
  }
  base.push(sizes[props.size])

  // Border radius
  base.push(props.rounded ? 'rounded-full' : 'rounded-lg')

  return base
})

const initials = computed(() => {
  if (!props.fallback) {return ''}

  const parts = props.fallback.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return props.fallback.substring(0, 2).toUpperCase()
})

const onlineIndicatorSize = computed(() => ({
  xs: 'w-1.5 h-1.5',
  sm: 'w-2 h-2',
  md: 'w-2.5 h-2.5',
  lg: 'w-3 h-3',
  xl: 'w-4 h-4',
  '2xl': 'w-5 h-5',
}[props.size]))
</script>

<template>
  <div :class="avatarClasses">
    <!-- Image -->
    <img
      v-if="src"
      :src="src"
      :alt="alt"
      class="w-full h-full object-cover"
    />

    <!-- Fallback: Initials -->
    <span v-else-if="fallback && initials">
      {{ initials }}
    </span>

    <!-- Fallback: Icon -->
    <UserIcon v-else class="w-1/2 h-1/2" />

    <!-- Online Indicator -->
    <span
      v-if="online"
      :class="[
        'absolute bottom-0 right-0',
        'rounded-full',
        'bg-green-500',
        'ring-2 ring-white dark:ring-gray-800',
        onlineIndicatorSize,
      ]"
    ></span>
  </div>
</template>
