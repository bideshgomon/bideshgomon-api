<template>
    <AuthenticatedLayout>
        <Head title="Agency Verification" />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Agency Verification</h1>
                <p class="mt-2 text-gray-600">Upload required documents to get your agency verified and build trust with clients.</p>
            </div>

            <!-- Current Status Card -->
            <div v-if="latestRequest" class="mb-8 bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Verification Status</h2>
                    <span
                        :class="[
                            'px-4 py-2 rounded-full text-sm font-medium',
                            latestRequest.status === 'approved' ? 'bg-green-100 text-green-800' :
                            latestRequest.status === 'rejected' ? 'bg-red-100 text-red-800' :
                            latestRequest.status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                            latestRequest.status === 'requires_changes' ? 'bg-orange-100 text-orange-800' :
                            'bg-yellow-100 text-yellow-800'
                        ]"
                    >
                        {{ (latestRequest?.status || '').replace('_', ' ').toUpperCase() }}
                    </span>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Submitted:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900">{{ formatDate(latestRequest.submitted_at) }}</span>
                    </div>

                    <div v-if="latestRequest.reviewed_at">
                        <span class="text-sm text-gray-500">Reviewed:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900">{{ formatDate(latestRequest.reviewed_at) }}</span>
                    </div>

                    <div v-if="latestRequest.reviewer">
                        <span class="text-sm text-gray-500">Reviewed by:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900">{{ latestRequest.reviewer.name }}</span>
                    </div>

                    <div v-if="latestRequest.admin_notes" class="mt-4 p-4 bg-blue-50 rounded-md">
                        <p class="text-sm font-medium text-blue-900">Admin Notes:</p>
                        <p class="mt-1 text-sm text-blue-700">{{ latestRequest.admin_notes }}</p>
                    </div>

                    <div v-if="latestRequest.rejection_reason" class="mt-4 p-4 bg-red-50 rounded-md">
                        <p class="text-sm font-medium text-red-900">Rejection Reason:</p>
                        <p class="mt-1 text-sm text-red-700">{{ latestRequest.rejection_reason }}</p>
                    </div>
                </div>
            </div>

            <!-- No Status Card -->
            <div v-else class="mb-8 bg-blue-600 rounded-lg shadow-md p-6 text-white border-2 border-blue-700">
                <h2 class="text-2xl font-bold mb-2">Get Verified Today!</h2>
                <p class="mb-4">Verified agencies receive more applications and build stronger trust with clients.</p>
                <ul class="space-y-2">
                    <li class="flex items-center">
                        <CheckIcon class="h-5 w-5 mr-2" />
                        <span>Verified badge on your profile</span>
                    </li>
                    <li class="flex items-center">
                        <CheckIcon class="h-5 w-5 mr-2" />
                        <span>Higher ranking in search results</span>
                    </li>
                    <li class="flex items-center">
                        <CheckIcon class="h-5 w-5 mr-2" />
                        <span>Increased client trust</span>
                    </li>
                </ul>
            </div>

            <!-- Required Documents -->
            <div class="mb-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Required Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="(label, type) in requiredDocumentTypes" :key="type" class="flex items-start p-4 border rounded-lg">
                        <DocumentIcon class="h-6 w-6 text-gray-400 mt-1 mr-3" />
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ label }}</p>
                            <p v-if="getDocumentByType(type)" class="text-sm text-green-600 mt-1">✓ Uploaded</p>
                            <p v-else class="text-sm text-gray-500 mt-1">Not uploaded</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Document Section -->
            <div v-if="!latestRequest || latestRequest.status === 'requires_changes' || latestRequest.status === 'rejected'" class="mb-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Document</h2>
                <form @submit.prevent="uploadDocument" class="space-y-4">
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                        <select
                            id="document_type"
                            v-model="uploadForm.document_type"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Select document type</option>
                            <option v-for="(label, type) in requiredDocumentTypes" :key="type" :value="type">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="document_name" class="block text-sm font-medium text-gray-700">Document Name (Optional)</label>
                        <input
                            type="text"
                            id="document_name"
                            v-model="uploadForm.document_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g., Trade License 2024"
                        />
                    </div>

                    <div>
                        <label for="document" class="block text-sm font-medium text-gray-700">Document File</label>
                        <input
                            type="file"
                            id="document"
                            @change="handleFileChange"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG (Max 10MB)</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="uploading"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        <span v-if="uploading">Uploading...</span>
                        <span v-else>Upload Document</span>
                    </button>
                </form>
            </div>

            <!-- Uploaded Documents -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Uploaded Documents</h2>
                <div v-if="documents.length > 0" class="space-y-3">
                    <div v-for="doc in documents" :key="doc.id" class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center flex-1">
                            <DocumentIcon class="h-6 w-6 text-gray-400 mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ doc.document_name }}</p>
                                <p class="text-sm text-gray-500">{{ (doc?.document_type || '').replace('_', ' ') }}</p>
                                <div class="flex items-center mt-1 space-x-4">
                                    <span class="text-xs text-gray-400">{{ doc.file_size ? formatFileSize(doc.file_size) : 'Unknown' }}</span>
                                    <span
                                        :class="[
                                            'text-xs px-2 py-1 rounded',
                                            doc.status === 'approved' ? 'bg-green-100 text-green-800' :
                                            doc.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                            'bg-yellow-100 text-yellow-800'
                                        ]"
                                    >
                                        {{ doc.status }}
                                    </span>
                                </div>
                                <p v-if="doc.rejection_reason" class="text-sm text-red-600 mt-2">{{ doc.rejection_reason }}</p>
                            </div>
                        </div>
                        <button
                            v-if="doc.status !== 'approved'"
                            @click="deleteDocument(doc.id)"
                            class="ml-4 text-red-600 hover:text-red-800"
                        >
                            <TrashIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    <DocumentIcon class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                    <p>No documents uploaded yet</p>
                </div>
            </div>

            <!-- Submit Verification Request -->
            <div v-if="canSubmitRequest" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Submit for Verification</h2>
                <form @submit.prevent="submitRequest" class="space-y-4">
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
                        <textarea
                            id="message"
                            v-model="submitForm.message"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Any additional information for the review team..."
                        ></textarea>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-md">
                        <p class="text-sm text-blue-900">
                            <strong>Note:</strong> Once submitted, our team will review your documents within 2-3 business days.
                            You'll be notified via email about the verification status.
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    >
                        <span v-if="submitting">Submitting...</span>
                        <span v-else>Submit for Verification</span>
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckIcon, DocumentIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    agency: Object,
    latestRequest: Object,
    documents: Array,
    requiredDocumentTypes: Object,
});

