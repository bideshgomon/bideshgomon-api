<template>
    <AuthenticatedLayout>
        <Head :title="`Submit Quote - ${flightRequest.request_reference}`" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Button -->
                <Link
                    :href="route('agency.flight-requests.index')"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Requests
                </Link>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Request Details -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="text-sm text-gray-500">Reference</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.request_reference }}</div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Customer</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.user.name }}</div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">Route</div>
                                    <div class="font-medium text-gray-900">{{ flightRequest.route_name }}</div>
                                </div>

                                <div v-if="flightRequest.trip_type !== 'multi_city'">
                                    <div class="text-sm text-gray-500">Travel Date</div>
                                    <div class="font-medium text-gray-900">{{ formatDate(flightRequest.travel_date) }}</div>
                                </div>

                                <div v-if="flightRequest.trip_type === 'round_trip'">
                                    <div class="text-sm text-gray-500">Return Date</div>
                                    <div class="font-medium text-gray-900">{{ formatDate(flightRequest.return_date) }}</div>
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

                                <div v-if="flightRequest.prefer_direct_flights">
                                    <div class="flex items-center text-sm">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700">Prefers direct flights</span>
                                    </div>
                                </div>

                                <div v-if="flightRequest.preferred_airlines && flightRequest.preferred_airlines.length > 0">
                                    <div class="text-sm text-gray-500 mb-2">Preferred Airlines</div>
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
                    </div>

                    <!-- Right Column - Quote Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">
                                {{ existingQuote ? 'Edit Your Quote' : 'Submit Your Quote' }}
                            </h2>

                            <div v-if="existingQuote && existingQuote.status !== 'pending'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800">Quote {{ existingQuote.status }}</p>
                                        <p class="text-sm text-yellow-700 mt-1">This quote has been {{ existingQuote.status }} and cannot be edited.</p>
                                    </div>
                                </div>
                            </div>

                            <form @submit.prevent="submitQuote" v-if="!existingQuote || existingQuote.status === 'pending'">
                                <div class="space-y-6">
                                    <!-- Airline Information -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Flight Information</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Airline Name <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model="form.airline_name"
                                                    type="text"
                                                    placeholder="e.g., Emirates"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    required
                                                />
                                                <div v-if="form.errors.airline_name" class="text-red-600 text-sm mt-1">
                                                    {{ form.errors.airline_name }}
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Flight Number <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model="form.flight_number"
                                                    type="text"
                                                    placeholder="e.g., EK582"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    required
                                                />
                                                <div v-if="form.errors.flight_number" class="text-red-600 text-sm mt-1">
                                                    {{ form.errors.flight_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pricing -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Total Quoted Price (৳) <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model.number="form.quoted_price"
                                                    type="number"
                                                    min="0"
                                                    step="100"
                                                    placeholder="e.g., 75000"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    required
                                                />
                                                <div v-if="form.errors.quoted_price" class="text-red-600 text-sm mt-1">
                                                    {{ form.errors.quoted_price }}
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Valid Until <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model="form.valid_until"
                                                    type="date"
                                                    :min="getTomorrowDate()"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    required
                                                />
                                                <div v-if="form.errors.valid_until" class="text-red-600 text-sm mt-1">
                                                    {{ form.errors.valid_until }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Price Breakdown <span class="text-red-500">*</span>
                                            </label>
                                            <textarea
                                                v-model="form.price_breakdown"
                                                rows="4"
                                                placeholder="Break down the pricing:&#10;Base fare: ৳50,000&#10;Taxes & fees: ৳15,000&#10;Baggage: ৳5,000&#10;Service charge: ৳5,000&#10;Total: ৳75,000"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required
                                            ></textarea>
                                            <div v-if="form.errors.price_breakdown" class="text-red-600 text-sm mt-1">
                                                {{ form.errors.price_breakdown }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Flight Details -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Flight Details</h3>
                                        <textarea
                                            v-model="form.flight_details"
                                            rows="6"
                                            placeholder="Provide complete flight details:&#10;&#10;Departure: DAC 10:30 AM&#10;Arrival: DXB 2:45 PM (same day)&#10;Duration: 5h 15m&#10;Aircraft: Boeing 777-300ER&#10;Layovers: Direct flight (no layovers)&#10;Baggage: 30kg checked + 7kg cabin&#10;Meal: Complimentary&#10;&#10;Return (if applicable):&#10;Departure: DXB 8:00 PM&#10;Arrival: DAC 2:15 AM (next day)&#10;..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        ></textarea>
                                        <div v-if="form.errors.flight_details" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.flight_details }}
                                        </div>
                                    </div>

                                    <!-- Agency Notes -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Additional Notes (Optional)
                                        </label>
                                        <textarea
                                            v-model="form.notes"
                                            rows="3"
                                            placeholder="Any additional information for the customer..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        ></textarea>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                                        <Link
                                            :href="route('agency.flight-requests.index')"
                                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                                        >
                                            Cancel
                                        </Link>
                                        <button
                                            type="submit"
                                            :disabled="form.processing"
                                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                                        >
                                            <span v-if="!form.processing">
                                                {{ existingQuote ? 'Update Quote' : 'Submit Quote' }}
                                            </span>
                                            <span v-else>Processing...</span>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Existing Quote Display (if not editable) -->
                            <div v-else-if="existingQuote" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500">Airline</div>
                                        <div class="font-medium text-gray-900">{{ existingQuote.airline_name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Flight Number</div>
                                        <div class="font-medium text-gray-900">{{ existingQuote.flight_number }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Quoted Price</div>
                                        <div class="font-medium text-gray-900">{{ existingQuote.formatted_price }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Valid Until</div>
                                        <div class="font-medium text-gray-900">{{ formatDate(existingQuote.valid_until) }}</div>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500 mb-2">Price Breakdown</div>
                                    <div class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-4 rounded-lg">{{ existingQuote.price_breakdown }}</div>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500 mb-2">Flight Details</div>
                                    <div class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-4 rounded-lg">{{ existingQuote.flight_details }}</div>
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    flightRequest: {
        type: Object,
        required: true
    },
    existingQuote: {
        type: Object,
        default: null
    }
});

const form = useForm({
    airline_name: props.existingQuote?.airline_name || '',
    flight_number: props.existingQuote?.flight_number || '',
    quoted_price: props.existingQuote?.quoted_price || null,
    price_breakdown: props.existingQuote?.price_breakdown || '',
    flight_details: props.existingQuote?.flight_details || '',
    valid_until: props.existingQuote?.valid_until ? props.existingQuote.valid_until.split('T')[0] : '',
    notes: props.existingQuote?.notes || ''
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getTomorrowDate = () => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
};

const submitQuote = () => {
    if (props.existingQuote && props.existingQuote.status === 'pending') {
        form.put(route('agency.flight-requests.update-quote', {
            id: props.flightRequest.id,
            quoteId: props.existingQuote.id
        }));
    } else {
        form.post(route('agency.flight-requests.store-quote', props.flightRequest.id));
    }
};
</script>
