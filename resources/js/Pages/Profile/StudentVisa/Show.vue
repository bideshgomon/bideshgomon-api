<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    application: Object, // Passed from controller
    // Static data passed again if editing allowed on this page, otherwise remove
    // countries: Array,
    // universities: Array,
    // courses: Array,
});

// Form for fields user can update (currently just notes)
const form = useForm({
    _method: 'PUT',
    user_notes: props.application.user_notes || '',
    // Add other editable fields here if logic changes
});

const submitUpdate = () => {
    // Use correct API route name for user updating their application
    form.post(route('api.student-visa-applications.update', props.application.id), {
        preserveScroll: true, // Keep scroll position on update
         onSuccess: () => {
             alert('Notes updated successfully!'); // Simple feedback
        },
        onError: (errors) => {
            console.error("Error updating student visa application:", errors);
            alert('Failed to update notes. Please check for errors.'); // Simple feedback
        }
    });
};

// Helper to format dates
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Helper for status styling (optional)
const statusClass = (status) => {
    switch (status) {
        case 'pending':
        case 'documents_required':
        case 'submitted_to_uni':
        case 'visa_processing':
            return 'text-yellow-700 bg-yellow-100';
        case 'offer_received':
        case 'visa_approved':
            return 'text-green-700 bg-green-100';
        case 'rejected':
            return 'text-red-700 bg-red-100';
        default:
            return 'text-gray-700 bg-gray-100';
    }
};

</script>

<template>
    <Head :title="'Student Visa Application - ' + application.destination_country?.name" />

     <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Visa Application Details</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('profile.student-visa.index')" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1">
                          <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                        </svg>
                        Back to Applications
                    </Link>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 space-y-6">
                        <div class="flex justify-between items-start">
                             <h3 class="text-lg font-semibold leading-6 text-gray-900">Application Overview</h3>
                             <span class="px-3 py-1 text-xs font-medium rounded-full capitalize" :class="statusClass(application.status)">
                                 {{ application.status.replace('_', ' ') }}
                             </span>
                         </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm border-t border-gray-200 pt-6">
                            <div><span class="font-semibold text-gray-600 block mb-1">Applicant:</span> {{ application.user?.name }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Applied On:</span> {{ formatDate(application.created_at) }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Destination:</span> {{ application.destination_country?.name }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">University:</span> {{ application.university?.name ?? 'Not Specified' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Course:</span> {{ application.course?.name ?? 'Not Specified' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Intake:</span> {{ application.intended_intake_month || 'N/A' }} {{ application.intended_intake_year || '' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Current Education:</span> {{ application.current_education_level || 'N/A' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">English Test:</span> {{ application.english_proficiency_test || 'N/A' }} ({{ application.english_proficiency_score || 'N/A' }})</div>
                             <div class="md:col-span-2"><span class="font-semibold text-gray-600 block mb-1">Assigned Agency:</span> {{ application.agency?.name ?? 'Not Assigned Yet' }}</div>
                             <div class="md:col-span-2"><span class="font-semibold text-gray-600 block mb-1">Application Reference:</span> {{ application.application_reference ?? 'N/A' }}</div>
                        </div>

                        <div class="border-t border-gray-200 pt-6 space-y-4">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Notes</h3>
                             <div>
                                <InputLabel value="Admin/Agency Notes" class="text-sm font-medium text-gray-700"/>
                                <div class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 rounded-md border border-gray-200 min-h-[60px]">
                                    {{ application.admin_notes || 'No notes from admin or agency yet.' }}
                                </div>
                            </div>
                             <div>
                                <form @submit.prevent="submitUpdate">
                                    <InputLabel for="user_notes" value="Your Notes (Editable)" class="text-sm font-medium text-gray-700"/>
                                    <TextareaInput id="user_notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" v-model="form.user_notes" rows="4" />
                                    <InputError class="mt-1 text-xs" :message="form.errors.user_notes" />
                                    <div class="flex justify-end mt-3">
                                         <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Update My Notes
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>

                         </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>