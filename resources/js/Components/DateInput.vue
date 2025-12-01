<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
  modelValue: String,
  required: Boolean,
  disabled: Boolean,
  min: String,
  max: String,
  id: String,
  placeholder: {
    type: String,
    default: 'DD/MM/YYYY'
  }
})

const emit = defineEmits(['update:modelValue'])

const { formatDateFromISO, parseDateToISO } = useBangladeshFormat()

// Display value in DD/MM/YYYY format
const displayValue = ref('')
const isFocused = ref(false)
const showNativePicker = ref(false)

// Initialize display value from ISO format
watch(() => props.modelValue, (newVal) => {
  if (newVal && !isFocused.value) {
    displayValue.value = formatDateFromISO(newVal)
  }
}, { immediate: true })

// Handle input changes
const handleInput = (event) => {
  let value = event.target.value
  displayValue.value = value

  // Auto-format as user types
  value = (value || '').replace(/[^\d/]/g, '') // Only numbers and /
  
  // Auto-add slashes
  if (value.length === 2 && !value.includes('/')) {
    value += '/'
  } else if (value.length === 5 && value.split('/').length === 2) {
    value += '/'
  }
  
  displayValue.value = value

  // Try to parse and emit ISO format
  if (value.length === 10) {
    const isoDate = parseDateToISO(value)
    if (isValidDate(isoDate)) {
      emit('update:modelValue', isoDate)
    }
  } else if (value === '') {
    emit('update:modelValue', '')
  }
}

// Handle blur - validate and format
const handleBlur = () => {
  isFocused.value = false
  if (displayValue.value) {
    const isoDate = parseDateToISO(displayValue.value)
    if (isValidDate(isoDate)) {
      displayValue.value = formatDateFromISO(isoDate)
      emit('update:modelValue', isoDate)
    } else {
      // Invalid date, reset to original value
      displayValue.value = props.modelValue ? formatDateFromISO(props.modelValue) : ''
    }
  }
}

const handleFocus = () => {
  isFocused.value = true
}

// Validate date
const isValidDate = (dateStr) => {
  if (!dateStr) return false
  const date = new Date(dateStr)
  return date instanceof Date && !isNaN(date.getTime())
}

// Open native date picker for mobile/convenience
const openNativePicker = () => {
  showNativePicker.value = true
}

const handleNativeChange = (event) => {
  const isoDate = event.target.value
  if (isoDate) {
    displayValue.value = formatDateFromISO(isoDate)
    emit('update:modelValue', isoDate)
  }
  showNativePicker.value = false
}

// Min/max in DD/MM/YYYY for display
const minDisplay = computed(() => props.min ? formatDateFromISO(props.min) : '')
const maxDisplay = computed(() => props.max ? formatDateFromISO(props.max) : '')

// Cleanup on unmount to prevent observer errors
const inputRef = ref(null)

onBeforeUnmount(() => {
  // Clear any pending operations
  displayValue.value = ''
  showNativePicker.value = false
})

</script>

<template>
  <div class="relative">
    <!-- Text input with DD/MM/YYYY format -->
    <input
      :id="id"
      v-model="displayValue"
      type="text"
      :required="required"
      :disabled="disabled"
      :placeholder="placeholder"
      maxlength="10"
      class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pr-10"
      @input="handleInput"
      @blur="handleBlur"
      @focus="handleFocus"
    />
    
    <!-- Calendar icon button -->
    <button
      type="button"
      @click="openNativePicker"
      :disabled="disabled"
      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
      :class="{ 'cursor-not-allowed opacity-50': disabled }"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
    </button>

    <!-- Hidden native date picker for fallback/mobile -->
    <input
      v-if="showNativePicker"
      type="date"
      :value="modelValue"
      :min="min"
      :max="max"
      :disabled="disabled"
      @change="handleNativeChange"
      @blur="showNativePicker = false"
      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
      autofocus
    />

    <!-- Helper text -->
    <p v-if="minDisplay || maxDisplay" class="mt-1 text-xs text-gray-500">
      <span v-if="minDisplay">Min: {{ minDisplay }}</span>
      <span v-if="minDisplay && maxDisplay"> | </span>
      <span v-if="maxDisplay">Max: {{ maxDisplay }}</span>
    </p>
  </div>
</template>
