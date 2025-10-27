<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue'; // Import ref

const props = defineProps({
    application: Object, agencies: Array, statuses: Array,
});

// Use ref for success message
const successMessage = ref('');

const form = useForm({
    _method: 'PUT',
    status: props.application.status,
    agency_id: props.application.agency_id || '',
    admin_notes: props.application.admin_notes || '',
});

const submitUpdate = () => {
    successMessage.value = ''; // Clear previous message
    // Use correct Admin API route name
    form.post(route('api.admin.student-visa-applications.update', props.application.id), {
        preserveScroll: true,
         onSuccess: () => {
             successMessage.value = 'Application updated successfully!';
             // Note: The 'application' prop might not auto-update if only partially loaded before.
             // Consider manually updating the prop or making Inertia reload the page/prop.
             // For simplicity, we just show a message. A better way is Inertia::reload() or prop update.
        },
        onError: (errors) => {
             console.error("Error updating application:", errors);
             // Errors shown by InputError
        }
    });
};
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Helper for status styling (optional) - same as user show page
const statusClass = (status) => { /* ... same as Profile/Show ... */ };

</script>

<template>
    <Head :title="'Admin - Manage Student Visa App: ' + application.user?.name" />
     <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Student Visa Application</h2>
        </template>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.student-visa-applications.index')" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
                        Back to Applications List
                    </Link>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 space-y-6">

                        <div v-if="successMessage" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ successMessage }}
                        </div>

                        <div class="flex justify-between items-start">
                             <h3 class="text-lg font-semibold leading-6 text-gray-900">Application Overview</h3>
                             <span class="px-3 py-1 text-xs font-medium rounded-full capitalize" :class="statusClass(application.status)">
                                 {{ application.status.replace('_', ' ') }}
                             </span>
                         </div>

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm border-t border-gray-200 pt-6">
                            <div><span class="font-semibold text-gray-600 block mb-1">Applicant:</span> {{ application.user?.name }} ({{ application.user?.email }})</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Applied On:</span> {{ formatDate(application.created_at) }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Destination:</span> {{ application.destination_country?.name }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">University:</span> {{ application.university?.name ?? 'Not Specified' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Course:</span> {{ application.course?.name ?? 'Not Specified' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">Intake:</span> {{ application.intended_intake_month || 'N/A' }} {{ application.intended_intake_year || '' }}</div>
                             <div><span class="font-semibold text-gray-600 block mb-1">Current Education:</span> {{ application.current_education_level || 'N/A' }}</div>
                            <div><span class="font-semibold text-gray-600 block mb-1">English Test:</span> {{ application.english_proficiency_test || 'N/A' }} ({{ application.english_proficiency_score || 'N/A' }})</div>
                             <div class="md:col-span-2"><span class="font-semibold text-gray-600 block mb-1">Application Reference:</span> {{ application.application_reference ?? 'N/A' }}</div>
                             <div class="md:col-span-2 border-t pt-4 mt-2">
                                 <span class="font-semibold text-gray-600 block mb-1">User Notes:</span>
                                 <p class="text-xs text-gray-700 bg-gray-50 p-2 rounded border border-gray-200 min-h-[50px]">{{ application.user_notes || 'None' }}</p>
                             </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Admin Management</h3>
                             <form @submit.prevent="submitUpdate" class="space-y-4">
                                 <div>
                                    <InputLabel for="status" value="Application Status" class="text-sm font-medium"/>
                                    <select id="status" v-model="form.status" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm capitalize">
                                        <option v-for="s in statuses" :key="s" :value="s">{{ s.replace('_', ' ') }}</option>
                                    </select>
                                    <InputError class="mt-1 text-xs" :message="form.errors.status" />
                                </div>
                                 <div>
                                    <InputLabel for="agency_id" value="Assign Agency (Optional)" class="text-sm font-medium"/>
                                    <select id="agency_id" v-model="form.agency_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- No Agency --</option>
                                        <option v-for="a in agencies" :key="a.id" :value="a.id">{{ a.name }}</option>
                                    </select>
                                    <InputError class="mt-1 text-xs" :message="form.errors.agency_id" />
                                </div>
                                 <div>
                                    <InputLabel for="admin_notes" value="Admin Notes (Internal Use)" class="text-sm font-medium"/>
                                    <TextareaInput id="admin_notes" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" v-model="form.admin_notes" rows="5" placeholder="Add internal notes about progress, required documents, communication, etc."/>
                                    <InputError class="mt-1 text-xs" :message="form.errors.admin_notes" />
                                </div>
                                 <div class="flex justify-end pt-4 border-t border-gray-200 mt-6">
                                     <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update Application</PrimaryButton>
                                </div>
                            </form>
                        </div>
                         </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>