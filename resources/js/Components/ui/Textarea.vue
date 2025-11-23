<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  rows: {
    type: [String, Number],
    default: 4,
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const textareaClasses = computed(() => [
  'block w-full px-4 py-2.5 border rounded-lg transition-all',
  'focus:ring-2 focus:ring-brand-primary focus:border-transparent',
  'dark:bg-gray-800 dark:text-white dark:border-gray-600',
  props.error
    ? 'border-red-500 focus:ring-red-500'
    : 'border-gray-300',
  props.disabled
    ? 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-900'
    : 'bg-white',
])

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<template>
  <div class="w-full">
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    <textarea
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      :required="required"
      :disabled="disabled"
      :class="textareaClasses"
      @input="handleInput"
    ></textarea>
    <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400">
      {{ error }}
    </p>
  </div>
</template>
