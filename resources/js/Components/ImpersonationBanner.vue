<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const loading = ref(false)

const impersonationData = computed(() => page.props.impersonation || {})
const isImpersonating = computed(() => impersonationData.value.isImpersonating || false)

function stopImpersonation() {
  if (loading.value) {return}

  loading.value = true
  
  console.log('Stopping impersonation...', route('admin.impersonate.stop'))

  router.post(route('admin.impersonate.stop'), {}, {
    preserveScroll: false,
    preserveState: false,
    forceFormData: false,
    onSuccess: (page) => {
      console.log('Stop impersonation success, redirecting to admin dashboard...')
      // Small delay to ensure session is fully updated
      setTimeout(() => {
        window.location.href = route('admin.dashboard')
      }, 100)
    },
    onError: (errors) => {
      console.error('Stop impersonation error', errors)
      alert('Failed to stop impersonation. Please try again or contact support.')
      loading.value = false
    },
    onFinish: () => {
      // Don't set loading to false here, let the page reload handle it
    },
  })
}
</script>

<template>
  <div v-if="isImpersonating" class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-orange-500 to-red-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between py-3">
        <div class="flex items-center space-x-3">
          <div class="flex-shrink-0">
            <svg
              class="h-6 w-6 text-white animate-pulse"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-white">
              <span class="font-bold">IMPERSONATION MODE:</span>
              You are currently viewing as
              <span class="font-bold">{{ impersonationData.targetUser.name }}</span>
              <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white bg-opacity-20 text-white">
                {{ impersonationData.targetUser.role?.name || impersonationData.targetUser.role?.slug || 'User' }}
              </span>
            </p>
            <p class="text-xs text-orange-100 mt-0.5">
              All actions are being logged for security audit purposes
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <button
            :disabled="loading"
            class="inline-flex items-center px-4 py-2 bg-white text-red-600 text-sm font-medium rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition disabled:opacity-50 disabled:cursor-not-allowed"
            @click="stopImpersonation"
          >
            <svg
              class="w-4 h-4 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
            {{ loading ? 'Returning...' : 'Return to Admin' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
