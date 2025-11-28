<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-900">Documents Management</h3>
            <p class="mt-1 text-sm text-gray-600">
                Upload and manage your important documents securely
            </p>
        </div>

        <!-- Upload Section -->
        <div class="bg-white rounded-lg border-2 border-dashed border-indigo-300 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-indigo-600 rounded-lg">
                    <CloudArrowUpIcon class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Upload New Document</h4>
                    <p class="text-sm text-gray-600">Supported formats: PDF, JPG, PNG (Max 10MB)</p>
                </div>
            </div>

            <form @submit.prevent="uploadDocument" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document Title *
                        </label>
                        <input
                            v-model="uploadForm.title"
                            type="text"
                            required
                            placeholder="e.g., Passport Copy"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type *
                        </label>
                        <select
                            v-model="uploadForm.document_type"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Select type...</option>
                            <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea
                        v-model="uploadForm.description"
                        rows="2"
                        placeholder="Optional description..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Issue Date
                        </label>
                        <input
                            v-model="uploadForm.issue_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Expiry Date
                        </label>
                        <input
                            v-model="uploadForm.expiry_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document Number
                        </label>
                        <input
                            v-model="uploadForm.document_number"
                            type="text"
                            placeholder="e.g., A12345678"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Issuing Authority
                        </label>
                        <input
                            v-model="uploadForm.issuing_authority"
                            type="text"
                            placeholder="e.g., DIP, Bangladesh"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select File *
                    </label>
                    <input
                        type="file"
                        @change="handleFileSelect"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    />
                    <p v-if="uploadForm.file" class="mt-2 text-sm text-gray-600">
                        Selected: {{ uploadForm.file.name }} ({{ formatFileSize(uploadForm.file.size) }})
                    </p>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="uploadForm.processing || !uploadForm.file"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium flex items-center gap-2"
                    >
                        <CloudArrowUpIcon class="w-5 h-5" />
                        <span v-if="!uploadForm.processing">Upload Document</span>
                        <span v-else>Uploading...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Documents List -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h4 class="font-semibold text-gray-900">Your Documents ({{ documents.length }})</h4>
                <div class="flex gap-2">
                    <button
                        v-for="filterType in ['all', 'active', 'expired']"
                        :key="filterType"
                        @click="filter = filterType"
                        :class="[
                            'px-3 py-1 rounded-lg text-sm font-medium transition',
                            filter === filterType
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        {{ filterType.charAt(0).toUpperCase() + filterType.slice(1) }}
                    </button>
                </div>
            </div>

            <div v-if="filteredDocuments.length === 0" class="p-12 text-center">
                <DocumentIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500 mb-2">No documents found</p>
                <p class="text-sm text-gray-400">Upload your first document to get started</p>
            </div>

            <div v-else class="divide-y divide-gray-200">
                <div
                    v-for="doc in filteredDocuments"
                    :key="doc.id"
                    class="p-4 hover:bg-gray-50 transition"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <!-- Document Icon -->
                            <div class="flex-shrink-0">
                                <div :class="[
                                    'w-12 h-12 rounded-lg flex items-center justify-center',
                                    getDocumentColor(doc.document_type)
                                ]">
                                    <DocumentTextIcon class="w-6 h-6 text-white" />
                                </div>
                            </div>

                            <!-- Document Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h5 class="font-semibold text-gray-900 truncate">{{ doc.title }}</h5>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                        {{ documentTypes[doc.document_type] || doc.document_type }}
                                    </span>
                                    <span
                                        v-if="doc.is_verified"
                                        class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 flex items-center gap-1"
                                    >
                                        <CheckBadgeIcon class="w-3 h-3" />
                                        Verified
                                    </span>
                                    <span
                                        v-if="isExpiringSoon(doc)"
                                        class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700"
                                    >
                                        ⚠️ Expiring Soon
                                    </span>
                                    <span
                                        v-if="isExpired(doc)"
                                        class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700"
                                    >
                                        ❌ Expired
                                    </span>
                                </div>

                                <p v-if="doc.description" class="text-sm text-gray-600 mb-2">
                                    {{ doc.description }}
                                </p>

                                <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                                    <span v-if="doc.document_number">
                                        📋 {{ doc.document_number }}
                                    </span>
                                    <span v-if="doc.issuing_authority">
                                        🏛️ {{ doc.issuing_authority }}
                                    </span>
                                    <span v-if="doc.issue_date">
                                        📅 Issued: {{ formatDate(doc.issue_date) }}
                                    </span>
                                    <span v-if="doc.expiry_date" :class="{ 'text-red-600 font-semibold': isExpired(doc) }">
                                        ⏰ Expires: {{ formatDate(doc.expiry_date) }}
                                    </span>
                                    <span>
                                        📄 {{ doc.file_type.toUpperCase() }} • {{ formatFileSize(doc.file_size) }}
                                    </span>
                                    <span>
                                        🕒 {{ formatDate(doc.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 ml-4">
                            <button
                                @click="downloadDocument(doc)"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                title="Download"
                            >
                                <ArrowDownTrayIcon class="w-5 h-5" />
                            </button>
                            <button
                                @click="toggleSharing(doc)"
                                :class="[
                                    'p-2 rounded-lg transition',
                                    doc.is_shared
                                        ? 'text-green-600 hover:bg-green-50'
                                        : 'text-gray-400 hover:bg-gray-100'
                                ]"
                                title="Share"
                            >
                                <ShareIcon class="w-5 h-5" />
                            </button>
                            <button
                                @click="deleteDocument(doc)"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Delete"
                            >
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-600 rounded-lg">
                        <DocumentTextIcon class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-2xl font-bold text-blue-700">{{ documents.length }}</div>
                </div>
                <div class="text-sm font-medium text-blue-800">Total Documents</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-green-600 rounded-lg">
                        <CheckBadgeIcon class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-2xl font-bold text-green-700">{{ verifiedCount }}</div>
                </div>
                <div class="text-sm font-medium text-green-800">Verified</div>
            </div>
            <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-amber-600 rounded-lg">
                        <ExclamationTriangleIcon class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-2xl font-bold text-amber-700">{{ expiringSoonCount }}</div>
                </div>
                <div class="text-sm font-medium text-amber-800">Expiring Soon</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-red-600 rounded-lg">
                        <XCircleIcon class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-2xl font-bold text-red-700">{{ expiredCount }}</div>
                </div>
                <div class="text-sm font-medium text-red-800">Expired</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    CloudArrowUpIcon,
    DocumentIcon,
    DocumentTextIcon,
    ArrowDownTrayIcon,
    ShareIcon,
    TrashIcon,
    CheckBadgeIcon,
    ExclamationTriangleIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    userProfile: {
        type: Object,
        required: true
    }
});

