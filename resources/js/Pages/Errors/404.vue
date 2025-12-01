<script setup>
import { Head, Link } from '@inertiajs/vue3'
import {
  MagnifyingGlassIcon,
  HomeIcon,
  DocumentTextIcon,
  QuestionMarkCircleIcon,
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const searchQuery = ref('')

const popularPages = [
  { name: 'Home', route: 'welcome', icon: HomeIcon, description: 'Return to homepage' },
  { name: 'Services', route: 'services.index', icon: DocumentTextIcon, description: 'Browse our services' },
  { name: 'My Applications', route: 'applications.index', icon: DocumentTextIcon, description: 'View your applications' },
  { name: 'Dashboard', route: 'dashboard', icon: HomeIcon, description: 'Go to dashboard' },
]

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    window.location.href = route('services.index', { search: searchQuery.value })
  }
}
</script>

<template>
  <div>
    <Head title="Page Not Found - 404" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl w-full text-center">
        <!-- 404 Illustration -->
        <div class="mb-8">
          <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
            404
          </h1>
          <div class="flex justify-center mt-4">
            <QuestionMarkCircleIcon class="h-24 w-24 text-gray-400 animate-bounce" />
          </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Oops! Page Not Found
          </h2>
          <p class="text-lg text-gray-600 mb-2">
            We couldn't find the page you're looking for. It might have been moved, deleted, or never existed.
          </p>
          <p class="text-sm text-gray-500">
            Error Code: 404 - The requested URL was not found on this server.
          </p>
        </div>

        <!-- Search Box -->
        <div class="mb-8">
          <form @submit.prevent="handleSearch" class="max-w-md mx-auto">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search for services, pages, or help..."
                class="w-full px-6 py-4 pr-12 text-gray-900 placeholder-gray-500 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
              />
              <button
                type="submit"
                class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
              >
                <MagnifyingGlassIcon class="h-5 w-5" />
              </button>
            </div>
          </form>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
          <h3 class="text-xl font-semibold text-gray-900 mb-6">Quick Navigation</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Link
              v-for="page in popularPages"
              :key="page.route"
              :href="route(page.route)"
              class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:shadow-md transition group"
            >
              <component
                :is="page.icon"
                class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0 group-hover:scale-110 transition"
              />
              <div class="text-left">
                <p class="font-semibold text-gray-900 group-hover:text-blue-600 transition">
                  {{ page.name }}
                </p>
                <p class="text-sm text-gray-600">{{ page.description }}</p>
              </div>
            </Link>
          </div>
        </div>

        <!-- Primary Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <button
            @click="$router.back()"
            class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition"
          >
            <ArrowLeftIcon class="h-5 w-5 mr-2" />
            Go Back
          </button>

          <Link
            :href="route('welcome')"
            class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition"
          >
            <HomeIcon class="h-5 w-5 mr-2" />
            Back to Home
          </Link>
        </div>

        <!-- Help Section -->
        <div class="mt-12 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 text-left">
          <h4 class="font-semibold text-blue-900 mb-3">💡 Need Help?</h4>
          <ul class="space-y-2 text-blue-800 text-sm">
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>Check the URL for typos or try refreshing the page</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>Use the search box above to find what you're looking for</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>
                Contact our support team at 
                <a href="mailto:support@bideshgomon.com" class="underline font-semibold">support@bideshgomon.com</a>
              </span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>Call us at <a href="tel:+8801712345678" class="underline font-semibold">+880 1712-345678</a></span>
            </li>
          </ul>
        </div>

        <!-- Footer Note -->
        <p class="mt-8 text-sm text-gray-500">
          If you believe this is an error, please 
          <a href="mailto:support@bideshgomon.com?subject=404 Error Report" class="text-blue-600 hover:underline">
            report this issue
          </a>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}

.animate-bounce {
  animation: bounce 2s infinite;
}
</style>
