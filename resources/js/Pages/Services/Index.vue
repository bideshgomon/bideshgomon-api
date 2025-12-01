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
  UserGroupIcon,
  BuildingOfficeIcon,
  LanguageIcon,
  HeartIcon,
  ShieldCheckIcon,
  ClipboardDocumentListIcon,
  TruckIcon,
  HomeModernIcon,
  BoltIcon,
  MapPinIcon,
  PaperAirplaneIcon,
  BuildingLibraryIcon,
  CurrencyDollarIcon,
  ScaleIcon,
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

// Service icon mapping (matching agency type card style)
const serviceIcons = {
  // Visa Services
  'tourist-visa': DocumentCheckIcon,
  'student-visa': AcademicCapIcon,
  'work-visa': BriefcaseIcon,
  'business-visa': BuildingOfficeIcon,
  'medical-visa': HeartIcon,
  'family-reunion-visa': UserGroupIcon,
  
  // Travel Services
  'flight-booking': PaperAirplaneIcon,
  'hotel-booking': BuildingLibraryIcon,
  'travel-insurance': ShieldCheckIcon,
  'tour-packages': MapPinIcon,
  'airport-transfer': TruckIcon,
  
  // Education Services
  'university-application': BuildingLibraryIcon,
  'school-application': AcademicCapIcon,
  'language-course': LanguageIcon,
  'scholarship-assistance': BanknotesIcon,
  'education-loan': CurrencyDollarIcon,
  'student-accommodation': HomeModernIcon,
  
  // Employment Services
  'job-posting': ClipboardDocumentListIcon,
  'job-application-assistance': BriefcaseIcon,
  'cv-builder': DocumentTextIcon,
  'interview-preparation': UserGroupIcon,
  'skill-verification': ShieldCheckIcon,
  
  // Document Services
  'translation': LanguageIcon,
  'attestation': ShieldCheckIcon,
  'notary': ScaleIcon,
  'certificate-verification': DocumentCheckIcon,
  'police-clearance': ShieldCheckIcon,
  'medical-certificate': HeartIcon,
  
  // Financial Services
  'foreign-exchange': CurrencyDollarIcon,
  'travel-money-card': BanknotesIcon,
  'bank-account-opening': BuildingOfficeIcon,
  
  // Other Services
  'hajj-umrah': GlobeAsiaAustraliaIcon,
  'medical-tourism': HeartIcon,
  'relocation-services': TruckIcon,
  'sim-card-activation': BoltIcon,
  'legal-consultation': ScaleIcon,
};

// Service type badge colors
const serviceTypeBadges = {
  query_based: { label: 'Query-Based', color: 'bg-blue-100 text-blue-800', icon: '💬' },
  api_based: { label: 'Instant', color: 'bg-green-100 text-green-800', icon: '⚡' },
  premade: { label: 'Self-Service', color: 'bg-purple-100 text-purple-800', icon: '🎯' },
  marketplace: { label: 'Marketplace', color: 'bg-orange-100 text-orange-800', icon: '🛍️' },
};

