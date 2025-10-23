<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid'; // <-- Icons for toggle

// --- Collapsible Logic ---
const isOpen = ref(false);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// --- SCRIPT LOGIC ---

// Ref to hold the list of user's uploaded documents
const documentList = ref([]);
// Ref to hold the dropdown options (Passport, Resume, etc.)
const documentTypes = ref([]);

// Create a reactive form object
const form = useForm({
    document_type_id: '',
    file: null,
    document_number: '',
    issue_date: '',
    expiry_date: '',
});

// Fetch user's uploaded documents
const getDocuments = () => {
    axios.get(route('api.profile.documents.index'))
        .then(response => {
            documentList.value = response.data;
        })
        .catch(error => console.error("Error fetching documents:", error));
};

// Fetch available document types
const getDocumentTypes = () => {
    axios.get(route('api.document-types.index'))
        .then(response => {
            documentTypes.value = response.data;
        })
        .catch(error => console.error("Error fetching document types:", error));
};

// Handle file selection
const onFileChange = (event) => {
    form.file = event.target.files[0];
};

// Submit a new document
const submitDocument = () => {
    form.post(route('api.profile.documents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            document.getElementById('file_upload').value = null;
            getDocuments();
        },
        onError: () => {
            console.error("Error submitting file:", form.errors);
        },
    });
};

// Delete document
const deleteDocument = (documentId) => {
    if (!confirm("Are you sure you want to delete this document?")) return;

    axios.delete(route('api.profile.documents.destroy', { document: documentId }))
        .then(() => getDocuments())
        .catch(error => {
            console.error("Error deleting document:", error);
            alert("Failed to delete document.");
        });
};

// On mount, load data
onMounted(() => {
    getDocuments();
    getDocumentTypes();
});
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <!-- Collapsible Header -->
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Documents</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Upload and manage your personal documents (e.g., Passport, IELTS, CV).
                    </p>
                </div>
                <button>
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <!-- Collapsible Content -->
            <div v-show="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="space-y-4">
                    <div v-if="documentList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                        No documents found. Upload one using the form below.
                    </div>

                    <div 
                        v-for="doc in documentList" 
                        :key="doc.id" 
                        class="p-4 border rounded-lg dark:border-gray-700 flex justify-between items-center"
                    >
                        <div>
                            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                {{ doc.document_type.name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ doc.file_name }}
                            </p>
                            <p v-if="doc.document_number" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Number: {{ doc.document_number }}
                            </p>
                            <p v-if="doc.expiry_date" class="text-sm text-gray-500 dark:text-gray-400">
                                Expires: {{ doc.expiry_date }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 ml-4 flex gap-2">
                            <a :href="doc.url" target="_blank" class="btn btn-secondary btn-sm">
                                View
                            </a>
                            <DangerButton @click="deleteDocument(doc.id)" class="btn-sm">
                                Delete
                            </DangerButton>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form @submit.prevent="submitDocument" class="mt-6 space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Upload New Document</h3>

                    <div>
                        <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Document Type
                        </label>
                        <select 
                            id="document_type_id" 
                            v-model="form.document_type_id"
                            class="mt-1 block w-full"
                        >
                            <option value="" disabled>Select a document type</option>
                            <option v-for="docType in documentTypes" :key="docType.id" :value="docType.id">
                                {{ docType.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.document_type_id" class="text-sm text-red-600 mt-1">{{ form.errors.document_type_id }}</p>
                    </div>

                    <div>
                        <label for="file_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File</label>
                        <input 
                            type="file" 
                            id="file_upload" 
                            @input="onFileChange"
                            class="mt-1 block w-full file-input" 
                        />
                        <p v-if="form.errors.file" class="text-sm text-red-600 mt-1">{{ form.errors.file }}</p>
                    </div>

                    <div>
                        <label for="document_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Document Number (Optional)
                        </label>
                        <input 
                            type="text" 
                            id="document_number"
                            v-model="form.document_number" 
                            class="mt-1 block w-full" 
                            placeholder="e.g., Passport or NID number" 
                        />
                        <p v-if="form.errors.document_number" class="text-sm text-red-600 mt-1">{{ form.errors.document_number }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date (Optional)</label>
                            <input 
                                type="date" 
                                id="issue_date" 
                                v-model="form.issue_date"
                                class="mt-1 block w-full" 
                            />
                            <p v-if="form.errors.issue_date" class="text-sm text-red-600 mt-1">{{ form.errors.issue_date }}</p>
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date (Optional)</label>
                            <input 
                                type="date" 
                                id="expiry_date" 
                                v-model="form.expiry_date"
                                class="mt-1 block w-full"
                            />
                            <p v-if="form.errors.expiry_date" class="text-sm text-red-600 mt-1">{{ form.errors.expiry_date }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Uploading...' : 'Upload Document' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* File input */
.file-input {
    @apply block w-full text-sm text-gray-900 dark:text-gray-300
           border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer
           bg-gray-50 dark:bg-gray-700 focus:outline-none
           file:mr-4 file:py-2 file:px-4
           file:rounded-l-lg file:border-0
           file:text-sm file:font-semibold
           file:bg-gray-200 dark:file:bg-gray-600
           file:text-gray-700 dark:file:text-gray-200
           hover:file:bg-gray-300 dark:hover:file:bg-gray-500;
}

.btn-sm {
  @apply px-3 py-1 text-xs;
}
.btn-secondary {
    @apply inline-flex items-center justify-center border border-transparent rounded-lg font-semibold tracking-widest transition ease-in-out duration-150
           bg-brand-secondary text-white hover:bg-opacity-90;
}
</style>
