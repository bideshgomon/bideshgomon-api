<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import {
  ArrowLeftIcon,
  ClockIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  InformationCircleIcon,
  DocumentTextIcon,
  XMarkIcon,
  BriefcaseIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  service: Object,
  user: Object,
});

const showRequirementsModal = ref(false);
const selectedCountry = ref('');
const selectedProfession = ref('');
const showApplicationForm = ref(false);

// Popular countries for visa
const countries = [
  { code: 'US', name: 'United States', flag: '🇺🇸', processingTime: '15-30 days', basePrice: 16000 },
  { code: 'UK', name: 'United Kingdom', flag: '🇬🇧', processingTime: '15-21 days', basePrice: 13000 },
  { code: 'CA', name: 'Canada', flag: '🇨🇦', processingTime: '20-30 days', basePrice: 12000 },
  { code: 'AU', name: 'Australia', flag: '🇦🇺', processingTime: '15-25 days', basePrice: 14000 },
  { code: 'SG', name: 'Singapore', flag: '🇸🇬', processingTime: '3-5 days', basePrice: 3500 },
  { code: 'MY', name: 'Malaysia', flag: '🇲🇾', processingTime: '3-7 days', basePrice: 2500 },
  { code: 'TH', name: 'Thailand', flag: '🇹🇭', processingTime: '3-5 days', basePrice: 3000 },
  { code: 'AE', name: 'UAE', flag: '🇦🇪', processingTime: '3-5 days', basePrice: 8000 },
  { code: 'SA', name: 'Saudi Arabia', flag: '🇸🇦', processingTime: '7-15 days', basePrice: 7000 },
  { code: 'IN', name: 'India', flag: '🇮🇳', processingTime: '3-7 days', basePrice: 2000 },
];

const professions = [
  'Student', 'Engineer', 'Doctor', 'Teacher', 'Business Owner',
  'IT Professional', 'Accountant', 'Manager', 'Consultant',
  'Freelancer', 'Government Employee', 'Private Employee', 'Other'
];

const form = useForm({
  service_module_id: props.service.id,
  destination_country: '',
  profession: '',
  purpose: 'Tourism',
  travel_dates: {
    departure: '',
    return: '',
  },
  notes: '',
});

const selectedCountryData = computed(() => {
  return countries.find(c => c.code === selectedCountry.value);
});

const requirementsByCountry = computed(() => {
  const common = [
    'Valid passport (minimum 6 months validity)',
    'Recent passport-size photographs',
    'Bank statement (last 6 months)',
    'Employment letter or business documents',
    'Travel itinerary',
    'Hotel booking confirmation',
    'Return flight ticket',
  ];

  if (selectedCountry.value === 'US') {
    return [...common, 'DS-160 form', 'Visa interview appointment'];
  } else if (selectedCountry.value === 'UK') {
    return [...common, 'TB test certificate', 'Travel insurance'];
  } else if (selectedCountry.value === 'CA') {
    return [...common, 'Biometrics appointment', 'Travel insurance'];
  }
  return common;
});

const documentsByProfession = computed(() => {
  const common = ['National ID Card', 'Passport copy'];

  if (selectedProfession.value === 'Student') {
    return [...common, 'Student ID', 'University enrollment letter', 'Academic transcript'];
  } else if (selectedProfession.value === 'Business Owner') {
    return [...common, 'Trade license', 'Tax return documents', 'Company bank statement'];
  } else if (['Engineer', 'Doctor', 'Teacher', 'IT Professional'].includes(selectedProfession.value)) {
    return [...common, 'Professional certificate', 'Employment letter', 'Salary slip'];
  }
  return [...common, 'Employment letter', 'Salary slip'];
});

const handleCountrySelect = (country) => {
  selectedCountry.value = country.code;
  form.destination_country = country.name;
};

const handleProfessionSelect = (profession) => {
  selectedProfession.value = profession;
  form.profession = profession;
};

const viewRequirements = () => {
  if (!selectedCountry.value || !selectedProfession.value) {
    alert('Please select destination country and profession first');
    return;
  }
  showRequirementsModal.value = true;
};

const proceedToApplication = () => {
  showRequirementsModal.value = false;
  showApplicationForm.value = true;
};

