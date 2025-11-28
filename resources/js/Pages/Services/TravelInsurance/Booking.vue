<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ArrowLeftIcon,
    CalendarIcon,
    MapPinIcon,
    UserGroupIcon,
    CreditCardIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    package: Object,
    countries: Array,
});

const step = ref(1);

const form = useForm({
    package_id: props.package.id,
    destination_country_id: '',
    trip_start_date: '',
    trip_end_date: '',
    trip_purpose: 'Tourism',
    travelers: [
        {
            name: '',
            age: '',
            passport_number: '',
        },
    ],
});

const duration = computed(() => {
    if (!form.trip_start_date || !form.trip_end_date) return 0;
    const start = new Date(form.trip_start_date);
    const end = new Date(form.trip_end_date);
    const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    return diff > 0 ? diff : 0;
});

const packagePrice = computed(() => {
    if (duration.value === 0 || form.travelers.length === 0) return 0;
    const basePrice = props.package.price_per_day * duration.value * form.travelers.length;
    return Math.max(basePrice, props.package.min_price);
});

const taxAmount = computed(() => {
    return packagePrice.value * 0.05; // 5% tax
});

const totalAmount = computed(() => {
    return packagePrice.value + taxAmount.value;
});

const formatCurrency = (amount) => {
    return '৳ ' + Number(amount).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const addTraveler = () => {
    form.travelers.push({
        name: '',
        age: '',
        passport_number: '',
    });
};

const removeTraveler = (index) => {
    if (form.travelers.length > 1) {
        form.travelers.splice(index, 1);
    }
};

const nextStep = () => {
    if (step.value < 3) {
        step.value++;
    }
};

const prevStep = () => {
    if (step.value > 1) {
        step.value--;
    }
};

const submitBooking = () => {
    form.post(route('travel-insurance.book'), {
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
};

const canProceedStep1 = computed(() => {
    return form.destination_country_id && form.trip_start_date && form.trip_end_date && duration.value > 0;
});

const canProceedStep2 = computed(() => {
    return form.travelers.every(t => t.name && t.age && t.passport_number);
});
</script>

<template>
    <Head title="Book Travel Insurance" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 pb-24">
            <!-- Header -->
            <div class="bg-growth-600 text-white px-4 pt-6 pb-8">
                <div class="max-w-4xl mx-auto">
                    <Link
                        :href="route('travel-insurance.show', package.slug)"
                        class="inline-flex items-center space-x-2 text-emerald-100 hover:text-white mb-4 touch-manipulation"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                        <span class="text-sm font-medium">Back</span>
                    </Link>
                    
                    <h1 class="text-2xl font-bold mb-2">Book: {{ package.name }}</h1>
                    <p class="text-emerald-100 text-sm">{{ formatCurrency(package.price_per_day) }} per day</p>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="max-w-4xl mx-auto px-4 -mt-4">
                <div class="bg-white rounded-2xl p-4 shadow-lg mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col items-center flex-1">
                            <div
                                :class="step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2"
                            >
                                1
                            </div>
                            <span class="text-xs text-gray-600 text-center">Trip Details</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2">
                            <div
                                :class="step >= 2 ? 'bg-emerald-600' : 'bg-gray-200'"
                                class="h-full transition-all"
                                :style="{ width: step >= 2 ? '100%' : '0%' }"
                            ></div>
                        </div>
                        <div class="flex flex-col items-center flex-1">
                            <div
                                :class="step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2"
                            >
                                2
                            </div>
                            <span class="text-xs text-gray-600 text-center">Travelers</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-2">
                            <div
                                :class="step >= 3 ? 'bg-emerald-600' : 'bg-gray-200'"
                                class="h-full transition-all"
                                :style="{ width: step >= 3 ? '100%' : '0%' }"
                            ></div>
                        </div>
                        <div class="flex flex-col items-center flex-1">
                            <div
                                :class="step >= 3 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2"
                            >
                                3
                            </div>
                            <span class="text-xs text-gray-600 text-center">Confirm</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="max-w-4xl mx-auto px-4">
                <!-- Step 1: Trip Details -->
                <div v-if="step === 1" class="bg-white rounded-2xl p-6 shadow-sm space-y-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center mb-4">
                        <MapPinIcon class="h-6 w-6 text-emerald-600 mr-2" />
                        Trip Details
                    </h2>

                    <!-- Destination Country -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Destination Country *
                        </label>
                        <select
                            v-model="form.destination_country_id"
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            required
                        >
                            <option value="">Select destination</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.flag_emoji }} {{ country.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Trip Dates -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <CalendarIcon class="h-4 w-4 inline mr-1" />
                                Start Date *
                            </label>
                            <input
                                v-model="form.trip_start_date"
                                type="date"
                                :min="new Date().toISOString().split('T')[0]"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <CalendarIcon class="h-4 w-4 inline mr-1" />
                                End Date *
                            </label>
                            <input
                                v-model="form.trip_end_date"
                                type="date"
                                :min="form.trip_start_date"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                required
                            />
                        </div>
                    </div>

                    <!-- Duration Display -->
                    <div v-if="duration > 0" class="bg-emerald-50 border-2 border-emerald-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-emerald-900 font-semibold">Trip Duration:</span>
                            <span class="text-emerald-600 font-bold text-lg">{{ duration }} days</span>
                        </div>
                    </div>

                    <!-- Trip Purpose -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Trip Purpose
                        </label>
                        <select
                            v-model="form.trip_purpose"
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="Tourism">Tourism</option>
                            <option value="Business">Business</option>
                            <option value="Study">Study</option>
                            <option value="Medical">Medical Treatment</option>
                            <option value="Hajj/Umrah">Hajj/Umrah</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Next Button -->
                    <button
                        @click="nextStep"
                        :disabled="!canProceedStep1"
                        :class="canProceedStep1 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-300 cursor-not-allowed'"
                        class="w-full text-white font-bold py-4 rounded-2xl transition-all"
                    >
                        Continue to Travelers
                    </button>
                </div>

                <!-- Step 2: Travelers -->
                <div v-if="step === 2" class="bg-white rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <UserGroupIcon class="h-6 w-6 text-emerald-600 mr-2" />
                            Travelers Information
                        </h2>
                        <button
                            @click="addTraveler"
                            class="text-emerald-600 font-semibold text-sm hover:text-emerald-700"
                        >
                            + Add Traveler
                        </button>
                    </div>

                    <!-- Traveler Forms -->
                    <div
                        v-for="(traveler, index) in form.travelers"
                        :key="index"
                        class="border-2 border-gray-200 rounded-2xl p-4 space-y-4"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-gray-900">Traveler {{ index + 1 }}</h3>
                            <button
                                v-if="form.travelers.length > 1"
                                @click="removeTraveler(index)"
                                class="text-red-600 text-sm hover:text-red-700"
                            >
                                Remove
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Full Name (as per passport) *
                            </label>
                            <input
                                v-model="traveler.name"
                                type="text"
                                placeholder="John Doe"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Age *
                                </label>
                                <input
                                    v-model="traveler.age"
                                    type="number"
                                    :min="package.min_age"
                                    :max="package.max_age"
                                    placeholder="25"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Passport Number *
                                </label>
                                <input
                                    v-model="traveler.passport_number"
                                    type="text"
                                    placeholder="BD1234567"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex space-x-4">
                        <button
                            @click="prevStep"
                            class="flex-1 bg-gray-200 text-gray-700 font-bold py-4 rounded-2xl hover:bg-gray-300 transition-all"
                        >
                            Back
                        </button>
                        <button
                            @click="nextStep"
                            :disabled="!canProceedStep2"
                            :class="canProceedStep2 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="flex-1 text-white font-bold py-4 rounded-2xl transition-all"
                        >
                            Continue to Confirm
                        </button>
                    </div>
                </div>

                <!-- Step 3: Confirmation & Payment -->
                <div v-if="step === 3" class="space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm space-y-4">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center mb-4">
                            <CheckCircleIcon class="h-6 w-6 text-emerald-600 mr-2" />
                            Booking Summary
                        </h2>

                        <!-- Package Info -->
                        <div class="pb-4 border-b border-gray-200">
                            <div class="font-semibold text-gray-900 mb-1">{{ package.name }}</div>
                            <div class="text-sm text-gray-600">{{ formatCurrency(package.price_per_day) }} per day</div>
                        </div>

                        <!-- Trip Details -->
                        <div class="space-y-2 text-sm pb-4 border-b border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Destination:</span>
                                <span class="font-medium text-gray-900">
                                    {{ countries.find(c => c.id == form.destination_country_id)?.name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-medium text-gray-900">{{ duration }} days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Travelers:</span>
                                <span class="font-medium text-gray-900">{{ form.travelers.length }} person(s)</span>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Package Price:</span>
                                <span class="font-medium text-gray-900">{{ formatCurrency(packagePrice) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (5%):</span>
                                <span class="font-medium text-gray-900">{{ formatCurrency(taxAmount) }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t-2 border-gray-200">
                                <span class="font-bold text-gray-900 text-lg">Total Amount:</span>
                                <span class="font-bold text-emerald-600 text-xl">{{ formatCurrency(totalAmount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                        <div class="flex items-start space-x-3">
                            <CreditCardIcon class="h-6 w-6 text-blue-600 flex-shrink-0 mt-1" />
                            <div>
                                <h3 class="font-bold text-blue-900 mb-2">Payment via Wallet</h3>
                                <p class="text-sm text-blue-800">
                                    Amount will be deducted from your wallet balance. Make sure you have sufficient funds before confirming.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <label class="flex items-start space-x-3">
                            <input type="checkbox" class="mt-1 h-5 w-5 text-emerald-600 rounded" required />
                            <span class="text-sm text-gray-700">
                                I agree to the <a href="#" class="text-emerald-600 underline">terms and conditions</a> and confirm that all information provided is accurate.
                            </span>
                        </label>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex space-x-4">
                        <button
                            @click="prevStep"
                            class="flex-1 bg-gray-200 text-gray-700 font-bold py-4 rounded-2xl hover:bg-gray-300 transition-all"
                        >
                            Back
                        </button>
                        <button
                            @click="submitBooking"
                            :disabled="form.processing"
                            class="flex-1 bg-growth-600 text-white font-bold py-4 rounded-2xl hover:bg-growth-700 transition-all disabled:opacity-50"
                        >
                            {{ form.processing ? 'Processing...' : 'Confirm & Pay ' + formatCurrency(totalAmount) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.touch-manipulation {
    touch-action: manipulation;
}
</style>
