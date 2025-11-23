<template>
    <Head title="Edit Visa Requirement" />

    <AdminLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Edit Visa Requirement</h1>
                            <p class="mt-2 text-gray-600">Update visa requirement for {{ visaRequirement.country }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link 
                                :href="route('admin.visa-requirements.show', visaRequirement.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                            >
                                View Details
                            </Link>
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
                                        />
                                        <p v-if="form.errors.general_requirements" class="mt-1 text-sm text-red-600">{{ form.errors.general_requirements }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Eligibility Criteria</label>
                                        <textarea 
                                            v-model="form.eligibility_criteria"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Important Notes</label>
                                        <textarea 
                                            v-model="form.important_notes"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                        />
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Financial Requirements Details</label>
                                        <textarea 
                                            v-model="form.financial_requirements"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Fee</label>
                                        <input 
                                            v-model.number="form.service_fee"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Processing Fee (Standard)</label>
                                        <input 
                                            v-model.number="form.processing_fee_standard"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Processing Time Info</label>
                                        <input 
                                            v-model="form.processing_time_info"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                        <div class="bg-gray-50 px-6 py-4 flex justify-between rounded-b-lg">
                            <button 
                                type="button"
                                @click="confirmDelete"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                            >
                                Delete Requirement
                            </button>
                            <div class="flex space-x-3">
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
                                    {{ form.processing ? 'Updating...' : 'Update Requirement' }}
                                </button>
                            </div>
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

const props = defineProps({
    visaRequirement: Object,
    serviceModules: Array,
    professionCategories: Object,
});

const form = useForm({
    service_module_id: props.visaRequirement.service_module_id,
    country: props.visaRequirement.country,
    country_code: props.visaRequirement.country_code,
    visa_type: props.visaRequirement.visa_type,
    visa_category: props.visaRequirement.visa_category,
    general_requirements: props.visaRequirement.general_requirements,
    eligibility_criteria: props.visaRequirement.eligibility_criteria,
    processing_time_info: props.visaRequirement.processing_time_info,
    validity_info: props.visaRequirement.validity_info,
    important_notes: props.visaRequirement.important_notes,
    min_bank_balance: props.visaRequirement.min_bank_balance,
    bank_statement_months: props.visaRequirement.bank_statement_months,
    financial_requirements: props.visaRequirement.financial_requirements,
    government_fee: props.visaRequirement.government_fee,
    service_fee: props.visaRequirement.service_fee,
    processing_fee_standard: props.visaRequirement.processing_fee_standard,
    processing_fee_express: props.visaRequirement.processing_fee_express,
    processing_fee_urgent: props.visaRequirement.processing_fee_urgent,
    processing_days_standard: props.visaRequirement.processing_days_standard,
    processing_days_express: props.visaRequirement.processing_days_express,
    processing_days_urgent: props.visaRequirement.processing_days_urgent,
    interview_required: props.visaRequirement.interview_required,
    interview_details: props.visaRequirement.interview_details,
    biometrics_required: props.visaRequirement.biometrics_required,
    biometrics_details: props.visaRequirement.biometrics_details,
    application_method: props.visaRequirement.application_method,
    embassy_website: props.visaRequirement.embassy_website,
    application_process: props.visaRequirement.application_process,
    is_active: props.visaRequirement.is_active,
});

const submitForm = () => {
    form.put(route('admin.visa-requirements.update', props.visaRequirement.id));
};

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this visa requirement? This action cannot be undone.')) {
        router.delete(route('admin.visa-requirements.destroy', props.visaRequirement.id));
    }
};
</script>
