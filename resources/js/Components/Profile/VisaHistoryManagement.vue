<template>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Visa History</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Track all your visa applications, approvals, rejections, and overstays.
                    <span class="text-red-600 font-semibold">Critical for USA, UK, Australia visa applications.</span>
                </p>
            </div>
            <button
                @click="addVisaHistory"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Visa Record
            </button>
        </div>

        <!-- Visa History Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div v-if="visaHistory.length === 0" class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No visa history records</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first visa record.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Country
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Visa Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Applied / Issued
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validity
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Red Flags
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="visa in visaHistory" :key="visa.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ visa.country }}</div>
                                <div class="text-sm text-gray-500">{{ visa.visa_number || 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ visa.visa_type }}</div>
                                <div class="text-sm text-gray-500">{{ visa.purpose }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusBadgeClass(visa.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ visa.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>Applied: {{ formatDate(visa.application_date) }}</div>
                                <div v-if="visa.issue_date" class="text-gray-500">Issued: {{ formatDate(visa.issue_date) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div v-if="visa.valid_from && visa.valid_to">
                                    {{ formatDate(visa.valid_from) }} - {{ formatDate(visa.valid_to) }}
                                </div>
                                <div v-if="visa.duration_days" class="text-gray-500">
                                    {{ visa.duration_days }} days
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    <!-- Rejection Flag -->
                                    <span v-if="visa.status === 'rejected'" class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Rejected
                                    </span>
                                    
                                    <!-- Overstay Flag -->
                                    <span v-if="visa.overstay_days && visa.overstay_days > 0" class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                        Overstay: {{ visa.overstay_days }}d
                                    </span>

                                    <!-- Appeal Flag -->
                                    <span v-if="visa.appeal_status" class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                        Appeal: {{ visa.appeal_status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="editVisaHistory(visa)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button @click="confirmDelete(visa.id)" class="text-red-600 hover:text-red-900">
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
                                    {{ isEditing ? 'Edit Visa Record' : 'Add Visa Record' }}
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
                                            {{ passport.passport_number }} ({{ passport.issuing_country }}) - Expires: {{ formatDate(passport.expiry_date) }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.user_passport_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_passport_id }}</p>
                                </div>

                                <!-- Country -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Country *</label>
                                    <select v-model="form.country" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <option value="NZ">New Zealand</option>
                                        <option value="SG">Singapore</option>
                                        <option value="MY">Malaysia</option>
                                        <option value="TH">Thailand</option>
                                        <option value="IN">India</option>
                                        <option value="AE">United Arab Emirates</option>
                                        <option value="SA">Saudi Arabia</option>
                                        <option value="QA">Qatar</option>
                                        <option value="KW">Kuwait</option>
                                        <option value="OM">Oman</option>
                                        <option value="BH">Bahrain</option>
                                        <option value="JP">Japan</option>
                                        <option value="KR">South Korea</option>
                                        <option value="CN">China</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="IT">Italy</option>
                                        <option value="ES">Spain</option>
                                        <option value="NL">Netherlands</option>
                                        <option value="SE">Sweden</option>
                                        <option value="NO">Norway</option>
                                        <option value="DK">Denmark</option>
                                        <option value="FI">Finland</option>
                                        <option value="TR">Turkey</option>
                                    </select>
                                    <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">{{ form.errors.country }}</p>
                                </div>

                                <!-- Visa Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Visa Type *</label>
                                    <select v-model="form.visa_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Visa Type</option>
                                        <option value="tourist">Tourist</option>
                                        <option value="business">Business</option>
                                        <option value="student">Student</option>
                                        <option value="work">Work</option>
                                        <option value="transit">Transit</option>
                                        <option value="family_visit">Family Visit</option>
                                        <option value="medical">Medical</option>
                                        <option value="conference">Conference</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <p v-if="form.errors.visa_type" class="mt-1 text-sm text-red-600">{{ form.errors.visa_type }}</p>
                                </div>

                                <!-- Visa Category (optional detail) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Visa Category/Subclass</label>
                                    <input v-model="form.visa_category" type="text" placeholder="e.g., B1/B2, Tier 4, Subclass 500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <p class="mt-1 text-xs text-gray-500">Specific category like "B1/B2", "Tier 4", etc.</p>
                                </div>

                                <!-- Visa Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Visa Number</label>
                                    <input v-model="form.visa_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Application Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Application Date *</label>
                                    <input v-model="form.application_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <p v-if="form.errors.application_date" class="mt-1 text-sm text-red-600">{{ form.errors.application_date }}</p>
                                </div>

                                <!-- Status -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                                    <select v-model="form.status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Status</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="pending">Pending</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>

                                <!-- Issue Date (if approved) -->
                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Issue Date</label>
                                    <input v-model="form.issue_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Expiry Date (if approved) -->
                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                    <input v-model="form.expiry_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Valid From -->
                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Valid From</label>
                                    <input v-model="form.valid_from" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Valid To -->
                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Valid To</label>
                                    <input v-model="form.valid_to" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Rejection Details (if rejected) -->
                                <div v-if="form.status === 'rejected'" class="col-span-2">
                                    <label class="block text-sm font-medium text-red-700">Rejection Reason *</label>
                                    <textarea v-model="form.rejection_reason" rows="3" required class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                                    <p class="mt-1 text-sm text-red-600">⚠️ This is CRITICAL information for future visa applications</p>
                                </div>

                                <!-- Entry/Exit Dates (if approved) -->
                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Entry Date (Actual)</label>
                                    <input v-model="form.entry_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Exit Date (Actual)</label>
                                    <input v-model="form.exit_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Duration of Stay (if approved) -->
                                <div v-if="form.status === 'approved'">
                                    <label class="block text-sm font-medium text-gray-700">Duration of Stay (Days)</label>
                                    <input v-model.number="form.duration_of_stay" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Purpose of Visit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Purpose of Visit</label>
                                    <textarea v-model="form.purpose_of_visit" rows="2" placeholder="Describe the purpose of your visit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <!-- Overstay Days (RED FLAG) -->
                                <div class="col-span-2 bg-red-50 p-4 rounded-md">
                                    <label class="block text-sm font-medium text-red-700">Overstay Days (if any) ⚠️</label>
                                    <input v-model.number="form.overstay_days" type="number" min="0" class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                    <p class="mt-1 text-sm text-red-600">
                                        ⚠️ CRITICAL: Overstays severely impact future visa applications to USA, UK, Australia, Canada
                                    </p>
                                </div>

                                <!-- Embassy Location -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Embassy/Consulate Location</label>
                                    <input v-model="form.embassy_location" type="text" placeholder="e.g., US Embassy Dhaka" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Visa Fee -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Visa Fee Paid (৳)</label>
                                    <input v-model.number="form.visa_fee" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <!-- Supporting Documents -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Supporting Documents (Upload)</label>
                                    <input type="file" @change="handleFileUpload" multiple accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    <p class="mt-1 text-sm text-gray-500">Upload visa approval letter, rejection letter, or related documents</p>
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
                                    Delete Visa Record
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this visa record? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="deleteVisaHistory" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
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
    visaHistory: Array,
    passports: Array,
})

const { formatDate } = useBangladeshFormat()

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const deleteId = ref(null)

const form = useForm({
    user_passport_id: '',
    country: '',
    visa_type: '',
    visa_category: '',
    visa_number: '',
    application_date: '',
    issue_date: '',
    expiry_date: '',
    entry_date: '',
    exit_date: '',
    status: '',
    was_visa_rejected: false,
    rejection_reason: '',
    duration_of_stay: null,
    purpose_of_visit: '',
    overstay_occurred: false,
    overstay_days: 0,
    application_reference: '',
    embassy_location: '',
    visa_fee: null,
    currency: 'BDT',
    supporting_documents: null,
    notes: '',
})

const addVisaHistory = () => {
    form.reset()
    isEditing.value = false
    showModal.value = true
}

const editVisaHistory = (visa) => {
    isEditing.value = true
    form.clearErrors()
    
    // Populate form with visa data
    Object.keys(form.data()).forEach(key => {
        form[key] = visa[key] || (key === 'overstay_days' ? 0 : '')
    })
    
    showModal.value = true
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('profile.visa-history.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
                form.reset()
            }
        })
    } else {
        form.post(route('profile.visa-history.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
                form.reset()
            }
        })
    }
}

const handleFileUpload = (event) => {
    form.supporting_documents = event.target.files
}

const confirmDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}

const deleteVisaHistory = () => {
    form.delete(route('profile.visa-history.destroy', deleteId.value), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            deleteId.value = null
        }
    })
}

const getStatusBadgeClass = (status) => {
    const classes = {
        applied: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        expired: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-orange-100 text-orange-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
