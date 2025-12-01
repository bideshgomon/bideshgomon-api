<script setup>
import { Head, Link } from '@inertiajs/vue3'
import {
  LockClosedIcon,
  RocketLaunchIcon,
  CheckCircleIcon,
  SparklesIcon,
  CreditCardIcon,
} from '@heroicons/vue/24/outline'

const premiumFeatures = [
  {
    icon: RocketLaunchIcon,
    title: 'Priority Processing',
    description: 'Get your applications processed 3x faster with dedicated support',
    badge: 'Premium'
  },
  {
    icon: CheckCircleIcon,
    title: 'Document Review',
    description: 'Expert review of all documents before submission to ensure accuracy',
    badge: 'Premium'
  },
  {
    icon: SparklesIcon,
    title: 'Unlimited Applications',
    description: 'Apply to unlimited services without restrictions',
    badge: 'Premium'
  },
  {
    icon: CheckCircleIcon,
    title: 'Personal Consultant',
    description: 'Dedicated consultant for one-on-one guidance',
    badge: 'Premium'
  },
  {
    icon: RocketLaunchIcon,
    title: 'Interview Preparation',
    description: 'Mock interviews and preparation materials included',
    badge: 'Premium'
  },
  {
    icon: SparklesIcon,
    title: 'Premium Resources',
    description: 'Access to exclusive guides, templates, and checklists',
    badge: 'Premium'
  }
]

const pricingPlans = [
  {
    name: 'Basic',
    price: '৳0',
    period: 'Free Forever',
    features: [
      'Apply to 2 services per month',
      'Standard processing time',
      'Email support (48hr response)',
      'Basic document templates'
    ],
    current: true
  },
  {
    name: 'Premium',
    price: '৳2,999',
    period: 'per month',
    features: [
      'Unlimited applications',
      '3x faster processing',
      'Priority support (2hr response)',
      'Expert document review',
      'Personal consultant',
      'Interview preparation',
      'Premium resources & guides',
      'Success guarantee or refund'
    ],
    highlighted: true,
    discount: 'Save 40% with annual plan'
  },
  {
    name: 'Enterprise',
    price: '৳9,999',
    period: 'per month',
    features: [
      'Everything in Premium',
      'Dedicated account manager',
      'Custom application strategies',
      'Company/family packages',
      'Bulk application discounts',
      'White-label solutions',
      'API access'
    ]
  }
]
</script>

