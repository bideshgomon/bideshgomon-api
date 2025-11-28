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
  XMarkIcon,
  DocumentCheckIcon,
  AcademicCapIcon,
  BriefcaseIcon,
  DocumentTextIcon,
  BanknotesIcon,
  WrenchScrewdriverIcon,
  GlobeAsiaAustraliaIcon,
} from '@heroicons/vue/24/outline';
import {
  DocumentCheckIcon as DocumentCheckIconSolid,
  AcademicCapIcon as AcademicCapIconSolid,
  BriefcaseIcon as BriefcaseIconSolid,
  DocumentTextIcon as DocumentTextIconSolid,
  BanknotesIcon as BanknotesIconSolid,
  WrenchScrewdriverIcon as WrenchScrewdriverIconSolid,
  Squares2X2Icon,
  GlobeAsiaAustraliaIcon as GlobeAsiaAustraliaIconSolid,
} from '@heroicons/vue/24/solid';

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
  const categoryName = typeof category === 'object' ? category?.name?.toLowerCase() : category?.toLowerCase();
  const colors = {
    'visa services': 'from-blue-500 to-indigo-600',
    'visa': 'from-blue-500 to-indigo-600',
    'travel services': 'from-emerald-500 to-teal-600',
    'travel': 'from-emerald-500 to-teal-600',
    'education services': 'from-purple-500 to-violet-600',
    'education': 'from-purple-500 to-violet-600',
    'employment services': 'from-orange-500 to-amber-600',
    'employment': 'from-orange-500 to-amber-600',
    'document services': 'from-red-500 to-rose-600',
    'documents': 'from-red-500 to-rose-600',
    'financial services': 'from-yellow-500 to-orange-600',
    'financial': 'from-yellow-500 to-orange-600',
    'other services': 'from-pink-500 to-fuchsia-600',
    'other': 'from-pink-500 to-fuchsia-600',
  };
  return colors[categoryName] || 'from-gray-500 to-slate-600';
};

const getCategoryIcon = (category, solid = false) => {
  const categoryName = typeof category === 'object' ? category?.name?.toLowerCase() : category?.toLowerCase();
  const icons = {
    'visa services': solid ? DocumentCheckIconSolid : DocumentCheckIcon,
    'visa': solid ? DocumentCheckIconSolid : DocumentCheckIcon,
    'travel services': solid ? GlobeAsiaAustraliaIconSolid : GlobeAsiaAustraliaIcon,
    'travel': solid ? GlobeAsiaAustraliaIconSolid : GlobeAsiaAustraliaIcon,
    'education services': solid ? AcademicCapIconSolid : AcademicCapIcon,
    'education': solid ? AcademicCapIconSolid : AcademicCapIcon,
    'employment services': solid ? BriefcaseIconSolid : BriefcaseIcon,
    'employment': solid ? BriefcaseIconSolid : BriefcaseIcon,
    'document services': solid ? DocumentTextIconSolid : DocumentTextIcon,
    'documents': solid ? DocumentTextIconSolid : DocumentTextIcon,
    'financial services': solid ? BanknotesIconSolid : BanknotesIcon,
    'financial': solid ? BanknotesIconSolid : BanknotesIcon,
    'other services': solid ? WrenchScrewdriverIconSolid : WrenchScrewdriverIcon,
    'other': solid ? WrenchScrewdriverIconSolid : WrenchScrewdriverIcon,
  };
  return icons[categoryName] || (solid ? Squares2X2Icon : WrenchScrewdriverIcon);
};

const categoryList = computed(() => {
  const cats = props.categories || [];
  return cats.map(cat => {
    const categoryName = typeof cat === 'object' ? cat.name : cat;
    return {
      name: categoryName,
      slug: categoryName?.toLowerCase().replace(/\s+/g, '-') || '',
      color: getCategoryColor(cat),
      icon: getCategoryIcon(cat, false)
    };
  });
});
</script>

