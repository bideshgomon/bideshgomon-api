<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    MagnifyingGlassIcon,
    CalendarIcon,
    UserGroupIcon,
    MapPinIcon,
    ClockIcon,
    CurrencyDollarIcon,
    ArrowRightIcon,
    FireIcon,
    PlusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    popularRoutes: Array,
    origins: Array,
    destinations: Array,
});

const tripType = ref('one_way');
const multiCityFlights = ref([
    { origin: 'DAC', destination: '', travel_date: '' },
    { origin: '', destination: '', travel_date: '' },
]);

const searchForm = useForm({
    trip_type: 'one_way',
    origin: 'DAC',
    destination: '',
    travel_date: '',
    return_date: '',
    passengers: 1,
    flight_class: 'economy',
    multi_city_segments: [],
});

const addFlight = () => {
    multiCityFlights.value.push({ origin: '', destination: '', travel_date: '' });
};

const removeFlight = (index) => {
    if (multiCityFlights.value.length > 2) {
        multiCityFlights.value.splice(index, 1);
    }
};

const searchFlights = () => {
    if (tripType.value === 'multi_city') {
        searchForm.trip_type = 'multi_city';
        searchForm.multi_city_segments = multiCityFlights.value;
    } else {
        searchForm.trip_type = tripType.value;
    }
    
    searchForm.post(route('flight-booking.search'), {
        preserveScroll: true,
    });
};

const formatCurrency = (amount) => {
    return '৳ ' + Number(amount).toLocaleString('en-BD', { minimumFractionDigits: 0 });
};

const getTomorrowDate = () => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
};
</script>

