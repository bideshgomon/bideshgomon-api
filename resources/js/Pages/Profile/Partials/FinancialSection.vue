<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue'
import FlowButton from '@/Components/Rhythmic/FlowButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import DateInput from '@/Components/DateInput.vue'
import InputError from '@/Components/InputError.vue'
import Modal from '@/Components/Modal.vue'
import {
  BanknotesIcon,
  EyeIcon,
  EyeSlashIcon,
  BuildingOfficeIcon,
  HomeIcon,
  TruckIcon,
  ChartBarIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  userProfile: Object,
})

// Show/hide sensitive data
const showSensitiveData = ref(false)
const showBankDetails = ref(false)
const showIncome = ref(false)

// Mask sensitive data
const maskAccount = (account) => {
  if (!account || showBankDetails.value) return account
  return '****' + account.slice(-4)
}

const maskAmount = (amount) => {
  if (!amount || showIncome.value) return amount
  return '৳ ****'
}

// Modal states
const showModal = ref(false)
const modalMode = ref('add') // 'add' or 'edit'
const editingItem = ref(null)

// Form data
const form = useForm({
  // Employment
  employer_name: props.userProfile?.employer_name || '',
  job_title: props.userProfile?.job_title || '',
  employment_start_date: props.userProfile?.employment_start_date || '',
  monthly_income: props.userProfile?.monthly_income || '',
  annual_income: props.userProfile?.annual_income || '',
  
  // Bank
  bank_name: props.userProfile?.bank_name || '',
  bank_branch: props.userProfile?.bank_branch || '',
  account_number: props.userProfile?.bank_account_number || '',
  bank_balance: props.userProfile?.bank_balance || '',
  
  // Assets
  property_value: props.userProfile?.property_value_bdt || '',
  property_details: props.userProfile?.property_details || '',
  vehicle_value: props.userProfile?.vehicle_value_bdt || '',
  vehicle_details: props.userProfile?.vehicle_details || '',
  investment_value: props.userProfile?.investment_value_bdt || '',
  investment_details: props.userProfile?.investment_details || '',
  
  // Liabilities
  liabilities_amount: props.userProfile?.liabilities_amount_bdt || '',
  liabilities_details: props.userProfile?.liabilities_details || '',
})

// Auto-calculate annual income from monthly income
watch(() => form.monthly_income, (newValue) => {
  if (newValue && !isNaN(newValue)) {
    form.annual_income = (parseFloat(newValue) * 12).toFixed(2)
  }
})

// Calculate net worth
const netWorth = computed(() => {
  const assets = 
    (parseFloat(form.property_value) || 0) +
    (parseFloat(form.vehicle_value) || 0) +
    (parseFloat(form.investment_value) || 0) +
    (parseFloat(form.bank_balance) || 0)
  const liabilities = parseFloat(form.liabilities_amount) || 0
  return (assets - liabilities).toFixed(2)
})

const openAddModal = () => {
  modalMode.value = 'add'
  form.reset()
  showModal.value = true
}

const openEditModal = () => {
  modalMode.value = 'edit'
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  form.post(route('profile.financial.update'), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
    },
  })
}
</script>

