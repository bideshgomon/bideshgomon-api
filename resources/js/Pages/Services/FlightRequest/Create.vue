<template>
    <AuthenticatedLayout>
        <Head title="Submit Flight Request" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Submit Flight Request</h1>
                    <p class="mt-2 text-gray-600">Tell us your travel requirements and get competitive quotes from agencies</p>
                </div>

                <!-- Popular Routes -->
                <div v-if="popularRoutes.length > 0" class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Trending Routes</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            v-for="route in popularRoutes"
                            :key="`${route.origin_airport_code}-${route.destination_airport_code}`"
                            @click="prefillRoute(route)"
                            class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition cursor-pointer"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        {{ route.origin_airport_code }} → {{ route.destination_airport_code }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ route.total_searches }} searches
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trip Type Tabs -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex space-x-4 mb-6 border-b border-gray-200">
                        <button
                            @click="form.trip_type = 'one_way'"
                            :class="[
                                'pb-3 px-4 font-medium text-sm transition',
                                form.trip_type === 'one_way'
                                    ? 'border-b-2 border-blue-500 text-blue-600'
                                    : 'text-gray-500 hover:text-gray-700'
                            ]"
                        >
                            One Way
                        </button>
                        <button
                            @click="form.trip_type = 'round_trip'"
                            :class="[
                                'pb-3 px-4 font-medium text-sm transition',
                                form.trip_type === 'round_trip'
                                    ? 'border-b-2 border-blue-500 text-blue-600'
                                    : 'text-gray-500 hover:text-gray-700'
                            ]"
                        >
                            Round Trip
                        </button>
                        <button
                            @click="form.trip_type = 'multi_city'"
                            :class="[
                                'pb-3 px-4 font-medium text-sm transition',
                                form.trip_type === 'multi_city'
                                    ? 'border-b-2 border-blue-500 text-blue-600'
                                    : 'text-gray-500 hover:text-gray-700'
                            ]"
                        >
                            Multi-City
                        </button>
                    </div>

                    <form @submit.prevent="submitRequest">
                        <!-- One Way / Round Trip Form -->
                        <div v-if="form.trip_type !== 'multi_city'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                    <input
                                        v-model="form.origin_airport_code"
                                        type="text"
                                        placeholder="e.g., DAC (Dhaka)"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                    <div v-if="form.errors.origin_airport_code" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.origin_airport_code }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                    <input
                                        v-model="form.destination_airport_code"
                                        type="text"
                                        placeholder="e.g., DXB (Dubai)"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                    <div v-if="form.errors.destination_airport_code" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.destination_airport_code }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Departure Date</label>
                                    <input
                                        v-model="form.travel_date"
                                        type="date"
                                        :min="getTodayDate()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                    <div v-if="form.errors.travel_date" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.travel_date }}
                                    </div>
                                </div>

                                <div v-if="form.trip_type === 'round_trip'">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Return Date</label>
                                    <input
                                        v-model="form.return_date"
                                        type="date"
                                        :min="form.travel_date || getTodayDate()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                    <div v-if="form.errors.return_date" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.return_date }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Multi-City Form -->
                        <div v-else class="space-y-6">
                            <div
                                v-for="(segment, index) in form.multi_city_segments"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-medium text-gray-900">Flight {{ index + 1 }}</h3>
                                    <button
                                        v-if="form.multi_city_segments.length > 2"
                                        @click="removeSegment(index)"
                                        type="button"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                        <input
                                            v-model="segment.origin"
                                            type="text"
                                            placeholder="e.g., DAC"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                        <input
                                            v-model="segment.destination"
                                            type="text"
                                            placeholder="e.g., DXB"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                        <input
                                            v-model="segment.date"
                                            type="date"
                                            :min="getTodayDate()"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            <button
                                v-if="form.multi_city_segments.length < 5"
                                @click="addSegment"
                                type="button"
                                class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-500 hover:text-blue-600 transition"
                            >
                                + Add Another Flight
                            </button>
                        </div>

                        <!-- Passengers & Class -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passengers</label>
                                <select
                                    v-model.number="form.passengers_count"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option v-for="n in 9" :key="n" :value="n">{{ n }} {{ n === 1 ? 'Passenger' : 'Passengers' }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                                <select
                                    v-model="form.flight_class"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="economy">Economy</option>
                                    <option value="business">Business</option>
                                    <option value="first">First Class</option>
                                </select>
                            </div>
                        </div>

                        <!-- Passenger Details -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Passenger Details</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="(passenger, index) in form.passengers"
                                    :key="index"
                                    class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 rounded-lg"
                                >
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input
                                            v-model="passenger.name"
                                            type="text"
                                            placeholder="As per passport"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                                        <input
                                            v-model.number="passenger.age"
                                            type="number"
                                            min="0"
                                            max="120"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Passport Number (Optional)</label>
                                        <input
                                            v-model="passenger.passport_number"
                                            type="text"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                                    <input
                                        v-model="form.contact_name"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input
                                        v-model="form.contact_email"
                                        type="email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input
                                        v-model="form.contact_phone"
                                        type="tel"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Budget Range -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Range (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Budget (৳)</label>
                                    <input
                                        v-model.number="form.budget_min"
                                        type="number"
                                        min="0"
                                        step="1000"
                                        placeholder="e.g., 50000"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Budget (৳)</label>
                                    <input
                                        v-model.number="form.budget_max"
                                        type="number"
                                        min="0"
                                        step="1000"
                                        placeholder="e.g., 100000"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Leave blank if flexible</p>
                        </div>

                        <!-- Preferences -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Preferences (Optional)</h3>
                            
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.prefer_direct_flights"
                                        type="checkbox"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Prefer direct flights (no layovers)</span>
                                </label>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Airlines</label>
                                    <input
                                        v-model="preferredAirlinesInput"
                                        type="text"
                                        placeholder="e.g., Emirates, Qatar Airways (comma separated)"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time of Day</label>
                                    <select
                                        v-model="form.preferred_time"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="flexible">Flexible</option>
                                        <option value="morning">Morning (6 AM - 12 PM)</option>
                                        <option value="afternoon">Afternoon (12 PM - 6 PM)</option>
                                        <option value="evening">Evening (6 PM - 12 AM)</option>
                                        <option value="night">Night (12 AM - 6 AM)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                                    <textarea
                                        v-model="form.special_requests"
                                        rows="3"
                                        placeholder="Any special requirements or notes..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <Link
                                :href="route('flight-requests.index')"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                <span v-if="!form.processing">Submit Request</span>
                                <span v-else>Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    popularRoutes: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    trip_type: 'one_way',
    origin_airport_code: '',
    destination_airport_code: '',
    travel_date: '',
    return_date: '',
    multi_city_segments: [
        { origin: '', destination: '', date: '' },
        { origin: '', destination: '', date: '' }
    ],
    passengers_count: 1,
    flight_class: 'economy',
    passengers: [{ name: '', age: '', passport_number: '' }],
    contact_name: '',
    contact_email: '',
    contact_phone: '',
    special_requests: '',
    budget_min: null,
    budget_max: null,
    prefer_direct_flights: false,
    preferred_airlines: [],
    preferred_time: 'flexible'
});

