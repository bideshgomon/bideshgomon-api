<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';
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

const getCategoryVariant = (category) => {
  const categoryName = typeof category === 'object' ? category?.name?.toLowerCase() : category?.toLowerCase();
  const variants = {
    'visa services': 'ocean',
    'visa': 'ocean',
    'travel services': 'sky',
    'travel': 'sky',
    'education services': 'heritage',
    'education': 'heritage',
    'employment services': 'sunrise',
    'employment': 'sunrise',
    'document services': 'growth',
    'documents': 'growth',
    'financial services': 'gold',
    'financial': 'gold',
    'other services': 'sunrise',
    'other': 'sunrise',
  };
  return variants[categoryName] || 'ocean';
};

const getCategoryColor = (category) => {
  const categoryName = typeof category === 'object' ? category?.name?.toLowerCase() : category?.toLowerCase();
  const colors = {
    'visa services': 'from-ocean-500 to-sky-600',
    'visa': 'from-ocean-500 to-sky-600',
    'travel services': 'from-sky-500 to-growth-600',
    'travel': 'from-sky-500 to-growth-600',
    'education services': 'from-heritage-500 to-heritage-600',
    'education': 'from-heritage-500 to-heritage-600',
    'employment services': 'from-sunrise-500 to-gold-600',
    'employment': 'from-sunrise-500 to-gold-600',
    'document services': 'from-growth-500 to-sky-600',
    'documents': 'from-growth-500 to-sky-600',
    'financial services': 'from-gold-500 to-sunrise-600',
    'financial': 'from-gold-500 to-sunrise-600',
    'other services': 'from-gray-500 to-slate-600',
    'other': 'from-gray-500 to-slate-600',
  };
  return colors[categoryName] || 'from-ocean-500 to-sky-600';
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

    <div class="py-rhythm-lg sm:py-rhythm-xl pb-20 sm:pb-rhythm-xl">
      <div class="mx-auto max-w-7xl px-rhythm-md sm:px-rhythm-lg md:px-rhythm-xl lg:px-rhythm-2xl">
        
        <!-- Hero Section with AnimatedSection -->
        <AnimatedSection 
          variant="growth" 
          :show-blobs="true"
          class="mb-rhythm-xl animate-fadeInUp"
        >
          <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-rhythm-lg">
            <div class="flex-1">
              <h1 class="text-2xl sm:text-3xl font-display font-bold text-white mb-rhythm-sm">
                38 Services at Your Fingertips
              </h1>
              <p class="text-white/90 text-sm sm:text-base md:text-lg">
                From visa applications to career services - we've got you covered
              </p>
            </div>
            <div class="hidden md:block flex-shrink-0">
              <SparklesIcon class="h-16 sm:h-20 md:h-24 w-16 sm:w-20 md:w-24 text-white opacity-20" />
            </div>
          </div>
        </AnimatedSection>

        <!-- Featured Services with RhythmicCard -->
        <div v-if="featured && featured.length > 0" class="mb-rhythm-xl animate-fadeIn" style="animation-delay: 100ms;">
          <div class="flex items-center justify-between mb-rhythm-lg px-1">
            <h2 class="text-xl sm:text-2xl font-display font-bold text-gray-900 dark:text-white flex items-center gap-rhythm-sm">
              <FireIcon class="h-5 w-5 sm:h-6 sm:w-6 md:h-7 md:w-7 text-sunrise-500" />
              <span class="hidden xs:inline">Featured Services</span>
              <span class="xs:hidden">Featured</span>
            </h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-rhythm-lg">
            <Link
              v-for="service in featured"
              :key="service.id"
              :href="`/services/${service.slug}`"
            >
              <RhythmicCard 
                :variant="getCategoryVariant(service.category)"
                size="md"
                hover-lift
                class="h-full border-2 border-sunrise-200 dark:border-sunrise-900/30"
              >
                <template #icon>
                  <component :is="getCategoryIcon(service.category, true)" class="h-6 w-6" />
                </template>
                <template #badge>
                  <StatusBadge status="featured" size="sm" />
                </template>
                <template #default>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-rhythm-sm">
                    {{ service.name }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                    {{ service.description }}
                  </p>
                </template>
                <template #footer>
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                      {{ service.category?.name || service.category }}
                    </span>
                    <ArrowRightIcon class="h-4 w-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" />
                  </div>
                </template>
              </RhythmicCard>
            </Link>
          </div>
        </div>

        <!-- Search and Filter with RhythmicCard -->
        <RhythmicCard 
          variant="light" 
          size="lg" 
          class="mb-rhythm-xl animate-fadeIn" 
          style="animation-delay: 200ms;"
        >
          <template #default>
            <!-- Search Bar -->
            <div class="mb-rhythm-lg">
              <div class="relative">
                <MagnifyingGlassIcon class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search by service name, description, or keywords..."
                  class="w-full pl-12 pr-12 py-4 text-base border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-ocean-500 focus:border-ocean-500 dark:bg-gray-700 dark:text-white transition-all"
                />
                <button
                  v-if="search"
                  @click="search = ''"
                  class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                  <XMarkIcon class="h-5 w-5" />
                </button>
              </div>
            </div>

            <!-- Category Filter Pills -->
            <div>
              <div class="flex items-center gap-rhythm-sm mb-rhythm-md">
                <FunnelIcon class="h-5 w-5 text-gray-600 dark:text-gray-400 flex-shrink-0" />
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                  Filter by Category
                </h3>
              </div>
              <div class="flex flex-wrap gap-rhythm-sm">
                <!-- All Categories Button -->
                <button
                  @click="selectedCategory = 'all'"
                  :class="[
                    'inline-flex items-center gap-rhythm-xs px-rhythm-md py-rhythm-sm rounded-xl font-medium text-sm transition-all duration-300',
                    selectedCategory === 'all'
                      ? 'bg-ocean-600 text-white shadow-rhythmic-md shadow-ocean-500/30 scale-105'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 hover:scale-105'
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

                <!-- Individual Category Buttons -->
                <button
                  v-for="cat in categoryList"
                  :key="cat.name"
                  @click="selectedCategory = cat.name"
                  :class="[
                    'inline-flex items-center gap-rhythm-xs px-rhythm-md py-rhythm-sm rounded-xl font-medium text-sm transition-all duration-300',
                    selectedCategory === cat.name
                      ? `bg-${cat.color.split('-')[1]}-600 text-white shadow-rhythmic-md scale-105`
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 hover:scale-105'
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
            <div v-if="search || selectedCategory !== 'all'" class="mt-rhythm-lg pt-rhythm-lg border-t border-gray-200 dark:border-gray-700">
              <div class="flex flex-wrap items-center gap-rhythm-sm">
                <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Active filters:</span>
                <StatusBadge 
                  v-if="selectedCategory !== 'all'" 
                  :status="selectedCategory.toLowerCase().includes('visa') ? 'processing' : 'active'"
                  size="sm"
                >
                  <template #default>
                    <span class="flex items-center gap-1">
                      {{ selectedCategory }}
                      <button @click="selectedCategory = 'all'" class="ml-1 hover:opacity-70">
                        <XMarkIcon class="h-3 w-3" />
                      </button>
                    </span>
                  </template>
                </StatusBadge>
                <span v-if="search" class="inline-flex items-center gap-1 px-rhythm-sm py-1 rounded-lg bg-heritage-100 dark:bg-heritage-900/30 text-heritage-700 dark:text-heritage-300 text-sm font-medium">
                  "{{ search }}"
                  <button @click="search = ''" class="ml-1 hover:opacity-70">
                    <XMarkIcon class="h-4 w-4" />
                  </button>
                </span>
                <button 
                  @click="search = ''; selectedCategory = 'all'"
                  class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium underline transition-colors"
                >
                  Clear all
                </button>
              </div>
            </div>
          </template>
        </RhythmicCard>

        <!-- Services Grid -->
        <div class="mb-rhythm-md">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-white">{{ filteredServices.length }}</span> service{{ filteredServices.length !== 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
          <Link
            v-for="(service, index) in filteredServices"
            :key="service.id"
            :href="`/services/${service.slug}`"
            class="animate-fadeIn"
            :style="{ animationDelay: `${(index % 9) * 50}ms` }"
          >
            <RhythmicCard 
              :variant="getCategoryVariant(service.category)"
              size="md"
              hover-lift
              class="h-full"
            >
              <template #icon>
                <component :is="getCategoryIcon(service.category, true)" class="h-6 w-6" />
              </template>
              
              <template #badge v-if="service.is_featured || service.is_coming_soon">
                <div class="flex flex-col gap-1">
                  <StatusBadge v-if="service.is_featured" status="featured" size="sm" />
                  <StatusBadge v-if="service.is_coming_soon" status="inactive" size="sm">
                    Coming Soon
                  </StatusBadge>
                </div>
              </template>

              <template #default>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-rhythm-sm">
                  {{ service.name }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                  {{ service.description }}
                </p>
              </template>

              <template #footer>
                <div class="flex items-center justify-between pt-rhythm-md border-t border-gray-100 dark:border-gray-700">
                  <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    {{ service.category?.name || service.category }}
                  </span>
                  <div class="flex items-center gap-rhythm-xs text-ocean-600 dark:text-ocean-400 font-medium text-sm group-hover:gap-rhythm-sm transition-all">
                    <span class="hidden sm:inline">Apply Now</span>
                    <ArrowRightIcon class="h-4 w-4" />
                  </div>
                </div>
              </template>
            </RhythmicCard>
          </Link>

          <!-- Empty State -->
          <div v-if="filteredServices.length === 0" class="col-span-full">
            <div class="text-center py-rhythm-4xl">
              <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400 mb-rhythm-md" />
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-rhythm-xs">No services found</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mb-rhythm-lg">
                Try adjusting your search or filter to find what you're looking for.
              </p>
              <FlowButton 
                variant="secondary"
                size="md"
                @click="search = ''; selectedCategory = 'all'"
              >
                Clear Filters
              </FlowButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
