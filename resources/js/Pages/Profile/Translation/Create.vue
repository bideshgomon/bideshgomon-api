<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    countries: Array,
});

const form = useForm({
    destination_country_id: '',
    document_type: '',
    source_language: '',
    target_language: '',
    page_count: '',
    certification_type: '',
    is_urgent: false,
    required_by_date: '',
    user_notes: '',
});

const submit = () => {
    form.post(route('api.translation-applications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Backend will handle redirect
        },
    });
};
</script>

<template>
    <Head title="Create Translation Application" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 sm:mb-8">
                    <Link :href="route('profile.translation.index')" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Applications
                    </Link>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">New Translation Application</h1>
                    <p class="mt-2 text-sm text-gray-600">Apply for document translation services</p>
                </div>

                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Form fields will be added here -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea v-model="form.user_notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional information..."></textarea>
                        </div>

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
                                            <li>Multiple agencies will provide quotes</li>
                                            <li>Compare and select the best option</li>
                                            <li>Your chosen agency will process your application</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                        <Link :href="route('profile.translation.index')" class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ form.processing ? 'Creating...' : 'Create Application' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>