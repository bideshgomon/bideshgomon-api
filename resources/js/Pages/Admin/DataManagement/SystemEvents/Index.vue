<template>
  <AdminLayout>
    <Head title="System Events" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">System Events</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Track and manage system events
                </p>
              </div>
              <div class="flex gap-2">
                <Link
                  :href="route('admin.data.system-events.bulk-upload')"
                  class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Bulk Upload
                </Link>
                <Link
                  :href="route('admin.data.system-events.create')"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                  Create Event
                </Link>
              </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <input
                  v-model="searchForm.search"
                  type="text"
                  placeholder="Search events..."
                  class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @input="debouncedSearch"
                />
              </div>
              <div>
                <select
                  v-model="searchForm.event_type"
                  class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @change="search"
                >
                  <option value="">All Event Types</option>
                  <option v-for="type in eventTypes" :key="type" :value="type">{{ type }}</option>
                </select>
              </div>
              <div>
                <select
                  v-model="searchForm.target_type"
                  class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @change="search"
                >
                  <option value="">All Target Types</option>
                  <option v-for="type in targetTypes" :key="type" :value="type">{{ type }}</option>
                </select>
              </div>
              <div>
                <a
                  :href="route('admin.data.system-events.export')"
                  class="inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                >
                  Export CSV
                </a>
              </div>
            </div>

            <!-- Events Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Event Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Target
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Data
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Created At
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="event in events.data" :key="event.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                          {{ event.event_type }}
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                      {{ event.user_id ? `User #${event.user_id}` : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                      <div v-if="event.target_type">
                        <div class="font-medium">{{ event.target_type }}</div>
                        <div class="text-gray-500 dark:text-gray-400">#{{ event.target_id }}</div>
                      </div>
                      <span v-else>-</span>
                    </td>
                    <td class="px-6 py-4">
                      <div v-if="event.data" class="text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate">
                        {{ JSON.stringify(event.data) }}
                      </div>
                      <span v-else class="text-gray-500 dark:text-gray-400">-</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(event.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <Link
                        :href="route('admin.data.system-events.edit', event.id)"
                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3"
                      >
                        Edit
                      </Link>
                      <button
                        @click="deleteEvent(event.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="events.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  events: Object,
  filters: Object,
  eventTypes: Array,
  targetTypes: Array,
})

const searchForm = useForm({
  search: props.filters.search || '',
  event_type: props.filters.event_type || '',
  target_type: props.filters.target_type || '',
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    search()
  }, 300)
}

const search = () => {
  searchForm.get(route('admin.data.system-events.index'), {
    preserveState: true,
    preserveScroll: true,
  })
}

const deleteEvent = (id) => {
  if (confirm('Are you sure you want to delete this event?')) {
    router.delete(route('admin.data.system-events.destroy', id))
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}
</script>
