<template>
  <Head title="Blog Categories" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Blog Categories Management</h1>
        <p class="mt-1 text-sm text-gray-600">Manage blog categories for content organization</p>
      </div>

      <!-- Actions Bar -->
      <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <Link :href="route('admin.data.blog-categories.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Category
        </Link>
        
        <Link :href="route('admin.data.blog-categories.bulk-upload')" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          Bulk Upload
        </Link>

        <a :href="route('admin.data.blog-categories.export', filters)" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Export CSV
        </a>
      </div>

      <!-- Filters -->
      <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input v-model="filters.search" type="text" placeholder="Search categories..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @input="debounceSearch"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @change="applyFilters">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
            <select v-model="filters.sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @change="applyFilters">
              <option value="order">Display Order</option>
              <option value="name">Name</option>
              <option value="posts">Post Count</option>
              <option value="latest">Latest First</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
        {{ $page.props.flash.success }}
      </div>

      <!-- Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in blogCategories.data" :key="category.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.order }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div v-if="category.icon" class="mr-2 text-gray-500">
                    <span class="text-lg">{{ category.icon }}</span>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                    <div v-if="category.description" class="text-sm text-gray-500 truncate max-w-xs">{{ category.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.slug }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded" :style="{ backgroundColor: category.color }"></div>
                  <span class="text-xs text-gray-500">{{ category.color }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.posts_count || 0 }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button @click="toggleStatus(category)" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="category.is_active ? 'bg-green-600' : 'bg-gray-300'">
                  <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="category.is_active ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <Link :href="route('admin.data.blog-categories.edit', category.id)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</Link>
                <button @click="confirmDelete(category)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="blogCategories.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No blog categories found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding a new category.</p>
        </div>

        <!-- Pagination -->
        <div v-if="blogCategories.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ blogCategories.from }} to {{ blogCategories.to }} of {{ blogCategories.total }} results
            </div>
            <div class="flex gap-2">
              <template v-for="link in blogCategories.links" :key="link.label">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  v-html="link.label"
                  class="px-3 py-1 border rounded-md text-sm"
                  :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                />
                <span
                  v-else
                  v-html="link.label"
                  class="px-3 py-1 border rounded-md text-sm opacity-50 cursor-not-allowed"
                  :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300'"
                />
              </template>
            </div>
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

const props = defineProps({
  blogCategories: Object,
  filters: Object,
});

const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  sort: props.filters.sort || 'order',
});

let searchTimeout;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => applyFilters(), 500);
};

const applyFilters = () => {
  router.get(route('admin.data.blog-categories.index'), filters, { 
    preserveState: true,
    preserveScroll: true 
  });
};

const toggleStatus = (category) => {
  router.post(route('admin.data.blog-categories.toggle-status', category.id), {}, {
    preserveScroll: true,
    onSuccess: () => {},
  });
};

const confirmDelete = (category) => {
  if (category.posts_count > 0) {
    alert(`Cannot delete "${category.name}" because it has ${category.posts_count} blog post(s). Please remove or reassign them first.`);
    return;
  }
  
  if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
    router.delete(route('admin.data.blog-categories.destroy', category.id), {
      preserveScroll: true,
    });
  }
};
</script>
