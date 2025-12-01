<template>
    <AuthenticatedLayout>
        <Head title="Agency Flight Requests" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">My Assigned Requests</h1>
                    <p class="mt-2 text-gray-600">Manage and quote on your assigned flight requests</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="text-sm text-gray-500 mb-1">Total Assigned</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.assigned }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-6">
                        <div class="text-sm text-yellow-700 mb-1">Needs Quote</div>
                        <div class="text-2xl font-bold text-yellow-900">{{ stats.needs_quote }}</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg shadow-sm border border-purple-200 p-6">
                        <div class="text-sm text-purple-700 mb-1">Quoted</div>
                        <div class="text-2xl font-bold text-purple-900">{{ stats.quoted }}</div>
                    </div>
                    <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-6">
                        <div class="text-sm text-green-700 mb-1">Accepted</div>
                        <div class="text-2xl font-bold text-green-900">{{ stats.accepted }}</div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="flex space-x-4 mb-6 border-b border-gray-200">
                    <Link
                        :href="route('agency.flight-requests.index')"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'all'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        All Requests
                    </Link>
                    <Link
                        :href="route('agency.flight-requests.index', { filter: 'needs_quote' })"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'needs_quote'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        Needs Quote
                    </Link>
                    <Link
                        :href="route('agency.flight-requests.index', { filter: 'pending' })"
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
                        :href="route('agency.flight-requests.index', { filter: 'accepted' })"
                        :class="[
                            'pb-3 px-4 font-medium text-sm transition',
                            filter === 'accepted'
                                ? 'border-b-2 border-blue-500 text-blue-600'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        Accepted
                    </Link>
                </div>

                <!-- Requests List -->
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
                                        {{ (request?.status || '').replace('_', ' ').toUpperCase() }}
                                    </span>
                                    <span v-if="hasQuoted(request)" class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                        Quote Submitted
                                    </span>
                                    <span v-else class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                        Needs Quote
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4">
                                    <div>
                                        <div class="text-sm text-gray-500">Reference</div>
                                        <div class="font-medium text-gray-900">{{ request.request_reference }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Customer</div>
                                        <div class="font-medium text-gray-900">{{ request.user.name }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Travel Date</div>
                                        <div class="font-medium text-gray-900">{{ formatDate(request.travel_date) }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Passengers</div>
                                        <div class="font-medium text-gray-900">{{ request.passengers_count }}</div>
                                    </div>

                                    <div>
                                        <div class="text-sm text-gray-500">Budget</div>
                                        <div class="font-medium text-gray-900">{{ request.formatted_budget }}</div>
                                    </div>
                                </div>

                                <div v-if="request.prefer_direct_flights || (request.preferred_airlines && request.preferred_airlines.length > 0)" class="mt-4 flex flex-wrap gap-2">
                                    <span v-if="request.prefer_direct_flights" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        Direct Flights Preferred
                                    </span>
                                    <span
                                        v-for="airline in request.preferred_airlines"
                                        :key="airline"
                                        class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs"
                                    >
                                        {{ airline }}
                                    </span>
                                </div>
                            </div>

                            <div class="ml-6 flex flex-col space-y-2">
                                <Link
                                    :href="route('agency.flight-requests.show', request.id)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm text-center"
                                >
                                    View Details
                                </Link>

                                <Link
                                    v-if="!hasQuoted(request)"
                                    :href="route('agency.flight-requests.create-quote', request.id)"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm text-center"
                                >
                                    Submit Quote
                                </Link>

                                <Link
                                    v-else-if="hasQuoted(request) && request.quotes[0].status === 'pending'"
                                    :href="route('agency.flight-requests.create-quote', request.id)"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm text-center"
                                >
                                    Edit Quote
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No requests assigned yet</h3>
                    <p class="text-gray-600">You'll see flight requests here once an admin assigns them to you</p>
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
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    flightRequests: {
        type: Object,
        required: true
    },
    filter: {
        type: String,
        default: 'all'
    },
    stats: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const hasQuoted = (request) => {
    return request.quotes && request.quotes.length > 0;
};
</script>
