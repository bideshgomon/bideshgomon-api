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

const showFilters = ref(false)

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
  <AdminLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Marketing Campaigns</h1>
              <p class="mt-2 text-sm text-gray-600">
                Create, manage and track your marketing campaigns across multiple channels
              </p>
            </div>
            <div class="flex gap-3">
              <Link
                :href="route('admin.marketing-campaigns.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all"
              >
                <PlusIcon class="h-5 w-5 mr-2" />
                Create Campaign
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats Overview -->
        <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-blue-100 rounded-lg">
                <ChartBarIcon class="w-6 h-6 text-blue-600" />
              </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Campaigns</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats?.total || 0 }}</p>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-green-100 rounded-lg">
                <UserGroupIcon class="w-6 h-6 text-green-600" />
              </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Reach</p>
            <p class="text-3xl font-bold text-gray-900">{{ (stats?.total_recipients || 0).toLocaleString() }}</p>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-purple-100 rounded-lg">
                <ArrowTrendingUpIcon class="w-6 h-6 text-purple-600" />
              </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Avg Open Rate</p>
            <p class="text-3xl font-bold text-gray-900">{{ formatPercentage(stats?.avg_open_rate) }}</p>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="p-3 bg-orange-100 rounded-lg">
                <CursorArrowRaysIcon class="w-6 h-6 text-orange-600" />
              </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Avg Click Rate</p>
            <p class="text-3xl font-bold text-gray-900">{{ formatPercentage(stats?.avg_click_rate) }}</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <!-- Filters -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row gap-3">
              <div class="flex-1">
                <input
                  v-model="filterForm.search"
                  type="text"
                  placeholder="Search campaigns..."
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  @input="applyFilters"
                />
              </div>
              <button
                @click="showFilters = !showFilters"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
              >
                <FunnelIcon class="h-5 w-5 mr-2" />
                {{ showFilters ? 'Hide' : 'Show' }} Filters
              </button>
            </div>

            <div v-if="showFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Campaign Type</label>
                <select 
                  v-model="filterForm.type" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select 
                  v-model="filterForm.status" 
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Campaigns List -->
          <div class="divide-y divide-gray-200">
            <div
              v-for="campaign in campaigns.data"
              :key="campaign.id"
              class="p-6 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-3">
                    <component 
                      :is="getTypeIcon(campaign.type).component" 
                      :class="['w-6 h-6', getTypeIcon(campaign.type).color]"
                    />
                    <div>
                      <Link
                        :href="route('admin.marketing-campaigns.show', campaign.id)"
                        class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors"
                      >
                        {{ campaign.name }}
                      </Link>
                      <div class="flex items-center gap-2 mt-1">
                        <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStatusColor(campaign.status)]">
                          {{ campaign.status }}
                        </span>
                        <span class="text-sm text-gray-500 capitalize">{{ campaign.type }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Stats Grid -->
                  <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mt-4 pt-4 border-t border-gray-100">
                    <div>
                      <p class="text-xs text-gray-500 mb-1">Recipients</p>
                      <p class="text-sm font-semibold text-gray-900">{{ campaign.total_recipients.toLocaleString() }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500 mb-1">Sent</p>
                      <p class="text-sm font-semibold text-gray-900">{{ campaign.sent_count.toLocaleString() }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500 mb-1">Delivered</p>
                      <p class="text-sm font-semibold text-green-600">{{ campaign.delivered_count.toLocaleString() }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500 mb-1">Opened</p>
                      <p class="text-sm font-semibold text-blue-600">{{ campaign.opened_count.toLocaleString() }} <span class="text-xs text-gray-500">({{ formatPercentage(campaign.open_rate) }})</span></p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500 mb-1">Clicked</p>
                      <p class="text-sm font-semibold text-purple-600">{{ campaign.clicked_count.toLocaleString() }} <span class="text-xs text-gray-500">({{ formatPercentage(campaign.click_rate) }})</span></p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500 mb-1">Bounced</p>
                      <p class="text-sm font-semibold text-red-600">{{ campaign.bounced_count.toLocaleString() }} <span class="text-xs text-gray-500">({{ formatPercentage(campaign.bounce_rate) }})</span></p>
                    </div>
                  </div>

                  <!-- Campaign Details -->
                  <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
                    <div v-if="campaign.email_template">
                      <span class="font-medium">Template:</span> {{ campaign.email_template.name }}
                    </div>
                    <div v-if="campaign.scheduled_at">
                      <span class="font-medium">Scheduled:</span> {{ formatDate(campaign.scheduled_at) }}
                    </div>
                    <div v-if="campaign.started_at">
                      <span class="font-medium">Started:</span> {{ formatDate(campaign.started_at) }}
                    </div>
                    <div v-if="campaign.completed_at">
                      <span class="font-medium">Completed:</span> {{ formatDate(campaign.completed_at) }}
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 ml-6">
                  <Link
                    :href="route('admin.marketing-campaigns.show', campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  >
                    <EyeIcon class="w-4 h-4" />
                    View
                  </Link>
                  <Link
                    v-if="['draft', 'scheduled', 'paused'].includes(campaign.status)"
                    :href="route('admin.marketing-campaigns.edit', campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  >
                    <PencilIcon class="w-4 h-4" />
                    Edit
                  </Link>
                  <button
                    v-if="['scheduled', 'sending'].includes(campaign.status)"
                    @click="pauseCampaign(campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                  >
                    <PauseIcon class="w-4 h-4" />
                    Pause
                  </button>
                  <button
                    v-if="campaign.status === 'paused'"
                    @click="resumeCampaign(campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  >
                    <PlayIcon class="w-4 h-4" />
                    Resume
                  </button>
                  <button
                    v-if="['scheduled', 'paused', 'sending'].includes(campaign.status)"
                    @click="cancelCampaign(campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  >
                    <XMarkIcon class="w-4 h-4" />
                    Cancel
                  </button>
                  <button
                    @click="duplicateCampaign(campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                  >
                    <DocumentDuplicateIcon class="w-4 h-4" />
                    Duplicate
                  </button>
                  <button
                    v-if="campaign.status === 'draft'"
                    @click="deleteCampaign(campaign.id)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  >
                    <TrashIcon class="w-4 h-4" />
                    Delete
                  </button>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="campaigns.data.length === 0" class="p-12 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <ChartBarIcon class="w-8 h-8 text-gray-400" />
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">
                No campaigns found
              </h3>
              <p class="text-sm text-gray-600 mb-4">
                Create your first marketing campaign to start engaging with your audience
              </p>
              <Link
                :href="route('admin.marketing-campaigns.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all"
              >
                <PlusIcon class="h-5 w-5 mr-2" />
                Create Your First Campaign
              </Link>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="campaigns.data.length > 0 && campaigns.links && campaigns.links.length > 3" class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-600">
                Showing {{ campaigns.from }} to {{ campaigns.to }} of {{ campaigns.total }} campaigns
              </div>
              <nav class="flex gap-2">
                <Link
                  v-for="link in campaigns.links"
                  :key="link.label"
                  :href="link.url"
                  v-html="link.label"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                    link.active
                      ? 'bg-indigo-600 text-white'
                      : link.url
                      ? 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                      : 'border border-gray-200 text-gray-400 cursor-not-allowed'
                  ]"
                />
              </nav>
            </div>
          </div>
        </div>

        <!-- Info Panel -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <ChartBarIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Campaign Management Tips</h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Draft campaigns can be edited and deleted</li>
                  <li>Scheduled campaigns can be edited, paused, or cancelled</li>
                  <li>Active campaigns can be paused or cancelled</li>
                  <li>Track performance metrics in real-time</li>
                  <li>Duplicate successful campaigns to save time</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
