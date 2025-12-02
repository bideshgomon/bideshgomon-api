<script setup>
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

defineProps({
  canLogin: {
    type: Boolean,
    default: false
  },
  canRegister: {
    type: Boolean,
    default: false
  }
})

const mobileMenuOpen = ref(false)

// Helper function to safely get route URL
const getMenuUrl = (item) => {
  if (item.is_external) return item.url
  if (item.route_name) {
    try {
      return route(item.route_name)
    } catch (error) {
      console.warn(`Route "${item.route_name}" not found for menu item "${item.name}"`)
      return '#'
    }
  }
  return item.url || '#'
}
</script>

<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200">
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <Link :href="route('welcome')" class="flex items-center">
            <ApplicationLogo class="h-10 w-auto" />
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex md:items-center md:space-x-8">
          <template v-for="item in $page.props.menus?.header_main || []" :key="item.id">
            <Link 
              v-if="item.is_active"
              :href="getMenuUrl(item)"
              :target="item.target || '_self'"
              class="text-gray-700 hover:text-green-600 font-medium transition-colors"
            >
              {{ item.name }}
            </Link>
          </template>
        </div>

        <!-- Auth Buttons -->
        <div class="flex items-center space-x-4">
          <template v-if="canLogin">
            <Link
              :href="route('login')"
              class="hidden md:inline-flex text-gray-700 hover:text-green-600 font-medium transition-colors"
            >
              Sign in
            </Link>
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-all shadow-sm hover:shadow-md"
            >
              Get Started
            </Link>
          </template>
          
          <!-- Mobile menu button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
          >
            <Bars3Icon v-if="!mobileMenuOpen" class="h-6 w-6" />
            <XMarkIcon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div
        v-show="mobileMenuOpen"
        class="md:hidden py-4 space-y-2 border-t border-gray-200"
      >
        <template v-for="item in $page.props.menus?.header_main || []" :key="item.id">
          <Link
            v-if="item.is_active"
            :href="getMenuUrl(item)"
            :target="item.target || '_self'"
            class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-medium"
          >
            {{ item.name }}
          </Link>
        </template>
        <template v-if="canLogin">
          <Link
            :href="route('login')"
            class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-medium"
          >
            Sign in
          </Link>
        </template>
      </div>
    </nav>
  </header>
</template>
