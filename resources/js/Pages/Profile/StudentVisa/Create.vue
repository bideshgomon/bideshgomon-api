<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    countries: Array,
    educationLevels: Array,
    studyFields: Array,
});

const form = useForm({
    destination_country_id: '',
    education_level: '',
    study_field: '',
    institution_name: '',
    course_name: '',
    intended_start_date: '',
    course_duration_months: '',
    has_admission_letter: false,
    has_ielts_toefl: false,
    english_test_type: '',
    english_test_score: '',
    previous_education_gpa: '',
    user_notes: '',
});

const englishTests = ['IELTS', 'TOEFL', 'PTE', 'Duolingo', 'Other', 'Not Taken'];

const selectedCountryName = computed(() => {
    if (!form.destination_country_id) return '';
    const country = props.countries.find(c => c.id === form.destination_country_id);
    return country ? country.name : '';
});

const submit = () => {
    form.post(route('api.student-visa-applications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Backend will handle redirect
        },
    });
};
</script>

<template>
    <Head title="Create Student Visa Application" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 sm:mb-8">
                    <Link :href="route('profile.student-visa.index')" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Applications
                    </Link>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">New Student Visa Application</h1>
                    <p class="mt-2 text-sm text-gray-600">Apply for a student visa to study abroad</p>
                </div>

                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Destination Country <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.destination_country_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Select destination country</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                            </select>
                            <p v-if="form.errors.destination_country_id" class="mt-2 text-sm text-red-600">{{ form.errors.destination_country_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Education Level <span class="text-red-500">*</span></label>
                            <select v-model="form.education_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Select education level</option>
                                <option v-for="level in educationLevels" :key="level" :value="level">{{ level }}</option>
                            </select>
                            <p v-if="form.errors.education_level" class="mt-2 text-sm text-red-600">{{ form.errors.education_level }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Study Field</label>
                            <select v-model="form.study_field" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select study field</option>
                                <option v-for="field in studyFields" :key="field" :value="field">{{ field }}</option>
                            </select>
                            <p v-if="form.errors.study_field" class="mt-2 text-sm text-red-600">{{ form.errors.study_field }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Institution Name</label>
                            <input type="text" v-model="form.institution_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Harvard University">
                            <p v-if="form.errors.institution_name" class="mt-2 text-sm text-red-600">{{ form.errors.institution_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Course Name</label>
                            <input type="text" v-model="form.course_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Master of Computer Science">
                            <p v-if="form.errors.course_name" class="mt-2 text-sm text-red-600">{{ form.errors.course_name }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Intended Start Date</label>
                                <input type="date" v-model="form.intended_start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p v-if="form.errors.intended_start_date" class="mt-2 text-sm text-red-600">{{ form.errors.intended_start_date }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course Duration (Months)</label>
                                <input type="number" v-model="form.course_duration_months" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="1" placeholder="e.g., 24">
                                <p v-if="form.errors.course_duration_months" class="mt-2 text-sm text-red-600">{{ form.errors.course_duration_months }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.has_admission_letter" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">I have an admission letter</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.has_ielts_toefl" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">I have taken an English proficiency test</span>
                            </label>
                        </div>

                        <div v-if="form.has_ielts_toefl" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">English Test Type</label>
                                <select v-model="form.english_test_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select test type</option>
                                    <option v-for="test in englishTests" :key="test" :value="test">{{ test }}</option>
                                </select>
                                <p v-if="form.errors.english_test_type" class="mt-2 text-sm text-red-600">{{ form.errors.english_test_type }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Test Score</label>
                                <input type="text" v-model="form.english_test_score" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 7.5 or 100">
                                <p v-if="form.errors.english_test_score" class="mt-2 text-sm text-red-600">{{ form.errors.english_test_score }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Previous Education GPA/Grade</label>
                            <input type="text" v-model="form.previous_education_gpa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 3.8 or A">
                            <p v-if="form.errors.previous_education_gpa" class="mt-2 text-sm text-red-600">{{ form.errors.previous_education_gpa }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea v-model="form.user_notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional information..."></textarea>
                            <p v-if="form.errors.user_notes" class="mt-2 text-sm text-red-600">{{ form.errors.user_notes }}</p>
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
                                            <li>Multiple agencies will provide quotes for processing your student visa</li>
                                            <li>Compare quotes and select the best option for you</li>
                                            <li>Your chosen agency will guide you through the entire process</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                        <Link :href="route('profile.student-visa.index')" class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
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
