<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { BellIcon, XMarkIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const notifications = ref([]);
const unreadCount = ref(0);
const showNotifications = ref(false);

let channel = null;

const addNotification = (notification) => {
  const id = Date.now() + Math.random();
  notifications.value.unshift({
    id,
    ...notification,
    read: false,
    timestamp: new Date(),
  });
  unreadCount.value++;
  
  // Show toast
  showToast(notification);
  
  // Auto-remove after 10 seconds
  setTimeout(() => {
    removeNotification(id);
  }, 10000);
};

const showToast = (notification) => {
  // Create toast element
  const toast = document.createElement('div');
  toast.className = 'fixed top-20 right-4 z-50 animate-slide-in';
  toast.innerHTML = `
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 max-w-sm border-l-4 border-blue-600">
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
          </svg>
        </div>
        <div class="flex-1">
          <p class="text-sm font-medium text-gray-900 dark:text-white">${notification.message}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Just now</p>
        </div>
      </div>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  // Auto-remove toast after 5 seconds
  setTimeout(() => {
    toast.classList.add('animate-slide-out');
    setTimeout(() => toast.remove(), 300);
  }, 5000);
};

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id);
  if (index > -1) {
    notifications.value.splice(index, 1);
  }
};

const markAsRead = (id) => {
  const notification = notifications.value.find(n => n.id === id);
  if (notification && !notification.read) {
    notification.read = true;
    unreadCount.value = Math.max(0, unreadCount.value - 1);
  }
};

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true);
  unreadCount.value = 0;
};

const clearAll = () => {
  notifications.value = [];
  unreadCount.value = 0;
};

onMounted(() => {
  // Subscribe to admin notifications channel
  if (window.Echo) {
    channel = window.Echo.private('admin-notifications')
      .listen('.service-application.created', (e) => {
        addNotification({
          type: 'success',
          message: e.message,
          data: e,
        });
      })
      .listen('.quote.created', (e) => {
        addNotification({
          type: 'info',
          message: `New quote received for ${e.service_name}`,
          data: e,
        });
      })
      .listen('.booking.created', (e) => {
        addNotification({
          type: 'success',
          message: `New booking created by ${e.user_name}`,
          data: e,
        });
      });
  }
});

onUnmounted(() => {
  if (channel) {
    window.Echo.leave('admin-notifications');
  }
});
</script>

<template>
  <div class="relative">
    <!-- Notification Bell Button -->
    <button
      @click="showNotifications = !showNotifications"
      class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all transform hover:scale-105"
      title="Notifications"
    >
      <BellIcon class="w-6 h-6" />
      
      <!-- Unread Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full animate-pulse"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Notifications Dropdown -->
    <div
      v-if="showNotifications"
      class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 z-50"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          Notifications
        </h3>
        <div class="flex items-center gap-2">
          <button
            v-if="notifications.length > 0"
            @click="markAllAsRead"
            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
          >
            Mark all read
          </button>
          <button
            @click="showNotifications = false"
            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Notifications List -->
      <div class="max-h-96 overflow-y-auto">
        <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
          <BellIcon class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
          <p class="text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
        </div>

        <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
              'px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer',
              !notification.read && 'bg-blue-50 dark:bg-blue-900/10'
            ]"
            @click="markAsRead(notification.id)"
          >
            <div class="flex items-start gap-3">
              <!-- Icon based on type -->
              <div
                :class="[
                  'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center',
                  notification.type === 'success' && 'bg-green-100 dark:bg-green-900/30',
                  notification.type === 'info' && 'bg-blue-100 dark:bg-blue-900/30',
                  notification.type === 'warning' && 'bg-yellow-100 dark:bg-yellow-900/30',
                ]"
              >
                <CheckCircleIcon
                  v-if="notification.type === 'success'"
                  class="w-6 h-6 text-green-600 dark:text-green-400"
                />
                <ExclamationCircleIcon
                  v-else
                  class="w-6 h-6 text-blue-600 dark:text-blue-400"
                />
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ notification.message }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ formatTime(notification.timestamp) }}
                </p>
              </div>

              <!-- Unread indicator -->
              <div v-if="!notification.read" class="flex-shrink-0">
                <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        v-if="notifications.length > 0"
        class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"
      >
        <button
          @click="clearAll"
          class="w-full text-sm text-center text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400"
        >
          Clear all notifications
        </button>
      </div>
    </div>
  </div>
</template>

<script>
function formatTime(timestamp) {
  const now = new Date();
  const diff = Math.floor((now - timestamp) / 1000); // seconds

  if (diff < 60) return 'Just now';
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  return `${Math.floor(diff / 86400)}d ago`;
}
</script>

<style scoped>
@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slide-out {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
  animation: slide-out 0.3s ease-in;
}
</style>
