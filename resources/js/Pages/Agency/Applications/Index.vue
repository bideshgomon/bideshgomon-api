<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  DocumentTextIcon,
  ClockIcon,
  CheckCircleIcon,
  PlusIcon,
  EyeIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  BriefcaseIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  applications: Object,
  stats: Object,
  filters: Object,
});

const { formatDate } = useBangladeshFormat();

const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const viewFilter = ref(props.filters?.filter || 'available');

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    quoted: 'bg-blue-100 text-blue-800',
    accepted: 'bg-green-100 text-green-800',
    in_progress: 'bg-indigo-100 text-indigo-800',
    completed: 'bg-emerald-100 text-emerald-800',
    cancelled: 'bg-red-100 text-red-800',
    rejected: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    quoted: 'Quoted',
    accepted: 'Accepted',
    in_progress: 'In Progress',
    completed: 'Completed',
    cancelled: 'Cancelled',
    rejected: 'Rejected',
  };
  return labels[status] || status;
};

const canQuote = (application) => {
  return application.status === 'pending' && !application.has_quoted;
};

const applyFilters = () => {
  router.get(route('agency.applications.index'), {
    search: searchQuery.value,
    status: statusFilter.value,
    filter: viewFilter.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearSearch = () => {
  searchQuery.value = '';
  applyFilters();
};

</script>

<template>
  <Head title="Service Applications - Agency Portal" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">Service Applications</h2>
          <p class="text-sm text-gray-600 mt-1">Manage and quote on available service applications</p>
        </div>
      </div>
    </template>

    <div class="py-4 sm:py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
          <!-- Pending Applications -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-2 sm:p-3 bg-yellow-100 rounded-lg">
                <ClockIcon class="h-4 w-4 sm:h-6 sm:w-6 text-yellow-600" />
              </div>
              <div class="ml-2 sm:ml-4 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Available</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats?.pending || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- My Applications -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-2 sm:p-3 bg-blue-100 rounded-lg">
                <BriefcaseIcon class="h-4 w-4 sm:h-6 sm:w-6 text-blue-600" />
              </div>
              <div class="ml-2 sm:ml-4 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">My Applications</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats?.my_applications || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Pending Quotes -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-2 sm:p-3 bg-orange-100 rounded-lg">
                <DocumentTextIcon class="h-4 w-4 sm:h-6 sm:w-6 text-orange-600" />
              </div>
              <div class="ml-2 sm:ml-4 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Pending Quotes</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats?.quoted || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Accepted -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-2 sm:p-3 bg-green-100 rounded-lg">
                <CheckCircleIcon class="h-4 w-4 sm:h-6 sm:w-6 text-green-600" />
              </div>
              <div class="ml-2 sm:ml-4 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Accepted</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats?.accepted || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
              
              <!-- View Toggle -->
              <div class="flex rounded-lg border border-gray-300 p-1 bg-gray-50">
                <button
                  @click="viewFilter = 'available'; applyFilters()"
                  :class="[
                    'px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-md transition-colors flex-1 sm:flex-none',
                    viewFilter === 'available'
                      ? 'bg-white text-indigo-600 shadow-sm'
                      : 'text-gray-700 hover:text-gray-900'
                  ]"
                >
                  Available
                </button>
                <button
                  @click="viewFilter = 'my'; applyFilters()"
                  :class="[
                    'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                    viewFilter === 'my'
                      ? 'bg-white text-indigo-600 shadow-sm'
                      : 'text-gray-700 hover:text-gray-900'
                  ]"
                >
                  My Applications
                </button>
              </div>

              <!-- Search Bar -->
              <div class="flex-1 relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                <input
                  v-model="searchQuery"
                  @keyup.enter="applyFilters"
                  type="text"
                  placeholder="Search by application number or user name..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
                <button
                  v-if="searchQuery"
                  @click="clearSearch"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  ×
                </button>
              </div>

              <!-- Status Filter -->
              <div class="flex items-center gap-2">
                <FunnelIcon class="h-5 w-5 text-gray-400" />
                <select
                  v-model="statusFilter"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="all">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="quoted">Quoted</option>
                  <option value="accepted">Accepted</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>

              <!-- Search Button -->
              <button
                @click="applyFilters"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
              >
                Search
              </button>
            </div>
          </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Application
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Service
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Destination
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="application in applications.data"
                  :key="application.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <DocumentTextIcon class="h-5 w-5 text-indigo-600" />
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ application.application_number }}
                        </div>
                        <div class="text-xs text-gray-500">
                          ID: {{ application.id }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ application.service_module?.name || 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ application.service_module?.category || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ application.user?.name || 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ application.user?.email || '' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ application.tourist_visa?.destination_country?.name || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold',
                        getStatusColor(application.status)
                      ]"
                    >
                      {{ getStatusLabel(application.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(application.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-3">
                      <Link
                        :href="route('agency.applications.show', application.id)"
                        class="inline-flex items-center gap-x-1.5 text-indigo-600 hover:text-indigo-900 font-medium"
                      >
                        <EyeIcon class="h-4 w-4" />
                        View
                      </Link>
                      <Link
                        v-if="canQuote(application)"
                        :href="route('agency.applications.show', application.id)"
                        class="inline-flex items-center gap-x-1.5 px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium"
                      >
                        <PlusIcon class="h-4 w-4" />
                        Quote
                      </Link>
                    </div>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!applications.data || applications.data.length === 0">
                  <td colspan="7" class="px-6 py-16 text-center">
                    <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No applications found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ viewFilter === 'available' 
                        ? 'There are no available applications at the moment. Check back later.' 
                        : 'You have not been assigned any applications yet.' 
                      }}
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="applications.data && applications.data.length > 0" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
              <Link
                v-if="applications.prev_page_url"
                :href="applications.prev_page_url"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="applications.next_page_url"
                :href="applications.next_page_url"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ applications.from || 0 }}</span>
                  to
                  <span class="font-medium">{{ applications.to || 0 }}</span>
                  of
                  <span class="font-medium">{{ applications.total || 0 }}</span>
                  applications
                </p>
              </div>
              <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <Link
                    v-if="applications.prev_page_url"
                    :href="applications.prev_page_url"
                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                  >
                    <span class="sr-only">Previous</span>
                    ←
                  </Link>
                  <Link
                    v-if="applications.next_page_url"
                    :href="applications.next_page_url"
                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                  >
                    <span class="sr-only">Next</span>
                    →
                  </Link>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
