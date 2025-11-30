<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import axios from 'axios'
import { FIELDS_OF_STUDY } from '@/Constants/profileData'

// Import necessary components
import Modal from '@/Components/Modal.vue'
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue'
import FlowButton from '@/Components/Rhythmic/FlowButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import DateInput from '@/Components/DateInput.vue'
import InputError from '@/Components/InputError.vue'
import Checkbox from '@/Components/Checkbox.vue'
import { AcademicCapIcon, ChevronUpIcon, ChevronDownIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  educations: Array,
  degrees: Array,
  fieldsOfStudy: Array,
  universities: Array,
  countries: Array,
})

// Use database data from props
const degreesList = computed(() => props.degrees || [])

const fieldsOfStudyList = computed(() => props.fieldsOfStudy && props.fieldsOfStudy.length > 0 
  ? props.fieldsOfStudy 
  : FIELDS_OF_STUDY.map(f => ({ id: f, name: f }))
)

const countriesList = computed(() => props.countries || [])

// Collapsible state
const isOpen = ref(true)
const toggle = () => {
  isOpen.value = !isOpen.value
}

// --- State Management ---
const educationList = ref(props.educations || [])
const isLoading = ref(false)
const showModal = ref(false)
const isEditMode = ref(false)
const currentEducationId = ref(null)

// --- Form Definition ---
const form = ref({
  institution_name: '',
  degree: '',
  field_of_study: '',
  start_date: '',
  end_date: '',
  country: '',
  city: '',
  is_completed: false,
  gpa_or_grade: '',
  language_of_instruction: '',
  courses_completed: '',
  honors_awards: '',
})

const formErrors = ref({})
const isProcessing = ref(false)

// --- Duplicate Detection Logic ---
const duplicateWarning = ref('')
const potentialDuplicate = ref(null)

// Normalize a string for comparison (case-insensitive, trimmed)
const norm = (val) => (val || '').toString().trim().toLowerCase()

// Build a signature for current form state (only fields relevant to uniqueness)
const currentSignature = computed(() => {
  if (!form.value.start_date) return null
  const degreePart = norm(form.value.degree)
  const instPart = norm(form.value.institution_name)
  return `${degreePart}::${instPart}::${form.value.start_date}`
})

const existingSignatures = computed(() => {
  return (educationList.value || []).filter(e => e && e.degree && e.institution_name && e.start_date).map(e => {
    const degreePart = norm(e.degree)
    const instPart = norm(e.institution_name)
    return {
      id: e.id,
      signature: `${degreePart}::${instPart}::${e.start_date?.substring(0,10)}`,
      record: e,
    }
  })
})

watch([currentSignature, educationList], () => {
  duplicateWarning.value = ''
  potentialDuplicate.value = null
  if (!currentSignature.value) return
  const hit = existingSignatures.value.find(s => s.signature === currentSignature.value && (!isEditMode.value || s.id !== currentEducationId.value))
  if (hit) {
    potentialDuplicate.value = hit.record
    duplicateWarning.value = `You already added this education (${hit.record.degree} at ${hit.record.institution_name}) starting on ${hit.record.start_date}.`;
  }
})

// --- API Interaction ---
const fetchEducation = async () => {
  // Data is now passed via props, so we can remove the direct API call
  // and just update the local ref if needed, or rely on Inertia's reactivity.
}

// --- Modal Controls ---
const openAddModal = () => {
  form.value = {
    institution_name: '',
    degree: '',
    field_of_study: '',
    start_date: '',
    end_date: '',
    country: '',
    city: '',
    is_completed: false,
    gpa_or_grade: '',
    language_of_instruction: '',
    courses_completed: '',
    honors_awards: '',
  }
  formErrors.value = {}
  isEditMode.value = false
  currentEducationId.value = null
  showModal.value = true
}

const openEditModal = (education) => {
  if (!education) {
    console.error('Education data is undefined')
    return
  }
  form.value = {
    institution_name: education.institution_name || '',
    degree: education.degree || '',
    field_of_study: education.field_of_study || '',
    start_date: education.start_date ? education.start_date.substring(0, 10) : '',
    end_date: education.end_date ? education.end_date.substring(0, 10) : '',
    country: education.country || '',
    city: education.city || '',
    is_completed: education.is_completed || false,
    gpa_or_grade: education.gpa_or_grade || '',
    language_of_instruction: education.language_of_instruction || '',
    courses_completed: education.courses_completed || '',
    honors_awards: education.honors_awards || '',
  }
  formErrors.value = {}
  isEditMode.value = true
  currentEducationId.value = education.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.value = {
    institution_name: '',
    degree: '',
    field_of_study: '',
    start_date: '',
    end_date: '',
    country: '',
    city: '',
    is_completed: false,
    gpa_or_grade: '',
    language_of_instruction: '',
    courses_completed: '',
    honors_awards: '',
  }
  formErrors.value = {}
}

