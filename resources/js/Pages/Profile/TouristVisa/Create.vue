<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    countries: Array,
});

const form = useForm({
    destination_country_id: '',
    intended_travel_date: '',
    duration_days: '',
    profession: '',
    user_notes: '',
});

// Updated 10:12:06
const professions = [
    'Student',
    'Job Holder',
    'Business Person',
];

const requirements = ref(null);
const loadingRequirements = ref(false);

// Fetch requirements when both country and profession are selected
watch([() => form.destination_country_id, () => form.profession], async ([countryId, profession]) => {
    if (countryId && profession) {
        loadingRequirements.value = true;
        try {
            const response = await axios.get(route('profile.tourist-visa.requirements', countryId), {
                params: { profession }
            });
            requirements.value = response.data.requirements;
        } catch (error) {
            console.error('Error fetching requirements:', error);
            requirements.value = null;
        } finally {
            loadingRequirements.value = false;
        }
    } else {
        requirements.value = null;
    }
});

const selectedCountryName = computed(() => {
    if (!form.destination_country_id) return '';
    const country = props.countries.find(c => c.id === form.destination_country_id);
    return country ? country.name : '';
});

const submit = () => {
    form.post(route('api.tourist-visa-applications.store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Backend will handle redirect
        },
    });
};
</script>

<template>
    <Head title="Create Tourist Visa Application" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <Link
                        :href="route('profile.tourist-visa.index')"
                        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Applications
                    </Link>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">New Tourist Visa Application</h1>
                    <p class="mt-2 text-sm text-gray-600">Fill in the details for your tourist visa application</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Destination Country -->
                        <div>
                            <label for="destination_country_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Destination Country <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="destination_country_id"
                                v-model="form.destination_country_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >
                                <option value="">Select destination country</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.destination_country_id" class="mt-2 text-sm text-red-600">
                                {{ form.errors.destination_country_id }}
                            </p>
                        </div>

                        <!-- Intended Travel Date -->
                        <div>
                            <label for="intended_travel_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Intended Travel Date
                            </label>
                            <input
                                type="date"
                                id="intended_travel_date"
                                v-model="form.intended_travel_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                :min="new Date().toISOString().split('T')[0]"
                            />
                            <p v-if="form.errors.intended_travel_date" class="mt-2 text-sm text-red-600">
                                {{ form.errors.intended_travel_date }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                When do you plan to travel? (Optional)
                            </p>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Duration (Days)
                            </label>
                            <input
                                type="number"
                                id="duration_days"
                                v-model="form.duration_days"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                min="1"
                                max="365"
                                placeholder="e.g., 14"
                            />
                            <p v-if="form.errors.duration_days" class="mt-2 text-sm text-red-600">
                                {{ form.errors.duration_days }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                How many days do you plan to stay? (Optional, max 365 days)
                            </p>
                        </div>

                        <!-- Profession -->
                        <div>
                            <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                                Profession <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="profession"
                                v-model="form.profession"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >
                                <option value="">Select your profession</option>
                                <option v-for="profession in professions" :key="profession" :value="profession">
                                    {{ profession }}
                                </option>
                            </select>
                            <p v-if="form.errors.profession" class="mt-2 text-sm text-red-600">
                                {{ form.errors.profession }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Your profession affects visa requirements and processing
                            </p>
                        </div>

                        <!-- User Notes -->
                        <div>
                            <label for="user_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes
                            </label>
                            <textarea
                                id="user_notes"
                                v-model="form.user_notes"
                                rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Any additional information or special requirements..."
                            ></textarea>
                            <p v-if="form.errors.user_notes" class="mt-2 text-sm text-red-600">
                                {{ form.errors.user_notes }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Add any additional information that might be helpful for your application (Optional, max 5000 characters)
                            </p>
                        </div>

                        <!-- Document Requirements Display -->
                        <div v-if="loadingRequirements" class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                                <span class="ml-3 text-gray-600">Loading requirements...</span>
                            </div>
                        </div>

                        <div v-else-if="requirements" class="border border-indigo-200 rounded-lg bg-indigo-50">
                            <div class="p-4 border-b border-indigo-200 bg-indigo-100">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-base font-semibold text-indigo-900">
                                            Required Documents for {{ selectedCountryName }}
                                        </h3>
                                        <p class="text-sm text-indigo-700 mt-1">
                                            Based on your profession: <strong>{{ form.profession }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4 space-y-4">
                                <!-- Mandatory Documents -->
                                <div v-if="requirements.mandatory_documents && requirements.mandatory_documents.length > 0">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Required
                                        </span>
                                        <span>Mandatory Documents ({{ requirements.mandatory_documents.length }})</span>
                                    </h4>
                                    <ul class="space-y-2">
                                        <li v-for="doc in requirements.mandatory_documents" :key="doc.id" class="flex items-start gap-3 bg-white rounded-lg p-3 border border-gray-200">
                                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ doc.name }}</p>
                                                <p v-if="doc.specific_notes" class="text-sm text-gray-600 mt-1">{{ doc.specific_notes }}</p>
                                                <span class="inline-block mt-1 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ doc.category }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Optional Documents -->
                                <div v-if="requirements.optional_documents && requirements.optional_documents.length > 0">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Optional
                                        </span>
                                        <span>Recommended Documents ({{ requirements.optional_documents.length }})</span>
                                    </h4>
                                    <ul class="space-y-2">
                                        <li v-for="doc in requirements.optional_documents" :key="doc.id" class="flex items-start gap-3 bg-white rounded-lg p-3 border border-gray-200">
                                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ doc.name }}</p>
                                                <p v-if="doc.specific_notes" class="text-sm text-gray-600 mt-1">{{ doc.specific_notes }}</p>
                                                <span class="inline-block mt-1 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ doc.category }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <div class="flex gap-2">
                                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-sm text-yellow-800">
                                            These requirements are specific to {{ selectedCountryName }} for tourist visa applications. Make sure to prepare all mandatory documents before proceeding.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Your application will be saved as "Pending"</li>
                                            <li>You can edit or submit it later from the applications list</li>
                                            <li>Once submitted, our team will review and process your application</li>
                                            <li>You'll receive updates via email and notifications</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                        <Link
                            :href="route('profile.tourist-visa.index')"
                            class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Creating...' : 'Create Application' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

