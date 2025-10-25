<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid';

// --- Collapsible Logic ---
const isOpen = ref(false); // Drawer starts closed
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// --- SCRIPT LOGIC ---

// Ref to hold the list of user's uploaded documents
const documentList = ref([]);
// Ref to hold the dropdown options (Passport, Resume, etc.)
const documentTypes = ref([]);
// Ref for the file input element
const fileInputRef = ref(null); // ✅ [FIX] Added ref for file input

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
    // ✅ [FIX] Correct route name
    axios.get(route('profile.documents.index'))
        .then(response => {
            documentList.value = response.data;
        })
        .catch(error => console.error("Error fetching documents:", error));
};

// Fetch available document types
const getDocumentTypes = () => {
    // ✅ This route name was correct
    axios.get(route('api.document-types.index'))
        .then(response => {
            documentTypes.value = response.data;
             // Set default dropdown value if types are loaded and form is empty
            if (documentTypes.value.length > 0 && !form.document_type_id) {
                form.document_type_id = documentTypes.value[0].id;
            }
        })
        .catch(error => console.error("Error fetching document types:", error));
};

// Handle file selection
const onFileChange = (event) => {
    form.file = event.target.files[0];
};

// Submit a new document
const submitDocument = () => {
    // ✅ [FIX] Correct route name
    form.post(route('profile.documents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // ✅ [FIX] Reset file input using ref
            if (fileInputRef.value) {
                fileInputRef.value.value = null;
            }
            getDocuments(); // Refresh the list
        },
        onError: () => {
            console.error("Error submitting file:", form.errors);
        },
    });
};

// Delete document
const deleteDocument = (documentId) => {
    if (!confirm("Are you sure you want to delete this document?")) return;

    // ✅ [FIX] Correct route name and parameter passing
    axios.delete(route('profile.documents.destroy', documentId ))
        .then(() => getDocuments()) // Refresh the list on success
        .catch(error => {
            console.error("Error deleting document:", error);
            alert("Failed to delete document. Please try again.");
        });
};

// On mount, load initial data
onMounted(() => {
    getDocuments();
    getDocumentTypes();
});
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Documents</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Upload and manage your personal documents (e.g., Passport, IELTS, CV).
                    </p>
                </div>
                <button class="focus:outline-none">
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-if="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="space-y-4">
                        <div v-if="documentList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                            No documents found. Upload one using the form below.
                        </div>

                        <div
                            v-for="doc in documentList"
                            :key="doc.id"
                            class="p-4 border rounded-lg dark:border-gray-700 flex justify-between items-center flex-wrap gap-4"
                        >
                            <div>
                                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                    {{ doc.document_type.name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ doc.original_filename }}
                                </p>
                                <p v-if="doc.document_number" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Number: {{ doc.document_number }}
                                </p>
                                <p v-if="doc.expiry_date" class="text-sm text-gray-500 dark:text-gray-400">
                                    Expires: {{ doc.expiry_date }} </p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <a :href="doc.file_path" target="_blank" class="btn btn-secondary btn-sm">
                                    View
                                </a>
                                <DangerButton @click="deleteDocument(doc.id)" class="btn-sm">
                                    Delete
                                </DangerButton>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitDocument" class="mt-6 space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Upload New Document</h3>

                        <div>
                            <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Document Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="document_type_id"
                                v-model="form.document_type_id"
                                required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            >
                                <option value="" disabled>Select a document type</option>
                                <option v-for="docType in documentTypes" :key="docType.id" :value="docType.id">
                                    {{ docType.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.document_type_id" class="text-sm text-red-600 mt-1">{{ form.errors.document_type_id }}</p>
                        </div>

                        <div>
                            <label for="file_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                File <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                id="file_upload"
                                ref="fileInputRef" @change="onFileChange" required
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
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                />
                                <p v-if="form.errors.issue_date" class="text-sm text-red-600 mt-1">{{ form.errors.issue_date }}</p>
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date (Optional)</label>
                                <input
                                    type="date"
                                    id="expiry_date"
                                    v-model="form.expiry_date"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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
            </transition>
        </section>
    </div>
</template>

<style scoped>
/* Basic styles for standard form inputs (if not globally defined) */
select.mt-1, input[type="text"].mt-1, input[type="date"].mt-1 {
    @apply border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm;
}

/* Custom file input styling */
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

/* Small button utility */
.btn-sm {
    @apply px-3 py-1 text-xs;
}

/* Secondary button styling (adjust colors as needed) */
.btn-secondary {
    @apply inline-flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-lg font-semibold tracking-widest transition ease-in-out duration-150
           bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
    /* Example using a 'secondary' color if defined in tailwind.config.js */
    /* bg-brand-secondary text-white hover:bg-opacity-90; */
}
</style>