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
                            <!-- Document Hub Notice -->
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-semibold text-blue-800">Using Legacy Document System</h3>
                                        <p class="mt-1 text-sm text-blue-700">
                                            This form uses the old text-based document system. For new requirements, consider using the 
                                            <Link :href="route('admin.document-assignments.index')" class="font-semibold underline hover:text-blue-900">Document Hub System</Link> 
                                            for better management and standardization with ICAO, ISO, WHO, and UN standards.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                        <select 
                                            v-model="form.country_id"
                                            @change="updateCountryInfo"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select a country</option>
                                            <option v-for="country in props.countries" :key="country.id" :value="country.id">
                                                {{ country.label }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.country_id" class="mt-1 text-sm text-red-600">{{ form.errors.country_id }}</p>
                                        <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">{{ form.errors.country }}</p>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Basic Documents (Common for All) *
                                            <span class="text-xs text-gray-500">One document per line</span>
                                        </label>
                                        <textarea 
                                            v-model="form.required_documents_text"
                                            required
                                            rows="8"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                                            placeholder="Valid passport with Old Passport if available (minimum 6 months validity)&#10;Recent passport-size photograph (white background, without glasses)&#10;Recent bank statement (last 6 months) and bank solvency certificate&#10;TIN Certificate and Income Tax Certificate&#10;Tour itinerary&#10;Air-ticket booking&#10;Hotel booking confirmation&#10;Cover letter to the visa officer"
                                        />
                                        <p v-if="form.errors.required_documents" class="mt-1 text-sm text-red-600">{{ form.errors.required_documents }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Profession-Specific Documents *
                                            <span class="text-xs text-gray-500">Select professions to add their requirements</span>
                                        </label>
                                        
                                        <!-- Profession Selector -->
                                        <div class="mb-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Add Documents For:</label>
                                            <select 
                                                v-model="selectedProfessionToAdd"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                @change="addProfessionSection"
                                            >
                                                <option value="">Select a profession...</option>
                                                <option v-for="profession in props.professions" :key="profession" :value="profession">
                                                    {{ profession }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Dynamic Profession Sections -->
                                        <div v-if="professionDocs.length === 0" class="text-sm text-gray-500 italic p-4 bg-gray-50 rounded-md">
                                            No profession-specific documents added yet. Select a profession above to add documents.
                                        </div>

                                        <div v-for="(item, index) in professionDocs" :key="index" class="mb-4 p-4 bg-white border-2 border-indigo-100 rounded-md">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-semibold text-indigo-700">{{ item.profession }}</h4>
                                                <button 
                                                    type="button"
                                                    @click="removeProfessionSection(index)"
                                                    class="text-red-600 hover:text-red-800 text-sm"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                            <textarea 
                                                v-model="item.documents"
                                                rows="5"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                                                :placeholder="`Enter ${item.profession} documents (one per line):\nNOC from current employer\nEmployee ID card photo\nSalary certificate`"
                                            />
                                        </div>

                                        <p v-if="form.errors.profession_specific_docs" class="mt-1 text-sm text-red-600">{{ form.errors.profession_specific_docs }}</p>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Important Notes / Special Requirements</label>
                                        <textarea 
                                            v-model="form.important_notes"
                                            rows="4"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Special notes for all applicants...&#10;Example: If the applicant has visited this country previously, provide visa copy"
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
import { ref, computed } from 'vue';

const props = defineProps({
    serviceModules: Array,
    professionCategories: Object,
    countries: Array,
    professions: Array,
});

// Profession-specific document management
const professionDocs = ref([]);
const selectedProfessionToAdd = ref('');

const addProfessionSection = () => {
    if (selectedProfessionToAdd.value) {
        professionDocs.value.push({
            profession: selectedProfessionToAdd.value,
            documents: ''
        });
        selectedProfessionToAdd.value = '';
    }
};

const removeProfessionSection = (index) => {
    professionDocs.value.splice(index, 1);
};

const form = useForm({
    service_module_id: null,
    country_id: null,
    country: '',
    country_code: '',
    visa_type: '',
    visa_category: '',
    required_documents_text: '', // Text area input
    profession_specific_docs_text: '', // Text area input
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

const updateCountryInfo = () => {
    const selectedCountry = props.countries.find(c => c.id === form.country_id);
    if (selectedCountry) {
        form.country = selectedCountry.name;
        form.country_code = selectedCountry.code;
    }
};

const submitForm = () => {
    // Convert professionDocs array to object format
    const professionDocsObject = {};
    professionDocs.value.forEach(item => {
        if (item.documents.trim()) {
            // If profession already exists, append with index
            let key = item.profession;
            let counter = 1;
            while (professionDocsObject[key]) {
                key = `${item.profession}_${counter}`;
                counter++;
            }
            professionDocsObject[key] = item.documents;
        }
    });

    // Convert text areas to JSON arrays (split by newline)
    const formData = {
        ...form.data(),
        required_documents: form.required_documents_text
            .split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0),
        profession_specific_docs: professionDocsObject,
    };

    // Remove the text versions
    delete formData.required_documents_text;
    delete formData.profession_specific_docs_text;

    form.transform(() => formData).post(route('admin.visa-requirements.store'));
};
</script>
