<script setup>
import { ref, computed } from 'vue'
import {
  GlobeAltIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  DocumentTextIcon,
  CalendarIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue'
import FlowButton from '@/Components/Rhythmic/FlowButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import DateInput from '@/Components/DateInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Modal from '@/Components/Modal.vue'
import { useForm } from '@inertiajs/vue3'
import { COMMON_LANGUAGES, LANGUAGE_TEST_TYPES, LANGUAGE_PROFICIENCY_LEVELS } from '@/Constants/profileData'

// Collapsible state
const isOpen = ref(true)
const toggle = () => {
  isOpen.value = !isOpen.value
}

// Props received from Edit.vue
const props = defineProps({
  userLanguages: Array, // Array of user's current languages with test details
  languages: Array, // Array of all available languages (id, name)
  languageTests: Array, // Array of language tests (IELTS, TOEFL, etc.)
})

// Use constants if props not provided
const languagesList = computed(() => props.languages && props.languages.length > 0 
  ? props.languages 
  : COMMON_LANGUAGES.map(l => ({ id: l, name: l }))
)

const languageTestsList = computed(() => props.languageTests && props.languageTests.length > 0 
  ? props.languageTests 
  : LANGUAGE_TEST_TYPES.map(t => ({ id: t, name: t }))
)

const proficiencyLevels = LANGUAGE_PROFICIENCY_LEVELS

const confirmingDeletion = ref(false)
const languageToDelete = ref(null)
const editingLanguage = ref(null)

const form = useForm({
  id: null, // For editing
  language_id: null,
  proficiency_level: '',
  language_test_id: null, // NEW: Test type (IELTS, TOEFL, etc.)
  overall_score: null, // NEW: Overall band/score
  reading_score: null, // NEW: Reading band/score
  writing_score: null, // NEW: Writing band/score
  listening_score: null, // NEW: Listening band/score
  speaking_score: null, // NEW: Speaking band/score
  test_date: null, // NEW: Test taken date
  expiry_date: null, // NEW: Certificate expiry date
  certificate: null, // NEW: Certificate file
  test_score: '', // Keep for backward compatibility
})

// Function to open modal for adding/editing
const openEditModal = (language = null) => {
  if (language) {
    // Editing existing
    editingLanguage.value = language
    form.id = language.id
    form.language_id = language.language_id
    form.proficiency_level = language.proficiency_level || language.proficiency
    form.language_test_id = language.language_test_id
    form.overall_score = language.overall_score
    form.reading_score = language.reading_score
    form.writing_score = language.writing_score
    form.listening_score = language.listening_score
    form.speaking_score = language.speaking_score
    form.test_date = language.test_date
    form.expiry_date = language.expiry_date
    form.test_score = language.test_score
    form.certificate = null // Don't pre-fill file input
  } else {
    // Adding new
    editingLanguage.value = {} // Indicate adding new
    form.reset() // Clear form fields
    form.id = null
  }
}

// Function to close modal
const closeModal = () => {
  editingLanguage.value = null
  form.reset()
  form.clearErrors()
}

// Function to handle file selection
const handleFileChange = event => {
  const file = event.target.files[0]
  form.certificate = file || null
}

// Function to save (add or update)
const saveLanguage = () => {
  // Use FormData for file upload
  const formData = new FormData()

  formData.append('language_id', form.language_id)
  formData.append('proficiency_level', form.proficiency_level)

  if (form.language_test_id) {
    formData.append('language_test_id', form.language_test_id)
  }
  if (form.overall_score) {
    formData.append('overall_score', form.overall_score)
  }
  if (form.reading_score) {
    formData.append('reading_score', form.reading_score)
  }
  if (form.writing_score) {
    formData.append('writing_score', form.writing_score)
  }
  if (form.listening_score) {
    formData.append('listening_score', form.listening_score)
  }
  if (form.speaking_score) {
    formData.append('speaking_score', form.speaking_score)
  }
  if (form.test_date) {
    formData.append('test_date', form.test_date)
  }
  if (form.expiry_date) {
    formData.append('expiry_date', form.expiry_date)
  }
  if (form.test_score) {
    formData.append('test_score', form.test_score)
  }
  if (form.certificate) {
    formData.append('certificate', form.certificate)
  }

  if (form.id) {
    // Add _method for Laravel to treat as PATCH
    formData.append('_method', 'PATCH')
    form
      .transform(() => formData)
      .post(route('api.profile.languages.update', form.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        forceFormData: true,
      })
  } else {
    form
      .transform(() => formData)
      .post(route('api.profile.languages.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        forceFormData: true,
      })
  }
}

// --- Deletion ---
const confirmDeletion = language => {
  languageToDelete.value = language
  confirmingDeletion.value = true
}

const closeDeleteModal = () => {
  confirmingDeletion.value = false
  languageToDelete.value = null
}

const deleteLanguage = () => {
  if (!languageToDelete.value) {
    return
  }

  useForm({}).delete(route('api.profile.languages.destroy', languageToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => closeDeleteModal(),
    onError: () => closeDeleteModal(),
  })
}

// Helper to get language name from ID
const getLanguageName = id => {
  const lang = props.languages.find(l => l.id === id)
  return lang ? lang.name : 'Unknown'
}

// Helper to get language test name from ID
const getTestName = id => {
  if (!id || !props.languageTests) {
    return null
  }
  const test = props.languageTests.find(t => t.id === id)
  return test ? test.name : null
}

// Check if certificate is expiring soon (within 6 months)
const isExpiringSoon = expiryDate => {
  if (!expiryDate) {
    return false
  }
  const expiry = new Date(expiryDate)
  const sixMonthsFromNow = new Date()
  sixMonthsFromNow.setMonth(sixMonthsFromNow.getMonth() + 6)
  return expiry < sixMonthsFromNow && expiry > new Date()
}

// Check if certificate is expired
const isExpired = expiryDate => {
  if (!expiryDate) {
    return false
  }
  return new Date(expiryDate) < new Date()
}

// Format date for display - DD/MM/YYYY format for Bangladesh
const formatDate = dateString => {
  if (!dateString) {
    return null
  }
  const date = new Date(dateString)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${day}/${month}/${year}`
}
</script>

<template>
  <section>
    <header class="mb-rhythm-lg">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-ocean-500 flex items-center justify-center shadow-rhythmic-md">
            <GlobeAltIcon class="w-6 h-6 text-white" />
          </div>
          <div>
            <h2 class="font-display font-bold text-xl text-gray-800">Language Proficiency</h2>
            <p class="text-xs text-gray-500">
              Languages with test scores and certificates
            </p>
          </div>
        </div>
        <FlowButton @click="openEditModal(null)" variant="primary">
          <template #icon-left><PlusIcon class="w-4 h-4" /></template>
          <span class="hidden sm:inline">Add Language</span>
          <span class="sm:hidden">Add</span>
        </FlowButton>
      </div>
    </header>

    <!-- Empty State -->
    <div
      v-if="!userLanguages || userLanguages.length === 0"
      class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700"
    >
      <GlobeAltIcon class="mx-auto h-16 w-16 md:h-20 md:w-20 text-gray-400 dark:text-gray-600" />
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
        No languages added yet
      </h3>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Start adding languages you speak
      </p>
      <div class="mt-6">
        <SecondaryButton @click="openEditModal(null)" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
          <PlusIcon class="h-5 w-5 md:h-6 md:w-6 mr-2" />
          Add Language
        </SecondaryButton>
      </div>
    </div>

    <!-- Languages List -->
    <div v-else class="space-y-4">
      <div
        v-for="lang in userLanguages"
        :key="lang.id"
        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="h-1 bg-ocean-500"></div>
        <div class="p-4 sm:p-6">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <GlobeAltIcon class="w-6 h-6 md:w-7 md:h-7 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">
                  {{ getLanguageName(lang.language_id) }}
                </h3>
              </div>
              <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-medium rounded-full">
                {{ lang.proficiency_level || lang.proficiency }}
              </span>
            </div>

            <span
              v-if="lang.language_test_id"
              class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200 text-xs font-medium rounded-full"
            >
              <AcademicCapIcon class="w-3.5 h-3.5" />
              {{ getTestName(lang.language_test_id) }}
            </span>
          </div>

          <!-- Test Scores Grid -->
          <div
            v-if="lang.overall_score || lang.reading_score || lang.writing_score || lang.listening_score || lang.speaking_score"
            class="grid grid-cols-2 sm:grid-cols-5 gap-2 mb-3"
          >
            <div v-if="lang.overall_score" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-2 border border-blue-200 dark:border-blue-700">
              <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Overall</div>
              <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ lang.overall_score }}</div>
            </div>
            <div v-if="lang.reading_score" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 border border-gray-200 dark:border-gray-600">
              <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Reading</div>
              <div class="text-lg font-bold text-gray-900 dark:text-white">{{ lang.reading_score }}</div>
            </div>
            <div v-if="lang.writing_score" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 border border-gray-200 dark:border-gray-600">
              <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Writing</div>
              <div class="text-lg font-bold text-gray-900 dark:text-white">{{ lang.writing_score }}</div>
            </div>
            <div v-if="lang.listening_score" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 border border-gray-200 dark:border-gray-600">
              <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Listening</div>
              <div class="text-lg font-bold text-gray-900 dark:text-white">{{ lang.listening_score }}</div>
            </div>
            <div v-if="lang.speaking_score" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 border border-gray-200 dark:border-gray-600">
              <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Speaking</div>
              <div class="text-lg font-bold text-gray-900 dark:text-white">{{ lang.speaking_score }}</div>
            </div>
          </div>

          <!-- Test Date Info -->
          <div v-if="lang.test_date || lang.expiry_date" class="space-y-1 mb-3 text-sm text-gray-600 dark:text-gray-400">
            <div v-if="lang.test_date" class="flex items-center gap-1.5">
              <CalendarIcon class="w-4 h-4 flex-shrink-0" />
              <span>Test Date: {{ formatDate(lang.test_date) }}</span>
            </div>
            <div v-if="lang.expiry_date" class="flex items-center gap-1.5">
              <CalendarIcon class="w-4 h-4 flex-shrink-0" />
              <span>Expires: {{ formatDate(lang.expiry_date) }}</span>
              <span v-if="isExpiringSoon(lang.expiry_date)" class="text-orange-600 dark:text-orange-400 font-semibold">(Soon)</span>
              <span v-if="isExpired(lang.expiry_date)" class="text-red-600 dark:text-red-400 font-semibold">(Expired)</span>
            </div>
          </div>

          <!-- Certificate Badge -->
          <div v-if="lang.file_path" class="inline-flex items-center gap-1.5 text-sm text-green-600 dark:text-green-400">
            <DocumentTextIcon class="w-4 h-4" />
            <span>Certificate Uploaded</span>
          </div>
        </div>

        <!-- Card Footer Actions -->
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
          <div class="flex gap-2">
            <button
              @click="openEditModal(lang)"
              class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm transition-colors"
            >
              <PencilSquareIcon class="h-4 w-4" />
              <span>Edit</span>
            </button>
            <button
              @click="confirmDelete(lang.id)"
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
        @click="openEditModal(null)"
        class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-ocean-600 hover:bg-ocean-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-base"
      >
        <PlusIcon class="h-5 w-5" />
        <span>ADD MORE LANGUAGES</span>
      </button>
    </div>

    <!-- Add/Edit Language Modal -->
    <Modal :show="editingLanguage !== null" @close="closeModal" max-width="2xl">
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl">
      <!-- Modal Header -->
      <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div
          class="w-12 h-12 bg-ocean-600 rounded-xl flex items-center justify-center shadow-lg"
        >
          <GlobeAltIcon class="w-7 h-7 text-white" />
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ form.id ? 'Edit Language' : 'Add New Language' }}
        </h2>
      </div>

      <form class="space-y-6" @submit.prevent="saveLanguage">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <InputLabel for="language_id" value="Language *" />
            <select
              id="language_id"
              v-model="form.language_id"
              class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg dark:bg-gray-700 dark:text-white"
              required
            >
              <option :value="null" disabled>Select a language</option>
              <option v-for="langOption in languagesList" :key="langOption.id" :value="langOption.id">
                {{ langOption.name }}
              </option>
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Choose the language you want to add
            </p>
            <InputError class="mt-2" :message="form.errors.language_id" />
          </div>

          <div>
            <InputLabel for="proficiency_level" value="Proficiency Level *" />
            <select
              id="proficiency_level"
              v-model="form.proficiency_level"
              class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg dark:bg-gray-700 dark:text-white"
              required
            >
              <option value="" disabled>Select proficiency</option>
              <option value="Beginner">Beginner</option>
              <option value="Intermediate">Intermediate</option>
              <option value="Advanced">Advanced</option>
              <option value="Fluent">Fluent</option>
              <option value="Native">Native</option>
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              How well do you speak this language?
            </p>
            <InputError
              class="mt-2"
              :message="form.errors.proficiency_level || form.errors.proficiency"
            />
          </div>
        </div>

        <!-- Language Test Section -->
        <div
          class="p-4 bg-sky-50 dark:bg-sky-900/20 rounded-lg border border-sky-200 dark:border-sky-800"
        >
          <div class="flex items-center gap-2 mb-4">
            <AcademicCapIcon class="w-5 h-5 text-cyan-600 dark:text-cyan-400" />
            <h3 class="font-semibold text-gray-900 dark:text-white">
              Language Test Details (Optional)
            </h3>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Add your language test scores (IELTS, TOEFL, etc.) to strengthen your profile
          </p>

          <div class="space-y-4">
            <!-- Test Type -->
            <div>
              <InputLabel for="language_test_id" value="Test Type" />
              <select
                id="language_test_id"
                v-model="form.language_test_id"
                class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-cyan-500 focus:ring-cyan-500 rounded-lg dark:bg-gray-700 dark:text-white"
              >
                <option :value="null">No test taken / Not applicable</option>
                <optgroup v-if="languageTestsList && languageTestsList.length > 0" label="Available Tests">
                  <option v-for="test in languageTestsList" :key="test.id" :value="test.id">
                    {{ test.name }}
                  </option>
                </optgroup>
              </select>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Select the language test you've taken (e.g., IELTS, TOEFL)
              </p>
              <InputError class="mt-2" :message="form.errors.language_test_id" />
            </div>

            <!-- Test Scores Grid -->
            <div
              v-if="form.language_test_id"
              class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4"
            >
              <div>
                <InputLabel for="overall_score" value="Overall Score" />
                <TextInput
                  id="overall_score"
                  v-model="form.overall_score"
                  type="number"
                  step="0.5"
                  min="0"
                  max="120"
                  class="mt-1 block w-full"
                  placeholder="e.g., 7.5"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Overall band/score</p>
                <InputError class="mt-2" :message="form.errors.overall_score" />
              </div>

              <div>
                <InputLabel for="reading_score" value="Reading" />
                <TextInput
                  id="reading_score"
                  v-model="form.reading_score"
                  type="number"
                  step="0.5"
                  min="0"
                  max="30"
                  class="mt-1 block w-full"
                  placeholder="e.g., 8.0"
                />
                <InputError class="mt-2" :message="form.errors.reading_score" />
              </div>

              <div>
                <InputLabel for="writing_score" value="Writing" />
                <TextInput
                  id="writing_score"
                  v-model="form.writing_score"
                  type="number"
                  step="0.5"
                  min="0"
                  max="30"
                  class="mt-1 block w-full"
                  placeholder="e.g., 7.0"
                />
                <InputError class="mt-2" :message="form.errors.writing_score" />
              </div>

              <div>
                <InputLabel for="listening_score" value="Listening" />
                <TextInput
                  id="listening_score"
                  v-model="form.listening_score"
                  type="number"
                  step="0.5"
                  min="0"
                  max="30"
                  class="mt-1 block w-full"
                  placeholder="e.g., 8.5"
                />
                <InputError class="mt-2" :message="form.errors.listening_score" />
              </div>

              <div>
                <InputLabel for="speaking_score" value="Speaking" />
                <TextInput
                  id="speaking_score"
                  v-model="form.speaking_score"
                  type="number"
                  step="0.5"
                  min="0"
                  max="30"
                  class="mt-1 block w-full"
                  placeholder="e.g., 7.5"
                />
                <InputError class="mt-2" :message="form.errors.speaking_score" />
              </div>
            </div>

            <!-- Test Dates -->
            <div v-if="form.language_test_id" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="test_date" value="Test Date" />
                <DateInput 
                  id="test_date" 
                  v-model="form.test_date" 
                  class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation" 
                  placeholder="DD/MM/YYYY"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  When did you take the test?
                </p>
                <InputError class="mt-2" :message="form.errors.test_date" />
              </div>

              <div>
                <InputLabel for="expiry_date" value="Expiry Date" />
                <DateInput 
                  id="expiry_date" 
                  v-model="form.expiry_date" 
                  class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation" 
                  placeholder="DD/MM/YYYY"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  When does the certificate expire?
                </p>
                <InputError class="mt-2" :message="form.errors.expiry_date" />
              </div>
            </div>

            <!-- Certificate Upload -->
            <div v-if="form.language_test_id">
              <InputLabel for="certificate" value="Certificate File" />
              <input
                id="certificate"
                type="file"
                accept=".pdf,.jpg,.jpeg,.png"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 dark:file:bg-cyan-900/30 dark:file:text-cyan-300 dark:text-gray-400"
                @change="handleFileChange"
              />
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Upload your language test certificate (PDF, JPG, PNG - Max 2MB)
              </p>
              <InputError class="mt-2" :message="form.errors.certificate" />
              <p
                v-if="editingLanguage && editingLanguage.file_path"
                class="mt-2 text-xs text-green-600 dark:text-green-400"
              >
                Current certificate on file. Upload new file to replace.
              </p>
            </div>
          </div>
        </div>

        <!-- Legacy Test Score Field (for backward compatibility) -->
        <div v-if="!form.language_test_id">
          <InputLabel for="test_score" value="Test Score (Optional - Legacy)" />
          <TextInput
            id="test_score"
            v-model="form.test_score"
            type="text"
            class="mt-1 block w-full"
            placeholder="e.g., IELTS 7.5, TOEFL 100, JLPT N2"
          />
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Use the "Language Test Details" section above for structured scores
          </p>
          <InputError class="mt-2" :message="form.errors.test_score" />
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <SecondaryButton type="button" @click="closeModal"> Cancel </SecondaryButton>
          <PrimaryButton
            :disabled="form.processing"
            type="submit"
            class="inline-flex items-center gap-2"
          >
            <span
              v-if="form.processing"
              class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"
            ></span>
            {{ form.processing ? 'Saving...' : form.id ? 'Update Language' : 'Save Language' }}
          </PrimaryButton>
        </div>
      </form>
    </div>
  </Modal>

  <!-- Delete Confirmation Modal -->
  <Modal :show="confirmingDeletion" @close="closeDeleteModal">
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl">
      <div class="flex items-center gap-4 mb-6">
        <div
          class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center"
        >
          <TrashIcon class="w-7 h-7 text-red-600 dark:text-red-400" />
        </div>
        <div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">Delete Language?</h2>
          <p v-if="languageToDelete" class="text-sm text-gray-600 dark:text-gray-400">
            {{ getLanguageName(languageToDelete.language_id) }} -
            {{ languageToDelete.proficiency_level || languageToDelete.proficiency }}
          </p>
        </div>
      </div>

      <p class="text-gray-700 dark:text-gray-300 mb-6">
        Are you sure you want to delete this language entry? This action cannot be undone.
      </p>

      <div class="flex justify-end gap-4">
        <SecondaryButton type="button" @click="closeDeleteModal" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px"> Cancel </SecondaryButton>
        <DangerButton @click="deleteLanguage"> Delete Entry </DangerButton>
      </div>
    </div>
  </Modal>
</section>
</template>
