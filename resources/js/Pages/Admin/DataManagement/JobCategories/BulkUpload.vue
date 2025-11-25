<template>
    <AdminLayout>
        <Head title="Bulk Upload Job Categories" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.job-categories.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Job Categories
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Bulk Upload Job Categories</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Import multiple job categories with parent-child relationships from CSV file</p>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">📋 CSV Upload Instructions</h2>
                    
                    <div class="space-y-3 text-sm text-blue-800 dark:text-blue-200">
                        <div>
                            <strong>Required Column:</strong>
                            <ul class="list-disc ml-6 mt-1 space-y-1">
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">name</code> - Category name in English (e.g., "Software Development")</li>
                            </ul>
                        </div>

                        <div>
                            <strong>Optional Columns:</strong>
                            <ul class="list-disc ml-6 mt-1 space-y-1">
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">parent_code</code> - Parent category slug for hierarchy (e.g., "information-technology")</li>
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">name_bn</code> - Category name in Bengali</li>
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">slug</code> - URL-friendly identifier (auto-generated if empty)</li>
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">description</code> - Brief description of the category</li>
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">order</code> - Display order (default: 0)</li>
                                <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">is_active</code> - 1 for active, 0 for inactive (default: 1)</li>
                            </ul>
                        </div>

                        <div class="bg-white dark:bg-blue-950 rounded p-3 border border-blue-200 dark:border-blue-700">
                            <strong class="block mb-2">💡 Creating Hierarchical Categories:</strong>
                            <p>Use the <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">parent_code</code> column to establish parent-child relationships:</p>
                            <ul class="list-disc ml-6 mt-2 space-y-1">
                                <li><strong>Root categories:</strong> Leave <code>parent_code</code> empty</li>
                                <li><strong>Child categories:</strong> Enter the parent's slug in <code>parent_code</code></li>
                                <li>Example: For "Software Development" under "Information Technology", use <code>parent_code: information-technology</code></li>
                                <li>You can create multiple levels (grandchildren, etc.) in one upload</li>
                            </ul>
                        </div>

                        <div>
                            <strong>Sample Data:</strong>
                            <div class="mt-2 overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-900 text-xs border border-blue-200 dark:border-blue-700">
                                    <thead class="bg-blue-100 dark:bg-blue-800">
                                        <tr>
                                            <th class="px-3 py-2 border-r">parent_code</th>
                                            <th class="px-3 py-2 border-r">name</th>
                                            <th class="px-3 py-2 border-r">name_bn</th>
                                            <th class="px-3 py-2 border-r">slug</th>
                                            <th class="px-3 py-2 border-r">description</th>
                                            <th class="px-3 py-2 border-r">order</th>
                                            <th class="px-3 py-2">is_active</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-t">
                                            <td class="px-3 py-2 border-r"><em class="text-gray-500">(empty)</em></td>
                                            <td class="px-3 py-2 border-r">Information Technology</td>
                                            <td class="px-3 py-2 border-r">তথ্য প্রযুক্তি</td>
                                            <td class="px-3 py-2 border-r">information-technology</td>
                                            <td class="px-3 py-2 border-r">IT related jobs</td>
                                            <td class="px-3 py-2 border-r">0</td>
                                            <td class="px-3 py-2">1</td>
                                        </tr>
                                        <tr class="border-t bg-blue-50 dark:bg-blue-900/30">
                                            <td class="px-3 py-2 border-r"><strong>information-technology</strong></td>
                                            <td class="px-3 py-2 border-r">Software Development</td>
                                            <td class="px-3 py-2 border-r">সফটওয়্যার উন্নয়ন</td>
                                            <td class="px-3 py-2 border-r">software-development</td>
                                            <td class="px-3 py-2 border-r">Software engineering jobs</td>
                                            <td class="px-3 py-2 border-r">1</td>
                                            <td class="px-3 py-2">1</td>
                                        </tr>
                                        <tr class="border-t bg-blue-50 dark:bg-blue-900/30">
                                            <td class="px-3 py-2 border-r"><strong>information-technology</strong></td>
                                            <td class="px-3 py-2 border-r">Network Administration</td>
                                            <td class="px-3 py-2 border-r">নেটওয়ার্ক প্রশাসন</td>
                                            <td class="px-3 py-2 border-r">network-administration</td>
                                            <td class="px-3 py-2 border-r">Network management jobs</td>
                                            <td class="px-3 py-2 border-r">2</td>
                                            <td class="px-3 py-2">1</td>
                                        </tr>
                                        <tr class="border-t">
                                            <td class="px-3 py-2 border-r"><em class="text-gray-500">(empty)</em></td>
                                            <td class="px-3 py-2 border-r">Healthcare</td>
                                            <td class="px-3 py-2 border-r">স্বাস্থ্যসেবা</td>
                                            <td class="px-3 py-2 border-r">healthcare</td>
                                            <td class="px-3 py-2 border-r">Medical and healthcare jobs</td>
                                            <td class="px-3 py-2 border-r">0</td>
                                            <td class="px-3 py-2">1</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-2 text-xs italic">Note: Rows with parent_code (highlighted) create child categories under the specified parent.</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upload CSV File</h2>

                    <form @submit.prevent="submitUpload">
                        <div class="space-y-4">
                            <div>
                                <label for="csv_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select CSV File <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="csv_file"
                                    type="file"
                                    accept=".csv"
                                    @change="handleFileSelect"
                                    class="block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none"
                                    required
                                />
                                <p v-if="uploadForm.errors.csv_file" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ uploadForm.errors.csv_file }}</p>
                            </div>

                            <div class="flex items-center space-x-4">
                                <button
                                    type="submit"
                                    :disabled="uploadForm.processing || !selectedFile"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ uploadForm.processing ? 'Uploading...' : 'Upload & Import' }}
                                </button>

                                <a
                                    :href="route('admin.data.job-categories.template')"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                                >
                                    📥 Download CSV Template
                                </a>

                                <a
                                    :href="route('admin.data.job-categories.export')"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                                >
                                    📤 Export Current Data
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Upload Results -->
                <div v-if="uploadResult" class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upload Results</h2>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-700 dark:text-green-400">{{ uploadResult.success }}</div>
                            <div class="text-sm text-green-600 dark:text-green-300">Successfully Imported</div>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="text-2xl font-bold text-red-700 dark:text-red-400">{{ uploadResult.failed }}</div>
                            <div class="text-sm text-red-600 dark:text-red-300">Failed</div>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ uploadResult.total }}</div>
                            <div class="text-sm text-blue-600 dark:text-blue-300">Total Processed</div>
                        </div>
                    </div>

                    <div v-if="uploadResult.errors && uploadResult.errors.length > 0" class="mt-4">
                        <h3 class="text-lg font-semibold text-red-700 dark:text-red-400 mb-2">Errors</h3>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 max-h-64 overflow-y-auto">
                            <ul class="list-disc ml-5 text-sm text-red-700 dark:text-red-300 space-y-1">
                                <li v-for="(error, index) in uploadResult.errors" :key="index">{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const selectedFile = ref(null);
const uploadResult = ref(null);

const uploadForm = useForm({
    csv_file: null,
});

const handleFileSelect = (event) => {
    selectedFile.value = event.target.files[0];
    uploadForm.csv_file = event.target.files[0];
};

const submitUpload = () => {
    uploadForm.post(route('admin.data.job-categories.process-upload'), {
        onSuccess: (response) => {
            uploadResult.value = response.props.result || response.props.flash?.result;
            selectedFile.value = null;
            uploadForm.reset();
            document.getElementById('csv_file').value = '';
        },
        onError: () => {
            uploadResult.value = null;
        },
    });
};
</script>
