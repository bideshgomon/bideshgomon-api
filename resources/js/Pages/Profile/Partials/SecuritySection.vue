<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue'
import FlowButton from '@/Components/Rhythmic/FlowButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import {
  ShieldCheckIcon,
  ShieldExclamationIcon,
  DocumentTextIcon,
  PencilIcon,
  CheckCircleIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  securityInformation: Object,
})

const showModal = ref(false)

const form = useForm({
  // Criminal Records
  has_criminal_record: props.securityInformation?.has_criminal_record || false,
  criminal_record_details: props.securityInformation?.criminal_record_details || '',
  
  // Police Clearance
  has_police_clearance: props.securityInformation?.has_police_clearance || false,
  police_clearance_country: props.securityInformation?.police_clearance_country || '',
  police_clearance_issue_date: props.securityInformation?.police_clearance_issue_date || '',
  police_clearance_expiry_date: props.securityInformation?.police_clearance_expiry_date || '',
  
  // Court Cases
  has_pending_court_cases: props.securityInformation?.has_pending_court_cases || false,
  court_case_details: props.securityInformation?.court_case_details || '',
  
  // Immigration
  has_immigration_violations: props.securityInformation?.has_immigration_violations || false,
  immigration_violation_details: props.securityInformation?.immigration_violation_details || '',
  has_visa_refusal: props.securityInformation?.has_visa_refusal || false,
  visa_refusal_details: props.securityInformation?.visa_refusal_details || '',
  
  // Travel Bans
  has_travel_ban: props.securityInformation?.has_travel_ban || false,
  travel_ban_details: props.securityInformation?.travel_ban_details || '',
  
  // Military Service
  has_military_service: props.securityInformation?.has_military_service || false,
  military_service_details: props.securityInformation?.military_service_details || '',
  
  // Watchlist
  on_watchlist: props.securityInformation?.on_watchlist || false,
  watchlist_details: props.securityInformation?.watchlist_details || '',
  
  // Additional Notes
  security_notes: props.securityInformation?.security_notes || '',
})

const openModal = () => {
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.clearErrors()
}

const submitForm = () => {
  form.post(route('profile.security.update'), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
    },
  })
}

const hasAnySecurityIssues = computed(() => {
  return form.has_criminal_record ||
         form.has_pending_court_cases ||
         form.has_immigration_violations ||
         form.has_visa_refusal ||
         form.has_travel_ban ||
         form.on_watchlist
})

const securityStatus = computed(() => {
  if (hasAnySecurityIssues.value) {
    return {
      icon: ShieldExclamationIcon,
      text: 'Security Issues Reported',
      class: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
      badge: 'bg-red-100 border-2 border-red-300'
    }
  }
  return {
    icon: ShieldCheckIcon,
    text: 'No Security Issues',
    class: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    badge: 'bg-green-600'
  }
})
</script>

