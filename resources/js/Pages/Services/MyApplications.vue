<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  DocumentTextIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationCircleIcon,
  PencilSquareIcon,
  EyeIcon,
  TrashIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'

const { formatDate, formatCurrency } = useBangladeshFormat()

const props = defineProps({
  applications: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({
      status: '',
      search: ''
    })
  }
})

// Filter options
const statusFilter = ref(props.filters.status || '')
const searchQuery = ref(props.filters.search || '')

// Apply filters
const applyFilters = () => {
  router.get(route('applications.index'), {
    status: statusFilter.value,
    search: searchQuery.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

// Clear filters
const clearFilters = () => {
  statusFilter.value = ''
  searchQuery.value = ''
  applyFilters()
}

// Delete application
const deleteApplication = (application) => {
  if (confirm(`Are you sure you want to delete application ${application.application_number}?`)) {
    router.delete(route('applications.destroy', application.id), {
      onSuccess: () => {
        alert('Application deleted successfully')
      }
    })
  }
}

// Cancel application
const cancelApplication = (application) => {
  if (confirm(`Are you sure you want to cancel application ${application.application_number}?`)) {
    router.post(route('applications.cancel', application.id), {}, {
      onSuccess: () => {
        alert('Application cancelled successfully')
      }
    })
  }
}

// Get status badge classes
const getStatusBadge = (status) => {
  const badges = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    under_review: 'bg-blue-100 text-blue-800',
    additional_info: 'bg-orange-100 text-orange-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
    completed: 'bg-purple-100 text-purple-800',
  }
  return badges[status] || 'bg-gray-100 text-gray-800'
}

// Get status icon
const getStatusIcon = (status) => {
  const icons = {
    draft: PencilSquareIcon,
    pending: ClockIcon,
    under_review: ArrowPathIcon,
    additional_info: ExclamationCircleIcon,
    approved: CheckCircleIcon,
    rejected: XCircleIcon,
    cancelled: XCircleIcon,
    completed: CheckCircleIcon,
  }
  return icons[status] || DocumentTextIcon
}

// Format status text
const formatStatus = (status) => {
  return status.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

// Status options for filter
const statusOptions = [
  { value: '', label: 'All Applications' },
  { value: 'draft', label: 'Drafts' },
  { value: 'pending', label: 'Pending' },
  { value: 'under_review', label: 'Under Review' },
  { value: 'additional_info', label: 'Info Requested' },
  { value: 'approved', label: 'Approved' },
  { value: 'rejected', label: 'Rejected' },
  { value: 'cancelled', label: 'Cancelled' },
  { value: 'completed', label: 'Completed' },
]
</script>

<template>
  <AuthenticatedLayout>
    <Head title="My Applications" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">My Applications</h1>
          <p class="text-sm text-gray-600 mt-1">Track and manage your service applications</p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Status Filter -->
            <div>
              <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
                Status
              </label>
              <select
                id="status-filter"
                v-model="statusFilter"
                @change="applyFilters"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
              >
                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>

            <!-- Search -->
            <div class="md:col-span-2">
              <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                Search
              </label>
              <div class="flex gap-2">
                <input
                  id="search"
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search by application number or service name..."
                  @keyup.enter="applyFilters"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />
                <button
                  type="button"
                  @click="clearFilters"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Applications List -->
        <div v-if="applications.data.length > 0" class="space-y-4">
          <div
            v-for="application in applications.data"
            :key="application.id"
            class="bg-white shadow rounded-lg overflow-hidden hover:shadow-md transition-shadow"
          >
            <div class="p-6">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Application Info -->
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <Link
                        :href="route('applications.show', application.id)"
                        class="text-lg font-semibold text-gray-900 hover:text-primary-600"
                      >
                        {{ application.service.name }}
                      </Link>
                      <p class="text-sm text-gray-500 mt-1">
                        Application #{{ application.application_number }}
                      </p>
                    </div>
                    
                    <!-- Status Badge -->
                    <span
                      :class="getStatusBadge(application.status)"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      <component
                        :is="getStatusIcon(application.status)"
                        class="h-4 w-4 mr-1"
                      />
                      {{ formatStatus(application.status) }}
                    </span>
                  </div>

                  <!-- Application Details -->
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 text-sm text-gray-600">
                    <div>
                      <p class="text-xs text-gray-500">Submitted</p>
                      <p class="font-medium">
                        {{ application.submitted_at ? formatDate(application.submitted_at) : 'Draft' }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Last Updated</p>
                      <p class="font-medium">{{ formatDate(application.updated_at) }}</p>
                    </div>
                    <div v-if="application.service.processing_days">
                      <p class="text-xs text-gray-500">Processing Time</p>
                      <p class="font-medium">{{ application.service.processing_days }} days</p>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 lg:flex-col">
                  <!-- View -->
                  <Link
                    :href="route('applications.show', application.id)"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                  >
                    <EyeIcon class="h-4 w-4 mr-2" />
                    View
                  </Link>

                  <!-- Edit (only for drafts) -->
                  <Link
                    v-if="application.status === 'draft'"
                    :href="route('services.show', application.service.slug) + '?edit=' + application.id"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                  >
                    <PencilSquareIcon class="h-4 w-4 mr-2" />
                    Edit
                  </Link>

                  <!-- Cancel (for pending/under_review) -->
                  <button
                    v-if="['pending', 'under_review'].includes(application.status)"
                    @click="cancelApplication(application)"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-orange-700 bg-white border border-orange-300 rounded-md hover:bg-orange-50"
                  >
                    <XCircleIcon class="h-4 w-4 mr-2" />
                    Cancel
                  </button>

                  <!-- Delete (only for drafts) -->
                  <button
                    v-if="application.status === 'draft'"
                    @click="deleteApplication(application)"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-md hover:bg-red-50"
                  >
                    <TrashIcon class="h-4 w-4 mr-2" />
                    Delete
                  </button>
                </div>
              </div>

              <!-- Latest Status Update (if available) -->
              <div
                v-if="application.latest_status_update"
                class="mt-4 pt-4 border-t border-gray-200"
              >
                <p class="text-sm text-gray-600">
                  <span class="font-medium">Latest Update:</span>
                  {{ application.latest_status_update.notes }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  {{ formatDate(application.latest_status_update.created_at) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <Pagination :links="applications.links" />
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white shadow rounded-lg p-12 text-center">
          <DocumentTextIcon class="h-16 w-16 mx-auto text-gray-400" />
          <h3 class="mt-4 text-lg font-medium text-gray-900">No applications found</h3>
          <p class="mt-2 text-sm text-gray-600">
            {{ statusFilter || searchQuery ? 'Try adjusting your filters' : "You haven't submitted any applications yet" }}
          </p>
          <Link
            v-if="!statusFilter && !searchQuery"
            :href="route('services.index')"
            class="mt-6 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700"
          >
            Browse Services
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
