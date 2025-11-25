<template>
  <AdminLayout>
    <Head title="Create Smart Suggestion" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Smart Suggestion</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Create a new intelligent user suggestion
                </p>
              </div>
              <Link
                :href="route('admin.data.smart-suggestions.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Back
              </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- User Selection -->
              <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  User <span class="text-red-500">*</span>
                </label>
                <select
                  id="user_id"
                  v-model="form.user_id"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="">Select a user</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }})
                  </option>
                </select>
                <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.user_id }}</p>
              </div>

              <!-- Category & Priority -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Category <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="category"
                    v-model="form.category"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  >
                    <option value="">Select category</option>
                    <option value="visa">🛂 Visa</option>
                    <option value="profile">👤 Profile</option>
                    <option value="document">📄 Document</option>
                    <option value="application">📝 Application</option>
                    <option value="assessment">⭐ Assessment</option>
                  </select>
                  <p v-if="form.errors.category" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.category }}</p>
                </div>

                <div>
                  <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Priority <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="priority"
                    v-model="form.priority"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  >
                    <option value="urgent">🔴 Urgent</option>
                    <option value="high">🟠 High</option>
                    <option value="medium">🟡 Medium</option>
                    <option value="low">🔵 Low</option>
                  </select>
                  <p v-if="form.errors.priority" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.priority }}</p>
                </div>
              </div>

              <!-- Suggestion Type & Title -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="suggestion_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Suggestion Type <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="suggestion_type"
                    v-model="form.suggestion_type"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., profile_completion"
                    required
                  />
                  <p v-if="form.errors.suggestion_type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.suggestion_type }}</p>
                </div>

                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Title <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Brief title"
                    required
                  />
                  <p v-if="form.errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.title }}</p>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Description <span class="text-red-500">*</span>
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Detailed description of the suggestion"
                  required
                ></textarea>
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.description }}</p>
              </div>

              <!-- Action Type & URL -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="action_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Action Type
                  </label>
                  <input
                    id="action_type"
                    v-model="form.action_type"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="e.g., navigate, external"
                  />
                  <p v-if="form.errors.action_type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.action_type }}</p>
                </div>

                <div>
                  <label for="action_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Action URL
                  </label>
                  <input
                    id="action_url"
                    v-model="form.action_url"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="/profile/edit or https://..."
                  />
                  <p v-if="form.errors.action_url" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.action_url }}</p>
                </div>
              </div>

              <!-- Additional Data (JSON) -->
              <div>
                <label for="data" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Additional Data (JSON)
                </label>
                <textarea
                  id="data"
                  v-model="form.data"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                  placeholder='{"key": "value", "another_key": 123}'
                ></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: Enter valid JSON for additional context data</p>
                <p v-if="form.errors.data" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.data }}</p>
              </div>

              <!-- Expiration & Relevance Score -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Expires At
                  </label>
                  <input
                    id="expires_at"
                    v-model="form.expires_at"
                    type="datetime-local"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for no expiration</p>
                  <p v-if="form.errors.expires_at" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.expires_at }}</p>
                </div>

                <div>
                  <label for="relevance_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Relevance Score (0-100)
                  </label>
                  <div class="flex items-center gap-4">
                    <input
                      id="relevance_score"
                      v-model.number="form.relevance_score"
                      type="range"
                      min="0"
                      max="100"
                      class="flex-1"
                    />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-12 text-right">
                      {{ form.relevance_score }}
                    </span>
                  </div>
                  <p v-if="form.errors.relevance_score" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.relevance_score }}</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end gap-4">
                <Link
                  :href="route('admin.data.smart-suggestions.index')"
                  class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Create Suggestion
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  users: Array,
})

const form = useForm({
  user_id: '',
  suggestion_type: '',
  category: '',
  priority: 'medium',
  title: '',
  description: '',
  data: '',
  action_type: '',
  action_url: '',
  expires_at: '',
  relevance_score: 50,
})

const submit = () => {
  form.post(route('admin.data.smart-suggestions.store'))
}
</script>
