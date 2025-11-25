<template>
  <AdminLayout>
    <Head title="Edit System Event" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit System Event</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Update event details
                </p>
              </div>
              <Link
                :href="route('admin.data.system-events.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Back
              </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Event Type -->
              <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Event Type <span class="text-red-500">*</span>
                </label>
                <input
                  id="event_type"
                  v-model="form.event_type"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                />
                <p v-if="form.errors.event_type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.event_type }}</p>
              </div>

              <!-- User -->
              <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  User
                </label>
                <select
                  id="user_id"
                  v-model="form.user_id"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">None</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }})
                  </option>
                </select>
                <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.user_id }}</p>
              </div>

              <!-- Target Type & ID -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="target_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Target Type
                  </label>
                  <input
                    id="target_type"
                    v-model="form.target_type"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <p v-if="form.errors.target_type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.target_type }}</p>
                </div>

                <div>
                  <label for="target_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Target ID
                  </label>
                  <input
                    id="target_id"
                    v-model.number="form.target_id"
                    type="number"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <p v-if="form.errors.target_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.target_id }}</p>
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
                  rows="6"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                ></textarea>
                <p v-if="form.errors.data" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.data }}</p>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end gap-4">
                <Link
                  :href="route('admin.data.system-events.index')"
                  class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Update Event
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
  event: Object,
  users: Array,
})

const form = useForm({
  event_type: props.event.event_type,
  user_id: props.event.user_id || '',
  target_type: props.event.target_type || '',
  target_id: props.event.target_id || '',
  data: props.event.data ? JSON.stringify(props.event.data, null, 2) : '',
})

const submit = () => {
  form.put(route('admin.data.system-events.update', props.event.id))
}
</script>
