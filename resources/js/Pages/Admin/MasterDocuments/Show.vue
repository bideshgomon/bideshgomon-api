<template>
    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Header -->
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">{{ document.document_name }}</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ document.category?.name }}
                                    </span>
                                </p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <Link :href="route('admin.master-documents.edit', document.id)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                                    Edit Document
                                </Link>
                                <Link :href="route('admin.master-documents.index')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                    Back to List
                                </Link>
                            </div>
                        </div>

                        <!-- Document Details -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Information -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Description -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Description</h3>
                                    <p class="text-gray-700 leading-relaxed">
                                        {{ document.description || 'No description provided' }}
                                    </p>
                                </div>

                                <!-- Specifications -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Specifications</h3>
                                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ document.specifications || 'No specifications provided' }}
                                    </div>
                                </div>

                                <!-- International Standard -->
                                <div v-if="document.international_standard" class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                                    <h3 class="text-lg font-semibold text-blue-900 mb-2 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        International Standard
                                    </h3>
                                    <p class="text-blue-800 font-medium">{{ document.international_standard }}</p>
                                </div>

                                <!-- Example URL -->
                                <div v-if="document.example_url" class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Reference Link</h3>
                                    <a :href="document.example_url" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline flex items-center">
                                        {{ document.example_url }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>

                                <!-- Country Assignments -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                        Country Assignments
                                        <span class="ml-2 text-sm font-normal text-gray-500">({{ document.country_requirements?.length || 0 }} countries)</span>
                                    </h3>
                                    <div v-if="document.country_requirements && document.country_requirements.length > 0" class="space-y-2">
                                        <div
                                            v-for="assignment in document.country_requirements"
                                            :key="assignment.id"
                                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                                        >
                                            <div>
                                                <Link :href="route('admin.document-assignments.show', assignment.country_id)" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                    {{ assignment.country?.name }}
                                                </Link>
                                                <span class="ml-2 text-xs text-gray-500">
                                                    {{ assignment.visa_type }} - {{ assignment.profession_category }}
                                                </span>
                                            </div>
                                            <span v-if="assignment.is_mandatory" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Mandatory
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Optional
                                            </span>
                                        </div>
                                    </div>
                                    <p v-else class="text-gray-500 text-sm">Not assigned to any country yet</p>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6">
                                <!-- Status Card -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Status</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Active</span>
                                            <span v-if="document.is_active" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Yes
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                No
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Sort Order</span>
                                            <span class="text-sm font-medium text-gray-900">{{ document.sort_order }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Requirements Card -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Requirements</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Translation</span>
                                            <span v-if="document.translation_required" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Required
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Not Required
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Notarization</span>
                                            <span v-if="document.notarization_required" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Required
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Not Required
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Validity Card -->
                                <div v-if="document.typical_validity_days" class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Validity</h3>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-indigo-600">{{ document.typical_validity_days }}</div>
                                        <div class="text-sm text-gray-600 mt-1">days</div>
                                    </div>
                                </div>

                                <!-- Metadata Card -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Metadata</h3>
                                    <div class="space-y-2 text-xs text-gray-600">
                                        <div>
                                            <span class="font-medium">Created:</span>
                                            {{ formatDate(document.created_at) }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Updated:</span>
                                            {{ formatDate(document.updated_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    document: Object,
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>
