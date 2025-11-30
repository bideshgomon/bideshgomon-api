<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    booking: Object,
});

const showCancelModal = ref(false);
const cancelForm = useForm({
    reason: '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        checked_in: 'bg-blue-100 text-blue-800',
        checked_out: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const cancelBooking = () => {
    cancelForm.post(route('hotels.bookings.cancel', props.booking.id), {
        onSuccess: () => {
            showCancelModal.value = false;
            cancelForm.reset();
        }
    });
};
</script>

<template>
    <Head :title="`Booking ${booking.booking_reference}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('hotels.my-bookings')" class="text-indigo-600 hover:text-indigo-800">
                        ← Back to My Bookings
                    </Link>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Header -->
                    <div class="bg-heritage-600 text-white p-8">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-bold mb-2">Booking Details</h1>
                                <p class="text-indigo-100">Reference: {{ booking.booking_reference }}</p>
                            </div>
                            <span :class="['px-4 py-2 rounded-full text-sm font-semibold', getStatusColor(booking.status)]">
                                {{ booking.status_badge.text }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- Hotel Information -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Hotel Information</h2>
                            <div class="flex items-start space-x-6">
                                <img :src="booking.hotel.featured_image" :alt="booking.hotel.name" 
                                    class="w-48 h-36 object-cover rounded-lg shadow-md" />
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ booking.hotel.name }}</h3>
                                    <div class="space-y-1 text-gray-600">
                                        <p>📍 {{ booking.hotel.address }}</p>
                                        <p>🏙️ {{ booking.hotel.city }}, {{ booking.hotel.country }}</p>
                                        <p>📞 {{ booking.hotel.phone }}</p>
                                        <p v-if="booking.hotel.email">✉️ {{ booking.hotel.email }}</p>
                                    </div>
                                    <div class="mt-3 text-yellow-500">
                                        {{ '⭐'.repeat(booking.hotel.star_rating) }} {{ booking.hotel.star_rating }}-Star Hotel
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stay Details</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-gray-600">Check-in</span>
                                        <p class="text-lg font-medium text-gray-900">{{ booking.check_in_date }}</p>
                                        <p class="text-sm text-gray-500">From {{ booking.hotel.check_in_time }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Check-out</span>
                                        <p class="text-lg font-medium text-gray-900">{{ booking.check_out_date }}</p>
                                        <p class="text-sm text-gray-500">Until {{ booking.hotel.check_out_time }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Duration</span>
                                        <p class="text-lg font-medium text-gray-900">{{ booking.nights }} night(s)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Room & Guests</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-gray-600">Room Type</span>
                                        <p class="text-lg font-medium text-gray-900">{{ booking.room.room_type }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Rooms</span>
                                        <p class="text-lg font-medium text-gray-900">{{ booking.rooms_count }} room(s)</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Guests</span>
                                        <p class="text-lg font-medium text-gray-900">
                                            {{ booking.adults_count }} Adults
                                            <span v-if="booking.children_count > 0">, {{ booking.children_count }} Children</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Information -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Guest Information</h2>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-600">Name</span>
                                        <p class="font-medium text-gray-900">{{ booking.guest_name }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Email</span>
                                        <p class="font-medium text-gray-900">{{ booking.guest_email }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Phone</span>
                                        <p class="font-medium text-gray-900">{{ booking.guest_phone }}</p>
                                    </div>
                                </div>
                                <div v-if="booking.special_requests" class="mt-4 pt-4 border-t">
                                    <span class="text-sm text-gray-600">Special Requests</span>
                                    <p class="mt-1 text-gray-900">{{ booking.special_requests }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Payment Details</h2>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Room Rate ({{ booking.nights }} nights × {{ booking.rooms_count }} room)</span>
                                        <span class="font-medium">{{ formatPrice(booking.subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Tax (5%)</span>
                                        <span class="font-medium">{{ formatPrice(booking.tax_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Service Charge</span>
                                        <span class="font-medium">{{ formatPrice(booking.service_charge) }}</span>
                                    </div>
                                    <div v-if="booking.discount_amount > 0" class="flex justify-between text-green-600">
                                        <span>Discount</span>
                                        <span class="font-medium">-{{ formatPrice(booking.discount_amount) }}</span>
                                    </div>
                                    <div class="border-t pt-3 flex justify-between items-center">
                                        <span class="text-xl font-semibold text-gray-900">Total Amount</span>
                                        <span class="text-2xl font-bold text-indigo-600">{{ formatPrice(booking.total_amount) }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                    <div>
                                        <span class="text-sm text-gray-600">Payment Method</span>
                                        <p class="font-medium text-gray-900 capitalize">{{ booking.payment_method }}</p>
                                    </div>
                                    <div>
                                        <span :class="['px-3 py-1 rounded-full text-sm font-semibold', 
                                            booking.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                            booking.payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-gray-100 text-gray-800']">
                                            {{ booking.payment_badge.text }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="booking.paid_at" class="mt-2 text-sm text-gray-600">
                                    Paid on {{ booking.paid_at }}
                                </div>
                            </div>
                        </div>

                        <!-- Important Dates -->
                        <div v-if="booking.confirmed_at || booking.checked_in_at || booking.checked_out_at || booking.cancelled_at" class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Booking Timeline</h2>
                            <div class="space-y-2">
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Created: {{ booking.created_at }}</span>
                                </div>
                                <div v-if="booking.confirmed_at" class="flex items-center text-green-700">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Confirmed: {{ booking.confirmed_at }}</span>
                                </div>
                                <div v-if="booking.checked_in_at" class="flex items-center text-blue-700">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                    <span>Checked In: {{ booking.checked_in_at }}</span>
                                </div>
                                <div v-if="booking.checked_out_at" class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Checked Out: {{ booking.checked_out_at }}</span>
                                </div>
                                <div v-if="booking.cancelled_at" class="flex items-center text-red-700">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Cancelled: {{ booking.cancelled_at }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Reason -->
                        <div v-if="booking.cancellation_reason" class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="font-semibold text-red-800 mb-2">Cancellation Reason</h3>
                            <p class="text-red-700">{{ booking.cancellation_reason }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <Link :href="route('hotels.my-bookings')"
                                class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-300 transition text-center">
                                Back to My Bookings
                            </Link>
                            <button v-if="booking.is_cancellable" @click="showCancelModal = true"
                                class="flex-1 bg-red-50 border-2 border-red-300 text-red-700 py-3 px-6 rounded-lg hover:bg-red-100 transition">
                                Cancel Booking
                            </button>
                            <Link v-if="booking.status === 'confirmed' || booking.status === 'checked_in'" 
                                :href="route('hotels.show', booking.hotel_id)"
                                class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition text-center">
                                View Hotel
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Cancel Modal -->
                <div v-if="showCancelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-lg max-w-md w-full p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Cancel Booking</h3>
                        <p class="text-gray-600 mb-4">
                            Are you sure you want to cancel this booking? This action cannot be undone.
                        </p>
                        <form @submit.prevent="cancelBooking">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for cancellation *</label>
                                <textarea v-model="cancelForm.reason" rows="3" required
                                    class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Please provide a reason..."></textarea>
                                <span v-if="cancelForm.errors.reason" class="text-sm text-red-600">{{ cancelForm.errors.reason }}</span>
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" @click="showCancelModal = false"
                                    class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                                    Keep Booking
                                </button>
                                <button type="submit" :disabled="cancelForm.processing"
                                    class="flex-1 bg-red-50 border-2 border-red-300 text-red-700 py-2 px-4 rounded-lg hover:bg-red-100 transition disabled:opacity-50">
                                    <span v-if="cancelForm.processing">Processing...</span>
                                    <span v-else>Cancel Booking</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
