<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  CheckCircleIcon,
  XCircleIcon,
  BuildingOfficeIcon,
  CurrencyDollarIcon,
  ClockIcon,
  DocumentTextIcon,
  StarIcon,
  ShieldCheckIcon,
} from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';

const props = defineProps({
  application: Object,
  quotes: Array,
});

const selectedQuote = ref(null);
const showAcceptModal = ref(false);
const acceptingQuote = ref(false);

const selectQuote = (quote) => {
  selectedQuote.value = quote;
};

const acceptQuote = () => {
  if (!selectedQuote.value) return;
  
  acceptingQuote.value = true;
  router.post(`/my-applications/${props.application.id}/quotes/${selectedQuote.value.id}/accept`, {}, {
    onSuccess: () => {
      showAcceptModal.value = false;
      acceptingQuote.value = false;
    },
    onError: () => {
      acceptingQuote.value = false;
    }
  });
};

const rejectQuote = (quoteId) => {
  if (confirm('Are you sure you want to reject this quote?')) {
    router.post(`/my-applications/${props.application.id}/quotes/${quoteId}/reject`);
  }
};

const getAgencyRating = (agency) => {
  return agency?.rating || 4.5;
};
</script>

<template>
  <Head title="Review Quotes" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Review Quotes
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ application.service_module?.name }} - {{ application.application_number }}
          </p>
        </div>
        <Link
          :href="`/my-applications/${application.id}`"
          class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
        >
          ← Back to Application
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Info Banner -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-8">
          <div class="flex">
            <div class="flex-shrink-0">
              <DocumentTextIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                You have {{ quotes.length }} quote{{ quotes.length !== 1 ? 's' : '' }} to review
              </h3>
              <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                <p>Compare quotes from different agencies and choose the one that best fits your needs.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quotes Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div
            v-for="quote in quotes"
            :key="quote.id"
            :class="[
              'bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 transition-all duration-200',
              selectedQuote?.id === quote.id
                ? 'border-indigo-500 shadow-lg'
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
            ]"
          >
            <div class="p-6">
              <!-- Agency Header -->
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                  <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <BuildingOfficeIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                      {{ quote.agency?.name || 'Agency' }}
                    </h3>
                    <div class="flex items-center gap-1 mt-1">
                      <StarIconSolid
                        v-for="i in 5"
                        :key="i"
                        :class="[
                          'h-4 w-4',
                          i <= Math.floor(getAgencyRating(quote.agency))
                            ? 'text-yellow-400'
                            : 'text-gray-300 dark:text-gray-600'
                        ]"
                      />
                      <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">
                        {{ getAgencyRating(quote.agency).toFixed(1) }}
                      </span>
                    </div>
                  </div>
                </div>
                <div v-if="quote.agency?.verified" class="flex items-center gap-1 text-green-600 dark:text-green-400 text-xs font-medium">
                  <ShieldCheckIcon class="h-4 w-4" />
                  Verified
                </div>
              </div>

              <!-- Quote Amount -->
              <div class="bg-heritage-50 dark:bg-heritage-900/20 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                      ${{ quote.quoted_amount?.toLocaleString() }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                      {{ quote.currency || 'USD' }}
                    </p>
                  </div>
                  <CurrencyDollarIcon class="h-12 w-12 text-indigo-400 opacity-50" />
                </div>
              </div>

              <!-- Quote Details -->
              <div class="space-y-3 mb-4">
                <div class="flex items-center gap-2 text-sm">
                  <ClockIcon class="h-4 w-4 text-gray-400" />
                  <span class="text-gray-600 dark:text-gray-400">Valid until:</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    {{ new Date(quote.valid_until).toLocaleDateString() }}
                  </span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                  <DocumentTextIcon class="h-4 w-4 text-gray-400" />
                  <span class="text-gray-600 dark:text-gray-400">Quote #:</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    {{ quote.quote_number }}
                  </span>
                </div>
              </div>

              <!-- Quote Details/Notes -->
              <div v-if="quote.quote_details || quote.notes" class="mb-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Details</h4>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ quote.notes || 'No additional details provided' }}
                  </p>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-3">
                <button
                  @click="selectQuote(quote); showAcceptModal = true"
                  :disabled="quote.status !== 'pending'"
                  class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors"
                >
                  <CheckCircleIcon class="h-5 w-5" />
                  Accept Quote
                </button>
                <button
                  v-if="quote.status === 'pending'"
                  @click="rejectQuote(quote.id)"
                  class="px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                >
                  <XCircleIcon class="h-5 w-5" />
                </button>
              </div>

              <!-- Status Badge -->
              <div v-if="quote.status === 'accepted'" class="mt-3 text-center">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                  <CheckCircleIcon class="h-4 w-4" />
                  Accepted
                </span>
              </div>
              <div v-else-if="quote.status === 'rejected'" class="mt-3 text-center">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                  <XCircleIcon class="h-4 w-4" />
                  Rejected
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!quotes || quotes.length === 0" class="col-span-full">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
              <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No quotes yet</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Agencies will submit quotes soon. Check back later.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Accept Confirmation Modal -->
    <div v-if="showAcceptModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Confirm Quote Acceptance
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
          You are about to accept the quote from <strong>{{ selectedQuote?.agency?.name }}</strong> for 
          <strong>${{ selectedQuote?.quoted_amount?.toLocaleString() }}</strong>. 
          This action cannot be undone.
        </p>
        <div class="flex gap-3">
          <button
            @click="acceptQuote"
            :disabled="acceptingQuote"
            class="flex-1 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white py-2 rounded-lg font-medium transition-colors"
          >
            {{ acceptingQuote ? 'Processing...' : 'Confirm' }}
          </button>
          <button
            @click="showAcceptModal = false"
            :disabled="acceptingQuote"
            class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white py-2 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
