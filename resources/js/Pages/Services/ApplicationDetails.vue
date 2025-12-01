<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  ArrowLeftIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  ExclamationCircleIcon,
  ArrowDownTrayIcon,
  PencilSquareIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const { formatDate, formatCurrency } = useBangladeshFormat()

const props = defineProps({
  application: {
    type: Object,
    required: true
  }
})

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
    under_review: ClockIcon,
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

// Delete application
const deleteApplication = () => {
  if (confirm(`Are you sure you want to delete this application?`)) {
    router.delete(route('applications.destroy', props.application.id), {
      onSuccess: () => {
        router.visit(route('applications.index'))
      }
    })
  }
}

// Cancel application
const cancelApplication = () => {
  if (confirm(`Are you sure you want to cancel this application?`)) {
    router.post(route('applications.cancel', props.application.id), {}, {
      onSuccess: () => {
        alert('Application cancelled successfully')
      }
    })
  }
}

// Download PDF
const downloadPdf = () => {
  window.open(route('applications.download-pdf', props.application.id), '_blank')
}

// Format field value for display
const formatFieldValue = (value, fieldType) => {
  if (value === null || value === undefined || value === '') {
    return 'Not provided'
  }
  
  if (Array.isArray(value)) {
    return value.join(', ')
  }
  
  if (fieldType === 'date') {
    return formatDate(value)
  }
  
  if (fieldType === 'checkbox') {
    return value ? 'Yes' : 'No'
  }
  
  if (typeof value === 'boolean') {
    return value ? 'Yes' : 'No'
  }
  
  return value
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Application #${application.application_number}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <Link
          :href="route('applications.index')"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
        >
          <ArrowLeftIcon class="h-4 w-4 mr-1" />
          Back to Applications
        </Link>

        <!-- Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ application.service.name }}
              </h1>
              <p class="text-sm text-gray-600 mt-1">
                Application #{{ application.application_number }}
              </p>
            </div>

            <!-- Status Badge -->
            <span
              :class="getStatusBadge(application.status)"
              class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium"
            >
              <component
                :is="getStatusIcon(application.status)"
                class="h-5 w-5 mr-2"
              />
              {{ formatStatus(application.status) }}
            </span>
          </div>

          <!-- Application Info -->
          <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
              <p class="text-gray-500">Submitted</p>
              <p class="font-medium text-gray-900 mt-1">
                {{ application.submitted_at ? formatDate(application.submitted_at) : 'Draft' }}
              </p>
            </div>
            <div>
              <p class="text-gray-500">Last Updated</p>
              <p class="font-medium text-gray-900 mt-1">
                {{ formatDate(application.updated_at) }}
              </p>
            </div>
            <div v-if="application.service.processing_days">
              <p class="text-gray-500">Processing Time</p>
              <p class="font-medium text-gray-900 mt-1">
                {{ application.service.processing_days }} days
              </p>
            </div>
            <div v-if="application.service.requires_approval">
              <p class="text-gray-500">Approval</p>
              <p class="font-medium text-gray-900 mt-1">Required</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex flex-wrap gap-3">
            <Link
              v-if="application.status === 'draft'"
              :href="route('services.show', application.service.slug) + '?edit=' + application.id"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700"
            >
              <PencilSquareIcon class="h-4 w-4 mr-2" />
              Edit Application
            </Link>

            <button
              @click="downloadPdf"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
              Download PDF
            </button>

            <button
              v-if="['pending', 'under_review'].includes(application.status)"
              @click="cancelApplication"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-orange-700 bg-white border border-orange-300 rounded-md hover:bg-orange-50"
            >
              <XCircleIcon class="h-4 w-4 mr-2" />
              Cancel Application
            </button>

            <button
              v-if="application.status === 'draft'"
              @click="deleteApplication"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-md hover:bg-red-50"
            >
              <TrashIcon class="h-4 w-4 mr-2" />
              Delete
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Application Data -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Form Data by Groups -->
            <div
              v-for="(fields, groupName) in application.grouped_form_data"
              :key="groupName"
              class="bg-white shadow rounded-lg p-6"
            >
              <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ groupName }}</h2>
              
              <dl class="space-y-4">
                <div
                  v-for="field in fields"
                  :key="field.name"
                  class="border-b border-gray-200 pb-3 last:border-0 last:pb-0"
                >
                  <dt class="text-sm font-medium text-gray-500">{{ field.label }}</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ formatFieldValue(field.value, field.type) }}
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Raw Form Data (fallback if grouped_form_data not available) -->
            <div v-if="!application.grouped_form_data && application.form_data" class="bg-white shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Application Data</h2>
              
              <dl class="space-y-4">
                <div
                  v-for="(value, key) in application.form_data"
                  :key="key"
                  class="border-b border-gray-200 pb-3 last:border-0 last:pb-0"
                >
                  <dt class="text-sm font-medium text-gray-500">
                    {{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ formatFieldValue(value) }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Status History -->
            <div class="bg-white shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Status History</h2>
              
              <div v-if="application.status_history && application.status_history.length > 0" class="space-y-4">
                <div
                  v-for="(history, index) in application.status_history"
                  :key="history.id"
                  class="relative"
                >
                  <!-- Timeline Line -->
                  <div
                    v-if="index < application.status_history.length - 1"
                    class="absolute left-2 top-8 bottom-0 w-0.5 bg-gray-200"
                  />
                  
                  <div class="flex items-start">
                    <div
                      :class="getStatusBadge(history.new_status)"
                      class="flex-shrink-0 h-4 w-4 rounded-full flex items-center justify-center mt-1"
                    />
                    
                    <div class="ml-4 flex-1">
                      <p class="text-sm font-medium text-gray-900">
                        {{ formatStatus(history.new_status) }}
                      </p>
                      <p v-if="history.notes" class="text-sm text-gray-600 mt-1">
                        {{ history.notes }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        {{ formatDate(history.created_at, true) }}
                      </p>
                      <p v-if="history.changed_by_name" class="text-xs text-gray-500">
                        by {{ history.changed_by_name }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <p v-else class="text-sm text-gray-500">
                No status updates yet
              </p>
            </div>

            <!-- Documents -->
            <div v-if="application.documents && application.documents.length > 0" class="bg-white shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Documents</h2>
              
              <div class="space-y-3">
                <div
                  v-for="document in application.documents"
                  :key="document.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center min-w-0">
                    <DocumentTextIcon class="h-5 w-5 text-gray-400 flex-shrink-0" />
                    <div class="ml-3 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ document.document_type || 'Document' }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ document.file_name }}
                      </p>
                    </div>
                  </div>
                  
                  <a
                    :href="document.file_path"
                    target="_blank"
                    class="ml-4 flex-shrink-0 text-primary-600 hover:text-primary-700"
                  >
                    <ArrowDownTrayIcon class="h-5 w-5" />
                  </a>
                </div>
              </div>
            </div>

            <!-- Service Info -->
            <div class="bg-white shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Service Information</h2>
              
              <Link
                :href="route('services.show', application.service.slug)"
                class="block hover:bg-gray-50 rounded-lg p-3 -m-3"
              >
                <p class="text-sm font-medium text-primary-600">
                  {{ application.service.name }}
                </p>
                <p v-if="application.service.short_description" class="text-sm text-gray-600 mt-1">
                  {{ application.service.short_description }}
                </p>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
