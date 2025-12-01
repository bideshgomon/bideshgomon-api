<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { 
  LockClosedIcon,
  ShieldCheckIcon,
  EyeIcon,
  ServerIcon,
  GlobeAltIcon,
  UserCircleIcon,
} from '@heroicons/vue/24/outline'

const companyEmail = 'privacy@bideshgomon.com'
const companyAddress = 'Dhaka, Bangladesh'
</script>

<template>
  <div>
    <Head title="Privacy Policy - BideshGomon" />

    <div class="min-h-screen bg-gray-50 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
          <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-8">
            <div class="flex items-center justify-center mb-4">
              <LockClosedIcon class="h-12 w-12 text-white" />
            </div>
            <h1 class="text-3xl font-bold text-white text-center">Privacy Policy</h1>
            <p class="text-green-100 text-center mt-2">Last Updated: December 1, 2025</p>
          </div>

          <div class="px-6 py-4 bg-green-50 border-b border-green-100">
            <p class="text-sm text-green-800 text-center">
              Your privacy is critically important to us. This policy explains how we collect, use, and protect your personal data.
            </p>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow rounded-lg p-8 space-y-8">
          <!-- Introduction -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Introduction</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
              BideshGomon ("we", "our", or "us") operates bideshgomon.com and provides visa application, job placement, 
              education consultancy, and travel services to users in Bangladesh and internationally. This Privacy Policy 
              governs the collection, use, disclosure, and protection of your personal information.
            </p>
            <p class="text-gray-700 leading-relaxed">
              By using our services, you agree to the collection and use of information in accordance with this policy. 
              If you do not agree, please discontinue use of our platform.
            </p>
          </section>

          <!-- Information We Collect -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <UserCircleIcon class="h-6 w-6 mr-2 text-green-600" />
              Information We Collect
            </h2>

            <div class="space-y-6">
              <!-- Personal Information -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">1. Personal Identification Information</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Account Information:</strong> Name, email, phone number, date of birth, NID/passport number</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Profile Details:</strong> Address, education history, work experience, language proficiency</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Documents:</strong> Passport scans, academic certificates, employment letters, bank statements</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Financial Information:</strong> Payment card details (encrypted), bKash/Nagad numbers, bank account info</span>
                  </li>
                </ul>
              </div>

              <!-- Usage Data -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">2. Usage & Technical Data</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Device Information:</strong> IP address, browser type, device ID, operating system</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Activity Logs:</strong> Pages visited, time spent, clicks, search queries</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Cookies:</strong> Session cookies, authentication tokens, preference settings</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Location Data:</strong> Country, city (from IP), GPS coordinates (if permitted)</span>
                  </li>
                </ul>
              </div>

              <!-- Third-Party Data -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">3. Third-Party Integrations</h3>
                <ul class="space-y-2 text-gray-700">
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Google OAuth:</strong> Name, email, profile picture (when you sign in with Google)</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Payment Gateways:</strong> Transaction data from SSLCommerz, bKash, Nagad</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-green-600 mr-2">•</span>
                    <span><strong>Analytics:</strong> Google Analytics, Facebook Pixel (for marketing insights)</span>
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <!-- How We Use Information -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <EyeIcon class="h-6 w-6 mr-2 text-green-600" />
              How We Use Your Information
            </h2>

            <div class="bg-gray-50 rounded-lg p-6">
              <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">1</span>
                  <span><strong>Service Delivery:</strong> Process visa applications, job placements, education consultancy, travel bookings</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">2</span>
                  <span><strong>Communication:</strong> Send application updates, payment confirmations, appointment reminders, support responses</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">3</span>
                  <span><strong>Account Management:</strong> Authenticate logins, verify identities, manage subscriptions</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">4</span>
                  <span><strong>Payment Processing:</strong> Handle transactions, refunds, wallet credits through secure gateways</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">5</span>
                  <span><strong>Personalization:</strong> Recommend relevant services, display targeted content, save preferences</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">6</span>
                  <span><strong>Marketing:</strong> Send newsletters, promotional offers, referral programs (you can opt-out anytime)</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">7</span>
                  <span><strong>Analytics & Improvement:</strong> Analyze usage patterns, fix bugs, improve user experience</span>
                </li>
                <li class="flex items-start">
                  <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-semibold mr-3">8</span>
                  <span><strong>Legal Compliance:</strong> Comply with visa regulations, tax laws, anti-fraud measures, court orders</span>
                </li>
              </ul>
            </div>
          </section>

          <!-- Data Sharing -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <GlobeAltIcon class="h-6 w-6 mr-2 text-green-600" />
              When We Share Your Data
            </h2>

            <div class="space-y-4">
              <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                <h3 class="font-semibold text-blue-900 mb-2">✓ Authorized Third Parties</h3>
                <ul class="list-disc list-inside text-blue-800 space-y-1 ml-4 text-sm">
                  <li><strong>Embassies & Government Bodies:</strong> Share visa applications, supporting documents (required by law)</li>
                  <li><strong>Payment Processors:</strong> SSLCommerz, bKash, Nagad (encrypted transaction data only)</li>
                  <li><strong>Partner Agencies:</strong> Job recruiters, universities, travel agencies (with your consent)</li>
                  <li><strong>Service Providers:</strong> Cloud hosting (AWS/DigitalOcean), email services (Mailgun), analytics (Google)</li>
                </ul>
              </div>

              <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <h3 class="font-semibold text-red-900 mb-2">✗ We NEVER Share</h3>
                <ul class="list-disc list-inside text-red-800 space-y-1 ml-4 text-sm">
                  <li>Sell your personal data to third parties</li>
                  <li>Share data with unauthorized advertisers</li>
                  <li>Disclose sensitive documents (passport, bank statements) without legal obligation</li>
                  <li>Use your data for purposes beyond this policy without explicit consent</li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Data Security -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <ShieldCheckIcon class="h-6 w-6 mr-2 text-green-600" />
              Data Security Measures
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">🔒 Encryption</h3>
                <p class="text-sm text-gray-700">SSL/TLS encryption for all data transmission. AES-256 encryption for stored documents.</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">🛡️ Access Control</h3>
                <p class="text-sm text-gray-700">Role-based permissions. Only authorized staff can access sensitive data.</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">🔐 Authentication</h3>
                <p class="text-sm text-gray-700">Secure password hashing (bcrypt). Two-factor authentication (2FA) available.</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">🚨 Monitoring</h3>
                <p class="text-sm text-gray-700">24/7 security monitoring. Regular security audits and vulnerability scans.</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">💾 Backups</h3>
                <p class="text-sm text-gray-700">Daily encrypted backups. Disaster recovery protocols in place.</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2">🗑️ Data Deletion</h3>
                <p class="text-sm text-gray-700">Secure deletion protocols. Data shredding for document destruction.</p>
              </div>
            </div>
          </section>

          <!-- Your Rights -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your Privacy Rights</h2>

            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6">
              <div class="space-y-4 text-gray-800">
                <div>
                  <h3 class="font-semibold text-green-900 mb-1">📄 Right to Access</h3>
                  <p class="text-sm">Request a copy of all personal data we hold about you (free of charge).</p>
                </div>

                <div>
                  <h3 class="font-semibold text-green-900 mb-1">✏️ Right to Rectification</h3>
                  <p class="text-sm">Correct inaccurate or incomplete information in your profile.</p>
                </div>

                <div>
                  <h3 class="font-semibold text-green-900 mb-1">🗑️ Right to Erasure ("Right to Be Forgotten")</h3>
                  <p class="text-sm">Request deletion of your account and data (subject to legal retention requirements).</p>
                </div>

                <div>
                  <h3 class="font-semibold text-green-900 mb-1">⏸️ Right to Restrict Processing</h3>
                  <p class="text-sm">Limit how we use your data while disputes are resolved.</p>
                </div>

                <div>
                  <h3 class="font-semibold text-green-900 mb-1">📤 Right to Data Portability</h3>
                  <p class="text-sm">Receive your data in machine-readable format (JSON/CSV) for transfer to another service.</p>
                </div>

                <div>
                  <h3 class="font-semibold text-green-900 mb-1">🚫 Right to Object</h3>
                  <p class="text-sm">Opt-out of marketing emails, profiling, or automated decision-making.</p>
                </div>

                <div>
                  <h3 class="font-semibold text-green-900 mb-1">🔔 Right to Withdraw Consent</h3>
                  <p class="text-sm">Revoke consent for data processing at any time (may affect service availability).</p>
                </div>
              </div>

              <div class="mt-6 pt-6 border-t border-green-200 text-center">
                <p class="text-sm text-gray-700 mb-3">To exercise any of these rights, contact us:</p>
                <a 
                  :href="`mailto:${companyEmail}`" 
                  class="inline-flex items-center px-5 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
                >
                  Email Privacy Team
                </a>
              </div>
            </div>
          </section>

          <!-- Cookies -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Cookie Policy</h2>
            <p class="text-gray-700 mb-4">
              We use cookies and similar tracking technologies to improve your experience. You can control cookies through your browser settings.
            </p>

            <div class="space-y-3">
              <div class="flex items-start">
                <span class="font-semibold text-gray-900 min-w-[180px]">Essential Cookies:</span>
                <span class="text-gray-700">Required for login, security, CSRF protection (cannot be disabled)</span>
              </div>
              <div class="flex items-start">
                <span class="font-semibold text-gray-900 min-w-[180px]">Analytics Cookies:</span>
                <span class="text-gray-700">Google Analytics (track page views, user behavior) - can be disabled</span>
              </div>
              <div class="flex items-start">
                <span class="font-semibold text-gray-900 min-w-[180px]">Marketing Cookies:</span>
                <span class="text-gray-700">Facebook Pixel, Google Ads (retargeting) - can be disabled</span>
              </div>
              <div class="flex items-start">
                <span class="font-semibold text-gray-900 min-w-[180px]">Preference Cookies:</span>
                <span class="text-gray-700">Remember language, currency, theme settings</span>
              </div>
            </div>
          </section>

          <!-- Data Retention -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
              <ServerIcon class="h-6 w-6 mr-2 text-green-600" />
              Data Retention
            </h2>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Retention Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Account Information</td>
                    <td class="px-6 py-4 whitespace-nowrap">Until account deletion</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Service provision</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Visa Applications</td>
                    <td class="px-6 py-4 whitespace-nowrap">7 years</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Legal requirement</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Payment Records</td>
                    <td class="px-6 py-4 whitespace-nowrap">5 years</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Tax compliance</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Support Tickets</td>
                    <td class="px-6 py-4 whitespace-nowrap">3 years</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Service improvement</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Marketing Consents</td>
                    <td class="px-6 py-4 whitespace-nowrap">Until withdrawal</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Consent-based</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">Analytics Logs</td>
                    <td class="px-6 py-4 whitespace-nowrap">26 months</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Google Analytics policy</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- Children's Privacy -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Children's Privacy</h2>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
              <p class="text-yellow-900 font-semibold mb-2">⚠️ Age Requirement: 18+</p>
              <p class="text-yellow-800 text-sm">
                BideshGomon services are intended for adults (18 years or older). We do not knowingly collect data from minors. 
                If you believe a child has provided information, contact us immediately for deletion.
              </p>
            </div>
          </section>

          <!-- International Transfers -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">International Data Transfers</h2>
            <p class="text-gray-700 mb-4">
              Your data may be transferred to and processed in countries outside Bangladesh (e.g., USA for cloud hosting). 
              We ensure adequate safeguards through:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
              <li>Standard Contractual Clauses (SCCs) approved by EU</li>
              <li>Privacy Shield-equivalent mechanisms</li>
              <li>Encryption during transit and at rest</li>
              <li>Data Processing Agreements (DPAs) with vendors</li>
            </ul>
          </section>

          <!-- Changes to Policy -->
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Changes to This Policy</h2>
            <p class="text-gray-700">
              We may update this Privacy Policy periodically to reflect changes in practices or legal requirements. 
              Significant changes will be notified via:
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-1 mt-3 ml-4">
              <li>Email notification to registered users</li>
              <li>Prominent banner on website</li>
              <li>Updated "Last Modified" date at top of this page</li>
            </ul>
            <p class="text-gray-700 mt-3">
              Continued use of services after changes constitutes acceptance of the updated policy.
            </p>
          </section>

          <!-- Contact -->
          <section class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Contact Our Privacy Team</h2>
            <p class="text-gray-700 text-center mb-6">
              Questions, concerns, or data requests? We're here to help.
            </p>
            
            <div class="space-y-3 max-w-md mx-auto">
              <div class="flex items-center justify-between bg-white rounded-lg p-4 border border-green-200">
                <span class="font-semibold text-gray-900">Email:</span>
                <a :href="`mailto:${companyEmail}`" class="text-green-600 hover:underline">{{ companyEmail }}</a>
              </div>
              
              <div class="flex items-center justify-between bg-white rounded-lg p-4 border border-green-200">
                <span class="font-semibold text-gray-900">Address:</span>
                <span class="text-gray-700">{{ companyAddress }}</span>
              </div>
            </div>

            <div class="text-center mt-6">
              <a 
                :href="`mailto:${companyEmail}?subject=Privacy Inquiry`" 
                class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
              >
                Send Privacy Inquiry
              </a>
            </div>
          </section>

          <!-- Footer Links -->
          <section class="border-t pt-6">
            <p class="text-sm text-gray-500 text-center mb-4">
              This Privacy Policy is governed by the laws of Bangladesh and complies with international data protection standards 
              including GDPR principles.
            </p>
            <div class="flex justify-center gap-6">
              <Link href="/legal/terms-of-service" class="text-sm text-green-600 hover:underline">
                Terms of Service
              </Link>
              <Link href="/legal/refund-policy" class="text-sm text-green-600 hover:underline">
                Refund Policy
              </Link>
              <Link href="/" class="text-sm text-green-600 hover:underline">
                Back to Home
              </Link>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>
