<template>
  <Head title="Email Templates" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Email Templates</h1>
          <p class="mt-1 text-sm text-gray-600">Manage email templates for notifications and campaigns</p>
        </div>
        <div class="flex gap-3">
          <Link :href="route('admin.data.email-templates.bulk-upload')" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Bulk Upload
          </Link>
          <Link :href="route('admin.data.email-templates.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Template
          </Link>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
          <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
              <input v-model="filters.search" @input="debouncedSearch" type="text" placeholder="Search templates..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"/>
            </div>
            <div class="w-40">
              <select v-model="filters.category" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Categories</option>
                <option value="general">General</option>
                <option value="transactional">Transactional</option>
                <option value="marketing">Marketing</option>
                <option value="notification">Notification</option>
              </select>
            </div>
            <div class="w-32">
              <select v-model="filters.is_active" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
            <div class="w-40">
              <select v-model="filters.sort" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="name">Name</option>
                <option value="category">Category</option>
                <option value="campaigns">Campaigns</option>
                <option value="created_at">Created Date</option>
              </select>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaigns</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="template in templates.data" :key="template.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ template.name }}</div>
                  <div class="text-sm text-gray-500">{{ template.slug }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900 max-w-xs truncate">{{ template.subject }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="{
                    'bg-blue-100 text-blue-800': template.category === 'general',
                    'bg-green-100 text-green-800': template.category === 'transactional',
                    'bg-purple-100 text-purple-800': template.category === 'marketing',
                    'bg-yellow-100 text-yellow-800': template.category === 'notification'
                  }">
                    {{ template.category }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                  {{ template.campaigns_count || 0 }}
                </td>
                <td class="px-6 py-4">
                  <button @click="toggleStatus(template)" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" :class="template.is_active ? 'bg-blue-600' : 'bg-gray-200'">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="template.is_active ? 'translate-x-6' : 'translate-x-1'"/>
                  </button>
                </td>
                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                  <Link :href="route('admin.data.email-templates.edit', template.id)" class="text-blue-600 hover:text-blue-900">Edit</Link>
                  <button @click="confirmDelete(template)" class="text-red-600 hover:text-red-900" :disabled="template.campaigns_count > 0" :class="{'opacity-50 cursor-not-allowed': template.campaigns_count > 0}">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="templates.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No email templates</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating a new template.</p>
        </div>

        <div v-if="templates.data.length > 0" class="px-6 py-4 border-t border-gray-200">
          <Pagination :links="templates.links" />
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
  templates: Object,
  filters: Object,
});

const filters = reactive({
  search: props.filters.search || '',
  category: props.filters.category || '',
  is_active: props.filters.is_active || '',
  sort: props.filters.sort || 'name',
  direction: props.filters.direction || 'asc',
});

let debounceTimeout = null;

const debouncedSearch = () => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(() => {
    applyFilters();
  }, 300);
};

const applyFilters = () => {
  router.get(route('admin.data.email-templates.index'), filters, {
    preserveState: true,
    preserveScroll: true,
  });
};

const toggleStatus = (template) => {
  router.post(route('admin.data.email-templates.toggle-status', template.id), {}, {
    preserveScroll: true,
  });
};

const confirmDelete = (template) => {
  if (template.campaigns_count > 0) {
    alert('Cannot delete template that is used in campaigns.');
    return;
  }
  
  if (confirm(`Are you sure you want to delete "${template.name}"?`)) {
    router.delete(route('admin.data.email-templates.destroy', template.id));
  }
};
</script>
