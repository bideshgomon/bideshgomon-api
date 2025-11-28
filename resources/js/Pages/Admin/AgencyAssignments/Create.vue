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

                            <!-- Service Module Selection -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Service</h3>
                                
                                <!-- Enable Multiple Service Selection -->
                                <div class="mb-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                v-model="enableMultipleServices"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <label class="font-medium text-gray-900">Assign multiple services at once</label>
                                            <p class="text-sm text-gray-500">Select multiple services to create bulk assignments with same settings</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ enableMultipleServices ? 'Service Modules *' : 'Service Module *' }}
                                    </label>
                                    
                                    <!-- Single Service Selection -->
                                    <select 
                                        v-if="!enableMultipleServices"
                                        v-model="selectedServiceModule"
                                        @change="onServiceModuleChange"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Select a service</option>
                                        <option v-for="module in serviceModules" :key="module.id" :value="module.id">
                                            {{ module.name }} ({{ module.service_type }})
                                        </option>
                                    </select>
                                    
                                    <!-- Multiple Service Selection -->
                                    <div v-else class="border border-gray-300 rounded-md max-h-60 overflow-y-auto p-3 bg-white">
                                        <div v-for="module in serviceModules" :key="module.id" class="flex items-center mb-2">
                                            <input 
                                                :id="'service-' + module.id"
                                                v-model="selectedServiceModules"
                                                :value="module.id"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <label :for="'service-' + module.id" class="ml-2 text-sm text-gray-700">
                                                {{ module.name }} <span class="text-gray-500">({{ module.service_type }})</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <p v-if="enableMultipleServices && selectedServiceModules.length > 0" class="mt-2 text-sm text-gray-600">
                                        {{ selectedServiceModules.length }} services selected
                                    </p>
                                    <p v-if="form.errors.service_module_id" class="mt-1 text-sm text-red-600">{{ form.errors.service_module_id }}</p>
                                </div>
                            </div>

                            <!-- Assignment Scope -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment Scope</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Scope *</label>
                                    <select 
                                        v-model="form.assignment_scope"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="global">Global (All Countries)</option>
                                        <option value="country_specific">Country Specific</option>
                                        <option value="visa_specific">Visa Type Specific</option>
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Global: Agency handles all applications. Country: Specific to one country. Visa: Specific country + visa type.
                                    </p>
                                </div>
                            </div>

                            <!-- Country & Visa Type -->
                            <div v-if="form.assignment_scope !== 'global'">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Country & Visa Type</h3>
                                
                                <!-- Enable Multiple Country Selection -->
                                <div class="mb-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                v-model="enableMultipleCountries"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <label class="font-medium text-gray-900">Assign multiple countries at once</label>
                                            <p class="text-sm text-gray-500">Select multiple countries to create bulk assignments with same settings</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ enableMultipleCountries ? 'Countries *' : 'Country *' }}
                                    </label>
                                    
                                    <!-- Single Country Selection -->
                                    <select 
                                        v-if="!enableMultipleCountries"
                                        v-model="selectedCountry"
                                        @change="onCountryChange"
                                        :required="form.assignment_scope !== 'global'"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Select a country</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">
                                            {{ country.name }} ({{ country.iso_code_2 }})
                                        </option>
                                    </select>
                                    
                                    <!-- Multiple Country Selection -->
                                    <div v-else class="border border-gray-300 rounded-md max-h-60 overflow-y-auto p-3 bg-white">
                                        <div v-for="country in countries" :key="country.id" class="flex items-center mb-2">
                                            <input 
                                                :id="'country-' + country.id"
                                                v-model="selectedCountries"
                                                :value="country.id"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <label :for="'country-' + country.id" class="ml-2 text-sm text-gray-700">
                                                {{ country.name }} ({{ country.iso_code_2 }})
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <p v-if="enableMultipleCountries && selectedCountries.length > 0" class="mt-2 text-sm text-gray-600">
                                        {{ selectedCountries.length }} countries selected
                                    </p>
                                    <p v-if="form.errors.country" class="mt-1 text-sm text-red-600">{{ form.errors.country }}</p>
                                </div>                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Country Code</label>
                                        <input 
                                            v-model="form.country_code"
                                            type="text"
                                            readonly
                                            class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                    </div>

                                    <div class="md:col-span-2" v-if="form.assignment_scope === 'visa_specific'">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type *</label>
                                        <select 
                                            v-model="form.visa_type_id"
                                            @change="onVisaTypeChange"
                                            :required="form.assignment_scope === 'visa_specific'"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select visa type</option>
                                            <option v-for="type in visaTypes" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.visa_type" class="mt-1 text-sm text-red-600">{{ form.errors.visa_type }}</p>
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
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    agencies: Array,
    serviceModules: Array,
    countries: Array,
    visaTypes: Array,
});

