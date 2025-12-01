<template>
    <AuthenticatedLayout>
        <Head :title="`Ticket #${ticket.ticket_number}`" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Ticket Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <Link :href="route('support.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                ← {{ __('Back to Tickets') }}
                            </Link>
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="ticket.status === 'open'"
                                    :href="route('support.edit', ticket.id)"
                                    class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200"
                                >
                                    {{ __('Edit') }}
                                </Link>
                                <button
                                    v-if="ticket.status === 'open' && !hasReplies"
                                    @click="deleteTicket"
                                    :disabled="deletingTicket"
                                    class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-md hover:bg-red-200"
                                    :class="{ 'opacity-50 cursor-not-allowed': deletingTicket }"
                                >
                                    {{ __('Delete') }}
                                </button>
                                <button
                                    v-if="ticket.status !== 'closed' && ticket.status !== 'resolved'"
                                    @click="closeTicket"
                                    :disabled="closingTicket"
                                    class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                                    :class="{ 'opacity-50 cursor-not-allowed': closingTicket }"
                                >
                                    {{ __('Close Ticket') }}
                                </button>
                            </div>
                        </div>

                        <div class="flex items-start justify-between">
                            <div>
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
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ ticket.subject }}</h1>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span>{{ __('Created') }}: {{ formatDate(ticket.created_at) }}</span>
                                    <span v-if="ticket.assigned_to">{{ __('Assigned to') }}: {{ ticket.assigned_to.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Original Message -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ (ticket.user.name || '').charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-900">{{ ticket.user.name }}</span>
                                    <span class="text-xs text-gray-500">{{ formatDate(ticket.created_at) }}</span>
                                </div>
                                <div class="prose prose-sm max-w-none text-gray-700">
                                    {{ ticket.message }}
                                </div>
                                <div v-if="ticket.attachments && ticket.attachments.length > 0" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">{{ __('Attachments') }}:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <a
                                            v-for="(attachment, index) in ticket.attachments"
                                            :key="index"
                                            :href="`/storage/${attachment.path}`"
                                            target="_blank"
                                            class="flex items-center gap-2 px-3 py-1 text-xs bg-gray-100 rounded-md hover:bg-gray-200"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            {{ attachment.name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Replies -->
                <div v-for="reply in ticket.replies" :key="reply.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div 
                                    :class="reply.is_staff_reply ? 'bg-green-600' : 'bg-blue-600'"
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
                                >
                                    {{ (reply.user.name || '').charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-900">{{ reply.user.name }}</span>
                                    <span v-if="reply.is_staff_reply" class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                        {{ __('Staff') }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <div class="prose prose-sm max-w-none text-gray-700">
                                    {{ reply.message }}
                                </div>
                                <div v-if="reply.attachments && reply.attachments.length > 0" class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">{{ __('Attachments') }}:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <a
                                            v-for="(attachment, index) in reply.attachments"
                                            :key="index"
                                            :href="`/storage/${attachment.path}`"
                                            target="_blank"
                                            class="flex items-center gap-2 px-3 py-1 text-xs bg-gray-100 rounded-md hover:bg-gray-200"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            {{ attachment.name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div v-if="ticket.status !== 'closed'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Add Reply') }}</h3>
                        <form @submit.prevent="submitReply">
                            <div class="mb-4">
                                <textarea
                                    v-model="replyForm.message"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': replyForm.errors.message }"
                                    placeholder="Type your reply..."
                                    required
                                ></textarea>
                                <p v-if="replyForm.errors.message" class="mt-1 text-sm text-red-600">{{ replyForm.errors.message }}</p>
                            </div>

                            <div class="mb-4">
                                <input
                                    type="file"
                                    @change="handleReplyFileUpload"
                                    multiple
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ __('Optional: Max 5 files, 10MB each') }}</p>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="replyForm.processing"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :class="{ 'opacity-50 cursor-not-allowed': replyForm.processing }"
                                >
                                    <span v-if="replyForm.processing">{{ __('Sending...') }}</span>
                                    <span v-else>{{ __('Send Reply') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Rating Form -->
                <div v-if="ticket.status === 'resolved' && !ticket.satisfaction_rating" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Rate Your Experience') }}</h3>
                    <form @submit.prevent="submitRating">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('How satisfied are you with the support?') }}</label>
                            <div class="flex gap-2">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    @click="ratingForm.rating = star"
                                    class="text-3xl transition"
                                    :class="star <= ratingForm.rating ? 'text-yellow-400' : 'text-gray-300'"
                                >
                                    ★
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Additional Feedback (Optional)') }}</label>
                            <textarea
                                v-model="ratingForm.feedback"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Tell us more about your experience..."
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="!ratingForm.rating || ratingForm.processing"
                            class="px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 transition ease-in-out duration-150"
                            :class="{ 'opacity-50 cursor-not-allowed': !ratingForm.rating || ratingForm.processing }"
                        >
                            {{ __('Submit Rating') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    ticket: Object,
});

const closingTicket = ref(false);
const deletingTicket = ref(false);
const hasReplies = ref(props.ticket.replies && props.ticket.replies.length > 0);

const replyForm = useForm({
    message: '',
    attachments: [],
});

const ratingForm = useForm({
    rating: 0,
    feedback: '',
});

const handleReplyFileUpload = (event) => {
    const files = Array.from(event.target.files);
    if (files.length > 5) {
        alert('Maximum 5 files allowed');
        event.target.value = '';
        return;
    }
    replyForm.attachments = files;
};

const submitReply = () => {
    replyForm.post(route('support.reply', props.ticket.id), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset();
        },
    });
};

const closeTicket = () => {
    if (!confirm('Are you sure you want to close this ticket?')) {
        return;
    }

    closingTicket.value = true;
    router.post(route('support.close', props.ticket.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            closingTicket.value = false;
        },
    });
};

const deleteTicket = () => {
    if (!confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
        return;
    }

    deletingTicket.value = true;
    router.delete(route('support.destroy', props.ticket.id), {
        onFinish: () => {
            deletingTicket.value = false;
        },
    });
};

const submitRating = () => {
    ratingForm.post(route('support.rate', props.ticket.id), {
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
