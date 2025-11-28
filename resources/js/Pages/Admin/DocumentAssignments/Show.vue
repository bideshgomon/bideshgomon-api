<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon, 
    TrashIcon,
    CheckCircleIcon,
    XCircleIcon,
    DocumentTextIcon,
    GlobeAltIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    country: Object,
    groupedRequirements: Object,
    categories: Array,
    visaTypes: Array,
    professions: Array,
});

const showAssignModal = ref(false);
const selectedVisaType = ref('tourist');
const selectedProfession = ref(null);
const selectedDocuments = ref([]);

const form = useForm({
    country_id: props.country.id,
    visa_type: 'tourist',
    profession_category: null,
    document_ids: [],
    is_mandatory: true,
});

const openAssignModal = () => {
    showAssignModal.value = true;
    selectedDocuments.value = [];
};

const toggleDocument = (documentId) => {
    const index = selectedDocuments.value.indexOf(documentId);
    if (index > -1) {
        selectedDocuments.value.splice(index, 1);
    } else {
        selectedDocuments.value.push(documentId);
    }
};

const bulkAssign = () => {
    form.visa_type = selectedVisaType.value;
    form.profession_category = selectedProfession.value;
    form.document_ids = selectedDocuments.value;
    
    form.post(route('admin.document-assignments.bulk-assign'), {
        onSuccess: () => {
            showAssignModal.value = false;
            selectedDocuments.value = [];
        },
    });
};

const deleteAssignment = (assignmentId) => {
    if (confirm('Are you sure you want to remove this document assignment?')) {
        router.delete(route('admin.document-assignments.destroy', assignmentId));
    }
};

const getRequirementsByType = (visaType, profession = null) => {
    const key = visaType + '|' + (profession || 'all');
    return props.groupedRequirements[key] || [];
};
</script>

<template>
    <Head :title="`${country.name} - Document Assignments`" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <Link
                            :href="route('admin.document-assignments.index')"
                            class="text-gray-600 hover:text-gray-900 mr-4"
                        >
                            ← Back
                        </Link>
                        <div>
                            <div class="flex items-center">
                                <span class="text-3xl mr-3">{{ country.flag_emoji }}</span>
                                <h1 class="text-2xl font-bold text-gray-900">{{ country.name }}</h1>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">Manage visa document requirements</p>
                        </div>
                    </div>
                    <button
                        @click="openAssignModal"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Assign Documents
                    </button>
                </div>

                <!-- Visa Types Tabs -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px overflow-x-auto">
                            <button
                                v-for="visaType in visaTypes"
                                :key="visaType"
                                @click="selectedVisaType = visaType"
                                :class="[
                                    'px-6 py-3 text-sm font-medium border-b-2 whitespace-nowrap',
                                    selectedVisaType === visaType
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                {{ visaType.charAt(0).toUpperCase() + visaType.slice(1) }}
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- Common Documents (All Professions) -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Common Documents (All Applicants)</h3>
                            <div v-if="getRequirementsByType(selectedVisaType).length > 0" class="space-y-3">
                                <div
                                    v-for="req in getRequirementsByType(selectedVisaType)"
                                    :key="req.id"
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                                >
                                    <div class="flex items-center flex-1">
                                        <DocumentTextIcon class="w-5 h-5 text-gray-400 mr-3" />
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ req.document.document_name }}</div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ req.document.category.name }}
                                                </span>
                                                <span v-if="req.document.international_standard" class="ml-2 text-gray-500 font-mono text-xs">
                                                    {{ req.document.international_standard }}
                                                </span>
                                            </div>
                                            <div v-if="req.specific_notes" class="text-sm text-gray-600 mt-2">
                                                📝 {{ req.specific_notes }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span v-if="req.is_mandatory" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <CheckCircleIcon class="w-3 h-3 mr-1" />
                                                Mandatory
                                            </span>
                                            <span v-else class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Optional
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        @click="deleteAssignment(req.id)"
                                        class="ml-4 text-red-600 hover:text-red-900"
                                        title="Remove"
                                    >
                                        <TrashIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                No common documents assigned yet
                            </div>
                        </div>

                        <!-- Profession-Specific Documents -->
                        <div v-for="profession in professions" :key="profession" class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ profession }}</h3>
                            <div v-if="getRequirementsByType(selectedVisaType, profession).length > 0" class="space-y-3">
                                <div
                                    v-for="req in getRequirementsByType(selectedVisaType, profession)"
                                    :key="req.id"
                                    class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg"
                                >
                                    <div class="flex items-center flex-1">
                                        <DocumentTextIcon class="w-5 h-5 text-indigo-400 mr-3" />
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ req.document.document_name }}</div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    {{ req.document.category.name }}
                                                </span>
                                                <span v-if="req.document.international_standard" class="ml-2 text-gray-500 font-mono text-xs">
                                                    {{ req.document.international_standard }}
                                                </span>
                                            </div>
                                            <div v-if="req.specific_notes" class="text-sm text-gray-600 mt-2">
                                                📝 {{ req.specific_notes }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span v-if="req.is_mandatory" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <CheckCircleIcon class="w-3 h-3 mr-1" />
                                                Mandatory
                                            </span>
                                            <span v-else class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Optional
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        @click="deleteAssignment(req.id)"
                                        class="ml-4 text-red-600 hover:text-red-900"
                                        title="Remove"
                                    >
                                        <TrashIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                                No {{ profession }} documents assigned yet
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assign Documents Modal -->
                <div v-if="showAssignModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAssignModal = false"></div>
                        
                        <div class="relative bg-white rounded-lg max-w-4xl w-full p-6 shadow-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Documents to {{ country.name }}</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Visa Type</label>
                                    <select v-model="selectedVisaType" class="w-full border-gray-300 rounded-md">
                                        <option v-for="type in visaTypes" :key="type" :value="type">
                                            {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Profession (Optional)</label>
                                    <select v-model="selectedProfession" class="w-full border-gray-300 rounded-md">
                                        <option :value="null">All Professions</option>
                                        <option v-for="prof in professions" :key="prof" :value="prof">
                                            {{ prof }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="max-h-96 overflow-y-auto mb-4">
                                <div v-for="category in categories" :key="category.id" class="mb-6">
                                    <h4 class="font-semibold text-gray-900 mb-2">{{ category.name }}</h4>
                                    <div class="space-y-2">
                                        <label
                                            v-for="doc in category.documents"
                                            :key="doc.id"
                                            class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="doc.id"
                                                :checked="selectedDocuments.includes(doc.id)"
                                                @change="toggleDocument(doc.id)"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <div class="ml-3 flex-1">
                                                <div class="font-medium text-gray-900">{{ doc.document_name }}</div>
                                                <div class="text-sm text-gray-600">{{ doc.description }}</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button
                                    @click="showAssignModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="bulkAssign"
                                    :disabled="selectedDocuments.length === 0 || form.processing"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    Assign {{ selectedDocuments.length }} Documents
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
