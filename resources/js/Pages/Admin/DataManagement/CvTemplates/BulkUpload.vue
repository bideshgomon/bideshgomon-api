<template>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('admin.data.cv-templates.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 flex items-center gap-1 mb-4">
                    <ChevronLeftIcon class="h-4 w-4" />
                    Back to CV Templates
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Bulk Upload CV Templates</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Upload multiple CV templates at once using a CSV file.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upload Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload CSV File</h2>
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- File Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Select CSV File <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500">
                                    <div class="space-y-1 text-center">
                                        <DocumentArrowUpIcon class="mx-auto h-12 w-12 text-gray-400" />
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500">
                                                <span>Upload a file</span>
                                                <input
                                                    id="file-upload"
                                                    type="file"
                                                    accept=".csv"
                                                    @change="handleFileChange"
                                                    class="sr-only"
                                                    required
                                                />
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">CSV file up to 10MB</p>
                                        <p v-if="form.file" class="text-sm text-gray-900 dark:text-white font-medium">{{ form.file.name }}</p>
                                    </div>
                                </div>
                                <p v-if="form.errors.file" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.file }}</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-3">
                                <Link :href="route('admin.data.cv-templates.index')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.file"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                >
                                    <ArrowUpTrayIcon class="-ml-1 mr-2 h-5 w-5" />
                                    <span v-if="form.processing">Uploading...</span>
                                    <span v-else>Upload CSV</span>
                                </button>
                            </div>
                        </form>

                        <!-- Results -->
                        <div v-if="uploadResults" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload Results</h3>
                            
                            <div class="space-y-3">
                                <div v-if="uploadResults.success > 0" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <CheckCircleIcon class="h-5 w-5 text-green-600 dark:text-green-400" />
                                        <span class="text-sm font-medium text-green-900 dark:text-green-300">
                                            Successfully imported {{ uploadResults.success }} template(s)
                                        </span>
                                    </div>
                                </div>

                                <div v-if="uploadResults.failed > 0" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                    <div class="flex items-start gap-2">
                                        <XCircleIcon class="h-5 w-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-red-900 dark:text-red-300 mb-2">
                                                {{ uploadResults.failed }} template(s) failed to import
                                            </p>
                                            <ul v-if="uploadResults.errors.length > 0" class="text-sm text-red-700 dark:text-red-400 space-y-1 list-disc list-inside">
                                                <li v-for="(error, index) in uploadResults.errors" :key="index">{{ error }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="space-y-6">
                    <!-- Download Templates -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Download Templates</h2>
                        <div class="space-y-3">
                            <a :href="route('admin.data.cv-templates.template')" class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <DocumentIcon class="h-5 w-5 text-gray-400" />
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Empty Template</span>
                                </div>
                                <ArrowDownTrayIcon class="h-5 w-5 text-gray-400" />
                            </a>
                            <a :href="route('admin.data.cv-templates.template', { sample: 1 })" class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-400" />
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Sample Data</span>
                                </div>
                                <ArrowDownTrayIcon class="h-5 w-5 text-gray-400" />
                            </a>
                            <a :href="route('admin.data.cv-templates.export')" class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-3">
                                    <TableCellsIcon class="h-5 w-5 text-gray-400" />
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Export All</span>
                                </div>
                                <ArrowDownTrayIcon class="h-5 w-5 text-gray-400" />
                            </a>
                        </div>
                    </div>

                    <!-- CSV Format Instructions -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">CSV Format</h2>
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white mb-1">Required Columns:</h3>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">name</code> - Template name (unique)</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">category</code> - professional, creative, modern, traditional, or academic</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">price</code> - Price in BDT (0 for free)</li>
                                </ul>
                            </div>

                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white mb-1">Optional Columns:</h3>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">slug</code> - Auto-generated if empty</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">description</code> - Template description</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">thumbnail</code> - Image URL/path</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">is_premium</code> - 1 or 0 (inferred from price if empty)</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">color_scheme</code> - JSON object</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">sections</code> - JSON array</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">sort_order</code> - Display order</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">is_active</code> - 1 or 0</li>
                                </ul>
                            </div>

                            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="font-medium text-gray-900 dark:text-white mb-2">JSON Format Examples:</h3>
                                <div class="space-y-2">
                                    <div>
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Color Scheme:</p>
                                        <code class="block text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded mt-1 overflow-x-auto">
                                            {"primary":"#2563EB","secondary":"#64748B"}
                                        </code>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Sections:</p>
                                        <code class="block text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded mt-1 overflow-x-auto">
                                            ["personal_info","education","experience","skills"]
                                        </code>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="font-medium text-gray-900 dark:text-white mb-2">Premium vs Free:</h3>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li>Free templates: Set <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">price</code> to 0</li>
                                    <li>Premium templates: Set <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">price</code> to amount in BDT</li>
                                    <li><code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">is_premium</code> is auto-determined from price</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    ChevronLeftIcon,
    DocumentArrowUpIcon,
    ArrowUpTrayIcon,
    ArrowDownTrayIcon,
    DocumentIcon,
    DocumentTextIcon,
    TableCellsIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()
const uploadResults = ref(page.props.uploadResults || null)

const form = useForm({
    file: null,
})

const handleFileChange = (event) => {
    form.file = event.target.files[0]
}

const submit = () => {
    form.post(route('admin.data.cv-templates.process-upload'), {
        onSuccess: () => {
            uploadResults.value = page.props.uploadResults
            form.reset()
        },
    })
}
</script>
