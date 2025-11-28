<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DocumentUpload from '@/Components/DocumentUpload.vue';
import {
  ArrowLeftIcon,
  ClockIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  InformationCircleIcon,
  DocumentTextIcon,
  XMarkIcon,
  BriefcaseIcon,
  ArrowUpTrayIcon,
  GlobeAsiaAustraliaIcon,
  UserIcon,
  CalendarIcon,
  ArrowRightIcon,
  ShieldCheckIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  service: Object,
  user: Object,
  countries: {
    type: Array,
    default: () => []
  },
  professions: {
    type: Array,
    default: () => []
  },
});

const currentStep = ref(1);
const showRequirementsModal = ref(false);
const selectedCountry = ref('');
const selectedProfession = ref('');
const showApplicationForm = ref(false);
const loadingRequirements = ref(false);
const visaRequirements = ref(null);
const visaFees = ref(null);

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
  documents: [],
});

const uploadedDocuments = ref([]);

const selectedCountryData = computed(() => {
  return props.countries.find(c => c.id === selectedCountry.value);
});

const selectedProfessionData = computed(() => {
  return props.professions.find(p => p === selectedProfession.value);
});

// Watch for country and profession changes to fetch requirements
watch([selectedCountry, selectedProfession], async ([country, profession]) => {
  if (country && profession) {
    await fetchVisaRequirements(country, profession);
  }
});

const fetchVisaRequirements = async (countryId, profession) => {
  loadingRequirements.value = true;
  try {
    const response = await axios.get(route('visa.requirements', {
      country_id: countryId,
      profession: profession
    }));
    visaRequirements.value = response.data.requirements;
    visaFees.value = response.data.fees;
  } catch (error) {
    console.error('Error fetching visa requirements:', error);
  } finally {
    loadingRequirements.value = false;
  }
};

const handleCountryChange = (event) => {
  const countryId = parseInt(event.target.value);
  selectedCountry.value = countryId;
  const country = props.countries.find(c => c.id === countryId);
  form.destination_country = country?.name || '';
};

const handleProfessionChange = (event) => {
  selectedProfession.value = event.target.value;
  form.profession = event.target.value;
};

const canProceedToStep2 = computed(() => {
  return selectedCountry.value && selectedCountryData.value;
});

const canProceedToStep3 = computed(() => {
  return canProceedToStep2.value && selectedProfession.value;
});

const goToStep = (step) => {
  if (step === 2 && !canProceedToStep2.value) return;
  if (step === 3 && !canProceedToStep3.value) return;
  currentStep.value = step;
};

const formatPrice = computed(() => {
  if (visaFees.value) {
    return `BDT ${visaFees.value.total_fee?.toLocaleString() || '0'}`;
  }
  if (selectedCountryData.value?.base_price) {
    return `BDT ${selectedCountryData.value.base_price.toLocaleString()}`;
  }
  return 'Select country to see price';
});

const processingTime = computed(() => {
  if (visaRequirements.value?.processing_time) {
    return visaRequirements.value.processing_time;
  }
  if (selectedCountryData.value?.processing_time) {
    return selectedCountryData.value.processing_time;
  }
  return 'Varies by destination';
});

const requirementsByCountry = computed(() => {
  if (visaRequirements.value?.required_documents) {
    return visaRequirements.value.required_documents.map(doc => ({
      text: doc,
      link: null
    }));
  }
  return [];
});

const documentsByProfession = computed(() => {
  if (visaRequirements.value?.profession_specific_docs) {
    return visaRequirements.value.profession_specific_docs;
  }
  return ['National ID Card', 'Passport copy', 'Employment letter', 'Salary slip'];
});

const viewRequirements = () => {
  showRequirementsModal.value = true;
};

const proceedToApplication = () => {
  showRequirementsModal.value = false;
  showApplicationForm.value = true;
};