<template>
  <div>
    <Head title="Premium Feature - Upgrade Required" />

    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-6">
            <LockClosedIcon class="h-12 w-12 text-purple-600" />
          </div>
          <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
            Premium Feature 🚀
          </h1>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            This feature is available exclusively for Premium members. 
            Upgrade now to unlock powerful tools and accelerate your journey abroad!
          </p>
        </div>

        <!-- What You're Missing -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-12">
          <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
            <h2 class="text-2xl font-bold text-white">What You'll Get with Premium</h2>
          </div>

          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="(feature, index) in premiumFeatures"
                :key="index"
                class="relative bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-6 border-2 border-transparent hover:border-purple-400 transition group"
              >
                <span class="absolute top-4 right-4 text-xs font-semibold px-2 py-1 bg-purple-600 text-white rounded-full">
                  {{ feature.badge }}
                </span>
                <component
                  :is="feature.icon"
                  class="h-10 w-10 text-purple-600 mb-4 group-hover:scale-110 transition"
                />
                <h3 class="font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
                <p class="text-sm text-gray-600">{{ feature.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pricing Plans -->
        <div class="mb-12">
          <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Choose Your Plan</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
              v-for="(plan, index) in pricingPlans"
              :key="index"
              :class="[
                'relative bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-2xl',
                plan.highlighted ? 'ring-4 ring-purple-500 scale-105 z-10' : ''
              ]"
            >
              <div
                v-if="plan.highlighted"
                class="absolute top-0 right-0 bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs font-bold px-4 py-1 rounded-bl-lg"
              >
                MOST POPULAR
              </div>

              <div :class="[
                'px-8 py-6',
                plan.highlighted ? 'bg-gradient-to-br from-purple-600 to-blue-600 text-white' : 'bg-gray-50'
              ]">
                <h3 :class="[
                  'text-2xl font-bold mb-2',
                  plan.highlighted ? 'text-white' : 'text-gray-900'
                ]">
                  {{ plan.name }}
                </h3>
                <div class="flex items-baseline">
                  <span :class="[
                    'text-4xl font-extrabold',
                    plan.highlighted ? 'text-white' : 'text-gray-900'
                  ]">
                    {{ plan.price }}
                  </span>
                  <span :class="[
                    'ml-2 text-sm',
                    plan.highlighted ? 'text-purple-100' : 'text-gray-600'
                  ]">
                    {{ plan.period }}
                  </span>
                </div>
                <p v-if="plan.discount" class="text-sm mt-2 text-purple-100 font-semibold">
                  {{ plan.discount }}
                </p>
              </div>

              <div class="px-8 py-6">
                <ul class="space-y-3 mb-8">
                  <li
                    v-for="(feature, idx) in plan.features"
                    :key="idx"
                    class="flex items-start"
                  >
                    <CheckCircleIcon class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" />
                    <span class="text-gray-700">{{ feature }}</span>
                  </li>
                </ul>

                <button
                  v-if="plan.current"
                  disabled
                  class="w-full py-3 px-6 bg-gray-200 text-gray-500 font-semibold rounded-lg cursor-not-allowed"
                >
                  Current Plan
                </button>
                <Link
                  v-else
                  :href="route('dashboard')"
                  :class="[
                    'block w-full text-center py-3 px-6 font-semibold rounded-lg transition',
                    plan.highlighted
                      ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl'
                      : 'bg-gray-900 text-white hover:bg-gray-800'
                  ]"
                >
                  Upgrade to {{ plan.name }}
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Money-Back Guarantee -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-8 mb-12 text-center">
          <CheckCircleIcon class="h-16 w-16 text-green-600 mx-auto mb-4" />
          <h3 class="text-2xl font-bold text-gray-900 mb-3">30-Day Money-Back Guarantee</h3>
          <p class="text-gray-700 max-w-2xl mx-auto">
            Try Premium risk-free for 30 days. If you're not completely satisfied, 
            we'll refund your payment—no questions asked. Your success is our priority!
          </p>
        </div>

        <!-- Testimonials -->
        <div class="bg-white shadow-xl rounded-2xl p-8 mb-12">
          <h3 class="text-2xl font-bold text-center text-gray-900 mb-8">What Our Premium Members Say</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                  <span v-for="i in 5" :key="i">⭐</span>
                </div>
              </div>
              <p class="text-gray-700 mb-4 italic">
                "Premium made everything so much faster! Got my visa approved in record time with the priority processing."
              </p>
              <p class="font-semibold text-gray-900">- Rashid Ahmed, Canada Visa</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
              <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                  <span v-for="i in 5" :key="i">⭐</span>
                </div>
              </div>
              <p class="text-gray-700 mb-4 italic">
                "The personal consultant was a game-changer. They guided me through every step of my UK study visa."
              </p>
              <p class="font-semibold text-gray-900">- Nadia Rahman, UK Student Visa</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
              <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                  <span v-for="i in 5" :key="i">⭐</span>
                </div>
              </div>
              <p class="text-gray-700 mb-4 italic">
                "Best investment I made! The interview prep and document review saved me from costly mistakes."
              </p>
              <p class="font-semibold text-gray-900">- Karim Hassan, Australia Work Visa</p>
            </div>
          </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            :href="route('dashboard')"
            class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-lg hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transition text-lg"
          >
            <RocketLaunchIcon class="h-6 w-6 mr-2" />
            Upgrade to Premium Now
          </Link>

          <Link
            :href="route('welcome')"
            class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition"
          >
            Maybe Later
          </Link>
        </div>

        <!-- Support -->
        <div class="text-center mt-12 text-gray-600">
          <p class="mb-2">Have questions about Premium?</p>
          <div class="flex justify-center gap-6 text-sm">
            <a href="mailto:support@bideshgomon.com" class="text-blue-600 hover:underline">
              Email Us
            </a>
            <span>•</span>
            <a href="tel:+8801712345678" class="text-blue-600 hover:underline">
              +880 1712-345678
            </a>
            <span>•</span>
            <Link :href="route('welcome')" class="text-blue-600 hover:underline">
              Learn More
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
