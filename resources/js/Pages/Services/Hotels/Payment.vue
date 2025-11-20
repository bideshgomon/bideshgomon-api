<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    booking: Object,
});

const form = useForm({
    payment_method: 'wallet',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};

const processPayment = () => {
    form.post(route('hotels.bookings.process-payment', props.booking.id));
};
</script>

<template>
    <Head title="Payment" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Header -->
                    <div class="bg-indigo-600 text-white p-6">
                        <h1 class="text-2xl font-bold">Complete Your Payment</h1>
                        <p class="text-indigo-100 mt-1">Booking Reference: {{ booking.booking_reference }}</p>
                    </div>

                    <div class="p-6">
                        <!-- Booking Summary -->
                        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-3">Booking Details</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Hotel:</span>
                                    <p class="font-medium">{{ booking.hotel.name }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Room:</span>
                                    <p class="font-medium">{{ booking.room.room_type }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Check-in:</span>
                                    <p class="font-medium">{{ booking.check_in_date }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Check-out:</span>
                                    <p class="font-medium">{{ booking.check_out_date }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Guests:</span>
                                    <p class="font-medium">{{ booking.adults_count }} Adults, {{ booking.children_count }} Children</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Rooms:</span>
                                    <p class="font-medium">{{ booking.rooms_count }} Room(s)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="mb-8">
                            <h3 class="font-semibold text-gray-900 mb-4">Price Breakdown</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-700">
                                    <span>Subtotal ({{ booking.nights }} nights × {{ booking.rooms_count }} room)</span>
                                    <span>{{ formatPrice(booking.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Tax (5%)</span>
                                    <span>{{ formatPrice(booking.tax_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Service Charge</span>
                                    <span>{{ formatPrice(booking.service_charge) }}</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                                    <span class="text-2xl font-bold text-indigo-600">{{ formatPrice(booking.total_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <form @submit.prevent="processPayment">
                            <div class="mb-8">
                                <h3 class="font-semibold text-gray-900 mb-4">Select Payment Method</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                        :class="{ 'border-indigo-600 bg-indigo-50': form.payment_method === 'wallet' }">
                                        <input v-model="form.payment_method" type="radio" value="wallet" class="text-indigo-600 focus:ring-indigo-500" />
                                        <div class="ml-3 flex-1">
                                            <div class="font-medium text-gray-900">Pay from Wallet</div>
                                            <div class="text-sm text-gray-600">Use your wallet balance for instant payment</div>
                                        </div>
                                        <div class="text-2xl">💳</div>
                                    </label>

                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                        :class="{ 'border-indigo-600 bg-indigo-50': form.payment_method === 'card' }">
                                        <input v-model="form.payment_method" type="radio" value="card" class="text-indigo-600 focus:ring-indigo-500" />
                                        <div class="ml-3 flex-1">
                                            <div class="font-medium text-gray-900">Credit/Debit Card</div>
                                            <div class="text-sm text-gray-600">Pay securely with your card</div>
                                        </div>
                                        <div class="text-2xl">💳</div>
                                    </label>

                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                        :class="{ 'border-indigo-600 bg-indigo-50': form.payment_method === 'cash' }">
                                        <input v-model="form.payment_method" type="radio" value="cash" class="text-indigo-600 focus:ring-indigo-500" />
                                        <div class="ml-3 flex-1">
                                            <div class="font-medium text-gray-900">Pay at Hotel</div>
                                            <div class="text-sm text-gray-600">Pay in cash when you check in</div>
                                        </div>
                                        <div class="text-2xl">💵</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Security Notice -->
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3 text-sm text-blue-800">
                                        <p class="font-semibold">Secure Payment</p>
                                        <p>Your payment information is encrypted and secure. We never store your card details.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-4">
                                <Link :href="route('hotels.show', booking.hotel_id)" 
                                    class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition text-center">
                                    Cancel
                                </Link>
                                <button type="submit" :disabled="form.processing"
                                    class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span v-if="form.processing">Processing...</span>
                                    <span v-else>Complete Payment {{ formatPrice(booking.total_amount) }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
