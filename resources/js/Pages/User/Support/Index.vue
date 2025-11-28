<template>
    <AuthenticatedLayout>
        <Head title="Support Tickets" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">My Support Tickets</h2>
                            <Link :href="route('support.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Ticket
                            </Link>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 flex flex-wrap gap-4">
                            <select v-model="filterStatus" @change="applyFilters" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="awaiting_reply">Awaiting Reply</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>

                            <select v-model="filterCategory" @change="applyFilters" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                <option value="technical">Technical</option>
                                <option value="billing">Billing</option>
                                <option value="general">General</option>
                                <option value="service_inquiry">Service Inquiry</option>
                                <option value="complaint">Complaint</option>
                            </select>
                        </div>

                        <!-- Tickets List -->
                        <div v-if="tickets.data.length > 0" class="space-y-4">
                            <Link v-for="ticket in tickets.data" :key="ticket.id" :href="route('support.show', ticket.id)" class="block border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-mono text-sm text-gray-600">{{ ticket.ticket_number }}</span>
                                            <span :class="statusClass(ticket.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ statusLabel(ticket.status) }}
                                            </span>
                                            <span :class="priorityClass(ticket.priority)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ priorityLabel(ticket.priority) }}
                                            </span>
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                                {{ categoryLabel(ticket.category) }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ ticket.subject }}</h3>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ ticket.message }}</p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                            <span>Created: {{ formatDate(ticket.created_at) }}</span>
                                            <span v-if="ticket.replies_count > 0">{{ ticket.replies_count }} replies</span>
                                            <span v-if="ticket.assigned_to">Assigned to: {{ ticket.assigned_to.name }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No support tickets</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new ticket.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="tickets.data.length > 0" class="mt-6">
                            <Pagination :links="tickets.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    tickets: Object,
    filters: Object,
});

const filterStatus = ref(props.filters?.status || '');
const filterCategory = ref(props.filters?.category || '');

const applyFilters = () => {
    router.get(route('support.index'), {
        status: filterStatus.value,
        category: filterCategory.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusClass = (status) => {
    const classes = {
        open: 'bg-green-100 text-green-800',
        in_progress: 'bg-blue-100 text-blue-800',
        awaiting_reply: 'bg-yellow-100 text-yellow-800',
        resolved: 'bg-purple-100 text-purple-800',
        closed: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const statusLabel = (status) => {
    const labels = {
        open: 'Open',
        in_progress: 'In Progress',
        awaiting_reply: 'Awaiting Reply',
        resolved: 'Resolved',
        closed: 'Closed',
    };
    return labels[status] || status;
};

const priorityClass = (priority) => {
    const classes = {
        urgent: 'bg-red-100 text-red-800',
        high: 'bg-orange-100 text-orange-800',
        normal: 'bg-blue-100 text-blue-800',
        low: 'bg-gray-100 text-gray-800',
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
};

const priorityLabel = (priority) => {
    const labels = {
        urgent: 'Urgent',
        high: 'High',
        normal: 'Normal',
        low: 'Low',
    };
    return labels[priority] || priority;
};

const categoryLabel = (category) => {
    const labels = {
        technical: 'Technical',
        billing: 'Billing',
        general: 'General',
        service_inquiry: 'Service Inquiry',
        complaint: 'Complaint',
    };
    return labels[category] || category;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
