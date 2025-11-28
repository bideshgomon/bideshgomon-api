<template>
    <AdminLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Header -->
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Create New Document</h2>
                                <p class="mt-1 text-sm text-gray-600">Add a new document to the master library</p>
                            </div>
                            <Link :href="route('admin.master-documents.index')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Back to Documents
                                </span>
                            </Link>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <!-- Document Name -->
                                <div>
                                    <label for="document_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Document Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.document_name"
                                        type="text"
                                        id="document_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    />
                                    <p v-if="form.errors.document_name" class="mt-1 text-sm text-red-600">{{ form.errors.document_name }}</p>
                                </div>

                                <!-- Category -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.category_id"
                                        id="category_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    >
                                        <option value="">Select Category</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        id="description"
                                        rows="3"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    ></textarea>
                                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                                </div>

                                <!-- Specifications -->
                                <div>
                                    <label for="specifications" class="block text-sm font-medium text-gray-700 mb-2">
                                        Specifications
                                    </label>
                                    <textarea
                                        v-model="form.specifications"
                                        id="specifications"
                                        rows="5"
                                        placeholder="Enter detailed requirements, format, size, etc."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    ></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Detailed requirements for this document type</p>
                                    <p v-if="form.errors.specifications" class="mt-1 text-sm text-red-600">{{ form.errors.specifications }}</p>
                                </div>

                                <!-- International Standard -->
                                <div>
                                    <label for="international_standard" class="block text-sm font-medium text-gray-700 mb-2">
                                        International Standard
                                    </label>
                                    <input
                                        v-model="form.international_standard"
                                        type="text"
                                        id="international_standard"
                                        placeholder="e.g., ICAO Doc 9303, ISO/IEC 7810, WHO IHR"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">e.g., ICAO, ISO, WHO, UN</p>
                                    <p v-if="form.errors.international_standard" class="mt-1 text-sm text-red-600">{{ form.errors.international_standard }}</p>
                                </div>

                                <!-- Requirements Checkboxes -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <input
                                            v-model="form.translation_required"
                                            type="checkbox"
                                            id="translation_required"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label for="translation_required" class="ml-2 block text-sm text-gray-700">
                                            Translation Required
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input
                                            v-model="form.notarization_required"
                                            type="checkbox"
                                            id="notarization_required"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label for="notarization_required" class="ml-2 block text-sm text-gray-700">
                                            Notarization Required
                                        </label>
                                    </div>
                                </div>

                                <!-- Typical Validity Days -->
                                <div>
                                    <label for="typical_validity_days" class="block text-sm font-medium text-gray-700 mb-2">
                                        Typical Validity (Days)
                                    </label>
                                    <input
                                        v-model.number="form.typical_validity_days"
                                        type="number"
                                        id="typical_validity_days"
                                        placeholder="e.g., 90, 180, 3650"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">How long this document remains valid</p>
                                    <p v-if="form.errors.typical_validity_days" class="mt-1 text-sm text-red-600">{{ form.errors.typical_validity_days }}</p>
                                </div>

                                <!-- Example URL -->
                                <div>
                                    <label for="example_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        Example/Reference URL
                                    </label>
                                    <input
                                        v-model="form.example_url"
                                        type="url"
                                        id="example_url"
                                        placeholder="https://"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Link to sample document or official guidelines</p>
                                    <p v-if="form.errors.example_url" class="mt-1 text-sm text-red-600">{{ form.errors.example_url }}</p>
                                </div>

                                <!-- Sort Order & Active Status -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                            Sort Order
                                        </label>
                                        <input
                                            v-model.number="form.sort_order"
                                            type="number"
                                            id="sort_order"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        />
                                    </div>

                                    <div class="flex items-center pt-7">
                                        <input
                                            v-model="form.is_active"
                                            type="checkbox"
                                            id="is_active"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                                <Link :href="route('admin.master-documents.index')" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Document</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    category_id: '',
    document_name: '',
    description: '',
    specifications: '',
    translation_required: false,
    notarization_required: false,
    typical_validity_days: null,
    international_standard: '',
    example_url: '',
    sort_order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.master-documents.store'));
};
</script>
