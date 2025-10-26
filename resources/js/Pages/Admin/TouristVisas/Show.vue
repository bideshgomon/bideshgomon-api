
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; // Removed unused 'router'
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from '@/Components/TextareaInput.vue'; // Assuming you have this
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/solid'; // Icon for back link

const props = defineProps({
    visaApplication: Object, // Passed from TouristVisaPageController@show
});

// Form for updating the main application status and notes
const form = useForm({
    status: props.visaApplication.status,
    admin_notes: props.visaApplication.admin_notes || '',
});

// Form for updating individual document status
const docForm = useForm({
    status: '',
    admin_notes: '',
});

const selectedDocument = ref(null);
const showUpdateModal = ref(false);

// Status options
const applicationStatusOptions = ['pending', 'submitted', 'processing', 'approved', 'rejected', 'cancelled'];
const documentStatusOptions = ['pending', 'submitted', 'verified', 'rejected'];

// Update the main application status/notes
const updateApplication = () => {
    form.patch(route('admin.api.tourist-visas.update', props.visaApplication.id), {
        preserveScroll: true,
        // onSuccess/onError handled by AdminLayout flash messages
    });
};

// Open modal to update a specific document
const openUpdateDocumentModal = (document) => {
    selectedDocument.value = document;
    docForm.status = document.status;
    docForm.admin_notes = document.admin_notes || '';
    docForm.errors = {}; // Clear previous errors
    showUpdateModal.value = true;
};

// Close modal
const closeModal = () => {
    showUpdateModal.value = false;
    selectedDocument.value = null;
    docForm.reset();
};

// Submit document status update
const updateDocument = () => {
    if (!selectedDocument.value) return;

    docForm.patch(route('admin.api.tourist-visa-documents.update', selectedDocument.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Data reloads via Inertia response
        },
        onError: () => {
            // Keep modal open to show errors
        }
    });
};

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    // Use a slightly more concise format
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    try {
        return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) { return dateString; }
};

// Helper for document link - CHECK IF THIS ROUTE EXISTS AND IS CORRECT
const getDocumentUrl = (doc) => {
    if (doc.user_document?.file_path) {
        // Option 1: Direct storage link (if files are public)
        return `/storage/${doc.user_document.file_path}`;

        // Option 2: Dedicated route for secure download (if files are private)
        // return route('documents.download', { documentId: doc.user_document.id }); // Example route name
    }
    return null;
}

