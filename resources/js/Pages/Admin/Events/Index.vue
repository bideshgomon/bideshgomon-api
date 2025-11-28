<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    MagnifyingGlassIcon, PlusIcon, FunnelIcon, CalendarIcon,
    MapPinIcon, UsersIcon, EyeIcon, PencilIcon, TrashIcon,
    SparklesIcon, GlobeAltIcon, CheckCircleIcon, XCircleIcon
} from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';

const props = defineProps({
    events: Object,
    filters: Object,
});

const { formatDate } = useBangladeshFormat();

const search = ref(props.filters?.search || '');
const type = ref(props.filters?.type || '');
const status = ref(props.filters?.status || '');
const showFilters = ref(false);

const performSearch = () => {
    router.get(route('admin.events.index'), {
        search: search.value,
        type: type.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    search.value = '';
    type.value = '';
    status.value = '';
    performSearch();
};

const hasActiveFilters = computed(() => {
    return search.value || type.value || status.value;
});

const deleteEvent = (eventId) => {
    if (confirm('Delete this event? This action cannot be undone.')) {
        router.delete(route('admin.events.destroy', eventId));
    }
};

const toggleFeatured = (eventId) => {
    router.post(route('admin.events.toggle-featured', eventId));
};

const togglePublished = (eventId) => {
    router.post(route('admin.events.toggle-published', eventId));
};

const getEventTypeColor = (eventType) => {
    const colors = {
        'seminar': 'bg-blue-100 text-blue-700',
        'workshop': 'bg-purple-100 text-purple-700',
        'webinar': 'bg-green-100 text-green-700',
        'fair': 'bg-orange-100 text-orange-700',
        'consultation': 'bg-indigo-100 text-indigo-700',
        'other': 'bg-gray-100 text-gray-700',
    };
    return colors[eventType?.toLowerCase()] || 'bg-gray-100 text-gray-700';
};

const isUpcoming = (eventDate) => {
    return new Date(eventDate) >= new Date();
};

const getStatusBadge = (event) => {
    if (!event.is_published) {
        return { text: 'Draft', class: 'bg-gray-100 text-gray-700' };
    }
    if (isUpcoming(event.event_date)) {
        return { text: 'Upcoming', class: 'bg-green-100 text-green-700' };
    }
    return { text: 'Past', class: 'bg-gray-100 text-gray-600' };
};
</script>

<template>
    <Head title="Manage Events" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 pb-12">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-4 py-8 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Events Management</h1>
                            <p class="mt-2 text-gray-600">Manage all events and webinars on the platform</p>
                        </div>
                        <Link
                            :href="route('admin.events.create')"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-sm"
                        >
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Create Event
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
                <!-- Search and Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col space-y-4">
                        <!-- Search Bar -->
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="search"
                                    @keyup.enter="performSearch"
                                    type="text"
                                    placeholder="Search events by title or location..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                />
                            </div>
                            <button
                                @click="performSearch"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                            >
                                Search
                            </button>
                            <button
                                @click="showFilters = !showFilters"
                                class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center"
                            >
                                <FunnelIcon class="h-5 w-5 mr-2" />
                                Filters
                            </button>
                        </div>

                        <!-- Filters Panel -->
                        <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                                <select
                                    v-model="type"
                                    @change="performSearch"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Types</option>
                                    <option value="seminar">Seminar</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="webinar">Webinar</option>
                                    <option value="fair">Fair</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select
                                    v-model="status"
                                    @change="performSearch"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Status</option>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button
                                    v-if="hasActiveFilters"
                                    @click="clearFilters"
                                    class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div v-if="events.data.length === 0" class="text-center py-12">
                        <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No events found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new event.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.events.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Create Event
                            </Link>
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Event
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date & Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Location
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="event in events.data" :key="event.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <img 
                                                v-if="event.image" 
                                                :src="event.image" 
                                                :alt="event.title"
                                                class="h-12 w-12 rounded-lg object-cover mr-3"
                                            />
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">{{ event.title }}</div>
                                                <div class="text-sm text-gray-500 line-clamp-1">{{ event.description }}</div>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span
                                                        v-if="event.is_featured"
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"
                                                    >
                                                        <SparklesIcon class="h-3 w-3 mr-1" />
                                                        Featured
                                                    </span>
                                                    <span
                                                        v-if="event.max_participants"
                                                        class="inline-flex items-center text-xs text-gray-500"
                                                    >
                                                        <UsersIcon class="h-3 w-3 mr-1" />
                                                        Max: {{ event.max_participants }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getEventTypeColor(event.event_type)"
                                            class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                        >
                                            {{ event.event_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(event.event_date) }}</div>
                                        <div class="text-sm text-gray-500">{{ event.event_time }}</div>
                                        <div v-if="event.end_date" class="text-xs text-gray-400 mt-1">
                                            to {{ formatDate(event.end_date) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-start text-sm text-gray-900">
                                            <GlobeAltIcon v-if="event.is_online" class="h-4 w-4 mr-1 text-green-500 mt-0.5" />
                                            <MapPinIcon v-else class="h-4 w-4 mr-1 text-gray-400 mt-0.5" />
                                            <div>
                                                <div>{{ event.is_online ? 'Online' : event.location }}</div>
                                                <div v-if="event.is_online && event.meeting_link" class="text-xs text-gray-500 truncate max-w-[150px]">
                                                    {{ event.meeting_link }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getStatusBadge(event).class"
                                            class="px-2 py-1 text-xs font-semibold rounded-full"
                                        >
                                            {{ getStatusBadge(event).text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center space-x-2">
                                            <Link
                                                :href="route('admin.events.show', event.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="View"
                                            >
                                                <EyeIcon class="h-5 w-5" />
                                            </Link>
                                            <Link
                                                :href="route('admin.events.edit', event.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit"
                                            >
                                                <PencilIcon class="h-5 w-5" />
                                            </Link>
                                            <button
                                                @click="toggleFeatured(event.id)"
                                                :class="event.is_featured ? 'text-yellow-600' : 'text-gray-400'"
                                                class="hover:text-yellow-900"
                                                title="Toggle Featured"
                                            >
                                                <SparklesIcon class="h-5 w-5" />
                                            </button>
                                            <button
                                                @click="togglePublished(event.id)"
                                                :class="event.is_published ? 'text-green-600' : 'text-gray-400'"
                                                class="hover:text-green-900"
                                                title="Toggle Published"
                                            >
                                                <CheckCircleIcon class="h-5 w-5" />
                                            </button>
                                            <button
                                                @click="deleteEvent(event.id)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="events.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ events.from }} to {{ events.to }} of {{ events.total }} events
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in events.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="{
                                        'bg-indigo-600 text-white': link.active,
                                        'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                        'pointer-events-none opacity-50': !link.url
                                    }"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
