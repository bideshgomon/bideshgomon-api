<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  SparklesIcon,
  ArrowRightIcon,
  CheckCircleIcon,
  FireIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  services: Array,
  categories: Array,
  featured: Array,
});

const search = ref('');
const selectedCategory = ref('all');

const filteredServices = computed(() => {
  let services = props.services || [];
  
  if (selectedCategory.value !== 'all') {
    services = services.filter(s => {
      const categoryName = typeof s.category === 'object' ? s.category?.name : s.category;
      return categoryName === selectedCategory.value;
    });
  }
  
  if (search.value) {
    const searchLower = search.value.toLowerCase();
    services = services.filter(s => 
      s.name.toLowerCase().includes(searchLower) ||
      s.description.toLowerCase().includes(searchLower)
    );
  }
  
  return services;
});

const getCategoryColor = (category) => {
  const categoryName = typeof category === 'object' ? category?.name : category;
  const colors = {
    visa: 'from-blue-500 to-blue-600',
    travel: 'from-green-500 to-green-600',
    education: 'from-purple-500 to-purple-600',
    employment: 'from-orange-500 to-orange-600',
    documents: 'from-red-500 to-red-600',
    legal: 'from-indigo-500 to-indigo-600',
    financial: 'from-yellow-500 to-yellow-600',
    lifestyle: 'from-pink-500 to-pink-600',
  };
  return colors[categoryName?.toLowerCase()] || 'from-gray-500 to-gray-600';
};

const getCategoryIcon = (category) => {
  const categoryName = typeof category === 'object' ? category?.name : category;
  const icons = {
    visa: '🛂',
    travel: '✈️',
    education: '🎓',
    employment: '💼',
    documents: '📄',
    legal: '⚖️',
    financial: '💰',
    lifestyle: '🏠',
  };
  return icons[categoryName?.toLowerCase()] || '📋';
};
</script>

<template>
  <Head title="Services - Browse All Services" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        Explore Our Services
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold mb-2">38 Services at Your Fingertips</h1>
              <p class="text-indigo-100 text-lg">
                From visa applications to career services - we've got you covered
              </p>
            </div>
            <div class="hidden md:block">
              <SparklesIcon class="h-24 w-24 text-white opacity-20" />
            </div>
          </div>
        </div>

        <!-- Featured Services -->
        <div v-if="featured && featured.length > 0" class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <FireIcon class="h-7 w-7 text-orange-500" />
              Featured Services
            </h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Link
              v-for="service in featured"
              :key="service.id"
              :href="`/services/${service.slug}`"
              class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-orange-200 dark:border-orange-900"
            >
              <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                  <div :class="[
                    'p-3 rounded-lg bg-gradient-to-br',
                    getCategoryColor(service.category)
                  ]">
                    <span class="text-3xl">{{ getCategoryIcon(service.category) }}</span>
                  </div>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                    Popular
                  </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                  {{ service.name }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                  {{ service.description }}
                </p>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ service.category?.name || service.category }}
                  </span>
                  <ArrowRightIcon class="h-5 w-5 text-indigo-600 dark:text-indigo-400 transform group-hover:translate-x-1 transition-transform" />
                </div>
              </div>
            </Link>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-8">
          <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
              <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search services..."
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                />
              </div>
            </div>
            <div class="lg:w-64">
              <div class="relative">
                <FunnelIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                <select
                  v-model="selectedCategory"
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white appearance-none"
                >
                  <option value="all">All Categories</option>
                  <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Services Grid -->
        <div class="mb-4">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing {{ filteredServices.length }} service{{ filteredServices.length !== 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Link
            v-for="service in filteredServices"
            :key="service.id"
            :href="`/services/${service.slug}`"
            class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden"
          >
            <div class="p-6">
              <div class="flex items-start justify-between mb-4">
                <div :class="[
                  'p-3 rounded-lg bg-gradient-to-br',
                  getCategoryColor(service.category)
                ]">
                  <span class="text-3xl">{{ getCategoryIcon(service.category) }}</span>
                </div>
                <div class="flex flex-col gap-1">
                  <span v-if="service.is_featured" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                    Featured
                  </span>
                  <span v-if="service.is_coming_soon" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                    Coming Soon
                  </span>
                </div>
              </div>

              <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                {{ service.name }}
              </h3>

              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                {{ service.description }}
              </p>

              <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  {{ service.category?.name || service.category }}
                </span>
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-medium text-sm">
                  <span>Apply Now</span>
                  <ArrowRightIcon class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" />
                </div>
              </div>
            </div>
          </Link>

          <!-- Empty State -->
          <div v-if="filteredServices.length === 0" class="col-span-full">
            <div class="text-center py-12">
              <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No services found</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Try adjusting your search or filter to find what you're looking for.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
