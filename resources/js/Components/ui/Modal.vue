<script setup>
/**
 * Modal Component
 * Material 3 Design System
 *
 * Usage:
 * <Modal :show="showModal" @close="showModal = false" title="Confirm Action">
 *   <p>Are you sure?</p>
 *   <template #footer>
 *     <Button @click="showModal = false">Cancel</Button>
 *     <Button variant="primary" @click="confirm">Confirm</Button>
 *   </template>
 * </Modal>
 */

import { computed, watch, onMounted, onUnmounted } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value),
  },
  closable: {
    type: Boolean,
    default: true,
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['close'])

const modalSizes = computed(() => ({
  sm: 'max-w-md',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-4xl',
  full: 'max-w-7xl',
}[props.size]))

const closeModal = () => {
  if (props.closable) {
    emit('close')
  }
}

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && props.show && props.closable) {
    closeModal()
  }
}

watch(() => props.show, (show) => {
  if (show) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape)
  document.body.style.overflow = ''
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeOnBackdrop && closeModal()"
      >
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="show"
            :class="[
              'bg-white dark:bg-gray-800',
              'rounded-2xl shadow-2xl',
              'w-full',
              modalSizes,
              'max-h-[90vh] overflow-y-auto',
            ]"
            @click.stop
          >
            <!-- Header -->
            <div
              v-if="title || closable || $slots.header"
              class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
              <slot name="header">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                  {{ title }}
                </h3>
              </slot>

              <button
                v-if="closable"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                aria-label="Close modal"
                @click="closeModal"
              >
                <XMarkIcon class="w-5 h-5" />
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
              <slot></slot>
            </div>

            <!-- Footer -->
            <div
              v-if="$slots.footer"
              class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
            >
              <slot name="footer"></slot>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
