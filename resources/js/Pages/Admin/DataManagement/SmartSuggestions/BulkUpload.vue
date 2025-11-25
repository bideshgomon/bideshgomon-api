<template>
  <AdminLayout>
    <Head title="Bulk Upload Smart Suggestions" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Upload Smart Suggestions</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Upload multiple suggestions via CSV file
                </p>
              </div>
              <Link
                :href="route('admin.data.smart-suggestions.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Back
              </Link>
            </div>

            <!-- Instructions -->
            <div class="mb-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
              <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">📋 Instructions</h3>
              <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1 list-disc list-inside">
                <li>Download the template CSV file and fill in your data</li>
                <li><strong>user_id</strong>: Must be a valid user ID from your database</li>
                <li><strong>priority</strong>: urgent, high, medium, or low</li>
                <li><strong>category</strong>: visa, profile, document, application, or assessment</li>
                <li><strong>data</strong>: Enter valid JSON format (optional)</li>
                <li><strong>expires_at</strong>: Use format YYYY-MM-DD HH:MM:SS (optional)</li>
                <li><strong>relevance_score</strong>: Number between 0 and 100 (default: 50)</li>
              </ul>
            </div>

            <!-- Download Templates -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">1. Download Template</h3>
              <div class="flex gap-4">
                <a
                  :href="route('admin.data.smart-suggestions.template')"
                  class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Download Empty Template
                </a>
                <a
                  :href="route('admin.data.smart-suggestions.template') + '?sample=true'"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                  Download Sample Data
                </a>
              </div>
            </div>

            <!-- Upload Form -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">2. Upload CSV File</h3>
              <form @submit.prevent="submit" class="space-y-4">
                <div>
                  <input
                    type="file"
                    @change="handleFileChange"
                    accept=".csv"
                    class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none"
                    required
                  />
                  <p v-if="form.errors.file" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.file }}</p>
                </div>

                <div>
                  <button
                    type="submit"
                    :disabled="form.processing || !form.file"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                  >
                    {{ form.processing ? 'Processing...' : 'Upload and Process' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- CSV Format Guide -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">📝 CSV Format Guide</h3>
              
              <div class="space-y-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Fields:</h4>
                  <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                    <li><code>user_id</code> - User database ID</li>
                    <li><code>suggestion_type</code> - Type identifier (e.g., profile_completion)</li>
                    <li><code>category</code> - visa, profile, document, application, assessment</li>
                    <li><code>priority</code> - urgent, high, medium, low</li>
                    <li><code>title</code> - Brief title</li>
                    <li><code>description</code> - Detailed description</li>
                  </ul>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Optional Fields:</h4>
                  <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                    <li><code>data</code> - JSON formatted additional data</li>
                    <li><code>action_type</code> - Action type (navigate, external, etc.)</li>
                    <li><code>action_url</code> - URL or route path</li>
                    <li><code>expires_at</code> - Expiration datetime (YYYY-MM-DD HH:MM:SS)</li>
                    <li><code>relevance_score</code> - 0-100 (default: 50)</li>
                  </ul>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Examples:</h4>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">🛂 visa</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">👤 profile</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">📄 document</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">📝 application</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">⭐ assessment</div>
                  </div>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority Levels:</h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div class="p-2 bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-200 rounded">🔴 urgent</div>
                    <div class="p-2 bg-orange-50 dark:bg-orange-900 text-orange-700 dark:text-orange-200 rounded">🟠 high</div>
                    <div class="p-2 bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 rounded">🟡 medium</div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded">🔵 low</div>
                  </div>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">JSON Data Example:</h4>
                  <pre class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-xs overflow-x-auto"><code>{"completion_percentage":60,"missing_fields":["education","work_experience"]}</code></pre>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Example Rows:</h4>
                  <div class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-xs overflow-x-auto">
                    <table class="min-w-full">
                      <thead>
                        <tr class="border-b border-gray-300 dark:border-gray-700">
                          <th class="text-left py-1 px-2">user_id</th>
                          <th class="text-left py-1 px-2">suggestion_type</th>
                          <th class="text-left py-1 px-2">category</th>
                          <th class="text-left py-1 px-2">priority</th>
                          <th class="text-left py-1 px-2">title</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="py-1 px-2">1</td>
                          <td class="py-1 px-2">profile_completion</td>
                          <td class="py-1 px-2">profile</td>
                          <td class="py-1 px-2">high</td>
                          <td class="py-1 px-2">Complete Your Profile</td>
                        </tr>
                        <tr>
                          <td class="py-1 px-2">1</td>
                          <td class="py-1 px-2">document_upload</td>
                          <td class="py-1 px-2">document</td>
                          <td class="py-1 px-2">urgent</td>
                          <td class="py-1 px-2">Upload Passport Copy</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = useForm({
  file: null,
})

const handleFileChange = (event) => {
  form.file = event.target.files[0]
}

const submit = () => {
  form.post(route('admin.data.smart-suggestions.process-upload'), {
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>
