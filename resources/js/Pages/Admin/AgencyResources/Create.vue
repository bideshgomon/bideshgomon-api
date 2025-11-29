<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    agencies: Array,
    serviceModules: Array,
    countries: Array,
    resourceTypes: Object,
});

const form = useForm({
    agency_id: '',
    service_module_id: '',
    resource_type: 'university',
    resource_name: '',
    resource_code: '',
    country_id: '',
    city: '',
    description: '',
    special_commission_rate: '',
    metadata: {},
});

const checkingAvailability = ref(false);
const availabilityMessage = ref('');
const isAvailable = ref(true);

const demoDataSets = [
    {
        name: 'Harvard University - Student Visa',
        data: {
            resource_type: 'university',
            resource_name: 'Harvard University',
            resource_code: 'HARV-001',
            city: 'Cambridge, Massachusetts',
            description: 'World-renowned Ivy League university offering undergraduate and graduate programs across various disciplines. Partner agency handles all student visa applications for this institution.',
            special_commission_rate: '25',
        }
    },
    {
        name: 'Oxford International School - Study Abroad',
        data: {
            resource_type: 'school',
            resource_name: 'Oxford International School',
            resource_code: 'OIS-UK-2024',
            city: 'Oxford',
            description: 'Premium international school with A-Level and IB programs. Exclusive partnership for international student admissions and visa processing.',
            special_commission_rate: '20',
        }
    },
    {
        name: 'British Council Language Centre',
        data: {
            resource_type: 'language_center',
            resource_name: 'British Council English Language Centre',
            resource_code: 'BC-ENG-001',
            city: 'London',
            description: 'Official British Council language training center. Agency has exclusive rights for IELTS preparation courses and student visa applications.',
            special_commission_rate: '15',
        }
    },
    {
        name: 'Global Tech Training Institute',
        data: {
            resource_type: 'training_institute',
            resource_name: 'Global Tech Training Institute',
            resource_code: 'GTTI-2024',
            city: 'Toronto',
            description: 'Professional IT training institute offering coding bootcamps and certification programs. Exclusive partner for international student enrollment.',
            special_commission_rate: '18',
        }
    },
    {
        name: 'JobsGlobal Portal - Work Visa',
        data: {
            resource_type: 'job_portal',
            resource_name: 'JobsGlobal International',
            resource_code: 'JG-INT-001',
            city: 'Dubai',
            description: 'Premium job portal connecting skilled workers with international employers. Exclusive agency for visa sponsorship applications.',
            special_commission_rate: '30',
        }
    },
];

const fillDemoData = (demoSet) => {
    // Fill in the demo data
    Object.keys(demoSet.data).forEach(key => {
        form[key] = demoSet.data[key];
    });
    
    // Auto-select first agency if not selected
    if (!form.agency_id && props.agencies.length > 0) {
        form.agency_id = props.agencies[0].id;
    }
    
    // Auto-select first service module if not selected
    if (!form.service_module_id && props.serviceModules.length > 0) {
        form.service_module_id = props.serviceModules[0].id;
    }
    
    // Auto-select a country based on the demo (try to match by city)
    if (!form.country_id && props.countries.length > 0) {
        const cityCountryMap = {
            'Cambridge, Massachusetts': 'United States',
            'Oxford': 'United Kingdom',
            'London': 'United Kingdom',
            'Toronto': 'Canada',
            'Dubai': 'United Arab Emirates',
        };
        
        const countryName = cityCountryMap[demoSet.data.city];
        const country = props.countries.find(c => c.name === countryName);
        if (country) {
            form.country_id = country.id;
        } else {
            // Default to first country
            form.country_id = props.countries[0].id;
        }
    }
    
    // Check availability after filling
    setTimeout(() => {
        checkAvailability();
    }, 100);
};

const clearForm = () => {
    form.reset();
    availabilityMessage.value = '';
    isAvailable.value = true;
};

const showDemoMenu = ref(false);

const selectedService = computed(() => {
    return props.serviceModules.find(s => s.id === form.service_module_id);
});

const checkAvailability = async () => {
    if (!form.service_module_id || !form.resource_type || !form.resource_name) {
        return;
    }

    checkingAvailability.value = true;
    try {
        const response = await fetch(route('admin.agency-resources.check-availability'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                service_module_id: form.service_module_id,
                resource_type: form.resource_type,
                resource_name: form.resource_name,
            }),
        });
        const data = await response.json();
        isAvailable.value = data.available;
        availabilityMessage.value = data.message;
    } catch (error) {
        console.error('Error checking availability:', error);
    } finally {
        checkingAvailability.value = false;
    }
};