const uploadForm = ref({
    document_type: '',
    document_name: '',
    document: null,
});

const submitForm = ref({
    message: '',
    document_ids: [],
});

const uploading = ref(false);
const submitting = ref(false);

const canSubmitRequest = computed(() => {
    if (props.latestRequest && (props.latestRequest.status === 'pending' || props.latestRequest.status === 'under_review')) {
        return false;
    }
    return props.documents.filter(d => d.status === 'pending').length > 0;
});

const handleFileChange = (e) => {
    uploadForm.value.document = e.target.files[0];
};

const uploadDocument = () => {
    uploading.value = true;
    const formData = new FormData();
    formData.append('document', uploadForm.value.document);
    formData.append('document_type', uploadForm.value.document_type);
    if (uploadForm.value.document_name) {
        formData.append('document_name', uploadForm.value.document_name);
    }

    router.post(route('agency.verification.upload-document'), formData, {
        onFinish: () => {
            uploading.value = false;
            uploadForm.value = { document_type: '', document_name: '', document: null };
            document.getElementById('document').value = '';
        },
    });
};

const deleteDocument = (id) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('agency.verification.delete-document', id));
    }
};

const submitRequest = () => {
    submitting.value = true;
    const documentIds = props.documents.filter(d => d.status === 'pending').map(d => d.id);
    
    router.post(route('agency.verification.submit'), {
        message: submitForm.value.message,
        document_ids: documentIds,
    }, {
        onFinish: () => {
            submitting.value = false;
            submitForm.value.message = '';
        },
    });
};

const getDocumentByType = (type) => {
    return props.documents.find(d => d.document_type === type);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};
</script>
