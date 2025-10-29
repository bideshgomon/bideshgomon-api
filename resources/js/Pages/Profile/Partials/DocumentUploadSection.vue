<script setup>
import { ref, onMounted } from 'vue'; // <-- Import 'onMounted'
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { TrashIcon, ArrowDownTrayIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

// --- THIS IS THE FIX ---
// Define the missing reactive variables
const documentList = ref([]);
const documentTypes = ref([]);
const isLoading = ref(false);
const generalError = ref(null);
// -----------------------

const form = ref({
    document_type_id: '',
    file: null,
    file_name: '',
});
const formErrors = ref({});
const fileInput = ref(null); // ref for the file input element

// Fetch initial data when the component is mounted
onMounted(async () => {
    await fetchDocumentTypes();
    await fetchDocuments();
});

const fetchDocumentTypes = async () => {
    try {
        const response = await axios.get('/api/document-types');
        documentTypes.value = response.data;
    } catch (error) {
        console.error('Error fetching document types:', error);
        generalError.value = 'Could not load document types.';
    }
};

const fetchDocuments = async () => {
    isLoading.value = true;
    generalError.value = null;
    try {
        const response = await axios.get('/api/profile/documents');
        documentList.value = response.data;
    } catch (error) {
        console.error('Error fetching documents:', error);
        generalError.value = 'A network error occurred while fetching documents.';
    } finally {
        isLoading.value = false;
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.file = file;
        form.value.file_name = file.name;
    }
};

// Triggers the hidden file input
const triggerFileInput = () => {
    fileInput.value.click();
};

const uploadDocument = async () => {
    if (!form.value.file) {
        formErrors.value = { file: ['Please select a file to upload.'] };
        return;
    }
    if (!form.value.document_type_id) {
        formErrors.value = { document_type_id: ['Please select a document type.'] };
        return;
    }

    isLoading.value = true;
    generalError.value = null;
    formErrors.value = {};

    const formData = new FormData();
    formData.append('document_type_id', form.value.document_type_id);
    formData.append('file', form.value.file);
    formData.append('file_name', form.value.file_name);

    try {
        await axios.post('/api/profile/documents', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        // Reset form and refresh list
        form.value.document_type_id = '';
        form.value.file = null;
        form.value.file_name = '';
        if (fileInput.value) {
            fileInput.value.value = ''; // Clear the file input
        }
        await fetchDocuments();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            formErrors.value = error.response.data.errors;
        } else {
            console.error('Error uploading document:', error);
            generalError.value = 'An unexpected error occurred. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

const deleteDocument = async (documentId) => {
    if (!confirm('Are you sure you want to delete this document?')) {
        return;
    }

    isLoading.value = true;
    generalError.value = null;
    try {
        await axios.delete(`/api/profile/documents/${documentId}`);
        await fetchDocuments(); // Refresh the list
    } catch (error) {
        console.error('Error deleting document:', error);
        generalError.value = 'Failed to delete the document. Please try again.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Documents</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Upload and manage your documents for applications (e.g., Passport, Transcript, CV).
            </p>
        </header>

        <div v-if="generalError" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ generalError }}
        </div>

        <form @submit.prevent="uploadDocument" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="document_type" value="Document Type" />
                    <select
                        id="document_type"
                        v-model="form.document_type_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-primary dark:focus:border-brand-primary focus:ring-brand-primary dark:focus:ring-brand-primary rounded-md shadow-sm"
                        :disabled="isLoading"
                    >
                        <option value="" disabled>Select a type...</option>
                        <option v-for="docType in documentTypes" :key="docType.id" :value="docType.id">
                            {{ docType.name }}
                        </option>
                    </select>
                    <InputError :message="formErrors.document_type_id ? formErrors.document_type_id[0] : ''" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="file_upload" value="File" />
                    <input
                        type="file"
                        ref="fileInput"
                        @change="handleFileChange"
                        class="hidden"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                    />
                    <button
                        type="button"
                        @click="triggerFileInput"
                        :disabled="isLoading"
                        class="mt-1 w-full text-left px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <span v-if="!form.file_name">Choose file...</span>
                        <span v-else class="flex items-center">
                            <CheckCircleIcon class="w-5 h-5 text-green-500 mr-2" />
                            {{ form.file_name }}
                        </span>
                    </button>
                    <InputError :message="formErrors.file ? formErrors.file[0] : ''" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="isLoading">
                    <span v-if="isLoading">Uploading...</span>
                    <span v-else>Upload Document</span>
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-10">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Uploaded Documents</h3>
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700">
                <ul v-if="documentList.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li v-for="doc in documentList" :key="doc.id" class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ doc.file_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Type: {{ doc.document_type.name }} | Uploaded: {{ new Date(doc.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <a :href="doc.file_url" target="_blank" class="text-brand-primary hover:text-brand-dark p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700" title="Download">
                                <ArrowDownTrayIcon class="w-5 h-5" />
                            </a>
                            <button @click="deleteDocument(doc.id)" :disabled="isLoading" class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700" title="Delete">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </li>
                </ul>
                <p v-else-if="!isLoading" class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    You have not uploaded any documents yet.
                </p>
                <p v-if="isLoading && documentList.length === 0" class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    Loading documents...
                </p>
            </div>
        </div>
    </section>
</template>