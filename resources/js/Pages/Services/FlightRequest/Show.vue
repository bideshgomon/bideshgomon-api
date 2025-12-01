<template>
    <AuthenticatedLayout>
        <Head :title="`Flight Request ${flightRequest.request_reference}`" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Button -->
                <Link
                    :href="route('flight-requests.index')"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to My Requests
                </Link>

                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ flightRequest.route_name }}</h1>
                                <span
                                    :class="flightRequest.status_color"
                                    class="px-3 py-1 rounded-full text-sm font-medium border"
                                >
                                    {{ (flightRequest?.status || '').replace('_', ' ').toUpperCase() }}
                                </span>
                            </div>
                            <p class="text-gray-600">Request Reference: <span class="font-semibold">{{ flightRequest.request_reference }}</span></p>
                        </div>

                        <button
                            v-if="['pending', 'assigned', 'quoted'].includes(flightRequest.status)"
                            @click="cancelRequest"
                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition"
                        >
                            Cancel Request
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Request Details -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Trip Details -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Trip Details</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="text-sm text-gray-500">Trip Type</div>
                                    <div class="font-medium text-gray-900 capitalize">{{ (flightRequest?.trip_type || '').replace('_', ' ') }}</div>
                                </div>

                                <div v-if="flightRequest.trip_type !== 'multi_city'">
                                    <div class="text-sm text-gray-500">Route</div>
                                    <div class="font-medium text-gray-900">
                                        {{ flightRequest.origin_airport_code }} → {{ flightRequest.destination_airport_code }}
                                    </div>
                                </div>

                                <div v-if="flightRequest.trip_type !== 'multi_city'">
                                    <div class="text-sm text-gray-500">Departure Date</div>
                                    <div class="font-medium text-gray-900">{{ formatDate(flightRequest.travel_date) }}</div>
                                </div>

                                <div v-if="flightRequest.trip_type === 'round_trip'">
                                    <div class="text-sm text-gray-500">Return Date</div>
                                    <div class="font-medium text-gray-900">{{ formatDate(flightRequest.return_date) }}</div>
                                </div>

                                <div v-if="flightRequest.trip_type === 'multi_city'">
                                    <div class="text-sm text-gray-500">Multi-City Segments</div>
                                    <div class="space-y-2 mt-2">
                                        <div
                                            v-for="(segment, index) in flightRequest.multi_city_segments"
                                            :key="index"
                                            class="p-3 bg-gray-50 rounded-lg"
                                        >
                                            <div class="font-medium text-gray-900">Flight {{ index + 1 }}</div>
                                            <div class="text-sm text-gray-600">
                                                {{ segment.origin }} → {{ segment.destination }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ formatDate(segment.date) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Passengers</div>
                                    <div class="font-medium text-gray-900">
                                        {{ flightRequest.passengers_count }} {{ flightRequest.passengers_count === 1 ? 'Person' : 'People' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Class</div>
                                    <div class="font-medium text-gray-900 capitalize">{{ flightRequest.flight_class }}</div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Budget</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.formatted_budget }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Preferences -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Preferences</h2>
                            
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg
                                        :class="flightRequest.prefer_direct_flights ? 'text-green-600' : 'text-gray-400'"
                                        class="w-5 h-5 mr-2"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-700">
                                        {{ flightRequest.prefer_direct_flights ? 'Prefers direct flights' : 'Layovers acceptable' }}
                                    </span>
                                </div>

                                <div v-if="flightRequest.preferred_airlines && flightRequest.preferred_airlines.length > 0">
                                    <div class="text-sm text-gray-500 mb-1">Preferred Airlines</div>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="airline in flightRequest.preferred_airlines"
                                            :key="airline"
                                            class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs"
                                        >
                                            {{ airline }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="flightRequest.preferred_time">
                                    <div class="text-sm text-gray-500">Preferred Time</div>
                                    <div class="font-medium text-gray-900 capitalize">{{ flightRequest.preferred_time }}</div>
                                </div>

                                <div v-if="flightRequest.special_requests">
                                    <div class="text-sm text-gray-500">Special Requests</div>
                                    <div class="text-sm text-gray-700 mt-1">{{ flightRequest.special_requests }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <div class="text-sm text-gray-500">Name</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.contact_name }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Email</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.contact_email }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Phone</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.contact_phone }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Quotes -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Quotes Received</h2>

                            <!-- No Quotes Yet -->
                            <div v-if="flightRequest.quotes.length === 0" class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No quotes yet</h3>
                                <p class="text-gray-600">
                                    {{ flightRequest.status === 'pending' 
                                        ? 'Your request is pending assignment to an agency.'
                                        : 'Agencies are reviewing your request. You will be notified when quotes are submitted.'
                                    }}
                                </p>
                            </div>

                            <!-- Quotes List -->
                            <div v-else class="space-y-4">
                                <div
                                    v-for="quote in flightRequest.quotes"
                                    :key="quote.id"
                                    :class="[
                                        'border rounded-lg p-6',
                                        quote.status === 'accepted' ? 'border-green-500 bg-green-50' : 'border-gray-200'
                                    ]"
                                >
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <div class="flex items-center space-x-2 mb-1">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ quote.airline_name }}</h3>
                                                <span
                                                    :class="quote.status_color"
                                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                                >
                                                    {{ (quote.status || '').toUpperCase() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">Flight {{ quote.flight_number }}</p>
                                            <p class="text-sm text-gray-500">By {{ quote.quoted_by.name }}</p>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-gray-900">{{ quote.formatted_price }}</div>
                                            <div class="text-sm text-gray-500">Total Price</div>
                                        </div>
                                    </div>

                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Price Breakdown</div>
                                            <div class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ quote.price_breakdown }}</div>
                                        </div>

                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Flight Details</div>
                                            <div class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ quote.flight_details }}</div>
                                        </div>

                                        <div v-if="quote.notes">
                                            <div class="text-sm font-medium text-gray-700">Agency Notes</div>
                                            <div class="text-sm text-gray-600 mt-1">{{ quote.notes }}</div>
                                        </div>

                                        <div class="flex items-center space-x-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Valid Until:</span>
                                                <span class="font-medium text-gray-900 ml-1">{{ formatDate(quote.valid_until) }}</span>
                                            </div>
                                            <div v-if="isExpired(quote.valid_until)" class="text-red-600 font-medium">
                                                (Expired)
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="quote.status === 'pending' && !isExpired(quote.valid_until) && flightRequest.status !== 'completed'">
                                        <button
                                            @click="acceptQuote(quote.id)"
                                            :disabled="processingQuote"
                                            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50 font-medium"
                                        >
                                            {{ processingQuote ? 'Processing...' : 'Accept This Quote' }}
                                        </button>
                                    </div>

                                    <div v-if="quote.status === 'accepted'" class="bg-green-100 border border-green-300 rounded-lg p-4 mt-4">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-green-800 font-medium">You accepted this quote</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

const props = defineProps({
    flightRequest: {
        type: Object,
        required: true
    }
});

const processingQuote = ref(false);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const isExpired = (validUntil) => {
    return new Date(validUntil) < new Date();
};

const acceptQuote = (quoteId) => {
    if (confirm('Are you sure you want to accept this quote? The payment will be deducted from your wallet.')) {
        processingQuote.value = true;
        router.post(
            route('flight-requests.accept-quote', { 
                requestId: props.flightRequest.id, 
                quoteId: quoteId 
            }),
            {},
            {
                onFinish: () => {
                    processingQuote.value = false;
                }
            }
        );
    }
};

const cancelRequest = () => {
    if (confirm('Are you sure you want to cancel this flight request?')) {
        router.post(route('flight-requests.cancel', props.flightRequest.id));
    }
};
</script>
