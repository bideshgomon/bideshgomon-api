<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { COUNTRIES } from '@/Constants/profileData'

// Import components
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import DateInput from '@/Components/DateInput.vue'
import InputError from '@/Components/InputError.vue'
import Checkbox from '@/Components/Checkbox.vue'
import { BriefcaseIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  workExperiences: Array,
})

// State
const workList = ref(props.workExperiences || [])
const isLoading = ref(false)
const showModal = ref(false)
const isEditMode = ref(false)
const currentWorkId = ref(null)

// Form
const form = useForm({
  company_name: '',
  job_title: '',
  employment_type: '',
  job_category: '',
  start_date: '',
  end_date: '',
  is_current_employment: false,
  country: 'Bangladesh',
  city: '',
  responsibilities: '',
  achievements: '',
  salary_range: '',
  reason_for_leaving: '',
})

// Define employment types locally
const employmentTypes = [
  'Full-time',
  'Part-time',
  'Contract',
  'Freelance',
  'Internship',
  'Temporary'
]

// Define job categories locally
const jobCategories = [
  'Information Technology',
  'Engineering',
  'Healthcare',
  'Education',
  'Finance',
  'Marketing',
  'Sales',
  'Customer Service',
  'Administration',
  'Manufacturing',
  'Construction',
  'Hospitality',
  'Other'
]

const countries = COUNTRIES || ['Bangladesh', 'Saudi Arabia', 'UAE', 'Qatar', 'Kuwait', 'Oman', 'Malaysia', 'Singapore']

// Fetch work experiences
const fetchWorkExperiences = async () => {
  isLoading.value = true
  try {
    const response = await fetch(route('api.profile.work-experience.index'))
    if (response.ok) {
      workList.value = await response.json()
    }
  } catch (error) {
    console.error('Failed to fetch work experiences:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchWorkExperiences()
})

// Open modal
const openModal = (work = null) => {
  if (work) {
    isEditMode.value = true
    currentWorkId.value = work.id
    form.company_name = work.company_name || ''
    form.job_title = work.job_title || ''
    form.employment_type = work.employment_type || ''
    form.job_category = work.job_category || ''
    form.start_date = work.start_date || ''
    form.end_date = work.end_date || ''
    form.is_current_employment = work.is_current_employment || false
    form.country = work.country || 'Bangladesh'
    form.city = work.city || ''
    form.responsibilities = work.responsibilities || ''
    form.achievements = work.achievements || ''
    form.salary_range = work.salary_range || ''
    form.reason_for_leaving = work.reason_for_leaving || ''
  } else {
    isEditMode.value = false
    currentWorkId.value = null
    form.reset()
    form.country = 'Bangladesh'
  }
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

// Submit form
const submit = () => {
  const url = isEditMode.value
    ? route('api.profile.work-experience.update', currentWorkId.value)
    : route('api.profile.work-experience.store')

  const method = isEditMode.value ? 'put' : 'post'

  form[method](url, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
      fetchWorkExperiences()
    },
  })
}

// Delete work experience
const deleteWork = (id) => {
  if (confirm('Are you sure you want to delete this work experience?')) {
    form.delete(route('api.profile.work-experience.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        fetchWorkExperiences()
      },
    })
  }
}

// Calculate duration
const calculateDuration = (startDate, endDate, isCurrent) => {
  if (!startDate) return ''
  
  const start = new Date(startDate)
  const end = isCurrent ? new Date() : new Date(endDate || new Date())
  
  const months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth())
  const years = Math.floor(months / 12)
  const remainingMonths = months % 12
  
  if (years > 0 && remainingMonths > 0) {
    return `${years} year${years > 1 ? 's' : ''}, ${remainingMonths} month${remainingMonths > 1 ? 's' : ''}`
  } else if (years > 0) {
    return `${years} year${years > 1 ? 's' : ''}`
  } else {
    return `${remainingMonths} month${remainingMonths > 1 ? 's' : ''}`
  }
}

// Format date for display
const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' })
}

