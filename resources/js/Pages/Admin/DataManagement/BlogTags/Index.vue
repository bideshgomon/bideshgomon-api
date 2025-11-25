<template>
  <Head title="Blog Tags" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Blog Tags Management</h1>
        <p class="mt-1 text-sm text-gray-600">Manage tags for blog content</p>
      </div>

      <!-- Actions Bar -->
      <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <Link :href="route('admin.data.blog-tags.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Tag
        </Link>
        
        <Link :href="route('admin.data.blog-tags.bulk-upload')" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          Bulk Upload
        </Link>

        <a :href="route('admin.data.blog-tags.export', filters)" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Export CSV
        </a>
      </div>

      <!-- Filters -->
      <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input v-model="filters.search" type="text" placeholder="Search tags..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @input="debounceSearch"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
            <select v-model="filters.sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @change="applyFilters">
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tag Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="tag in blogTags.data" :key="tag.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ tag.name }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tag.slug }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ tag.posts_count || 0 }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <Link :href="route('admin.data.blog-tags.edit', tag.id)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</Link>
                <button @click="confirmDelete(tag)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="blogTags.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No blog tags found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding a new tag.</p>
        </div>

        <!-- Pagination -->
        <div v-if="blogTags.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ blogTags.from }} to {{ blogTags.to }} of {{ blogTags.total }} results
            </div>
            <div class="flex gap-2">
              <template v-for="link in blogTags.links" :key="link.label">
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
  blogTags: Object,
  filters: Object,
});

const filters = reactive({
  search: props.filters.search || '',
  sort: props.filters.sort || 'name',
});

let searchTimeout;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => applyFilters(), 500);
};

const applyFilters = () => {
  router.get(route('admin.data.blog-tags.index'), filters, { 
    preserveState: true,
    preserveScroll: true 
  });
};

const confirmDelete = (tag) => {
  if (tag.posts_count > 0) {
    alert(`Cannot delete "${tag.name}" because it's used in ${tag.posts_count} blog post(s). Please remove it from posts first.`);
    return;
  }
  
  if (confirm(`Are you sure you want to delete "${tag.name}"?`)) {
    router.delete(route('admin.data.blog-tags.destroy', tag.id), {
      preserveScroll: true,
    });
  }
};
</script>
