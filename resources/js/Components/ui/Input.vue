<script setup>
/**
 * Input Component with Icon Support
 * Design System V2 - BideshGomon
 *
 * Usage:
 * <Input v-model="form.email" type="email" placeholder="Email" />
 * <Input v-model="form.search" :icon="MagnifyingGlassIcon" placeholder="Search..." />
 * <Input v-model="form.password" type="password" :error="form.errors.password" />
 */

import { computed, ref } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  icon: {
    type: [Object, Function], // Heroicon component (left icon) - can be Object or Function
    default: null,
  },
  leftIcon: {
    type: [Object, Function], // Heroicon component (alias for icon)
    default: null,
  },
  rightIcon: {
    type: [Object, Function], // Heroicon component (right side)
    default: null,
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

// Password visibility toggle
const showPassword = ref(false)
const inputType = computed(() => {
  if (props.type === 'password' && showPassword.value) {
    return 'text'
  }
  return props.type
})

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const hasLeftIcon = computed(() => props.icon || props.leftIcon)
const hasRightIcon = computed(() => props.rightIcon || props.type === 'password')

// Inline style fallback to guarantee padding even if Tailwind purges classes
const inputStyle = computed(() => {
  const style = {}
  if (hasLeftIcon.value) {style.paddingLeft = '2.5rem'} // Adjusted for proper spacing
  if (hasRightIcon.value) {style.paddingRight = '2.5rem'} // Adjusted for proper spacing
  return style
})

const inputClasses = computed(() => {
  const base = [
    'w-full',
    'border',
    'rounded-md', // Design System V2: rounded-md (6px)
    'transition-all duration-base',
    'focus:outline-none focus:ring-2 focus:ring-brand-red-400 focus:border-brand-red-400',
    'bg-white',
    'placeholder:text-gray-400',
    'disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50',
  ]

  // Size - consistent height for all inputs
  const sizes = {
    sm: 'h-9 text-sm',
    md: 'h-10 text-base',
    lg: 'h-12 text-lg',
  }
  base.push(sizes[props.size])

  // Padding (adjust for icons) - Fixed: Proper spacing to prevent icon overlap
  if (hasLeftIcon.value && hasRightIcon.value) {
    base.push('pl-10 pr-10')
  } else if (hasLeftIcon.value) {
    base.push('pl-10 pr-3')
  } else if (hasRightIcon.value) {
    base.push('pl-3 pr-10')
  } else {
    base.push('px-3')
  }

  // Border color based on error state (Design System V2)
  if (props.error) {
    base.push('border-red-400 focus:ring-red-400 focus:border-red-400')
  } else {
    base.push('border-gray-200 hover:border-gray-300')
  }

  return base
})

const iconClasses = computed(() => props.error
  ? 'text-red-400'
  : 'text-gray-400')
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

    <!-- Input Container -->
    <div class="relative">
      <!-- Left Icon -->
      <component
        :is="props.icon || props.leftIcon"
        v-if="hasLeftIcon"
        :class="['absolute left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none z-10', iconClasses]"
      />

      <!-- Input -->
      <input
        :value="modelValue"
        :type="inputType"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :class="inputClasses"
        :style="inputStyle"
        @input="emit('update:modelValue', $event.target.value)"
      />

      <!-- Right Icon (default) -->
      <component
        :is="props.rightIcon"
        v-if="props.rightIcon"
        :class="['absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none z-10', iconClasses]"
      />

      <!-- Password Toggle Icon -->
      <button
        v-if="type === 'password' && !props.rightIcon"
        type="button"
        @click="togglePasswordVisibility"
        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 z-10 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
        tabindex="-1"
      >
        <EyeIcon v-if="!showPassword" class="w-5 h-5" />
        <EyeSlashIcon v-else class="w-5 h-5" />
      </button>
    </div>

    <!-- Error Message -->
    <p v-if="error" class="text-sm text-red-600 dark:text-red-400 mt-1">
      {{ error }}
    </p>
  </div>
</template>
