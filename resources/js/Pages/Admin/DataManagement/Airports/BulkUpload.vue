<template>
    <AdminLayout>
        <Head title="Bulk Upload Airports" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.airports.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Airports
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Bulk Upload Airports</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Upload multiple airports at once using a CSV file</p>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">📋 Instructions</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800 dark:text-blue-300">
                        <li>Download the CSV template below</li>
                        <li>Fill in your airport data with valid city names and airport codes</li>
                        <li>IATA codes must be exactly 3 uppercase letters (e.g., DAC, DXB, LHR)</li>
                        <li>ICAO codes must be exactly 4 uppercase letters (e.g., VGHS, OMDB, EGLL)</li>
                        <li>Upload the completed CSV file</li>
                        <li>Review any errors and fix them</li>
                        <li>Successfully uploaded airports will appear in the list</li>
                    </ol>
                </div>

                <!-- Template Download -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📥 Download Template</h3>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">airports_template.csv</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">CSV template with sample data</p>
                        </div>
                        <a
                            :href="route('admin.data.airports-template')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template
                        </a>
                    </div>

                    <!-- CSV Format Info -->
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Required Columns:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded text-xs font-mono">city_name*</span>
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded text-xs font-mono">name*</span>
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded text-xs font-mono">iata_code*</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 mt-3">Optional Columns:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">name_bn</span>
                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">icao_code</span>
                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">latitude</span>
                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">longitude</span>
                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">is_international</span>
                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs font-mono">is_active</span>
                        </div>
                        <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded">
                            <p class="text-xs text-yellow-800 dark:text-yellow-300">
                                <strong>⚠️ Important:</strong><br>
                                • IATA codes: Exactly 3 uppercase letters (DAC, DXB, LHR)<br>
                                • ICAO codes: Exactly 4 uppercase letters (VGHS, OMDB, EGLL)<br>
                                • City name must match an existing city in the database<br>
                                • Coordinates must be valid decimals (latitude: -90 to 90, longitude: -180 to 180)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📤 Upload CSV File</h3>
                    
                    <!-- File Drop Zone -->
                    <div
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleDrop"
                        :class="dragOver ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'"
                        class="border-2 border-dashed rounded-lg p-12 text-center transition-colors"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".csv,.txt"
                            @change="handleFileSelect"
                            class="hidden"
                        />
                        
                        <div v-if="!form.file">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                Drag and drop your CSV file here, or
                                <button
                                    type="button"
                                    @click="$refs.fileInput.click()"
                                    class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium"
                                >
                                    browse
                                </button>
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">CSV or TXT files only, up to 10MB</p>
                        </div>

                        <div v-else class="flex items-center justify-center space-x-3">
                            <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ form.file.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(form.file.size) }}</p>
                            </div>
                            <button
                                type="button"
                                @click="form.file = null"
                                class="text-red-600 hover:text-red-700 dark:text-red-400"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <p v-if="form.errors.file" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.file }}</p>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <Link
                            :href="route('admin.data.airports.index')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="!form.file || form.processing"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Uploading...' : 'Upload & Process' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const fileInput = ref(null);
const dragOver = ref(false);

const form = useForm({
    file: null,
});

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.file = file;
    }
};

const handleDrop = (e) => {
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    if (file && (file.name.endsWith('.csv') || file.name.endsWith('.txt'))) {
        form.file = file;
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const submit = () => {
    form.post(route('admin.data.airports-process-upload'), {
        forceFormData: true,
    });
};
</script>
