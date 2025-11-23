<script setup>
/**
 * Radio Component
 * Material 3 Design System
 *
 * Usage:
 * <Radio v-model="form.plan" value="basic" label="Basic Plan" />
 * <Radio v-model="form.plan" value="premium" label="Premium Plan" />
 */

import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean],
    default: null,
  },
  value: {
    type: [String, Number, Boolean],
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  description: {
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

const isChecked = computed(() => props.modelValue === props.value)

const radioClasses = computed(() => {
  const base = [
    'rounded-full',
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
  base.push('border-gray-300 dark:border-gray-600 text-brand-primary')

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
  <label class="flex items-center gap-3 cursor-pointer">
    <input
      type="radio"
      :value="value"
      :checked="isChecked"
      :disabled="disabled"
      :class="radioClasses"
      @change="emit('update:modelValue', value)"
    />
    <div class="flex-1">
      <span :class="labelClasses">
        <slot>{{ label }}</slot>
      </span>
      <p v-if="description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ description }}
      </p>
    </div>
  </label>
</template>