// --- Form Submission ---
const submit = async () => {
  if (duplicateWarning.value) {
    const proceed = window.confirm(duplicateWarning.value + '\n\nSubmit anyway?')
    if (!proceed) return
  }
  
  if (form.value.is_completed === false) {
    form.value.end_date = null
  }

  isProcessing.value = true
  formErrors.value = {}

  try {
    if (isEditMode.value) {
      await axios.put(route('profile.education.update', currentEducationId.value), form.value)
    } else {
      await axios.post(route('profile.education.store'), form.value)
    }
    
    closeModal()
    router.reload({ only: ['educations'] })
  } catch (error) {
    if (error.response && error.response.data.errors) {
      formErrors.value = error.response.data.errors
    } else {
      console.error('Form submission error:', error)
    }
  } finally {
    isProcessing.value = false
  }
}

// --- Delete ---
const confirmDelete = async (educationId) => {
  if (window.confirm('Are you sure you want to delete this education record?')) {
    try {
      await axios.delete(route('profile.education.destroy', educationId))
      router.reload({ only: ['educations'] })
    } catch (error) {
      console.error('Delete error:', error)
    }
  }
}

// --- Lifecycle Hook ---
onMounted(() => {
  // No need to fetch, data is in props.
})

watch(() => props.educations, (newVal) => {
  educationList.value = newVal || []
})

// Utility function to format dates
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>

