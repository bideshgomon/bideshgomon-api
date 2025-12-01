<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const { formatDate } = useBangladeshFormat()

const familyMembers = ref([])
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingMember = ref(null)
const deletingMember = ref(null)

const form = useForm({
    relationship: '',
    full_name: '',
    date_of_birth: '',
    place_of_birth: '',
    gender: '',
    nationality: '',
    current_country: '',
    current_city: '',
    occupation: '',
    employer: '',
    annual_income_bdt: '',
    education_level: '',
    marital_status: '',
    is_dependent: false,
    lives_with_user: false,
    will_accompany_travel: false,
    passport_number: '',
    visa_status: '',
    deceased: false,
    deceased_date: '',
    contact_phone: '',
    contact_email: '',
    emergency_contact: false,
    relationship_proof: null,
    notes: ''
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
    'other'
]

const genderOptions = ['male', 'female', 'other']

const educationLevelOptions = [
    'none',
    'primary',
    'secondary',
    'higher_secondary',
    'bachelors',
    'masters',
    'doctorate'
]

const maritalStatusOptions = [
    'single',
    'married',
    'divorced',
    'widowed',
    'separated'
]

const visaStatusOptions = [
    'none',
    'tourist',
    'student',
    'work',
    'permanent_resident',
    'citizen'
]

onMounted(() => {
    fetchFamilyMembers()
})

const fetchFamilyMembers = async () => {
    try {
        const response = await axios.get(route('profile.family-members.index'))
        familyMembers.value = response.data
    } catch (error) {
        console.error('Error fetching family members:', error)
    }
}

const calculateAge = (dateOfBirth) => {
    if (!dateOfBirth) return null
    const today = new Date()
    const birthDate = new Date(dateOfBirth)
    let age = today.getFullYear() - birthDate.getFullYear()
    const monthDiff = today.getMonth() - birthDate.getMonth()
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--
    }
    return age
}

const isMinor = (dateOfBirth) => {
    const age = calculateAge(dateOfBirth)
    return age !== null && age < 18
}

const getAgeBadgeClass = (dateOfBirth) => {
    const age = calculateAge(dateOfBirth)
    if (age === null) return 'bg-gray-100 text-gray-800'
    if (age < 18) return 'bg-yellow-100 text-yellow-800'
    return 'bg-green-100 text-green-800'
}

