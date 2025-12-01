<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  XCircleIcon,
  ArrowPathIcon,
  HomeIcon,
  QuestionMarkCircleIcon,
  ChatBubbleLeftRightIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  amount: {
    type: Number,
    required: false
  },
  service: {
    type: Object,
    required: false
  },
  retryUrl: {
    type: String,
    required: false
  }
})

const { formatCurrency } = useBangladeshFormat()

const reasons = [
  {
    title: 'Changed Your Mind?',
    description: 'No problem! You can always come back and complete the payment when you\'re ready.',
    action: 'Browse Services',
    link: 'services.index'
  },
  {
    title: 'Need More Information?',
    description: 'Our team can help answer any questions about the service before you proceed.',
    action: 'Contact Support',
    link: 'support.index'
  },
  {
    title: 'Want a Different Payment Method?',
    description: 'We offer multiple payment options including bKash, Nagad, cards, and bank transfer.',
    action: 'View Payment Options',
    link: 'wallet.index'
  }
]
</script>

<template>
  <div>
    <Head title="Payment Cancelled" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-24 h-24 bg-yellow-100 rounded-full mb-4">
            <XCircleIcon class="h-16 w-16 text-yellow-600" />
          </div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Payment Cancelled
          </h1>
          <p class="text-lg text-gray-600">
            Your payment was cancelled and no charges were made.
          </p>
        </div>

        <!-- Cancellation Info Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Cancellation Details</h2>
          </div>

          <div class="p-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6">
              <div class="flex items-start">
                <QuestionMarkCircleIcon class="h-6 w-6 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" />
                <div>
                  <h3 class="font-semibold text-yellow-900 mb-1">What Happened?</h3>
                  <p class="text-yellow-800">
                    You chose to cancel the payment process. Don't worry, no money has been deducted from your account.
                  </p>
                </div>
              </div>
            </div>

            <!-- Transaction Details -->
            <div v-if="amount || service" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div v-if="amount" class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Amount (Not Charged)</p>
                <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(amount) }}</p>
              </div>

              <div v-if="service" class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">Service</p>
                <p class="text-base font-semibold text-gray-900">{{ service.name }}</p>
              </div>
            </div>

            <!-- Resume Payment Button -->
            <div v-if="retryUrl" class="pt-6 border-t">
              <a
                :href="retryUrl"
                class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                <ArrowPathIcon class="h-5 w-5 mr-2" />
                Resume Payment
              </a>
            </div>
          </div>
        </div>

        <!-- Why Did You Cancel? -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">How Can We Help?</h2>
          </div>

          <div class="p-6 space-y-4">
            <div
              v-for="(item, index) in reasons"
              :key="index"
              class="border border-gray-200 rounded-lg p-5 hover:border-blue-400 hover:shadow-md transition"
            >
              <h3 class="font-semibold text-gray-900 mb-2">{{ item.title }}</h3>
              <p class="text-gray-600 text-sm mb-4">{{ item.description }}</p>
              <Link
                :href="route(item.link)"
                class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm"
              >
                {{ item.action }} →
              </Link>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 mb-6 text-white">
          <div class="text-center">
            <h3 class="text-xl font-semibold mb-2">Did You Know?</h3>
            <p class="text-blue-100 mb-4">
              Over 10,000+ people have successfully used BideshGomon services to achieve their dreams abroad!
            </p>
            <div class="grid grid-cols-3 gap-4 mt-6">
              <div>
                <p class="text-3xl font-bold">98%</p>
                <p class="text-sm text-blue-100">Success Rate</p>
              </div>
              <div>
                <p class="text-3xl font-bold">24/7</p>
                <p class="text-sm text-blue-100">Support</p>
              </div>
              <div>
                <p class="text-3xl font-bold">50+</p>
                <p class="text-sm text-blue-100">Countries</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Support Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
          <div class="p-6 text-center">
            <ChatBubbleLeftRightIcon class="h-12 w-12 text-blue-600 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Still Have Questions?</h3>
            <p class="text-gray-600 mb-6">
              Our support team is here to help you 24/7. Feel free to reach out!
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
              <a
                href="mailto:support@bideshgomon.com"
                class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                Email Support
              </a>
              <a
                href="tel:+8801712345678"
                class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
              >
                Call Now
              </a>
            </div>

            <p class="text-sm text-gray-500 mt-4">
              support@bideshgomon.com • +880 1712-345678
            </p>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            v-if="retryUrl"
            :href="retryUrl"
            class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
          >
            <ArrowPathIcon class="h-5 w-5 mr-2" />
            Complete Payment
          </Link>

          <Link
            :href="route('services.index')"
            class="inline-flex items-center justify-center px-8 py-3 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition"
          >
            Browse Services
          </Link>

          <Link
            :href="route('dashboard')"
            class="inline-flex items-center justify-center px-8 py-3 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition"
          >
            <HomeIcon class="h-5 w-5 mr-2" />
            Dashboard
          </Link>
        </div>

        <!-- Reassurance Message -->
        <div class="text-center mt-8 text-gray-600">
          <p class="text-sm">
            🔒 Your payment information is secure and was not saved. You can retry anytime.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
