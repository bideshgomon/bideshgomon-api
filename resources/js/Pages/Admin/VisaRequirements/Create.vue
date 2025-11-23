<template>
    <Head title="Create Visa Requirement" />

    <AdminLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Create Visa Requirement</h1>
                            <p class="mt-2 text-gray-600">Add a new visa requirement for a country</p>
                        </div>
                        <Link 
                            :href="route('admin.visa-requirements.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to List
                        </Link>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-lg shadow">
                    <form @submit.prevent="submitForm">
                        <div class="p-6 space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                        <input 
                                            v-model="form.country"
                                            type="text"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., United States"
                                        />
                                        <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">{{ form.errors.country }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country Code *</label>
                                        <input 
                                            v-model="form.country_code"
                                            type="text"
                                            required
                                            maxlength="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., USA"
                                        />
                                        <p v-if="form.errors.country_code" class="mt-1 text-sm text-red-600">{{ form.errors.country_code }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type *</label>
                                        <select 
                                            v-model="form.visa_type"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select Type</option>
                                            <option value="tourist">Tourist Visa</option>
                                            <option value="business">Business Visa</option>
                                            <option value="student">Student Visa</option>
                                            <option value="work">Work Visa</option>
                                            <option value="medical">Medical Visa</option>
                                            <option value="transit">Transit Visa</option>
                                            <option value="family">Family Visa</option>
                                        </select>
                                        <p v-if="form.errors.visa_type" class="mt-1 text-sm text-red-600">{{ form.errors.visa_type }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Visa Category</label>
                                        <input 
                                            v-model="form.visa_category"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., B1/B2"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Requirements</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">General Requirements *</label>
                                        <textarea 
                                            v-model="form.general_requirements"
                                            required
                                            rows="4"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter general visa requirements..."
                                        />
                                        <p v-if="form.errors.general_requirements" class="mt-1 text-sm text-red-600">{{ form.errors.general_requirements }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Eligibility Criteria</label>
                                        <textarea 
                                            v-model="form.eligibility_criteria"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter eligibility criteria..."
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Important Notes</label>
                                        <textarea 
                                            v-model="form.important_notes"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter important notes..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Requirements -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Requirements</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Bank Balance</label>
                                        <input 
                                            v-model.number="form.min_bank_balance"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 500000"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Statement Months</label>
                                        <input 
                                            v-model.number="form.bank_statement_months"
                                            type="number"
                                            min="1"
                                            max="12"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 6"
                                        />
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Financial Requirements Details</label>
                                        <textarea 
                                            v-model="form.financial_requirements"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter financial requirements..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Fees -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Fees</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Government Fee</label>
                                        <input 
                                            v-model.number="form.government_fee"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 16000"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Fee</label>
                                        <input 
                                            v-model.number="form.service_fee"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 15000"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Processing Fee (Standard)</label>
                                        <input 
                                            v-model.number="form.processing_fee_standard"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 5000"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Processing Time -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Processing Time</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Processing Days (Standard)</label>
                                        <input 
                                            v-model.number="form.processing_days_standard"
                                            type="number"
                                            min="1"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 15"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Processing Time Info</label>
                                        <input 
                                            v-model="form.processing_time_info"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 2-4 weeks"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input 
                                            v-model="form.interview_required"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <label class="ml-2 text-sm font-medium text-gray-700">Interview Required</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input 
                                            v-model="form.biometrics_required"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <label class="ml-2 text-sm font-medium text-gray-700">Biometrics Required</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input 
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <label class="ml-2 text-sm font-medium text-gray-700">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-lg">
                            <Link 
                                :href="route('admin.visa-requirements.index')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Creating...' : 'Create Requirement' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    serviceModules: Array,
    professionCategories: Object,
});

const form = useForm({
    service_module_id: null,
    country: '',
    country_code: '',
    visa_type: '',
    visa_category: '',
    general_requirements: '',
    eligibility_criteria: '',
    processing_time_info: '',
    validity_info: '',
    important_notes: '',
    min_bank_balance: null,
    bank_statement_months: 6,
    financial_requirements: '',
    government_fee: null,
    service_fee: null,
    processing_fee_standard: null,
    processing_fee_express: null,
    processing_fee_urgent: null,
    processing_days_standard: null,
    processing_days_express: null,
    processing_days_urgent: null,
    interview_required: false,
    interview_details: '',
    biometrics_required: false,
    biometrics_details: '',
    application_method: '',
    embassy_website: '',
    application_process: '',
    is_active: true,
});

const submitForm = () => {
    form.post(route('admin.visa-requirements.store'));
};
</script>
