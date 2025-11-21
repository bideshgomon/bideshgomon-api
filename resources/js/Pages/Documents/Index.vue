<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
  documents: Object,
  supportedTypes: Array,
})

const { formatDate } = useBangladeshFormat()

const form = useForm({
  document_type: '',
  file: null,
  expires_at: '',
  is_primary: false,
})

function submit() {
  form.post(route('documents.store'), {
    forceFormData: true,
    onSuccess: () => {
      form.reset('file')
    }
  })
}

function handleFile(e) {
  form.file = e.target.files[0]
}
</script>

<template>
  <AuthenticatedLayout title="My Documents">
    <div class="max-w-6xl mx-auto py-8 px-4">
      <h1 class="text-2xl font-bold mb-6">Document Verification</h1>

      <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Upload New Document</h2>
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Document Type</label>
            <select v-model="form.document_type" class="w-full border rounded p-2">
              <option value="">Select type...</option>
              <option v-for="t in supportedTypes" :key="t" :value="t">{{ t.replace('_',' ') }}</option>
            </select>
            <p v-if="form.errors.document_type" class="text-red-600 text-sm mt-1">{{ form.errors.document_type }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">File</label>
            <input type="file" @change="handleFile" class="w-full" />
            <p v-if="form.errors.file" class="text-red-600 text-sm mt-1">{{ form.errors.file }}</p>
          </div>
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="block text-sm font-medium mb-1">Expiry Date (optional)</label>
              <input type="date" v-model="form.expires_at" class="w-full border rounded p-2" />
            </div>
            <div class="flex items-center mt-6">
              <input id="is_primary" type="checkbox" v-model="form.is_primary" class="mr-2" />
              <label for="is_primary" class="text-sm">Set as primary</label>
            </div>
          </div>
          <button :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50">Upload</button>
          <p v-if="form.recentlySuccessful" class="text-green-600 text-sm">Uploaded successfully.</p>
        </form>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">My Documents</h2>
        <div v-if="documents.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h5.586a1 1 0 01.707.293l1.414 1.414A1 1 0 0012.414 5H20a1 1 0 011 1v10a1 1 0 01-1 1h-5"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 10h18M3 13h18M3 16h9"/></svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No documents yet</h3>
          <p class="mt-1 text-sm text-gray-500">Upload your first document to start verification.</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primary</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                <th class="px-3 py-2" />
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="doc in documents.data" :key="doc.id" class="hover:bg-gray-50">
                <td class="px-3 py-2 text-sm font-medium text-gray-900">{{ doc.document_type.replace('_',' ') }}</td>
                <td class="px-3 py-2 text-sm text-gray-600">{{ doc.original_filename }}</td>
                <td class="px-3 py-2">
                  <span :class="badgeClass(doc.status)" class="px-2 py-1 rounded-full text-xs font-semibold">{{ doc.status }}</span>
                  <div v-if="doc.rejection_reason" class="text-xs text-red-600 mt-1">{{ doc.rejection_reason }}</div>
                </td>
                <td class="px-3 py-2 text-sm">
                  <span v-if="doc.is_primary" class="text-green-600 font-semibold">Yes</span>
                  <span v-else class="text-gray-500">No</span>
                </td>
                <td class="px-3 py-2 text-sm">{{ doc.expires_at ? formatDate(doc.expires_at) : '—' }}</td>
                <td class="px-3 py-2 text-sm">{{ formatDate(doc.created_at) }}</td>
                <td class="px-3 py-2 text-sm text-right">
                  <form @submit.prevent="() => form.delete(route('documents.destroy', doc.id))">
                    <button class="text-red-600 hover:text-red-800 text-xs" :disabled="form.processing">Delete</button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 flex items-center justify-between" v-if="documents.links.length > 3">
            <div class="text-sm text-gray-600">Showing {{ documents.data.length }} of {{ documents.total }}</div>
            <div class="flex gap-1">
              <Link v-for="l in documents.links" :key="l.url + l.label" :href="l.url" v-html="l.label" :class="['px-2 py-1 rounded text-xs', l.active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700']" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
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