<template>
    <Head title="Flight Booking" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 pb-20">
            <!-- Hero Section with Search -->
            <div class="bg-sky-600 text-white px-4 pt-8 pb-24">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-white/20 p-3 rounded-xl">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Flight Booking</h1>
                            <p class="text-sky-100 text-lg">Find the best flights to your destination</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Form Card -->
            <div class="max-w-4xl mx-auto px-4 -mt-16">
                <div class="bg-white rounded-3xl p-6 shadow-2xl mb-6">
                    <!-- Trip Type Tabs -->
                    <div class="flex space-x-2 mb-6 p-1 bg-gray-100 rounded-xl">
                        <button
                            type="button"
                            @click="tripType = 'one_way'"
                            :class="[
                                'flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                                tripType === 'one_way' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            One Way
                        </button>
                        <button
                            type="button"
                            @click="tripType = 'round_trip'"
                            :class="[
                                'flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                                tripType === 'round_trip' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Round Trip
                        </button>
                        <button
                            type="button"
                            @click="tripType = 'multi_city'"
                            :class="[
                                'flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                                tripType === 'multi_city' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Multi-City
                        </button>
                    </div>

                    <form @submit.prevent="searchFlights" class="space-y-4">
                        <!-- One Way / Round Trip -->
                        <div v-if="tripType !== 'multi_city'">
                            <!-- Origin & Destination -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <MapPinIcon class="h-5 w-5 inline-block mr-1" />
                                        From
                                    </label>
                                    <select 
                                        v-model="searchForm.origin"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        required
                                    >
                                        <option v-for="origin in origins" :key="origin.code" :value="origin.code">
                                            {{ origin.city }} ({{ origin.code }})
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <MapPinIcon class="h-5 w-5 inline-block mr-1" />
                                        To
                                    </label>
                                    <select 
                                        v-model="searchForm.destination"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        required
                                    >
                                        <option value="">Select Destination</option>
                                        <option v-for="dest in destinations" :key="dest.code" :value="dest.code">
                                            {{ dest.city }}, {{ dest.country }} ({{ dest.code }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Date & Passengers -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <CalendarIcon class="h-5 w-5 inline-block mr-1" />
                                        Departure Date
                                    </label>
                                    <input 
                                        type="date"
                                        v-model="searchForm.travel_date"
                                        :min="getTomorrowDate()"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        required
                                    />
                                </div>
                                <div v-if="tripType === 'round_trip'">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <CalendarIcon class="h-5 w-5 inline-block mr-1" />
                                        Return Date
                                    </label>
                                    <input 
                                        type="date"
                                        v-model="searchForm.return_date"
                                        :min="searchForm.travel_date || getTomorrowDate()"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                        :required="tripType === 'round_trip'"
                                    />
                                </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <UserGroupIcon class="h-5 w-5 inline-block mr-1" />
                                    Passengers
                                </label>
                                <select 
                                    v-model="searchForm.passengers"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                >
                                    <option v-for="n in 9" :key="n" :value="n">{{ n }} {{ n === 1 ? 'Passenger' : 'Passengers' }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Class
                                </label>
                                <select 
                                    v-model="searchForm.flight_class"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                >
                                    <option value="economy">Economy</option>
                                    <option value="business">Business</option>
                                    <option value="first_class">First Class</option>
                                </select>
                            </div>
                        </div>
                        </div>

                        <!-- Multi-City -->
                        <div v-else class="space-y-4">
                            <div v-for="(flight, index) in multiCityFlights" :key="index" class="p-4 border-2 border-gray-200 rounded-xl relative">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-gray-700">Flight {{ index + 1 }}</span>
                                    <button
                                        v-if="multiCityFlights.length > 2"
                                        type="button"
                                        @click="removeFlight(index)"
                                        class="p-1 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    >
                                        <XMarkIcon class="h-5 w-5" />
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">From</label>
                                        <select 
                                            v-model="flight.origin"
                                            class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-sky-500 focus:ring-0 text-sm"
                                            required
                                        >
                                            <option value="">Select</option>
                                            <option v-for="origin in origins" :key="origin.code" :value="origin.code">
                                                {{ origin.city }} ({{ origin.code }})
                                            </option>
                                            <option v-for="dest in destinations" :key="dest.code" :value="dest.code">
                                                {{ dest.city }} ({{ dest.code }})
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">To</label>
                                        <select 
                                            v-model="flight.destination"
                                            class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-sky-500 focus:ring-0 text-sm"
                                            required
                                        >
                                            <option value="">Select</option>
                                            <option v-for="dest in destinations" :key="dest.code" :value="dest.code">
                                                {{ dest.city }} ({{ dest.code }})
                                            </option>
                                            <option v-for="origin in origins" :key="origin.code" :value="origin.code">
                                                {{ origin.city }} ({{ origin.code }})
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                                        <input 
                                            type="date"
                                            v-model="flight.travel_date"
                                            :min="getTomorrowDate()"
                                            class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-sky-500 focus:ring-0 text-sm"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            <button
                                v-if="multiCityFlights.length < 5"
                                type="button"
                                @click="addFlight"
                                class="w-full py-3 border-2 border-dashed border-sky-300 text-sky-600 rounded-xl hover:border-sky-500 hover:bg-sky-50 transition-all flex items-center justify-center space-x-2 font-medium"
                            >
                                <PlusIcon class="h-5 w-5" />
                                <span>Add Another Flight</span>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <UserGroupIcon class="h-5 w-5 inline-block mr-1" />
                                        Passengers
                                    </label>
                                    <select 
                                        v-model="searchForm.passengers"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                    >
                                        <option v-for="n in 9" :key="n" :value="n">{{ n }} {{ n === 1 ? 'Passenger' : 'Passengers' }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Flight Class
                                    </label>
                                    <select 
                                        v-model="searchForm.flight_class"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0 transition-colors"
                                    >
                                        <option value="economy">Economy</option>
                                        <option value="business">Business</option>
                                        <option value="first_class">First Class</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <button
                            type="submit"
                            :disabled="searchForm.processing"
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white py-4 rounded-xl font-semibold text-lg transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                        >
                            <MagnifyingGlassIcon class="h-6 w-6" />
                            <span>{{ searchForm.processing ? 'Searching...' : 'Search Flights' }}</span>
                        </button>
                    </form>
                </div>

                <!-- Popular Routes -->
                <div class="mb-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <FireIcon class="h-6 w-6 text-orange-500" />
                        <h2 class="text-xl font-bold text-gray-900">Popular Routes</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Link
                            v-for="flightRoute in popularRoutes"
                            :key="flightRoute.id"
                            :href="route('flight-booking.booking-form', flightRoute.id) + `?travel_date=${getTomorrowDate()}&passengers=1&flight_class=economy`"
                            class="bg-white rounded-2xl p-5 shadow-md hover:shadow-xl transition-all active:scale-98 group"
                        >
                            <!-- Airline & Flight Number -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-sky-100 p-2 rounded-lg">
                                        <span class="text-sky-700 font-bold text-sm">{{ flightRoute.airline_code }}</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ flightRoute.airline_name }}</div>
                                        <div class="text-xs text-gray-500">{{ flightRoute.flight_number }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-sky-600">
                                        {{ formatCurrency(flightRoute.economy_price) }}
                                    </div>
                                    <div class="text-xs text-gray-500">per person</div>
                                </div>
                            </div>

                            <!-- Route -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-center flex-1">
                                    <div class="text-2xl font-bold text-gray-900">{{ flightRoute.origin_airport_code }}</div>
                                    <div class="text-xs text-gray-600">{{ flightRoute.origin_city }}</div>
                                </div>
                                <div class="flex-1 flex flex-col items-center">
                                    <ArrowRightIcon class="h-5 w-5 text-gray-400" />
                                    <div class="text-xs text-gray-500 mt-1">{{ flightRoute.flight_duration }}</div>
                                </div>
                                <div class="text-center flex-1">
                                    <div class="text-2xl font-bold text-gray-900">{{ flightRoute.destination_airport_code }}</div>
                                    <div class="text-xs text-gray-600">{{ flightRoute.destination_city }}</div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="flex items-center justify-between text-xs text-gray-600 pt-3 border-t border-gray-100">
                                <span v-if="flightRoute.is_direct" class="bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                                    Direct Flight
                                </span>
                                <span class="flex items-center">
                                    <ClockIcon class="h-4 w-4 mr-1" />
                                    {{ flightRoute.departure_time }}
                                </span>
                                <span class="text-sky-600 font-medium group-hover:underline">
                                    View Details →
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                        <div class="text-3xl mb-2">✈️</div>
                        <div class="font-semibold text-gray-900">10 Routes</div>
                        <div class="text-xs text-gray-600">Popular destinations</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                        <div class="text-3xl mb-2">💰</div>
                        <div class="font-semibold text-gray-900">Best Prices</div>
                        <div class="text-xs text-gray-600">Competitive fares</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                        <div class="text-3xl mb-2">🎫</div>
                        <div class="font-semibold text-gray-900">Instant Booking</div>
                        <div class="text-xs text-gray-600">Quick confirmation</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                        <div class="text-3xl mb-2">💳</div>
                        <div class="font-semibold text-gray-900">Wallet Payment</div>
                        <div class="text-xs text-gray-600">Safe & secure</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.active\:scale-98:active {
    transform: scale(0.98);
}
</style>
