<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue'
import FlowButton from '@/Components/Rhythmic/FlowButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import Modal from '@/Components/Modal.vue'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  UserGroupIcon,
  UsersIcon,
  HeartIcon,
  CakeIcon,
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  BriefcaseIcon,
  AcademicCapIcon,
  IdentificationIcon,
  ExclamationTriangleIcon,
  CheckBadgeIcon,
} from '@heroicons/vue/24/outline'

const { formatCurrency } = useBangladeshFormat()

const familyMembers = ref([])
const showModal = ref(false)
const editingId = ref(null)
const confirmingDeletion = ref(false)
const memberToDelete = ref(null)

const form = useForm({
  relationship: '',
  full_name: '',
  date_of_birth: '',
  place_of_birth: '',
  gender: '',
  nationality: '',
  country_of_residence: '',
  city: '',
  occupation: '',
  employer_name: '',
  annual_income: '',
  income_currency: 'BDT',
  education_level: '',
  marital_status: '',
  is_dependent: false,
  lives_with_user: false,
  will_accompany: false,
  will_accompany_travel: false,
  passport_number: '',
  immigration_status: '',
  is_deceased: false,
  deceased_date: '',
  phone_number: '',
  email: '',
  emergency_contact: false,
  relationship_proof: null,
  address: '',
  notes: '',
})

const relationshipOptions = [
  'spouse',
  'child',
  'parent',
  'sibling',
  'grandparent',
  'grandchild',
  'uncle',
  'aunt',
  'cousin',
  'nephew',
  'niece',
  'in-law',
  'other',
]

const genderOptions = ['male', 'female', 'other']

const educationLevelOptions = [
  'none',
  'primary',
  'secondary',
  'higher_secondary',
  'bachelors',
  'masters',
  'doctorate',
]

const maritalStatusOptions = ['single', 'married', 'divorced', 'widowed', 'separated']

const visaStatusOptions = ['none', 'tourist', 'student', 'work', 'permanent_resident', 'citizen']

const calculateAge = dateOfBirth => {
  if (!dateOfBirth) {
    return null
  }
  const today = new Date()
  const birthDate = new Date(dateOfBirth)
  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }
  return age
}

const formatLabel = str =>
  str
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')

const getFamilyMembers = () => {
  axios
    .get(route('api.profile.family.index'))
    .then(response => {
      familyMembers.value = response.data
    })
    .catch(error => console.error('Error fetching family members:', error))
}

const submitForm = () => {
  const url = editingId.value
    ? route('api.profile.family.update', editingId.value)
    : route('api.profile.family.store')

  const method = editingId.value ? 'put' : 'post'

  form.processing = true
  form.errors = {}

  // Create FormData for file upload
  const formData = new FormData()
  const fields = ['relationship', 'full_name', 'date_of_birth', 'occupation', 'phone', 'email', 'address', 'is_dependent', 'is_traveling_with_applicant', 'relationship_proof']
  
  fields.forEach(key => {
    if (key === 'relationship_proof' && form[key]) {
      formData.append(key, form[key])
    } else if (typeof form[key] === 'boolean') {
      formData.append(key, form[key] ? '1' : '0')
    } else if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key])
    }
  })

  // Add _method for PUT requests
  if (method === 'put') {
    formData.append('_method', 'PUT')
  }

  const config = {
    headers: { 'Content-Type': 'multipart/form-data' },
  }

  const axiosMethod = method === 'put' ? 'post' : 'post'

  axios[axiosMethod](url, formData, config)
    .then(() => {
      closeModal()
      getFamilyMembers()
      form.processing = false
      form.recentlySuccessful = true
      setTimeout(() => (form.recentlySuccessful = false), 2000)
    })
    .catch(error => {
      form.processing = false
      if (error.response && error.response.status === 422) {
        form.errors = error.response.data.errors || {}
      } else {
        console.error('Error submitting form:', error)
        form.errors = { general: 'An error occurred while saving. Please try again.' }
      }
    })
}

