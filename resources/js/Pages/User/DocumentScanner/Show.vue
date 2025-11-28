<template>
    <Head :title="`Document Scan - ${scan.document_type}`" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    :href="route('document-scanner.index')"
                    class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 mb-4"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                    Back to Scanner
                </Link>

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Scan Details</h1>
                        <div class="flex items-center gap-3">
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium capitalize',
                                    getStatusColor(scan.status)
                                ]"
                            >
                                {{ scan.status }}
                            </span>
                            <span class="text-gray-500 capitalize">
                                {{ scan.document_type.replace('_', ' ') }}
                            </span>
                            <span class="text-gray-400 text-sm">
                                {{ formatDate(scan.created_at) }}
                            </span>
                        </div>
                    </div>

                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors"
                    >
                        Delete Scan
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Document Image -->
                <div>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Document Image</h2>
                        <div class="aspect-[3/2] bg-gray-100 rounded-lg overflow-hidden">
                            <img
                                :src="`/storage/${scan.original_image}`"
                                alt="Document"
                                class="w-full h-full object-contain"
                            />
                        </div>
                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Processing Method:</span> {{ scan.processing_method || 'N/A' }}</p>
                            <p v-if="scan.confidence_score">
                                <span class="font-medium">Confidence Score:</span> {{ scan.confidence_score }}%
                            </p>
                            <p v-if="scan.processing_time">
                                <span class="font-medium">Processing Time:</span> {{ scan.processing_time }}s
                            </p>
                            <p v-if="scan.processed_at">
                                <span class="font-medium">Processed:</span> {{ formatDate(scan.processed_at) }}
                            </p>
                        </div>

                        <!-- Fraud Detection Metadata -->
                        <div v-if="scan.metadata" class="mt-6 border-t pt-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Document Analysis</h3>
                            
                            <!-- Fraud Indicators Warning -->
                            <div v-if="scan.metadata.fraud_indicators && scan.metadata.fraud_indicators.length > 0" 
                                 class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <div class="flex gap-2 mb-2">
                                    <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-900">Potential Issues Detected</p>
                                        <ul class="mt-1 text-xs text-amber-800 list-disc list-inside space-y-1">
                                            <li v-for="(indicator, idx) in scan.metadata.fraud_indicators" :key="idx">
                                                {{ indicator }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- File Information -->
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div v-if="scan.metadata.file_size">
                                    <span class="text-gray-500">File Size:</span>
                                    <span class="ml-1 text-gray-900 font-medium">{{ formatFileSize(scan.metadata.file_size) }}</span>
                                </div>
                                <div v-if="scan.metadata.mime_type">
                                    <span class="text-gray-500">Type:</span>
                                    <span class="ml-1 text-gray-900 font-medium">{{ scan.metadata.mime_type }}</span>
                                </div>
                                <div v-if="scan.metadata.width && scan.metadata.height">
                                    <span class="text-gray-500">Dimensions:</span>
                                    <span class="ml-1 text-gray-900 font-medium">{{ scan.metadata.width }} × {{ scan.metadata.height }}</span>
                                </div>
                                <div v-if="scan.metadata.aspect_ratio">
                                    <span class="text-gray-500">Aspect Ratio:</span>
                                    <span class="ml-1 text-gray-900 font-medium">{{ scan.metadata.aspect_ratio }}</span>
                                </div>
                            </div>

                            <!-- EXIF Data -->
                            <div v-if="hasExifData(scan.metadata)" class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-2">Camera Information</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div v-if="scan.metadata.camera_make">
                                        <span class="text-gray-500">Make:</span>
                                        <span class="ml-1 text-gray-900">{{ scan.metadata.camera_make }}</span>
                                    </div>
                                    <div v-if="scan.metadata.camera_model">
                                        <span class="text-gray-500">Model:</span>
                                        <span class="ml-1 text-gray-900">{{ scan.metadata.camera_model }}</span>
                                    </div>
                                    <div v-if="scan.metadata.datetime_original">
                                        <span class="text-gray-500">Taken:</span>
                                        <span class="ml-1 text-gray-900">{{ scan.metadata.datetime_original }}</span>
                                    </div>
                                    <div v-if="scan.metadata.software">
                                        <span class="text-gray-500">Software:</span>
                                        <span class="ml-1 text-gray-900">{{ scan.metadata.software }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extracted Data -->
                <div>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Extracted Data</h2>
                            <button
                                v-if="scan.status === 'completed' && Object.keys(scan.extracted_data || {}).length > 0"
                                @click="showApplyModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
                            >
                                <ArrowDownTrayIcon class="h-4 w-4" />
                                Apply to Profile
                            </button>
                        </div>

                        <div v-if="scan.status === 'completed'">
                            <div v-if="scan.extracted_data && Object.keys(scan.extracted_data).length > 0" class="space-y-4">
                                <div
                                    v-for="(value, key) in scan.extracted_data"
                                    :key="key"
                                    class="flex justify-between items-start py-3 border-b border-gray-200 last:border-0"
                                >
                                    <span class="font-medium text-gray-700 capitalize">
                                        {{ key.replace(/_/g, ' ') }}
                                    </span>
                                    <span class="text-gray-900 text-right max-w-xs">{{ value }}</span>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                No data extracted from this document
                            </div>
                        </div>

                        <div v-else-if="scan.status === 'failed'" class="text-center py-8">
                            <XCircleIcon class="mx-auto h-12 w-12 text-red-500 mb-4" />
                            <p class="text-red-600 font-medium mb-2">Processing Failed</p>
                            <p class="text-gray-600 text-sm mb-4">{{ scan.error_message || 'Unknown error occurred' }}</p>
                            <button
                                @click="reprocess"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors"
                            >
                                <ArrowPathIcon class="h-5 w-5" />
                                Retry Processing
                            </button>
                        </div>

                        <div v-else class="text-center py-8">
                            <ArrowPathIcon class="mx-auto h-12 w-12 text-indigo-500 animate-spin mb-4" />
                            <p class="text-indigo-600 font-medium">Processing document...</p>
                            <p class="text-gray-600 text-sm mt-2">This may take a few moments</p>
                        </div>
                    </div>

                    <!-- Tips for Manual Entry -->
                    <div v-if="scan.status === 'failed'" class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex gap-3">
                            <InformationCircleIcon class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-amber-900">
                                <p class="font-semibold mb-2">Extraction failed?</p>
                                <p>You can manually enter your document information in your profile settings.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apply to Profile Modal -->
        <Modal :show="showApplyModal" @close="showApplyModal = false" max-width="2xl">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Apply Data to Profile</h3>
                <p class="text-gray-600 mb-6">
                    Select which fields you want to apply to your profile. Existing data will be overwritten.
                </p>

                <form @submit.prevent="applyToProfile">
                    <div class="space-y-3 max-h-96 overflow-y-auto mb-6">
                        <label
                            v-for="(value, key) in scan.extracted_data"
                            :key="key"
                            class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                        >
                            <input
                                v-model="selectedFields"
                                type="checkbox"
                                :value="key"
                                class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 capitalize">{{ key.replace(/_/g, ' ') }}</p>
                                <p class="text-sm text-gray-600">{{ value }}</p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showApplyModal = false"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="selectedFields.length === 0 || applyForm.processing"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            {{ applyForm.processing ? 'Applying...' : `Apply ${selectedFields.length} Field${selectedFields.length !== 1 ? 's' : ''}` }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

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
import Modal from '@/Components/Modal.vue';
import {
    ArrowLeftIcon,
    ArrowDownTrayIcon,
    ArrowPathIcon,
    XCircleIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    scan: Object,
});

const showApplyModal = ref(false);
const showDeleteModal = ref(false);
const selectedFields = ref([]);

const applyForm = useForm({
    fields: [],
});

const applyToProfile = () => {
    applyForm.fields = selectedFields.value;
    applyForm.post(route('document-scanner.apply', props.scan.id), {
        onSuccess: () => {
            showApplyModal.value = false;
            selectedFields.value = [];
        },
    });
};

const reprocess = () => {
    router.post(route('document-scanner.reprocess', props.scan.id));
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteScan = () => {
    router.delete(route('document-scanner.destroy', props.scan.id), {
        onSuccess: () => {
            router.visit(route('document-scanner.index'));
        },
    });
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

const formatFileSize = (bytes) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
};

const hasExifData = (metadata) => {
    return metadata?.camera_make || metadata?.camera_model || 
           metadata?.datetime_original || metadata?.software;
};
</script>
