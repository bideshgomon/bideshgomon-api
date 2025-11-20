<template>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Travel History</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Track all your international travels with entry/exit dates and duration.
                    <span class="text-blue-600 font-semibold">Required by USA, UK, Australia, Canada.</span>
                </p>
            </div>
            <button
                @click="addTravelHistory"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Travel Record
            </button>
        </div>

        <!-- Travel History Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div v-if="travelHistory.length === 0" class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No travel history records</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first international travel record.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Country & City
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Entry Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exit Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="travel in travelHistory" :key="travel.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ travel.country }}</div>
                                <div class="text-sm text-gray-500">{{ travel.city || 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getPurposeBadgeClass(travel.purpose)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ travel.purpose }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatDate(travel.entry_date) }}
                                <div class="text-xs text-gray-500">{{ travel.entry_port || '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatDate(travel.exit_date) }}
                                <div class="text-xs text-gray-500">{{ travel.exit_port || '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ travel.duration_days }} days</div>
                                <span v-if="travel.is_long_stay" class="text-xs text-orange-600 font-semibold">
                                    Long Stay (>90d)
                                </span>
                                <span v-if="travel.is_recent" class="text-xs text-green-600 font-semibold">
                                    Recent (<12m)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    <span v-if="travel.incidents_or_violations" class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">
                                        ⚠️ Incident
                                    </span>
                                    <span v-if="travel.return_ticket_proof_uploaded" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                        ✓ Return Ticket
                                    </span>
                                    <span v-if="travel.accommodation_proof_uploaded" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                        ✓ Accommodation
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="editTravelHistory(travel)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button @click="confirmDelete(travel.id)" class="text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form @submit.prevent="submitForm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ isEditing ? 'Edit Travel Record' : 'Add Travel Record' }}
                                </h3>
                                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                                <!-- Passport Selection -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Passport Used *</label>
                                    <select v-model="form.user_passport_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Passport</option>
                                        <option v-for="passport in passports" :key="passport.id" :value="passport.id">
                                            {{ passport.passport_number }} ({{ passport.issuing_country }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.user_passport_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_passport_id }}</p>
                                </div>

                                <!-- Visa Selection (Optional) -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Visa Used (if applicable)</label>
                                    <select v-model="form.user_visa_history_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">No visa / Visa-free entry</option>
                                        <option v-for="visa in visaHistory" :key="visa.id" :value="visa.id">
                                            {{ visa.country }} - {{ visa.visa_type }} ({{ visa.visa_number || 'N/A' }})
                                        </option>
                                    </select>
                                </div>

                                <!-- Country -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Country *</label>
                                    <input v-model="form.country" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">{{ form.errors.country }}</p>
                                </div>

                                <!-- City -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Purpose -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Purpose *</label>
                                    <select v-model="form.purpose" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="tourism">Tourism</option>
                                        <option value="business">Business</option>
                                        <option value="education">Education</option>
                                        <option value="family">Family Visit</option>
                                        <option value="medical">Medical Treatment</option>
                                        <option value="transit">Transit</option>
                                    </select>
                                </div>

                                <!-- Entry Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Entry Date *</label>
                                    <input v-model="form.entry_date" type="date" required @change="calculateDuration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <p v-if="form.errors.entry_date" class="mt-1 text-sm text-red-600">{{ form.errors.entry_date }}</p>
                                </div>

                                <!-- Exit Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Exit Date *</label>
                                    <input v-model="form.exit_date" type="date" required @change="calculateDuration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <p v-if="form.errors.exit_date" class="mt-1 text-sm text-red-600">{{ form.errors.exit_date }}</p>
                                </div>

                                <!-- Duration (Auto-calculated) -->
                                <div class="col-span-2 bg-blue-50 p-4 rounded-md">
                                    <label class="block text-sm font-medium text-blue-700">Duration (Days) - Auto-calculated</label>
                                    <input v-model.number="form.duration_days" type="number" readonly class="mt-1 block w-full rounded-md border-blue-300 bg-blue-100 shadow-sm" />
                                    <p v-if="form.duration_days > 90" class="mt-1 text-sm text-orange-600">
                                        ⚠️ Long stay (>90 days) - May require explanation for future visa applications
                                    </p>
                                </div>

                                <!-- Visa Type Used -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Visa Type Used</label>
                                    <input v-model="form.visa_type_used" type="text" placeholder="Tourist, Business, Student, etc." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Accommodation Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Accommodation Type</label>
                                    <select v-model="form.accommodation_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Type</option>
                                        <option value="hotel">Hotel</option>
                                        <option value="hostel">Hostel</option>
                                        <option value="airbnb">Airbnb</option>
                                        <option value="family">Family/Friends</option>
                                        <option value="company">Company Provided</option>
                                        <option value="university">University Dorm</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Accommodation Address -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Accommodation Address</label>
                                    <textarea v-model="form.accommodation_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <!-- Transportation Mode -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Transportation Mode</label>
                                    <select v-model="form.transportation_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Mode</option>
                                        <option value="air">Air</option>
                                        <option value="land">Land</option>
                                        <option value="sea">Sea</option>
                                    </select>
                                </div>

                                <!-- Entry Port -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Entry Port</label>
                                    <input v-model="form.entry_port" type="text" placeholder="Airport/Border name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Exit Port -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Exit Port</label>
                                    <input v-model="form.exit_port" type="text" placeholder="Airport/Border name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Sponsoring Organization -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Sponsoring Organization (if any)</label>
                                    <input v-model="form.sponsoring_organization" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Travel Companions -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Travel Companions</label>
                                    <input v-model="form.travel_companions" type="text" placeholder="Names of companions (if any)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Incidents or Violations (RED FLAG) -->
                                <div class="col-span-2 bg-red-50 p-4 rounded-md">
                                    <label class="block text-sm font-medium text-red-700">Incidents or Violations (if any) ⚠️</label>
                                    <textarea v-model="form.incidents_or_violations" rows="3" class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                                    <p class="mt-1 text-sm text-red-600">
                                        ⚠️ Report any legal issues, fines, detentions, or policy violations during this trip
                                    </p>
                                </div>

                                <!-- Return Ticket Proof -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Return Ticket Proof</label>
                                    <input type="file" @change="handleReturnTicketUpload" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                                </div>

                                <!-- Accommodation Proof -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Accommodation Proof</label>
                                    <input type="file" @change="handleAccommodationProofUpload" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                                </div>

                                <!-- Notes -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                    <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>{{ isEditing ? 'Update' : 'Save' }}</span>
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete Travel Record
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this travel record? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="deleteTravelHistory" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button type="button" @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
    travelHistory: Array,
    passports: Array,
    visaHistory: Array,
})

const { formatDate } = useBangladeshFormat()

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const deleteId = ref(null)

const form = useForm({
    user_passport_id: '',
    user_visa_history_id: '',
    country: '',
    city: '',
    purpose: 'tourism',
    entry_date: '',
    exit_date: '',
    duration_days: 0,
    visa_type_used: '',
    accommodation_type: '',
    accommodation_address: '',
    transportation_mode: '',
    entry_port: '',
    exit_port: '',
    sponsoring_organization: '',
    travel_companions: '',
    incidents_or_violations: '',
    return_ticket_proof: null,
    accommodation_proof: null,
    notes: '',
})

const addTravelHistory = () => {
    form.reset()
    isEditing.value = false
    showModal.value = true
}

const editTravelHistory = (travel) => {
    isEditing.value = true
    form.clearErrors()
    
    // Populate form with travel data
    Object.keys(form.data()).forEach(key => {
        if (key !== 'return_ticket_proof' && key !== 'accommodation_proof') {
            form[key] = travel[key] || ''
        }
    })
    
    showModal.value = true
}

const calculateDuration = () => {
    if (form.entry_date && form.exit_date) {
        const entry = new Date(form.entry_date)
        const exit = new Date(form.exit_date)
        const diffTime = Math.abs(exit - entry)
        form.duration_days = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    }
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('profile.travel-history.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
                form.reset()
            }
        })
    } else {
        form.post(route('profile.travel-history.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
                form.reset()
            }
        })
    }
}

const handleReturnTicketUpload = (event) => {
    form.return_ticket_proof = event.target.files[0]
}

const handleAccommodationProofUpload = (event) => {
    form.accommodation_proof = event.target.files[0]
}

const confirmDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}

const deleteTravelHistory = () => {
    form.delete(route('profile.travel-history.destroy', deleteId.value), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            deleteId.value = null
        }
    })
}

const getPurposeBadgeClass = (purpose) => {
    const classes = {
        tourism: 'bg-blue-100 text-blue-800',
        business: 'bg-purple-100 text-purple-800',
        education: 'bg-green-100 text-green-800',
        family: 'bg-pink-100 text-pink-800',
        medical: 'bg-red-100 text-red-800',
        transit: 'bg-gray-100 text-gray-800',
    }
    return classes[purpose] || 'bg-gray-100 text-gray-800'
}
</script>