// Sort by start date (most recent first)
const sortedWorkList = computed(() => {
  return [...workList.value].sort((a, b) => {
    if (a.is_current_employment) return -1
    if (b.is_current_employment) return 1
    return new Date(b.start_date) - new Date(a.start_date)
  })
})
</script>

<template>
  <section>
    <header class="mb-6">
      <div class="flex flex-col gap-4">
        <div>
          <h2 class="text-lg font-medium text-gray-900">Work Experience</h2>
          <p class="mt-1 text-sm text-gray-600">
            Add your employment history to showcase your professional background.
          </p>
        </div>
        <button
          @click="openModal()"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
        >
          <PlusIcon class="h-5 w-5" />
          <span>ADD EXPERIENCE</span>
        </button>
      </div>
    </header>

    <!-- Work Experience List -->
    <div v-if="isLoading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      <p class="mt-2 text-sm text-gray-600">Loading work experiences...</p>
    </div>

    <div v-else-if="sortedWorkList.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
      <BriefcaseIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No work experience</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by adding your first work experience.</p>
      <div class="mt-6">
        <SecondaryButton @click="openModal()">
          <PlusIcon class="h-5 w-5 mr-2" />
          Add Work Experience
        </SecondaryButton>
      </div>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="(work, index) in sortedWorkList"
        :key="work.id"
        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Card Header -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
          <div class="flex items-start justify-between gap-3 mb-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <BriefcaseIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" />
                <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ work.job_title }}</h3>
              </div>
              <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ work.company_name }}</p>
            </div>
            <span
              v-if="work.is_current_employment"
              class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 text-xs font-semibold rounded-full flex-shrink-0"
            >
              <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
              Current
            </span>
          </div>
        </div>

        <!-- Card Body -->
        <div class="p-4 space-y-3">
          <!-- Location & Date Info -->
          <div class="space-y-2">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ work.city }}, {{ work.country }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>{{ formatDate(work.start_date) }} - {{ work.is_current_employment ? 'Present' : formatDate(work.end_date) }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ calculateDuration(work.start_date, work.end_date, work.is_current_employment) }}</span>
            </div>
          </div>
          
          <!-- Tags -->
          <div class="flex flex-wrap gap-2">
            <span
              v-if="work.employment_type"
              class="inline-flex items-center px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full"
            >
              {{ work.employment_type }}
            </span>
            <span
              v-if="work.job_category"
              class="inline-flex items-center px-2.5 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium rounded-full"
            >
              {{ work.job_category }}
            </span>
          </div>
          
          <!-- Responsibilities -->
          <div v-if="work.responsibilities" class="pt-2">
            <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Responsibilities</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed">{{ work.responsibilities }}</p>
          </div>
          
          <!-- Achievements -->
          <div v-if="work.achievements" class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3 rounded-r">
            <h4 class="text-xs font-semibold text-green-800 dark:text-green-300 mb-1 flex items-center gap-1.5 uppercase tracking-wide">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              Achievements
            </h4>
            <p class="text-sm text-green-700 dark:text-green-200 whitespace-pre-line leading-relaxed">{{ work.achievements }}</p>
          </div>
        </div>

        <!-- Card Footer with Actions -->
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
          <div class="flex gap-2">
            <button
              @click="openModal(work)"
              class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm transition-colors"
            >
              <PencilSquareIcon class="h-4 w-4" />
              <span>Edit</span>
            </button>
            <button
              @click="deleteWork(work.id)"
              class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg font-medium text-sm transition-colors"
            >
              <TrashIcon class="h-4 w-4" />
              <span>Delete</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Add More Button -->
      <button
        @click="openModal()"
        class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-base"
      >
        <PlusIcon class="h-5 w-5" />
        <span>ADD MORE EXPERIENCE</span>
      </button>
    </div>

    <!-- Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="2xl">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ isEditMode ? 'Edit Work Experience' : 'Add Work Experience' }}
        </h3>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Company Name & Job Title -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="company_name" value="Company Name *" />
              <TextInput
                id="company_name"
                v-model="form.company_name"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.company_name" />
            </div>

            <div>
              <InputLabel for="job_title" value="Job Title *" />
              <TextInput
                id="job_title"
                v-model="form.job_title"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.job_title" />
            </div>
          </div>

          <!-- Employment Type & Job Category -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="employment_type" value="Employment Type" />
              <select
                id="employment_type"
                v-model="form.employment_type"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
              >
                <option value="">Select Type</option>
                <option v-for="type in employmentTypes" :key="type" :value="type">{{ type }}</option>
              </select>
              <InputError class="mt-2" :message="form.errors.employment_type" />
            </div>

            <div>
              <InputLabel for="job_category" value="Job Category" />
              <select
                id="job_category"
                v-model="form.job_category"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
              >
                <option value="">Select Category</option>
                <option v-for="category in jobCategories" :key="category" :value="category">{{ category }}</option>
              </select>
              <InputError class="mt-2" :message="form.errors.job_category" />
            </div>
          </div>

          <!-- Start Date & End Date -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="start_date" value="Start Date *" />
              <DateInput
                id="start_date"
                v-model="form.start_date"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.start_date" />
            </div>

            <div>
              <InputLabel for="end_date" :value="form.is_current_employment ? 'End Date (Optional)' : 'End Date *'" />
              <DateInput
                id="end_date"
                v-model="form.end_date"
                class="mt-1 block w-full"
                :disabled="form.is_current_employment"
                :required="!form.is_current_employment"
              />
              <InputError class="mt-2" :message="form.errors.end_date" />
            </div>
          </div>

          <!-- Currently Working Here -->
          <div class="flex items-center">
            <Checkbox
              id="is_current_employment"
              v-model:checked="form.is_current_employment"
              @update:checked="val => { if (val) form.end_date = '' }"
            />
            <InputLabel for="is_current_employment" value="I currently work here" class="ml-2" />
          </div>

          <!-- Country & City -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="country" value="Country *" />
              <select
                id="country"
                v-model="form.country"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
              >
                <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
              </select>
              <InputError class="mt-2" :message="form.errors.country" />
            </div>

            <div>
              <InputLabel for="city" value="City" />
              <TextInput
                id="city"
                v-model="form.city"
                type="text"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.city" />
            </div>
          </div>

          <!-- Responsibilities -->
          <div>
            <InputLabel for="responsibilities" value="Key Responsibilities" />
            <textarea
              id="responsibilities"
              v-model="form.responsibilities"
              rows="3"
              class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
              placeholder="Describe your main responsibilities and duties..."
            ></textarea>
            <InputError class="mt-2" :message="form.errors.responsibilities" />
          </div>

          <!-- Achievements -->
          <div>
            <InputLabel for="achievements" value="Achievements & Accomplishments" />
            <textarea
              id="achievements"
              v-model="form.achievements"
              rows="2"
              class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
              placeholder="Notable achievements, awards, or accomplishments..."
            ></textarea>
            <InputError class="mt-2" :message="form.errors.achievements" />
          </div>

          <!-- Salary Range & Reason for Leaving -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="salary_range" value="Salary Range (Optional)" />
              <TextInput
                id="salary_range"
                v-model="form.salary_range"
                type="text"
                class="mt-1 block w-full"
                placeholder="e.g., 50,000 - 60,000 BDT"
              />
              <InputError class="mt-2" :message="form.errors.salary_range" />
            </div>

            <div v-if="!form.is_current_employment">
              <InputLabel for="reason_for_leaving" value="Reason for Leaving" />
              <TextInput
                id="reason_for_leaving"
                v-model="form.reason_for_leaving"
                type="text"
                class="mt-1 block w-full"
                placeholder="e.g., Career growth"
              />
              <InputError class="mt-2" :message="form.errors.reason_for_leaving" />
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t">
            <SecondaryButton @click="closeModal" type="button">
              Cancel
            </SecondaryButton>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              {{ isEditMode ? 'Update' : 'Save' }} Work Experience
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </section>
</template>