const getRelationshipDisplay = (relationship) => {
    return relationship.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const addFamilyMember = () => {
    editingMember.value = null
    form.reset()
    form.clearErrors()
    showModal.value = true
}

const editFamilyMember = (member) => {
    editingMember.value = member
    form.relationship = member.relationship
    form.full_name = member.full_name
    form.date_of_birth = member.date_of_birth
    form.place_of_birth = member.place_of_birth || ''
    form.gender = member.gender
    form.nationality = member.nationality
    form.current_country = member.current_country || ''
    form.current_city = member.current_city || ''
    form.occupation = member.occupation || ''
    form.employer = member.employer || ''
    form.annual_income_bdt = member.annual_income_bdt || ''
    form.education_level = member.education_level || ''
    form.marital_status = member.marital_status || ''
    form.is_dependent = member.is_dependent || false
    form.lives_with_user = member.lives_with_user || false
    form.will_accompany_travel = member.will_accompany_travel || false
    form.passport_number = member.passport_number || ''
    form.visa_status = member.visa_status || ''
    form.deceased = member.deceased || false
    form.deceased_date = member.deceased_date || ''
    form.contact_phone = member.contact_phone || ''
    form.contact_email = member.contact_email || ''
    form.emergency_contact = member.emergency_contact || false
    form.relationship_proof = null
    form.notes = member.notes || ''
    form.clearErrors()
    showModal.value = true
}

const saveFamilyMember = () => {
    const formData = new FormData()
    
    Object.keys(form.data()).forEach(key => {
        if (key === 'relationship_proof' && form[key]) {
            formData.append(key, form[key])
        } else if (typeof form[key] === 'boolean') {
            formData.append(key, form[key] ? '1' : '0')
        } else if (form[key] !== null && form[key] !== '') {
            formData.append(key, form[key])
        }
    })

    if (editingMember.value) {
        formData.append('_method', 'PUT')
        axios.post(route('profile.family-members.update', editingMember.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(() => {
            showModal.value = false
            fetchFamilyMembers()
            form.reset()
        })
        .catch(error => {
            if (error.response?.data?.errors) {
                form.setError(error.response.data.errors)
            }
        })
    } else {
        axios.post(route('profile.family-members.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(() => {
            showModal.value = false
            fetchFamilyMembers()
            form.reset()
        })
        .catch(error => {
            if (error.response?.data?.errors) {
                form.setError(error.response.data.errors)
            }
        })
    }
}

const confirmDelete = (member) => {
    deletingMember.value = member
    showDeleteModal.value = true
}

const deleteFamilyMember = () => {
    if (!deletingMember.value) return

    axios.delete(route('profile.family-members.destroy', deletingMember.value.id))
        .then(() => {
            showDeleteModal.value = false
            deletingMember.value = null
            fetchFamilyMembers()
        })
        .catch(error => {
            console.error('Error deleting family member:', error)
            alert('Failed to delete family member')
        })
}

const handleFileChange = (event) => {
    form.relationship_proof = event.target.files[0]
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Family Members</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Manage your family member information for visa applications
                </p>
            </div>
            <button
                @click="addFamilyMember"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Family Member
            </button>
        </div>

        <!-- Family Members List -->
        <div v-if="familyMembers.length === 0" class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No family members</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a family member.</p>
        </div>

        <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name & Relationship</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age & Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flags</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="member in familyMembers" :key="member.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ member.full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ getRelationshipDisplay(member.relationship) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getAgeBadgeClass(member.date_of_birth)]">
                                    {{ calculateAge(member.date_of_birth) !== null ? `${calculateAge(member.date_of_birth)} years old` : 'Age unknown' }}
                                </span>
                                <div v-if="member.deceased" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-white ml-2">
                                    Deceased
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ member.current_country || 'Not specified' }}</div>
                            <div v-if="member.current_city" class="text-sm text-gray-500">{{ member.current_city }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span v-if="member.is_dependent" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Dependent
                                </span>
                                <span v-if="member.lives_with_user" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    Lives Together
                                </span>
                                <span v-if="member.will_accompany_travel" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Will Accompany
                                </span>
                                <span v-if="member.emergency_contact" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Emergency Contact
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <button
                                @click="editFamilyMember(member)"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Edit
                            </button>
                            <button
                                @click="confirmDelete(member)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ editingMember ? 'Edit Family Member' : 'Add Family Member' }}
                    </h3>
                </div>

                <form @submit.prevent="saveFamilyMember" class="px-6 py-4 space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Basic Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Relationship *</label>
                                <select v-model="form.relationship" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select relationship</option>
                                    <option v-for="rel in relationshipOptions" :key="rel" :value="rel">
                                        {{ getRelationshipDisplay(rel) }}
                                    </option>
                                </select>
                                <p v-if="form.errors.relationship" class="mt-1 text-sm text-red-600">{{ form.errors.relationship }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                                <input v-model="form.full_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-if="form.errors.full_name" class="mt-1 text-sm text-red-600">{{ form.errors.full_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                                <input v-model="form.date_of_birth" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-if="form.errors.date_of_birth" class="mt-1 text-sm text-red-600">{{ form.errors.date_of_birth }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Place of Birth</label>
                                <input v-model="form.place_of_birth" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Gender *</label>
                                <select v-model="form.gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select gender</option>
                                    <option v-for="gender in genderOptions" :key="gender" :value="gender">
                                        {{ ((gender || '').charAt(0).toUpperCase() || '') + (gender || '').slice(1) }}
                                    </option>
                                </select>
                                <p v-if="form.errors.gender" class="mt-1 text-sm text-red-600">{{ form.errors.gender }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nationality *</label>
                                <input v-model="form.nationality" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p v-if="form.errors.nationality" class="mt-1 text-sm text-red-600">{{ form.errors.nationality }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Location -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Current Location</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Country</label>
                                <input v-model="form.current_country" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current City</label>
                                <input v-model="form.current_city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Employment & Financial -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Employment & Financial Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Occupation</label>
                                <input v-model="form.occupation" type="text" :disabled="form.is_dependent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Employer</label>
                                <input v-model="form.employer" type="text" :disabled="form.is_dependent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Annual Income (৳)</label>
                                <input v-model="form.annual_income_bdt" type="number" step="0.01" :disabled="form.is_dependent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Education Level</label>
                                <select v-model="form.education_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select education level</option>
                                    <option v-for="level in educationLevelOptions" :key="level" :value="level">
                                        {{ level.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Marital Status</label>
                                <select v-model="form.marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select marital status</option>
                                    <option v-for="status in maritalStatusOptions" :key="status" :value="status">
                                        {{ ((status || '').charAt(0).toUpperCase() || '') + (status || '').slice(1) }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dependency & Travel Status -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Dependency & Travel Status</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input v-model="form.is_dependent" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                <label class="ml-2 block text-sm text-gray-900">
                                    Is Dependent (financially dependent on you)
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input v-model="form.lives_with_user" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                <label class="ml-2 block text-sm text-gray-900">
                                    Lives with me
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input v-model="form.will_accompany_travel" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                <label class="ml-2 block text-sm text-gray-900">
                                    Will accompany me on travels
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input v-model="form.emergency_contact" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                <label class="ml-2 block text-sm text-gray-900">
                                    Emergency Contact
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Documents -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Travel Documents</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Passport Number</label>
                                <input v-model="form.passport_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Visa Status</label>
                                <select v-model="form.visa_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select visa status</option>
                                    <option v-for="status in visaStatusOptions" :key="status" :value="status">
                                        {{ status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Deceased Information -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Deceased Information</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input v-model="form.deceased" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                <label class="ml-2 block text-sm text-gray-900">
                                    Deceased
                                </label>
                            </div>

                            <div v-if="form.deceased">
                                <label class="block text-sm font-medium text-gray-700">Date of Death</label>
                                <input v-model="form.deceased_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Contact Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Phone</label>
                                <input v-model="form.contact_phone" type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="+880 1XXX-XXXXXX" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                                <input v-model="form.contact_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Supporting Documents</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Relationship Proof</label>
                            <input @change="handleFileChange" type="file" accept="image/*,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p class="mt-1 text-sm text-gray-500">Upload birth certificate, marriage certificate, or other proof of relationship</p>
                            <p v-if="editingMember?.relationship_proof_uploaded" class="mt-1 text-sm text-green-600">✓ Document uploaded</p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Family Member' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Family Member</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete <strong>{{ deletingMember?.full_name }}</strong>? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteFamilyMember"
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
