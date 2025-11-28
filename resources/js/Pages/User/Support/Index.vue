<template>
    <AuthenticatedLayout>
        <Head title="Support Tickets" />

        <div class="py-rhythm-xl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <RhythmicCard variant="ocean" class="mb-rhythm-lg">
                    <template #icon>
                        <ChatBubbleLeftRightIcon class="w-6 h-6 text-white" />
                    </template>
                    <template #title>
                        <h2 class="font-display font-bold text-2xl text-gray-800">My Support Tickets</h2>
                    </template>
                    <template #description>
                        <p class="text-gray-600">Manage your support tickets and get help from our team</p>
                    </template>
                    <template #footer>
                        <FlowButton
                            variant="primary"
                            :href="route('support.create')"
                            class="w-full sm:w-auto"
                        >
                            <template #icon>
                                <PlusIcon class="w-5 h-5" />
                            </template>
                            Create New Ticket
                        </FlowButton>
                    </template>
                </RhythmicCard>

                <div class="bg-white overflow-hidden shadow-rhythmic-md rounded-xl">
                    <div class="p-rhythm-md">

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
                        <div v-if="tickets.data.length > 0" class="space-y-rhythm-md">
                            <Link v-for="ticket in tickets.data" :key="ticket.id" :href="route('support.show', ticket.id)" class="block group">
                                <RhythmicCard variant="default" class="transition-all duration-200 hover:shadow-rhythmic-lg hover:scale-[1.01]">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-rhythm-sm flex-wrap">
                                                <span class="font-mono text-sm text-gray-600 font-semibold">{{ ticket.ticket_number }}</span>
                                                <StatusBadge :status="ticket.status">
                                                    {{ statusLabel(ticket.status) }}
                                                </StatusBadge>
                                                <StatusBadge :status="ticket.priority === 'urgent' ? 'danger' : ticket.priority === 'high' ? 'warning' : 'info'">
                                                    {{ priorityLabel(ticket.priority) }}
                                                </StatusBadge>
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full font-medium">
                                                    {{ categoryLabel(ticket.category) }}
                                                </span>
                                            </div>
                                            <h3 class="font-display font-bold text-lg text-gray-900 mb-rhythm-xs group-hover:text-ocean-600 transition-colors">{{ ticket.subject }}</h3>
                                            <p class="text-sm text-gray-600 line-clamp-2 mb-rhythm-sm">{{ ticket.message }}</p>
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span>Created: {{ formatDate(ticket.created_at) }}</span>
                                                <span v-if="ticket.replies_count > 0" class="font-medium text-ocean-600">{{ ticket.replies_count }} replies</span>
                                                <span v-if="ticket.assigned_to">Assigned to: {{ ticket.assigned_to.name }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-ocean-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </RhythmicCard>
                            </Link>
                        </div>

                        <RhythmicCard v-else variant="light" class="text-center py-rhythm-xl">
                            <div class="w-16 h-16 bg-ocean-500 rounded-full flex items-center justify-center mx-auto mb-rhythm-md shadow-rhythmic-md">
                                <ChatBubbleLeftRightIcon class="w-8 h-8 text-white" />
                            </div>
                            <h3 class="font-display font-bold text-lg text-gray-900 mb-rhythm-xs">No support tickets yet</h3>
                            <p class="text-gray-600 mb-rhythm-md">Get started by creating your first support ticket</p>
                            <FlowButton variant="primary" :href="route('support.create')">
                                <template #icon>
                                    <PlusIcon class="w-5 h-5" />
                                </template>
                                Create New Ticket
                            </FlowButton>
                        </RhythmicCard>

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
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import { ChatBubbleLeftRightIcon, PlusIcon } from '@heroicons/vue/24/solid';

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
