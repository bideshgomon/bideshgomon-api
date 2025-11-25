<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    countries: Array,
});

const form = useForm({
    destination_country_id: '',
    intended_travel_date: '',
    duration_days: '',
    user_notes: '',
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
