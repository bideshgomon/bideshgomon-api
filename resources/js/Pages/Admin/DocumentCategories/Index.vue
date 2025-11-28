<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon,
    PencilIcon,
    TrashIcon,
    FolderIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
});

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category? All documents in this category will also be deleted.')) {
        router.delete(route('admin.document-categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Document Categories" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Document Categories</h1>
                        <p class="mt-1 text-sm text-gray-600">Organize documents into logical categories</p>
                    </div>
                    <Link
                        :href="route('admin.document-categories.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Add Category
                    </Link>
                </div>

                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <FolderIcon class="w-6 h-6 text-indigo-600 mr-3" />
                                <h3 class="text-lg font-semibold text-gray-900">{{ category.name }}</h3>
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    :href="route('admin.document-categories.edit', category.id)"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Edit"
                                >
                                    <PencilIcon class="w-5 h-5" />
                                </Link>
                                <button
                                    @click="deleteCategory(category.id)"
                                    class="text-red-600 hover:text-red-900"
                                    title="Delete"
                                >
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <p v-if="category.description" class="text-sm text-gray-600 mb-4">
                            {{ category.description }}
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ category.documents_count }}</span> documents
                            </div>
                            <div class="text-sm text-gray-500">
                                Order: {{ category.sort_order }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-8 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600">Total Categories</div>
                            <div class="text-2xl font-bold text-gray-900">{{ categories.length }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600">Total Documents</div>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ categories.reduce((sum, cat) => sum + cat.documents_count, 0) }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600">Avg per Category</div>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ Math.round(categories.reduce((sum, cat) => sum + cat.documents_count, 0) / categories.length) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
