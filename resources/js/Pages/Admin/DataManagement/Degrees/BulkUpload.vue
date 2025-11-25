<template>
  <Head title="Bulk Upload Degrees" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Bulk Upload Degrees</h1>
        <p class="mt-1 text-sm text-gray-600">Upload multiple degrees using CSV file</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Upload CSV File</h2>
          
          <form @submit.prevent="submit">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Select CSV File <span class="text-red-500">*</span>
              </label>
              <input type="file" @change="handleFileChange" accept=".csv" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"/>
              <p v-if="form.errors.file" class="mt-1 text-sm text-red-600">{{ form.errors.file }}</p>
            </div>

            <div class="flex gap-3">
              <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                {{ form.processing ? 'Uploading...' : 'Upload & Process' }}
              </button>
              <Link :href="route('admin.data.degrees.index')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                Cancel
              </Link>
            </div>
          </form>

          <!-- Template Download -->
          <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Download Templates</h3>
            <div class="flex gap-2">
              <a :href="route('admin.data.degrees.template')" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Empty Template
              </a>
              <a :href="route('admin.data.degrees.template', { with_sample: 1 })" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Template with Sample Data
              </a>
            </div>
          </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">CSV Format Instructions</h2>
          
          <div class="space-y-4 text-sm">
            <div>
              <h3 class="font-medium text-gray-900 mb-2">Required Columns:</h3>
              <ul class="list-disc list-inside text-gray-600 space-y-1">
                <li><strong>name</strong> - Degree name (must be unique)</li>
                <li><strong>short_name</strong> - Abbreviated form (e.g., BSc, MSc)</li>
                <li><strong>level</strong> - Certificate/Diploma/Undergraduate/Graduate/Postgraduate</li>
              </ul>
            </div>

            <div>
              <h3 class="font-medium text-gray-900 mb-2">Optional Columns:</h3>
              <ul class="list-disc list-inside text-gray-600 space-y-1">
                <li><strong>name_bn</strong> - Bengali translation</li>
                <li><strong>typical_duration_years</strong> - Number (1-10)</li>
                <li><strong>is_active</strong> - 1 (active) or 0 (inactive), default: 1</li>
              </ul>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
              <h3 class="font-medium text-blue-900 mb-2">Important Notes:</h3>
              <ul class="list-disc list-inside text-blue-800 text-xs space-y-1">
                <li>Degree names must be unique across the system</li>
                <li>Use exact level values: Certificate, Diploma, Undergraduate, Graduate, Postgraduate</li>
                <li>Duration must be between 1-10 years (can be left empty)</li>
                <li>All text fields are case-sensitive</li>
                <li>Duplicate names in the CSV will be skipped</li>
              </ul>
            </div>

            <div>
              <h3 class="font-medium text-gray-900 mb-2">Example CSV:</h3>
              <pre class="bg-gray-50 border border-gray-200 rounded p-2 text-xs overflow-x-auto">name,short_name,level,typical_duration_years,is_active
Bachelor of Science,BSc,Undergraduate,4,1
Master of Science,MSc,Graduate,2,1
Doctor of Philosophy,PhD,Postgraduate,5,1</pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="mt-6">
        <div v-if="$page.props.flash.success" class="bg-green-50 border border-green-200 rounded-lg p-4">
          <h3 class="text-sm font-medium text-green-900 mb-2">Upload Successful</h3>
          <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
        </div>

        <div v-if="$page.props.flash.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <h3 class="text-sm font-medium text-red-900 mb-2">Upload Failed</h3>
          <p class="text-sm text-red-800">{{ $page.props.flash.error }}</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
  file: null,
});

const handleFileChange = (event) => {
  form.file = event.target.files[0];
};

const submit = () => {
  form.post(route('admin.data.degrees.process-upload'), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>