const editMember = member => {
  editingId.value = member.id
  form.relationship = member.relationship
  form.full_name = member.full_name
  form.date_of_birth = member.date_of_birth ? member.date_of_birth.substring(0, 10) : ''
  form.place_of_birth = member.place_of_birth || ''
  form.gender = member.gender || ''
  form.nationality = member.nationality || ''
  form.country_of_residence = member.country_of_residence || ''
  form.city = member.city || ''
  form.occupation = member.occupation || ''
  form.employer_name = member.employer_name || ''
  form.annual_income = member.annual_income || ''
  form.income_currency = member.income_currency || 'BDT'
  form.education_level = member.education_level || ''
  form.marital_status = member.marital_status || ''
  form.is_dependent = member.is_dependent || false
  form.lives_with_user = member.lives_with_user || false
  form.will_accompany = member.will_accompany || false
  form.will_accompany_travel = member.will_accompany_travel || false
  form.passport_number = member.passport_number || ''
  form.immigration_status = member.immigration_status || ''
  form.is_deceased = member.is_deceased || false
  form.deceased_date = member.deceased_date ? member.deceased_date.substring(0, 10) : ''
  form.phone_number = member.phone_number || ''
  form.email = member.email || ''
  form.emergency_contact = member.emergency_contact || false
  form.relationship_proof = null
  form.address = member.address || ''
  form.notes = member.notes || ''
  form.clearErrors()
  showModal.value = true
}

const openAddModal = () => {
  editingId.value = null
  resetForm()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingId.value = null
  resetForm()
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}

const handleFileChange = event => {
  form.relationship_proof = event.target.files[0]
}

const confirmDelete = (member) => {
  memberToDelete.value = member
  confirmingDeletion.value = true
}

const closeDeleteModal = () => {
  confirmingDeletion.value = false
  memberToDelete.value = null
}

const deleteMember = () => {
  if (!memberToDelete.value) return

  axios
    .delete(route('api.profile.family.destroy', memberToDelete.value.id))
    .then(() => {
      getFamilyMembers()
      if (editingId.value === memberToDelete.value.id) {
        closeModal()
      }
      closeDeleteModal()
    })
    .catch(error => {
      console.error('Error deleting family member:', error)
      alert('Failed to delete record.')
      closeDeleteModal()
    })
}

onMounted(() => {
  getFamilyMembers()
})
</script>

