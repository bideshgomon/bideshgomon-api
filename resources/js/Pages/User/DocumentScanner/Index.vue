<template>
    <Head title="Document Scanner - AI-Powered OCR" />

    <AuthenticatedLayout>
        <!-- Hero Section -->
        <div class="bg-heritage-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex items-center gap-4 mb-4">
                    <DocumentMagnifyingGlassIcon class="h-12 w-12" />
                    <div>
                        <h1 class="text-4xl font-bold">Document Scanner</h1>
                        <p class="text-xl text-indigo-100">AI-powered document scanning & data extraction</p>
                    </div>
                </div>
                <p class="text-lg max-w-3xl">
                    Upload your passport, ID, or visa documents and let our AI extract the information automatically.
                    Save time and reduce errors in your applications.
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Upload Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Upload New Document</h2>
                
                <form @submit.prevent="uploadDocument" class="space-y-6">
                    <!-- Document Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.document_type"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Select document type...</option>
                            <option value="passport">Passport</option>
                            <option value="national_id">National ID</option>
                            <option value="visa">Visa</option>
                            <option value="driving_license">Driving License</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document Image <span class="text-red-500">*</span>
                        </label>
                        <div
                            @dragover.prevent="isDragging = true"
                            @dragleave="isDragging = false"
                            @drop.prevent="handleDrop"
                            :class="[
                                'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors',
                                isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'
                            ]"
                            @click="$refs.fileInput.click()"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                @change="handleFileSelect"
                                accept="image/jpeg,image/png,image/jpg"
                                class="hidden"
                            />

                            <div v-if="!previewUrl">
                                <CloudArrowUpIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                <p class="text-gray-600 mb-2">
                                    <span class="font-semibold text-indigo-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-sm text-gray-500">PNG, JPG up to 10MB</p>
                            </div>

                            <div v-else class="space-y-4">
                                <img :src="previewUrl" alt="Preview" class="max-h-64 mx-auto rounded" />
                                <button
                                    type="button"
                                    @click.stop="clearFile"
                                    class="text-sm text-red-600 hover:text-red-700"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex gap-3">
                            <InformationCircleIcon class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-blue-900">
                                <p class="font-semibold mb-2">Tips for best results:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Ensure good lighting and avoid shadows</li>
                                    <li>Capture the entire document in the frame</li>
                                    <li>Keep the document flat and avoid glare</li>
                                    <li>Use a high-resolution image</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing || !form.document_type || !form.document_image"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
                    >
                        <SparklesIcon class="h-5 w-5" />
                        <span v-if="form.processing">Uploading...</span>
                        <span v-else>Scan Document with AI</span>
                    </button>
                </form>
            </div>

            <!-- Previous Scans -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Scan History</h2>

                <div v-if="scans.data.length > 0" class="space-y-4">
                    <div
                        v-for="scan in scans.data"
                        :key="scan.id"
                        class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <!-- Document Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-sm font-medium capitalize',
                                            getStatusColor(scan.status)
                                        ]"
                                    >
                                        {{ scan.status }}
                                    </span>
                                    <span class="text-sm text-gray-500 capitalize">
                                        {{ scan.document_type.replace('_', ' ') }}
                                    </span>
                                    <span class="text-sm text-gray-400">
                                        {{ formatDate(scan.created_at) }}
                                    </span>
                                </div>

                                <!-- Extracted Data Preview -->
                                <div v-if="scan.status === 'completed' && scan.extracted_data" class="space-y-2">
                                    <div class="flex items-center gap-2 text-sm">
                                        <CheckCircleIcon class="h-5 w-5 text-green-500" />
                                        <span class="font-medium text-gray-900">
                                            Data extracted ({{ Object.keys(scan.extracted_data).length }} fields)
                                        </span>
                                        <span v-if="scan.confidence_score" class="text-gray-500">
                                            • {{ scan.confidence_score }}% confidence
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(value, key) in Object.entries(scan.extracted_data).slice(0, 3)"
                                            :key="key"
                                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs"
                                        >
                                            {{ value[0].replace('_', ' ') }}: {{ value[1] }}
                                        </span>
                                        <span v-if="Object.keys(scan.extracted_data).length > 3" class="text-xs text-gray-500 self-center">
                                            +{{ Object.keys(scan.extracted_data).length - 3 }} more
                                        </span>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div v-else-if="scan.status === 'failed'" class="flex items-start gap-2 text-sm text-red-600">
                                    <XCircleIcon class="h-5 w-5 flex-shrink-0" />
                                    <span>{{ scan.error_message || 'Processing failed' }}</span>
                                </div>

                                <!-- Processing -->
                                <div v-else-if="scan.status === 'processing'" class="flex items-center gap-2 text-sm text-indigo-600">
                                    <ArrowPathIcon class="h-5 w-5 animate-spin" />
                                    <div>
                                        <span class="font-medium">Processing document...</span>
                                        <p class="text-xs text-indigo-500 mt-0.5">Estimated: 5-30 seconds</p>
                                    </div>
                                </div>

                                <!-- Pending -->
                                <div v-else class="flex items-center gap-2 text-sm text-gray-500">
                                    <ClockIcon class="h-5 w-5" />
                                    <span>Pending processing</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="scan.status === 'completed'"
                                    :href="route('document-scanner.show', scan.id)"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                    View Details
                                </Link>

                                <button
                                    v-if="scan.status === 'failed'"
                                    @click="reprocess(scan.id)"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium"
                                >
                                    <ArrowPathIcon class="h-4 w-4" />
                                    Retry
                                </button>

                                <button
                                    @click="confirmDelete(scan.id)"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <DocumentMagnifyingGlassIcon class="mx-auto h-16 w-16 text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No scans yet</h3>
                    <p class="text-gray-600">Upload your first document to get started with AI-powered extraction</p>
                </div>

                <!-- Pagination -->
                <div v-if="scans.data.length > 0" class="mt-6">
                    <Pagination :links="scans.links" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Document Scan</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete this scan? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteScan"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import {
    DocumentMagnifyingGlassIcon,
    CloudArrowUpIcon,
    SparklesIcon,
    InformationCircleIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
    ClockIcon,
    EyeIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    scans: Object,
});

const form = useForm({
    document_type: '',
    document_image: null,
});

const isDragging = ref(false);
const previewUrl = ref(null);
const fileInput = ref(null);
const showDeleteModal = ref(false);
const scanToDelete = ref(null);

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.document_image = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const handleDrop = (event) => {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        form.document_image = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const clearFile = () => {
    form.document_image = null;
    previewUrl.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const uploadDocument = () => {
    form.post(route('document-scanner.upload'), {
        onSuccess: () => {
            form.reset();
            clearFile();
        },
    });
};

const reprocess = (scanId) => {
    router.post(route('document-scanner.reprocess', scanId));
};

const confirmDelete = (scanId) => {
    scanToDelete.value = scanId;
    showDeleteModal.value = true;
};

const deleteScan = () => {
    if (scanToDelete.value) {
        router.delete(route('document-scanner.destroy', scanToDelete.value), {
            onSuccess: () => {
                showDeleteModal.value = false;
                scanToDelete.value = null;
            },
        });
    }
};

const getStatusColor = (status) => {
    const colors = {
        completed: 'bg-green-100 text-green-800',
        processing: 'bg-indigo-100 text-indigo-800',
        pending: 'bg-gray-100 text-gray-800',
        failed: 'bg-red-100 text-red-800',
    };
    return colors[status] || colors.pending;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
