<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ArrowLeftIcon,
    UserIcon,
    CreditCardIcon,
    ShieldCheckIcon,
    CheckCircleIcon,
    ClockIcon,
    MapPinIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline';
import { ref, computed } from 'vue';

const props = defineProps({
    route: Object,
    travelDate: String,
    passengers: Number,
    flightClass: String,
    pricing: Object,
});

const page = usePage();
const currentStep = ref(1);

const bookingForm = useForm({
    flight_route_id: props.route.id,
    travel_date: props.travelDate,
    flight_class: props.flightClass,
    passengers_count: props.passengers,
    passengers: Array.from({ length: props.passengers }, () => ({
        name: '',
        passport_number: '',
        age: '',
        gender: 'male',
    })),
    contact_name: page.props.auth.user.name || '',
    contact_email: page.props.auth.user.email || '',
    contact_phone: '',
    special_requests: '',
    selected_seats: [],
    extra_baggage_count: 0,
    meal_preferences: [],
    add_insurance: false,
});

const formatCurrency = (amount) => {
    return '৳ ' + Number(amount).toLocaleString('en-BD', { minimumFractionDigits: 0 });
};

const formatTime = (time) => {
    return new Date('1970-01-01T' + time).toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
};

const totalAmount = computed(() => {
    let total = props.pricing.total_amount;
    
    if (bookingForm.extra_baggage_count > 0) {
        total += bookingForm.extra_baggage_count * 2000;
    }
    
    if (bookingForm.add_insurance) {
        total += props.passengers * 1500;
    }
    
    return total;
});

const walletBalance = computed(() => {
    return page.props.auth.user.wallet?.balance || 0;
});

const hasSufficientBalance = computed(() => {
    return walletBalance.value >= totalAmount.value;
});

const nextStep = () => {
    if (currentStep.value === 1) {
        // Validate passenger info
        const allFilled = bookingForm.passengers.every(p => 
            p.name && p.passport_number && p.age && p.gender
        );
        if (!allFilled) {
            alert('Please fill all passenger details');
            return;
        }
    }
    
    if (currentStep.value === 2) {
        // Validate contact info
        if (!bookingForm.contact_name || !bookingForm.contact_email || !bookingForm.contact_phone) {
            alert('Please fill all contact details');
            return;
        }
    }
    
    currentStep.value++;
};

const prevStep = () => {
    currentStep.value--;
};

