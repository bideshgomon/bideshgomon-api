<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    application: Object,
    countries: Array,
    canEdit: Boolean,
    canDelete: Boolean,
});

const isEditing = ref(false);
const processing = ref(false);
const errors = ref({});

const form = ref({
    destination_country_id: props.application.destination_country_id,
    intended_travel_date: props.application.intended_travel_date || '',
    duration_days: props.application.duration_days || '',
    profession: props.application.profession || '',
    user_notes: props.application.user_notes || '',
});

const professions = [
    'Student',
    'Job Holder',
    'Business Person',
];

const statusColors = {
    pending: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    processing: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-600',
};

const statusLabels = {
    pending: 'Pending',
    submitted: 'Submitted',
    processing: 'Processing',
    approved: 'Approved',
    rejected: 'Rejected',
    cancelled: 'Cancelled',
};

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
    if (!isEditing.value) {
        // Reset form if canceling edit
        form.value = {
            destination_country_id: props.application.destination_country_id,
            intended_travel_date: props.application.intended_travel_date || '',
            duration_days: props.application.duration_days || '',
            profession: props.application.profession || '',
            user_notes: props.application.user_notes || '',
        };
        errors.value = {};
    }
};

const saveChanges = async () => {
    processing.value = true;
    errors.value = {};

    try {
        await axios.put(route('api.hajj-umrah-applications.update', props.application.id), form.value);
        router.reload({ only: ['application'] });
        isEditing.value = false;
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
        } else {
            console.error('Error updating application:', error);
            alert('Failed to update application. Please try again.');
        }
    } finally {
        processing.value = false;
    }
};

const deleteApplication = async () => {
    if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
        try {
            await axios.delete(route('api.hajj-umrah-applications.destroy', props.application.id));
            router.visit(route('profile.hajj-umrah.index'));
        } catch (error) {
            console.error('Error deleting application:', error);
            alert('Failed to delete application. Please try again.');
        }
    }
};

const formatDate = (date) => {
    if (!date) return 'Not specified';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Hajj & Umrah Application Details" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <Link
                        :href="route('profile.hajj-umrah.index')"
                        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Applications
                    </Link>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Application Details</h1>
                            <p class="mt-2 text-sm text-gray-600">Reference: {{ application.application_reference }}</p>
                        </div>
                        <span
                            :class="[
                                'px-4 py-2 rounded-full text-sm font-medium inline-flex items-center w-fit',
                                statusColors[application.status] || 'bg-gray-100 text-gray-800',
                            ]"
                        >
                            {{ statusLabels[application.status] || application.status }}
                        </span>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <!-- View Mode -->
                        <div v-if="!isEditing" class="space-y-6">
                            <!-- Destination Country -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Destination Country</h3>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-lg font-semibold text-gray-900">{{ application.destination_country?.name || 'Not specified' }}</p>
                                </div>
                            </div>

                            <!-- Travel Details Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Profession -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Profession</h3>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-base text-gray-900">{{ application.profession || 'Not specified' }}</p>
                                    </div>
                                </div>

                                <!-- Intended Travel Date -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Intended Travel Date</h3>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-base text-gray-900">{{ formatDate(application.intended_travel_date) }}</p>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Duration</h3>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-900">{{ application.duration_days ? `${application.duration_days} days` : 'Not specified' }}</p>
                                    </div>
                                </div>

                                <!-- Created Date -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Application Date</h3>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-900">{{ formatDate(application.created_at) }}</p>
                                    </div>
                                </div>

                                <!-- Last Updated -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Last Updated</h3>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <p class="text-base text-gray-900">{{ formatDate(application.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- User Notes -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Additional Notes</h3>
                                <p v-if="application.user_notes" class="text-base text-gray-900 whitespace-pre-wrap">{{ application.user_notes }}</p>
                                <p v-else class="text-base text-gray-400 italic">No additional notes provided</p>
                            </div>

                            <!-- Admin Notes (if any) -->
                            <div v-if="application.admin_notes" class="border-t border-gray-200 pt-6">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Admin Notes</h3>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ application.admin_notes }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <form v-else @submit.prevent="saveChanges" class="space-y-6">
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
                                <p v-if="errors.destination_country_id" class="mt-2 text-sm text-red-600">
                                    {{ errors.destination_country_id[0] }}
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
                                <p v-if="errors.intended_travel_date" class="mt-2 text-sm text-red-600">
                                    {{ errors.intended_travel_date[0] }}
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
                                <p v-if="errors.duration_days" class="mt-2 text-sm text-red-600">
                                    {{ errors.duration_days[0] }}
                                </p>
                            </div>

                            <!-- Profession -->
                            <div>
                                <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                                    Profession
                                </label>
                                <select
                                    id="profession"
                                    v-model="form.profession"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">Select your profession</option>
                                    <option v-for="profession in professions" :key="profession" :value="profession">
                                        {{ profession }}
                                    </option>
                                </select>
                                <p v-if="errors.profession" class="mt-2 text-sm text-red-600">
                                    {{ errors.profession[0] }}
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
                                <p v-if="errors.user_notes" class="mt-2 text-sm text-red-600">
                                    {{ errors.user_notes[0] }}
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-between">
                        <div class="flex flex-col-reverse sm:flex-row gap-3">
                            <button
                                v-if="canDelete && !isEditing"
                                @click="deleteApplication"
                                class="inline-flex items-center justify-center px-6 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Application
                            </button>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row gap-3">
                            <button
                                v-if="isEditing"
                                @click="toggleEdit"
                                type="button"
                                class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                v-if="!isEditing && canEdit"
                                @click="toggleEdit"
                                class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Application
                            </button>
                            <button
                                v-if="isEditing"
                                @click="saveChanges"
                                :disabled="processing"
                                class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div v-if="!canEdit && !isEditing" class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This application cannot be edited because it has been {{ application.status }}. Please contact support if you need to make changes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
