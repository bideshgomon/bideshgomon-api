<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  campaign: Object,
})

const getStatusColor = (status) => {
  const colors = {
    draft: 'gray',
    scheduled: 'blue',
    sending: 'yellow',
    sent: 'green',
    paused: 'orange',
    cancelled: 'red',
  }
  return colors[status] || 'gray'
}

const formatDate = (date) => {
  if (!date) {return 'N/A'}
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatPercentage = (value) => value ? `${value.toFixed(2)  }%` : '0.00%'

const getProgressPercentage = () => {
  if (props.campaign.total_recipients === 0) {return 0}
  return ((props.campaign.sent_count / props.campaign.total_recipients) * 100).toFixed(1)
}
</script>

<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <Link
              :href="route('admin.marketing-campaigns.index')"
              class="text-blue-600 hover:text-brand-red-dark text-sm mb-2 inline-block"
            >
              ← Back to Campaigns
            </Link>
            <h1 class="text-3xl font-bold text-gray-900">
              {{ campaign.name }}
            </h1>
            <div class="flex flex-wrap items-center gap-3 mt-2">
              <span
                :class="`inline-flex flex-wrap items-center px-3 py-1 rounded-full text-sm font-medium bg-${getStatusColor(campaign.status)}-100 text-${getStatusColor(campaign.status)}-800`"
              >
                {{ campaign.status }}
              </span>
              <span class="text-sm text-gray-600">{{ campaign.type }}</span>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link
              v-if="['draft', 'scheduled', 'paused'].includes(campaign.status)"
              :href="route('admin.marketing-campaigns.edit', campaign.id)"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 transition-colors text-white rounded-lg hover:bg-brand-red-dark transition"
            >
              Edit Campaign
            </Link>
          </div>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">
            Total Recipients
          </div>
          <div class="text-3xl font-bold text-gray-900">
            {{ campaign.total_recipients.toLocaleString() }}
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">
            Sent
          </div>
          <div class="text-3xl font-bold text-blue-600">
            {{ campaign.sent_count.toLocaleString() }}
          </div>
          <div class="text-sm text-gray-500 mt-1">
            {{ getProgressPercentage() }}% of total
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">
            Delivered
          </div>
          <div class="text-3xl font-bold text-green-600">
            {{ campaign.delivered_count.toLocaleString() }}
          </div>
          <div class="text-sm text-gray-500 mt-1">
            {{ campaign.sent_count > 0 ? ((campaign.delivered_count / campaign.sent_count) * 100).toFixed(1) : 0 }}% delivery rate
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">
            Bounced
          </div>
          <div class="text-3xl font-bold text-red-600">
            {{ campaign.bounced_count.toLocaleString() }}
          </div>
          <div class="text-sm text-gray-500 mt-1">
            {{ formatPercentage(campaign.bounce_rate) }} bounce rate
          </div>
        </div>
      </div>

      <!-- Engagement Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="text-lg font-semibold text-gray-900">
              Opens
            </div>
            <div class="text-2xl">
              👁️
            </div>
          </div>
          <div class="text-3xl font-bold text-purple-600 mb-2">
            {{ campaign.opened_count.toLocaleString() }}
          </div>
          <div class="text-sm text-gray-600">
            {{ formatPercentage(campaign.open_rate) }} open rate
          </div>
          <div class="mt-4 bg-gray-200 rounded-full h-2">
            <div
              class="bg-brand-secondary h-2 rounded-full"
              :style="{ width: campaign.open_rate + '%' }"
            ></div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="text-lg font-semibold text-gray-900">
              Clicks
            </div>
            <div class="text-2xl">
              👆
            </div>
          </div>
          <div class="text-3xl font-bold text-blue-600 mb-2">
            {{ campaign.clicked_count.toLocaleString() }}
          </div>
          <div class="text-sm text-gray-600">
            {{ formatPercentage(campaign.click_rate) }} click rate
          </div>
          <div class="mt-4 bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-600 hover:bg-blue-700 transition-colors h-2 rounded-full"
              :style="{ width: campaign.click_rate + '%' }"
            ></div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="text-lg font-semibold text-gray-900">
              Unsubscribes
            </div>
            <div class="text-2xl">
              🚫
            </div>
          </div>
          <div class="text-3xl font-bold text-orange-600 mb-2">
            {{ campaign.unsubscribed_count.toLocaleString() }}
          </div>
          <div class="text-sm text-gray-600">
            {{ campaign.delivered_count > 0 ? ((campaign.unsubscribed_count / campaign.delivered_count) * 100).toFixed(2) : 0 }}% unsubscribe rate
          </div>
          <div class="mt-4 bg-gray-200 rounded-full h-2">
            <div
              class="bg-orange-600 h-2 rounded-full"
              :style="{ width: (campaign.delivered_count > 0 ? (campaign.unsubscribed_count / campaign.delivered_count) * 100 : 0) + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Campaign Details -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Campaign Details
          </h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Type:</span>
              <span class="font-medium capitalize">{{ campaign.type }}</span>
            </div>
            <div v-if="campaign.email_template" class="flex justify-between">
              <span class="text-gray-600">Template:</span>
              <span class="font-medium">{{ campaign.email_template.name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Audience:</span>
              <span class="font-medium capitalize">{{ campaign.audience_type.replace('_', ' ') }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">A/B Test:</span>
              <span class="font-medium">{{ campaign.is_ab_test ? 'Yes' : 'No' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Created By:</span>
              <span class="font-medium">{{ campaign.creator?.name || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Timeline
          </h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Created:</span>
              <span class="font-medium">{{ formatDate(campaign.created_at) }}</span>
            </div>
            <div v-if="campaign.scheduled_at" class="flex justify-between">
              <span class="text-gray-600">Scheduled:</span>
              <span class="font-medium">{{ formatDate(campaign.scheduled_at) }}</span>
            </div>
            <div v-if="campaign.started_at" class="flex justify-between">
              <span class="text-gray-600">Started:</span>
              <span class="font-medium">{{ formatDate(campaign.started_at) }}</span>
            </div>
            <div v-if="campaign.completed_at" class="flex justify-between">
              <span class="text-gray-600">Completed:</span>
              <span class="font-medium">{{ formatDate(campaign.completed_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="font-medium">{{ formatDate(campaign.updated_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Progress Bar (for active campaigns) -->
      <div v-if="['scheduled', 'sending'].includes(campaign.status)" class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          Campaign Progress
        </h2>
        <div class="mb-2 flex justify-between text-sm">
          <span class="text-gray-600">Sending Progress</span>
          <span class="font-medium">{{ getProgressPercentage() }}%</span>
        </div>
        <div class="bg-gray-200 rounded-full h-4">
          <div
            class="bg-blue-600 hover:bg-blue-700 transition-colors h-4 rounded-full transition-all duration-500"
            :style="{ width: getProgressPercentage() + '%' }"
          ></div>
        </div>
        <div class="mt-2 text-sm text-gray-600">
          {{ campaign.sent_count.toLocaleString() }} of {{ campaign.total_recipients.toLocaleString() }} sent
        </div>
      </div>

      <!-- Audience Filters (if segment) -->
      <div v-if="campaign.audience_type === 'segment' && campaign.audience_filters && Object.keys(campaign.audience_filters).length > 0" class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          Audience Filters
        </h2>
        <div class="bg-gray-50 rounded-lg p-4">
          <pre class="text-sm text-gray-700">{{ JSON.stringify(campaign.audience_filters, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
