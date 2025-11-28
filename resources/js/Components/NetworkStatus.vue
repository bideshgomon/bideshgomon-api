<template>
  <Transition name="slide-down">
    <div v-if="!isOnline" class="fixed top-0 left-0 right-0 z-50">
      <div class="bg-red-600 text-white px-4 py-3 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
            </svg>
            <div>
              <p class="text-sm font-medium">You're offline</p>
              <p class="text-xs opacity-90">Some features may be limited</p>
            </div>
          </div>
          <button
            @click="retry"
            class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded text-xs font-medium transition-colors"
          >
            Retry
          </button>
        </div>
      </div>
    </div>
  </Transition>

  <Transition name="slide-down">
    <div v-if="showBackOnline" class="fixed top-0 left-0 right-0 z-50">
      <div class="bg-green-600 text-white px-4 py-3 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center space-x-3">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <p class="text-sm font-medium">Back online! Syncing data...</p>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { pwa } from '../pwa';

const isOnline = ref(navigator.onLine);
const showBackOnline = ref(false);

const handleOffline = () => {
  isOnline.value = false;
  showBackOnline.value = false;
};

const handleOnline = () => {
  isOnline.value = true;
  showBackOnline.value = true;

  // Hide the "back online" message after 3 seconds
  setTimeout(() => {
    showBackOnline.value = false;
  }, 3000);
};

const retry = () => {
  window.location.reload();
};

onMounted(() => {
  window.addEventListener('pwa:offline', handleOffline);
  window.addEventListener('pwa:online', handleOnline);
});

onUnmounted(() => {
  window.removeEventListener('pwa:offline', handleOffline);
  window.removeEventListener('pwa:online', handleOnline);
});
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}
</style>
