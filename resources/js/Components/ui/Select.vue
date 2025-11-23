<script setup>
/**
 * Select Component
 * Material 3 Design System
 *
 * Usage:
 * <Select v-model="form.country" :options="countries" label="Country" />
 * <Select v-model="form.role" :options="roles" placeholder="Select role" :error="form.errors.role" />
 */

import { computed } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean, null],
    default: null,
  },
  options: {
    type: Array,
    required: true,
    // Expected format: [{ value: 1, label: 'Option 1' }] or ['option1', 'option2']
  },
  placeholder: {
    type: String,
    default: 'Select an option',
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
  required: {
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

const normalizedOptions = computed(() => props.options.map(option => {
  if (typeof option === 'object' && option !== null) {
    return option
  }
  return { value: option, label: option }
}))

const selectClasses = computed(() => {
  const base = [
    'w-full',
    'border',
    'rounded-md',
    'transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600',
    'bg-white dark:bg-gray-700 dark:text-white',
    'disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 dark:disabled:bg-gray-800',
    'appearance-none',
    'bg-no-repeat',
  ]

  // Size - consistent height and padding with inputs
  const sizes = {
    sm: 'h-9 text-sm pl-4',
    md: 'h-10 text-base pl-4',
    lg: 'h-12 text-lg pl-5',
  }
  base.push(sizes[props.size])

  // Border color based on error state
  if (props.error) {
    base.push('border-red-500 dark:border-red-500')
  } else {
    base.push('border-gray-300 dark:border-gray-600')
  }

  return base
})

// Ensure enough right padding to avoid chevron overlap across all browsers
const selectStyle = computed(() => ({ paddingRight: '2.75rem' }))
</script>

<template>
  <div class="w-full">
    <!-- Label -->
    <label
      v-if="label"
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Select with custom dropdown icon -->
    <div class="relative">
      <select
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="selectClasses"
        :style="selectStyle"
        @change="emit('update:modelValue', $event.target.value)"
      >
        <option value="" disabled :selected="modelValue === null || modelValue === ''">
          {{ placeholder }}
        </option>
        <option
          v-for="option in normalizedOptions"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
      <!-- Custom chevron icon -->
      <ChevronDownIcon class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400" />
    </div>

    <!-- Error Message -->
    <p v-if="error" class="text-sm text-red-600 dark:text-red-400 mt-1">
      {{ error }}
    </p>
  </div>
</template>
