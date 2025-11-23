<script setup>
import { ref, onMounted, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { BellIcon } from '@heroicons/vue/24/outline'
import { BellAlertIcon } from '@heroicons/vue/24/solid'

const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user)

const notifications = ref([])
const unreadCount = ref(0)
const showDropdown = ref(false)
const loading = ref(false)

const fetchNotifications = async () => {
  // Skip if user is not authenticated
  if (!isAuthenticated.value) {return}

  try {
    // Get CSRF cookie first for session-based auth
    await axios.get('/sanctum/csrf-cookie').catch(() => {})
    
    const response = await axios.get(route('api.notifications.recent'))
    notifications.value = response.data.notifications
    unreadCount.value = response.data.unread_count
  } catch (error) {
    // Silently fail - don't log 401/403 errors to avoid console spam
    if (error.response?.status !== 401 && error.response?.status !== 403) {
      console.error('Failed to fetch notifications:', error)
    }
  }
}

const fetchUnreadCount = async () => {
  // Skip if user is not authenticated
  if (!isAuthenticated.value) {return}

  try {
    // Get CSRF cookie first for session-based auth
    await axios.get('/sanctum/csrf-cookie').catch(() => {})
    
    const response = await axios.get(route('api.notifications.unread-count'))
    unreadCount.value = response.data.unread_count
  } catch (error) {
    // Silently fail - don't log 401/403 errors to avoid console spam
    if (error.response?.status !== 401 && error.response?.status !== 403) {
      console.error('Failed to fetch unread count:', error)
    }
    // Stop polling if unauthorized
    if (error.response?.status === 401 || error.response?.status === 403) {
      return false
    }
  }
}

const markAsRead = async (id) => {
  try {
    // Initialize Sanctum CSRF cookie for SPA authentication
    await axios.get('/sanctum/csrf-cookie')

    await axios.post(route('api.notifications.mark-read', { id }))
    await fetchNotifications()
  } catch (error) {
    console.error('Failed to mark as read:', error)
  }
}

const markAllAsRead = async () => {
  loading.value = true
  try {
    // Initialize Sanctum CSRF cookie for SPA authentication
    await axios.get('/sanctum/csrf-cookie')

    await axios.post(route('api.notifications.mark-all-read'))
    await fetchNotifications()
  } catch (error) {
    console.error('Failed to mark all as read:', error)
  } finally {
    loading.value = false
  }
}

const viewAllNotifications = () => {
  showDropdown.value = false
  router.visit('/notifications')
}

const getNotificationIcon = (notification) => {
  // Use icon from notification if available
  if (notification.icon) {
    const icons = {
      'bell': '🔔',
      'check-circle': '✅',
      'x-circle': '❌',
      'exclamation': '⚠️',
      'currency-dollar': '💰',
      'document': '📄',
      'badge-check': '✓',
      'gift': '🎁',
      'calendar': '📅',
      'chat': '💬',
    }
    return icons[notification.icon] || '🔔'
  }
  
  // Fallback to type-based icons (legacy support)
  const typeIcons = {
    referral_earned: '🎉',
    reward_received: '💰',
    cashout_approved: '✅',
    cashout_rejected: '❌',
    cashout_completed: '🎊',
    daily_login_bonus: '🎁',
    badge_earned: '🏆',
    level_up: '⭐',
    visa_update: '🛂',
    message_received: '💬',
    payment_success: '💳',
    payment_failed: '❌',
    application_approved: '✅',
    application_rejected: '❌',
    document_verified: '✓',
    document_rejected: '❌',
    appointment_scheduled: '📅',
    wallet_credited: '💰',
    admin_message: '📢',
  }
  return typeIcons[notification.type] || '🔔'
}

const formatTime = (date) => {
  const now = new Date()
  const notifDate = new Date(date)
  const diffMs = now - notifDate
  const diffMins = Math.floor(diffMs / 60000)

  if (diffMins < 1) {return 'Just now'}
  if (diffMins < 60) {return `${diffMins}m ago`}

  const diffHours = Math.floor(diffMins / 60)
  if (diffHours < 24) {return `${diffHours}h ago`}

  const diffDays = Math.floor(diffHours / 24)
  if (diffDays < 7) {return `${diffDays}d ago`}

  return notifDate.toLocaleDateString()
}

onMounted(() => {
  // Only fetch if user is authenticated
  if (isAuthenticated.value) {
    fetchUnreadCount()

    // Poll for new notifications every 30 seconds
    setInterval(fetchUnreadCount, 30000)
  }
})

const toggleDropdown = () => {
  if (!isAuthenticated.value) {return}

  showDropdown.value = !showDropdown.value
  if (showDropdown.value && notifications.value.length === 0) {
    fetchNotifications()
  }
}
</script>

<template>
  <div class="relative">
    <!-- Bell Icon -->
    <button
      class="relative p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors"
      @click="toggleDropdown"
    >
      <BellAlertIcon v-if="unreadCount > 0" class="h-6 w-6 text-indigo-600 animate-bounce" />
      <BellIcon v-else class="h-6 w-6" />

      <!-- Unread Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <div
      v-if="showDropdown"
      class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-2xl border border-gray-200 z-50"
      @click.stop
    >
      <!-- Header -->
      <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">
          Notifications
        </h3>
        <button
          v-if="unreadCount > 0"
          :disabled="loading"
          class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
          @click="markAllAsRead"
        >
          {{ loading ? 'Marking...' : 'Mark all read' }}
        </button>
      </div>

      <!-- Notifications List -->
      <div class="max-h-96 overflow-y-auto">
        <div v-if="notifications.length > 0" class="divide-y divide-gray-100">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
              'px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors',
              !notification.is_read ? 'bg-indigo-50' : ''
            ]"
            @click="!notification.is_read && markAsRead(notification.id)"
          >
            <div class="flex items-start gap-3">
              <div class="text-2xl flex-shrink-0">
                {{ getNotificationIcon(notification) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">
                  {{ notification.title }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                  {{ notification.message }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  {{ formatTime(notification.created_at) }}
                </p>
              </div>
              <div v-if="!notification.is_read" class="flex-shrink-0">
                <div class="h-2 w-2 bg-indigo-600 rounded-full"></div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="px-4 py-12 text-center">
          <BellIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            No notifications
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            You're all caught up!
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
        <button
          class="w-full text-center text-sm text-indigo-600 hover:text-indigo-700 font-medium"
          @click="viewAllNotifications"
        >
          View all notifications
        </button>
      </div>
    </div>

    <!-- Backdrop -->
    <div
      v-if="showDropdown"
      class="fixed inset-0 z-40"
      @click="showDropdown = false"
    ></div>
  </div>
</template>