// Helper for status badge class (shared with Index page logic)
const statusClass = (status) => {
    switch (status) {
        case 'pending':
        case 'submitted':
        case 'processing':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'verified': // Document specific
        case 'approved': // Application specific
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'rejected':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'cancelled': // Application specific
            return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

</script>

<template>
    <Head :title="'Tourist Visa App: ' + visaApplication.user.name" />

    <AdminLayout>
        <div class="mb-4">
             <Link :href="route('admin.tourist-visas.index')" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                 <ArrowLeftIcon class="h-4 w-4 mr-1.5"/> Back to Applications List
            </Link>
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
            <section>
                 <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Application Details
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Viewing application for {{ visaApplication.user.name }} to {{ visaApplication.destination_country.name }}.
                    </p>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                     <div class="space-y-1"><span class="font-medium text-gray-500 block">Applicant:</span> <span class="text-gray-900 dark:text-gray-100">{{ visaApplication.user.name }} ({{ visaApplication.user.email }})</span></div>
                    <div class="space-y-1"><span class="font-medium text-gray-500 block">Destination:</span> <span class="text-gray-900 dark:text-gray-100">{{ visaApplication.destination_country.name }}</span></div>
                    <div class="space-y-1"><span class="font-medium text-gray-500 block">Intended Travel Date:</span> <span class="text-gray-900 dark:text-gray-100">{{ formatDate(visaApplication.intended_travel_date) }}</span></div>
                    <div class="space-y-1"><span class="font-medium text-gray-500 block">Planned Duration:</span> <span class="text-gray-900 dark:text-gray-100">{{ visaApplication.duration_days ? visaApplication.duration_days + ' days' : 'N/A' }}</span></div>
                    <div class="space-y-1"><span class="font-medium text-gray-500 block">Application ID:</span> <span class="text-gray-900 dark:text-gray-100">{{ visaApplication.id }}</span></div>
                    <div class="space-y-1"><span class="font-medium text-gray-500 block">Reference:</span> <span class="text-gray-900 dark:text-gray-100">{{ visaApplication.application_reference || 'N/A' }}</span></div>
                     <div class="space-y-1"><span class="font-medium text-gray-500 block">Applied On:</span> <span class="text-gray-900 dark:text-gray-100">{{ formatDate(visaApplication.created_at) }}</span></div>
                     <div class="space-y-1"><span class="font-medium text-gray-500 block">Current Status:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize" :class="statusClass(visaApplication.status)">
                            {{ visaApplication.status?.replace('_', ' ') || 'Unknown' }}
                        </span>
                     </div>
                </div>

                <form @submit.prevent="updateApplication" class="mt-6 border-t dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Update Application Status / Notes</h3>
                    <div class="space-y-4">
                        <div class="w-full sm:w-1/2"> {/* PATCH: Use form-group like structure */}
                            <InputLabel for="status" value="Application Status" class="font-semibold"/>
                            <select id="status" v-model="form.status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                                <option v-for="s in applicationStatusOptions" :key="s" :value="s" class="capitalize">{{ s.replace('_', ' ') }}</option>
                            </select>
                            <InputError :message="form.errors.status" class="mt-2" />
                        </div>
                        <div> {/* PATCH: Use form-group like structure */}
                            <InputLabel for="admin_notes" value="Admin Notes" class="font-semibold"/>
                            <TextareaInput
                                id="admin_notes"
                                v-model="form.admin_notes"
                                class="mt-1 block w-full text-sm"
                                rows="3"
                                placeholder="Add internal notes about the application..."
                            />
                             <InputError :message="form.errors.admin_notes" class="mt-2" />
                        </div>
                    </div>
                     <div class="mt-4 flex justify-end">
                        <PrimaryButton :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </PrimaryButton>
                    </div>
                </form>
            </section>
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                 <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Required Documents
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Manage the status of documents for this application.
                    </p>
                </header>

                <div class="space-y-4">
                    <div v-if="!visaApplication.documents || visaApplication.documents.length === 0" class="text-gray-500 dark:text-gray-400 italic text-center py-4">
                        No documents are currently associated with this application type.
                    </div>
                    <div v-else v-for="doc in visaApplication.documents" :key="doc.id" class="border dark:border-gray-700 rounded-md p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ doc.document_type.name }}</p>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                Status:
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize" :class="statusClass(doc.status)">
                                    {{ doc.status?.replace('_', ' ') || 'Unknown' }}
                                </span>
                            </div>
                            <a v-if="getDocumentUrl(doc)" :href="getDocumentUrl(doc)" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-1 inline-block break-all">
                                View File: {{ doc.user_document?.file_name || 'Link' }}
                            </a>
                             <p v-else class="text-sm text-gray-400 dark:text-gray-500 mt-1 italic">No file submitted yet.</p>
                             <p v-if="doc.admin_notes" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Notes: {{ doc.admin_notes }}</p>
                        </div>
                        <div class="flex-shrink-0 mt-2 sm:mt-0">
                             <SecondaryButton @click="openUpdateDocumentModal(doc)">Update Status</SecondaryButton>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <Modal :show="showUpdateModal" @close="closeModal">
             <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Update Document: {{ selectedDocument?.document_type?.name }}
                </h2>

                <form @submit.prevent="updateDocument" class="mt-6 space-y-4">
                     <div>
                        <InputLabel for="doc_status" value="Document Status" />
                        <select id="doc_status" v-model="docForm.status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                            <option v-for="s in documentStatusOptions" :key="s" :value="s" class="capitalize">{{ s.replace('_', ' ') }}</option>
                        </select>
                        <InputError :message="docForm.errors.status" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="doc_admin_notes" value="Admin Notes (Optional)" />
                        <TextareaInput
                            id="doc_admin_notes"
                            v-model="docForm.admin_notes"
                            class="mt-1 block w-full text-sm"
                            rows="3"
                            placeholder="Add notes specific to this document..."
                        />
                         <InputError :message="docForm.errors.admin_notes" class="mt-2" />
                    </div>

                     <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" @click="closeModal"> Cancel </SecondaryButton>
                        <PrimaryButton :disabled="docForm.processing" :class="{ 'opacity-25': docForm.processing }">
                            {{ docForm.processing ? 'Saving...' : 'Save Status' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AdminLayout>
</template>