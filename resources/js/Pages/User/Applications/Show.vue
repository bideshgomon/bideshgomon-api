<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProgressWave from '@/Components/Rhythmic/ProgressWave.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import {
  ArrowLeftIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  DocumentTextIcon,
  CalendarIcon,
  MapPinIcon,
  BriefcaseIcon,
  CurrencyDollarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  application: Object,
});

const statusColor = computed(() => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    quoted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    in_progress: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    cancelled: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
  };
  return colors[props.application.status] || colors.pending;
});

const statusIcon = computed(() => {
  const icons = {
    pending: ClockIcon,
    quoted: DocumentTextIcon,
    accepted: CheckCircleIcon,
    completed: CheckCircleIcon,
    rejected: XCircleIcon,
    cancelled: XCircleIcon,
  };
  return icons[props.application.status] || ClockIcon;
});

const applicationData = computed(() => {
  try {
    return typeof props.application.application_data === 'string' 
      ? JSON.parse(props.application.application_data) 
      : props.application.application_data || {};
  } catch (e) {
    return {};
  }
});

const applicationSteps = computed(() => {
  const statusMap = {
    pending: 0,
    quoted: 1,
    accepted: 2,
    in_progress: 3,
    completed: 4,
    cancelled: 0,
    rejected: 0,
  };

  return [
    { label: 'Submitted', completed: true },
    { label: 'Quote Received', completed: statusMap[props.application.status] >= 1 },
    { label: 'Quote Accepted', completed: statusMap[props.application.status] >= 2 },
    { label: 'In Progress', completed: statusMap[props.application.status] >= 3 },
    { label: 'Completed', completed: statusMap[props.application.status] >= 4 },
  ];
});

const currentStep = computed(() => {
  const statusMap = {
    pending: 0,
    quoted: 1,
    accepted: 2,
    in_progress: 3,
    completed: 4,
    cancelled: 0,
    rejected: 0,
  };
  return statusMap[props.application.status] || 0;
});
</script>

