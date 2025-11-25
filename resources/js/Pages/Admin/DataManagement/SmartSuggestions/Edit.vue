<template>
  <AdminLayout>
    <Head title="Edit Smart Suggestion" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Smart Suggestion</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Update suggestion details
                </p>
              </div>
              <Link
                :href="route('admin.data.smart-suggestions.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Back
              </Link>
            </div>

            <!-- Status Alert -->
            <div v-if="suggestion.is_completed || suggestion.is_dismissed" class="mb-6 p-4 rounded-md" :class="suggestion.is_completed ? 'bg-blue-50 dark:bg-blue-900' : 'bg-gray-50 dark:bg-gray-900'">
              <div class="flex">
                <div class="flex-shrink-0">
                  <span v-if="suggestion.is_completed" class="text-2xl">✓</span>
                  <span v-else class="text-2xl">✕</span>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium" :class="suggestion.is_completed ? 'text-blue-800 dark:text-blue-200' : 'text-gray-800 dark:text-gray-200'">
                    {{ suggestion.is_completed ? 'Completed' : 'Dismissed' }}
                  </h3>
                  <div class="mt-2 text-sm" :class="suggestion.is_completed ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300'">
                    <p v-if="suggestion.is_completed">
                      This suggestion was marked as completed on {{ formatDate(suggestion.completed_at) }}
                    </p>
                    <p v-else>
                      This suggestion was dismissed on {{ formatDate(suggestion.dismissed_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- User (Read-only) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  User
                </label>
                <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ suggestion.user?.name || 'N/A' }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ suggestion.user?.email || '' }}
                  </div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">User cannot be changed after creation</p>
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
                ></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter valid JSON for additional context data</p>
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

              <!-- Status Controls -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="flex items-center">
                    <input
                      v-model="form.is_completed"
                      type="checkbox"
                      class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    />
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark as Completed</span>
                  </label>
                </div>

                <div>
                  <label class="flex items-center">
                    <input
                      v-model="form.is_dismissed"
                      type="checkbox"
                      class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    />
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark as Dismissed</span>
                  </label>
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
                  Update Suggestion
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
  suggestion: Object,
  users: Array,
})

const form = useForm({
  user_id: props.suggestion.user_id,
  suggestion_type: props.suggestion.suggestion_type,
  category: props.suggestion.category,
  priority: props.suggestion.priority,
  title: props.suggestion.title,
  description: props.suggestion.description,
  data: props.suggestion.data ? JSON.stringify(props.suggestion.data, null, 2) : '',
  action_type: props.suggestion.action_type || '',
  action_url: props.suggestion.action_url || '',
  expires_at: props.suggestion.expires_at ? props.suggestion.expires_at.substring(0, 16) : '',
  relevance_score: props.suggestion.relevance_score,
  is_completed: props.suggestion.is_completed,
  is_dismissed: props.suggestion.is_dismissed,
})

const submit = () => {
  form.put(route('admin.data.smart-suggestions.update', props.suggestion.id))
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}
</script>
