<script setup>
/**
 * Skeleton Component
 * Loading placeholder for async content
 *
 * Usage:
 * <Skeleton type="text" :lines="3" />
 * <Skeleton type="circle" size="lg" />
 * <Skeleton type="rectangle" height="200px" />
 */

import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'circle', 'rectangle', 'avatar', 'card'].includes(value),
  },
  width: {
    type: String,
    default: '',
  },
  height: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
  },
  lines: {
    type: Number,
    default: 1,
  },
  animated: {
    type: Boolean,
    default: true,
  },
})

const baseClasses = computed(() => {
  const classes = [
    'bg-gray-200 dark:bg-gray-700',
  ]

  if (props.animated) {
    classes.push('animate-pulse')
  }

  return classes
})

const skeletonClasses = computed(() => {
  const classes = [...baseClasses.value]

  // Type-specific styles
  if (props.type === 'text') {
    classes.push('h-4 rounded')
  } else if (props.type === 'circle') {
    classes.push('rounded-full')
    const sizes = {
      sm: 'w-8 h-8',
      md: 'w-12 h-12',
      lg: 'w-16 h-16',
      xl: 'w-24 h-24',
    }
    classes.push(sizes[props.size])
  } else if (props.type === 'rectangle') {
    classes.push('rounded-lg')
  } else if (props.type === 'avatar') {
    classes.push('rounded-full')
    const sizes = {
      sm: 'w-8 h-8',
      md: 'w-10 h-10',
      lg: 'w-12 h-12',
      xl: 'w-16 h-16',
    }
    classes.push(sizes[props.size])
  } else if (props.type === 'card') {
    classes.push('rounded-2xl h-48')
  }

  return classes
})

const style = computed(() => {
  const styles = {}

  if (props.width) {
    styles.width = props.width
  }

  if (props.height) {
    styles.height = props.height
  }

  return styles
})
</script>

<template>
  <div>
    <!-- Text lines -->
    <div
      v-if="type === 'text'"
      class="space-y-3"
    >
      <div
        v-for="line in lines"
        :key="line"
        :class="skeletonClasses"
        :style="{ width: line === lines ? '60%' : '100%', ...style }"
      ></div>
    </div>

    <!-- Circle/Avatar -->
    <div
      v-else-if="type === 'circle' || type === 'avatar'"
      :class="skeletonClasses"
      :style="style"
    ></div>

    <!-- Rectangle/Card -->
    <div
      v-else
      :class="skeletonClasses"
      :style="style"
    ></div>
  </div>
</template>
