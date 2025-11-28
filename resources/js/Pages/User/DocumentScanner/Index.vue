<template>
    <Head title="Document Scanner - AI-Powered OCR" />

    <AuthenticatedLayout>
        <!-- Hero Section -->
        <AnimatedSection variant="heritage" :show-blobs="true" class="mb-rhythm-2xl">
            <div class="relative z-10">
                <div class="flex items-center gap-rhythm-md mb-rhythm-md">
                    <div class="p-rhythm-md rounded-2xl bg-heritage-600 shadow-lg">
                        <DocumentMagnifyingGlassIcon class="h-12 w-12 text-white" />
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold mb-rhythm-xs text-heritage-900">Document Scanner</h1>
                        <p class="text-xl text-heritage-700">AI-powered document scanning & data extraction</p>
                    </div>
                </div>
                <p class="text-lg max-w-3xl text-heritage-600">
                    Upload your passport, ID, or visa documents and let our AI extract the information automatically.
                    Save time and reduce errors in your applications.
                </p>
            </div>
        </AnimatedSection>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-rhythm-2xl">
            <!-- Upload Section -->
            <RhythmicCard variant="heritage" size="lg" class="mb-rhythm-2xl">
                <template #default>
                    <h2 class="text-2xl font-bold text-gray-900 mb-rhythm-lg">Upload New Document</h2>
                
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
                    <div class="bg-ocean-50 border-2 border-ocean-200 rounded-xl p-rhythm-md">
                        <div class="flex gap-rhythm-sm">
                            <InformationCircleIcon class="h-5 w-5 text-ocean-600 flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-ocean-900">
                                <p class="font-semibold mb-rhythm-sm">Tips for best results:</p>
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
                    <FlowButton
                        variant="heritage"
                        size="lg"
                        full-width
                        :disabled="form.processing || !form.document_type || !form.document_image"
                        :loading="form.processing"
                        @click="uploadDocument"
                    >
                        <template #icon-left>
                            <SparklesIcon class="h-5 w-5" />
                        </template>
                        {{ form.processing ? 'Uploading...' : 'Scan Document with AI' }}
                    </FlowButton>
                </form>
                </template>
            </RhythmicCard>

            <!-- Previous Scans -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-rhythm-lg">Scan History</h2>

                <div v-if="scans.data.length > 0" class="space-y-rhythm-md">
                    <RhythmicCard
                        v-for="scan in scans.data"
                        :key="scan.id"
                        variant="white"
                        hover-lift
                    >
                        <template #default>
                        <div class="flex items-start justify-between gap-4">
                            <!-- Document Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-rhythm-sm mb-rhythm-sm">
                                    <StatusBadge
                                        :status="getStatusType(scan.status)"
                                        :label="scan.status"
                                        size="md"
                                    />
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
                            <div class="flex items-center gap-rhythm-sm">
                                <FlowButton
                                    v-if="scan.status === 'completed'"
                                    variant="heritage"
                                    size="sm"
                                    :href="route('document-scanner.show', scan.id)"
                                >
                                    <template #icon-left>
                                        <EyeIcon class="h-4 w-4" />
                                    </template>
                                    View Details
                                </FlowButton>

                                <FlowButton
                                    v-if="scan.status === 'failed'"
                                    variant="sunrise"
                                    size="sm"
                                    @click="reprocess(scan.id)"
                                >
                                    <template #icon-left>
                                        <ArrowPathIcon class="h-4 w-4" />
                                    </template>
                                    Retry
                                </FlowButton>

                                <button
                                    @click="confirmDelete(scan.id)"
                                    class="p-rhythm-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                        </template>
                    </RhythmicCard>
                </div>

                <!-- Empty State -->
                <RhythmicCard v-else variant="white" size="lg">
                    <template #default>
                        <div class="text-center py-rhythm-2xl">
                            <div class="w-16 h-16 mx-auto mb-rhythm-md rounded-2xl bg-heritage-100 flex items-center justify-center">
                                <DocumentMagnifyingGlassIcon class="h-10 w-10 text-heritage-600" />
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-rhythm-sm">No scans yet</h3>
                            <p class="text-gray-600">Upload your first document to get started with AI-powered extraction</p>
                        </div>
                    </template>
                </RhythmicCard>

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
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
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
</script>
