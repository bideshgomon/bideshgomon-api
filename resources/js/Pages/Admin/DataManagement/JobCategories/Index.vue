<template>
    <AdminLayout>
        <Head title="Job Categories Management" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Job Categories Management</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage job categories with hierarchical structure</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <Link
                                :href="route('admin.data.job-categories.bulk-upload')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Bulk Upload
                            </Link>
                            <Link
                                :href="route('admin.data.job-categories.create')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Category
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search by name, slug..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                @input="debouncedFilter"
                            />
                        </div>

                        <!-- Parent Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Category</label>
                            <select
                                v-model="form.parent_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                @change="filter"
                            >
                                <option value="">All Categories</option>
                                <option value="root">Root Categories Only</option>
                                <option v-for="category in allCategories" :key="category.id" :value="category.id">
                                    {{ '—'.repeat(category.depth) }} {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select
                                v-model="form.is_active"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                @change="filter"
                            >
                                <option value="">All Status</option>
                                <option value="true">Active</option>
                                <option value="false">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            @click="resetFilters"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                        >
                            Reset Filters
                        </button>
                        <a
                            :href="route('admin.data.job-categories.export', form)"
                            class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th @click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <div class="flex items-center space-x-1">
                                            <span>Category Name</span>
                                            <svg v-if="sort.field === 'name'" class="w-4 h-4" :class="sort.direction === 'asc' ? '' : 'rotate-180'" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Parent Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Slug</th>
                                    <th @click="sortBy('order')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <div class="flex items-center space-x-1">
                                            <span>Order</span>
                                            <svg v-if="sort.field === 'order'" class="w-4 h-4" :class="sort.direction === 'asc' ? '' : 'rotate-180'" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="category in jobCategories.data" :key="category.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ category.name }}</div>
                                            <div v-if="category.name_bn" class="text-sm text-gray-500 dark:text-gray-400">{{ category.name_bn }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="category.parent" class="text-sm text-gray-900 dark:text-white">{{ category.parent.name }}</span>
                                        <span v-else class="text-sm text-gray-400 dark:text-gray-500">Root</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-xs font-mono rounded">{{ category.slug }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ category.order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="toggleStatus(category)"
                                            :class="category.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                            class="px-3 py-1 rounded-full text-xs font-semibold transition-colors hover:opacity-80"
                                        >
                                            {{ category.is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link
                                                :href="route('admin.data.job-categories.edit', category.id)"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteCategory(category)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <Pagination :links="jobCategories.links" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    jobCategories: Object,
    allCategories: Array,
    filters: Object,
    sort: Object,
});

const form = reactive({
    search: props.filters.search || '',
    parent_id: props.filters.parent_id || '',
    is_active: props.filters.is_active || '',
});

let debounceTimer = null;

const filter = () => {
    router.get(route('admin.data.job-categories.index'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedFilter = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(filter, 300);
};

const sortBy = (field) => {
    const direction = props.sort.field === field && props.sort.direction === 'asc' ? 'desc' : 'asc';
    router.get(route('admin.data.job-categories.index'), {
        ...form,
        sort: field,
        direction,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.parent_id = '';
    form.is_active = '';
    filter();
};

const toggleStatus = (category) => {
    if (confirm(`Are you sure you want to ${category.is_active ? 'deactivate' : 'activate'} ${category.name}?`)) {
        router.post(route('admin.data.job-categories.toggle-status', category.id), {}, {
            preserveScroll: true,
        });
    }
};

const deleteCategory = (category) => {
    if (confirm(`Are you sure you want to delete ${category.name}? This action cannot be undone.`)) {
        router.delete(route('admin.data.job-categories.destroy', category.id), {
            preserveScroll: true,
        });
    }
};
</script>
