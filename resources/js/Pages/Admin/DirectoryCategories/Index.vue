<template>
    <AdminLayout>
        <Head title="Directory Categories" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Directory Categories</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage directory categories for embassies, airlines, training centers, etc.</p>
                    </div>
                    <Link
                        :href="route('admin.directory-categories.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Category
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input
                                v-model="filters.search"
                                @input="debouncedSearch"
                                type="text"
                                placeholder="Search categories..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="filters.status"
                                @change="filterCategories"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select
                                v-model="filters.sort"
                                @change="filterCategories"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="sort_order">Sort Order</option>
                                <option value="name">Name (A-Z)</option>
                                <option value="created_at">Newest First</option>
                                <option value="directories_count">Most Directories</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div v-if="categories.data.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Directories
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ category.sort_order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                v-if="category.icon"
                                                class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-white"
                                                :style="{ backgroundColor: category.color || '#3B82F6' }"
                                            >
                                                <i :class="category.icon" class="text-xl"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ category.name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ category.name_bn }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ category.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ category.directories_count || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="toggleStatus(category)"
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer transition-colors',
                                                category.is_active
                                                    ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                                            ]"
                                        >
                                            {{ category.is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link
                                                :href="route('admin.directory-categories.edit', category.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </Link>
                                            <button
                                                @click="confirmDelete(category)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No categories found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new directory category.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.directory-categories.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Category
                            </Link>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="categories.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="categories.prev_page_url"
                                    :href="categories.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="categories.next_page_url"
                                    :href="categories.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ categories.from }}</span>
                                        to
                                        <span class="font-medium">{{ categories.to }}</span>
                                        of
                                        <span class="font-medium">{{ categories.total }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <Link
                                            v-if="categories.prev_page_url"
                                            :href="categories.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </Link>
                                        <Link
                                            v-if="categories.next_page_url"
                                            :href="categories.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="deleteModal.show" @close="closeDeleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Category</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to delete "{{ deleteModal.category?.name }}"?
                    <span v-if="deleteModal.category?.directories_count > 0" class="block mt-2 text-red-600 font-medium">
                        Warning: This category has {{ deleteModal.category.directories_count }} directories. You cannot delete it.
                    </span>
                    <span v-else class="block mt-2">
                        This action cannot be undone.
                    </span>
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="closeDeleteModal"
                        type="button"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        v-if="deleteModal.category?.directories_count === 0"
                        @click="deleteCategory"
                        type="button"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    categories: Object,
    filters: Object
});

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    sort: props.filters?.sort || 'sort_order'
});

const deleteModal = reactive({
    show: false,
    category: null
});

let searchTimeout = null;

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        filterCategories();
    }, 300);
};

const filterCategories = () => {
    router.get(route('admin.directory-categories.index'), filters, {
        preserveState: true,
        preserveScroll: true
    });
};

const toggleStatus = (category) => {
    router.post(
        route('admin.directory-categories.toggle-active', category.id),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Category will be updated automatically by Inertia
            }
        }
    );
};

const confirmDelete = (category) => {
    deleteModal.category = category;
    deleteModal.show = true;
};

const closeDeleteModal = () => {
    deleteModal.show = false;
    deleteModal.category = null;
};

const deleteCategory = () => {
    router.delete(route('admin.directory-categories.destroy', deleteModal.category.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
        }
    });
};
</script>
