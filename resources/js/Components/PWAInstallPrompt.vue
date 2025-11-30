<template>
  <Transition name="slide-up">
    <div v-if="showPrompt" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:max-w-sm z-50">
      <div class="bg-gradient-to-br from-blue-50 via-white to-green-50 rounded-xl shadow-2xl border-2 border-blue-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-sm p-4 border-b-2 border-blue-100">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center p-1.5 border-2 border-blue-200 shadow-sm">
                <img src="/images/bideshgomonlogo.png" alt="BideshGomon" class="w-full h-full object-contain opacity-90" />
              </div>
              <div>
                <h3 class="text-gray-900 font-semibold text-sm">Install BideshGomon</h3>
                <p class="text-gray-600 text-xs">Get quick access</p>
              </div>
            </div>
            <button
              @click="dismiss"
              class="text-gray-500 hover:bg-gray-100 rounded-lg p-1 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="p-4">
          <p class="text-sm text-gray-600 mb-4">
            Add BideshGomon to your home screen for faster access and a better experience.
          </p>

          <!-- Features -->
          <div class="space-y-2 mb-4">
            <div class="flex items-center space-x-2 text-xs text-gray-700">
              <svg class="w-4 h-4 text-green-600 flex-shrink-0 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>Works offline</span>
            </div>
            <div class="flex items-center space-x-2 text-xs text-gray-700">
              <svg class="w-4 h-4 text-green-600 flex-shrink-0 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>Instant access from home screen</span>
            </div>
            <div class="flex items-center space-x-2 text-xs text-gray-700">
              <svg class="w-4 h-4 text-green-600 flex-shrink-0 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>Push notifications for updates</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex space-x-2">
            <button
              @click="install"
              :disabled="isInstalling"
              class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 text-sm"
            >
              <span v-if="isInstalling">Installing...</span>
              <span v-else>Install App</span>
            </button>
            <button
              @click="dismiss"
              class="px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg font-medium transition-colors text-sm"
            >
              Later
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { pwa } from '../pwa';

const showPrompt = ref(false);
const isInstalling = ref(false);

// Check if we should show the prompt
const shouldShowPrompt = () => {
  // Don't show if already installed
  if (pwa.isInstalled) return false;

  // Don't show if dismissed recently (within 7 days)
  const dismissedAt = localStorage.getItem('pwa-install-dismissed');
  if (dismissedAt) {
    const daysSinceDismissed = (Date.now() - parseInt(dismissedAt)) / (1000 * 60 * 60 * 24);
    if (daysSinceDismissed < 7) return false;
  }

  // Don't show too early (wait for user to engage)
  const installPromptShownAt = localStorage.getItem('pwa-install-shown');
  if (installPromptShownAt) {
    const timesShown = parseInt(installPromptShownAt) || 0;
    if (timesShown >= 3) return false; // Max 3 times
  }

  return true;
};

const handleShowInstall = () => {
  if (shouldShowPrompt()) {
    // Delay showing by 10 seconds to let user explore
    setTimeout(() => {
      showPrompt.value = true;
      
      // Track how many times shown
      const timesShown = parseInt(localStorage.getItem('pwa-install-shown') || '0');
      localStorage.setItem('pwa-install-shown', (timesShown + 1).toString());
    }, 10000);
  }
};

const handleHideInstall = () => {
  showPrompt.value = false;
};

const install = async () => {
  isInstalling.value = true;
  
  const accepted = await pwa.promptInstall();
  
  if (accepted) {
    showPrompt.value = false;
    localStorage.removeItem('pwa-install-dismissed');
    localStorage.removeItem('pwa-install-shown');
  }
  
  isInstalling.value = false;
};

const dismiss = () => {
  showPrompt.value = false;
  localStorage.setItem('pwa-install-dismissed', Date.now().toString());
};

onMounted(() => {
  window.addEventListener('pwa:showInstall', handleShowInstall);
  window.addEventListener('pwa:hideInstall', handleHideInstall);
  
  // Check on mount
  handleShowInstall();
});

onUnmounted(() => {
  window.removeEventListener('pwa:showInstall', handleShowInstall);
  window.removeEventListener('pwa:hideInstall', handleHideInstall);
});
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  transform: translateY(100%);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>