const preferredAirlinesInput = ref('');

// Watch passengers_count to adjust passengers array
watch(() => form.passengers_count, (newCount) => {
    const currentLength = form.passengers.length;
    if (newCount > currentLength) {
        for (let i = currentLength; i < newCount; i++) {
            form.passengers.push({ name: '', age: '', passport_number: '' });
        }
    } else if (newCount < currentLength) {
        form.passengers = form.passengers.slice(0, newCount);
    }
});

const getTodayDate = () => {
    return new Date().toISOString().split('T')[0];
};

const addSegment = () => {
    if (form.multi_city_segments.length < 5) {
        form.multi_city_segments.push({ origin: '', destination: '', date: '' });
    }
};

const removeSegment = (index) => {
    if (form.multi_city_segments.length > 2) {
        form.multi_city_segments.splice(index, 1);
    }
};

const prefillRoute = (route) => {
    form.trip_type = 'one_way';
    form.origin_airport_code = route.origin_airport_code;
    form.destination_airport_code = route.destination_airport_code;
};

const submitRequest = () => {
    // Parse preferred airlines from comma-separated input
    if (preferredAirlinesInput.value) {
        form.preferred_airlines = preferredAirlinesInput.value
            .split(',')
            .map(a => a.trim())
            .filter(a => a.length > 0);
    }

    form.post(route('flight-requests.store'));
};
</script>
