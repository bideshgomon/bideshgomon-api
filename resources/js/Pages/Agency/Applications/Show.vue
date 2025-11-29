<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {
  DocumentTextIcon,
  UserIcon,
  GlobeAltIcon,
  CalendarIcon,
  ClockIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  XCircleIcon,
  PlusIcon,
  PaperClipIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  application: Object,
});

const { formatDate, formatCurrency } = useBangladeshFormat();

const showQuoteModal = ref(false);

// Quote form
const quoteForm = useForm({
  quoted_amount: '',
  processing_time_days: 30,
  valid_until: '',
  quote_notes: '',
});

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

const canQuote = computed(() => {
  return props.application.status === 'pending' && 
         (!props.application.quotes || props.application.quotes.length === 0);
});

const hasExistingQuote = computed(() => {
  return props.application.quotes && props.application.quotes.length > 0;
});

const existingQuote = computed(() => {
  return props.application.quotes?.[0] || null;
});

// Calculate platform commission (assuming 10% - should come from service module)
const platformCommissionRate = computed(() => {
  return props.application.service_module?.platform_commission_rate || 10;
});

const calculatedCommission = computed(() => {
  if (!quoteForm.quoted_amount) return 0;
  return (parseFloat(quoteForm.quoted_amount) * platformCommissionRate.value / 100).toFixed(2);
});

const agencyEarnings = computed(() => {
  if (!quoteForm.quoted_amount) return 0;
  return (parseFloat(quoteForm.quoted_amount) - parseFloat(calculatedCommission.value)).toFixed(2);
});

// Set default valid_until to 7 days from now
const setDefaultValidUntil = () => {
  const date = new Date();
  date.setDate(date.getDate() + 7);
  quoteForm.valid_until = date.toISOString().split('T')[0];
};

const openQuoteModal = () => {
  setDefaultValidUntil();
  showQuoteModal.value = true;
};

const closeQuoteModal = () => {
  showQuoteModal.value = false;
  quoteForm.reset();
};

const submitQuote = () => {
  quoteForm.post(route('agency.quotes.store', props.application.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeQuoteModal();
    },
  });
};
</script>

