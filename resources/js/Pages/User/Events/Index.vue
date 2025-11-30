<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    events: Object,
    featuredEvents: Array,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedType = ref(props.filters.type || '');
const selectedTime = ref(props.filters.time || 'upcoming');

const applyFilters = () => {
    router.get(route('events.index'), {
        search: searchQuery.value,
        type: selectedType.value,
        time: selectedTime.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedType.value = '';
    selectedTime.value = 'upcoming';
    applyFilters();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
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
        seminar: 'bg-blue-100 text-blue-800',
        workshop: 'bg-blue-100 text-blue-800',
        fair: 'bg-green-100 text-green-800',
        consultation: 'bg-yellow-100 text-yellow-800',
        webinar: 'bg-blue-100 text-blue-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
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
</script>

<template>
    <Head title="Events" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-sky-500 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-8 text-white">
                    <h1 class="text-4xl font-bold mb-2">Upcoming Events</h1>
                    <p class="text-blue-100">Join our seminars, workshops, and fairs to expand your opportunities.</p>
                </div>

                <!-- Featured Events -->
                <div v-if="featuredEvents.length > 0 && selectedTime === 'upcoming'" class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Featured Events</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Link
                            v-for="event in featuredEvents"
                            :key="event.id"
                            :href="route('events.show', event.slug)"
                            class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
                        >
                            <div v-if="event.image" class="h-48 bg-gray-200">
                                <img :src="`/storage/${event.image}`" :alt="event.title" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="h-48 bg-sky-500 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span :class="['px-2 py-1 rounded text-xs font-semibold', getEventTypeColor(event.event_type)]">
                                        {{ getEventTypeLabel(event.event_type) }}
                                    </span>
                                    <span v-if="event.is_online" class="px-2 py-1 bg-cyan-100 text-cyan-800 rounded text-xs font-semibold">
                                        Online
                                    </span>
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-semibold">
                                        ⭐ Featured
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ event.title }}</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ formatDate(event.event_date) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ formatTime(event.event_time) }}</span>
                                    </div>
                                    <div v-if="event.location" class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="line-clamp-1">{{ event.location }}</span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by title, description, or location..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @keyup.enter="applyFilters"
                                />
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                                <select
                                    v-model="selectedType"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="applyFilters"
                                >
                                    <option value="">All Types</option>
                                    <option value="seminar">Seminar</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="fair">Fair</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="webinar">Webinar</option>
                                </select>
                            </div>

                            <!-- Time Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">When</label>
                                <select
                                    v-model="selectedTime"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="applyFilters"
                                >
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past Events</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-4">
                            <button
                                @click="applyFilters"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                            >
                                Search
                            </button>
                            <button
                                @click="clearFilters"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Events Grid -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            {{ selectedTime === 'upcoming' ? 'All Upcoming Events' : 'Past Events' }}
                            <span class="text-gray-500 text-base font-normal ml-2">({{ events.data.length }} events)</span>
                        </h2>

                        <!-- Empty State -->
                        <div v-if="events.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-gray-500">No events found matching your criteria.</p>
                            <button
                                @click="clearFilters"
                                class="mt-4 text-blue-600 hover:text-blue-700 font-medium"
                            >
                                Clear filters and show all events
                            </button>
                        </div>

                        <!-- Events List -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <Link
                                v-for="event in events.data"
                                :key="event.id"
                                :href="route('events.show', event.slug)"
                                class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
                            >
                                <div v-if="event.image" class="h-40 bg-gray-200">
                                    <img :src="`/storage/${event.image}`" :alt="event.title" class="w-full h-full object-cover">
                                </div>
                                <div v-else class="h-40 bg-gray-500 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span :class="['px-2 py-1 rounded text-xs font-semibold', getEventTypeColor(event.event_type)]">
                                            {{ getEventTypeLabel(event.event_type) }}
                                        </span>
                                        <span v-if="event.is_online" class="px-2 py-1 bg-cyan-100 text-cyan-800 rounded text-xs font-semibold">
                                            Online
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ event.title }}</h3>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ event.description }}</p>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ formatDate(event.event_date) }} at {{ formatTime(event.event_time) }}</span>
                                        </div>
                                        <div v-if="event.location" class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="line-clamp-1">{{ event.location }}</span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="events.data.length > 0 && (events.prev_page_url || events.next_page_url)" class="mt-6">
                            <div class="flex items-center justify-between">
                                <Link
                                    v-if="events.prev_page_url"
                                    :href="events.prev_page_url"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                >
                                    Previous
                                </Link>
                                <span v-else class="px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                    Previous
                                </span>

                                <span class="text-sm text-gray-600">
                                    Page {{ events.current_page }} of {{ events.last_page }}
                                </span>

                                <Link
                                    v-if="events.next_page_url"
                                    :href="events.next_page_url"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                >
                                    Next
                                </Link>
                                <span v-else class="px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                    Next
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
