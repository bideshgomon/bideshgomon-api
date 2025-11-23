<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
  pending: Object,
  recent: Array,
  counts: Object,
})

const approveForm = useForm({})
const rejectForm = useForm({ reason: '' })
const { formatDate } = useBangladeshFormat()

function approve(doc) {
  approveForm.post(route('admin.documents.approve', doc.id))
}
function reject(doc) {
  rejectForm.post(route('admin.documents.reject', doc.id), {
    onSuccess: () => rejectForm.reset('reason')
  })
}
</script>

<template>
  <AdminLayout title="Verify Documents">
    <div class="max-w-7xl mx-auto py-8 px-4">
      <h1 class="text-2xl font-bold mb-6">Document Verification</h1>

      <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white shadow rounded p-4">
          <p class="text-sm text-gray-500">Pending</p>
          <p class="text-2xl font-semibold">{{ counts.pending }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
          <p class="text-sm text-gray-500">Approved</p>
          <p class="text-2xl font-semibold">{{ counts.approved }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
          <p class="text-sm text-gray-500">Rejected</p>
          <p class="text-2xl font-semibold">{{ counts.rejected }}</p>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6 mb-10">
        <h2 class="text-lg font-semibold mb-4">Pending Documents</h2>
        <div v-if="pending.data.length === 0" class="text-center py-8 text-sm text-gray-500">No pending documents.</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Filename</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                <th class="px-3 py-2" />
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="doc in pending.data" :key="doc.id">
                <td class="px-3 py-2 text-sm">{{ doc.user?.name || 'User #' + doc.user_id }}</td>
                <td class="px-3 py-2 text-sm font-medium">{{ doc.document_type.replace('_',' ') }}</td>
                <td class="px-3 py-2 text-sm">{{ doc.original_filename }}</td>
                <td class="px-3 py-2 text-sm">{{ formatDate(doc.created_at) }}</td>
                <td class="px-3 py-2 text-sm text-right space-x-2">
                  <button @click="approve(doc)" :disabled="approveForm.processing" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">Approve</button>
                  <details class="inline-block">
                    <summary class="cursor-pointer bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded inline-block">Reject</summary>
                    <div class="mt-2 bg-white border rounded p-3 w-64 shadow">
                      <textarea v-model="rejectForm.reason" rows="3" class="w-full border rounded p-2 text-sm" placeholder="Reason"></textarea>
                      <button @click.prevent="reject(doc)" :disabled="rejectForm.processing" class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">Confirm Reject</button>
                    </div>
                  </details>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 flex items-center justify-between" v-if="pending.links.length > 3">
            <div class="text-sm text-gray-600">Showing {{ pending.data.length }} of {{ pending.total }}</div>
            <div class="flex gap-1">
              <Link v-for="l in pending.links" :key="l.url + l.label" :href="l.url" v-html="l.label" :class="['px-2 py-1 rounded text-xs', l.active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']" />
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Recently Reviewed</h2>
        <div v-if="recent.length === 0" class="text-center py-8 text-sm text-gray-500">No reviewed documents yet.</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reviewed By</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reviewed At</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="doc in recent" :key="doc.id">
                <td class="px-3 py-2 text-sm">{{ doc.user?.name || 'User #' + doc.user_id }}</td>
                <td class="px-3 py-2 text-sm font-medium">{{ doc.document_type.replace('_',' ') }}</td>
                <td class="px-3 py-2 text-sm">
                  <span :class="badgeClass(doc.status)" class="px-2 py-1 rounded-full text-xs font-semibold">{{ doc.status }}</span>
                  <div v-if="doc.rejection_reason" class="text-xs text-red-600 mt-1">{{ doc.rejection_reason }}</div>
                </td>
                <td class="px-3 py-2 text-sm">{{ doc.reviewer?.name || '—' }}</td>
                <td class="px-3 py-2 text-sm">{{ doc.reviewed_at ? formatDate(doc.reviewed_at) : '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
export default {
  methods: {
    badgeClass(status) {
      return {
        'bg-yellow-100 text-yellow-800': status === 'pending',
        'bg-green-100 text-green-800': status === 'approved',
        'bg-red-100 text-red-800': status === 'rejected'
      }
    }
  }
}
</script>

<style scoped>
</style>
