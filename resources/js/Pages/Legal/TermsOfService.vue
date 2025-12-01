<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { 
  ScaleIcon,
  DocumentCheckIcon,
  ExclamationTriangleIcon,
  BanknotesIcon,
  ShieldExclamationIcon,
  NoSymbolIcon,
} from '@heroicons/vue/24/outline'

const companyName = 'BideshGomon'
const companyEmail = 'legal@bideshgomon.com'
</script>

<template>
  <div>
    <Head title="Terms of Service - BideshGomon" />

    <div class="min-h-screen bg-gray-50 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-8">
            <div class="flex items-center justify-center mb-4">
              <ScaleIcon class="h-12 w-12 text-white" />
            </div>
            <h1 class="text-3xl font-bold text-white text-center">Terms of Service</h1>
            <p class="text-purple-100 text-center mt-2">Last Updated: December 1, 2025</p>
          </div>

          <div class="px-6 py-4 bg-purple-50 border-b border-purple-100">
            <p class="text-sm text-purple-800 text-center">
              Please read these Terms carefully before using BideshGomon services. By using our platform, you agree to be bound by these Terms.
            </p>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow rounded-lg p-8 space-y-8">
          <!-- Introduction -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Agreement to Terms</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
              These Terms of Service ("Terms") constitute a legally binding agreement between you ("User", "you", or "your") 
              and {{ companyName }} ("we", "us", or "our") governing your access to and use of bideshgomon.com (the "Platform") 
              and our visa application, job placement, education consultancy, travel booking, and related services (collectively, the "Services").
            </p>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
              <p class="text-yellow-900 font-semibold mb-2">⚠️ Important Notice</p>
              <p class="text-yellow-800 text-sm">
                By accessing or using our Services, you acknowledge that you have read, understood, and agree to be bound by these Terms 
                and our <Link href="/legal/privacy-policy" class="underline">Privacy Policy</Link>. 
                If you do not agree, you must immediately discontinue use of the Platform.
              </p>
            </div>
          </section>

          <!-- Eligibility -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Eligibility</h2>
            <p class="text-gray-700 mb-4">To use our Services, you must:</p>
            <ul class="space-y-2 text-gray-700">
              <li class="flex items-start">
                <span class="text-purple-600 font-semibold mr-2">•</span>
                <span>Be at least <strong>18 years old</strong> or the age of majority in your jurisdiction</span>
              </li>
              <li class="flex items-start">
                <span class="text-purple-600 font-semibold mr-2">•</span>
                <span>Have the legal capacity to enter into binding contracts</span>
              </li>
              <li class="flex items-start">
                <span class="text-purple-600 font-semibold mr-2">•</span>
                <span>Provide accurate, current, and complete information during registration</span>
              </li>
              <li class="flex items-start">
                <span class="text-purple-600 font-semibold mr-2">•</span>
                <span>Not be prohibited from using our Services under applicable laws</span>
              </li>
              <li class="flex items-start">
                <span class="text-purple-600 font-semibold mr-2">•</span>
                <span>Not have a previously suspended or terminated account</span>
              </li>
            </ul>
          </section>

          <!-- Account Registration -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <DocumentCheckIcon class="h-6 w-6 mr-2 text-purple-600" />
              3. Account Registration & Security
            </h2>

            <div class="space-y-4">
              <div>
                <h3 class="font-semibold text-gray-900 mb-2">3.1 Account Creation</h3>
                <p class="text-gray-700">
                  You must create an account to access most Services. You agree to provide truthful information and maintain its accuracy. 
                  Creating accounts with false identities or on behalf of others without authorization is prohibited.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">3.2 Account Security</h3>
                <p class="text-gray-700 mb-2">You are responsible for:</p>
                <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                  <li>Maintaining the confidentiality of your password</li>
                  <li>All activities that occur under your account</li>
                  <li>Notifying us immediately of unauthorized access</li>
                  <li>Enabling two-factor authentication (recommended)</li>
                </ul>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">3.3 Account Termination</h3>
                <p class="text-gray-700">
                  We reserve the right to suspend or terminate accounts that violate these Terms, engage in fraudulent activities, 
                  or pose security risks. You may delete your account at any time through account settings.
                </p>
              </div>
            </div>
          </section>

          <!-- Services Description -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Services Provided</h2>
            <p class="text-gray-700 mb-4">{{ companyName }} offers the following services:</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h3 class="font-semibold text-purple-900 mb-2">🛂 Visa Application Services</h3>
                <p class="text-sm text-gray-700">
                  Application processing, document review, embassy submission assistance for tourist, work, study, and migration visas.
                </p>
              </div>

              <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h3 class="font-semibold text-purple-900 mb-2">💼 Job Placement Services</h3>
                <p class="text-sm text-gray-700">
                  International job matching, resume building, employer connections, interview preparation, work permit guidance.
                </p>
              </div>

              <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h3 class="font-semibold text-purple-900 mb-2">🎓 Education Consultancy</h3>
                <p class="text-sm text-gray-700">
                  University selection, application support, scholarship guidance, student visa assistance, admission processing.
                </p>
              </div>

              <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h3 class="font-semibold text-purple-900 mb-2">✈️ Travel Booking Services</h3>
                <p class="text-sm text-gray-700">
                  Flight reservations, hotel bookings, travel insurance, tour packages, itinerary planning.
                </p>
              </div>

              <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h3 class="font-semibold text-purple-900 mb-2">📄 CV Builder & Portfolio</h3>
                <p class="text-sm text-gray-700">
                  Professional resume creation, public profile pages, skill showcasing, certificate management.
                </p>
              </div>

              <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h3 class="font-semibold text-purple-900 mb-2">💰 Wallet & Referral System</h3>
                <p class="text-sm text-gray-700">
                  Digital wallet for payments, referral rewards, transaction history, cashback offers.
                </p>
              </div>
            </div>
          </section>

          <!-- User Obligations -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. User Obligations & Conduct</h2>
            
            <div class="space-y-4">
              <div>
                <h3 class="font-semibold text-gray-900 mb-2">5.1 Accurate Information</h3>
                <p class="text-gray-700">
                  You must provide accurate, complete, and truthful information in all applications and documents. 
                  Providing false information may result in visa rejection, legal consequences, and account termination.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">5.2 Timely Responses</h3>
                <p class="text-gray-700">
                  You must respond to our requests for additional information or documentation within specified timeframes. 
                  Delays may affect application processing and deadlines.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">5.3 Payment Obligations</h3>
                <p class="text-gray-700">
                  You agree to pay all fees for Services as displayed at the time of purchase. 
                  Prices are in Bangladeshi Taka (৳) unless otherwise specified. All fees are non-refundable except as outlined in our 
                  <Link href="/legal/refund-policy" class="text-purple-600 underline">Refund Policy</Link>.
                </p>
              </div>
            </div>
          </section>

          <!-- Prohibited Activities -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <NoSymbolIcon class="h-6 w-6 mr-2 text-red-600" />
              6. Prohibited Activities
            </h2>

            <div class="bg-red-50 border-l-4 border-red-500 p-6">
              <p class="text-red-900 font-semibold mb-3">You are strictly prohibited from:</p>
              <ul class="space-y-2 text-red-800">
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Submitting fraudulent documents, fake passports, or forged certificates</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Using the Platform for illegal activities, money laundering, or human trafficking</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Impersonating others or creating fake profiles</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Hacking, scraping, or attempting unauthorized access to systems</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Distributing malware, viruses, or malicious code</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Reverse engineering, decompiling, or disassembling the Platform</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Abusing referral programs through fake accounts or self-referrals</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Harassing staff, threatening consultants, or abusive communication</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Posting defamatory, obscene, or offensive content</span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">❌</span>
                  <span>Violating intellectual property rights or copyrights</span>
                </li>
              </ul>

              <p class="text-red-900 font-semibold mt-4">
                ⚠️ Violation of these prohibitions may result in immediate account termination, legal action, and reporting to authorities.
              </p>
            </div>
          </section>

          <!-- Intellectual Property -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Intellectual Property Rights</h2>

            <div class="space-y-4">
              <div>
                <h3 class="font-semibold text-gray-900 mb-2">7.1 Our Ownership</h3>
                <p class="text-gray-700">
                  All content, features, functionality, trademarks, logos, and designs on the Platform are owned by {{ companyName }} 
                  and protected by international copyright, trademark, and intellectual property laws. Unauthorized use is prohibited.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">7.2 User Content License</h3>
                <p class="text-gray-700">
                  By uploading documents, CVs, or other content, you grant us a non-exclusive, worldwide, royalty-free license to use, 
                  reproduce, and process such content solely for providing Services (e.g., submitting applications to embassies).
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">7.3 Third-Party Content</h3>
                <p class="text-gray-700">
                  Job listings, university information, and travel offers may be provided by third parties. 
                  We do not claim ownership and are not responsible for accuracy or availability.
                </p>
              </div>
            </div>
          </section>

          <!-- Payment Terms -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <BanknotesIcon class="h-6 w-6 mr-2 text-purple-600" />
              8. Payment Terms
            </h2>

            <div class="space-y-4">
              <div>
                <h3 class="font-semibold text-gray-900 mb-2">8.1 Pricing</h3>
                <p class="text-gray-700">
                  Service fees are displayed in Bangladeshi Taka (৳) and include applicable taxes. 
                  We reserve the right to change prices with 7 days' notice.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">8.2 Payment Methods</h3>
                <p class="text-gray-700 mb-2">We accept:</p>
                <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                  <li>bKash, Nagad, Rocket (mobile banking)</li>
                  <li>Credit/Debit Cards (Visa, Mastercard) via SSLCommerz</li>
                  <li>Bank Transfer</li>
                  <li>BideshGomon Wallet balance</li>
                </ul>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">8.3 Third-Party Fees</h3>
                <p class="text-gray-700">
                  Embassy fees, government charges, courier costs, and other third-party expenses are separate and non-refundable. 
                  These are clearly itemized during checkout.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">8.4 Refunds</h3>
                <p class="text-gray-700">
                  Refund eligibility is governed by our <Link href="/legal/refund-policy" class="text-purple-600 underline">Refund Policy</Link>. 
                  Service fees may be non-refundable after work has commenced.
                </p>
              </div>
            </div>
          </section>

          <!-- Disclaimers -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <ExclamationTriangleIcon class="h-6 w-6 mr-2 text-yellow-600" />
              9. Disclaimers & Limitations
            </h2>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 space-y-4">
              <div>
                <h3 class="font-semibold text-yellow-900 mb-2">9.1 No Visa Guarantee</h3>
                <p class="text-yellow-800 text-sm">
                  <strong>IMPORTANT:</strong> We provide application assistance services only. We DO NOT guarantee visa approval, 
                  job placement, university admission, or travel outcomes. Embassy/immigration decisions are beyond our control.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-yellow-900 mb-2">9.2 "AS IS" Service</h3>
                <p class="text-yellow-800 text-sm">
                  Services are provided "as is" without warranties of any kind, express or implied, including merchantability, 
                  fitness for a particular purpose, or non-infringement.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-yellow-900 mb-2">9.3 Third-Party Services</h3>
                <p class="text-yellow-800 text-sm">
                  We are not responsible for actions, errors, or omissions of third parties (embassies, airlines, hotels, employers, universities). 
                  Claims must be directed to the respective entities.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-yellow-900 mb-2">9.4 Force Majeure</h3>
                <p class="text-yellow-800 text-sm">
                  We are not liable for delays or failures caused by circumstances beyond reasonable control (natural disasters, 
                  pandemics, war, government actions, strikes, internet outages).
                </p>
              </div>
            </div>
          </section>

          <!-- Limitation of Liability -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <ShieldExclamationIcon class="h-6 w-6 mr-2 text-red-600" />
              10. Limitation of Liability
            </h2>

            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
              <p class="text-red-900 font-semibold mb-3">
                TO THE MAXIMUM EXTENT PERMITTED BY LAW:
              </p>
              <ul class="space-y-2 text-red-800 text-sm">
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>
                    {{ companyName }} shall not be liable for indirect, incidental, special, consequential, or punitive damages 
                    including loss of profits, data, use, goodwill, or other intangible losses.
                  </span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>
                    Our total liability for any claim shall not exceed the amount you paid us for the specific Service in question.
                  </span>
                </li>
                <li class="flex items-start">
                  <span class="mr-2">•</span>
                  <span>
                    We are not liable for visa rejections, job placement failures, admission denials, travel disruptions, 
                    or third-party errors.
                  </span>
                </li>
              </ul>
            </div>
          </section>

          <!-- Indemnification -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Indemnification</h2>
            <p class="text-gray-700">
              You agree to indemnify, defend, and hold harmless {{ companyName }}, its affiliates, officers, directors, employees, 
              and agents from any claims, liabilities, damages, losses, costs, or expenses (including legal fees) arising from:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-1 mt-3 ml-4">
              <li>Your violation of these Terms</li>
              <li>Your violation of any laws or regulations</li>
              <li>Your infringement of third-party rights</li>
              <li>Fraudulent or misleading information you provide</li>
              <li>Unauthorized use of your account</li>
            </ul>
          </section>

          <!-- Dispute Resolution -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Dispute Resolution</h2>

            <div class="space-y-4">
              <div>
                <h3 class="font-semibold text-gray-900 mb-2">12.1 Informal Resolution</h3>
                <p class="text-gray-700">
                  Before initiating legal action, you agree to contact us at {{ companyEmail }} to resolve disputes amicably. 
                  Most concerns can be resolved through good-faith negotiation.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">12.2 Governing Law</h3>
                <p class="text-gray-700">
                  These Terms shall be governed by and construed in accordance with the laws of Bangladesh, 
                  without regard to conflict of law principles.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">12.3 Jurisdiction</h3>
                <p class="text-gray-700">
                  Any legal proceedings arising from these Terms or the Services shall be brought exclusively in the courts of Dhaka, Bangladesh. 
                  You consent to the personal jurisdiction of such courts.
                </p>
              </div>

              <div>
                <h3 class="font-semibold text-gray-900 mb-2">12.4 Arbitration (Optional)</h3>
                <p class="text-gray-700">
                  By mutual agreement, disputes may be resolved through binding arbitration in accordance with Bangladesh Arbitration Act, 2001.
                </p>
              </div>
            </div>
          </section>

          <!-- Termination -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">13. Termination</h2>

            <div class="space-y-3">
              <p class="text-gray-700">
                <strong>By You:</strong> You may terminate your account at any time by contacting support or using account deletion settings. 
                Paid services remain non-refundable unless covered by our Refund Policy.
              </p>

              <p class="text-gray-700">
                <strong>By Us:</strong> We may suspend or terminate your account immediately without notice if you:
              </p>
              <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                <li>Violate these Terms</li>
                <li>Engage in fraudulent activities</li>
                <li>Pose security or legal risks</li>
                <li>Fail to pay fees owed</li>
                <li>Repeatedly abuse support staff</li>
              </ul>

              <p class="text-gray-700">
                <strong>Effect of Termination:</strong> Upon termination, your right to use Services ceases immediately. 
                We may delete your data per retention policies. Outstanding fees remain payable.
              </p>
            </div>
          </section>

          <!-- Modifications -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">14. Modifications to Terms</h2>
            <p class="text-gray-700 mb-3">
              We reserve the right to modify these Terms at any time. Material changes will be notified via:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
              <li>Email to registered users</li>
              <li>Prominent banner on the Platform</li>
              <li>Updated "Last Modified" date at the top of this page</li>
            </ul>
            <p class="text-gray-700 mt-3">
              Continued use of Services after changes constitutes acceptance. If you disagree, you must stop using the Platform.
            </p>
          </section>

          <!-- Severability -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">15. Severability & Entire Agreement</h2>
            <p class="text-gray-700 mb-3">
              <strong>Severability:</strong> If any provision of these Terms is found invalid or unenforceable, 
              the remaining provisions shall continue in full force.
            </p>
            <p class="text-gray-700">
              <strong>Entire Agreement:</strong> These Terms, together with our Privacy Policy and Refund Policy, 
              constitute the entire agreement between you and {{ companyName }} regarding the Services and supersede all prior agreements.
            </p>
          </section>

          <!-- Contact -->
          <section class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-8 text-center">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Questions About These Terms?</h2>
            <p class="text-gray-700 mb-6">
              Our legal team is available to clarify any provisions or address concerns.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
              <a 
                :href="`mailto:${companyEmail}`" 
                class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition"
              >
                Contact Legal Team
              </a>
              <Link 
                href="/" 
                class="inline-flex items-center justify-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg border-2 border-purple-600 hover:bg-purple-50 transition"
              >
                Back to Home
              </Link>
            </div>
          </section>

          <!-- Footer Links -->
          <section class="border-t pt-6">
            <p class="text-sm text-gray-500 text-center mb-4">
              By using BideshGomon, you acknowledge that you have read, understood, and agreed to these Terms of Service.
            </p>
            <div class="flex justify-center gap-6">
              <Link href="/legal/privacy-policy" class="text-sm text-purple-600 hover:underline">
                Privacy Policy
              </Link>
              <Link href="/legal/refund-policy" class="text-sm text-purple-600 hover:underline">
                Refund Policy
              </Link>
              <Link href="/" class="text-sm text-purple-600 hover:underline">
                Home
              </Link>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>
