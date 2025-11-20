<template>
    <AuthenticatedLayout>
        <Head title="My Flight Requests" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Flight Requests</h1>
                        <p class="mt-2 text-gray-600">Track your flight requests and compare quotes</p>
                    </div>
                    <Link
                        :href="route('flight-requests.create')"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Request
                    </Link>
                </div>

                <!-- Filter Tabs -->
                <div class="flex space-x-4 mb-6 border-b border-gray-200">
                    <Link
                        :href="route('flight-requests.index')"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'all'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        All
                    </Link>
                    <Link
                        :href="route('flight-requests.index', { filter: 'pending' })"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'pending'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        Pending
                    </Link>
                    <Link
                        :href="route('flight-requests.index', { filter: 'quoted' })"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'quoted'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        Quoted
                    </Link>
                    <Link
                        :href="route('flight-requests.index', { filter: 'completed' })"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'completed'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        Completed
                    </Link>
                    <Link
                        :href="route('flight-requests.index', { filter: 'cancelled' })"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'cancelled'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        Cancelled
                    </Link>
                </div>

                <!-- Flight Requests List -->
                <div v-if="flightRequests.data.length > 0" class="space-y-4">
                    <div
                        v-for="request in flightRequests.data"
                        :key="request.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ request.route_name }}
                                    </h3>
                                    <span
                                        :class="request.status_color"
                                        class="px-3 py-1 rounded-full text-xs font-medium border"
                                    >
                                        {{ request.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                    <span v-if="request.quotes_count > 0" class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                        {{ request.quotes_count }} {{ request.quotes_count === 1 ? 'Quote' : 'Quotes' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                    <div>
                                        <div class="text-sm text-gray-500">Reference</div>
                                        <div class="font-medium text-gray-900">{{ request.request_reference }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Travel Date</div>
                                        <div class="font-medium text-gray-900">{{ formatDate(request.travel_date) }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Passengers</div>
                                        <div class="font-medium text-gray-900">{{ request.passengers_count }} {{ request.passengers_count === 1 ? 'Person' : 'People' }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Budget</div>
                                        <div class="font-medium text-gray-900">{{ request.formatted_budget }}</div>
                                    </div>
                                </div>

                                <div v-if="request.assigned_to" class="mt-4">
                                    <div class="text-sm text-gray-500">
                                        Assigned to: <span class="font-medium text-gray-900">{{ request.assigned_to.name }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-6 flex flex-col space-y-2">
                                <Link
                                    :href="route('flight-requests.show', request.id)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm text-center"
                                >
                                    View Details
                                </Link>

                                <Link
                                    v-if="request.quotes_count > 0 && request.status === 'quoted'"
                                    :href="route('flight-requests.show', request.id)"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm text-center"
                                >
                                    Compare Quotes
                                </Link>

                                <button
                                    v-if="['pending', 'assigned', 'quoted'].includes(request.status)"
                                    @click="cancelRequest(request.id)"
                                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition text-sm"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No flight requests yet</h3>
                    <p class="text-gray-600 mb-6">Submit your first flight request to get competitive quotes from agencies</p>
                    <Link
                        :href="route('flight-requests.create')"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Submit Flight Request
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="flightRequests.data.length > 0" class="mt-8">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Showing {{ flightRequests.from }} to {{ flightRequests.to }} of {{ flightRequests.total }} requests
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-for="link in flightRequests.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-4 py-2 border rounded-lg text-sm',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    flightRequests: {
        type: Object,
        required: true
    },
    filter: {
        type: String,
        default: 'all'
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const cancelRequest = (id) => {
    if (confirm('Are you sure you want to cancel this flight request?')) {
        router.post(route('flight-requests.cancel', id));
    }
};
</script>