const getServiceIcon = (slug) => {
  return serviceIcons[slug] || DocumentCheckIcon;
};

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
    const searchLower = (search.value || '').toLowerCase();
    services = services.filter(s => 
      (s.name || '').toLowerCase().includes(searchLower) ||
      (s.description || '').toLowerCase().includes(searchLower)
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
    'visa services': 'ocean-500',
    'visa': 'ocean-500',
    'travel services': 'sky-500',
    'travel': 'sky-500',
    'education services': 'heritage-500',
    'education': 'heritage-500',
    'employment services': 'sunrise-500',
    'employment': 'sunrise-500',
    'document services': 'growth-500',
    'documents': 'growth-500',
    'financial services': 'gold-500',
    'financial': 'gold-500',
    'other services': 'gray-500',
    'other': 'gray-500',
  };
  return colors[categoryName] || 'ocean-500';
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
                {{ services.length }} Services at Your Fingertips
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

        <!-- Visual Card Grid (Agency Type Style) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link
            v-for="(service, index) in filteredServices"
            :key="service.id"
            :href="`/services/${service.slug}`"
            class="block group animate-fadeIn"
            :style="{ animationDelay: `${(index % 9) * 50}ms` }"
          >
            <div 
              :class="[
                'relative p-6 rounded-2xl border-2 transition-all duration-300 h-full',
                'hover:shadow-lg hover:scale-[1.02] hover:border-ocean-400',
                service.coming_soon 
                  ? 'bg-gray-50 border-gray-200 opacity-60' 
                  : 'bg-white border-gray-200 hover:bg-ocean-50/30',
              ]"
            >
              <!-- Success Checkmark (for active services) -->
              <div 
                v-if="!service.coming_soon"
                class="absolute top-4 right-4 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center"
              >
                <CheckCircleIcon class="w-4 h-4 text-green-600" />
              </div>

              <!-- Coming Soon Badge -->
              <div 
                v-else
                class="absolute top-4 right-4"
              >
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Coming Soon
                </span>
              </div>

              <!-- Service Icon (Large, Like Agency Types) -->
              <div 
                :class="[
                  'w-16 h-16 rounded-2xl flex items-center justify-center mb-4 transition-all duration-300',
                  service.coming_soon 
                    ? 'bg-gray-100' 
                    : getCategoryColor(service.category).includes('ocean') ? 'bg-blue-100 group-hover:bg-blue-200' :
                      getCategoryColor(service.category).includes('sky') ? 'bg-cyan-100 group-hover:bg-cyan-200' :
                      getCategoryColor(service.category).includes('heritage') ? 'bg-purple-100 group-hover:bg-purple-200' :
                      getCategoryColor(service.category).includes('sunrise') ? 'bg-orange-100 group-hover:bg-orange-200' :
                      getCategoryColor(service.category).includes('growth') ? 'bg-green-100 group-hover:bg-green-200' :
                      getCategoryColor(service.category).includes('gold') ? 'bg-yellow-100 group-hover:bg-yellow-200' :
                      'bg-gray-100 group-hover:bg-gray-200'
                ]"
              >
                <component 
                  :is="getServiceIcon(service.slug)" 
                  :class="[
                    'w-8 h-8',
                    service.coming_soon 
                      ? 'text-gray-400' 
                      : getCategoryColor(service.category).includes('ocean') ? 'text-blue-600' :
                        getCategoryColor(service.category).includes('sky') ? 'text-cyan-600' :
                        getCategoryColor(service.category).includes('heritage') ? 'text-purple-600' :
                        getCategoryColor(service.category).includes('sunrise') ? 'text-orange-600' :
                        getCategoryColor(service.category).includes('growth') ? 'text-green-600' :
                        getCategoryColor(service.category).includes('gold') ? 'text-yellow-600' :
                        'text-gray-600'
                  ]"
                />
              </div>

              <!-- Service Name -->
              <h3 class="text-lg font-bold text-gray-900 mb-2">
                {{ service.name }}
              </h3>

              <!-- Service Description -->
              <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                {{ service.short_description || service.description }}
              </p>

              <!-- Service Type Badge -->
              <div class="flex items-center gap-2 mt-auto">
                <span 
                  v-if="service.service_type && serviceTypeBadges[service.service_type]"
                  :class="[
                    'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold',
                    serviceTypeBadges[service.service_type].color
                  ]"
                >
                  <span>{{ serviceTypeBadges[service.service_type].icon }}</span>
                  <span>{{ serviceTypeBadges[service.service_type].label }}</span>
                </span>

                <!-- Featured Badge -->
                <span 
                  v-if="service.is_featured"
                  class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200"
                >
                  <FireIcon class="w-3 h-3" />
                  <span>Popular</span>
                </span>
              </div>
            </div>
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
