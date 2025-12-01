<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  XCircleIcon,
  ArrowPathIcon,
  CreditCardIcon,
  PhoneIcon,
  EnvelopeIcon,
  HomeIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  transaction: {
    type: Object,
    required: false
  },
  errorMessage: {
    type: String,
    default: 'Payment could not be processed'
  },
  amount: {
    type: Number,
    required: false
  },
  retryUrl: {
    type: String,
    required: false
  }
})

const { formatCurrency } = useBangladeshFormat()

const commonReasons = [
  'Insufficient balance in your account',
  'Card declined by your bank',
  'Incorrect card details or OTP',
  'Payment gateway timeout',
  'Network connectivity issues',
  'Transaction limit exceeded'
]

const nextSteps = [
  'Check your account balance and try again',
  'Verify your card details are correct',
  'Contact your bank if the issue persists',
  'Try a different payment method',
  'Wait a few minutes and retry',
  'Contact our support team for assistance'
]
</script>

<template>
  <div>
    <Head title="Payment Failed" />

    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 py-12">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Error Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-4">
            <XCircleIcon class="h-16 w-16 text-red-600" />
          </div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Payment Failed
          </h1>
          <p class="text-lg text-gray-600">
            Unfortunately, we couldn't process your payment.
          </p>
        </div>

        <!-- Error Details Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Transaction Details</h2>
          </div>

          <div class="p-6">
            <!-- Error Message -->
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6">
              <div class="flex items-start">
                <ExclamationTriangleIcon class="h-6 w-6 text-red-600 mt-0.5 mr-3 flex-shrink-0" />
                <div>
                  <h3 class="font-semibold text-red-900 mb-1">Error Message</h3>
                  <p class="text-red-800">{{ errorMessage }}</p>
                </div>
              </div>
            </div>

            <!-- Transaction Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div v-if="amount" class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Attempted Amount</p>
                <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(amount) }}</p>
              </div>

              <div v-if="transaction?.transaction_id" class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Transaction Reference</p>
                <p class="text-base font-mono text-gray-900">{{ transaction.transaction_id }}</p>
              </div>
            </div>

            <!-- Retry Button -->
            <div v-if="retryUrl" class="pt-6 border-t">
              <a
                :href="retryUrl"
                class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                <ArrowPathIcon class="h-5 w-5 mr-2" />
                Retry Payment
              </a>
            </div>
          </div>
        </div>

        <!-- Common Reasons -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Common Reasons for Payment Failure</h2>
          </div>

          <div class="p-6">
            <ul class="space-y-3">
              <li
                v-for="(reason, index) in commonReasons"
                :key="index"
                class="flex items-start"
              >
                <span class="flex-shrink-0 w-6 h-6 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">
                  {{ index + 1 }}
                </span>
                <span class="text-gray-700">{{ reason }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6">
          <h3 class="text-lg font-semibold text-blue-900 mb-3">💡 What Should You Do?</h3>
          <ul class="space-y-2">
            <li
              v-for="(step, index) in nextSteps"
              :key="index"
              class="flex items-start text-blue-800"
            >
              <span class="mr-2">•</span>
              <span>{{ step }}</span>
            </li>
          </ul>
        </div>

        <!-- Alternative Payment Methods -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Try Alternative Payment Methods</h2>
          </div>

          <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 text-center">
              <CreditCardIcon class="h-8 w-8 text-pink-600 mx-auto mb-2" />
              <p class="font-semibold text-gray-900">bKash / Nagad</p>
              <p class="text-xs text-gray-600 mt-1">Instant mobile banking</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center">
              <CreditCardIcon class="h-8 w-8 text-blue-600 mx-auto mb-2" />
              <p class="font-semibold text-gray-900">Credit/Debit Card</p>
              <p class="text-xs text-gray-600 mt-1">Visa, Mastercard</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 text-center">
              <CreditCardIcon class="h-8 w-8 text-purple-600 mx-auto mb-2" />
              <p class="font-semibold text-gray-900">Bank Transfer</p>
              <p class="text-xs text-gray-600 mt-1">Direct deposit</p>
            </div>
          </div>
        </div>

        <!-- Support Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Need Help?</h2>
          </div>

          <div class="p-6">
            <p class="text-gray-700 mb-4">
              Our support team is available 24/7 to help you complete your payment.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <a
                href="mailto:support@bideshgomon.com"
                class="flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                <EnvelopeIcon class="h-5 w-5 mr-2" />
                Email Support
              </a>

              <a
                href="tel:+8801712345678"
                class="flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
              >
                <PhoneIcon class="h-5 w-5 mr-2" />
                Call Support
              </a>
            </div>

            <div class="mt-4 text-center text-sm text-gray-600">
              <p>Email: support@bideshgomon.com</p>
              <p>Phone: +880 1712-345678</p>
              <p v-if="transaction?.transaction_id" class="mt-2">
                Reference: <span class="font-mono">{{ transaction.transaction_id }}</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Quick Navigation -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            v-if="retryUrl"
            :href="retryUrl"
            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
          >
            <ArrowPathIcon class="h-5 w-5 mr-2" />
            Try Again
          </Link>

          <Link
            :href="route('dashboard')"
            class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition"
          >
            <HomeIcon class="h-5 w-5 mr-2" />
            Back to Dashboard
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
