<script setup>
/**
 * Checkbox Component
 * Material 3 Design System
 *
 * Usage:
 * <Checkbox v-model="form.agree" label="I agree to terms" />
 * <Checkbox v-model="form.newsletter" :error="form.errors.newsletter">Subscribe to newsletter</Checkbox>
 */

import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const emit = defineEmits(['update:modelValue'])

const checkboxClasses = computed(() => {
  const base = [
    'rounded',
    'border-2',
    'transition-colors duration-200',
    'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary',
    'disabled:opacity-50 disabled:cursor-not-allowed',
  ]

  // Size
  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-5 h-5',
    lg: 'w-6 h-6',
  }
  base.push(sizes[props.size])

  // Colors
  if (props.error) {
    base.push('border-red-500 text-red-600')
  } else {
    base.push('border-gray-300 dark:border-gray-600 text-brand-primary')
  }

  return base
})

const labelClasses = computed(() => {
  const base = ['select-none']

  const sizes = {
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
  }
  base.push(sizes[props.size])

  if (props.disabled) {
    base.push('text-gray-400 dark:text-gray-600 cursor-not-allowed')
  } else {
    base.push('text-gray-700 dark:text-gray-300 cursor-pointer')
  }

  return base
})
</script>

<template>
  <div class="w-full">
    <label class="flex items-center gap-3 cursor-pointer">
      <input
        type="checkbox"
        :checked="modelValue"
        :disabled="disabled"
        :class="checkboxClasses"
        @change="emit('update:modelValue', $event.target.checked)"
      />
      <span :class="labelClasses">
        <slot>{{ label }}</slot>
      </span>
    </label>

    <!-- Error Message -->
    <p v-if="error" class="text-sm text-red-600 dark:text-red-400 mt-1 ml-8">
      {{ error }}
    </p>
  </div>
</template>
