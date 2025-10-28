<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

// Import components
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
// REMOVED: import FileInput from '@/Components/FileInput.vue'; // <-- REMOVED IMPORT

// ... (rest of the script setup remains the same) ...

const handleFileChange = (event) => {
    uploadForm.document_file = event.target.files[0];
     uploadError.value = ''; // Clear error when a new file is selected
};

onMounted(() => {
    fetchData();
});
</script>

<template>
    <section class="space-y-6">
       {/* ... (header, loading, list remain the same) ... */}
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Documents</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Upload and manage your important documents (Passport, CV, Certificates, etc.).
            </p>
        </header>
         <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading documents...</div>
        <div v-else-if="generalError" class="text-sm text-red-600 dark:text-red-400">{{ generalError }}</div>
        <div v-else>
            <div v-if="documentList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                No documents uploaded yet.
            </div>
            <ul v-else class="space-y-4">
                <li v-for="doc in documentList" :key="doc.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-center">
                    <div>
                        <a :href="doc.file_url" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-primary hover:underline dark:text-blue-400">
                            {{ doc.file_name }}
                        </a>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ doc.document_type?.name }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <DangerButton @click="deleteDocument(doc.id, doc.file_name)">Delete</DangerButton>
                    </div>
                </li>
            </ul>
        </div>


        <form @submit.prevent="submitUpload" class="mt-6 border-t dark:border-gray-700 pt-6 space-y-4 max-w-xl">
             <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Upload New Document</h3>

             <div>
                <InputLabel for="document_type_id" value="Document Type" />
                <SelectInput
                    id="document_type_id"
                    class="mt-1 block w-full"
                    v-model="uploadForm.document_type_id"
                    :options="documentTypes"
                    option-value="id"
                    option-label="name"
                    required
                />
                <InputError class="mt-2" :message="uploadForm.errors.document_type_id" />
            </div>

            <div>
                <InputLabel for="document_file" value="Select File" />
                 <input
                    id="document_file"
                    type="file"
                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    @change="handleFileChange"
                    required
                />
                 <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="file_input_help">PDF, DOCX, JPG, PNG (MAX. 5MB).</p>
                 <InputError class="mt-2" :message="uploadError || uploadForm.errors.document_file" />
            </div>

             {/* ... (progress bar, button remain the same) ... */}
            <div v-if="isUploading">
                 <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: uploadProgress + '%' }"></div>
                 </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ uploadProgress }}% Uploaded</p>
            </div>
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="isUploading || !uploadForm.document_file || !uploadForm.document_type_id">
                    {{ isUploading ? 'Uploading...' : 'Upload Document' }}
                </PrimaryButton>
            </div>
        </form>

    </section>
</template>