<template>
  <Head :title="`Application ${application.application_number}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">Application Details</h2>
          <p class="text-sm text-gray-600 mt-1">{{ application.application_number }}</p>
        </div>
        <button
          v-if="canQuote"
          @click="openQuoteModal"
          class="inline-flex items-center gap-x-2 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5" />
          Submit Quote
        </button>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Status and Overview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-start justify-between mb-6">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Application Overview</h3>
              <p class="text-sm text-gray-600 mt-1">Submitted on {{ formatDate(application.created_at) }}</p>
            </div>
            <span
              :class="[
                'inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold',
                getStatusColor(application.status)
              ]"
            >
              {{ application.status }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- User Info -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-blue-100 rounded-lg">
                <UserIcon class="h-5 w-5 text-blue-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Applicant</p>
                <p class="text-sm font-semibold text-gray-900">{{ application.user?.name }}</p>
                <p class="text-xs text-gray-600">{{ application.user?.email }}</p>
              </div>
            </div>

            <!-- Service -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-indigo-100 rounded-lg">
                <DocumentTextIcon class="h-5 w-5 text-indigo-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Service</p>
                <p class="text-sm font-semibold text-gray-900">{{ application.service_module?.name }}</p>
                <p class="text-xs text-gray-600">{{ application.service_module?.category }}</p>
              </div>
            </div>

            <!-- Destination -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-green-100 rounded-lg">
                <GlobeAltIcon class="h-5 w-5 text-green-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Destination</p>
                <p class="text-sm font-semibold text-gray-900">
                  {{ application.tourist_visa?.destination_country?.name || 'N/A' }}
                </p>
              </div>
            </div>

            <!-- Date -->
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-orange-100 rounded-lg">
                <CalendarIcon class="h-5 w-5 text-orange-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Submitted</p>
                <p class="text-sm font-semibold text-gray-900">{{ formatDate(application.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Existing Quote (if any) -->
        <div v-if="hasExistingQuote" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Your Quote</h3>
              <p class="text-sm text-gray-600 mt-1">Submitted on {{ formatDate(existingQuote.created_at) }}</p>
            </div>
            <span
              :class="[
                'inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold',
                getStatusColor(existingQuote.status)
              ]"
            >
              {{ existingQuote.status }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-green-100 rounded-lg">
                <CurrencyDollarIcon class="h-5 w-5 text-green-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Quoted Amount</p>
                <p class="text-lg font-bold text-gray-900">{{ formatCurrency(existingQuote.quoted_amount) }}</p>
                <p class="text-xs text-gray-600">Your earnings: {{ formatCurrency(existingQuote.agency_earnings) }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-blue-100 rounded-lg">
                <ClockIcon class="h-5 w-5 text-blue-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Processing Time</p>
                <p class="text-lg font-bold text-gray-900">{{ existingQuote.processing_time_days }} days</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 p-2 bg-orange-100 rounded-lg">
                <CalendarIcon class="h-5 w-5 text-orange-600" />
              </div>
              <div>
                <p class="text-xs text-gray-500 font-medium">Valid Until</p>
                <p class="text-lg font-bold text-gray-900">{{ formatDate(existingQuote.valid_until) }}</p>
              </div>
            </div>
          </div>

          <div v-if="existingQuote.quote_notes" class="mt-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500 font-medium mb-1">Quote Notes</p>
            <p class="text-sm text-gray-900">{{ existingQuote.quote_notes }}</p>
          </div>
        </div>

        <!-- Application Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Details</h3>
          
          <div v-if="application.application_data" class="space-y-4">
            <div v-for="(value, key) in application.application_data" :key="key" class="flex items-start gap-3">
              <div class="flex-shrink-0 w-48">
                <p class="text-sm font-medium text-gray-700">{{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</p>
              </div>
              <div class="flex-1">
                <p class="text-sm text-gray-900">{{ value || 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Documents -->
        <div v-if="application.tourist_visa?.documents?.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Documents</h3>
          
          <div class="space-y-3">
            <div
              v-for="document in application.tourist_visa.documents"
              :key="document.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center gap-3">
                <PaperClipIcon class="h-5 w-5 text-gray-400" />
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ document.document_type }}</p>
                  <p class="text-xs text-gray-600">Uploaded {{ formatDate(document.created_at) }}</p>
                </div>
              </div>
              <a
                v-if="document.file_path"
                :href="`/storage/${document.file_path}`"
                target="_blank"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
              >
                View
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Quote Submission Modal -->
    <Modal :show="showQuoteModal" @close="closeQuoteModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Quote</h3>

        <form @submit.prevent="submitQuote" class="space-y-4">
          
          <!-- Quoted Amount -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Quoted Amount <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">৳</span>
              <input
                v-model="quoteForm.quoted_amount"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="0.00"
              />
            </div>
            <p v-if="quoteForm.errors.quoted_amount" class="mt-1 text-sm text-red-600">
              {{ quoteForm.errors.quoted_amount }}
            </p>
          </div>

          <!-- Fee Breakdown -->
          <div v-if="quoteForm.quoted_amount" class="p-4 bg-gray-50 rounded-lg space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Platform Commission ({{ platformCommissionRate }}%)</span>
              <span class="font-medium text-gray-900">{{ formatCurrency(calculatedCommission) }}</span>
            </div>
            <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
              <span class="text-gray-900 font-medium">Your Earnings</span>
              <span class="font-bold text-green-600">{{ formatCurrency(agencyEarnings) }}</span>
            </div>
          </div>

          <!-- Processing Time -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Processing Time (Days) <span class="text-red-500">*</span>
            </label>
            <input
              v-model="quoteForm.processing_time_days"
              type="number"
              min="1"
              max="365"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
            <p v-if="quoteForm.errors.processing_time_days" class="mt-1 text-sm text-red-600">
              {{ quoteForm.errors.processing_time_days }}
            </p>
          </div>

          <!-- Valid Until -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Quote Valid Until <span class="text-red-500">*</span>
            </label>
            <input
              v-model="quoteForm.valid_until"
              type="date"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
            <p v-if="quoteForm.errors.valid_until" class="mt-1 text-sm text-red-600">
              {{ quoteForm.errors.valid_until }}
            </p>
          </div>

          <!-- Quote Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Additional Notes (Optional)
            </label>
            <textarea
              v-model="quoteForm.quote_notes"
              rows="4"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Any special instructions, conditions, or information for the client..."
            ></textarea>
            <p v-if="quoteForm.errors.quote_notes" class="mt-1 text-sm text-red-600">
              {{ quoteForm.errors.quote_notes }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <SecondaryButton @click="closeQuoteModal" type="button">
              Cancel
            </SecondaryButton>
            <PrimaryButton type="submit" :disabled="quoteForm.processing">
              {{ quoteForm.processing ? 'Submitting...' : 'Submit Quote' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
