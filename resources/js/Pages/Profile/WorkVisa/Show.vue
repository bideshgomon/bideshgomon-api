<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or your ProfileLayout
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    application: Object, // Passed from controller
    countries: Array,
    jobCategories: Array,
});

const form = useForm({
    _method: 'PUT',
    user_notes: props.application.user_notes || '',
    // Add other fields user might be allowed to update later
});

const submitUpdate = () => {
    form.post(route('api.work-visa-applications.update', props.application.id), {
        preserveScroll: true,
         onSuccess: () => {
             alert('Notes updated successfully!');
        },
        onError: (errors) => {
            console.error("Error updating application:", errors);
            alert('Failed to update notes.');
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
    <Head :title="'Work Visa Application Details - ' + application.destination_country?.name" />

     <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Work Visa Application Details</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                 <div class="mb-4">
                    <Link :href="route('profile.work-visa.index')" class="text-sm text-indigo-600 hover:text-indigo-900">&larr; Back to Applications</Link>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Application Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><span class="font-semibold">Applied On:</span> {{ formatDate(application.created_at) }}</div>
                            <div><span class="font-semibold">Destination:</span> {{ application.destination_country?.name }}</div>
                            <div><span class="font-semibold">Job Category:</span> {{ application.job_category?.name ?? 'N/A' }}</div>
                             <div><span class="font-semibold">Job Posting:</span> {{ application.job_posting?.title ?? 'N/A' }}</div>
                             <div><span class="font-semibold">Agency:</span> {{ application.agency?.name ?? 'Not Assigned' }}</div>
                            <div><span class="font-semibold">Status:</span> <span class="capitalize font-medium" :class="{
                                'text-yellow-600': application.status === 'pending' || application.status === 'processing',
                                'text-green-600': application.status === 'approved',
                                'text-red-600': application.status === 'rejected',
                                'text-blue-600': application.status === 'document_request',
                            }">{{ application.status }}</span></div>
                             <div class="md:col-span-2"><span class="font-semibold">Reference:</span> {{ application.application_reference ?? 'N/A' }}</div>
                        </div>

                        <hr class="my-4">

                        <h3 class="text-lg font-medium leading-6 text-gray-900">Notes</h3>
                         <div>
                            <InputLabel value="Admin/Agency Notes" />
                            <p class="mt-1 text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-200 min-h-[50px]">
                                {{ application.admin_notes || 'No notes from admin yet.' }}
                            </p>
                        </div>
                         <div>
                            <form @submit.prevent="submitUpdate">
                                <InputLabel for="user_notes" value="Your Notes (Editable)" />
                                <TextareaInput id="user_notes" class="mt-1 block w-full" v-model="form.user_notes" rows="4" />
                                <InputError class="mt-2" :message="form.errors.user_notes" />
                                <div class="flex justify-end mt-2">
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
    </AuthenticatedLayout>
</template>