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
                    <Link
                        :href="route('admin.agency-resources.index')"
                        class="text-sm text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Resources
                    </Link>
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="absolute right-3 top-2 text-gray-500">%</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Override the default commission rate for this specific resource
                            </p>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div class="text-sm text-blue-700">
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
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
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
