<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    bookings: Object,
    statusCounts: Object,
    currentStatus: String,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};

const changeStatus = (status) => {
    router.get(route('hotels.my-bookings', { status }), {}, {
        preserveState: true,
        preserveScroll: true,
    });
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
</script>

<template>
    <Head title="My Hotel Bookings" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Hotel Bookings</h1>
                        <p class="mt-2 text-gray-600">Manage and track your hotel reservations</p>
                    </div>
                    <Link :href="route('hotels.index')" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        + Book New Hotel
                    </Link>
                </div>

                <!-- Status Tabs -->
                <div class="mb-6 bg-white rounded-lg shadow-md p-2">
                    <div class="flex flex-wrap gap-2">
                        <button @click="changeStatus('all')"
                            :class="[
                                'px-4 py-2 rounded-lg transition',
                                currentStatus === 'all' 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]">
                            All Bookings ({{ statusCounts.all }})
                        </button>
                        <button @click="changeStatus('upcoming')"
                            :class="[
                                'px-4 py-2 rounded-lg transition',
                                currentStatus === 'upcoming' 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]">
                            Upcoming ({{ statusCounts.upcoming }})
                        </button>
                        <button @click="changeStatus('active')"
                            :class="[
                                'px-4 py-2 rounded-lg transition',
                                currentStatus === 'active' 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]">
                            Active ({{ statusCounts.active }})
                        </button>
                        <button @click="changeStatus('past')"
                            :class="[
                                'px-4 py-2 rounded-lg transition',
                                currentStatus === 'past' 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]">
                            Past ({{ statusCounts.past }})
                        </button>
                        <button @click="changeStatus('cancelled')"
                            :class="[
                                'px-4 py-2 rounded-lg transition',
                                currentStatus === 'cancelled' 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]">
                            Cancelled ({{ statusCounts.cancelled }})
                        </button>
                    </div>
                </div>

                <!-- Bookings List -->
                <div v-if="bookings.data.length > 0" class="space-y-6">
                    <div v-for="booking in bookings.data" :key="booking.id" 
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="flex flex-col md:flex-row">
                            <!-- Hotel Image -->
                            <div class="md:w-1/4 h-48 md:h-auto">
                                <img :src="booking.hotel.featured_image" :alt="booking.hotel.name" 
                                    class="w-full h-full object-cover" />
                            </div>

                            <!-- Booking Details -->
                            <div class="md:w-3/4 p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-900">{{ booking.hotel.name }}</h3>
                                        <p class="text-gray-600">{{ booking.hotel.city }}, {{ booking.hotel.country }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ booking.room.room_type }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span :class="['px-3 py-1 rounded-full text-sm font-semibold', getStatusColor(booking.status)]">
                                            {{ booking.status_badge.text }}
                                        </span>
                                        <p class="text-sm text-gray-500 mt-2">{{ booking.booking_reference }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Check-in:</span>
                                        <p class="font-medium text-gray-900">{{ booking.check_in_date }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Check-out:</span>
                                        <p class="font-medium text-gray-900">{{ booking.check_out_date }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Duration:</span>
                                        <p class="font-medium text-gray-900">{{ booking.nights }} night(s)</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Guests:</span>
                                        <p class="font-medium text-gray-900">{{ booking.adults_count }} Adults, {{ booking.children_count }} Children</p>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center pt-4 border-t">
                                    <div>
                                        <span class="text-sm text-gray-600">Total Paid:</span>
                                        <p class="text-xl font-bold text-indigo-600">{{ formatPrice(booking.total_amount) }}</p>
                                    </div>
                                    <div class="flex space-x-3">
                                        <Link :href="route('hotels.bookings.show', booking.id)"
                                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                                            View Details
                                        </Link>
                                        <Link v-if="booking.is_cancellable" 
                                            :href="route('hotels.bookings.show', booking.id)"
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                            Cancel Booking
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No bookings found</h3>
                    <p class="mt-2 text-gray-600">You don't have any {{ currentStatus !== 'all' ? currentStatus : '' }} hotel bookings yet.</p>
                    <Link :href="route('hotels.index')" 
                        class="mt-6 inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
                        Browse Hotels
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.links && bookings.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex space-x-2">
                        <Link 
                            v-for="(link, index) in bookings.links" 
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 rounded-lg border transition',
                                link.active 
                                    ? 'bg-indigo-600 text-white border-indigo-600' 
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                !link.url && 'opacity-50 cursor-not-allowed'
                            ]"
                            :disabled="!link.url"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