<template>
  <Head :title="`Application #${application.application_number}`" />

  <AuthenticatedLayout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <Link
          :href="route('user.applications.index')"
          class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6 transition-colors"
        >
          <ArrowLeftIcon class="h-5 w-5" />
          Back to Applications
        </Link>

        <!-- Header -->
        <RhythmicCard variant="light" size="lg" class="mb-6 animate-fadeInUp">
          <div class="flex items-start justify-between mb-6">
            <div>
              <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                  {{ application.service_module.name }}
                </h1>
                <component
                  :is="statusIcon"
                  class="h-6 w-6"
                  :class="statusColor.split(' ')[1]"
                />
              </div>
              <p class="text-gray-600 dark:text-gray-400">
                Application #{{ application.application_number }}
              </p>
            </div>
            <span
              class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
              :class="statusColor"
            >
              {{ application.status.replace('_', ' ').toUpperCase() }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center gap-3">
              <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                <CalendarIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" />
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Submitted</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                  {{ new Date(application.created_at).toLocaleDateString() }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                <DocumentTextIcon class="h-6 w-6 text-purple-600 dark:text-purple-400" />
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Quotes Received</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                  {{ application.quotes?.length || 0 }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                <CurrencyDollarIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Base Price</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                  BDT {{ parseFloat(application.service_module.base_price || 0).toLocaleString() }}
                </p>
              </div>
            </div>
          </div>
        </RhythmicCard>

        <!-- Progress Tracking -->
        <RhythmicCard variant="ocean" size="lg" class="mb-6 animate-fadeIn" style="animation-delay: 100ms;">
          <template #default>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
              Application Progress
            </h2>
            <ProgressWave 
              :steps="applicationSteps"
              :current-step="currentStep"
              variant="ocean"
            />
          </template>
        </RhythmicCard>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Application Details -->
            <RhythmicCard variant="light" size="lg" class="animate-fadeIn" style="animation-delay: 200ms;">
              <template #default>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                Application Details
              </h2>

              <dl class="space-y-4">
                <div v-if="applicationData.destination_country">
                  <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2 mb-1">
                    <MapPinIcon class="h-5 w-5" />
                    Destination Country
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-white ml-7">
                    {{ applicationData.destination_country }}
                  </dd>
                </div>

                <div v-if="applicationData.profession">
                  <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2 mb-1">
                    <BriefcaseIcon class="h-5 w-5" />
                    Profession
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-white ml-7">
                    {{ applicationData.profession }}
                  </dd>
                </div>

                <div v-if="applicationData.purpose">
                  <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2 mb-1">
                    <DocumentTextIcon class="h-5 w-5" />
                    Purpose
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-white ml-7">
                    {{ applicationData.purpose }}
                  </dd>
                </div>

                <div v-if="applicationData.travel_dates">
                  <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2 mb-1">
                    <CalendarIcon class="h-5 w-5" />
                    Travel Dates
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-white ml-7">
                    {{ applicationData.travel_dates.departure }} to {{ applicationData.travel_dates.return }}
                  </dd>
                </div>

                <div v-if="application.special_notes">
                  <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                    Additional Notes
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    {{ application.special_notes }}
                  </dd>
                </div>
              </dl>
              </template>
            </RhythmicCard>

            <!-- Quotes Section -->
            <RhythmicCard variant="light" size="lg" class="animate-fadeIn" style="animation-delay: 300ms;">
              <template #default>
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                  Quotes ({{ application.quotes?.length || 0 }})
                </h2>
                <Link
                  v-if="application.quotes?.length > 0"
                  :href="route('user.applications.quotes', application.id)"
                  class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium"
                >
                  View All →
                </Link>
              </div>

              <div v-if="application.quotes?.length > 0" class="space-y-4">
                <div
                  v-for="quote in application.quotes.slice(0, 3)"
                  :key="quote.id"
                  class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-blue-400 dark:hover:border-blue-600 transition-colors"
                >
                  <div class="flex items-start justify-between mb-3">
                    <div>
                      <h3 class="font-semibold text-gray-900 dark:text-white">
                        {{ quote.agency.name }}
                      </h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ quote.agency.email }}
                      </p>
                    </div>
                    <StatusBadge :status="quote.status" size="sm" />
                  </div>

                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                      <span class="font-semibold text-gray-900 dark:text-white ml-2">
                        BDT {{ parseFloat(quote.quoted_amount).toLocaleString() }}
                      </span>
                    </div>
                    <div>
                      <span class="text-gray-600 dark:text-gray-400">Processing:</span>
                      <span class="font-semibold text-gray-900 dark:text-white ml-2">
                        {{ quote.processing_time_days }} days
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-12">
                <DocumentTextIcon class="h-16 w-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                <p class="text-gray-600 dark:text-gray-400">
                  No quotes received yet
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                  Agencies will send quotes soon
                </p>
              </div>
              </template>
            </RhythmicCard>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Service Info -->
            <RhythmicCard variant="sky" size="md" class="mb-6 animate-fadeIn" style="animation-delay: 400ms;">
              <template #default>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                Service Information
              </h3>
              <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600 dark:text-gray-400">Category:</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    {{ application.service_module.category?.name || 'N/A' }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600 dark:text-gray-400">Processing Time:</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    {{ application.service_module.processing_time?.min }}-{{ application.service_module.processing_time?.max }} 
                    {{ application.service_module.processing_time?.unit }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600 dark:text-gray-400">Base Price:</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    BDT {{ parseFloat(application.service_module.base_price || 0).toLocaleString() }}
                  </span>
                </div>
              </div>
              </template>
            </RhythmicCard>

            <!-- Actions -->
            <RhythmicCard variant="gradient" size="md" class="animate-fadeIn" style="animation-delay: 500ms;">
              <template #default>
                <h3 class="text-lg font-bold text-white mb-4">Need Help?</h3>
                <p class="text-white/90 text-sm mb-4">
                  If you have questions about your application, feel free to contact us.
                </p>
                <FlowButton 
                  variant="white-outline"
                  href="#"
                  class="w-full"
                >
                  Contact Support
                </FlowButton>
              </template>
            </RhythmicCard>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
