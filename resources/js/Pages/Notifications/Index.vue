<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
  notifications: Object,
  unreadCount: Number
})

const { formatDate, formatTime } = useBangladeshFormat()
const readForm = useForm({})
const readAllForm = useForm({})

function markRead(id) {
  readForm.post(route('notifications.read', id))
}
function markAll() {
  readAllForm.post(route('notifications.read-all'))
}
</script>

<template>
  <AuthenticatedLayout title="Notifications">
    <div class="max-w-5xl mx-auto py-8 px-4">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Notifications</h1>
        <button @click="markAll" :disabled="readAllForm.processing || unreadCount===0" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded disabled:opacity-50">Mark All Read ({{ unreadCount }})</button>
      </div>

      <div v-if="notifications.data.length === 0" class="bg-white rounded shadow p-8 text-center">
        <p class="text-gray-600">No notifications yet.</p>
      </div>

      <ul v-else class="space-y-3">
        <li v-for="n in notifications.data" :key="n.id" class="bg-white shadow rounded p-4 flex justify-between items-start" :class="{'opacity-70': n.read_at}">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span :class="badgeClass(n.priority)" class="px-2 py-0.5 text-xs font-semibold rounded-full">{{ n.priority }}</span>
              <h2 class="font-semibold text-sm">{{ n.title }}</h2>
            </div>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ n.body }}</p>
            <div class="mt-2 text-xs text-gray-500">{{ formatDate(n.created_at) }} • {{ formatTime(n.created_at) }}</div>
          </div>
          <div class="ml-4">
            <button v-if="!n.read_at" @click="markRead(n.id)" :disabled="readForm.processing" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">Mark Read</button>
            <span v-else class="text-xs text-gray-500">Read</span>
          </div>
        </li>
      </ul>

      <div class="mt-6 flex justify-between items-center" v-if="notifications.links.length > 3">
        <div class="text-sm text-gray-600">Showing {{ notifications.data.length }} of {{ notifications.total }}</div>
        <div class="flex gap-1">
          <Link v-for="l in notifications.links" :key="l.url + l.label" :href="l.url" v-html="l.label" :class="['px-2 py-1 rounded text-xs', l.active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']" />
        </div>
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
