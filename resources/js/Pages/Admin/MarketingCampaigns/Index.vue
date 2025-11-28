<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Card from '@/Components/ui/Card.vue'
import Badge from '@/Components/ui/Badge.vue'
import {
  FunnelIcon,
  PlusIcon,
  EyeIcon,
  PencilIcon,
  PauseIcon,
  PlayIcon,
  XMarkIcon,
  DocumentDuplicateIcon,
  TrashIcon,
  ChartBarIcon,
  EnvelopeIcon,
  DevicePhoneMobileIcon,
  BellIcon,
  InboxIcon,
  UserGroupIcon,
  ArrowTrendingUpIcon,
  CursorArrowRaysIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  campaigns: Object,
  filters: Object,
  stats: Object,
})

const filterForm = ref({
  type: props.filters?.type || '',
  status: props.filters?.status || '',
  search: props.filters?.search || '',
})

const applyFilters = () => {
  router.get(route('admin.marketing-campaigns.index'), filterForm.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  filterForm.value = { type: '', status: '', search: '' }
  applyFilters()
}

const getStatusColor = (status) => {
  const colors = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
    scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    sending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    sent: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    paused: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
  }
  return colors[status] || colors.draft
}

const getTypeIcon = (type) => {
  const icons = {
    email: { component: EnvelopeIcon, color: 'text-blue-600' },
    sms: { component: DevicePhoneMobileIcon, color: 'text-green-600' },
    push: { component: BellIcon, color: 'text-purple-600' },
    notification: { component: InboxIcon, color: 'text-orange-600' },
  }
  return icons[type] || icons.email
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatPercentage = (value) => value ? `${value.toFixed(1)}%` : '0.0%'

const pauseCampaign = (campaignId) => {
  if (!confirm('Are you sure you want to pause this campaign?')) return

  router.post(route('admin.marketing-campaigns.pause', campaignId), {}, {
    preserveScroll: true,
  })
}

const resumeCampaign = (campaignId) => {
  if (!confirm('Are you sure you want to resume this campaign?')) return

  router.post(route('admin.marketing-campaigns.resume', campaignId), {}, {
    preserveScroll: true,
  })
}

const cancelCampaign = (campaignId) => {
  if (!confirm('Are you sure you want to cancel this campaign? This action cannot be undone.')) return

  router.post(route('admin.marketing-campaigns.cancel', campaignId), {}, {
    preserveScroll: true,
  })
}

const duplicateCampaign = (campaignId) => {
  router.post(route('admin.marketing-campaigns.duplicate', campaignId), {}, {
    preserveScroll: true,
  })
}

const deleteCampaign = (campaignId) => {
  if (!confirm('Are you sure you want to delete this draft campaign? This action cannot be undone.')) return

  router.delete(route('admin.marketing-campaigns.destroy', campaignId), {
    preserveScroll: true,
  })
}
</script>

<template>
  <AdminLayout title="Marketing Campaigns">
    <div class="mb-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Marketing Campaigns</h1>
        <Link
          :href="route('admin.marketing-campaigns.create')"
          class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Create Campaign
        </Link>
      </div>
    </div>

    <!-- Stats Overview -->
    <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center mb-2">
          <div class="p-2 bg-blue-100 rounded-lg mr-3">
            <ChartBarIcon class="w-5 h-5 text-blue-600" />
          </div>
          <p class="text-sm font-medium text-gray-600">Total Campaigns</p>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ stats?.total || 0 }}</p>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center mb-2">
          <div class="p-2 bg-green-100 rounded-lg mr-3">
            <UserGroupIcon class="w-5 h-5 text-green-600" />
          </div>
          <p class="text-sm font-medium text-gray-600">Total Reach</p>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ (stats?.total_recipients || 0).toLocaleString() }}</p>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center mb-2">
          <div class="p-2 bg-purple-100 rounded-lg mr-3">
            <ArrowTrendingUpIcon class="w-5 h-5 text-purple-600" />
          </div>
          <p class="text-sm font-medium text-gray-600">Avg Open Rate</p>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ formatPercentage(stats?.avg_open_rate) }}</p>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center mb-2">
          <div class="p-2 bg-orange-100 rounded-lg mr-3">
            <CursorArrowRaysIcon class="w-5 h-5 text-orange-600" />
          </div>
          <p class="text-sm font-medium text-gray-600">Avg Click Rate</p>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ formatPercentage(stats?.avg_click_rate) }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="filterForm.search"
            type="text"
            placeholder="Search campaigns..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            @input="applyFilters"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Type</label>
          <select
            v-model="filterForm.type"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            @change="applyFilters"
          >
            <option value="">All Types</option>
            <option value="email">Email</option>
            <option value="sms">SMS</option>
            <option value="push">Push</option>
            <option value="notification">Notification</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="filterForm.status"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            @change="applyFilters"
          >
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="sending">Sending</option>
            <option value="sent">Sent</option>
            <option value="paused">Paused</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div v-if="campaigns.data.length === 0" class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No campaigns found</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new marketing campaign.</p>
        <div class="mt-6">
          <Link
            :href="route('admin.marketing-campaigns.create')"
            class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Create Campaign
          </Link>
        </div>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="campaign in campaigns.data"
            :key="campaign.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <component
                  :is="getTypeIcon(campaign.type).component"
                  :class="['w-5 h-5 mr-3', getTypeIcon(campaign.type).color]"
                />
                <div>
                  <Link
                    :href="route('admin.marketing-campaigns.show', campaign.id)"
                    class="text-sm font-medium text-gray-900 hover:text-teal-600"
                  >
                    {{ campaign.name }}
                  </Link>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm text-gray-600 capitalize">{{ campaign.type }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusColor(campaign.status)]">
                {{ campaign.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ campaign.total_recipients.toLocaleString() }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-600">
                <div>Open: {{ formatPercentage(campaign.open_rate) }}</div>
                <div>Click: {{ formatPercentage(campaign.click_rate) }}</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              <div v-if="campaign.scheduled_at">{{ formatDate(campaign.scheduled_at) }}</div>
              <div v-else-if="campaign.started_at">{{ formatDate(campaign.started_at) }}</div>
              <div v-else>-</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end gap-2">
                <Link
                  :href="route('admin.marketing-campaigns.show', campaign.id)"
                  class="text-blue-600 hover:text-blue-900"
                  title="View"
                >
                  <EyeIcon class="w-5 h-5" />
                </Link>
                <Link
                  v-if="['draft', 'scheduled', 'paused'].includes(campaign.status)"
                  :href="route('admin.marketing-campaigns.edit', campaign.id)"
                  class="text-green-600 hover:text-green-900"
                  title="Edit"
                >
                  <PencilIcon class="w-5 h-5" />
                </Link>
                <button
                  v-if="['scheduled', 'sending'].includes(campaign.status)"
                  @click="pauseCampaign(campaign.id)"
                  class="text-orange-600 hover:text-orange-900"
                  title="Pause"
                >
                  <PauseIcon class="w-5 h-5" />
                </button>
                <button
                  v-if="campaign.status === 'paused'"
                  @click="resumeCampaign(campaign.id)"
                  class="text-green-600 hover:text-green-900"
                  title="Resume"
                >
                  <PlayIcon class="w-5 h-5" />
                </button>
                <button
                  @click="duplicateCampaign(campaign.id)"
                  class="text-purple-600 hover:text-purple-900"
                  title="Duplicate"
                >
                  <DocumentDuplicateIcon class="w-5 h-5" />
                </button>
                <button
                  v-if="campaign.status === 'draft'"
                  @click="deleteCampaign(campaign.id)"
                  class="text-red-600 hover:text-red-900"
                  title="Delete"
                >
                  <TrashIcon class="w-5 h-5" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="campaigns.data.length > 0 && campaigns.links && campaigns.links.length > 3" class="px-6 py-4 border-t border-gray-200 bg-white">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-600">
            Showing <span class="font-medium">{{ campaigns.from }}</span> to <span class="font-medium">{{ campaigns.to }}</span> of <span class="font-medium">{{ campaigns.total }}</span> campaigns
          </div>
          <nav class="flex gap-2">
            <Link
              v-for="link in campaigns.links"
              :key="link.label"
              :href="link.url"
              v-html="link.label"
              :class="[
                'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                link.active
                  ? 'bg-teal-600 text-white'
                  : link.url
                  ? 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                  : 'border border-gray-200 text-gray-400 cursor-not-allowed'
              ]"
            />
          </nav>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