const selectedCountry = ref('');
const selectedCountries = ref([]);
const enableMultipleCountries = ref(false);
const selectedServiceModule = ref('');
const selectedServiceModules = ref([]);
const enableMultipleServices = ref(false);

const form = useForm({
    agency_id: '',
    service_module_id: '',
    service_module_ids: [],  // For multiple services
    country_ids: [],  // For multiple countries
    country_id: '',
    country: '',
    country_code: '',
    visa_type_id: '',
    visa_type: '',
    assignment_scope: 'visa_specific',
    platform_commission_rate: 15,
    commission_type: 'percentage',
    fixed_commission_amount: null,
    can_edit_requirements: true,
    can_set_fees: true,
    can_process_applications: true,
    assignment_notes: '',
    auto_assign_requirements: true,
});

// Watch for service module checkbox changes
watch(enableMultipleServices, (newValue) => {
    if (newValue) {
        // Switching to multiple - clear single selection
        selectedServiceModule.value = '';
        form.service_module_id = '';
    } else {
        // Switching to single - clear multiple selections
        selectedServiceModules.value = [];
        form.service_module_ids = [];
    }
});

// Watch for service module selection changes
watch(selectedServiceModules, (newValue) => {
    if (enableMultipleServices.value) {
        onServiceModuleChange();
    }
});

const onCountryChange = () => {
    if (!enableMultipleCountries.value) {
        const country = props.countries.find(c => c.id === parseInt(selectedCountry.value));
        if (country) {
            form.country = country.name;
            form.country_code = country.iso_code_2;
            form.country_id = country.id;
            form.country_ids = [];
        }
    } else {
        // For multiple countries, we'll handle this in submit
        form.country_ids = selectedCountries.value;
        form.country_id = '';
        form.country = '';
        form.country_code = '';
    }
};

const onServiceModuleChange = () => {
    if (!enableMultipleServices.value) {
        const module = props.serviceModules.find(m => m.id === parseInt(selectedServiceModule.value));
        if (module) {
            form.service_module_id = module.id;
            form.service_module_ids = [];
            // Auto-set scope based on service type
            if (module.service_type === 'premade' || module.service_type === 'api_based') {
                form.assignment_scope = 'global';
            }
        }
    } else {
        form.service_module_ids = selectedServiceModules.value;
        form.service_module_id = '';
    }
};

const onVisaTypeChange = (event) => {
    const visaTypeId = event.target.value;
    const visaType = props.visaTypes.find(vt => vt.id === parseInt(visaTypeId));
    if (visaType) {
        form.visa_type_id = visaType.id;
        form.visa_type = visaType.name;
    }
};

const submitForm = () => {
    // Update country_ids array when submitting
    if (enableMultipleCountries.value) {
        form.country_ids = selectedCountries.value;
    } else {
        form.country_ids = [];
    }
    
    // Update service_module_ids array when submitting
    if (enableMultipleServices.value) {
        form.service_module_ids = selectedServiceModules.value;
    } else {
        form.service_module_ids = [];
    }
    
    form.post(route('admin.agency-assignments.store'));
};
</script>