<template>
  <section class="space-y-4">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-rhythm-lg">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-50 border-2 border-red-200 flex items-center justify-center shadow-sm">
          <ShieldCheckIcon class="w-6 h-6 text-red-600 opacity-70" />
        </div>
        <div>
          <h2 class="font-display font-bold text-xl text-gray-800">Security & Background</h2>
          <p class="text-xs text-gray-500">Background check information</p>
        </div>
      </div>
      <FlowButton @click="openModal" variant="primary">
        <template #icon-left><PencilIcon class="w-4 h-4" /></template>
        <span class="hidden sm:inline">Edit Details</span>
        <span class="sm:hidden">Edit</span>
      </FlowButton>
    </div>

    <!-- Status Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
      <div class="h-1" :class="hasAnySecurityIssues ? 'bg-red-200' : 'bg-green-600'"></div>
      <div class="p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="securityStatus.class">
            <component :is="securityStatus.icon" class="w-7 h-7" />
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ securityStatus.text }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
              {{ hasAnySecurityIssues ? 'Review required for visa processing' : 'Clear for visa processing' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Security Checks Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
      <div class="h-1 bg-red-200"></div>
      <div class="p-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Security Checks</h3>
        <div class="space-y-3">
          <!-- Criminal Record -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Criminal Record</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_criminal_record ? XCircleIcon : CheckCircleIcon"
                class="w-5 h-5"
                :class="form.has_criminal_record ? 'text-red-600' : 'text-green-600'"
              />
              <span class="text-xs font-medium" :class="form.has_criminal_record ? 'text-red-600' : 'text-green-600'">
                {{ form.has_criminal_record ? 'Declared' : 'Clean' }}
              </span>
            </div>
          </div>

          <!-- Police Clearance -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Police Clearance</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_police_clearance ? CheckCircleIcon : XCircleIcon"
                class="w-5 h-5"
                :class="form.has_police_clearance ? 'text-green-600' : 'text-gray-400'"
              />
              <span class="text-xs font-medium" :class="form.has_police_clearance ? 'text-green-600' : 'text-gray-500'">
                {{ form.has_police_clearance ? 'Obtained' : 'Not Obtained' }}
              </span>
            </div>
          </div>

          <!-- Court Cases -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Pending Court Cases</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_pending_court_cases ? XCircleIcon : CheckCircleIcon"
                class="w-5 h-5"
                :class="form.has_pending_court_cases ? 'text-red-600' : 'text-green-600'"
              />
              <span class="text-xs font-medium" :class="form.has_pending_court_cases ? 'text-red-600' : 'text-green-600'">
                {{ form.has_pending_court_cases ? 'Pending' : 'None' }}
              </span>
            </div>
          </div>

          <!-- Immigration Violations -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Immigration Violations</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_immigration_violations ? XCircleIcon : CheckCircleIcon"
                class="w-5 h-5"
                :class="form.has_immigration_violations ? 'text-red-600' : 'text-green-600'"
              />
              <span class="text-xs font-medium" :class="form.has_immigration_violations ? 'text-red-600' : 'text-green-600'">
                {{ form.has_immigration_violations ? 'Reported' : 'Clean' }}
              </span>
            </div>
          </div>

          <!-- Visa Refusal -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Previous Visa Refusal</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_visa_refusal ? XCircleIcon : CheckCircleIcon"
                class="w-5 h-5"
                :class="form.has_visa_refusal ? 'text-orange-600' : 'text-green-600'"
              />
              <span class="text-xs font-medium" :class="form.has_visa_refusal ? 'text-orange-600' : 'text-green-600'">
                {{ form.has_visa_refusal ? 'Yes' : 'No' }}
              </span>
            </div>
          </div>

          <!-- Travel Ban -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Travel Ban</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_travel_ban ? XCircleIcon : CheckCircleIcon"
                class="w-5 h-5"
                :class="form.has_travel_ban ? 'text-red-600' : 'text-green-600'"
              />
              <span class="text-xs font-medium" :class="form.has_travel_ban ? 'text-red-600' : 'text-green-600'">
                {{ form.has_travel_ban ? 'Active' : 'None' }}
              </span>
            </div>
          </div>

          <!-- Military Service -->
          <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-700 dark:text-gray-300">Military Service</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.has_military_service ? CheckCircleIcon : XCircleIcon"
                class="w-5 h-5"
                :class="form.has_military_service ? 'text-blue-600' : 'text-gray-400'"
              />
              <span class="text-xs font-medium" :class="form.has_military_service ? 'text-blue-600' : 'text-gray-500'">
                {{ form.has_military_service ? 'Served' : 'Not Served' }}
              </span>
            </div>
          </div>

          <!-- Watchlist -->
          <div class="flex items-center justify-between py-2">
            <span class="text-sm text-gray-700 dark:text-gray-300">Watchlist Status</span>
            <div class="flex items-center gap-2">
              <component 
                :is="form.on_watchlist ? XCircleIcon : CheckCircleIcon"
                class="w-5 h-5"
                :class="form.on_watchlist ? 'text-red-600' : 'text-green-600'"
              />
              <span class="text-xs font-medium" :class="form.on_watchlist ? 'text-red-600' : 'text-green-600'">
                {{ form.on_watchlist ? 'Listed' : 'Clear' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Notes (if any) -->
    <div v-if="form.security_notes" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <DocumentTextIcon class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-amber-900 dark:text-amber-100">Additional Notes</p>
          <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">{{ form.security_notes }}</p>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="4xl">
      <div class="p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center border-2 border-red-200">
            <ShieldCheckIcon class="w-6 h-6 text-red-600 opacity-70" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Security & Background Check</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Update security information</p>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800 dark:text-blue-200">
              <strong>Important:</strong> Provide accurate information. False declarations may result in visa denial or legal consequences.
            </p>
          </div>

          <!-- Criminal Record -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_criminal_record"
                type="checkbox"
                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have a criminal record</span>
            </label>

            <div v-if="form.has_criminal_record">
              <InputLabel for="criminal_record_details" value="Criminal Record Details *" />
              <textarea
                id="criminal_record_details"
                v-model="form.criminal_record_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Describe the offense, date, location, and outcome..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.criminal_record_details" />
            </div>
          </div>

          <!-- Police Clearance -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_police_clearance"
                type="checkbox"
                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have obtained police clearance certificate</span>
            </label>

            <div v-if="form.has_police_clearance" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="police_clearance_country" value="Country" />
                <TextInput
                  id="police_clearance_country"
                  v-model="form.police_clearance_country"
                  type="text"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                  placeholder="e.g., Bangladesh"
                />
                <InputError class="mt-2" :message="form.errors.police_clearance_country" />
              </div>

              <div>
                <InputLabel for="police_clearance_issue_date" value="Issue Date" />
                <TextInput
                  id="police_clearance_issue_date"
                  v-model="form.police_clearance_issue_date"
                  type="date"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                />
                <InputError class="mt-2" :message="form.errors.police_clearance_issue_date" />
              </div>

              <div>
                <InputLabel for="police_clearance_expiry_date" value="Expiry Date" />
                <TextInput
                  id="police_clearance_expiry_date"
                  v-model="form.police_clearance_expiry_date"
                  type="date"
                  class="mt-1 block w-full"
                  style="font-size: 16px"
                />
                <InputError class="mt-2" :message="form.errors.police_clearance_expiry_date" />
              </div>
            </div>
          </div>

          <!-- Court Cases -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_pending_court_cases"
                type="checkbox"
                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have pending court cases</span>
            </label>

            <div v-if="form.has_pending_court_cases">
              <InputLabel for="court_case_details" value="Court Case Details *" />
              <textarea
                id="court_case_details"
                v-model="form.court_case_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Case number, court name, nature of case..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.court_case_details" />
            </div>
          </div>

          <!-- Immigration Violations -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_immigration_violations"
                type="checkbox"
                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have immigration violations</span>
            </label>

            <div v-if="form.has_immigration_violations">
              <InputLabel for="immigration_violation_details" value="Immigration Violation Details *" />
              <textarea
                id="immigration_violation_details"
                v-model="form.immigration_violation_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Describe overstays, deportations, or other violations..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.immigration_violation_details" />
            </div>
          </div>

          <!-- Visa Refusal -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_visa_refusal"
                type="checkbox"
                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have been refused a visa before</span>
            </label>

            <div v-if="form.has_visa_refusal">
              <InputLabel for="visa_refusal_details" value="Visa Refusal Details *" />
              <textarea
                id="visa_refusal_details"
                v-model="form.visa_refusal_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Country, date, and reason for refusal..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.visa_refusal_details" />
            </div>
          </div>

          <!-- Travel Ban -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_travel_ban"
                type="checkbox"
                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have an active travel ban</span>
            </label>

            <div v-if="form.has_travel_ban">
              <InputLabel for="travel_ban_details" value="Travel Ban Details *" />
              <textarea
                id="travel_ban_details"
                v-model="form.travel_ban_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Countries, reason, and duration..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.travel_ban_details" />
            </div>
          </div>

          <!-- Military Service -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.has_military_service"
                type="checkbox"
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I have served in the military</span>
            </label>

            <div v-if="form.has_military_service">
              <InputLabel for="military_service_details" value="Military Service Details" />
              <textarea
                id="military_service_details"
                v-model="form.military_service_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="Branch, rank, dates of service..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.military_service_details" />
            </div>
          </div>

          <!-- Watchlist -->
          <div class="space-y-4">
            <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer" style="min-height: 44px">
              <input
                v-model="form.on_watchlist"
                type="checkbox"
                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">I am on a watchlist or sanctions list</span>
            </label>

            <div v-if="form.on_watchlist">
              <InputLabel for="watchlist_details" value="Watchlist Details *" />
              <textarea
                id="watchlist_details"
                v-model="form.watchlist_details"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
                style="font-size: 16px"
                placeholder="List name, reason, and countries..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.watchlist_details" />
            </div>
          </div>

          <!-- Additional Notes -->
          <div>
            <InputLabel for="security_notes" value="Additional Security Notes (Optional)" />
            <textarea
              id="security_notes"
              v-model="form.security_notes"
              rows="3"
              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm"
              style="font-size: 16px"
              placeholder="Any other relevant security information..."
            ></textarea>
            <InputError class="mt-2" :message="form.errors.security_notes" />
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
              class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-red-700 bg-red-50 border-2 border-red-300 rounded-lg hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md"
              style="min-height: 44px"
            >
              <span v-if="form.processing">Saving...</span>
              <span v-else>Save Security Information</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </section>
</template>
