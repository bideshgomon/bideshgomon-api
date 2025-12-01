<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  CheckCircleIcon,
  ClockIcon,
  DocumentTextIcon,
  ArrowDownTrayIcon,
  HomeIcon,
  RocketLaunchIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  application: {
    type: Object,
    required: true
  },
  service: {
    type: Object,
    required: true
  }
})

const { formatDate, formatTime, formatCurrency } = useBangladeshFormat()

const timeline = [
  {
    step: 1,
    title: 'Application Submitted',
    description: 'Your application has been received',
    status: 'complete',
    icon: CheckCircleIcon
  },
  {
    step: 2,
    title: 'Document Review',
    description: 'Our team will review your documents within 24-48 hours',
    status: 'pending',
    icon: DocumentTextIcon,
    eta: '1-2 business days'
  },
  {
    step: 3,
    title: 'Processing',
    description: 'Application will be processed and submitted to relevant authorities',
    status: 'pending',
    icon: ClockIcon,
    eta: '3-7 business days'
  },
  {
    step: 4,
    title: 'Notification',
    description: 'You will receive updates via email and SMS',
    status: 'pending',
    icon: RocketLaunchIcon,
    eta: 'Upon completion'
  }
]

const downloadReceipt = () => {
  // Navigate to receipt download route
  window.open(route('applications.receipt', props.application.id), '_blank')
}
</script>

<template>
  <div>
    <Head title="Application Submitted Successfully" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4 animate-bounce">
            <CheckCircleIcon class="h-12 w-12 text-green-600" />
          </div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Application Submitted Successfully! 🎉
          </h1>
          <p class="text-lg text-gray-600">
            Your {{ service.name }} application has been received and is being processed.
          </p>
        </div>

        <!-- Application Details Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-green-600 to-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Application Details</h2>
          </div>

          <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Application ID</p>
                <p class="text-lg font-bold text-gray-900">#{{ application.application_number }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Submission Date</p>
                <p class="text-lg font-semibold text-gray-900">
                  {{ formatDate(application.created_at) }} {{ formatTime(application.created_at) }}
                </p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Service Type</p>
                <p class="text-lg font-semibold text-gray-900">{{ service.name }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                  <ClockIcon class="h-4 w-4 mr-1" />
                  {{ application.status }}
                </span>
              </div>

              <div v-if="application.amount_paid" class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Amount Paid</p>
                <p class="text-2xl font-bold text-green-600">{{ formatCurrency(application.amount_paid) }}</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
              <button
                @click="downloadReceipt"
                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                Download Receipt
              </button>

              <Link
                :href="route('applications.show', application.id)"
                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg border-2 border-blue-600 hover:bg-blue-50 transition"
              >
                <DocumentTextIcon class="h-5 w-5 mr-2" />
                View Application
              </Link>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">What Happens Next?</h2>
          </div>

          <div class="p-6">
            <div class="space-y-6">
              <div
                v-for="(item, index) in timeline"
                :key="index"
                class="flex items-start"
              >
                <div class="flex-shrink-0">
                  <div
                    :class="[
                      'flex items-center justify-center w-10 h-10 rounded-full',
                      item.status === 'complete' ? 'bg-green-100' : 'bg-gray-100'
                    ]"
                  >
                    <component
                      :is="item.icon"
                      :class="[
                        'h-6 w-6',
                        item.status === 'complete' ? 'text-green-600' : 'text-gray-400'
                      ]"
                    />
                  </div>
                </div>

                <div class="ml-4 flex-1">
                  <div class="flex items-center justify-between">
                    <h3
                      :class="[
                        'text-lg font-semibold',
                        item.status === 'complete' ? 'text-green-600' : 'text-gray-900'
                      ]"
                    >
                      {{ item.step }}. {{ item.title }}
                    </h3>
                    <span
                      v-if="item.status === 'complete'"
                      class="text-sm font-medium text-green-600"
                    >
                      ✓ Complete
                    </span>
                    <span
                      v-else-if="item.eta"
                      class="text-sm text-gray-500"
                    >
                      ETA: {{ item.eta }}
                    </span>
                  </div>
                  <p class="text-gray-600 mt-1">{{ item.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Important Information -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6">
          <h3 class="text-lg font-semibold text-blue-900 mb-3">📧 Important Information</h3>
          <ul class="space-y-2 text-blue-800">
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>You will receive email and SMS notifications at each stage of processing</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>Please check your email regularly (including spam folder)</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>Keep your Application ID handy for any inquiries: <strong>#{{ application.application_number }}</strong></span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>If you have questions, contact support with your Application ID</span>
            </li>
          </ul>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
          </div>

          <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link
              :href="route('applications.index')"
              class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center"
            >
              <DocumentTextIcon class="h-8 w-8 text-blue-600 mb-2" />
              <span class="font-semibold text-gray-900">My Applications</span>
              <span class="text-sm text-gray-600 mt-1">View all applications</span>
            </Link>

            <Link
              :href="route('services.index')"
              class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center"
            >
              <RocketLaunchIcon class="h-8 w-8 text-green-600 mb-2" />
              <span class="font-semibold text-gray-900">Browse Services</span>
              <span class="text-sm text-gray-600 mt-1">Explore more services</span>
            </Link>

            <Link
              :href="route('dashboard')"
              class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center"
            >
              <HomeIcon class="h-8 w-8 text-purple-600 mb-2" />
              <span class="font-semibold text-gray-900">Dashboard</span>
              <span class="text-sm text-gray-600 mt-1">Back to dashboard</span>
            </Link>
          </div>
        </div>

        <!-- Support Contact -->
        <div class="text-center mt-8 text-gray-600">
          <p class="mb-2">Need help? Our support team is here for you.</p>
          <div class="flex justify-center gap-4 text-sm">
            <a href="mailto:support@bideshgomon.com" class="text-blue-600 hover:underline">
              Email Support
            </a>
            <span>•</span>
            <a href="tel:+8801712345678" class="text-blue-600 hover:underline">
              +880 1712-345678
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
