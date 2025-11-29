<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  CurrencyDollarIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  XCircleIcon,
  TrophyIcon,
  ChartBarIcon,
  CalendarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  financials: Object,
  periodEarnings: Array,
  monthlyBreakdown: Array,
  topServices: Array,
  recentCompletions: Array,
  pendingPayments: Array,
  period: String,
});

const { formatCurrency, formatDate } = useBangladeshFormat();

const selectedPeriod = ref(props.period || '30');

const changePeriod = (days) => {
  router.get(route('agency.earnings.index'), { period: days }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getStatusColor = (status) => {
  const colors = {
    completed: 'bg-green-100 text-green-800',
    accepted: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-indigo-100 text-indigo-800',
    pending: 'bg-yellow-100 text-yellow-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const formatMonth = (monthStr) => {
  const [year, month] = monthStr.split('-');
  const date = new Date(year, month - 1);
  return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};
</script>

<template>
  <Head title="Earnings Dashboard - Agency Portal" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">Earnings Dashboard</h2>
          <p class="text-sm text-gray-600 mt-1">Track your revenue and performance metrics</p>
        </div>
        
        <!-- Period Selector -->
        <div class="flex items-center gap-2">
          <CalendarIcon class="h-5 w-5 text-gray-400" />
          <select
            v-model="selectedPeriod"
            @change="changePeriod(selectedPeriod)"
            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="7">Last 7 Days</option>
            <option value="30">Last 30 Days</option>
            <option value="90">Last 90 Days</option>
            <option value="180">Last 6 Months</option>
            <option value="365">Last Year</option>
          </select>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Financial Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
          <!-- Total Earnings -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
              <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
              </div>
            </div>
            <div class="mt-4">
              <p class="text-sm font-medium text-gray-600">Total Earnings</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ formatCurrency(financials.total_earnings) }}
              </p>
              <p class="text-xs text-gray-500 mt-1">From completed applications</p>
            </div>
          </div>

          <!-- Pending Earnings -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
              <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                <ClockIcon class="h-6 w-6 text-blue-600" />
              </div>
            </div>
            <div class="mt-4">
              <p class="text-sm font-medium text-gray-600">Pending Earnings</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ formatCurrency(financials.pending_earnings) }}
              </p>
              <p class="text-xs text-gray-500 mt-1">In progress applications</p>
            </div>
          </div>

          <!-- Win Rate -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
              <div class="flex-shrink-0 p-3 bg-orange-100 rounded-lg">
                <TrophyIcon class="h-6 w-6 text-orange-600" />
              </div>
            </div>
            <div class="mt-4">
              <p class="text-sm font-medium text-gray-600">Win Rate</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ financials.win_rate }}%
              </p>
              <p class="text-xs text-gray-500 mt-1">
                {{ financials.accepted_quotes }} of {{ financials.total_quotes }} quotes won
              </p>
            </div>
          </div>

          <!-- Average Quote -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
              <div class="flex-shrink-0 p-3 bg-indigo-100 rounded-lg">
                <ChartBarIcon class="h-6 w-6 text-indigo-600" />
              </div>
            </div>
            <div class="mt-4">
              <p class="text-sm font-medium text-gray-600">Avg Quote Amount</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ formatCurrency(financials.avg_quote_amount) }}
              </p>
              <p class="text-xs text-gray-500 mt-1">Across all quotes</p>
            </div>
          </div>
        </div>

        <!-- Quote Performance Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Quote Performance</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 p-2 bg-gray-100 rounded-lg">
                <DocumentTextIcon class="h-5 w-5 text-gray-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500">Total Quotes</p>
                <p class="text-lg font-bold text-gray-900">{{ financials.total_quotes }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 p-2 bg-green-100 rounded-lg">
                <CheckCircleIcon class="h-5 w-5 text-green-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500">Accepted</p>
                <p class="text-lg font-bold text-green-600">{{ financials.accepted_quotes }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 p-2 bg-yellow-100 rounded-lg">
                <ClockIcon class="h-5 w-5 text-yellow-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-lg font-bold text-yellow-600">{{ financials.pending_quotes }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 p-2 bg-red-100 rounded-lg">
                <XCircleIcon class="h-5 w-5 text-red-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500">Rejected</p>
                <p class="text-lg font-bold text-red-600">{{ financials.rejected_quotes }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Breakdown & Top Services -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          
          <!-- Monthly Breakdown -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Breakdown</h3>
            
            <div v-if="monthlyBreakdown.length > 0" class="space-y-3">
              <div
                v-for="month in monthlyBreakdown"
                :key="month.month"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center gap-3">
                  <CalendarIcon class="h-5 w-5 text-gray-400" />
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ formatMonth(month.month) }}</p>
                    <p class="text-xs text-gray-600">{{ month.count }} applications</p>
                  </div>
                </div>
                <p class="text-sm font-bold text-green-600">{{ formatCurrency(month.earnings) }}</p>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm font-medium text-gray-900">No data yet</p>
              <p class="text-xs text-gray-500">Complete applications to see monthly breakdown</p>
            </div>
          </div>

          <!-- Top Services -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Services by Revenue</h3>
            
            <div v-if="topServices.length > 0" class="space-y-3">
              <div
                v-for="(service, index) in topServices"
                :key="service.service_module_id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center gap-3">
                  <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-indigo-600">{{ index + 1 }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ service.service_module?.name || 'Unknown Service' }}
                    </p>
                    <p class="text-xs text-gray-600">{{ service.count }} applications</p>
                  </div>
                </div>
                <p class="text-sm font-bold text-green-600">{{ formatCurrency(service.earnings) }}</p>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <TrophyIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm font-medium text-gray-900">No data yet</p>
              <p class="text-xs text-gray-500">Complete applications to see top services</p>
            </div>
          </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Payments</h3>
          
          <div v-if="pendingPayments.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="payment in pendingPayments" :key="payment.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link
                      :href="route('agency.applications.show', payment.id)"
                      class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
                    >
                      {{ payment.application_number }}
                    </Link>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ payment.user?.name }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ payment.service_module?.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold',
                        getStatusColor(payment.status)
                      ]"
                    >
                      {{ payment.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                    {{ formatCurrency(payment.agency_earnings) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="text-center py-8">
            <ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm font-medium text-gray-900">No pending payments</p>
            <p class="text-xs text-gray-500">All accepted applications are completed</p>
          </div>
        </div>

        <!-- Recent Completions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Completions</h3>
          
          <div v-if="recentCompletions.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Earnings</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="completion in recentCompletions" :key="completion.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link
                      :href="route('agency.applications.show', completion.id)"
                      class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
                    >
                      {{ completion.application_number }}
                    </Link>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ completion.user?.name }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ completion.service_module?.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ completion.tourist_visa?.destination_country?.name || 'N/A' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(completion.completed_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                    {{ formatCurrency(completion.agency_earnings) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="text-center py-8">
            <CheckCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm font-medium text-gray-900">No completed applications yet</p>
            <p class="text-xs text-gray-500">Complete your first application to see it here</p>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
