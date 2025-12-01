<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DynamicFormField from '@/Components/Services/DynamicFormField.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputError from '@/Components/InputError.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  ClockIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'

const { formatCurrency, formatDate } = useBangladeshFormat()

const props = defineProps({
  service: {
    type: Object,
    required: true
  },
  formFields: {
    type: Array,
    required: true
  },
  prefilledData: {
    type: Object,
    default: () => ({})
  },
  existingApplication: {
    type: Object,
    default: null
  },
  user: {
    type: Object,
    required: true
  }
})

// Group fields by group_name
const formGroups = computed(() => {
  const groups = {}
  
  props.formFields.forEach(field => {
    const groupName = field.group_name || 'General Information'
    if (!groups[groupName]) {
      groups[groupName] = []
    }
    groups[groupName].push(field)
  })
  
  // Sort fields within each group by sort_order
  Object.keys(groups).forEach(groupName => {
    groups[groupName].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
  })
  
  return groups
})

// Check if any fields have profile mapping
const hasMappedFields = computed(() => {
  return props.formFields.some(field => field.profile_map_key)
})

// Initialize form data
const formData = ref({})
const saveToProfile = ref(false)
const isDraft = ref(false)

// Initialize form with prefilled data or existing application data
onMounted(() => {
  if (props.existingApplication && props.existingApplication.form_data) {
    // Load existing application data
    formData.value = { ...props.existingApplication.form_data }
    isDraft.value = props.existingApplication.status === 'draft'
  } else if (props.prefilledData) {
    // Load prefilled data from profile
    formData.value = { ...props.prefilledData }
  }
})

// Check if field should be shown based on conditional rules
const shouldShowField = (field) => {
  if (!field.conditional_rules) return true
  
  try {
    const rules = typeof field.conditional_rules === 'string'
      ? JSON.parse(field.conditional_rules)
      : field.conditional_rules
    
    if (rules.show_if) {
      const { field_name, operator, value } = rules.show_if
      const fieldValue = formData.value[field_name]
      
      switch (operator) {
        case 'equals':
          return fieldValue == value
        case 'not_equals':
          return fieldValue != value
        case 'contains':
          return fieldValue && fieldValue.includes(value)
        case 'greater_than':
          return fieldValue > value
        case 'less_than':
          return fieldValue < value
        case 'is_empty':
          return !fieldValue
        case 'is_not_empty':
          return !!fieldValue
        default:
          return true
      }
    }
    
    return true
  } catch (e) {
    console.error('Error evaluating conditional rules:', e)
    return true
  }
}

// Use Inertia form for submission
const form = useForm({
  form_data: formData,
  save_to_profile: saveToProfile,
  status: 'submitted'
})

// Auto-save draft every 30 seconds
let autoSaveTimer = null
onMounted(() => {
  autoSaveTimer = setInterval(() => {
    if (Object.keys(formData.value).length > 0) {
      saveDraft(true) // silent save
    }
  }, 30000) // 30 seconds
})

// Clear timer on unmount
onMounted(() => {
  return () => {
    if (autoSaveTimer) clearInterval(autoSaveTimer)
  }
})

// Save as draft
const saveDraft = (silent = false) => {
  const draftForm = useForm({
    form_data: formData.value,
    status: 'draft'
  })
  
  const url = props.existingApplication
    ? route('applications.update', props.existingApplication.id)
    : route('applications.store', props.service.slug)
  
  if (props.existingApplication) {
    draftForm.put(url, {
      preserveScroll: true,
      onSuccess: () => {
        if (!silent) {
          alert('Draft saved successfully!')
        }
      }
    })
  } else {
    draftForm.post(url, {
      preserveScroll: true,
      onSuccess: () => {
        if (!silent) {
          alert('Draft saved successfully!')
        }
      }
    })
  }
}

// Submit application
const submitApplication = () => {
  form.form_data = formData.value
  form.save_to_profile = saveToProfile.value
  form.status = 'submitted'
  
  const url = props.existingApplication
    ? route('applications.submit', props.existingApplication.id)
    : route('applications.store', props.service.slug)
  
  if (props.existingApplication && isDraft.value) {
    // Submit existing draft
    form.post(route('applications.submit', props.existingApplication.id), {
      onSuccess: () => {
        router.visit(route('applications.index'))
      }
    })
  } else if (props.existingApplication) {
    // Update existing application
    form.put(route('applications.update', props.existingApplication.id), {
      onSuccess: () => {
        router.visit(route('applications.show', props.existingApplication.id))
      }
    })
  } else {
    // Create new application
    form.post(route('applications.store', props.service.slug), {
      onSuccess: () => {
        router.visit(route('applications.index'))
      }
    })
  }
}

