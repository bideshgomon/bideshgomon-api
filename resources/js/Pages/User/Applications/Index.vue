<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import {
  ClockIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  XCircleIcon,
  CurrencyDollarIcon,
  BuildingOfficeIcon,
  EyeIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  applications: Object,
  stats: Object,
});

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    quoted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    pending: 'Waiting for Quotes',
    quoted: 'Quotes Available',
    accepted: 'Quote Accepted',
    in_progress: 'In Progress',
    completed: 'Completed',
    cancelled: 'Cancelled',
  };
  return texts[status] || status;
};
</script>

<template>
  <Head title="My Applications" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
          My Applications
        </h2>
        <Link
          href="/services"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
        >
          Browse Services
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <RhythmicCard variant="sky" size="md" class="animate-fadeIn">
            <template #icon>
              <DocumentTextIcon class="h-8 w-8" />
            </template>
            <template #default>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total || 0 }}</p>
            </template>
          </RhythmicCard>

          <RhythmicCard variant="gold" size="md" class="animate-fadeIn" style="animation-delay: 100ms;">
            <template #icon>
              <ClockIcon class="h-8 w-8" />
            </template>
            <template #default>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.pending || 0 }}</p>
            </template>
          </RhythmicCard>

          <RhythmicCard variant="ocean" size="md" class="animate-fadeIn" style="animation-delay: 200ms;">
            <template #icon>
              <ArrowPathIcon class="h-8 w-8" />
            </template>
            <template #default>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.in_progress || 0 }}</p>
            </template>
          </RhythmicCard>

          <RhythmicCard variant="growth" size="md" class="animate-fadeIn" style="animation-delay: 300ms;">
            <template #icon>
              <CheckCircleIcon class="h-8 w-8" />
            </template>
            <template #default>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.completed || 0 }}</p>
            </template>
          </RhythmicCard>
        </div>

        <!-- Applications List -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Applications</h3>
          </div>

          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <RhythmicCard
              v-for="application in applications.data"
              :key="application.id"
              variant="light"
              size="lg"
              :hover-lift="true"
              class="animate-fadeIn"
            >
              <template #default>
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                      {{ application.service_module?.name || 'Service' }}
                    </h4>
                    <StatusBadge :status="application.status" size="md" />
                  </div>

                  <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                    <div class="flex items-center gap-1">
                      <DocumentTextIcon class="h-4 w-4" />
                      <span>{{ application.application_number }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                      <ClockIcon class="h-4 w-4" />
                      <span>{{ new Date(application.created_at).toLocaleDateString() }}</span>
                    </div>
                    <div v-if="application.quotes_count > 0" class="flex items-center gap-1">
                      <BuildingOfficeIcon class="h-4 w-4" />
                      <span>{{ application.quotes_count }} quote{{ application.quotes_count !== 1 ? 's' : '' }} received</span>
                    </div>
                  </div>

                  <div v-if="application.status === 'pending'" class="text-sm text-gray-600 dark:text-gray-400">
                    Waiting for agencies to submit quotes...
                  </div>
                  <div v-else-if="application.status === 'quoted'" class="text-sm">
                    <span class="text-green-600 dark:text-green-400 font-medium">
                      {{ application.quotes_count }} quote{{ application.quotes_count !== 1 ? 's' : '' }} available - 
                    </span>
                    <Link :href="`/my-applications/${application.id}/quotes`" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                      Review quotes
                    </Link>
                  </div>
                </div>

                <div class="ml-4">
                  <FlowButton
                    :href="`/my-applications/${application.id}`"
                    variant="secondary"
                    size="md"
                  >
                    <template #icon-left>
                      <EyeIcon class="h-4 w-4" />
                    </template>
                    View Details
                  </FlowButton>
                </div>
              </div>
              </template>
            </RhythmicCard>

            <!-- Empty State -->
            <div v-if="!applications.data || applications.data.length === 0" class="p-12 text-center">
              <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No applications yet</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-4">
                Get started by browsing our services and submitting an application.
              </p>
              <Link
                href="/services"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
              >
                Browse Services
              </Link>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="applications.data && applications.data.length > 0 && (applications.prev_page_url || applications.next_page_url)" class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-6 py-4">
            <div class="flex items-center justify-between">
              <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing {{ applications.from }} to {{ applications.to }} of {{ applications.total }} applications
              </p>
              <div class="flex gap-2">
                <Link
                  v-if="applications.prev_page_url"
                  :href="applications.prev_page_url"
                  class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Previous
                </Link>
                <Link
                  v-if="applications.next_page_url"
                  :href="applications.next_page_url"
                  class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Next
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
