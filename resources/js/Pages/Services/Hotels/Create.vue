<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    hotel: Object,
    room: Object,
    booking_details: Object,
});

const form = useForm({
    hotel_id: props.hotel.id,
    hotel_room_id: props.room.id,
    check_in_date: props.booking_details.check_in,
    check_out_date: props.booking_details.check_out,
    rooms_count: props.booking_details.rooms_count,
    adults_count: props.booking_details.adults_count,
    children_count: props.booking_details.children_count,
    guests: Array.from({ length: props.booking_details.adults_count }, () => ({ name: '', age: null })),
    guest_name: '',
    guest_email: '',
    guest_phone: '',
    special_requests: '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};

const submit = () => {
    form.post(route('hotels.bookings.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Book ${room.room_type} at ${hotel.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('hotels.show', hotel.id)" class="text-indigo-600 hover:text-indigo-800">
                        ← Back to Hotel
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Booking Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Booking</h1>

                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Guest Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Primary Guest Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                            <input v-model="form.guest_name" type="text" required
                                                class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                                            <span v-if="form.errors.guest_name" class="text-sm text-red-600">{{ form.errors.guest_name }}</span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                            <input v-model="form.guest_email" type="email" required
                                                class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                                            <span v-if="form.errors.guest_email" class="text-sm text-red-600">{{ form.errors.guest_email }}</span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                            <input v-model="form.guest_phone" type="tel" required
                                                class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                                            <span v-if="form.errors.guest_phone" class="text-sm text-red-600">{{ form.errors.guest_phone }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- All Guests Details -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Guest Details</h3>
                                    <div v-for="(guest, index) in form.guests" :key="index" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                        <h4 class="font-medium text-gray-700 mb-3">Guest {{ index + 1 }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                                <input v-model="guest.name" type="text" required
                                                    class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                                                <input v-model="guest.age" type="number" min="1" max="120"
                                                    class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Special Requests -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests (Optional)</label>
                                    <textarea v-model="form.special_requests" rows="4"
                                        placeholder="Any special requirements or requests..."
                                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="border-t pt-6">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                        <h4 class="font-semibold text-yellow-800 mb-2">Cancellation Policy</h4>
                                        <p class="text-sm text-yellow-700">{{ hotel.cancellation_policy || 'Free cancellation up to 24 hours before check-in.' }}</p>
                                    </div>
                                    
                                    <label class="flex items-start">
                                        <input type="checkbox" required class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                        <span class="ml-2 text-sm text-gray-700">
                                            I agree to the hotel's cancellation policy and house rules. I understand that payment will be processed immediately.
                                        </span>
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" :disabled="form.processing"
                                    class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span v-if="form.processing">Processing...</span>
                                    <span v-else>Proceed to Payment</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Booking Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>

                            <!-- Hotel Info -->
                            <div class="mb-6">
                                <img :src="hotel.featured_image" :alt="hotel.name" class="w-full h-32 object-cover rounded-lg mb-3" />
                                <h4 class="font-semibold text-gray-900">{{ hotel.name }}</h4>
                                <p class="text-sm text-gray-600">{{ hotel.city }}, {{ hotel.country }}</p>
                                <div class="text-yellow-500 text-sm mt-1">{{ '⭐'.repeat(hotel.star_rating) }}</div>
                            </div>

                            <!-- Room Info -->
                            <div class="border-t pt-4 mb-4">
                                <h5 class="font-medium text-gray-900 mb-2">{{ room.room_type }}</h5>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p>📅 {{ booking_details.check_in }} to {{ booking_details.check_out }}</p>
                                    <p>🌙 {{ booking_details.nights }} night(s)</p>
                                    <p>🏠 {{ booking_details.rooms_count }} room(s)</p>
                                    <p>👥 {{ booking_details.adults_count }} adult(s), {{ booking_details.children_count }} child(ren)</p>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Room × {{ booking_details.nights }} night(s)</span>
                                    <span>{{ formatPrice(booking_details.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Tax (5%)</span>
                                    <span>{{ formatPrice(booking_details.tax_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Service Charge</span>
                                    <span>{{ formatPrice(booking_details.service_charge) }}</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between items-center">
                                    <span class="font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-indigo-600">{{ formatPrice(booking_details.total_amount) }}</span>
                                </div>
                            </div>

                            <div class="mt-6 p-3 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-800">✓ Best Price Guarantee</p>
                                <p class="text-sm text-green-800">✓ Instant Confirmation</p>
                                <p class="text-sm text-green-800">✓ Secure Payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
