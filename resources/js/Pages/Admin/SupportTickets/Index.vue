<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
    tickets: Object,
    stats: Object,
    filters: Object,
})

const { formatDate, formatTime } = useBangladeshFormat()

const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    category: props.filters.category || '',
})

const search = () => {
    searchForm.get(route('admin.support-tickets.index'), {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    searchForm.reset()
    search()
}

const deleteTicket = (ticketId) => {
    if (confirm('Are you sure you want to delete this support ticket?')) {
        router.delete(route('admin.support-tickets.destroy', ticketId), {
            preserveScroll: true,
        })
    }
}

const getStatusBadgeClass = (status) => {
    const classes = {
        open: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        resolved: 'bg-green-100 text-green-800',
        closed: 'bg-gray-100 text-gray-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityBadgeClass = (priority) => {
    const classes = {
        low: 'bg-gray-100 text-gray-800',
        medium: 'bg-blue-100 text-blue-800',
        high: 'bg-orange-100 text-orange-800',
        urgent: 'bg-red-100 text-red-800',
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <Head title="Support Tickets" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="md:flex md:items-center md:justify-between mb-6">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate dark:text-white">
                            Support Tickets
                        </h2>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <Link
                            :href="route('admin.support-tickets.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Ticket
                        </Link>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tickets</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total || 0 }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Open</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.open || 0 }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resolved</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.resolved || 0 }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgent</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.urgent || 0 }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <form @submit.prevent="search" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search tickets..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select
                                v-model="searchForm.status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                            <select
                                v-model="searchForm.priority"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select
                                v-model="searchForm.category"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Categories</option>
                                <option value="technical">Technical</option>
                                <option value="billing">Billing</option>
                                <option value="general">General</option>
                                <option value="visa">Visa</option>
                                <option value="booking">Booking</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                            >
                                Filter
                            </button>
                            <button
                                type="button"
                                @click="clearFilters"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                            >
                                Clear
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tickets Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        #{{ ticket.id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ ticket.subject }}</div>
                                        <div class="text-gray-500 dark:text-gray-400 truncate" style="max-width: 300px;">
                                            {{ ticket.message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ ticket.user?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getPriorityBadgeClass(ticket.priority)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ ticket.priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusBadgeClass(ticket.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ (ticket?.status || '').replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(ticket.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="route('admin.support-tickets.show', ticket.id)"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="route('admin.support-tickets.edit', ticket.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="deleteTicket(ticket.id)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!tickets.data || tickets.data.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No support tickets</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new support ticket.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="tickets.data && tickets.data.length > 0" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="tickets.prev_page_url"
                                    :href="tickets.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="tickets.next_page_url"
                                    :href="tickets.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing
                                        <span class="font-medium">{{ tickets.from || 0 }}</span>
                                        to
                                        <span class="font-medium">{{ tickets.to || 0 }}</span>
                                        of
                                        <span class="font-medium">{{ tickets.total || 0 }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <Link
                                            v-if="tickets.prev_page_url"
                                            :href="tickets.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
                                        >
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        <Link
                                            v-if="tickets.next_page_url"
                                            :href="tickets.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
                                        >
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
