<template>
    <Head :title="`Document Scan - ${scan.document_type}`" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-rhythm-2xl">
            <!-- Header -->
            <div class="mb-rhythm-2xl">
                <Link
                    :href="route('document-scanner.index')"
                    class="inline-flex items-center gap-rhythm-sm text-heritage-600 hover:text-heritage-700 mb-rhythm-md font-medium"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                    Back to Scanner
                </Link>

                <div class="flex items-start justify-between gap-rhythm-md">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-rhythm-md">Document Scan Details</h1>
                        <div class="flex flex-wrap items-center gap-rhythm-sm">
                            <StatusBadge
                                :status="getStatusType(scan.status)"
                                :label="scan.status"
                                size="md"
                            />
                            <span class="px-rhythm-sm py-1 bg-heritage-100 text-heritage-800 rounded-lg text-sm font-medium capitalize">
                                {{ scan.document_type.replace('_', ' ') }}
                            </span>
                            <span class="text-gray-400 text-sm">
                                {{ formatDate(scan.created_at) }}
                            </span>
                        </div>
                    </div>

                    <FlowButton
                        variant="error"
                        size="md"
                        @click="confirmDelete"
                    >
                        <template #icon-left>
                            <TrashIcon class="h-4 w-4" />
                        </template>
                        Delete Scan
                    </FlowButton>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-rhythm-xl">
                <!-- Document Image -->
                <div>
                    <RhythmicCard variant="heritage" size="lg">
                        <template #default>
                            <h2 class="text-xl font-semibold text-gray-900 mb-rhythm-md">Document Image</h2>
                            <div class="aspect-[3/2] bg-gray-100 rounded-xl overflow-hidden mb-rhythm-md">
                                <img
                                    :src="`/storage/${scan.original_image}`"
                                    alt="Document"
                                    class="w-full h-full object-contain"
                                />
                            </div>
                            <div class="space-y-rhythm-sm text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span class="font-medium">Processing Method:</span>
                                    <span>{{ scan.processing_method || 'N/A' }}</span>
                                </div>
                                <div v-if="scan.confidence_score" class="flex justify-between">
                                    <span class="font-medium">Confidence Score:</span>
                                    <StatusBadge
                                        :status="scan.confidence_score >= 90 ? 'success' : scan.confidence_score >= 70 ? 'warning' : 'error'"
                                        :label="`${scan.confidence_score}%`"
                                        size="sm"
                                    />
                                </div>
                                <div v-if="scan.processing_time" class="flex justify-between">
                                    <span class="font-medium">Processing Time:</span>
                                    <span>{{ scan.processing_time }}s</span>
                                </div>
                                <div v-if="scan.processed_at" class="flex justify-between">
                                    <span class="font-medium">Processed:</span>
                                    <span>{{ formatDate(scan.processed_at) }}</span>
                                </div>
                            </div>

                        <!-- Fraud Detection Metadata -->
                        <div v-if="scan.metadata" class="mt-rhythm-lg border-t-2 border-gray-100 pt-rhythm-lg">
                            <h3 class="text-sm font-semibold text-gray-900 mb-rhythm-sm">Document Analysis</h3>
                            
                            <!-- Fraud Indicators Warning -->
                            <div v-if="scan.metadata.fraud_indicators && scan.metadata.fraud_indicators.length > 0" 
                                 class="mb-rhythm-md bg-sunrise-50 border-2 border-sunrise-200 rounded-xl p-rhythm-sm">
                                <div class="flex gap-rhythm-sm mb-rhythm-xs">
                                    <ExclamationTriangleIcon class="h-5 w-5 text-sunrise-600 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-semibold text-sunrise-900">Potential Issues Detected</p>
                                        <ul class="mt-rhythm-xs text-xs text-sunrise-800 list-disc list-inside space-y-1">
                                            <li v-for="(indicator, idx) in scan.metadata.fraud_indicators" :key="idx">
                                                {{ indicator }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- File Information -->
                            <div class="grid grid-cols-2 gap-rhythm-sm text-xs">
                                <div v-if="scan.metadata.file_size" class="flex justify-between">
                                    <span class="text-gray-500">File Size:</span>
                                    <span class="text-gray-900 font-medium">{{ formatFileSize(scan.metadata.file_size) }}</span>
                                </div>
                                <div v-if="scan.metadata.mime_type" class="flex justify-between">
                                    <span class="text-gray-500">Type:</span>
                                    <span class="text-gray-900 font-medium">{{ scan.metadata.mime_type }}</span>
                                </div>
                                <div v-if="scan.metadata.width && scan.metadata.height" class="flex justify-between">
                                    <span class="text-gray-500">Dimensions:</span>
                                    <span class="text-gray-900 font-medium">{{ scan.metadata.width }} × {{ scan.metadata.height }}</span>
                                </div>
                                <div v-if="scan.metadata.aspect_ratio" class="flex justify-between">
                                    <span class="text-gray-500">Aspect Ratio:</span>
                                    <span class="text-gray-900 font-medium">{{ scan.metadata.aspect_ratio }}</span>
                                </div>
                            </div>

                            <!-- EXIF Data -->
                            <div v-if="hasExifData(scan.metadata)" class="mt-rhythm-sm pt-rhythm-sm border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 mb-rhythm-xs">Camera Information</p>
                                <div class="grid grid-cols-2 gap-rhythm-xs text-xs">
                                    <div v-if="scan.metadata.camera_make" class="flex justify-between">
                                        <span class="text-gray-500">Make:</span>
                                        <span class="text-gray-900">{{ scan.metadata.camera_make }}</span>
                                    </div>
                                    <div v-if="scan.metadata.camera_model" class="flex justify-between">
                                        <span class="text-gray-500">Model:</span>
                                        <span class="text-gray-900">{{ scan.metadata.camera_model }}</span>
                                    </div>
                                    <div v-if="scan.metadata.datetime_original" class="flex justify-between">
                                        <span class="text-gray-500">Taken:</span>
                                        <span class="text-gray-900">{{ scan.metadata.datetime_original }}</span>
                                    </div>
                                    <div v-if="scan.metadata.software" class="flex justify-between">
                                        <span class="text-gray-500">Software:</span>
                                        <span class="text-gray-900">{{ scan.metadata.software }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </template>
                    </RhythmicCard>
                </div>

                <!-- Extracted Data -->
                <div>
                    <RhythmicCard variant="ocean" size="lg">
                        <template #default>
                            <div class="flex items-center justify-between mb-rhythm-md">
                                <h2 class="text-xl font-semibold text-gray-900">Extracted Data</h2>
                                <FlowButton
                                    v-if="scan.status === 'completed' && Object.keys(scan.extracted_data || {}).length > 0"
                                    variant="ocean"
                                    size="sm"
                                    @click="showApplyModal = true"
                                >
                                    <template #icon-left>
                                        <ArrowDownTrayIcon class="h-4 w-4" />
                                    </template>
                                    Apply to Profile
                                </FlowButton>
                            </div>

                        <div v-if="scan.status === 'completed'">
                            <div v-if="scan.extracted_data && Object.keys(scan.extracted_data).length > 0" class="space-y-rhythm-sm">
                                <div
                                    v-for="(value, key) in scan.extracted_data"
                                    :key="key"
                                    class="flex justify-between items-start py-rhythm-sm border-b-2 border-ocean-100 last:border-0"
                                >
                                    <span class="font-medium text-gray-700 capitalize">
                                        {{ key.replace(/_/g, ' ') }}
                                    </span>
                                    <span class="text-gray-900 text-right max-w-xs font-semibold">{{ value }}</span>
                                </div>
                            </div>
                            <div v-else class="text-center py-rhythm-2xl text-gray-500">
                                <DocumentIcon class="w-12 h-12 mx-auto mb-rhythm-sm text-gray-300" />
                                <p>No data extracted from this document</p>
                            </div>
                        </div>

                        <div v-else-if="scan.status === 'failed'" class="text-center py-rhythm-2xl">
                            <div class="w-16 h-16 mx-auto mb-rhythm-md rounded-2xl bg-red-100 flex items-center justify-center">
                                <XCircleIcon class="h-10 w-10 text-red-600" />
                            </div>
                            <p class="text-red-600 font-medium mb-rhythm-sm text-lg">Processing Failed</p>
                            <p class="text-gray-600 text-sm mb-rhythm-lg">{{ scan.error_message || 'Unknown error occurred' }}</p>
                            <FlowButton
                                variant="sunrise"
                                size="md"
                                @click="reprocess"
                            >
                                <template #icon-left>
                                    <ArrowPathIcon class="h-5 w-5" />
                                </template>
                                Retry Processing
                            </FlowButton>
                        </div>

                        <div v-else class="text-center py-rhythm-2xl">
                            <div class="w-16 h-16 mx-auto mb-rhythm-md rounded-2xl bg-heritage-100 flex items-center justify-center">
                                <ArrowPathIcon class="h-10 w-10 text-heritage-600 animate-spin" />
                            </div>
                            <p class="text-heritage-600 font-medium text-lg">Processing document...</p>
                            <p class="text-gray-600 text-sm mt-rhythm-xs">This may take a few moments</p>
                        </div>
                        </template>
                    </RhythmicCard>

                    <!-- Tips for Manual Entry -->
                    <RhythmicCard v-if="scan.status === 'failed'" variant="sunrise" size="md" class="mt-rhythm-lg">
                        <template #icon>
                            <InformationCircleIcon class="h-5 w-5" />
                        </template>
                        <template #default>
                            <p class="font-semibold mb-rhythm-xs text-gray-900">Extraction failed?</p>
                            <p class="text-sm text-gray-700">You can manually enter your document information in your profile settings.</p>
                        </template>
                    </RhythmicCard>
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
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import {
    ArrowLeftIcon,
    ArrowDownTrayIcon,
    ArrowPathIcon,
    XCircleIcon,
    InformationCircleIcon,
    TrashIcon,
    ExclamationTriangleIcon,
    DocumentIcon,
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

const getStatusType = (status) => {
    const types = {
        completed: 'success',
        processing: 'info',
        pending: 'pending',
        failed: 'error',
    };
    return types[status] || 'pending';
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
