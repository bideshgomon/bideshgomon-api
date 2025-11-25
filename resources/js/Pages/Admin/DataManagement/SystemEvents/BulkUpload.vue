<template>
  <AdminLayout>
    <Head title="Bulk Upload System Events" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Upload System Events</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Upload multiple events via CSV file
                </p>
              </div>
              <Link
                :href="route('admin.data.system-events.index')"
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
                <li><strong>event_type</strong>: Required - use dot notation (e.g., user.login)</li>
                <li><strong>user_id</strong>: Optional - must be a valid user ID</li>
                <li><strong>target_type</strong>: Optional - entity type (e.g., Job, Payment)</li>
                <li><strong>target_id</strong>: Optional - entity ID number</li>
                <li><strong>data</strong>: Optional - valid JSON format</li>
              </ul>
            </div>

            <!-- Download Templates -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">1. Download Template</h3>
              <div class="flex gap-4">
                <a
                  :href="route('admin.data.system-events.template')"
                  class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Download Empty Template
                </a>
                <a
                  :href="route('admin.data.system-events.template') + '?sample=true'"
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
                    <li><code>event_type</code> - Event type in dot notation</li>
                  </ul>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Optional Fields:</h4>
                  <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                    <li><code>user_id</code> - User database ID</li>
                    <li><code>target_type</code> - Entity type (Job, Payment, Document, etc.)</li>
                    <li><code>target_id</code> - Entity ID number</li>
                    <li><code>data</code> - JSON formatted additional data</li>
                  </ul>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Type Examples:</h4>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">user.login</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">user.logout</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">job.created</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">job.updated</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">payment.processed</div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">document.verified</div>
                  </div>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">JSON Data Example:</h4>
                  <pre class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-xs overflow-x-auto"><code>{"ip":"192.168.1.1","user_agent":"Mozilla/5.0","action":"profile_update"}</code></pre>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Example Rows:</h4>
                  <div class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-xs overflow-x-auto">
                    <table class="min-w-full">
                      <thead>
                        <tr class="border-b border-gray-300 dark:border-gray-700">
                          <th class="text-left py-1 px-2">event_type</th>
                          <th class="text-left py-1 px-2">user_id</th>
                          <th class="text-left py-1 px-2">target_type</th>
                          <th class="text-left py-1 px-2">target_id</th>
                          <th class="text-left py-1 px-2">data</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="py-1 px-2">user.login</td>
                          <td class="py-1 px-2">1</td>
                          <td class="py-1 px-2"></td>
                          <td class="py-1 px-2"></td>
                          <td class="py-1 px-2">{"ip":"192.168.1.1"}</td>
                        </tr>
                        <tr>
                          <td class="py-1 px-2">job.created</td>
                          <td class="py-1 px-2">2</td>
                          <td class="py-1 px-2">Job</td>
                          <td class="py-1 px-2">123</td>
                          <td class="py-1 px-2">{"title":"Software Engineer"}</td>
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
  form.post(route('admin.data.system-events.process-upload'), {
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>
