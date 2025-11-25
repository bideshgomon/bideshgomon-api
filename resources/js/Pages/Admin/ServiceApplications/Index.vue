<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowPathIcon,
  EyeIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  DocumentTextIcon,
  UserIcon,
  BuildingOfficeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  applications: Object,
  stats: Object,
  filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const serviceFilter = ref(props.filters?.service_module_id || 'all');
const showFilters = ref(false);

const statuses = [
  { value: 'all', label: 'All Status', color: 'gray' },
  { value: 'pending', label: 'Pending', color: 'yellow' },
  { value: 'quoted', label: 'Quoted', color: 'blue' },
  { value: 'accepted', label: 'Accepted', color: 'green' },
  { value: 'in_progress', label: 'In Progress', color: 'indigo' },
  { value: 'completed', label: 'Completed', color: 'green' },
  { value: 'cancelled', label: 'Cancelled', color: 'red' },
];

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    quoted: 'bg-blue-100 text-blue-800',
    accepted: 'bg-green-100 text-green-800',
    in_progress: 'bg-indigo-100 text-indigo-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const filterApplications = () => {
  router.get('/admin/service-applications', {
    search: search.value,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    service_module_id: serviceFilter.value !== 'all' ? serviceFilter.value : undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  search.value = '';
  statusFilter.value = 'all';
  serviceFilter.value = 'all';
  router.get('/admin/service-applications', {}, { preserveState: true });
};
</script>

<template>
  <Head title="Service Applications - Plugin System" />

  <AdminLayout>
    <div class="py-8">
      <!-- Header -->
      <div class="px-4 sm:px-6 lg:px-8 mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              Service Applications
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
              Universal Plugin System - Manage all 38 service applications
            </p>
          </div>
          <div class="mt-4 sm:mt-0">
            <button
              @click="filterApplications"
              class="inline-flex items-center gap-x-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all"
            >
              <ArrowPathIcon class="h-5 w-5" />
              Refresh
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending</p>
                <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ stats?.pending || 0 }}</p>
              </div>
              <ClockIcon class="h-12 w-12 text-yellow-400" />
            </div>
          </div>

          <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Quoted</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ stats?.quoted || 0 }}</p>
              </div>
              <DocumentTextIcon class="h-12 w-12 text-blue-400" />
            </div>
          </div>

          <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">In Progress</p>
                <p class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">{{ stats?.in_progress || 0 }}</p>
              </div>
              <ArrowPathIcon class="h-12 w-12 text-indigo-400" />
            </div>
          </div>

          <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-green-600 dark:text-green-400">Completed</p>
                <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ stats?.completed || 0 }}</p>
              </div>
              <CheckCircleIcon class="h-12 w-12 text-green-400" />
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="px-4 sm:px-6 lg:px-8 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
              <label for="search" class="sr-only">Search</label>
              <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="search"
                  v-model="search"
                  @keyup.enter="filterApplications"
                  type="text"
                  placeholder="Search by application number, user, service..."
                  class="block w-full rounded-lg border-0 py-2.5 pl-10 pr-3 text-gray-900 dark:text-white dark:bg-gray-700 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                />
              </div>
            </div>

            <!-- Status Filter -->
            <div class="lg:w-48">
              <select
                v-model="statusFilter"
                @change="filterApplications"
                class="block w-full rounded-lg border-0 py-2.5 text-gray-900 dark:text-white dark:bg-gray-700 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
              >
                <option v-for="status in statuses" :key="status.value" :value="status.value">
                  {{ status.label }}
                </option>
              </select>
            </div>

            <!-- Reset Button -->
            <button
              @click="resetFilters"
              class="inline-flex items-center justify-center gap-x-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-all"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Applications Table -->
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                    Application
                  </th>
                  <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                    Service
                  </th>
                  <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                    User
                  </th>
                  <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                    Quotes
                  </th>
                  <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                    Created
                  </th>
                  <th scope="col" class="relative px-6 py-3.5">
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
                    <div class="text-sm text-gray-900 dark:text-white font-medium">
                      {{ application.service_module?.name || 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ application.service_module?.slug || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <UserIcon class="h-5 w-5 text-gray-400 mr-2" />
                      <div>
                        <div class="text-sm text-gray-900 dark:text-white">
                          {{ application.user?.name || 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ application.user?.email || 'N/A' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                        getStatusColor(application.status)
                      ]"
                    >
                      {{ application.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <BuildingOfficeIcon class="h-5 w-5 text-gray-400 mr-2" />
                      <span class="text-sm text-gray-900 dark:text-white font-medium">
                        {{ application.quotes_count || 0 }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(application.created_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="`/admin/service-applications/${application.id}`"
                      class="inline-flex items-center gap-x-1.5 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                    >
                      <EyeIcon class="h-4 w-4" />
                      View
                    </Link>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!applications.data || applications.data.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No applications found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                      Get started by creating a service application.
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
              <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <Link
                    v-if="applications.prev_page_url"
                    :href="applications.prev_page_url"
                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                  </Link>
                  <Link
                    v-if="applications.next_page_url"
                    :href="applications.next_page_url"
                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20"
                  >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                  </Link>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