const submitApplication = () => {
  form.post(route('user.applications.store'), {
    onSuccess: () => {
      showApplicationForm.value = false;
      form.reset();
    },
  });
};

const formatPrice = computed(() => {
  if (selectedCountryData.value) {
    return `BDT ${selectedCountryData.value.basePrice.toLocaleString()}`;
  }
  const price = parseFloat(props.service.base_price || 0);
  return `${props.service.currency} ${price.toLocaleString()}`;
});

const processingTime = computed(() => {
  if (selectedCountryData.value) {
    return selectedCountryData.value.processingTime;
  }
  if (!props.service.processing_time) return 'Contact us';
  const { min, max, unit } = props.service.processing_time;
  if (min === max) return `${min} ${unit}`;
  return `${min}-${max} ${unit}`;
});
</script>

<template>
  <Head :title="`${service.name} - Application Form`" />

  <AuthenticatedLayout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <Link
          :href="route('services.index')"
          class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6 transition-colors"
        >
          <ArrowLeftIcon class="h-5 w-5" />
          Back to Services
        </Link>

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl p-8 mb-8 text-white">
          <div class="flex items-center gap-4 mb-4">
            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
              <span class="text-4xl">🛂</span>
            </div>
            <div>
              <h1 class="text-3xl md:text-4xl font-bold">
                {{ service.name }} Application
              </h1>
              <p class="text-blue-100 mt-2">
                {{ service.short_description }}
              </p>
            </div>
          </div>

          <div class="flex flex-wrap gap-4 mt-6">
            <div class="flex items-center gap-2 bg-white/10 rounded-lg px-4 py-2 backdrop-blur-sm">
              <ClockIcon class="h-5 w-5" />
              <span class="text-sm">Processing: {{ processingTime }}</span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 rounded-lg px-4 py-2 backdrop-blur-sm">
              <CurrencyDollarIcon class="h-5 w-5" />
              <span class="text-sm">Starting from: {{ formatPrice }}</span>
            </div>
          </div>
        </div>

        <!-- Step-by-Step Form -->
        <div class="max-w-4xl mx-auto">
          <!-- Step 1: Select Destination Country -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">
                1
              </div>
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                  Select Destination Country
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                  Choose the country you wish to visit
                </p>
              </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
              <button
                v-for="country in countries"
                :key="country.code"
                @click="handleCountrySelect(country)"
                :class="[
                  'p-4 rounded-xl border-2 transition-all hover:scale-105',
                  selectedCountry === country.code
                    ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 shadow-lg'
                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-400'
                ]"
              >
                <div class="text-4xl mb-2">{{ country.flag }}</div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                  {{ country.name }}
                </div>
                <div v-if="selectedCountry === country.code" class="mt-2">
                  <CheckCircleIcon class="h-5 w-5 text-blue-600 mx-auto" />
                </div>
              </button>
            </div>

            <!-- Selected Country Details -->
            <div
              v-if="selectedCountryData"
              class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl"
            >
              <div class="flex items-start gap-4">
                <InformationCircleIcon class="h-6 w-6 text-blue-600 flex-shrink-0 mt-1" />
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                    {{ selectedCountryData.name }} Visa Details
                  </h3>
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600 dark:text-gray-400">Processing Time:</span>
                      <span class="font-medium text-gray-900 dark:text-white ml-2">
                        {{ selectedCountryData.processingTime }}
                      </span>
                    </div>
                    <div>
                      <span class="text-gray-600 dark:text-gray-400">Estimated Cost:</span>
                      <span class="font-medium text-gray-900 dark:text-white ml-2">
                        BDT {{ selectedCountryData.basePrice.toLocaleString() }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Select Profession -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">
                2
              </div>
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                  Your Profession
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                  This helps us determine required documents
                </p>
              </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              <button
                v-for="profession in professions"
                :key="profession"
                @click="handleProfessionSelect(profession)"
                :class="[
                  'p-4 rounded-lg border-2 transition-all text-sm font-medium',
                  selectedProfession === profession
                    ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                    : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-blue-400'
                ]"
              >
                {{ profession }}
              </button>
            </div>
          </div>

          <!-- View Requirements Button -->
          <div
            v-if="selectedCountry && selectedProfession"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mb-6"
          >
            <div class="text-center">
              <button
                @click="viewRequirements"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-4 px-8 rounded-xl transition-all transform hover:scale-105 shadow-lg"
              >
                <DocumentTextIcon class="h-6 w-6" />
                View Requirements & Fees
              </button>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                See complete requirements and estimated fees for your application
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Requirements Modal -->
    <Modal :show="showRequirementsModal" @close="showRequirementsModal = false" max-width="4xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Requirements for {{ selectedCountryData?.name }} Tourist Visa
          </h2>
          <button
            @click="showRequirementsModal = false"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- General Requirements -->
          <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <CheckCircleIcon class="h-5 w-5 text-blue-600" />
              General Requirements
            </h3>
            <ul class="space-y-3">
              <li
                v-for="(req, index) in requirementsByCountry"
                :key="index"
                class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300"
              >
                <CheckCircleIcon class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
                <span>{{ req }}</span>
              </li>
            </ul>
          </div>

          <!-- Profession-Specific Documents -->
          <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <BriefcaseIcon class="h-5 w-5 text-purple-600" />
              Required for {{ selectedProfession }}
            </h3>
            <ul class="space-y-3">
              <li
                v-for="(doc, index) in documentsByProfession"
                :key="index"
                class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300"
              >
                <DocumentTextIcon class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" />
                <span>{{ doc }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <CurrencyDollarIcon class="h-5 w-5 text-green-600" />
            Estimated Cost Breakdown
          </h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Visa Fee</span>
              <span class="font-semibold text-gray-900 dark:text-white">
                BDT {{ selectedCountryData ? (selectedCountryData.basePrice * 0.6).toLocaleString() : '0' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Service Charge</span>
              <span class="font-semibold text-gray-900 dark:text-white">
                BDT {{ selectedCountryData ? (selectedCountryData.basePrice * 0.3).toLocaleString() : '0' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Processing Fee</span>
              <span class="font-semibold text-gray-900 dark:text-white">
                BDT {{ selectedCountryData ? (selectedCountryData.basePrice * 0.1).toLocaleString() : '0' }}
              </span>
            </div>
            <div class="pt-3 border-t border-green-200 dark:border-green-700 flex justify-between">
              <span class="font-bold text-gray-900 dark:text-white">Total Estimated Cost</span>
              <span class="font-bold text-green-600 text-lg">
                BDT {{ selectedCountryData?.basePrice.toLocaleString() }}
              </span>
            </div>
          </div>
        </div>

        <div class="flex gap-3">
          <button
            @click="showRequirementsModal = false"
            type="button"
            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
          >
            Back
          </button>
          <button
            @click="proceedToApplication"
            class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition-all"
          >
            Proceed to Application
          </button>
        </div>
      </div>
    </Modal>

    <!-- Application Form Modal -->
    <Modal :show="showApplicationForm" @close="showApplicationForm = false" max-width="3xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Complete Your Application
          </h2>
          <button
            @click="showApplicationForm = false"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="submitApplication" class="space-y-6">
          <!-- Travel Dates -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Departure Date
              </label>
              <input
                v-model="form.travel_dates.departure"
                type="date"
                required
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Return Date
              </label>
              <input
                v-model="form.travel_dates.return"
                type="date"
                required
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
          </div>

          <!-- Purpose -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Purpose of Travel
            </label>
            <select
              v-model="form.purpose"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="Tourism">Tourism</option>
              <option value="Business">Business</option>
              <option value="Medical">Medical</option>
              <option value="Family Visit">Family Visit</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <!-- Additional Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Additional Information (Optional)
            </label>
            <textarea
              v-model="form.notes"
              rows="4"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              placeholder="Any additional details or special requests..."
            ></textarea>
          </div>

          <!-- Summary -->
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Destination:</span>
              <span class="font-medium text-gray-900 dark:text-white">{{ form.destination_country }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Profession:</span>
              <span class="font-medium text-gray-900 dark:text-white">{{ form.profession }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Estimated Cost:</span>
              <span class="font-medium text-gray-900 dark:text-white">{{ formatPrice }}</span>
            </div>
          </div>

          <div class="flex gap-3">
            <button
              @click="showApplicationForm = false"
              type="button"
              class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="form.processing">Submitting...</span>
              <span v-else">Submit Application</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
