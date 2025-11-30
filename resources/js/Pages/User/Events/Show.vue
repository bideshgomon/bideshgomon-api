<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    event: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (time) => {
    if (!time) return '';
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    return `${displayHour}:${minutes} ${ampm}`;
};

const getEventTypeColor = (type) => {
    const colors = {
        seminar: 'bg-blue-100 text-blue-800 border-blue-200',
        workshop: 'bg-blue-100 text-blue-800 border-blue-200',
        fair: 'bg-green-100 text-green-800 border-green-200',
        consultation: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        webinar: 'bg-blue-100 text-blue-800 border-blue-200',
    };
    return colors[type] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getEventTypeLabel = (type) => {
    const labels = {
        seminar: 'Seminar',
        workshop: 'Workshop',
        fair: 'Fair',
        consultation: 'Consultation',
        webinar: 'Webinar',
    };
    return labels[type] || 'Event';
};

const isUpcoming = () => {
    return new Date(props.event.event_date) >= new Date();
};

const hasRegistrationClosed = () => {
    if (!props.event.registration_deadline) return false;
    return new Date(props.event.registration_deadline) < new Date();
};

const getDaysUntilEvent = () => {
    const eventDate = new Date(props.event.event_date);
    const today = new Date();
    const diffTime = eventDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};
</script>

<template>
    <Head :title="event.title" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Back Button -->
                <div class="mb-6">
                    <Link
                        :href="route('events.index')"
                        class="inline-flex items-center text-blue-600 hover:text-blue-700"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Events
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Event Image -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div v-if="event.image" class="h-96 bg-gray-200">
                                <img :src="`/storage/${event.image}`" :alt="event.title" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="h-96 bg-sky-500 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Event Title and Description -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <span :class="['px-3 py-1 rounded-full text-sm font-semibold border', getEventTypeColor(event.event_type)]">
                                        {{ getEventTypeLabel(event.event_type) }}
                                    </span>
                                    <span v-if="event.is_online" class="px-3 py-1 bg-cyan-100 text-cyan-800 rounded-full text-sm font-semibold border border-cyan-200">
                                        🌐 Online Event
                                    </span>
                                    <span v-if="isUpcoming()" class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold border border-green-200">
                                        📅 Upcoming
                                    </span>
                                    <span v-else class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold border border-gray-200">
                                        Past Event
                                    </span>
                                </div>

                                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ event.title }}</h1>

                                <div class="prose max-w-none text-gray-700">
                                    <p class="whitespace-pre-wrap">{{ event.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Details</h2>
                                
                                <div class="space-y-4">
                                    <!-- Date and Time -->
                                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900">Date & Time</div>
                                            <div class="text-gray-700">{{ formatDate(event.event_date) }}</div>
                                            <div class="text-gray-700">{{ formatTime(event.event_time) }}
                                                <span v-if="event.end_time"> - {{ formatTime(event.end_time) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div v-if="event.location" class="flex items-start gap-3 p-4 bg-green-50 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900">Location</div>
                                            <div class="text-gray-700">{{ event.location }}</div>
                                        </div>
                                    </div>

                                    <!-- Registration Deadline -->
                                    <div v-if="event.registration_deadline" class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg">
                                        <svg class="w-6 h-6 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900">Registration Deadline</div>
                                            <div class="text-gray-700">{{ formatDate(event.registration_deadline) }}</div>
                                            <div v-if="hasRegistrationClosed()" class="text-red-600 text-sm mt-1">
                                                Registration has closed
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Max Participants -->
                                    <div v-if="event.max_participants" class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-900">Capacity</div>
                                            <div class="text-gray-700">Limited to {{ event.max_participants }} participants</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Registration Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Registration</h3>
                                
                                <!-- Upcoming Event -->
                                <div v-if="isUpcoming() && !hasRegistrationClosed()">
                                    <div class="bg-sky-500 text-white p-4 rounded-lg mb-4 text-center">
                                        <div class="text-3xl font-bold">{{ getDaysUntilEvent() }}</div>
                                        <div class="text-sm">{{ getDaysUntilEvent() === 1 ? 'Day' : 'Days' }} Until Event</div>
                                    </div>
                                    
                                    <button
                                        class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-colors"
                                    >
                                        Register for This Event
                                    </button>
                                    
                                    <p class="text-sm text-gray-500 mt-3 text-center">
                                        Registration is open until {{ formatDate(event.registration_deadline || event.event_date) }}
                                    </p>
                                </div>

                                <!-- Registration Closed -->
                                <div v-else-if="isUpcoming() && hasRegistrationClosed()">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                        <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="font-semibold text-red-800">Registration Closed</p>
                                        <p class="text-sm text-red-600 mt-1">
                                            Registration deadline has passed
                                        </p>
                                    </div>
                                </div>

                                <!-- Past Event -->
                                <div v-else>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="font-semibold text-gray-700">This Event Has Ended</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            This event took place on {{ formatDate(event.event_date) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Online Event Link (if available) -->
                                <div v-if="event.is_online && event.meeting_link && isUpcoming()" class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-medium">Online Meeting Link:</span>
                                    </div>
                                    <a
                                        :href="event.meeting_link"
                                        target="_blank"
                                        class="block text-blue-600 hover:text-blue-700 text-sm break-all underline"
                                    >
                                        {{ event.meeting_link }}
                                    </a>
                                </div>

                                <!-- Share Event -->
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Share this event:</p>
                                    <div class="flex gap-2">
                                        <button class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-sm">
                                            Facebook
                                        </button>
                                        <button class="flex-1 px-3 py-2 bg-cyan-50 text-cyan-600 rounded hover:bg-cyan-100 text-sm">
                                            Twitter
                                        </button>
                                        <button class="flex-1 px-3 py-2 bg-green-50 text-green-600 rounded hover:bg-green-100 text-sm">
                                            WhatsApp
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Info -->
                        <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Need Help?</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Have questions about this event? Contact our support team.
                                </p>
                                <Link
                                    :href="route('support.create')"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium"
                                >
                                    Contact Support
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
