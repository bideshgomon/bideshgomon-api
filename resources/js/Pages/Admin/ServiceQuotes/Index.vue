<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  EyeIcon,
  TrashIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  CurrencyDollarIcon,
  DocumentTextIcon,
  BuildingOfficeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  quotes: Object,
  stats: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const searchQuotes = () => {
  router.get(route('admin.service-quotes.index'), {
    search: search.value,
    status: statusFilter.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  search.value = '';
  statusFilter.value = '';
  router.get(route('admin.service-quotes.index'));
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  };
  return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const deleteQuote = (quoteId) => {
  if (confirm('Are you sure you want to delete this quote?')) {
    router.delete(route('admin.service-quotes.destroy', quoteId));
  }
};

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toLocaleString();
};
</script>

<template>
  <Head title="Service Quotes Management" />

  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Service Quotes Management
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            Manage agency quotes for service applications
          </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Quotes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ stats.total }}
                </p>
              </div>
              <div class="p-3 rounded-full bg-indigo-50 dark:bg-indigo-900/20">
                <DocumentTextIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                  {{ stats.pending }}
                </p>
              </div>
              <div class="p-3 rounded-full bg-yellow-50 dark:bg-yellow-900/20">
                <ClockIcon class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Accepted</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{ stats.accepted }}
                </p>
              </div>
              <div class="p-3 rounded-full bg-green-50 dark:bg-green-900/20">
                <CheckCircleIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Rejected</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                  {{ stats.rejected }}
                </p>
              </div>
              <div class="p-3 rounded-full bg-red-50 dark:bg-red-900/20">
                <XCircleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ formatCurrency(stats.total_amount) }}
                </p>
              </div>
              <div class="p-3 rounded-full bg-purple-50 dark:bg-purple-900/20">
                <CurrencyDollarIcon class="h-6 w-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
          <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
              <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                <input
                  v-model="search"
                  @keyup.enter="searchQuotes"
                  type="text"
                  placeholder="Search by quote #, agency, or application #..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                />
              </div>
            </div>

            <div class="flex gap-3">
              <select
                v-model="statusFilter"
                @change="searchQuotes"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
              >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="rejected">Rejected</option>
              </select>

              <button
                @click="searchQuotes"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2"
              >
                <FunnelIcon class="h-5 w-5" />
                Filter
              </button>

              <button
                @click="resetFilters"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
              >
                Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Quotes Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Quote #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Agency
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Application
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Service
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-for="quote in quotes.data"
                  :key="quote.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ quote.quote_number }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <BuildingOfficeIcon class="h-5 w-5 text-gray-400" />
                      <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ quote.agency?.name || 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ quote.agency?.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white">
                      {{ quote.service_application?.application_number }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ quote.service_application?.user?.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white">
                      {{ quote.service_application?.service_module?.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                      BDT {{ formatCurrency(quote.quoted_amount) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ quote.processing_time_days }} days
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="getStatusColor(quote.status)"
                      class="px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ quote.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(quote.created_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                      <Link
                        :href="route('admin.service-quotes.show', quote.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                      >
                        <EyeIcon class="h-5 w-5" />
                      </Link>
                      <button
                        @click="deleteQuote(quote.id)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      >
                        <TrashIcon class="h-5 w-5" />
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="quotes.data.length === 0">
                  <td colspan="8" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <DocumentTextIcon class="h-12 w-12 text-gray-400 mb-3" />
                      <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-1">
                        No quotes found
                      </p>
                      <p class="text-gray-400 dark:text-gray-500 text-sm">
                        Try adjusting your filters
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="quotes.data.length > 0" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
            <Pagination :links="quotes.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
