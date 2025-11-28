<template>
  <div
    v-if="show"
    class="fixed bottom-20 md:bottom-4 right-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white px-4 py-3 rounded-lg shadow-xl z-50 max-w-sm"
  >
    <div class="flex items-start space-x-3">
      <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
      <div class="flex-1">
        <p class="text-sm font-semibold mb-1">Slow Connection Detected</p>
        <p class="text-xs opacity-90">
          We've optimized your experience for {{ speed }} networks
        </p>
      </div>
      <button @click="dismiss" class="text-white/80 hover:text-white">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const show = ref(false);
const speed = ref('3G');

onMounted(() => {
  if ('connection' in navigator) {
    const connection = navigator.connection;
    const effectiveType = connection.effectiveType;
    
    // Show warning for slow connections
    if (effectiveType === 'slow-2g' || effectiveType === '2g' || effectiveType === '3g') {
      speed.value = effectiveType.toUpperCase().replace('-', ' ');
      
      // Don't show if dismissed in last 24 hours
      const dismissed = localStorage.getItem('slow-connection-dismissed');
      if (!dismissed || Date.now() - parseInt(dismissed) > 24 * 60 * 60 * 1000) {
        setTimeout(() => {
          show.value = true;
        }, 3000);
      }
    }
  }
});

const dismiss = () => {
  show.value = false;
  localStorage.setItem('slow-connection-dismissed', Date.now().toString());
};
</script>