// Cancel application
const cancelApplication = () => {
  if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
    router.visit(route('services.show', props.service.slug))
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Apply for ${service.name}`" />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <Link
          :href="route('services.show', service.slug)"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
        >
          <ArrowLeftIcon class="h-4 w-4 mr-1" />
          Back to Service
        </Link>

        <!-- Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <div class="flex items-start justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ existingApplication ? 'Edit Application' : 'Apply for Service' }}
              </h1>
              <p class="text-sm text-gray-600 mt-1">{{ service.name }}</p>
            </div>
            <div
              v-if="existingApplication && existingApplication.application_number"
              class="text-right"
            >
              <p class="text-xs text-gray-500">Application Number</p>
              <p class="text-sm font-mono font-semibold text-gray-900">
                {{ existingApplication.application_number }}
              </p>
            </div>
          </div>

          <!-- Service Info -->
          <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center text-sm text-gray-600">
              <ClockIcon class="h-5 w-5 mr-2 text-gray-400" />
              <span>Processing: {{ service.processing_days || 'N/A' }} days</span>
            </div>
            <div v-if="service.requires_approval" class="flex items-center text-sm text-gray-600">
              <CheckCircleIcon class="h-5 w-5 mr-2 text-gray-400" />
              <span>Requires admin approval</span>
            </div>
          </div>
        </div>

        <!-- Auto-fill Notice -->
        <div
          v-if="hasMappedFields"
          class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6"
        >
          <div class="flex items-start">
            <InformationCircleIcon class="h-5 w-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" />
            <div>
              <h3 class="text-sm font-medium text-blue-900">Auto-filled from your profile</h3>
              <p class="text-sm text-blue-700 mt-1">
                Some fields have been automatically filled from your profile. Please review and
                update if necessary.
              </p>
            </div>
          </div>
        </div>

        <!-- Draft Notice -->
        <div
          v-if="isDraft"
          class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6"
        >
          <div class="flex items-start">
            <ExclamationTriangleIcon class="h-5 w-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" />
            <div>
              <h3 class="text-sm font-medium text-yellow-900">Draft Application</h3>
              <p class="text-sm text-yellow-700 mt-1">
                This application is saved as a draft. Submit it to send for review.
              </p>
            </div>
          </div>
        </div>

        <!-- Application Form -->
        <form @submit.prevent="submitApplication" class="space-y-6">
          <!-- Form Groups -->
          <div
            v-for="(fields, groupName) in formGroups"
            :key="groupName"
            class="bg-white shadow rounded-lg p-6"
          >
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ groupName }}</h2>
            
            <div class="grid grid-cols-12 gap-4">
              <DynamicFormField
                v-for="field in fields"
                :key="field.id"
                v-show="shouldShowField(field)"
                :field="field"
                v-model="formData[field.field_name]"
                :error="form.errors[`form_data.${field.field_name}`]"
              />
            </div>
          </div>

          <!-- Save to Profile Option -->
          <div
            v-if="hasMappedFields && !existingApplication"
            class="bg-white shadow rounded-lg p-6"
          >
            <label class="flex items-start">
              <input
                type="checkbox"
                v-model="saveToProfile"
                class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
              />
              <span class="ml-3">
                <span class="text-sm font-medium text-gray-900">
                  Save changes back to my profile
                </span>
                <p class="text-sm text-gray-500">
                  Update your profile with the information provided in this application for faster
                  future applications.
                </p>
              </span>
            </label>
          </div>

          <!-- Form Errors -->
          <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
              <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
            </ul>
          </div>

          <!-- Action Buttons -->
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
              <div class="flex gap-3 w-full sm:w-auto">
                <SecondaryButton
                  type="button"
                  @click="cancelApplication"
                  class="flex-1 sm:flex-none"
                >
                  Cancel
                </SecondaryButton>
                
                <SecondaryButton
                  type="button"
                  @click="saveDraft"
                  :disabled="form.processing"
                  class="flex-1 sm:flex-none"
                >
                  Save as Draft
                </SecondaryButton>
              </div>

              <PrimaryButton
                type="submit"
                :disabled="form.processing"
                class="w-full sm:w-auto"
              >
                <CheckCircleIcon class="h-5 w-5 mr-2" />
                {{ isDraft ? 'Submit Application' : (existingApplication ? 'Update Application' : 'Submit Application') }}
              </PrimaryButton>
            </div>

            <p class="text-xs text-gray-500 mt-4 text-center">
              Your application will be automatically saved every 30 seconds
            </p>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