<template>
  <section class="space-y-4">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-rhythm-lg">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center shadow-sm">
          <BanknotesIcon class="w-6 h-6 text-white" />
        </div>
        <div>
          <h2 class="font-display font-bold text-xl text-gray-800">Financial Information</h2>
          <p class="text-xs text-gray-500">Secure financial overview</p>
        </div>
      </div>
      <FlowButton @click="openEditModal" variant="primary">
        <template #icon-left><PencilIcon class="w-4 h-4" /></template>
        <span class="hidden sm:inline">Edit Details</span>
        <span class="sm:hidden">Edit</span>
      </FlowButton>
    </div>

    <!-- Privacy Controls -->
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <EyeSlashIcon class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-amber-900 dark:text-amber-100">Sensitive Data Protected</p>
          <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">Financial data is encrypted and masked by default.</p>
          <div class="flex flex-wrap gap-2 mt-3">
            <button
              @click="showIncome = !showIncome"
              class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md transition-colors"
              :class="showIncome ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
              style="min-height: 36px"
            >
              <component :is="showIncome ? EyeIcon : EyeSlashIcon" class="w-4 h-4" />
              {{ showIncome ? 'Hide' : 'Show' }} Income
            </button>
            <button
              @click="showBankDetails = !showBankDetails"
              class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md transition-colors"
              :class="showBankDetails ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
              style="min-height: 36px"
            >
              <component :is="showBankDetails ? EyeIcon : EyeSlashIcon" class="w-4 h-4" />
              {{ showBankDetails ? 'Hide' : 'Show' }} Bank Details
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Net Worth Summary Card -->
    <div class="bg-gradient-to-br from-green-600 to-emerald-700 rounded-xl p-6 text-white shadow-lg">
      <div class="flex items-center gap-2 mb-2">
        <ChartBarIcon class="w-5 h-5" />
        <span class="text-sm font-medium opacity-90">Net Worth</span>
      </div>
      <div class="text-3xl font-bold">৳ {{ netWorth }}</div>
      <p class="text-xs opacity-75 mt-1">Assets minus liabilities</p>
    </div>

    <!-- Employment Card -->
    <div v-if="form.employer_name" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
      <div class="h-1 bg-gradient-to-r from-green-600 to-emerald-700"></div>
      <div class="p-4">
        <div class="flex items-start gap-3 mb-4">
          <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
            <BuildingOfficeIcon class="w-5 h-5 text-green-600 dark:text-green-400" />
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ form.employer_name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ form.job_title }}</p>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div>
            <span class="text-gray-500 dark:text-gray-400">Monthly Income:</span>
            <p class="font-semibold text-gray-900 dark:text-white mt-0.5">
              {{ showIncome ? `৳ ${form.monthly_income}` : maskAmount(form.monthly_income) }}
            </p>
          </div>
          <div>
            <span class="text-gray-500 dark:text-gray-400">Annual Income:</span>
            <p class="font-semibold text-gray-900 dark:text-white mt-0.5">
              {{ showIncome ? `৳ ${form.annual_income}` : maskAmount(form.annual_income) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Bank Account Card -->
    <div v-if="form.bank_name" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
      <div class="h-1 bg-gradient-to-r from-blue-600 to-cyan-600"></div>
      <div class="p-4">
        <div class="flex items-start gap-3 mb-4">
          <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
            <BanknotesIcon class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ form.bank_name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ form.bank_branch }}</p>
          </div>
        </div>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-gray-500 dark:text-gray-400">Account Number:</span>
            <span class="font-mono font-semibold text-gray-900 dark:text-white">
              {{ maskAccount(form.account_number) }}
            </span>
          </div>
          <div class="flex justify-between items-center py-2">
            <span class="text-gray-500 dark:text-gray-400">Current Balance:</span>
            <span class="font-semibold text-green-600 dark:text-green-400">
              {{ showBankDetails ? `৳ ${form.bank_balance}` : maskAmount(form.bank_balance) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Assets Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Property Card -->
      <div v-if="form.property_value" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="h-1 bg-gradient-to-r from-purple-600 to-pink-600"></div>
        <div class="p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
              <HomeIcon class="w-5 h-5 text-purple-600 dark:text-purple-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Property</h3>
          </div>
          <p class="text-lg font-bold text-gray-900 dark:text-white">৳ {{ form.property_value }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ form.property_details || 'Real estate holdings' }}</p>
        </div>
      </div>

      <!-- Vehicle Card -->
      <div v-if="form.vehicle_value" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="h-1 bg-gradient-to-r from-orange-600 to-red-600"></div>
        <div class="p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
              <TruckIcon class="w-5 h-5 text-orange-600 dark:text-orange-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Vehicle</h3>
          </div>
          <p class="text-lg font-bold text-gray-900 dark:text-white">৳ {{ form.vehicle_value }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ form.vehicle_details || 'Vehicle assets' }}</p>
        </div>
      </div>

      <!-- Investment Card -->
      <div v-if="form.investment_value" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="h-1 bg-gradient-to-r from-teal-600 to-cyan-600"></div>
        <div class="p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
              <ChartBarIcon class="w-5 h-5 text-teal-600 dark:text-teal-400" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Investments</h3>
          </div>
          <p class="text-lg font-bold text-gray-900 dark:text-white">৳ {{ form.investment_value }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ form.investment_details || 'Investment portfolio' }}</p>
        </div>
      </div>
    </div>

    <!-- Liabilities Card (if exists) -->
    <div v-if="form.liabilities_amount" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-red-200 dark:border-red-900/50">
      <div class="h-1 bg-gradient-to-r from-red-600 to-orange-600"></div>
      <div class="p-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Liabilities</h3>
            <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-1">৳ {{ form.liabilities_amount }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ form.liabilities_details || 'Outstanding debts and obligations' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!form.employer_name && !form.bank_name && !form.property_value && !form.vehicle_value && !form.investment_value" class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
      <BanknotesIcon class="w-12 h-12 text-gray-400 mx-auto mb-3" />
      <p class="text-gray-600 dark:text-gray-400 font-medium">No financial information added yet</p>
      <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Add your financial details for visa applications</p>
      <button
        @click="openEditModal"
        class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-700 rounded-lg hover:from-green-700 hover:to-emerald-800 transition-all shadow-md"
        style="min-height: 44px"
      >
        <PlusIcon class="w-5 h-5" />
        Add Financial Information
      </button>
    </div>

    <!-- Edit Modal - Reduced width for better readability -->
    <Modal :show="showModal" @close="closeModal" max-width="3xl">
      <div class="p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-600 to-emerald-700 flex items-center justify-center">
            <BanknotesIcon class="w-6 h-6 text-white" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Financial Information</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Update your financial details</p>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Employment Section -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <BuildingOfficeIcon class="w-5 h-5 text-green-600" />
              Employment Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="employer_name" value="Employer Name" />
                <TextInput
                  id="employer_name"
                  v-model="form.employer_name"
                  type="text"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="Company name"
                />
                <InputError class="mt-2" :message="form.errors.employer_name" />
              </div>

              <div>
                <InputLabel for="job_title" value="Job Title" />
                <TextInput
                  id="job_title"
                  v-model="form.job_title"
                  type="text"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="Your position"
                />
                <InputError class="mt-2" :message="form.errors.job_title" />
              </div>

              <div>
                <InputLabel for="monthly_income" value="Monthly Income (৳ BDT)" />
                <TextInput
                  id="monthly_income"
                  v-model="form.monthly_income"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="50000.00"
                />
                <InputError class="mt-2" :message="form.errors.monthly_income" />
              </div>

              <div>
                <InputLabel for="annual_income" value="Annual Income (৳ BDT)" />
                <div class="relative">
                  <TextInput
                    id="annual_income"
                    v-model="form.annual_income"
                    type="number"
                    step="0.01"
                    class="mt-1 block w-full"
                    style="font-size: 16px"
                    placeholder="600000.00"
                    readonly
                  />
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500 dark:text-gray-400">Auto-calculated</span>
                </div>
                <InputError class="mt-2" :message="form.errors.annual_income" />
              </div>
            </div>
          </div>

          <!-- Bank Details Section -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <BanknotesIcon class="w-5 h-5 text-blue-600" />
              Bank Account Details
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="bank_name" value="Bank Name" />
                <TextInput
                  id="bank_name"
                  v-model="form.bank_name"
                  type="text"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="e.g., Dutch-Bangla Bank"
                />
                <InputError class="mt-2" :message="form.errors.bank_name" />
              </div>

              <div>
                <InputLabel for="bank_branch" value="Branch Name" />
                <TextInput
                  id="bank_branch"
                  v-model="form.bank_branch"
                  type="text"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="Gulshan Branch"
                />
                <InputError class="mt-2" :message="form.errors.bank_branch" />
              </div>

              <div>
                <InputLabel for="account_number" value="Account Number" />
                <TextInput
                  id="account_number"
                  v-model="form.account_number"
                  type="text"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="Account number"
                />
                <InputError class="mt-2" :message="form.errors.account_number" />
              </div>

              <div>
                <InputLabel for="bank_balance" value="Current Balance (৳ BDT)" />
                <TextInput
                  id="bank_balance"
                  v-model="form.bank_balance"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="2500000.00"
                />
                <InputError class="mt-2" :message="form.errors.bank_balance" />
              </div>
            </div>
          </div>

          <!-- Assets Section -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <HomeIcon class="w-5 h-5 text-purple-600" />
              Assets & Property
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="property_value" value="Property Value (৳ BDT)" />
                <TextInput
                  id="property_value"
                  v-model="form.property_value"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="15000000.00"
                />
                <InputError class="mt-2" :message="form.errors.property_value" />
              </div>

              <div>
                <InputLabel for="vehicle_value" value="Vehicle Value (৳ BDT)" />
                <TextInput
                  id="vehicle_value"
                  v-model="form.vehicle_value"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="3500000.00"
                />
                <InputError class="mt-2" :message="form.errors.vehicle_value" />
              </div>

              <div>
                <InputLabel for="investment_value" value="Investment Value (৳ BDT)" />
                <TextInput
                  id="investment_value"
                  v-model="form.investment_value"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="5000000.00"
                />
                <InputError class="mt-2" :message="form.errors.investment_value" />
              </div>
            </div>

            <div class="sm:col-span-2">
              <InputLabel for="property_details" value="Property Details" />
              <textarea
                id="property_details"
                v-model="form.property_details"
                rows="2"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Describe your properties..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.property_details" />
            </div>

            <div class="sm:col-span-2">
              <InputLabel for="vehicle_details" value="Vehicle Details" />
              <textarea
                id="vehicle_details"
                v-model="form.vehicle_details"
                rows="2"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Make, model, year..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.vehicle_details" />
            </div>

            <div class="sm:col-span-2">
              <InputLabel for="investment_details" value="Investment Details" />
              <textarea
                id="investment_details"
                v-model="form.investment_details"
                rows="2"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Describe your investments..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.investment_details" />
            </div>
          </div>

          <!-- Liabilities Section -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Liabilities & Debts
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="liabilities_amount" value="Total Liabilities (৳ BDT)" />
                <TextInput
                  id="liabilities_amount"
                  v-model="form.liabilities_amount"
                  type="number"
                  step="0.01"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="2000000.00"
                />
                <InputError class="mt-2" :message="form.errors.liabilities_amount" />
              </div>

              <div class="sm:col-span-2">
                <InputLabel for="liabilities_details" value="Liabilities Details" />
                <textarea
                  id="liabilities_details"
                  v-model="form.liabilities_details"
                  rows="2"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
                  style="font-size: 16px"
                  placeholder="List all liabilities: type, amount, repayment terms..."
                ></textarea>
                <InputError class="mt-2" :message="form.errors.liabilities_details" />
              </div>
            </div>
          </div>

          <!-- Net Worth Display -->
          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Calculated Net Worth:</span>
              <span class="text-xl font-bold text-green-600 dark:text-green-400">৳ {{ netWorth }}</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total assets minus liabilities</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              type="button"
              @click="closeModal"
              class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              style="min-height: 44px"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-700 rounded-lg hover:from-green-700 hover:to-emerald-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md"
              style="min-height: 44px"
            >
              <span v-if="form.processing">Saving...</span>
              <span v-else>Save Financial Information</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </section>
</template>
