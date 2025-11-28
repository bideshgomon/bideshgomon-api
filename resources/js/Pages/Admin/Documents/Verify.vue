<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import { EyeIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  pending: Object,
  recent: Array,
  counts: Object,
})

const approveForm = useForm({})
const rejectForm = useForm({ reason: '' })
const { formatDate } = useBangladeshFormat()

const viewingDocument = ref(null)
const showModal = ref(false)

function viewDocument(doc) {
  viewingDocument.value = doc
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  viewingDocument.value = null
}

function approve(doc) {
  approveForm.post(route('admin.documents.approve', doc.id))
}
function reject(doc) {
  rejectForm.post(route('admin.documents.reject', doc.id), {
    onSuccess: () => rejectForm.reset('reason')
  })
}

function getDocumentUrl(doc) {
  // Handle both full URLs and storage paths
  if (doc.file_path.startsWith('http://') || doc.file_path.startsWith('https://')) {
    return doc.file_path
  }
  // Remove 'documents/' prefix if it exists since /storage already points to storage/app/public
  const cleanPath = doc.file_path.replace(/^documents\//, '')
  return `/storage/documents/${cleanPath}`
}

function isImage(filename) {
  return /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(filename)
}

function isPdf(filename) {
  return /\.pdf$/i.test(filename)
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
                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="doc in pending.data" :key="doc.id">
                <td class="px-3 py-2 text-sm">{{ doc.user?.name || 'User #' + doc.user_id }}</td>
                <td class="px-3 py-2 text-sm font-medium">{{ doc.document_type.replace('_',' ') }}</td>
                <td class="px-3 py-2 text-sm">{{ doc.original_filename }}</td>
                <td class="px-3 py-2 text-sm">{{ formatDate(doc.created_at) }}</td>
                <td class="px-3 py-2 text-sm text-right space-x-2">
                  <button @click="viewDocument(doc)" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded inline-flex items-center gap-1">
                    <EyeIcon class="h-3 w-3" />
                    View
                  </button>
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

      <!-- Document Viewer Modal -->
      <Modal :show="showModal" @close="closeModal" max-width="4xl">
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">Document Preview</h2>
              <p v-if="viewingDocument" class="text-sm text-gray-600 mt-1">
                {{ viewingDocument.document_type.replace('_',' ') }} - {{ viewingDocument.original_filename }}
              </p>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <XMarkIcon class="h-6 w-6" />
            </button>
          </div>

          <div v-if="viewingDocument" class="bg-gray-100 rounded-lg p-4 min-h-[500px] flex items-center justify-center">
            <!-- Image Preview -->
            <img 
              v-if="isImage(viewingDocument.original_filename)"
              :src="getDocumentUrl(viewingDocument)"
              :alt="viewingDocument.original_filename"
              class="max-w-full max-h-[600px] object-contain rounded shadow-lg"
            />

            <!-- PDF Preview -->
            <iframe
              v-else-if="isPdf(viewingDocument.original_filename)"
              :src="getDocumentUrl(viewingDocument)"
              class="w-full h-[600px] rounded shadow-lg bg-white"
              frameborder="0"
            ></iframe>

            <!-- Other Files - Download Link -->
            <div v-else class="text-center">
              <p class="text-gray-600 mb-4">Preview not available for this file type</p>
              <a 
                :href="getDocumentUrl(viewingDocument)"
                target="_blank"
                download
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Download {{ viewingDocument.original_filename }}
              </a>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <div v-if="viewingDocument" class="text-sm text-gray-600">
              <p><strong>User:</strong> {{ viewingDocument.user?.name || 'User #' + viewingDocument.user_id }}</p>
              <p><strong>Uploaded:</strong> {{ formatDate(viewingDocument.created_at) }}</p>
            </div>
            <div class="flex gap-3">
              <button 
                v-if="viewingDocument"
                @click="approve(viewingDocument); closeModal()" 
                :disabled="approveForm.processing"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"
              >
                Approve Document
              </button>
              <button 
                @click="closeModal"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </Modal>
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
