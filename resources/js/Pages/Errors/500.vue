<script setup>
import { Head, Link } from '@inertiajs/vue3'
import {
  WrenchScrewdriverIcon,
  ClockIcon,
  BellAlertIcon,
  EnvelopeIcon,
  HomeIcon,
} from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const email = ref('')
const subscribed = ref(false)

const subscribe = () => {
  if (email.value && email.value.includes('@')) {
    // In production, this would call an API endpoint
    subscribed.value = true
  }
}

const estimatedTime = '2-3 hours'
const maintenanceReason = 'System Upgrade'
</script>

<template>
  <div>
    <Head title="Service Temporarily Unavailable - 500" />

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl w-full">
        <!-- Maintenance Icon -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-32 h-32 bg-orange-100 rounded-full mb-6 relative">
            <WrenchScrewdriverIcon class="h-20 w-20 text-orange-600 animate-pulse" />
            <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center animate-bounce">
              <span class="text-white text-xs font-bold">!</span>
            </div>
          </div>

          <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
            We'll Be Right Back!
          </h1>
          <p class="text-xl text-gray-600 mb-2">
            BideshGomon is currently undergoing scheduled maintenance.
          </p>
          <p class="text-lg text-gray-500">
            We're working hard to improve your experience.
          </p>
        </div>

        <!-- Status Card -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden mb-8">
          <div class="bg-gradient-to-r from-orange-500 to-red-500 px-8 py-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
              <ClockIcon class="h-7 w-7 mr-3" />
              Maintenance Status
            </h2>
          </div>

          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div class="bg-orange-50 rounded-lg p-5 border-l-4 border-orange-500">
                <p class="text-sm text-gray-600 mb-1">Reason</p>
                <p class="text-lg font-bold text-gray-900">{{ maintenanceReason }}</p>
              </div>

              <div class="bg-blue-50 rounded-lg p-5 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600 mb-1">Estimated Duration</p>
                <p class="text-lg font-bold text-gray-900">{{ estimatedTime }}</p>
              </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-5">
              <div class="flex items-start">
                <BellAlertIcon class="h-6 w-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" />
                <div>
                  <h3 class="font-semibold text-yellow-900 mb-2">What's Happening?</h3>
                  <p class="text-yellow-800 text-sm">
                    We're upgrading our systems to serve you better. This includes:
                  </p>
                  <ul class="mt-3 space-y-1 text-yellow-800 text-sm">
                    <li class="flex items-start">
                      <span class="mr-2">•</span>
                      <span>Enhanced security features</span>
                    </li>
                    <li class="flex items-start">
                      <span class="mr-2">•</span>
                      <span>Faster application processing</span>
                    </li>
                    <li class="flex items-start">
                      <span class="mr-2">•</span>
                      <span>Improved user experience</span>
                    </li>
                    <li class="flex items-start">
                      <span class="mr-2">•</span>
                      <span>New features and services</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-700">Maintenance Progress</span>
            <span class="text-sm font-bold text-orange-600">65%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 h-3 rounded-full animate-pulse" style="width: 65%"></div>
          </div>
          <p class="text-xs text-gray-500 mt-2 text-center">We're making great progress!</p>
        </div>

        <!-- Email Notification Signup -->
        <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
          <div class="text-center mb-6">
            <EnvelopeIcon class="h-12 w-12 text-blue-600 mx-auto mb-3" />
            <h3 class="text-xl font-bold text-gray-900 mb-2">Get Notified When We're Back</h3>
            <p class="text-gray-600">
              We'll send you an email as soon as the maintenance is complete.
            </p>
          </div>

          <div v-if="!subscribed">
            <form @submit.prevent="subscribe" class="flex flex-col sm:flex-row gap-3">
              <input
                v-model="email"
                type="email"
                placeholder="your.email@example.com"
                required
                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
              />
              <button
                type="submit"
                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-lg hover:shadow-xl"
              >
                Notify Me
              </button>
            </form>
          </div>

          <div v-else class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center">
            <p class="text-green-800 font-semibold">✓ You're subscribed!</p>
            <p class="text-green-700 text-sm mt-1">We'll notify you at {{ email }}</p>
          </div>
        </div>

        <!-- What You Can Do -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-8">
          <h3 class="font-semibold text-blue-900 mb-4">💡 What You Can Do in the Meantime</h3>
          <ul class="space-y-3 text-blue-800">
            <li class="flex items-start">
              <span class="mr-2 font-bold">1.</span>
              <span>Check back in {{ estimatedTime }} to access your account</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2 font-bold">2.</span>
              <span>Prepare your documents for future applications</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2 font-bold">3.</span>
              <span>Follow us on social media for live updates</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2 font-bold">4.</span>
              <span>Contact support via email if you have urgent inquiries</span>
            </li>
          </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
          <button
            @click="$router.go(0)"
            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white font-semibold rounded-lg hover:from-orange-700 hover:to-red-700 shadow-lg hover:shadow-xl transition"
          >
            <ClockIcon class="h-5 w-5 mr-2" />
            Refresh Status
          </button>

          <Link
            :href="route('welcome')"
            class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition"
          >
            <HomeIcon class="h-5 w-5 mr-2" />
            Visit Homepage
          </Link>
        </div>

        <!-- Contact Support -->
        <div class="bg-white shadow-lg rounded-lg p-6 text-center">
          <h4 class="font-semibold text-gray-900 mb-4">Need Urgent Assistance?</h4>
          <p class="text-gray-600 mb-4">
            Our support team is still available during maintenance.
          </p>
          <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a
              href="mailto:support@bideshgomon.com"
              class="inline-flex items-center justify-center px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
            >
              <EnvelopeIcon class="h-5 w-5 mr-2" />
              Email Support
            </a>
            <a
              href="tel:+8801712345678"
              class="inline-flex items-center justify-center px-5 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
            >
              📞 +880 1712-345678
            </a>
          </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-8">
          <p class="text-sm text-gray-500">
            Error Code: 503 - Service Temporarily Unavailable
          </p>
          <p class="text-xs text-gray-400 mt-2">
            Thank you for your patience and understanding! 🙏
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}
</style>
