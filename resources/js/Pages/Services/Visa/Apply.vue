<template>
    <AuthenticatedLayout>
        <Head title="Apply for Visa" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Visa Application Form</h1>
                    <p class="mt-2 text-gray-600">Complete the form below to apply for your visa</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Visa Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Visa Details</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type *</label>
                                <select v-model="form.visa_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select visa type</option>
                                    <option value="tourist">Tourist Visa</option>
                                    <option value="business">Business Visa</option>
                                    <option value="student">Student Visa</option>
                                    <option value="work">Work Visa</option>
                                    <option value="medical">Medical Visa</option>
                                    <option value="transit">Transit Visa</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Destination Country *</label>
                                <input
                                    v-model="form.destination_country"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g., United States"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country Code *</label>
                                <input
                                    v-model="form.destination_country_code"
                                    type="text"
                                    required
                                    maxlength="10"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g., USA"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Visa Category *</label>
                                <select v-model="form.visa_category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select category</option>
                                    <option value="single_entry">Single Entry</option>
                                    <option value="multiple_entry">Multiple Entry</option>
                                    <option value="transit">Transit</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (days)</label>
                                <input
                                    v-model.number="form.duration_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="30"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Processing Type *</label>
                                <select v-model="form.processing_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="standard">Standard (15 days)</option>
                                    <option value="express">Express (7 days)</option>
                                    <option value="urgent">Urgent (3 days)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Applicant Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input
                                    v-model="form.applicant_name"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input
                                    v-model="form.applicant_email"
                                    type="email"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                <input
                                    v-model="form.applicant_phone"
                                    type="tel"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                                <input
                                    v-model="form.applicant_dob"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nationality *</label>
                                <input
                                    v-model="form.nationality"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                                <input
                                    v-model="form.occupation"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Employer Name</label>
                                <input
                                    v-model="form.employer_name"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Passport Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Passport Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passport Number *</label>
                                <input
                                    v-model="form.passport_number"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Issuing Country *</label>
                                <input
                                    v-model="form.passport_issuing_country"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Issue Date *</label>
                                <input
                                    v-model="form.passport_issue_date"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                                <input
                                    v-model="form.passport_expiry_date"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Travel Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Travel Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Intended Travel Date *</label>
                                <input
                                    v-model="form.intended_travel_date"
                                    type="date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Return Date</label>
                                <input
                                    v-model="form.return_date"
                                    type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Travel Purpose *</label>
                                <textarea
                                    v-model="form.travel_purpose"
                                    required
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Describe your purpose of travel..."
                                ></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.previous_visa_rejected"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">I have been previously rejected for a visa</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Fees -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Fees</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service Fee *</label>
                                <input
                                    v-model.number="form.service_fee"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Government Fee *</label>
                                <input
                                    v-model.number="form.government_fee"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Processing Fee *</label>
                                <input
                                    v-model.number="form.processing_fee"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-indigo-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                                <span class="text-2xl font-bold text-indigo-600">৳{{ totalAmount.toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between">
                        <Link
                            :href="route('visa.index')"
                            class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Processing...</span>
                            <span v-else>Submit Application</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    visaType: String,
    selectedCountry: String,
    documentRequirements: Array,
});

const form = useForm({
    visa_type: props.visaType || '',
    destination_country: props.selectedCountry || '',
    destination_country_code: '',
    visa_category: '',
    duration_days: 30,
    applicant_name: '',
    applicant_email: '',
    applicant_phone: '',
    applicant_dob: '',
    passport_number: '',
    passport_issue_date: '',
    passport_expiry_date: '',
    passport_issuing_country: '',
    nationality: '',
    intended_travel_date: '',
    return_date: '',
    travel_purpose: '',
    occupation: '',
    employer_name: '',
    previous_visa_rejected: false,
    processing_type: 'standard',
    service_fee: 0,
    government_fee: 0,
    processing_fee: 0,
});

const totalAmount = computed(() => {
    return form.service_fee + form.government_fee + form.processing_fee;
});

const submit = () => {
    form.post(route('visa.store'));
};
</script>