const submitApplication = () => {
  // Prepare FormData for file upload
  const formData = new FormData();
  
  formData.append('service_module_id', form.service_module_id);
  formData.append('destination_country', form.destination_country);
  formData.append('profession', form.profession);
  formData.append('purpose', form.purpose);
  formData.append('travel_dates[departure]', form.travel_dates.departure);
  formData.append('travel_dates[return]', form.travel_dates.return);
  formData.append('notes', form.notes || '');
  
  // Append documents with their types
  uploadedDocuments.value.forEach((fileObj, index) => {
    if (fileObj.file) {
      formData.append(`documents[${index}]`, fileObj.file);
      formData.append(`document_types[${index}]`, fileObj.documentType || 'Other');
    }
  });

  router.post(route('user.applications.store'), formData, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showApplicationForm.value = false;
      form.reset();
      uploadedDocuments.value = [];
    },
    onError: (errors) => {
      console.error('Application submission error:', errors);
    },
  });
};
</script>

<template>
  <Head :title="`${service.name} - Apply Online`" />

  <AuthenticatedLayout>
    <div class="py-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <Link
          :href="route('services.index')"
          class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4 transition-colors"
        >
          <ArrowLeftIcon class="h-5 w-5" />
          Back to Services
        </Link>

        <!-- Header (Only shows basic info, no price until country selected) -->
        <div class="bg-sky-600 rounded-2xl shadow-xl p-6 md:p-8 mb-6 text-white">
          <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
              <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                <GlobeAsiaAustraliaIcon class="h-10 w-10" />
              </div>
              <div>
                <h1 class="text-2xl md:text-3xl font-bold">
                  {{ service.name }}
                </h1>
                <p class="text-blue-100 mt-1 text-sm md:text-base">
                  {{ service.short_description }}
                </p>
              </div>
            </div>
            
            <!-- Show price only when country is selected -->
            <div v-if="selectedCountryData" class="flex items-center gap-2 bg-white/20 rounded-xl px-4 py-2 backdrop-blur-sm">
              <div class="text-right">
                <div class="text-xs text-blue-100">Estimated Cost</div>
                <div class="text-lg md:text-xl font-bold">{{ formatPrice }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Steps Indicator -->
        <div class="max-w-4xl mx-auto mb-6">
          <div class="flex items-center justify-between">
            <div v-for="step in 3" :key="step" class="flex items-center flex-1">
              <div class="flex items-center">
                <div
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all',
                    currentStep >= step
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                  ]"
                >
                  <CheckCircleIcon v-if="currentStep > step" class="h-6 w-6" />
                  <span v-else>{{ step }}</span>
                </div>
                <div class="ml-2 hidden sm:block">
                  <div
                    :class="[
                      'text-sm font-medium',
                      currentStep >= step
                        ? 'text-gray-900 dark:text-white'
                        : 'text-gray-500 dark:text-gray-400'
                    ]"
                  >
                    <span v-if="step === 1">Select Country</span>
                    <span v-else-if="step === 2">Your Profile</span>
                    <span v-else>Review & Apply</span>
                  </div>
                </div>
              </div>
              <div
                v-if="step < 3"
                :class="[
                  'flex-1 h-1 mx-2 transition-all',
                  currentStep > step
                    ? 'bg-blue-600'
                    : 'bg-gray-200 dark:bg-gray-700'
                ]"
              ></div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
          <!-- Step 1: Country & Profession Selection -->
          <div v-show="currentStep === 1" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8">
            <div class="mb-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Start Your Visa Application
              </h2>
              <p class="text-gray-600 dark:text-gray-400">
                Select your destination and provide basic information
              </p>
            </div>

            <div class="space-y-6">
              <!-- Country Selection Dropdown -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <GlobeAsiaAustraliaIcon class="h-5 w-5 text-blue-600" />
                    Destination Country <span class="text-red-500">*</span>
                  </span>
                </label>
                <div class="relative">
                  <select
                    :value="selectedCountry"
                    @change="handleCountryChange"
                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white appearance-none bg-white pr-10 transition-all"
                    required
                  >
                    <option value="">Select destination country...</option>
                    <option v-for="country in countries" :key="country.id" :value="country.id">
                      {{ country.flag }} {{ country.name }}
                    </option>
                  </select>
                  <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                </div>
              </div>

              <!-- Profession Selection Dropdown -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <span class="flex items-center gap-2">
                    <BriefcaseIcon class="h-5 w-5 text-purple-600" />
                    Your Profession <span class="text-red-500">*</span>
                  </span>
                </label>
                <div class="relative">
                  <select
                    :value="selectedProfession"
                    @change="handleProfessionChange"
                    :disabled="!selectedCountry"
                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white appearance-none bg-white pr-10 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    required
                  >
                    <option value="">Select your profession...</option>
                    <option v-for="profession in professions" :key="profession" :value="profession">
                      {{ profession }}
                    </option>
                  </select>
                  <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                </div>
                <p v-if="!selectedCountry" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  Please select a country first
                </p>
              </div>

              <!-- Selected Info Preview -->
              <div v-if="selectedCountryData" class="p-5 bg-sky-50 dark:bg-sky-900/20 rounded-xl border border-sky-200 dark:border-sky-800">
                <div class="flex items-start gap-4">
                  <div class="text-5xl">{{ selectedCountryData.flag }}</div>
                  <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                      {{ selectedCountryData.name }} Tourist Visa
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                      <div class="flex items-center gap-2">
                        <ClockIcon class="h-4 w-4 text-blue-600" />
                        <div>
                          <div class="text-xs text-gray-600 dark:text-gray-400">Processing</div>
                          <div class="font-semibold text-gray-900 dark:text-white">
                            {{ processingTime }}
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center gap-2">
                        <CurrencyDollarIcon class="h-4 w-4 text-green-600" />
                        <div>
                          <div class="text-xs text-gray-600 dark:text-gray-400">Estimated Cost</div>
                          <div class="font-semibold text-gray-900 dark:text-white">
                            {{ formatPrice }}
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Loading Requirements -->
                    <div v-if="loadingRequirements" class="mt-3 flex items-center gap-2 text-sm text-blue-600">
                      <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Loading requirements...
                    </div>
                  </div>
                </div>
              </div>

              <!-- Continue Button -->
              <div class="flex justify-end">
                <button
                  @click="goToStep(2)"
                  :disabled="!canProceedToStep2"
                  class="bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-8 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg hover:shadow-xl"
                >
                  Continue
                  <ArrowRightIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Step 2: Additional Details -->
          <div v-show="currentStep === 2" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8">
            <div class="mb-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Additional Information
              </h2>
              <p class="text-gray-600 dark:text-gray-400">
                Provide travel details for your visa application
              </p>
            </div>

            <!-- Selection Summary -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                  <div class="text-3xl">{{ selectedCountryData?.flag }}</div>
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Destination</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                      {{ selectedCountryData?.name }}
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <BriefcaseIcon class="h-6 w-6 text-purple-600" />
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Profession</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                      {{ selectedProfession }}
                    </div>
                  </div>
                </div>
                <button
                  @click="goToStep(1)"
                  class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium"
                >
                  Change
                </button>
              </div>
            </div>

            <div class="space-y-5">
              <!-- Travel Purpose -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Purpose of Travel <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.purpose"
                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                  required
                >
                  <option value="Tourism">Tourism</option>
                  <option value="Business">Business</option>
                  <option value="Medical">Medical</option>
                  <option value="Family Visit">Family Visit</option>
                  <option value="Conference">Conference</option>
                  <option value="Other">Other</option>
                </select>
              </div>

              <!-- Travel Dates -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Departure Date <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.travel_dates.departure"
                    type="date"
                    :min="new Date().toISOString().split('T')[0]"
                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    required
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Return Date <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.travel_dates.return"
                    type="date"
                    :min="form.travel_dates.departure"
                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    required
                  />
                </div>
              </div>

              <!-- Additional Notes -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Additional Notes (Optional)
                </label>
                <textarea
                  v-model="form.notes"
                  rows="3"
                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                  placeholder="Any special requirements or additional information..."
                ></textarea>
              </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex gap-3 mt-6">
              <button
                @click="goToStep(1)"
                class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
              >
                Back
              </button>
              <button
                @click="goToStep(3)"
                :disabled="!form.travel_dates.departure || !form.travel_dates.return"
                class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-6 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-lg"
              >
                Continue to Review
                <ArrowRightIcon class="h-5 w-5" />
              </button>
            </div>
          </div>

          <!-- Step 3: Review & Requirements -->
          <div v-show="currentStep === 3" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8">
            <div class="mb-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Review & Requirements
              </h2>
              <p class="text-gray-600 dark:text-gray-400">
                Check the requirements and start your application
              </p>
            </div>

            <!-- Application Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex items-center gap-3">
                  <div class="text-3xl">{{ selectedCountryData?.flag }}</div>
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Destination</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                      {{ selectedCountryData?.name }}
                    </div>
                    <button
                      @click="goToStep(1)"
                      class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400"
                    >
                      Change
                    </button>
                  </div>
                </div>
              </div>
              <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <div class="flex items-center gap-3">
                  <UserIcon class="h-8 w-8 text-purple-600" />
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Profession</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                      {{ selectedProfession }}
                    </div>
                    <button
                      @click="goToStep(2)"
                      class="text-xs text-purple-600 hover:text-purple-700 dark:text-purple-400"
                    >
                      Change
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cost Breakdown -->
            <div class="mb-6 p-5 bg-growth-50 dark:bg-growth-900/20 rounded-xl border border-growth-200 dark:border-growth-800">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
                Cost Breakdown
              </h3>
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Embassy/Consulate Fee</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    BDT {{ selectedCountryData ? Math.round(selectedCountryData.basePrice * 0.6).toLocaleString() : '0' }}
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Service Charge</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    BDT {{ selectedCountryData ? Math.round(selectedCountryData.basePrice * 0.3).toLocaleString() : '0' }}
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Document Processing</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    BDT {{ selectedCountryData ? Math.round(selectedCountryData.basePrice * 0.1).toLocaleString() : '0' }}
                  </span>
                </div>
                <div class="pt-3 border-t-2 border-green-300 dark:border-green-700 flex justify-between">
                  <span class="font-bold text-gray-900 dark:text-white">Total Amount</span>
                  <span class="font-bold text-green-600 text-xl">
                    {{ formatPrice }}
                  </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 mt-2">
                  <ClockIcon class="h-4 w-4" />
                  Processing time: {{ selectedCountryData?.processingTime }}
                </div>
              </div>
            </div>

            <!-- Requirements Preview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                  <CheckCircleIcon class="h-5 w-5 text-blue-600" />
                  General Documents
                </h4>
                <ul class="space-y-2 text-sm">
                  <li v-for="(req, index) in requirementsByCountry.slice(0, 5)" :key="index" class="flex items-start gap-2">
                    <CheckCircleIcon class="h-4 w-4 text-green-600 flex-shrink-0 mt-0.5" />
                    <span class="text-gray-700 dark:text-gray-300">{{ req.text }}</span>
                  </li>
                </ul>
              </div>
              <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                  <BriefcaseIcon class="h-5 w-5 text-purple-600" />
                  Profession-Specific
                </h4>
                <ul class="space-y-2 text-sm">
                  <li v-for="(doc, index) in documentsByProfession" :key="index" class="flex items-start gap-2">
                    <DocumentTextIcon class="h-4 w-4 text-purple-600 flex-shrink-0 mt-0.5" />
                    <span class="text-gray-700 dark:text-gray-300">{{ doc }}</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- View Full Requirements Button -->
            <button
              @click="viewRequirements"
              class="w-full p-4 border-2 border-blue-600 text-blue-600 dark:text-blue-400 rounded-lg font-semibold hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all mb-6"
            >
              View Complete Requirements & Checklist
            </button>

            <!-- Action Buttons -->
            <div class="flex gap-3">
              <button
                @click="goToStep(2)"
                class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
              >
                Back
              </button>
              <button
                @click="proceedToApplication"
                class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
              >
                <ShieldCheckIcon class="h-5 w-5" />
                Start Application
              </button>
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

        <!-- 360° Solution Banner -->
        <div class="bg-sky-50 dark:bg-sky-900/30 border border-sky-200 dark:border-sky-800 rounded-xl p-4 mb-6">
          <div class="flex items-start gap-3">
            <InformationCircleIcon class="h-6 w-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
            <div>
              <h4 class="font-semibold text-gray-900 dark:text-white mb-1">
                360° Travel Solution Platform
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Need help with hotel bookings or flight tickets? Click on the blue links below to access our related services. 
                All services are integrated for your convenience!
              </p>
            </div>
          </div>
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
                class="flex items-start gap-2 text-sm"
              >
                <CheckCircleIcon class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
                <span v-if="!req.service" class="text-gray-700 dark:text-gray-300">{{ req.text }}</span>
                <a
                  v-else
                  :href="req.link"
                  target="_blank"
                  class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline decoration-dotted underline-offset-2 hover:decoration-solid transition-all font-medium"
                >
                  {{ req.text }} ↗
                </a>
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
        <div class="bg-growth-50 dark:bg-growth-900/20 rounded-xl p-6 mb-6">
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
                BDT {{ visaFees ? (visaFees.processing_fee || 0).toLocaleString() : '0' }}
              </span>
            </div>
            <div class="pt-3 border-t border-green-200 dark:border-green-700 flex justify-between">
              <span class="font-bold text-gray-900 dark:text-white">Total Estimated Cost</span>
              <span class="font-bold text-green-600 text-lg">
                BDT {{ visaFees ? (visaFees.total_fee || 0).toLocaleString() : '0' }}
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
            class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-6 rounded-xl transition-all"
          >
            Proceed to Application
          </button>
        </div>
      </div>
    </Modal>

    <!-- Application Form Modal -->
    <Modal :show="showApplicationForm" @close="showApplicationForm = false" max-width="4xl">
      <div class="p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6 sticky top-0 bg-white dark:bg-gray-800 pb-4 border-b border-gray-200 dark:border-gray-700">
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
                Departure Date <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.travel_dates.departure"
                type="date"
                required
                :min="new Date().toISOString().split('T')[0]"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Return Date <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.travel_dates.return"
                type="date"
                required
                :min="form.travel_dates.departure"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              />
            </div>
          </div>

          <!-- Purpose -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Purpose of Travel <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.purpose"
              required
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="Tourism">Tourism</option>
              <option value="Business">Business</option>
              <option value="Medical">Medical</option>
              <option value="Family Visit">Family Visit</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <!-- Document Upload Section -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex items-center gap-2 mb-4">
              <ArrowUpTrayIcon class="h-6 w-6 text-blue-600" />
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Upload Required Documents
              </h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
              Please upload all required documents based on your profession and destination country. 
              Each file must be under 10MB and in PDF, JPG, or PNG format.
            </p>
            
            <DocumentUpload
              v-model="uploadedDocuments"
              :required-documents="documentsByProfession"
              :max-file-size="10"
            />
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
          <div class="bg-sky-50 dark:bg-sky-900/20 rounded-xl p-4 space-y-2 text-sm border border-sky-200 dark:border-sky-800">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Application Summary</h4>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <span class="text-gray-600 dark:text-gray-400">Destination:</span>
                <span class="font-medium text-gray-900 dark:text-white block">{{ form.destination_country }}</span>
              </div>
              <div>
                <span class="text-gray-600 dark:text-gray-400">Profession:</span>
                <span class="font-medium text-gray-900 dark:text-white block">{{ form.profession }}</span>
              </div>
              <div>
                <span class="text-gray-600 dark:text-gray-400">Purpose:</span>
                <span class="font-medium text-gray-900 dark:text-white block">{{ form.purpose }}</span>
              </div>
              <div>
                <span class="text-gray-600 dark:text-gray-400">Estimated Cost:</span>
                <span class="font-medium text-gray-900 dark:text-white block">{{ formatPrice }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-gray-600 dark:text-gray-400">Documents Uploaded:</span>
                <span class="font-medium text-gray-900 dark:text-white block">
                  {{ uploadedDocuments.length }} file(s)
                </span>
              </div>
            </div>
          </div>

          <!-- Important Notice -->
          <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <InformationCircleIcon class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" />
              <div class="text-sm text-amber-800 dark:text-amber-300">
                <p class="font-semibold mb-1">Before Submitting:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>Ensure all documents are clear and readable</li>
                  <li>Check that document types are correctly assigned</li>
                  <li>Verify your travel dates are accurate</li>
                  <li>Our team will review your application within 24-48 hours</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              @click="showApplicationForm = false"
              type="button"
              class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing || uploadedDocuments.length === 0"
              class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-6 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
            >
              <span v-if="form.processing">Submitting...</span>
              <span v-else>Submit Application</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
