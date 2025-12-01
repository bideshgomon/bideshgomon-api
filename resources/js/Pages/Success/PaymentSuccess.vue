<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  CheckCircleIcon,
  ArrowDownTrayIcon,
  WalletIcon,
  DocumentTextIcon,
  HomeIcon,
  ReceiptPercentIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  transaction: {
    type: Object,
    required: true
  },
  walletBalance: {
    type: Number,
    required: true
  }
})

const { formatDate, formatTime, formatCurrency } = useBangladeshFormat()

const downloadReceipt = () => {
  window.open(route('payments.receipt', props.transaction.id), '_blank')
}
</script>

<template>
  <div>
    <Head title="Payment Successful" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 py-12">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-4 animate-bounce">
            <CheckCircleIcon class="h-16 w-16 text-green-600" />
          </div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Payment Successful! 💳
          </h1>
          <p class="text-lg text-gray-600">
            Your payment has been processed successfully.
          </p>
        </div>

        <!-- Transaction Details Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Transaction Details</h2>
          </div>

          <div class="p-6">
            <!-- Amount -->
            <div class="text-center py-6 border-b">
              <p class="text-sm text-gray-500 mb-2">Amount Paid</p>
              <p class="text-4xl font-bold text-green-600">
                {{ formatCurrency(transaction.amount) }}
              </p>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Transaction ID</p>
                <p class="text-base font-semibold text-gray-900">{{ transaction.transaction_id }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                <p class="text-base font-semibold text-gray-900 capitalize">{{ transaction.payment_method }}</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Date & Time</p>
                <p class="text-base font-semibold text-gray-900">
                  {{ formatDate(transaction.created_at) }} {{ formatTime(transaction.created_at) }}
                </p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                  <CheckCircleIcon class="h-4 w-4 mr-1" />
                  Success
                </span>
              </div>

              <div v-if="transaction.reference" class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Reference</p>
                <p class="text-base font-semibold text-gray-900">{{ transaction.reference }}</p>
              </div>
            </div>

            <!-- Download Button -->
            <div class="mt-6 pt-6 border-t">
              <button
                @click="downloadReceipt"
                class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                Download Receipt
              </button>
            </div>
          </div>
        </div>

        <!-- Wallet Balance Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                  <WalletIcon class="h-6 w-6 text-purple-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm text-gray-500">Current Wallet Balance</p>
                  <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(walletBalance) }}</p>
                </div>
              </div>
              <Link
                :href="route('wallet.index')"
                class="text-purple-600 hover:text-purple-700 font-semibold text-sm"
              >
                View Wallet →
              </Link>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6 mb-6">
          <h3 class="text-lg font-semibold text-green-900 mb-3">✓ What's Next?</h3>
          <ul class="space-y-2 text-green-800">
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>A confirmation email has been sent to your registered email address</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>You can download your receipt anytime from your transaction history</span>
            </li>
            <li class="flex items-start">
              <span class="mr-2">•</span>
              <span>Your wallet balance has been updated and is ready to use</span>
            </li>
            <li v-if="transaction.reference_type === 'application'" class="flex items-start">
              <span class="mr-2">•</span>
              <span>Your application is now being processed by our team</span>
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
              v-if="transaction.reference_type === 'application'"
              :href="route('applications.show', transaction.reference_id)"
              class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center"
            >
              <DocumentTextIcon class="h-8 w-8 text-blue-600 mb-2" />
              <span class="font-semibold text-gray-900">View Application</span>
              <span class="text-sm text-gray-600 mt-1">Check status</span>
            </Link>

            <Link
              :href="route('wallet.transactions')"
              class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center"
            >
              <ReceiptPercentIcon class="h-8 w-8 text-green-600 mb-2" />
              <span class="font-semibold text-gray-900">Transaction History</span>
              <span class="text-sm text-gray-600 mt-1">View all transactions</span>
            </Link>

            <Link
              :href="route('dashboard')"
              class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center"
            >
              <HomeIcon class="h-8 w-8 text-purple-600 mb-2" />
              <span class="font-semibold text-gray-900">Dashboard</span>
              <span class="text-sm text-gray-600 mt-1">Back to home</span>
            </Link>
          </div>
        </div>

        <!-- Support Contact -->
        <div class="text-center mt-8 text-gray-600">
          <p class="text-sm">Transaction ID: <span class="font-mono text-gray-900">{{ transaction.transaction_id }}</span></p>
          <p class="mt-2 text-sm">
            Need help? Contact us at 
            <a href="mailto:support@bideshgomon.com" class="text-blue-600 hover:underline">support@bideshgomon.com</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
