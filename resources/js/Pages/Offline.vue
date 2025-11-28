<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
      <!-- Offline Icon -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-6">
          <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
          </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          You're Offline
        </h1>
        
        <p class="text-lg text-gray-600 mb-8">
          No internet connection detected
        </p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
        <!-- Status -->
        <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
          <div class="flex items-center space-x-3">
            <div class="relative">
              <div class="w-3 h-3 bg-red-500 rounded-full"></div>
              <div class="absolute inset-0 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
            </div>
            <span class="text-sm font-medium text-gray-700">Connection Status</span>
          </div>
          <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
            Offline
          </span>
        </div>

        <!-- Info -->
        <div class="space-y-4 mb-8">
          <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-gray-600">
              Don't worry! Some features are still available offline.
            </p>
          </div>

          <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-gray-600">
              Your data will sync automatically when you're back online.
            </p>
          </div>

          <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-gray-600">
              Previously viewed pages are cached for offline access.
            </p>
          </div>
        </div>

        <!-- Retry Button -->
        <button
          @click="retryConnection"
          :disabled="isRetrying"
          class="w-full flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="isRetrying" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span v-if="isRetrying">Checking Connection...</span>
          <span v-else>Try Again</span>
        </button>

        <!-- Go Back Link -->
        <button
          @click="goBack"
          class="w-full mt-3 px-6 py-3 text-gray-700 font-medium hover:bg-gray-50 rounded-lg transition-colors"
        >
          Go Back
        </button>
      </div>

      <!-- Tips -->
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
          <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          Quick Fixes
        </h3>
        <ul class="text-xs text-blue-800 space-y-1">
          <li>• Check your WiFi or mobile data connection</li>
          <li>• Try toggling Airplane mode on and off</li>
          <li>• Move to an area with better signal</li>
          <li>• Restart your device if the issue persists</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const isRetrying = ref(false);

const retryConnection = async () => {
  isRetrying.value = true;

  // Check if online
  if (navigator.onLine) {
    // Redirect to home
    router.visit('/');
  } else {
    // Wait a bit and check again
    setTimeout(() => {
      isRetrying.value = false;
      if (navigator.onLine) {
        router.visit('/');
      }
    }, 2000);
  }
};

const goBack = () => {
  window.history.back();
};

// Auto-check connection
const connectionChecker = setInterval(() => {
  if (navigator.onLine) {
    clearInterval(connectionChecker);
    router.visit('/');
  }
}, 5000);
</script>
