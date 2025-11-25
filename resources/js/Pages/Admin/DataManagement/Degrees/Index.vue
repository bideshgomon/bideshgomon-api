<template>
  <Head title="Degrees Management" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Degrees Management</h1>
        <p class="mt-1 text-sm text-gray-600">Manage educational degrees and qualifications</p>
      </div>

      <!-- Actions Bar -->
      <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <Link :href="route('admin.data.degrees.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Degree
        </Link>
        
        <Link :href="route('admin.data.degrees.bulk-upload')" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          Bulk Upload
        </Link>

        <a :href="route('admin.data.degrees.export', filters)" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Export CSV
        </a>
      </div>

      <!-- Filters -->
      <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input v-model="filters.search" type="text" placeholder="Search degrees..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @input="debounceSearch"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
            <select v-model="filters.level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @change="applyFilters">
              <option value="">All Levels</option>
              <option value="Undergraduate">Undergraduate</option>
              <option value="Graduate">Graduate</option>
              <option value="Postgraduate">Postgraduate</option>
              <option value="Diploma">Diploma</option>
              <option value="Certificate">Certificate</option>
            </select>
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
              <option value="level">Level</option>
              <option value="name">Name</option>
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Degree Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Short Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (Years)</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="degree in degrees.data" :key="degree.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ degree.name }}</div>
                <div v-if="degree.name_bn" class="text-sm text-gray-500">{{ degree.name_bn }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ degree.short_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="{
                  'bg-blue-100 text-blue-800': degree.level === 'Undergraduate',
                  'bg-purple-100 text-purple-800': degree.level === 'Graduate',
                  'bg-indigo-100 text-indigo-800': degree.level === 'Postgraduate',
                  'bg-green-100 text-green-800': degree.level === 'Diploma',
                  'bg-yellow-100 text-yellow-800': degree.level === 'Certificate',
                  'bg-gray-100 text-gray-800': !['Undergraduate', 'Graduate', 'Postgraduate', 'Diploma', 'Certificate'].includes(degree.level)
                }">
                  {{ degree.level }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ degree.typical_duration_years || 'N/A' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button @click="toggleStatus(degree)" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="degree.is_active ? 'bg-green-600' : 'bg-gray-300'">
                  <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="degree.is_active ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <Link :href="route('admin.data.degrees.edit', degree.id)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</Link>
                <button @click="confirmDelete(degree)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="degrees.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No degrees found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding a new degree.</p>
        </div>

        <!-- Pagination -->
        <div v-if="degrees.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ degrees.from }} to {{ degrees.to }} of {{ degrees.total }} results
            </div>
            <div class="flex gap-2">
              <template v-for="link in degrees.links" :key="link.label">
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
  degrees: Object,
  filters: Object,
});

const filters = reactive({
  search: props.filters.search || '',
  level: props.filters.level || '',
  status: props.filters.status || '',
  sort: props.filters.sort || 'level',
});

let searchTimeout;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => applyFilters(), 500);
};

const applyFilters = () => {
  router.get(route('admin.data.degrees.index'), filters, { 
    preserveState: true,
    preserveScroll: true 
  });
};

const toggleStatus = (degree) => {
  router.post(route('admin.data.degrees.toggle-status', degree.id), {}, {
    preserveScroll: true,
    onSuccess: () => {},
  });
};

const confirmDelete = (degree) => {
  if (confirm(`Are you sure you want to delete "${degree.name}"?`)) {
    router.delete(route('admin.data.degrees.destroy', degree.id), {
      preserveScroll: true,
    });
  }
};
</script>
