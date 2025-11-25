<template>
  <AdminLayout>
    <Head title="Smart Suggestions" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Smart Suggestions</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Manage intelligent user suggestions with priorities and categories
                </p>
              </div>
              <div class="flex gap-2">
                <Link
                  :href="route('admin.data.smart-suggestions.bulk-upload')"
                  class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Bulk Upload
                </Link>
                <Link
                  :href="route('admin.data.smart-suggestions.create')"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                  Add Suggestion
                </Link>
              </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Search suggestions..."
                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
              <select
                v-model="statusFilter"
                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="dismissed">Dismissed</option>
                <option value="expired">Expired</option>
              </select>
              <select
                v-model="categoryFilter"
                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">All Categories</option>
                <option value="visa">🛂 Visa</option>
                <option value="profile">👤 Profile</option>
                <option value="document">📄 Document</option>
                <option value="application">📝 Application</option>
                <option value="assessment">⭐ Assessment</option>
              </select>
              <select
                v-model="priorityFilter"
                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">All Priorities</option>
                <option value="urgent">🔴 Urgent</option>
                <option value="high">🟠 High</option>
                <option value="medium">🟡 Medium</option>
                <option value="low">🔵 Low</option>
              </select>
            </div>

            <!-- Suggestions Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title & Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Relevance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="suggestion in suggestions.data" :key="suggestion.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ suggestion.user?.name || 'N/A' }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ suggestion.user?.email || '' }}
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <span class="text-xl">{{ getCategoryIcon(suggestion.category) }}</span>
                        <div>
                          <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ suggestion.title }}
                          </div>
                          <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                            {{ suggestion.category }} • {{ suggestion.suggestion_type }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'px-2 py-1 text-xs font-semibold rounded-full',
                          getPriorityClass(suggestion.priority)
                        ]"
                      >
                        {{ suggestion.priority }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'px-2 py-1 text-xs font-semibold rounded-full',
                          getStatusClass(suggestion)
                        ]"
                      >
                        {{ getStatus(suggestion) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center gap-2">
                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                          <div
                            class="bg-indigo-600 h-2 rounded-full"
                            :style="{ width: `${suggestion.relevance_score}%` }"
                          ></div>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ suggestion.relevance_score }}</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      <span v-if="suggestion.expires_at" :class="isExpired(suggestion.expires_at) ? 'text-red-600 dark:text-red-400' : ''">
                        {{ formatDate(suggestion.expires_at) }}
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex gap-2">
                        <button
                          v-if="suggestion.action_url && !suggestion.is_completed && !suggestion.is_dismissed"
                          @click="openActionUrl(suggestion.action_url)"
                          class="text-purple-600 hover:text-purple-900 dark:text-purple-400"
                          title="Take Action"
                        >
                          ➜
                        </button>
                        <button
                          v-if="!suggestion.is_completed && !suggestion.is_dismissed"
                          @click="markCompleted(suggestion.id)"
                          class="text-green-600 hover:text-green-900 dark:text-green-400"
                          title="Mark Completed"
                        >
                          ✓
                        </button>
                        <button
                          v-if="!suggestion.is_completed && !suggestion.is_dismissed"
                          @click="dismissSuggestion(suggestion.id)"
                          class="text-gray-600 hover:text-gray-900 dark:text-gray-400"
                          title="Dismiss"
                        >
                          ✕
                        </button>
                        <Link
                          :href="route('admin.data.smart-suggestions.edit', suggestion.id)"
                          class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400"
                        >
                          Edit
                        </Link>
                        <button
                          @click="deleteSuggestion(suggestion.id)"
                          class="text-red-600 hover:text-red-900 dark:text-red-400"
                        >
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="suggestions.data && suggestions.data.length > 0" class="mt-6">
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                  Showing {{ suggestions.from }} to {{ suggestions.to }} of {{ suggestions.total }} results
                </div>
                <div class="flex gap-2">
                  <template v-for="link in suggestions.links" :key="link.label">
                    <Link
                      v-if="link.url"
                      :href="link.url"
                      :class="[
                        'px-3 py-1 rounded border text-sm',
                        link.active
                          ? 'bg-indigo-600 text-white border-indigo-600'
                          : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                      ]"
                      v-html="link.label"
                    />
                    <span
                      v-else
                      :class="[
                        'px-3 py-1 rounded border text-sm opacity-50 cursor-not-allowed',
                        link.active
                          ? 'bg-indigo-600 text-white border-indigo-600'
                          : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600'
                      ]"
                      v-html="link.label"
                    />
                  </template>
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
import { ref, watch } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  suggestions: Object,
  filters: Object,
})

const searchForm = useForm({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  category: props.filters?.category || '',
  priority: props.filters?.priority || '',
})

const searchTerm = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')
const categoryFilter = ref(props.filters?.category || '')
const priorityFilter = ref(props.filters?.priority || '')

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    performSearch()
  }, 300)
}

watch(statusFilter, () => performSearch())
watch(categoryFilter, () => performSearch())
watch(priorityFilter, () => performSearch())
watch(searchTerm, () => debouncedSearch())

const performSearch = () => {
  searchForm.search = searchTerm.value
  searchForm.status = statusFilter.value
  searchForm.category = categoryFilter.value
  searchForm.priority = priorityFilter.value

  searchForm.get(route('admin.data.smart-suggestions.index'), {
    preserveState: true,
    preserveScroll: true,
  })
}

const getCategoryIcon = (category) => {
  const icons = {
    visa: '🛂',
    profile: '👤',
    document: '📄',
    application: '📝',
    assessment: '⭐',
  }
  return icons[category] || '💡'
}

const getPriorityClass = (priority) => {
  const classes = {
    urgent: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    high: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    low: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
  }
  return classes[priority] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

const getStatus = (suggestion) => {
  if (suggestion.is_completed) return 'Completed'
  if (suggestion.is_dismissed) return 'Dismissed'
  if (suggestion.expires_at && isExpired(suggestion.expires_at)) return 'Expired'
  return 'Active'
}

const getStatusClass = (suggestion) => {
  if (suggestion.is_completed) return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
  if (suggestion.is_dismissed) return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
  if (suggestion.expires_at && isExpired(suggestion.expires_at)) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
  return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
}

const isExpired = (expiresAt) => {
  return new Date(expiresAt) < new Date()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const openActionUrl = (url) => {
  window.open(url, '_blank')
}

const markCompleted = (id) => {
  if (confirm('Mark this suggestion as completed?')) {
    router.post(route('admin.data.smart-suggestions.mark-completed', id))
  }
}

const dismissSuggestion = (id) => {
  if (confirm('Dismiss this suggestion?')) {
    router.post(route('admin.data.smart-suggestions.dismiss', id))
  }
}

const deleteSuggestion = (id) => {
  if (confirm('Are you sure you want to delete this suggestion?')) {
    router.delete(route('admin.data.smart-suggestions.destroy', id))
  }
}
</script>
