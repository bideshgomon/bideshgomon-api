<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  DocumentTextIcon,
  ClockIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  PlusIcon,
  EyeIcon,
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
    in_progress: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const canQuote = (application) => {
  return application.status === 'pending' && !application.has_quoted;
};
</script>

<template>
  <Head title="Agency Dashboard - Service Applications" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
          Service Applications
        </h2>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                  <ClockIcon class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-5">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Applications</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.pending || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                  <DocumentTextIcon class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-5">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">My Quotes</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.quoted || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                  <CheckCircleIcon class="h-8 w-8 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-5">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Accepted Quotes</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.accepted || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                  <CurrencyDollarIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="ml-5">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ (stats?.revenue || 0).toLocaleString() }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Available Applications</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Submit quotes for new service applications</p>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Application
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Service
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    User
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-for="application in applications.data"
                  :key="application.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                        <DocumentTextIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ application.application_number }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          ID: {{ application.id }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ application.service_module?.name || 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ application.service_module?.category || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white">
                      {{ application.user?.name || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                        getStatusColor(application.status)
                      ]"
                    >
                      {{ application.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(application.created_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                    <Link
                      :href="`/agency/applications/${application.id}`"
                      class="inline-flex items-center gap-x-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      <EyeIcon class="h-4 w-4" />
                      View
                    </Link>
                    <Link
                      v-if="canQuote(application)"
                      :href="`/agency/applications/${application.id}/quote`"
                      class="inline-flex items-center gap-x-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      <PlusIcon class="h-4 w-4" />
                      Quote
                    </Link>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!applications.data || applications.data.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No applications available</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                      Check back later for new service applications.
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="applications.data && applications.data.length > 0" class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
              <Link
                v-if="applications.prev_page_url"
                :href="applications.prev_page_url"
                class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Previous
              </Link>
              <Link
                v-if="applications.next_page_url"
                :href="applications.next_page_url"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  Showing
                  <span class="font-medium">{{ applications.from }}</span>
                  to
                  <span class="font-medium">{{ applications.to }}</span>
                  of
                  <span class="font-medium">{{ applications.total }}</span>
                  applications
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
