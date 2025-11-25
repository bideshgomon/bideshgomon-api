<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  ArrowLeftIcon,
  BuildingOfficeIcon,
  UserIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  ClockIcon,
  CalendarIcon,
  CheckCircleIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  quote: Object,
});

const statusForm = useForm({
  status: props.quote.status,
  admin_notes: props.quote.admin_notes || '',
});

const updateStatus = () => {
  statusForm.put(route('admin.service-quotes.update-status', props.quote.id));
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toLocaleString();
};
</script>

<template>
  <Head :title="`Quote ${quote.quote_number}`" />

  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <Link
          :href="route('admin.service-quotes.index')"
          class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6 transition-colors"
        >
          <ArrowLeftIcon class="h-5 w-5" />
          Back to Quotes
        </Link>

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
          <div class="flex items-start justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Quote {{ quote.quote_number }}
              </h1>
              <p class="text-gray-600 dark:text-gray-400">
                Created {{ new Date(quote.created_at).toLocaleString() }}
              </p>
            </div>
            <span
              :class="getStatusColor(quote.status)"
              class="px-3 py-1 text-sm font-semibold rounded-full"
            >
              {{ quote.status }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Quote Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Quote Details
              </h2>

              <div class="grid grid-cols-2 gap-6">
                <div>
                  <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1">
                    <CurrencyDollarIcon class="h-5 w-5" />
                    <span class="text-sm">Quoted Amount</span>
                  </div>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    BDT {{ formatCurrency(quote.quoted_amount) }}
                  </p>
                </div>

                <div>
                  <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1">
                    <ClockIcon class="h-5 w-5" />
                    <span class="text-sm">Processing Time</span>
                  </div>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ quote.processing_time_days }} days
                  </p>
                </div>

                <div>
                  <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1">
                    <CalendarIcon class="h-5 w-5" />
                    <span class="text-sm">Valid Until</span>
                  </div>
                  <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ quote.valid_until ? new Date(quote.valid_until).toLocaleDateString() : 'N/A' }}
                  </p>
                </div>

                <div>
                  <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1">
                    <CurrencyDollarIcon class="h-5 w-5" />
                    <span class="text-sm">Service Fee</span>
                  </div>
                  <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    BDT {{ formatCurrency(quote.service_fee) }}
                  </p>
                </div>
              </div>

              <div v-if="quote.details" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Additional Details
                </h3>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">
                  {{ quote.details }}
                </p>
              </div>

              <div v-if="quote.terms_conditions" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Terms & Conditions
                </h3>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">
                  {{ quote.terms_conditions }}
                </p>
              </div>
            </div>

            <!-- Application Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Application Information
              </h2>

              <div class="space-y-4">
                <div>
                  <label class="text-sm text-gray-500 dark:text-gray-400">Application Number</label>
                  <p class="text-gray-900 dark:text-white font-medium">
                    {{ quote.service_application?.application_number }}
                  </p>
                </div>

                <div>
                  <label class="text-sm text-gray-500 dark:text-gray-400">Service</label>
                  <p class="text-gray-900 dark:text-white font-medium">
                    {{ quote.service_application?.service_module?.name }}
                  </p>
                </div>

                <div>
                  <label class="text-sm text-gray-500 dark:text-gray-400">User</label>
                  <div class="flex items-center gap-2 mt-1">
                    <UserIcon class="h-5 w-5 text-gray-400" />
                    <div>
                      <p class="text-gray-900 dark:text-white font-medium">
                        {{ quote.service_application?.user?.name }}
                      </p>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ quote.service_application?.user?.email }}
                      </p>
                    </div>
                  </div>
                </div>

                <div>
                  <Link
                    :href="route('service-applications.show', quote.service_application?.id)"
                    class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 text-sm font-medium"
                  >
                    View Full Application →
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Agency Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <BuildingOfficeIcon class="h-5 w-5" />
                Agency Information
              </h2>

              <div class="space-y-3">
                <div>
                  <label class="text-sm text-gray-500 dark:text-gray-400">Agency Name</label>
                  <p class="text-gray-900 dark:text-white font-medium">
                    {{ quote.agency?.name }}
                  </p>
                </div>

                <div>
                  <label class="text-sm text-gray-500 dark:text-gray-400">Email</label>
                  <p class="text-gray-900 dark:text-white">
                    {{ quote.agency?.email }}
                  </p>
                </div>

                <div>
                  <label class="text-sm text-gray-500 dark:text-gray-400">Phone</label>
                  <p class="text-gray-900 dark:text-white">
                    {{ quote.agency?.phone || 'N/A' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Update Status
              </h2>

              <form @submit.prevent="updateStatus" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                  </label>
                  <select
                    v-model="statusForm.status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                  >
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Admin Notes
                  </label>
                  <textarea
                    v-model="statusForm.admin_notes"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Add notes about this status update..."
                  ></textarea>
                </div>

                <button
                  type="submit"
                  :disabled="statusForm.processing"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors disabled:opacity-50"
                >
                  <span v-if="statusForm.processing">Updating...</span>
                  <span v-else>Update Status</span>
                </button>
              </form>
            </div>

            <!-- Admin Notes -->
            <div v-if="quote.admin_notes" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Admin Notes
              </h2>
              <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">
                {{ quote.admin_notes }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
