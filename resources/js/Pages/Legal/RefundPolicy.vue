<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { 
  ShieldCheckIcon,
  DocumentTextIcon,
  ClockIcon,
  CreditCardIcon,
  EnvelopeIcon,
} from '@heroicons/vue/24/outline'

const companyEmail = 'support@bideshgomon.com'
const companyPhone = '+880 1712-345678'
</script>

<template>
  <div>
    <Head title="Refund Policy - BideshGomon" />

    <div class="min-h-screen bg-gray-50 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8">
            <div class="flex items-center justify-center mb-4">
              <ShieldCheckIcon class="h-12 w-12 text-white" />
            </div>
            <h1 class="text-3xl font-bold text-white text-center">Refund Policy</h1>
            <p class="text-blue-100 text-center mt-2">Last Updated: December 1, 2025</p>
          </div>

          <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
            <p class="text-sm text-blue-800 text-center">
              At BideshGomon, your satisfaction is our priority. Please read our refund policy carefully.
            </p>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow rounded-lg p-8 space-y-8">
          <!-- Overview -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <DocumentTextIcon class="h-6 w-6 mr-2 text-blue-600" />
              Overview
            </h2>
            <p class="text-gray-700 leading-relaxed">
              This Refund Policy governs the refund process for services purchased through BideshGomon.com. 
              We strive to provide transparent and fair refund terms for all our services including visa applications, 
              job placements, educational consultancy, and travel packages.
            </p>
          </section>

          <!-- Refund Eligibility -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Refund Eligibility</h2>
            <div class="space-y-4">
              <div class="bg-green-50 border-l-4 border-green-500 p-4">
                <h3 class="font-semibold text-green-900 mb-2">✓ Eligible for Full Refund</h3>
                <ul class="list-disc list-inside text-green-800 space-y-1 ml-4">
                  <li>Service not delivered within agreed timeframe (with no valid reason)</li>
                  <li>Duplicate payment or technical error during transaction</li>
                  <li>Service cancelled by BideshGomon before commencement</li>
                  <li>Proven service deficiency or misrepresentation</li>
                </ul>
              </div>

              <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                <h3 class="font-semibold text-yellow-900 mb-2">⚠ Eligible for Partial Refund (50-80%)</h3>
                <ul class="list-disc list-inside text-yellow-800 space-y-1 ml-4">
                  <li>Service cancellation requested 7+ days before scheduled date</li>
                  <li>Visa application rejected due to incomplete documentation (client fault)</li>
                  <li>Job placement services partially rendered</li>
                </ul>
              </div>

              <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <h3 class="font-semibold text-red-900 mb-2">✗ Not Eligible for Refund</h3>
                <ul class="list-disc list-inside text-red-800 space-y-1 ml-4">
                  <li>Visa rejection by embassy (beyond our control)</li>
                  <li>Service fully completed as per agreement</li>
                  <li>Cancellation requested within 48 hours of service delivery</li>
                  <li>Third-party fees (embassy fees, government charges)</li>
                  <li>Change of mind after service commencement</li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Service-Specific Policies -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Service-Specific Refund Terms</h2>
            
            <div class="space-y-6">
              <!-- Visa Services -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🛂 Visa Application Services</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Before Submission:</span>
                    <span>100% refund minus processing fee (৳500)</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">After Submission:</span>
                    <span>No refund (embassy fees non-refundable)</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Application Error:</span>
                    <span>Full refund if error caused by our team</span>
                  </li>
                </ul>
              </div>

              <!-- Job Placement -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">💼 Job Placement Services</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Within 30 Days:</span>
                    <span>80% refund if no job offer received</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">After 30 Days:</span>
                    <span>50% refund if service ongoing</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Job Secured:</span>
                    <span>No refund (service completed)</span>
                  </li>
                </ul>
              </div>

              <!-- Education Consultancy -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🎓 Education Consultancy</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Before Application:</span>
                    <span>90% refund (consultation fee retained)</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Application Submitted:</span>
                    <span>50% refund</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Admission Secured:</span>
                    <span>No refund</span>
                  </li>
                </ul>
              </div>

              <!-- Travel Packages -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">✈️ Travel & Flight Bookings</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">30+ Days Before:</span>
                    <span>90% refund (subject to airline policy)</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">15-29 Days Before:</span>
                    <span>50% refund</span>
                  </li>
                  <li class="flex items-start">
                    <span class="font-semibold min-w-[140px]">Less than 15 Days:</span>
                    <span>No refund (as per airline cancellation policy)</span>
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Refund Process -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <ClockIcon class="h-6 w-6 mr-2 text-blue-600" />
              Refund Request Process
            </h2>
            <div class="bg-gray-50 rounded-lg p-6">
              <ol class="space-y-4">
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">1</span>
                  <div>
                    <p class="font-semibold text-gray-900">Submit Refund Request</p>
                    <p class="text-gray-600 text-sm mt-1">
                      Email {{ companyEmail }} with your application ID, payment receipt, and reason for refund.
                    </p>
                  </div>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">2</span>
                  <div>
                    <p class="font-semibold text-gray-900">Review Period (3-5 Business Days)</p>
                    <p class="text-gray-600 text-sm mt-1">
                      Our team will review your request and verify eligibility according to this policy.
                    </p>
                  </div>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">3</span>
                  <div>
                    <p class="font-semibold text-gray-900">Decision Notification</p>
                    <p class="text-gray-600 text-sm mt-1">
                      You will receive an email with approval/rejection and refund amount breakdown.
                    </p>
                  </div>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold mr-4">4</span>
                  <div>
                    <p class="font-semibold text-gray-900">Refund Processing (7-14 Business Days)</p>
                    <p class="text-gray-600 text-sm mt-1">
                      Approved refunds will be credited to your original payment method or BideshGomon Wallet.
                    </p>
                  </div>
                </li>
              </ol>
            </div>
          </section>

          <!-- Payment Methods -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <CreditCardIcon class="h-6 w-6 mr-2 text-blue-600" />
              Refund Methods & Timeline
            </h2>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Refund Timeline</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">bKash / Nagad</td>
                    <td class="px-6 py-4 whitespace-nowrap">3-5 business days</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Instant refund to mobile wallet</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Credit/Debit Card</td>
                    <td class="px-6 py-4 whitespace-nowrap">7-14 business days</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Bank processing time applies</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Bank Transfer</td>
                    <td class="px-6 py-4 whitespace-nowrap">5-10 business days</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Provide valid bank details</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">BideshGomon Wallet</td>
                    <td class="px-6 py-4 whitespace-nowrap">Instant</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Fastest refund option</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- Special Circumstances -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Special Circumstances</h2>
            <div class="space-y-4 text-gray-700">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">🏥 Medical Emergency</h3>
                <p class="text-sm">
                  Full refund available with valid medical certificate from registered hospital. 
                  Must be submitted within 48 hours of incident.
                </p>
              </div>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">💀 Death in Family</h3>
                <p class="text-sm">
                  Full refund with death certificate. Our condolences and full support during this time.
                </p>
              </div>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">🌍 Natural Disaster / Force Majeure</h3>
                <p class="text-sm">
                  Case-by-case evaluation. Government advisory or news proof required.
                </p>
              </div>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">🚫 Visa Ban / Travel Restriction</h3>
                <p class="text-sm">
                  Full refund if destination country implements sudden travel ban affecting your application.
                </p>
              </div>
            </div>
          </section>

          <!-- Contact -->
          <section class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-8 text-center">
            <EnvelopeIcon class="h-12 w-12 mx-auto text-blue-600 mb-4" />
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Need Help with Refund?</h2>
            <p class="text-gray-700 mb-6">
              Our support team is here to assist you with any refund-related questions.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
              <a 
                :href="`mailto:${companyEmail}`" 
                class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
              >
                Email Support
              </a>
              <a 
                :href="`tel:${companyPhone}`" 
                class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg border-2 border-blue-600 hover:bg-blue-50 transition"
              >
                Call Us
              </a>
            </div>
          </section>

          <!-- Footer Notice -->
          <section class="border-t pt-6">
            <p class="text-sm text-gray-500 text-center">
              BideshGomon reserves the right to update this Refund Policy at any time. 
              Changes will be effective immediately upon posting. Last updated: December 1, 2025.
            </p>
            <div class="flex justify-center gap-6 mt-4">
              <Link href="/legal/privacy-policy" class="text-sm text-blue-600 hover:underline">
                Privacy Policy
              </Link>
              <Link href="/legal/terms-of-service" class="text-sm text-blue-600 hover:underline">
                Terms of Service
              </Link>
              <Link href="/" class="text-sm text-blue-600 hover:underline">
                Back to Home
              </Link>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>


