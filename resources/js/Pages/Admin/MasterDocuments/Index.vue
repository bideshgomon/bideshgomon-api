<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    MagnifyingGlassIcon, 
    PlusIcon, 
    PencilIcon, 
    TrashIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    FunnelIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    documents: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');

const searchDocuments = () => {
    router.get(route('admin.master-documents.index'), {
        search: search.value,
        category: selectedCategory.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteDocument = (id) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('admin.master-documents.destroy', id));
    }
};

const getCategoryBadgeColor = (categoryName) => {
    const colors = {
        'Identity Documents': 'bg-blue-100 text-blue-800',
        'Financial Documents': 'bg-green-100 text-green-800',
        'Employment Documents': 'bg-purple-100 text-purple-800',
        'Business Documents': 'bg-orange-100 text-orange-800',
        'Educational Documents': 'bg-pink-100 text-pink-800',
        'Travel Documents': 'bg-indigo-100 text-indigo-800',
        'Supporting Documents': 'bg-yellow-100 text-yellow-800',
        'Medical Documents': 'bg-red-100 text-red-800',
    };
    return colors[categoryName] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Master Documents" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Master Documents Library</h1>
                        <p class="mt-1 text-sm text-gray-600">International standard visa document templates</p>
                    </div>
                    <Link
                        :href="route('admin.master-documents.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Add Document
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <input
                                    v-model="search"
                                    @keyup.enter="searchDocuments"
                                    type="text"
                                    placeholder="Search documents..."
                                    class="pl-10 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select
                                v-model="selectedCategory"
                                @change="searchDocuments"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                @click="searchDocuments"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 flex items-center justify-center"
                            >
                                <FunnelIcon class="w-4 h-4 mr-2" />
                                Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Document Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Standard
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requirements
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="document in documents.data" :key="document.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <DocumentTextIcon class="w-5 h-5 text-gray-400 mr-3" />
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ document.document_name }}</div>
                                            <div v-if="document.description" class="text-xs text-gray-500 mt-1">
                                                {{ document.description.substring(0, 60) }}{{ document.description.length > 60 ? '...' : '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="getCategoryBadgeColor(document.category.name)"
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ document.category.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="document.international_standard" class="text-sm text-gray-600 font-mono">
                                        {{ document.international_standard }}
                                    </span>
                                    <span v-else class="text-sm text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <span 
                                            v-if="document.translation_required"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800"
                                            title="Translation Required"
                                        >
                                            🌐 Translation
                                        </span>
                                        <span 
                                            v-if="document.notarization_required"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-800"
                                            title="Notarization Required"
                                        >
                                            ⚖️ Notarization
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="document.is_active" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <CheckCircleIcon class="w-4 h-4 mr-1" />
                                        Active
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <XCircleIcon class="w-4 h-4 mr-1" />
                                        Inactive
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link
                                            :href="route('admin.master-documents.show', document.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            title="View"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="route('admin.master-documents.edit', document.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Edit"
                                        >
                                            <PencilIcon class="w-4 h-4" />
                                        </Link>
                                        <button
                                            @click="deleteDocument(document.id)"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                        >
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="documents.links && documents.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ documents.from }} to {{ documents.to }} of {{ documents.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <component
                                    :is="link.url ? Link : 'span'"
                                    v-for="(link, index) in documents.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-md',
                                        link.active
                                            ? 'bg-indigo-600 text-white'
                                            : link.url 
                                                ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 cursor-pointer'
                                                : 'bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600">Total Documents</div>
                        <div class="text-2xl font-bold text-gray-900">{{ documents.total }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600">Categories</div>
                        <div class="text-2xl font-bold text-gray-900">{{ categories.length }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600">Translation Required</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ documents.data.filter(d => d.translation_required).length }}
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600">Notarization Required</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ documents.data.filter(d => d.notarization_required).length }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
