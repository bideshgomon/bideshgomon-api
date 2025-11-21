<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
  recent: Array,
  stats: Object
})

const { formatDate, formatTime } = useBangladeshFormat()
const form = useForm({
  title: '',
  body: '',
  priority: 'normal',
  scope: 'all',
  user_ids: '', // comma separated for simplicity
})

function submit() {
  let payload = { ...form.data() }
  if (payload.scope === 'user_ids') {
    payload.user_ids = payload.user_ids.split(',').map(v => v.trim()).filter(v => v.length).map(v => parseInt(v))
  } else {
    delete payload.user_ids
  }
  form.post(route('admin.notifications.broadcast'), {
    onSuccess: () => form.reset('title', 'body')
  })
}
</script>

<template>
  <AuthenticatedLayout title="Admin Notifications">
    <div class="max-w-7xl mx-auto py-8 px-4">
      <h1 class="text-2xl font-bold mb-6">Notifications Center</h1>

      <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded shadow p-4"><p class="text-sm text-gray-500">Total</p><p class="text-2xl font-semibold">{{ stats.total }}</p></div>
        <div class="bg-white rounded shadow p-4"><p class="text-sm text-gray-500">Unread</p><p class="text-2xl font-semibold">{{ stats.unread }}</p></div>
        <div class="bg-white rounded shadow p-4"><p class="text-sm text-gray-500">Critical</p><p class="text-2xl font-semibold">{{ stats.critical }}</p></div>
      </div>

      <div class="bg-white shadow rounded-lg p-6 mb-10">
        <h2 class="text-lg font-semibold mb-4">Send Broadcast</h2>
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="text-sm font-medium block mb-1">Title</label>
            <input v-model="form.title" type="text" class="w-full border rounded p-2" />
            <p v-if="form.errors.title" class="text-xs text-red-600 mt-1">{{ form.errors.title }}</p>
          </div>
          <div>
            <label class="text-sm font-medium block mb-1">Body</label>
            <textarea v-model="form.body" rows="4" class="w-full border rounded p-2"></textarea>
            <p v-if="form.errors.body" class="text-xs text-red-600 mt-1">{{ form.errors.body }}</p>
          </div>
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="text-sm font-medium block mb-1">Priority</label>
              <select v-model="form.priority" class="w-full border rounded p-2">
                <option value="normal">Normal</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
              </select>
            </div>
            <div class="flex-1">
              <label class="text-sm font-medium block mb-1">Scope</label>
              <select v-model="form.scope" class="w-full border rounded p-2">
                <option value="all">All Users</option>
                <option value="user_ids">Specific User IDs</option>
              </select>
            </div>
          </div>
          <div v-if="form.scope === 'user_ids'">
            <label class="text-sm font-medium block mb-1">User IDs (comma separated)</label>
            <input v-model="form.user_ids" type="text" class="w-full border rounded p-2" placeholder="e.g. 1,2,5" />
            <p v-if="form.errors.user_ids" class="text-xs text-red-600 mt-1">{{ form.errors.user_ids }}</p>
          </div>
          <button :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm disabled:opacity-50">Send Broadcast</button>
          <p v-if="form.recentlySuccessful" class="text-green-600 text-xs">Broadcast sent.</p>
        </form>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Notifications</h2>
        <div v-if="recent.length === 0" class="text-sm text-gray-500">No notifications yet.</div>
        <ul v-else class="space-y-3">
          <li v-for="n in recent" :key="n.id" class="border rounded p-4 flex justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span :class="badgeClass(n.priority)" class="px-2 py-0.5 text-xs font-semibold rounded-full">{{ n.priority }}</span>
                <h3 class="font-medium text-sm">{{ n.title }}</h3>
              </div>
              <p class="text-xs text-gray-600">To: {{ n.user?.name || 'User #'+n.user_id }}</p>
              <p class="text-sm text-gray-700 mt-1">{{ n.body }}</p>
              <div class="text-xs text-gray-500 mt-1">{{ formatDate(n.created_at) }} • {{ formatTime(n.created_at) }}</div>
            </div>
            <div class="text-xs text-gray-500">{{ n.read_at ? 'Read' : 'Unread' }}</div>
          </li>
        </ul>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
export default {
  methods: {
    badgeClass(priority) {
      return {
        'bg-blue-100 text-blue-700': priority === 'normal',
        'bg-yellow-100 text-yellow-800': priority === 'high',
        'bg-red-100 text-red-800': priority === 'critical'
      }
    }
  }
}
</script>

<style scoped></style>
