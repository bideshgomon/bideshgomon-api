<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    pages: Object,
    filters: Object,
    pageTypes: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedType = ref(props.filters.type || '');
const selectedStatus = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('admin.pages.index'), {
        search: searchQuery.value,
        type: selectedType.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedType.value = '';
    selectedStatus.value = '';
    applyFilters();
};

const togglePublished = (page) => {
    if (confirm(`Are you sure you want to ${page.is_published ? 'unpublish' : 'publish'} this page?`)) {
        router.post(route('admin.pages.toggle-published', page.id));
    }
};

const duplicatePage = (page) => {
    if (confirm('Duplicate this page?')) {
        router.post(route('admin.pages.duplicate', page.id));
    }
};

const deletePage = (page) => {
    if (confirm('Are you sure you want to delete this page? This action cannot be undone.')) {
        router.delete(route('admin.pages.destroy', page.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="CMS Pages Management" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">CMS Pages Management</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage website pages, terms, privacy policy, and more</p>
                    </div>
                    <Link
                        :href="route('admin.pages.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        📄 Create New Page
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by title, content, or slug..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Page Type</label>
                            <select
                                v-model="selectedType"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                @change="applyFilters"
                            >
                                <option value="">All Types</option>
                                <option v-for="(label, value) in pageTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="selectedStatus"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                @change="applyFilters"
                            >
                                <option value="">All Pages</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button
                            @click="applyFilters"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Search
                        </button>
                        <button
                            @click="clearFilters"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Pages List -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div v-if="pages.data.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-4 text-gray-500">No pages found</p>
                        <Link
                            :href="route('admin.pages.create')"
                            class="mt-4 inline-block text-blue-600 hover:text-blue-700"
                        >
                            Create your first page
                        </Link>
                    </div>

                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="page in pages.data" :key="page.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ page.title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                /page/{{ page.slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ pageTypes[page.page_type] || page.page_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-semibold rounded-full',
                                        page.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                    ]">
                                        {{ page.is_published ? '✓ Published' : '○ Draft' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(page.updated_at) }}
                                    <span v-if="page.updated_by" class="block text-xs text-gray-400">
                                        by {{ page.updated_by.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('page.show', page.slug)"
                                            target="_blank"
                                            class="text-gray-600 hover:text-gray-900"
                                            title="View"
                                        >
                                            👁️
                                        </Link>
                                        <Link
                                            :href="route('admin.pages.edit', page.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Edit"
                                        >
                                            ✏️
                                        </Link>
                                        <button
                                            @click="togglePublished(page)"
                                            class="text-yellow-600 hover:text-yellow-900"
                                            :title="page.is_published ? 'Unpublish' : 'Publish'"
                                        >
                                            {{ page.is_published ? '📕' : '📗' }}
                                        </button>
                                        <button
                                            @click="duplicatePage(page)"
                                            class="text-green-600 hover:text-green-900"
                                            title="Duplicate"
                                        >
                                            📋
                                        </button>
                                        <button
                                            @click="deletePage(page)"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="pages.data.length > 0 && (pages.prev_page_url || pages.next_page_url)" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <Link
                                v-if="pages.prev_page_url"
                                :href="pages.prev_page_url"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Previous
                            </Link>
                            <span v-else class="px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                Previous
                            </span>

                            <span class="text-sm text-gray-600">
                                Page {{ pages.current_page }} of {{ pages.last_page }}
                            </span>

                            <Link
                                v-if="pages.next_page_url"
                                :href="pages.next_page_url"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Next
                            </Link>
                            <span v-else class="px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                Next
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
