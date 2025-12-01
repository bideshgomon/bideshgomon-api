<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  },
  success: {
    type: Boolean,
    default: false
  },
  successMessage: {
    type: String,
    default: 'Changes saved successfully!'
  },
  loading: {
    type: Boolean,
    default: false
  },
  loadingMessage: {
    type: String,
    default: 'Processing...'
  }
})

const emit = defineEmits(['submit'])

const isSubmitting = ref(false)
const showSuccess = ref(false)

const hasErrors = computed(() => {
  return Object.keys(props.errors).length > 0
})

watch(() => props.success, (newVal) => {
  if (newVal) {
    showSuccess.value = true
    setTimeout(() => {
      showSuccess.value = false
    }, 5000)
  }
})

watch(() => props.loading, (newVal) => {
  isSubmitting.value = newVal
})

const handleSubmit = async () => {
  try {
    isSubmitting.value = true
    await emit('submit')
  } catch (error) {
    console.error('Form submission error:', error)
  } finally {
    isSubmitting.value = false
  }
}

const formatFieldName = (field) => {
  return field
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="relative">
    <!-- Loading Overlay -->
    <Transition name="fade">
      <div 
        v-if="isSubmitting" 
        class="absolute inset-0 bg-white/75 backdrop-blur-sm z-50 flex items-center justify-center rounded-lg"
      >
        <div class="text-center">
          <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-sm text-gray-600 font-medium">{{ loadingMessage }}</p>
        </div>
      </div>
    </Transition>

    <!-- Success Banner -->
    <Transition name="slide-down">
      <div 
        v-if="showSuccess" 
        class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 flex items-start gap-3"
      >
        <svg class="h-5 w-5 text-green-400 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <h3 class="text-sm font-medium text-green-800">{{ successMessage }}</h3>
        </div>
        <button 
          type="button"
          @click="showSuccess = false"
          class="text-green-400 hover:text-green-500"
        >
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
          </svg>
        </button>
      </div>
    </Transition>

    <!-- Error Summary Banner -->
    <Transition name="slide-down">
      <div 
        v-if="hasErrors" 
        class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4"
      >
        <div class="flex items-start gap-3">
          <svg class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
          </svg>
          <div class="flex-1">
            <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
              <li v-for="(error, field) in errors" :key="field">
                <span class="font-medium">{{ formatFieldName(field) }}:</span> {{ error }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Form Content -->
    <div class="space-y-6">
      <slot />
    </div>

    <!-- Form Actions -->
    <div class="mt-6 flex items-center justify-end gap-3">
      <slot name="actions" />
    </div>
  </form>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