const documents = ref(props.userProfile.documents || []);
const filter = ref('all');

const documentTypes = {
    'passport': '🛂 Passport',
    'visa': '✈️ Visa',
    'nid': '🪪 National ID',
    'birth_certificate': '📄 Birth Certificate',
    'driving_license': '🚗 Driving License',
    'educational_certificate': '🎓 Educational Certificate',
    'work_permit': '💼 Work Permit',
    'bank_statement': '🏦 Bank Statement',
    'police_clearance': '👮 Police Clearance',
    'medical_certificate': '🏥 Medical Certificate',
    'insurance': '🛡️ Insurance Document',
    'reference_letter': '📝 Reference Letter',
    'other': '📎 Other Document',
};

const uploadForm = useForm({
    title: '',
    document_type: '',
    description: '',
    file: null,
    issue_date: '',
    expiry_date: '',
    document_number: '',
    issuing_authority: '',
});

const handleFileSelect = (event) => {
    uploadForm.file = event.target.files[0];
};

const uploadDocument = () => {
    uploadForm.post(route('documents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset();
            // Refresh documents list
            window.location.reload();
        }
    });
};

const downloadDocument = (doc) => {
    window.location.href = route('documents.download', doc.id);
};

const toggleSharing = (doc) => {
    // Implementation for sharing toggle
    console.log('Toggle sharing for', doc.id);
};

const deleteDocument = (doc) => {
    if (confirm('Are you sure you want to delete this document?')) {
        useForm({}).delete(route('documents.destroy', doc.id), {
            preserveScroll: true,
            onSuccess: () => {
                documents.value = documents.value.filter(d => d.id !== doc.id);
            }
        });
    }
};

const isExpired = (doc) => {
    if (!doc.expiry_date) return false;
    return new Date(doc.expiry_date) < new Date();
};

const isExpiringSoon = (doc) => {
    if (!doc.expiry_date || isExpired(doc)) return false;
    const daysUntilExpiry = Math.ceil((new Date(doc.expiry_date) - new Date()) / (1000 * 60 * 60 * 24));
    return daysUntilExpiry <= 30;
};

const filteredDocuments = computed(() => {
    if (filter.value === 'all') return documents.value;
    if (filter.value === 'active') return documents.value.filter(d => !isExpired(d));
    if (filter.value === 'expired') return documents.value.filter(d => isExpired(d));
    return documents.value;
});

const verifiedCount = computed(() => documents.value.filter(d => d.is_verified).length);
const expiredCount = computed(() => documents.value.filter(d => isExpired(d)).length);
const expiringSoonCount = computed(() => documents.value.filter(d => isExpiringSoon(d)).length);

const formatFileSize = (bytes) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getDocumentColor = (type) => {
    const colors = {
        'passport': 'bg-blue-600',
        'visa': 'bg-purple-600',
        'nid': 'bg-indigo-600',
        'birth_certificate': 'bg-pink-600',
        'driving_license': 'bg-green-600',
        'educational_certificate': 'bg-yellow-600',
        'work_permit': 'bg-orange-600',
        'bank_statement': 'bg-cyan-600',
        'police_clearance': 'bg-teal-600',
        'medical_certificate': 'bg-red-600',
        'insurance': 'bg-violet-600',
        'reference_letter': 'bg-emerald-600',
        'other': 'bg-gray-600',
    };
    return colors[type] || 'bg-gray-600';
};
</script>