const submitBooking = () => {
    if (!hasSufficientBalance.value) {
        alert('Insufficient wallet balance. Please add funds first.');
        return;
    }
    
    bookingForm.post(route('flight-booking.book'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Book Flight - ${route.route_name}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 pb-20">
            <!-- Header -->
            <div class="bg-gradient-to-br from-sky-600 to-blue-800 text-white px-4 pt-6 pb-24">
                <div class="max-w-4xl mx-auto">
                    <Link
                        :href="route('flight-booking.index')"
                        class="inline-flex items-center space-x-2 text-sky-100 hover:text-white mb-4 touch-manipulation"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                        <span class="text-sm font-medium">Back to Search</span>
                    </Link>
                    
                    <h1 class="text-2xl font-bold mb-2">Complete Your Booking</h1>
                    <p class="text-sky-100">{{ route.origin_city }} → {{ route.destination_city }}</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto px-4 -mt-16 space-y-4">
                <!-- Flight Summary Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xl mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-sky-100 p-2 rounded-lg">
                                <span class="text-sky-700 font-bold text-sm">{{ route.airline_code }}</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ route.airline_name }}</div>
                                <div class="text-xs text-gray-500">{{ route.flight_number }} • {{ route.aircraft_type }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-sky-600">
                                {{ formatCurrency(pricing.base_fare) }}
                            </div>
                            <div class="text-xs text-gray-500">per person</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <div class="text-center flex-1">
                            <div class="text-2xl font-bold text-gray-900">{{ route.origin_airport_code }}</div>
                            <div class="text-xs text-gray-600">{{ formatTime(route.departure_time) }}</div>
                            <div class="text-xs text-gray-500">{{ route.origin_city }}</div>
                        </div>
                        <div class="flex-1 flex flex-col items-center">
                            <ArrowRightIcon class="h-5 w-5 text-gray-400" />
                            <div class="text-xs text-gray-500 mt-1">{{ route.flight_duration }}</div>
                            <div v-if="route.is_direct" class="text-xs text-green-600 font-medium mt-1">Direct</div>
                        </div>
                        <div class="text-center flex-1">
                            <div class="text-2xl font-bold text-gray-900">{{ route.destination_airport_code }}</div>
                            <div class="text-xs text-gray-600">{{ formatTime(route.arrival_time) }}</div>
                            <div class="text-xs text-gray-500">{{ route.destination_city }}</div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-sm">
                        <span class="text-gray-600">Travel Date:</span>
                        <span class="font-semibold text-gray-900">{{ new Date(travelDate).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }) }}</span>
                    </div>
                    <div class="pt-2 flex items-center justify-between text-sm">
                        <span class="text-gray-600">Passengers:</span>
                        <span class="font-semibold text-gray-900">{{ passengers }}</span>
                    </div>
                    <div class="pt-2 flex items-center justify-between text-sm">
                        <span class="text-gray-600">Class:</span>
                        <span class="font-semibold text-gray-900 capitalize">{{ flightClass.replace('_', ' ') }}</span>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="bg-white rounded-2xl p-4 shadow-lg mb-4">
                    <div class="flex items-center justify-between">
                        <div v-for="step in 3" :key="step" class="flex-1 flex items-center">
                            <div class="flex items-center space-x-2">
                                <div 
                                    :class="[
                                        'w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm',
                                        currentStep >= step ? 'bg-sky-600 text-white' : 'bg-gray-200 text-gray-500'
                                    ]"
                                >
                                    <CheckCircleIcon v-if="currentStep > step" class="h-5 w-5" />
                                    <span v-else>{{ step }}</span>
                                </div>
                                <span 
                                    :class="[
                                        'text-sm font-medium hidden sm:inline',
                                        currentStep >= step ? 'text-gray-900' : 'text-gray-500'
                                    ]"
                                >
                                    {{ step === 1 ? 'Passengers' : step === 2 ? 'Contact' : 'Payment' }}
                                </span>
                            </div>
                            <div 
                                v-if="step < 3"
                                :class="[
                                    'flex-1 h-1 mx-2',
                                    currentStep > step ? 'bg-sky-600' : 'bg-gray-200'
                                ]"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Passenger Information -->
                <div v-show="currentStep === 1" class="bg-white rounded-2xl p-6 shadow-lg">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <UserIcon class="h-6 w-6 mr-2 text-sky-600" />
                        Passenger Information
                    </h2>
                    
                    <div v-for="(passenger, index) in bookingForm.passengers" :key="index" class="mb-6 pb-6 border-b border-gray-100 last:border-0">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Passenger {{ index + 1 }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input 
                                    v-model="passenger.name"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                    placeholder="As per passport"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passport Number *</label>
                                <input 
                                    v-model="passenger.passport_number"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                    placeholder="ABC123456"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                                <input 
                                    v-model="passenger.age"
                                    type="number"
                                    min="1"
                                    max="120"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                                <select 
                                    v-model="passenger.gender"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                >
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Add-ons -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Add-ons (Optional)</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-3">
                                    <input 
                                        type="checkbox"
                                        v-model="bookingForm.add_insurance"
                                        class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                                    />
                                    <span class="text-sm text-gray-700">Travel Insurance (৳1,500 per passenger)</span>
                                </label>
                                <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(passengers * 1500) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-gray-700">Extra Baggage (৳2,000 per 5kg)</label>
                                <select 
                                    v-model="bookingForm.extra_baggage_count"
                                    class="px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-sky-500 focus:ring-0 text-sm"
                                >
                                    <option :value="0">None</option>
                                    <option v-for="n in 5" :key="n" :value="n">{{ n }} × 5kg</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="nextStep"
                        class="w-full mt-6 bg-sky-600 text-white py-4 rounded-xl font-semibold hover:bg-sky-700 transition-all"
                    >
                        Continue to Contact Details
                    </button>
                </div>

                <!-- Step 2: Contact Information -->
                <div v-show="currentStep === 2" class="bg-white rounded-2xl p-6 shadow-lg">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Contact Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name *</label>
                            <input 
                                v-model="bookingForm.contact_name"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input 
                                v-model="bookingForm.contact_email"
                                type="email"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input 
                                v-model="bookingForm.contact_phone"
                                type="tel"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                placeholder="+880 1XXX-XXXXXX"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests (Optional)</label>
                            <textarea 
                                v-model="bookingForm.special_requests"
                                rows="3"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                placeholder="e.g., Wheelchair assistance, special meal, etc."
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button
                            @click="prevStep"
                            class="flex-1 bg-gray-200 text-gray-700 py-4 rounded-xl font-semibold hover:bg-gray-300 transition-all"
                        >
                            Back
                        </button>
                        <button
                            @click="nextStep"
                            class="flex-1 bg-sky-600 text-white py-4 rounded-xl font-semibold hover:bg-sky-700 transition-all"
                        >
                            Continue to Payment
                        </button>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div v-show="currentStep === 3" class="space-y-4">
                    <!-- Price Breakdown -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Price Breakdown</h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Base Fare ({{ passengers }} × {{ formatCurrency(pricing.base_fare) }})</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(pricing.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Taxes & Fees</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(pricing.taxes_fees) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Booking Fee</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(pricing.booking_fee) }}</span>
                            </div>
                            <div v-if="bookingForm.extra_baggage_count > 0" class="flex justify-between text-sm">
                                <span class="text-gray-600">Extra Baggage ({{ bookingForm.extra_baggage_count }} × ৳2,000)</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(bookingForm.extra_baggage_count * 2000) }}</span>
                            </div>
                            <div v-if="bookingForm.add_insurance" class="flex justify-between text-sm">
                                <span class="text-gray-600">Travel Insurance ({{ passengers }} × ৳1,500)</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(passengers * 1500) }}</span>
                            </div>
                            <div class="pt-3 border-t-2 border-gray-200 flex justify-between">
                                <span class="font-bold text-gray-900">Total Amount</span>
                                <span class="text-2xl font-bold text-sky-600">{{ formatCurrency(totalAmount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Payment -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <CreditCardIcon class="h-6 w-6 mr-2 text-sky-600" />
                            Payment Method
                        </h2>
                        
                        <div class="p-4 bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-600">Wallet Balance</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ formatCurrency(walletBalance) }}</div>
                                </div>
                                <div class="bg-white p-3 rounded-lg">
                                    💳
                                </div>
                            </div>
                        </div>

                        <div v-if="!hasSufficientBalance" class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
                            <div class="flex items-center space-x-2 text-red-700">
                                <ShieldCheckIcon class="h-5 w-5" />
                                <span class="text-sm font-medium">Insufficient Balance</span>
                            </div>
                            <p class="text-sm text-red-600 mt-1">
                                You need {{ formatCurrency(totalAmount - walletBalance) }} more to complete this booking.
                            </p>
                            <Link
                                :href="route('wallet.index')"
                                class="inline-block mt-3 text-sm font-semibold text-red-700 hover:text-red-800 underline"
                            >
                                Add Funds to Wallet →
                            </Link>
                        </div>

                        <div v-else class="p-4 bg-green-50 border border-green-200 rounded-xl mb-4">
                            <div class="flex items-center space-x-2 text-green-700">
                                <CheckCircleIcon class="h-5 w-5" />
                                <span class="text-sm font-medium">Sufficient Balance Available</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button
                            @click="prevStep"
                            class="flex-1 bg-gray-200 text-gray-700 py-4 rounded-xl font-semibold hover:bg-gray-300 transition-all"
                        >
                            Back
                        </button>
                        <button
                            @click="submitBooking"
                            :disabled="!hasSufficientBalance || bookingForm.processing"
                            class="flex-1 bg-gradient-to-r from-sky-600 to-blue-600 text-white py-4 rounded-xl font-semibold hover:from-sky-700 hover:to-blue-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ bookingForm.processing ? 'Processing...' : 'Confirm Booking' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