<template>
  <Head title="Services - Browse All Services" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        Explore Our Services
      </h2>
    </template>

    <div class="py-6 sm:py-8 pb-20 sm:pb-8">
      <div class="mx-auto max-w-7xl px-3 sm:px-4 md:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 mb-6 sm:mb-8 text-white">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">38 Services at Your Fingertips</h1>
              <p class="text-indigo-100 text-sm sm:text-base md:text-lg">
                From visa applications to career services - we've got you covered
              </p>
            </div>
            <div class="hidden md:block flex-shrink-0">
              <SparklesIcon class="h-16 sm:h-20 md:h-24 w-16 sm:w-20 md:w-24 text-white opacity-20" />
            </div>
          </div>
        </div>

        <!-- Featured Services -->
        <div v-if="featured && featured.length > 0" class="mb-6 sm:mb-8">
          <div class="flex items-center justify-between mb-3 sm:mb-4 px-1">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <FireIcon class="h-5 w-5 sm:h-6 sm:w-6 md:h-7 md:w-7 text-orange-500" />
              <span class="hidden xs:inline">Featured Services</span>
              <span class="xs:hidden">Featured</span>
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
                    'p-4 rounded-xl bg-gradient-to-br shadow-lg',
                    getCategoryColor(service.category)
                  ]">
                    <component :is="getCategoryIcon(service.category, true)" class="h-8 w-8 text-white" />
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
          <!-- Search Bar -->
          <div class="mb-6">
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
              <input
                v-model="search"
                type="text"
                placeholder="Search by service name, description, or keywords..."
                class="w-full pl-12 pr-12 py-4 text-base border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all"
              />
              <button
                v-if="search"
                @click="search = ''"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>

          <!-- Category Filter Pills -->
          <div>
            <div class="flex items-center gap-3 mb-3">
              <FunnelIcon class="h-5 w-5 text-gray-600 dark:text-gray-400 flex-shrink-0" />
              <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                Filter by Category
              </h3>
            </div>
            <div class="flex flex-wrap gap-3">
              <!-- All Categories -->
              <button
                @click="selectedCategory = 'all'"
                :class="[
                  'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200',
                  selectedCategory === 'all'
                    ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30 scale-105'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                ]"
              >
                <Squares2X2Icon class="h-4 w-4" />
                <span>All Categories</span>
                <span :class="[
                  'px-2 py-0.5 rounded-full text-xs font-bold',
                  selectedCategory === 'all' 
                    ? 'bg-white/20 text-white' 
                    : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300'
                ]">
                  {{ services.length }}
                </span>
              </button>

              <!-- Individual Categories -->
              <button
                v-for="cat in categoryList"
                :key="cat.name"
                @click="selectedCategory = cat.name"
                :class="[
                  'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200',
                  selectedCategory === cat.name
                    ? `bg-gradient-to-r ${cat.color} text-white shadow-lg scale-105`
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                ]"
              >
                <component :is="cat.icon" class="h-4 w-4" />
                <span>{{ cat.name }}</span>
                <span :class="[
                  'px-2 py-0.5 rounded-full text-xs font-bold',
                  selectedCategory === cat.name 
                    ? 'bg-white/20 text-white' 
                    : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300'
                ]">
                  {{ services.filter(s => {
                    const categoryName = typeof s.category === 'object' ? s.category?.name : s.category;
                    return categoryName === cat.name;
                  }).length }}
                </span>
              </button>
            </div>
          </div>

          <!-- Active Filters Display -->
          <div v-if="search || selectedCategory !== 'all'" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Active filters:</span>
              <span v-if="selectedCategory !== 'all'" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium">
                {{ selectedCategory }}
                <button @click="selectedCategory = 'all'" class="ml-1 hover:text-indigo-900 dark:hover:text-indigo-100">
                  <XMarkIcon class="h-4 w-4" />
                </button>
              </span>
              <span v-if="search" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-sm font-medium">
                "{{ search }}"
                <button @click="search = ''" class="ml-1 hover:text-purple-900 dark:hover:text-purple-100">
                  <XMarkIcon class="h-4 w-4" />
                </button>
              </span>
              <button 
                @click="search = ''; selectedCategory = 'all'"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium underline"
              >
                Clear all
              </button>
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
                  'p-4 rounded-xl bg-gradient-to-br shadow-lg',
                  getCategoryColor(service.category)
                ]">
                  <component :is="getCategoryIcon(service.category, true)" class="h-8 w-8 text-white" />
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
