<template>
  <Head title="Add Degree" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Add New Degree</h1>
        <p class="mt-1 text-sm text-gray-600">Create a new educational degree</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form @submit.prevent="submit">
          <!-- Name (English) -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Degree Name (English) <span class="text-red-500">*</span>
            </label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.name ? 'border-red-500' : 'border-gray-300'" placeholder="e.g., Bachelor of Science"/>
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Name (Bengali) -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Degree Name (Bengali)
            </label>
            <input v-model="form.name_bn" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., ব্যাচেলর অফ সায়েন্স"/>
            <p v-if="form.errors.name_bn" class="mt-1 text-sm text-red-600">{{ form.errors.name_bn }}</p>
          </div>

          <!-- Short Name -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Short Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.short_name" type="text" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.short_name ? 'border-red-500' : 'border-gray-300'" placeholder="e.g., BSc"/>
            <p v-if="form.errors.short_name" class="mt-1 text-sm text-red-600">{{ form.errors.short_name }}</p>
          </div>

          <!-- Level -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Level <span class="text-red-500">*</span>
            </label>
            <select v-model="form.level" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.level ? 'border-red-500' : 'border-gray-300'">
              <option value="">Select Level</option>
              <option value="Certificate">Certificate</option>
              <option value="Diploma">Diploma</option>
              <option value="Undergraduate">Undergraduate</option>
              <option value="Graduate">Graduate</option>
              <option value="Postgraduate">Postgraduate</option>
            </select>
            <p v-if="form.errors.level" class="mt-1 text-sm text-red-600">{{ form.errors.level }}</p>
          </div>

          <!-- Typical Duration -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Typical Duration (Years)
            </label>
            <input v-model.number="form.typical_duration_years" type="number" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 4"/>
            <p class="mt-1 text-xs text-gray-500">Leave empty if duration varies</p>
            <p v-if="form.errors.typical_duration_years" class="mt-1 text-sm text-red-600">{{ form.errors.typical_duration_years }}</p>
          </div>

          <!-- Status -->
          <div class="mb-6">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"/>
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3">
            <Link :href="route('admin.data.degrees.index')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
              {{ form.processing ? 'Creating...' : 'Create Degree' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
  name: '',
  name_bn: '',
  short_name: '',
  level: '',
  typical_duration_years: null,
  is_active: true,
});

const submit = () => {
  form.post(route('admin.data.degrees.store'));
};
</script>
