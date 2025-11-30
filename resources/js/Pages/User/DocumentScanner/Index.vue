<template>
    <Head title="Document Scanner - AI-Powered OCR" />

    <AuthenticatedLayout>
        <!-- Hero Section with better gradient -->
        <div class="relative overflow-hidden bg-blue-50 border-b border-gray-200">
            <div class="absolute inset-0 bg-grid-gray-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] -z-10"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0 p-4 rounded-2xl bg-blue-600 shadow-xl shadow-blue-200 border-2 border-blue-700">
                        <DocumentMagnifyingGlassIcon class="h-14 w-14 text-white" />
                    </div>
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold mb-2 text-blue-900">
                            AI Document Scanner
                        </h1>
                        <p class="text-xl text-gray-600 mb-4">
                            Extract data from passports, IDs, and visas instantly with AI
                        </p>
                        <div class="flex flex-wrap gap-6 text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <SparklesIcon class="h-5 w-5 text-blue-600" />
                                <span>98% accuracy rate</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <ClockIcon class="h-5 w-5 text-blue-600" />
                                <span>Process in 5-30 seconds</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <CheckCircleIcon class="h-5 w-5 text-blue-600" />
                                <span>Auto-fill your profile</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Upload Section with modern card design -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="bg-blue-600 px-6 py-4 border-b-2 border-blue-700">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <CloudArrowUpIcon class="h-7 w-7" />
                        Upload New Document
                    </h2>
                </div>
                
                <form @submit.prevent="uploadDocument" class="p-6 space-y-6">
                    <!-- Document Type with icons -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <label
                                v-for="type in documentTypes"
                                :key="type.value"
                                :class="[
                                    'relative flex flex-col items-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition-all',
                                    form.document_type === type.value
                                        ? 'border-indigo-500 bg-indigo-50 shadow-md'
                                        : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50'
                                ]"
                            >
                                <input
                                    type="radio"
                                    v-model="form.document_type"
                                    :value="type.value"
                                    class="sr-only"
                                />
                                <component :is="type.icon" class="h-8 w-8" :class="form.document_type === type.value ? 'text-indigo-600' : 'text-gray-400'" />
                                <span class="text-sm font-medium text-center" :class="form.document_type === type.value ? 'text-indigo-900' : 'text-gray-700'">
                                    {{ type.label }}
                                </span>
                                <CheckCircleIcon 
                                    v-if="form.document_type === type.value"
                                    class="absolute top-2 right-2 h-5 w-5 text-indigo-600"
                                />
                            </label>
                        </div>
                    </div>

                    <!-- File Upload with better design -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">
                            Document Image <span class="text-red-500">*</span>
                        </label>
                        <div
                            @dragover.prevent="isDragging = true"
                            @dragleave="isDragging = false"
                            @drop.prevent="handleDrop"
                            :class="[
                                'relative border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all duration-200',
                                isDragging 
                                    ? 'border-indigo-500 bg-indigo-50 scale-[1.02]' 
                                    : 'border-gray-300 hover:border-indigo-400 hover:bg-gray-50',
                                previewUrl ? 'p-4' : ''
                            ]"
                            @click="!previewUrl && $refs.fileInput.click()"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                @change="handleFileSelect"
                                accept="image/jpeg,image/png,image/jpg"
                                class="hidden"
                            />

                            <div v-if="!previewUrl" class="py-8">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-600 flex items-center justify-center border-2 border-blue-700">
                                    <CloudArrowUpIcon class="h-10 w-10 text-white" />
                                </div>
                                <p class="text-lg font-semibold text-gray-900 mb-2">
                                    <span class="text-blue-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-sm text-gray-500 mb-4">PNG, JPG up to 10MB</p>
                                <div class="flex items-center justify-center gap-4 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <SparklesIcon class="h-4 w-4" />
                                        AI-powered
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1">
                                        <CheckCircleIcon class="h-4 w-4" />
                                        Secure upload
                                    </span>
                                </div>
                            </div>

                            <div v-else class="space-y-4">
                                <div class="relative inline-block">
                                    <img :src="previewUrl" alt="Preview" class="max-h-80 mx-auto rounded-xl shadow-lg" />
                                    <button
                                        type="button"
                                        @click.stop="clearFile"
                                        class="absolute top-2 right-2 p-2 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-lg transition-colors"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Click trash icon to remove and upload a different image</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 rounded-2xl p-6 border-2 border-blue-100">
                        <div class="flex items-center gap-2 mb-4">
                            <LightBulbIcon class="h-6 w-6 text-blue-600" />
                            <h3 class="font-semibold text-gray-900">Pro Tips for Best Results</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-3">
                                <CheckCircleIcon class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <span>Ensure good lighting and avoid shadows on your document</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <CheckCircleIcon class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <span>Capture the entire document within the frame for complete data</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <CheckCircleIcon class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <span>Keep the document flat on a contrasting surface to avoid glare</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <CheckCircleIcon class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <span>Use a high-resolution image for maximum OCR accuracy</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="button"
                        :disabled="form.processing || !form.document_type || !form.document_image"
                        @click="uploadDocument"
                        :class="[
                            'w-full py-4 px-6 rounded-2xl font-semibold text-white shadow-lg transition-all duration-200',
                            'bg-blue-600',
                            'hover:bg-blue-700 hover:shadow-xl hover:scale-[1.02]',
                            'disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100',
                            'flex items-center justify-center gap-3'
                        ]"
                    >
                        <SparklesIcon class="h-6 w-6" />
                        <span class="text-lg">{{ form.processing ? 'Processing with AI...' : 'Scan Document with AI' }}</span>
                    </button>
                </form>
            </div>

            <!-- Previous Scans -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-8 bg-blue-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-gray-900">Scan History</h2>
                </div>

                <div v-if="scans.data.length > 0" class="space-y-4">
                    <div
                        v-for="scan in scans.data"
                        :key="scan.id"
                        class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-200 overflow-hidden border border-gray-100"
                    >
                        <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <!-- Document Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <StatusBadge
                                        :status="getStatusType(scan.status)"
                                        :label="scan.status"
                                        size="md"
                                    />
                                    <span class="text-sm font-medium text-gray-700 capitalize">
                                        {{ scan.document_type.replace('_', ' ') }}
                                    </span>
                                    <span class="text-sm text-gray-400">
                                        {{ formatDate(scan.created_at) }}
                                    </span>
                                </div>

                                <!-- Extracted Data Preview -->
                                <div v-if="scan.status === 'completed' && scan.extracted_data" class="space-y-3">
                                    <div class="flex items-center gap-2 text-sm">
                                        <CheckCircleIcon class="h-5 w-5 text-green-500" />
                                        <span class="font-semibold text-gray-900">
                                            Data extracted ({{ Object.keys(scan.extracted_data).length }} fields)
                                        </span>
                                        <span v-if="scan.confidence_score" class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                            {{ scan.confidence_score }}% confidence
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(value, key) in Object.entries(scan.extracted_data).slice(0, 3)"
                                            :key="key"
                                            class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium"
                                        >
                                            {{ value[0].replace('_', ' ') }}: {{ value[1] }}
                                        </span>
                                        <span v-if="Object.keys(scan.extracted_data).length > 3" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium self-center">
                                            +{{ Object.keys(scan.extracted_data).length - 3 }} more fields
                                        </span>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div v-else-if="scan.status === 'failed'" class="flex items-start gap-3 p-4 bg-red-50 rounded-xl border border-red-100">
                                    <XCircleIcon class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
                                    <span class="text-sm text-red-700">{{ scan.error_message || 'Processing failed. Please try again.' }}</span>
                                </div>

                                <!-- Processing -->
                                <div v-else-if="scan.status === 'processing'" class="flex items-center gap-3 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                    <ArrowPathIcon class="h-5 w-5 animate-spin text-indigo-600" />
                                    <div>
                                        <span class="font-medium text-indigo-900 text-sm">Processing document with AI...</span>
                                        <p class="text-xs text-indigo-600 mt-1">Estimated: 5-30 seconds</p>
                                    </div>
                                </div>

                                <!-- Pending -->
                                <div v-else class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <ClockIcon class="h-5 w-5 text-gray-500" />
                                    <span class="text-sm text-gray-600 font-medium">Waiting for processing...</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="scan.status === 'completed'"
                                    :href="route('document-scanner.show', scan.id)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2 text-sm font-medium"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                    View Details
                                </Link>

                                <button
                                    v-if="scan.status === 'failed'"
                                    @click="reprocess(scan.id)"
                                    class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors flex items-center gap-2 text-sm font-medium"
                                >
                                    <ArrowPathIcon class="h-4 w-4" />
                                    Retry
                                </button>

                                <button
                                    @click="confirmDelete(scan.id)"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors"
                                    title="Delete scan"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl shadow-md p-12 text-center border border-gray-100">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-blue-100 flex items-center justify-center border-2 border-blue-200">
                        <DocumentMagnifyingGlassIcon class="h-12 w-12 text-blue-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No scans yet</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">Upload your first document to get started with AI-powered data extraction</p>
                    <button
                        @click="scrollToUpload"
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all font-medium inline-flex items-center gap-2"
                    >
                        <CloudArrowUpIcon class="h-5 w-5" />
                        Upload Document
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="scans.data.length > 0" class="mt-6">
                    <Pagination :links="scans.links" />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-8">
                <div class="mb-6">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-2xl bg-red-100 flex items-center justify-center">
                        <TrashIcon class="h-7 w-7 text-red-600" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 text-center">Delete Document Scan</h3>
                    <p class="text-gray-600 text-center">
                        Are you sure you want to delete this scan? This action cannot be undone and all extracted data will be lost.
                    </p>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteScan"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium flex items-center gap-2"
                    >
                        <TrashIcon class="h-4 w-4" />
                        Delete Scan
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
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import {
    DocumentMagnifyingGlassIcon,
    CloudArrowUpIcon,
    SparklesIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
    ClockIcon,
    EyeIcon,
    TrashIcon,
    LightBulbIcon,
    IdentificationIcon,
    CreditCardIcon,
    DocumentIcon,
    NewspaperIcon,
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

const scrollToUpload = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>
