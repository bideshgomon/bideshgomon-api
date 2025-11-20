<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    booking: Object,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};

const printConfirmation = () => {
    window.print();
};
</script>

<template>
    <Head title="Booking Confirmed" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Success Header -->
                    <div class="bg-green-600 text-white p-8 text-center">
                        <div class="inline-block bg-white rounded-full p-3 mb-4">
                            <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold mb-2">Booking Confirmed!</h1>
                        <p class="text-green-100">Your hotel reservation has been successfully confirmed</p>
                    </div>

                    <div class="p-8">
                        <!-- Booking Reference -->
                        <div class="text-center mb-8 p-4 bg-indigo-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Booking Reference</p>
                            <p class="text-3xl font-bold text-indigo-600">{{ booking.booking_reference }}</p>
                            <p class="text-sm text-gray-600 mt-2">Please save this reference number for your records</p>
                        </div>

                        <!-- Hotel Information -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Hotel Details</h2>
                            <div class="flex items-start space-x-4">
                                <img :src="booking.hotel.featured_image" :alt="booking.hotel.name" 
                                    class="w-32 h-24 object-cover rounded-lg" />
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ booking.hotel.name }}</h3>
                                    <p class="text-gray-600">{{ booking.hotel.address }}</p>
                                    <p class="text-gray-600">{{ booking.hotel.city }}, {{ booking.hotel.country }}</p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-yellow-500">{{ '⭐'.repeat(booking.hotel.star_rating) }}</span>
                                        <span class="text-sm text-gray-600">{{ booking.hotel.phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Check-in</h3>
                                    <p class="text-lg font-medium text-gray-900">{{ booking.check_in_date }}</p>
                                    <p class="text-sm text-gray-600">From {{ booking.hotel.check_in_time }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Check-out</h3>
                                    <p class="text-lg font-medium text-gray-900">{{ booking.check_out_date }}</p>
                                    <p class="text-sm text-gray-600">Until {{ booking.hotel.check_out_time }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Room Type</h3>
                                    <p class="text-lg font-medium text-gray-900">{{ booking.room.room_type }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Guests</h3>
                                    <p class="text-lg font-medium text-gray-900">
                                        {{ booking.adults_count }} Adults, {{ booking.children_count }} Children
                                    </p>
                                    <p class="text-sm text-gray-600">{{ booking.rooms_count }} Room(s) for {{ booking.nights }} night(s)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Information -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Guest Information</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="font-medium text-gray-900">{{ booking.guest_name }}</p>
                                <p class="text-gray-600">{{ booking.guest_email }}</p>
                                <p class="text-gray-600">{{ booking.guest_phone }}</p>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="mb-8 border-t pt-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Summary</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between text-gray-700">
                                    <span>Subtotal</span>
                                    <span>{{ formatPrice(booking.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Tax</span>
                                    <span>{{ formatPrice(booking.tax_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Service Charge</span>
                                    <span>{{ formatPrice(booking.service_charge) }}</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Paid</span>
                                    <span class="text-2xl font-bold text-green-600">{{ formatPrice(booking.total_amount) }}</span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Payment Method: <span class="font-medium capitalize">{{ booking.payment_method }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Important Information -->
                        <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h3 class="font-semibold text-yellow-800 mb-2">Important Information</h3>
                            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                <li>A confirmation email has been sent to {{ booking.guest_email }}</li>
                                <li>Please bring a valid ID for check-in</li>
                                <li>{{ booking.hotel.cancellation_policy || 'Check cancellation policy on your booking' }}</li>
                                <li>Early check-in and late check-out subject to availability</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button @click="printConfirmation"
                                class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition text-center">
                                🖨️ Print Confirmation
                            </button>
                            <Link :href="route('hotels.my-bookings')"
                                class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition text-center">
                                View All Bookings
                            </Link>
                        </div>

                        <div class="mt-6 text-center">
                            <Link :href="route('hotels.index')" class="text-indigo-600 hover:text-indigo-800">
                                ← Back to Hotel Search
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