<template>
  <section>
    <header class="mb-rhythm-lg">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-growth-500 flex items-center justify-center shadow-rhythmic-md">
            <AcademicCapIcon class="w-6 h-6 text-white" />
          </div>
          <div>
            <h2 class="font-display font-bold text-xl text-gray-800">Education History</h2>
            <p class="text-xs text-gray-500">Academic qualifications and degrees</p>
          </div>
        </div>
        <FlowButton
          @click="openAddModal"
          variant="primary"
        >
          <template #icon-left>
            <PlusIcon class="w-5 h-5" />
          </template>
          Add Education
        </FlowButton>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      <p class="mt-2 text-sm text-gray-600">Loading education records...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="!educationList || educationList.length === 0" class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
      <AcademicCapIcon class="mx-auto h-16 w-16 md:h-20 md:w-20 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No education records</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by adding your first qualification</p>
      <div class="mt-6">
        <SecondaryButton @click="openAddModal" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
          <PlusIcon class="w-5 h-5 md:w-6 md:h-6 mr-2" />
          Add Education
        </SecondaryButton>
      </div>
    </div>

    <!-- Education List -->
    <div v-else class="space-y-4">
      <div
        v-for="edu in educationList"
        :key="edu.id"
        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Card Header -->
        <div class="h-1 bg-heritage-500"></div>
        
        <!-- Card Content -->
        <div class="p-4 sm:p-6">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <AcademicCapIcon class="w-6 h-6 md:w-7 md:h-7 text-purple-600 dark:text-purple-400 flex-shrink-0" />
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">
                  {{ edu.degree_name || edu.custom_degree || 'Degree' }}
                </h3>
              </div>
              <p class="text-sm sm:text-base font-semibold text-purple-600 dark:text-purple-400">
                {{ edu.institution_name || edu.custom_university || 'Institution' }}
              </p>
            </div>
            
            <span
              v-if="edu.is_current"
              class="flex-shrink-0 inline-flex items-center px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 text-xs font-semibold rounded-full"
            >
              <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
              Current
            </span>
          </div>

          <!-- Meta Info -->
          <div class="space-y-2 mb-3">
            <div v-if="edu.field_of_study" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span>{{ edu.field_of_study }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>{{ formatDate(edu.start_date) }} - {{ edu.is_current ? 'Present' : formatDate(edu.end_date) }}</span>
            </div>
          </div>

          <!-- Result/Grade -->
          <div v-if="edu.result || edu.grade" class="bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 p-3 rounded-r mb-3">
            <h4 class="text-xs font-semibold text-purple-800 dark:text-purple-300 mb-1 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              RESULT
            </h4>
            <p class="text-sm text-purple-700 dark:text-purple-200">{{ edu.result || edu.grade }}</p>
          </div>

          <!-- Description -->
          <p v-if="edu.description" class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ edu.description }}
          </p>
        </div>

        <!-- Card Footer Actions -->
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
          <div class="flex gap-2">
            <button
              @click="openEditModal(edu)"
              class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm transition-colors"
            >
              <PencilSquareIcon class="h-4 w-4" />
              <span>Edit</span>
            </button>
            <button
              @click="confirmDelete(edu.id)"
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
        @click="openAddModal"
        class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-heritage-600 hover:bg-heritage-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-base"
      >
        <PlusIcon class="h-5 w-5" />
        <span>ADD MORE EDUCATION</span>
      </button>
    </div>

    <!-- Modal -->
    <Modal :show="showModal" @close="closeModal">
      <div class="p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 md:w-14 md:h-14 bg-purple-100 dark:bg-purple-900/30 rounded-lg md:rounded-xl flex items-center justify-center">
            <AcademicCapIcon class="w-7 h-7 md:w-8 md:h-8 text-purple-600 dark:text-purple-400" />
          </div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ isEditMode ? 'Edit Education Record' : 'Add New Education' }}
          </h2>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
          <!-- Duplicate Warning Banner -->
          <div v-if="duplicateWarning" class="p-3 rounded-lg bg-orange-50 border border-orange-300 text-sm text-orange-800 dark:bg-orange-900/20 dark:border-orange-700 dark:text-orange-300">
            <strong>Possible Duplicate:</strong> {{ duplicateWarning }}
          </div>
                    <!-- Institute / University -->
          <div>
            <InputLabel for="institution_name" value="Institute / University *" />
            <TextInput
              id="institution_name"
              v-model="form.value.institution_name"
              type="text"
              class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation"
              placeholder="e.g., University of Dhaka, BUET, Harvard University, NSU"
              required
            />
            <p class="mt-1 text-xs text-gray-500">
              Enter the full name of your educational institution
            </p>
            <InputError class="mt-2" :message="formErrors.institution_name?.[0]" />
          </div>

          <!-- Degree Level and Degree -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <InputLabel for="degree" value="Degree / Qualification *" />
              <select 
                id="degree" 
                v-model="form.value.degree" 
                class="mt-1 block w-full py-3 px-4 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm touch-manipulation"
                required
              >
                <option value="">Select Degree</option>
                <option v-for="degree in degreesList" :key="degree.id" :value="degree.name">
                  {{ degree.name }}
                </option>
              </select>
              <p class="mt-1 text-xs text-gray-500">
                e.g., Bachelor of Science, MBA, Diploma
              </p>
              <InputError class="mt-2" :message="formErrors.degree?.[0]" />
            </div>

            <div>
              <InputLabel for="field_of_study" value="Field of Study" />
                <select id="field_of_study" v-model="form.value.field_of_study" class="mt-1 block w-full py-3 px-4 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm touch-manipulation">
                  <option value="">
                    Select Field of Study
                  </option>
                  <option v-for="field in fieldsOfStudyList" :key="field.id" :value="field.name">
                    {{ field.name }}
                </option>
              </select>
                <InputError class="mt-2" :message="formErrors.field_of_study?.[0]" />
            </div>
          </div>

          <!-- Result/Grade -->
          <div>
            <InputLabel for="gpa_or_grade" value="Result / Grade" />
            <TextInput
              id="gpa_or_grade"
              v-model="form.value.gpa_or_grade"
              type="text"
              class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation"
              placeholder="e.g., CGPA: 3.8/4.0, First Class, 85%"
            />
            <p class="mt-1 text-xs text-gray-500">
              Your grade, CGPA, percentage, or class (e.g., "CGPA: 3.8/4.0", "First Class", "85%")
            </p>
            <InputError class="mt-2" :message="formErrors.gpa_or_grade?.[0]" />
          </div>

          <!-- Dates -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <InputLabel for="start_date" value="Start Date *" />
              <DateInput
                id="start_date"
                v-model="form.value.start_date"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="formErrors.start_date?.[0]" />
            </div>
            <div>
              <InputLabel for="end_date" value="End Date" />
              <DateInput
                id="end_date"
                v-model="form.value.end_date"
                class="mt-1 block w-full"
                :disabled="form.value.currently_studying"
              />
              <p class="mt-1 text-xs text-gray-500">
                Leave empty if currently studying
              </p>
              <InputError class="mt-2" :message="formErrors.end_date?.[0]" />
            </div>
          </div>

          <!-- Completed Status -->
          <div class="flex items-center">
            <Checkbox
              id="is_completed"
              v-model:checked="form.value.is_completed"
            />
            <InputLabel for="is_completed" value="I have completed this education" class="ml-2 cursor-pointer" />
          </div>

          <!-- Location -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <InputLabel for="country" value="Country" />
              <TextInput
                id="country"
                v-model="form.value.country"
                type="text"
                class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation"
                placeholder="Bangladesh, USA, UK, etc."
              />
              <InputError class="mt-2" :message="formErrors.country?.[0]" />
            </div>
            <div>
              <InputLabel for="city" value="City" />
              <TextInput
                id="city"
                v-model="form.value.city"
                type="text"
                class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation"
                placeholder="Dhaka, Boston, London, etc."
              />
              <InputError class="mt-2" :message="formErrors.city?.[0]" />
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <SecondaryButton type="button" @click="closeModal" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
              Cancel
            </SecondaryButton>
            <PrimaryButton :disabled="isProcessing" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
              <span v-if="isProcessing">Saving...</span>
              <span v-else>{{ isEditMode ? 'Update Education' : 'Save Education' }}</span>
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </section>
</template>