const submit = () => {
    form.post(route('admin.agency-resources.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Add Agency Resource" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <Link
                            :href="route('admin.agency-resources.index')"
                            class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center transition"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Resources
                        </Link>
                        <div class="flex gap-2">
                                <button
                                    type="button"
                                    @click="clearForm"
                                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-700 border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Clear
                                </button>
                                <div class="relative">
                                    <button
                                        type="button"
                                        @click="showDemoMenu = !showDemoMenu"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition"
                                    >
                                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                        </svg>
                                        Demo Data
                                        <svg class="ml-1.5 h-5 w-5 transition-transform" :class="{ 'rotate-180': showDemoMenu }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>                                <!-- Demo Data Dropdown Menu -->
                                <div
                                    v-show="showDemoMenu"
                                    class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                >
                                    <div class="py-1">
                                        <div class="px-4 py-2 border-b border-gray-200">
                                            <p class="text-xs font-medium text-gray-900">Select Demo Resource</p>
                                            <p class="text-xs text-gray-500">Choose a sample to auto-fill the form</p>
                                        </div>
                                        <button
                                            v-for="(demo, index) in demoDataSets"
                                            :key="index"
                                            type="button"
                                            @click="fillDemoData(demo); showDemoMenu = false"
                                            class="w-full text-left px-4 py-3 text-sm hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors"
                                        >
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ resourceTypes[demo.data.resource_type] }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-gray-900 truncate">{{ demo.data.resource_name }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ demo.data.city }}</p>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Add Exclusive Resource</h2>
                    <p class="mt-1 text-sm text-gray-600">Assign an exclusive resource (university, school, etc.) to an agency</p>
                </div>

                <!-- Form -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Agency Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Agency <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.agency_id"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select Agency</option>
                                <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                    {{ agency.name }} ({{ agency.email }})
                                </option>
                            </select>
                            <p v-if="form.errors.agency_id" class="mt-1 text-sm text-red-600">{{ form.errors.agency_id }}</p>
                        </div>

                        <!-- Service Module -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Service Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.service_module_id"
                                @change="checkAvailability"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select Service (Exclusive Resource Only)</option>
                                <option v-for="service in serviceModules" :key="service.id" :value="service.id">
                                    {{ service.name }}
                                </option>
                            </select>
                            <p v-if="selectedService" class="mt-1 text-xs text-gray-500">
                                Commission Rate: {{ selectedService.platform_commission_rate }}%
                            </p>
                            <p v-if="form.errors.service_module_id" class="mt-1 text-sm text-red-600">{{ form.errors.service_module_id }}</p>
                        </div>

                        <!-- Resource Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Resource Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.resource_type"
                                @change="checkAvailability"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="(label, value) in resourceTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <p v-if="form.errors.resource_type" class="mt-1 text-sm text-red-600">{{ form.errors.resource_type }}</p>
                        </div>

                        <!-- Resource Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Resource Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.resource_name"
                                @blur="checkAvailability"
                                type="text"
                                required
                                placeholder="e.g., Harvard University, Oxford International School"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="checkingAvailability" class="mt-1 text-sm text-gray-500">Checking availability...</p>
                            <p
                                v-else-if="availabilityMessage"
                                :class="isAvailable ? 'text-green-600' : 'text-red-600'"
                                class="mt-1 text-sm font-medium"
                            >
                                {{ availabilityMessage }}
                            </p>
                            <p v-if="form.errors.resource_name" class="mt-1 text-sm text-red-600">{{ form.errors.resource_name }}</p>
                        </div>

                        <!-- Resource Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Resource Code (Optional)
                            </label>
                            <input
                                v-model="form.resource_code"
                                type="text"
                                placeholder="University code, school ID, etc."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p class="mt-1 text-xs text-gray-500">Internal identifier for the resource</p>
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.country_id"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select Country</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }} ({{ country.iso_code_2 }})
                                </option>
                            </select>
                            <p v-if="form.errors.country_id" class="mt-1 text-sm text-red-600">{{ form.errors.country_id }}</p>
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                City (Optional)
                            </label>
                            <input
                                v-model="form.city"
                                type="text"
                                placeholder="e.g., Cambridge, Boston"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description (Optional)
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Additional details about this resource..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <!-- Special Commission Rate -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Special Commission Rate (Optional)
                            </label>
                            <div class="relative">
                                <input
                                    v-model="form.special_commission_rate"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    placeholder="Leave empty to use default"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <span class="absolute right-3 top-2 text-gray-500">%</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Override the default commission rate for this specific resource
                            </p>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div class="text-sm text-gray-700">
                                    <strong>Exclusive Resource Model:</strong> Only ONE agency can own each resource. When a user applies to this resource (e.g., Harvard University), they will be directed exclusively to the assigned agency. This requires admin approval.
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <Link
                                :href="route('admin.agency-resources.index')"
                                class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || !isAvailable"
                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Resource Assignment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
