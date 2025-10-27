<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    application: Object, // Passed from controller
    // Pass other data if needed for display (users, countries)
    agencies: Array,
    statuses: Array,
});

const form = useForm({
    _method: 'PUT',
    status: props.application.status,
    agency_id: props.application.agency_id || '', // Handle null agency_id
    admin_notes: props.application.admin_notes || '',
});

const submitUpdate = () => {
    // ** IMPORTANT: Need an Admin API endpoint for updating applications **
    // Assuming '/api/admin/work-visa-applications/{id}' exists and accepts status, agency_id, admin_notes
    form.post(route('api.admin.work-visa-applications.update', props.application.id), {
        preserveScroll: true,
         onSuccess: () => {
             alert('Application updated successfully!');
             // Might need to reload the application data if Inertia doesn't auto-refresh prop
        },
        onError: (errors) => {
            console.error("Error updating application:", errors);
            alert('Failed to update application.');
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

</script>

<template>
    <Head :title="'Admin - Manage Application: ' + application.user?.name" />

     <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Work Visa Application</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link :href="route('admin.work-visa-applications.index')" class="text-sm text-indigo-600 hover:text-indigo-900">&larr; Back to Applications</Link>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Application Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><span class="font-semibold">Applicant:</span> {{ application.user?.name }} ({{ application.user?.email }})</div>
                            <div><span class="font-semibold">Applied On:</span> {{ formatDate(application.created_at) }}</div>
                            <div><span class="font-semibold">Destination:</span> {{ application.destination_country?.name }}</div>
                            <div><span class="font-semibold">Job Category:</span> {{ application.job_category?.name ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Job Posting:</span> {{ application.job_posting?.title ?? 'N/A' }}</div>
                             <div class="md:col-span-2"><span class="font-semibold">Reference:</span> {{ application.application_reference ?? 'N/A' }}</div>
                             <div class="md:col-span-2"><span class="font-semibold">User Notes:</span> <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded border border-gray-200 mt-1 min-h-[50px]">{{ application.user_notes || 'None' }}</p></div>
                        </div>

                        <hr class="my-4">

                        <h3 class="text-lg font-medium leading-6 text-gray-900">Admin Management</h3>
                         <form @submit.prevent="submitUpdate" class="space-y-4">
                             <div>
                                <InputLabel for="status" value="Application Status" />
                                <select id="status" v-model="form.status" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm capitalize">
                                    <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                             <div>
                                <InputLabel for="agency_id" value="Assigned Agency (Optional)" />
                                <select id="agency_id" v-model="form.agency_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">None</option>
                                    <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                        {{ agency.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.agency_id" />
                            </div>

                             <div>
                                <InputLabel for="admin_notes" value="Admin Notes (Internal)" />
                                <TextareaInput id="admin_notes" class="mt-1 block w-full" v-model="form.admin_notes" rows="4" />
                                <InputError class="mt-2" :message="form.errors.admin_notes" />
                            </div>

                             <div class="flex justify-end mt-4">
                                 <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Application
                                </PrimaryButton>
                            </div>
                        </form>

                         </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>