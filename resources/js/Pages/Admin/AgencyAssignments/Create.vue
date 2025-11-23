<template>
    <Head title="Assign Agency to Country" />

    <AdminLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Assign Agency to Country</h1>
                            <p class="mt-2 text-gray-600">Set up agency management for visa applications</p>
                        </div>
                        <Link 
                            :href="route('admin.agency-assignments.index')"
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
                            <!-- Agency Selection -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Agency</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Agency *</label>
                                    <select 
                                        v-model="form.agency_id"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Select an agency</option>
                                        <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                            {{ agency.name }} ({{ agency.email }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.agency_id" class="mt-1 text-sm text-red-600">{{ form.errors.agency_id }}</p>
                                </div>
                            </div>

                            <!-- Country & Visa Type -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Country & Visa Type</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                        <select 
                                            v-model="selectedCountry"
                                            @change="onCountryChange"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select a country</option>
                                            <option v-for="country in countries" :key="country.country" :value="country.country">
                                                {{ country.country }} ({{ country.country_code }})
                                            </option>
                                        </select>
                                        <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">{{ form.errors.country }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country Code *</label>
                                        <input 
                                            v-model="form.country_code"
                                            type="text"
                                            required
                                            readonly
                                            class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type *</label>
                                        <select 
                                            v-model="form.visa_type"
                                            required
                                            :disabled="!selectedCountry"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select visa type</option>
                                            <option v-for="type in availableVisaTypes" :key="type" :value="type">
                                                {{ type }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.visa_type" class="mt-1 text-sm text-red-600">{{ form.errors.visa_type }}</p>
                                        <p v-if="!selectedCountry" class="mt-1 text-sm text-gray-500">Select a country first</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Commission Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Commission Settings</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Type *</label>
                                        <select 
                                            v-model="form.commission_type"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="percentage">Percentage</option>
                                            <option value="fixed">Fixed Amount</option>
                                        </select>
                                    </div>

                                    <div v-if="form.commission_type === 'percentage'">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Rate (%) *</label>
                                        <input 
                                            v-model.number="form.platform_commission_rate"
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 15"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">Platform commission on agency earnings</p>
                                    </div>

                                    <div v-if="form.commission_type === 'fixed'">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Commission Amount (৳) *</label>
                                        <input 
                                            v-model.number="form.fixed_commission_amount"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., 5000"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">Fixed amount per application</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Agency Permissions -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Agency Permissions</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                v-model="form.can_edit_requirements"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <label class="font-medium text-gray-900">Can Edit Requirements</label>
                                            <p class="text-sm text-gray-500">Allow agency to modify visa requirements</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                v-model="form.can_set_fees"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <label class="font-medium text-gray-900">Can Set Fees</label>
                                            <p class="text-sm text-gray-500">Allow agency to set their service and processing fees</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                v-model="form.can_process_applications"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <label class="font-medium text-gray-900">Can Process Applications</label>
                                            <p class="text-sm text-gray-500">Allow agency to process visa applications</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Settings</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Notes</label>
                                    <textarea 
                                        v-model="form.assignment_notes"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Add any notes about this assignment..."
                                    />
                                </div>

                                <div class="mt-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                v-model="form.auto_assign_requirements"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <label class="font-medium text-gray-900">Auto-assign existing requirements</label>
                                            <p class="text-sm text-gray-500">Automatically assign existing visa requirements for this country to the agency</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-lg">
                            <Link 
                                :href="route('admin.agency-assignments.index')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Assigning...' : 'Assign Agency' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
  </AdminLayout>
</template><script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    agencies: Array,
    countries: Array,
});

const selectedCountry = ref('');
const availableVisaTypes = ref([]);

const form = useForm({
    agency_id: '',
    country: '',
    country_code: '',
    visa_type: '',
    platform_commission_rate: 15,
    commission_type: 'percentage',
    fixed_commission_amount: null,
    can_edit_requirements: true,
    can_set_fees: true,
    can_process_applications: true,
    assignment_notes: '',
    auto_assign_requirements: true,
});

const onCountryChange = () => {
    const country = props.countries.find(c => c.country === selectedCountry.value);
    if (country) {
        form.country = country.country;
        form.country_code = country.country_code;
        availableVisaTypes.value = country.visa_types || [];
        form.visa_type = '';
    }
};

const submitForm = () => {
    form.post(route('admin.agency-assignments.store'));
};
</script>
