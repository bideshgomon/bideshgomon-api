<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { FunnelIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  logs: Object,
  filters: Object,
  admins: Array,
});

const localFilters = ref({
  status: props.filters.status || '',
  admin_id: props.filters.admin_id || '',
  target_id: props.filters.target_id || '',
  from: props.filters.from || '',
  to: props.filters.to || '',
});

function applyFilters() {
  router.get(route('admin.impersonations.index'), localFilters.value, { preserveState: true, replace: true });
}

function resetFilters() {
  localFilters.value = { status: '', admin_id: '', target_id: '', from: '', to: '' };
  applyFilters();
}

function exportCsv() {
  const params = new URLSearchParams(localFilters.value).toString();
  window.location.href = route('admin.impersonations.export') + (params ? ('?' + params) : '');
}

const formatDateTime = (dt) => dt ? new Date(dt).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';

</script>

<template>
  <Head title="Impersonation Logs" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Admin Impersonation Logs</h1>
          <p class="text-sm text-gray-600 mt-1">Audit trail of admin user session impersonations.</p>
        </div>
        <div class="flex items-center space-x-3">
          <button @click="exportCsv" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <ArrowDownTrayIcon class="h-5 w-5 mr-2" /> Export CSV
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center mb-4">
          <FunnelIcon class="h-5 w-5 text-gray-500 mr-2" />
          <h2 class="text-sm font-semibold text-gray-700">Filters</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select v-model="localFilters.value.status" class="w-full rounded-md border-gray-300 text-sm">
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="ended">Ended</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Admin</label>
            <select v-model="localFilters.value.admin_id" class="w-full rounded-md border-gray-300 text-sm">
              <option value="">All</option>
              <option v-for="admin in admins" :key="admin.id" :value="admin.id">{{ admin.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
            <input type="date" v-model="localFilters.value.from" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
            <input type="date" v-model="localFilters.value.to" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
        </div>
        <div class="mt-4 flex items-center space-x-3">
          <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Apply</button>
          <button @click="resetFilters" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Reset</button>
        </div>
      </div>

      <!-- Logs Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">ID</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Admin</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Target User</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Purpose</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Started</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Ended</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Duration</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50">
                <td class="px-3 py-2 text-gray-600">{{ log.id }}</td>
                <td class="px-3 py-2">
                  <span v-if="log.impersonator" class="font-medium text-gray-900">{{ log.impersonator.name }}</span>
                  <span v-else class="text-gray-400">Unknown</span>
                </td>
                <td class="px-3 py-2">
                  <span v-if="log.target" class="font-medium text-gray-900">{{ log.target.name }}</span>
                  <span v-else class="text-gray-400">Unknown</span>
                </td>
                <td class="px-3 py-2 text-gray-700"><span class="line-clamp-2 max-w-xs block">{{ log.purpose || '—' }}</span></td>
                <td class="px-3 py-2 text-gray-600">{{ formatDateTime(log.started_at) }}</td>
                <td class="px-3 py-2 text-gray-600">{{ log.ended_at ? formatDateTime(log.ended_at) : '—' }}</td>
                <td class="px-3 py-2 text-gray-600">{{ log.duration_minutes !== null ? (log.duration_minutes + ' min') : '—' }}</td>
                <td class="px-3 py-2">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="{
                    'bg-green-100 text-green-700': log.status === 'ended',
                    'bg-yellow-100 text-yellow-700': log.status === 'active'
                  }">{{ log.status }}</span>
                </td>
              </tr>
              <tr v-if="logs.data.length === 0">
                <td colspan="8" class="px-3 py-6 text-center text-gray-500">No impersonation logs found.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div v-if="logs.links" class="mt-4 flex flex-wrap gap-2">
          <Link v-for="link in logs.links" :key="link.url || link.label" :href="link.url || '#'" v-html="link.label" :class="[
            'px-3 py-1 text-xs rounded border',
            link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300',
            !link.url ? 'opacity-50 cursor-not-allowed' : ''
          ]" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