<template>
  <section>
    <header class="mb-rhythm-lg">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-heritage-500 to-gold-500 flex items-center justify-center shadow-rhythmic-md">
            <UsersIcon class="w-6 h-6 text-white" />
          </div>
          <div>
            <h2 class="font-display font-bold text-xl text-gray-800">Family Information</h2>
            <p class="text-xs text-gray-500">
              Family members and dependents for visa applications
            </p>
          </div>
        </div>
        <FlowButton @click="openAddModal" variant="primary">
          <template #icon-left><PlusIcon class="w-4 h-4" /></template>
          <span class="hidden sm:inline">Add Member</span>
          <span class="sm:hidden">Add</span>
        </FlowButton>
      </div>
    </header>

    <!-- Family Members List -->
    <div v-if="familyMembers.length > 0" class="space-y-4 mb-6">
      <div
        v-for="member in familyMembers"
        :key="member.id"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200"
      >
        <!-- Gradient Header Stripe -->
        <div class="h-px bg-gradient-to-r from-pink-600 to-rose-600"></div>
        
        <!-- Card Content -->
        <div class="p-4 sm:p-6">
          <!-- Header with Icon and Name -->
          <div class="flex items-start gap-3 mb-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-600 to-rose-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
              <UsersIcon class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate">
                {{ member.full_name }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2 mt-1">
                <HeartIcon class="w-4 h-4 flex-shrink-0" />
                {{ formatLabel(member.relationship) }}
              </p>
            </div>
          </div>

          <!-- Status Badges -->
          <div class="flex flex-wrap gap-2 mb-4">
            <span
              v-if="calculateAge(member.date_of_birth) !== null"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
            >
              <CakeIcon class="w-3.5 h-3.5" />
              {{ calculateAge(member.date_of_birth) }} years
            </span>
            <span
              v-if="member.is_deceased"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-700 dark:bg-gray-600 text-white"
            >
              Deceased
            </span>
            <span
              v-if="member.is_dependent"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300"
            >
              Dependent
            </span>
            <span
              v-if="member.lives_with_user"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300"
            >
              Lives Together
            </span>
            <span
              v-if="member.will_accompany_travel || member.will_accompany"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300"
            >
              <CheckBadgeIcon class="w-3.5 h-3.5" />
              Will Accompany
            </span>
            <span
              v-if="member.emergency_contact"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300"
            >
              <ExclamationTriangleIcon class="w-3.5 h-3.5" />
              Emergency Contact
            </span>
          </div>

          <!-- Key Information Grid -->
          <div class="space-y-3 text-sm">
            <div v-if="member.date_of_birth" class="flex items-start gap-2">
              <CakeIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <span class="text-gray-500 dark:text-gray-400">Born:</span>
                <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ member.date_of_birth }}</span>
                <span v-if="member.place_of_birth" class="ml-1 text-gray-600 dark:text-gray-400">in {{ member.place_of_birth }}</span>
              </div>
            </div>

            <div v-if="member.nationality" class="flex items-start gap-2">
              <MapPinIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <span class="text-gray-500 dark:text-gray-400">Nationality:</span>
                <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ member.nationality }}</span>
                <span v-if="member.country_of_residence" class="ml-1 text-gray-600 dark:text-gray-400">
                  (Currently in {{ member.city ? `${member.city}, ` : '' }}{{ member.country_of_residence }})
                </span>
              </div>
            </div>

            <div v-if="member.occupation && !member.is_dependent" class="flex items-start gap-2">
              <BriefcaseIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <span class="text-gray-900 dark:text-white font-medium">{{ member.occupation }}</span>
                <span v-if="member.employer_name" class="text-gray-600 dark:text-gray-400"> at {{ member.employer_name }}</span>
                <span v-if="member.annual_income" class="block mt-0.5 text-gray-500 dark:text-gray-400">
                  Income: {{ formatCurrency(member.annual_income) }}
                </span>
              </div>
            </div>

            <div v-if="member.education_level" class="flex items-start gap-2">
              <AcademicCapIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <span class="text-gray-500 dark:text-gray-400">Education:</span>
                <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ formatLabel(member.education_level) }}</span>
              </div>
            </div>

            <div v-if="member.passport_number" class="flex items-start gap-2">
              <IdentificationIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <span class="text-gray-500 dark:text-gray-400">Passport:</span>
                <span class="ml-2 text-gray-900 dark:text-white font-mono font-medium">{{ member.passport_number }}</span>
                <span v-if="member.immigration_status && member.immigration_status !== 'none'" class="ml-2 text-gray-600 dark:text-gray-400">
                  ({{ formatLabel(member.immigration_status) }})
                </span>
              </div>
            </div>

            <div v-if="member.phone_number" class="flex items-start gap-2">
              <PhoneIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <a :href="`tel:${member.phone_number}`" class="text-pink-600 dark:text-pink-400 hover:underline font-medium">
                  {{ member.phone_number }}
                </a>
              </div>
            </div>

            <div v-if="member.email" class="flex items-start gap-2">
              <EnvelopeIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <a :href="`mailto:${member.email}`" class="text-pink-600 dark:text-pink-400 hover:underline font-medium">
                  {{ member.email }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Footer -->
        <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
          <div class="flex gap-3">
            <button
              @click="editMember(member)"
              class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm transition-colors"
            >
              <PencilSquareIcon class="h-4 w-4" />
              <span>Edit</span>
            </button>
            <button
              @click="confirmDelete(member)"
              class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg font-medium text-sm transition-colors"
            >
              <TrashIcon class="h-4 w-4" />
              <span>Delete</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-md">
      <UserGroupIcon class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Family Members Added</h3>
      <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
        Add family members for visa sponsorship and travel documentation
      </p>
    </div>

    <!-- Add Button -->
    <button
      @click="openAddModal"
      class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-base mt-6"
    >
      <PlusIcon class="h-5 w-5" />
      <span>ADD FAMILY MEMBER</span>
    </button>

    <!-- Add/Edit Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="3xl">
      <div class="p-6 bg-white dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
          <div class="w-12 h-12 bg-gradient-to-br from-pink-600 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
            <UsersIcon class="w-7 h-7 text-white" />
          </div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ editingId ? 'Edit Family Member' : 'Add Family Member' }}
          </h2>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Basic Information -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Basic Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="relationship" value="Relationship *" />
                <select
                  id="relationship"
                  v-model="form.relationship"
                  class="mt-1 block w-full text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-pink-500 focus:ring-pink-500 rounded-lg"
                  required
                >
                  <option value="">Select relationship</option>
                  <option v-for="rel in relationshipOptions" :key="rel" :value="rel">
                    {{ formatLabel(rel) }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.relationship" />
              </div>

              <div>
                <InputLabel for="full_name" value="Full Name *" />
                <TextInput
                  id="full_name"
                  v-model="form.full_name"
                  type="text"
                  class="mt-1 block w-full text-base"
                  required
                  placeholder="As per passport"
                />
                <InputError class="mt-2" :message="form.errors.full_name" />
              </div>

              <div>
                <InputLabel for="date_of_birth" value="Date of Birth *" />
                <TextInput
                  type="date"
                  id="date_of_birth"
                  v-model="form.date_of_birth"
                  class="mt-1 block w-full text-base"
                  required
                />
                <InputError class="mt-2" :message="form.errors.date_of_birth" />
                <p v-if="form.date_of_birth && calculateAge(form.date_of_birth)" class="text-xs text-gray-500 mt-1">
                  Age: {{ calculateAge(form.date_of_birth) }} years
                </p>
              </div>

              <div>
                <InputLabel for="gender" value="Gender *" />
                <select
                  id="gender"
                  v-model="form.gender"
                  class="mt-1 block w-full text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-pink-500 focus:ring-pink-500 rounded-lg"
                  required
                >
                  <option value="">Select gender</option>
                  <option v-for="gender in genderOptions" :key="gender" :value="gender">
                    {{ formatLabel(gender) }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.gender" />
              </div>

              <div>
                <InputLabel for="nationality" value="Nationality *" />
                <TextInput
                  id="nationality"
                  v-model="form.nationality"
                  type="text"
                  class="mt-1 block w-full text-base"
                  required
                  placeholder="e.g., Bangladeshi"
                />
                <InputError class="mt-2" :message="form.errors.nationality" />
              </div>

              <div>
                <InputLabel for="place_of_birth" value="Place of Birth" />
                <TextInput
                  id="place_of_birth"
                  v-model="form.place_of_birth"
                  type="text"
                  class="mt-1 block w-full text-base"
                  placeholder="City, Country"
                />
                <InputError class="mt-2" :message="form.errors.place_of_birth" />
              </div>
            </div>
          </div>

          <!-- Current Location -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Current Location
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="country_of_residence" value="Current Country" />
                <TextInput
                  id="country_of_residence"
                  v-model="form.country_of_residence"
                  type="text"
                  class="mt-1 block w-full text-base"
                  placeholder="Residing country"
                />
                <InputError class="mt-2" :message="form.errors.country_of_residence" />
              </div>

              <div>
                <InputLabel for="city" value="Current City" />
                <TextInput
                  id="city"
                  v-model="form.city"
                  type="text"
                  class="mt-1 block w-full text-base"
                  placeholder="Residing city"
                />
                <InputError class="mt-2" :message="form.errors.city" />
              </div>
            </div>
          </div>

          <!-- Employment & Education -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Employment & Education
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="occupation" value="Occupation" />
                <TextInput
                  id="occupation"
                  v-model="form.occupation"
                  type="text"
                  class="mt-1 block w-full text-base"
                  :disabled="form.is_dependent"
                  placeholder="Current occupation"
                />
                <InputError class="mt-2" :message="form.errors.occupation" />
              </div>

              <div>
                <InputLabel for="employer_name" value="Employer" />
                <TextInput
                  id="employer_name"
                  v-model="form.employer_name"
                  type="text"
                  class="mt-1 block w-full text-base"
                  :disabled="form.is_dependent"
                  placeholder="Employer name"
                />
                <InputError class="mt-2" :message="form.errors.employer_name" />
              </div>

              <div>
                <InputLabel for="education_level" value="Education Level" />
                <select
                  id="education_level"
                  v-model="form.education_level"
                  class="mt-1 block w-full text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-pink-500 focus:ring-pink-500 rounded-lg"
                >
                  <option value="">Select education level</option>
                  <option v-for="level in educationLevelOptions" :key="level" :value="level">
                    {{ formatLabel(level) }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.education_level" />
              </div>

              <div>
                <InputLabel for="marital_status" value="Marital Status" />
                <select
                  id="marital_status"
                  v-model="form.marital_status"
                  class="mt-1 block w-full text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-pink-500 focus:ring-pink-500 rounded-lg"
                >
                  <option value="">Select marital status</option>
                  <option v-for="status in maritalStatusOptions" :key="status" :value="status">
                    {{ formatLabel(status) }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.marital_status" />
              </div>
            </div>
          </div>

          <!-- Travel Documents -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Travel Documents
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="passport_number" value="Passport Number" />
                <TextInput
                  id="passport_number"
                  v-model="form.passport_number"
                  type="text"
                  class="mt-1 block w-full text-base"
                  placeholder="If applicable"
                />
                <InputError class="mt-2" :message="form.errors.passport_number" />
              </div>

              <div>
                <InputLabel for="immigration_status" value="Immigration Status" />
                <select
                  id="immigration_status"
                  v-model="form.immigration_status"
                  class="mt-1 block w-full text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-pink-500 focus:ring-pink-500 rounded-lg"
                >
                  <option value="">Select immigration status</option>
                  <option v-for="status in visaStatusOptions" :key="status" :value="status">
                    {{ formatLabel(status) }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.immigration_status" />
              </div>
            </div>
          </div>

          <!-- Status Flags -->
          <div class="space-y-3">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Status & Relationship
            </h3>
            <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition">
              <input
                v-model="form.is_dependent"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 text-pink-600 focus:ring-pink-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                This person is my dependent (for visa sponsorship)
              </span>
            </label>

            <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition">
              <input
                v-model="form.lives_with_user"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 text-pink-600 focus:ring-pink-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lives with me</span>
            </label>

            <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition">
              <input
                v-model="form.will_accompany_travel"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 text-pink-600 focus:ring-pink-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Will accompany me on travel</span>
            </label>

            <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition">
              <input
                v-model="form.emergency_contact"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500"
              />
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Emergency Contact</span>
            </label>
          </div>

          <!-- Contact Information -->
          <div class="space-y-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Contact Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <InputLabel for="phone_number" value="Contact Phone" />
                <TextInput
                  id="phone_number"
                  v-model="form.phone_number"
                  type="tel"
                  class="mt-1 block w-full text-base"
                  placeholder="+880..."
                />
                <InputError class="mt-2" :message="form.errors.phone_number" />
              </div>

              <div>
                <InputLabel for="email" value="Contact Email" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full text-base"
                  placeholder="email@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <SecondaryButton type="button" @click="closeModal">Cancel</SecondaryButton>
            <PrimaryButton :disabled="form.processing" type="submit">
              {{ form.processing ? 'Saving...' : editingId ? 'Update Member' : 'Add Member' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="confirmingDeletion" @close="closeDeleteModal">
      <div class="p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
            <TrashIcon class="w-7 h-7 text-red-600 dark:text-red-400" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Delete Family Member?</h2>
            <p v-if="memberToDelete" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
              {{ memberToDelete.full_name }} - {{ formatLabel(memberToDelete.relationship) }}
            </p>
          </div>
        </div>

        <p class="text-gray-700 dark:text-gray-300 mb-6">
          Are you sure you want to delete this family member? This action cannot be undone.
        </p>

        <div class="flex justify-end gap-3">
          <SecondaryButton type="button" @click="closeDeleteModal">Cancel</SecondaryButton>
          <DangerButton @click="deleteMember">Delete Member</DangerButton>
        </div>
      </div>
    </Modal>
  </section>
</template>

