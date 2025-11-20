<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    MagnifyingGlassIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    XMarkIcon,
    CheckCircleIcon,
    ClockIcon,
    ExclamationCircleIcon,
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    bookings: Object,
    filter: String,
});

const showCancelModal = ref(false);
const cancellingBooking = ref(null);

const cancelForm = useForm({
    cancellation_reason: '',
});

const filterBookings = (filter) => {
    router.get(route('flight-booking.my-bookings', { filter }), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatCurrency = (amount) => {
    return '৳ ' + Number(amount).toLocaleString('en-BD', { minimumFractionDigits: 0 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const formatTime = (time) => {
    return new Date('1970-01-01T' + time).toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
};

const openCancelModal = (booking) => {
    cancellingBooking.value = booking;
    showCancelModal.value = true;
};

const closeCancelModal = () => {
    showCancelModal.value = false;
    cancellingBooking.value = null;
    cancelForm.reset();
};

const submitCancellation = () => {
    if (!cancelForm.cancellation_reason) {
        alert('Please provide a reason for cancellation');
        return;
    }
    
    cancelForm.post(route('flight-booking.cancel', cancellingBooking.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCancelModal();
        }
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200',
        completed: 'bg-blue-100 text-blue-800 border-blue-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getPaymentStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-700',
        paid: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
        refunded: 'bg-blue-100 text-blue-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="My Flight Bookings" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 pb-24">
            <!-- Header -->
            <div class="bg-gradient-to-br from-sky-600 to-blue-800 text-white px-4 pt-6 pb-20">
                <div class="max-w-4xl mx-auto">
                    <Link
                        :href="route('flight-booking.index')"
                        class="inline-flex items-center space-x-2 text-sky-100 hover:text-white mb-4 touch-manipulation"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                        <span class="text-sm font-medium">Back to Search</span>
                    </Link>
                    
                    <h1 class="text-3xl font-bold mb-2">My Flight Bookings</h1>
                    <p class="text-sky-100">View and manage your flight reservations</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto px-4 -mt-12 space-y-4">
                <!-- Filter Tabs -->
                <div class="bg-white rounded-2xl shadow-lg p-2 flex space-x-2 overflow-x-auto">
                    <button
                        @click="filterBookings('all')"
                        :class="[
                            'flex-1 min-w-[80px] px-4 py-3 rounded-xl text-sm font-semibold transition-all touch-manipulation',
                            filter === 'all' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        All
                    </button>
                    <button
                        @click="filterBookings('upcoming')"
                        :class="[
                            'flex-1 min-w-[80px] px-4 py-3 rounded-xl text-sm font-semibold transition-all touch-manipulation',
                            filter === 'upcoming' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        Upcoming
                    </button>
                    <button
                        @click="filterBookings('past')"
                        :class="[
                            'flex-1 min-w-[80px] px-4 py-3 rounded-xl text-sm font-semibold transition-all touch-manipulation',
                            filter === 'past' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        Past
                    </button>
                    <button
                        @click="filterBookings('cancelled')"
                        :class="[
                            'flex-1 min-w-[80px] px-4 py-3 rounded-xl text-sm font-semibold transition-all touch-manipulation',
                            filter === 'cancelled' ? 'bg-sky-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        Cancelled
                    </button>
                </div>

                <!-- Bookings List -->
                <div v-if="bookings.data.length > 0" class="space-y-4">
                    <div 
                        v-for="booking in bookings.data" 
                        :key="booking.id"
                        class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all"
                    >
                        <!-- Booking Header -->
                        <div class="p-4 bg-gradient-to-r from-sky-50 to-blue-50 border-b border-sky-100">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <div class="text-xs text-gray-600">Booking Reference</div>
                                    <div class="font-bold text-gray-900 text-lg">{{ booking.booking_reference }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-600">PNR Number</div>
                                    <div class="font-semibold text-gray-900">{{ booking.pnr_number || 'Pending' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span 
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-semibold border',
                                        getStatusColor(booking.status)
                                    ]"
                                >
                                    {{ booking.status.toUpperCase() }}
                                </span>
                                <span 
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-semibold',
                                        getPaymentStatusColor(booking.payment_status)
                                    ]"
                                >
                                    {{ booking.payment_status.toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <!-- Flight Details -->
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-sky-100 p-2 rounded-lg">
                                        <span class="text-sky-700 font-bold text-sm">{{ booking.flight_route.airline_code }}</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ booking.flight_route.airline_name }}</div>
                                        <div class="text-xs text-gray-500">{{ booking.flight_route.flight_number }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-sky-600">{{ formatCurrency(booking.total_amount) }}</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="text-center flex-1">
                                    <div class="text-2xl font-bold text-gray-900">{{ booking.flight_route.origin_airport_code }}</div>
                                    <div class="text-xs text-gray-600">{{ formatTime(booking.flight_route.departure_time) }}</div>
                                    <div class="text-xs text-gray-500">{{ booking.flight_route.origin_city }}</div>
                                </div>
                                <div class="flex-1 flex flex-col items-center">
                                    <ArrowRightIcon class="h-5 w-5 text-gray-400" />
                                    <div class="text-xs text-gray-500 mt-1">{{ booking.flight_route.flight_duration }}</div>
                                    <div v-if="booking.flight_route.is_direct" class="text-xs text-green-600 font-medium mt-1">Direct</div>
                                </div>
                                <div class="text-center flex-1">
                                    <div class="text-2xl font-bold text-gray-900">{{ booking.flight_route.destination_airport_code }}</div>
                                    <div class="text-xs text-gray-600">{{ formatTime(booking.flight_route.arrival_time) }}</div>
                                    <div class="text-xs text-gray-500">{{ booking.flight_route.destination_city }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 rounded-xl text-sm mb-4">
                                <div>
                                    <span class="text-gray-600">Travel Date:</span>
                                    <div class="font-semibold text-gray-900">{{ formatDate(booking.travel_date) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-600">Passengers:</span>
                                    <div class="font-semibold text-gray-900">{{ booking.passengers_count }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-600">Class:</span>
                                    <div class="font-semibold text-gray-900 capitalize">{{ booking.flight_class.replace('_', ' ') }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-600">Booked On:</span>
                                    <div class="font-semibold text-gray-900">{{ formatDate(booking.created_at) }}</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                <Link
                                    :href="route('flight-booking.booking-details', booking.id)"
                                    class="flex-1 bg-sky-600 text-white text-center py-3 rounded-xl font-semibold hover:bg-sky-700 transition-all text-sm"
                                >
                                    View Details
                                </Link>
                                
                                <Link
                                    v-if="booking.ticket_issued && booking.status === 'confirmed'"
                                    :href="route('flight-booking.download-ticket', booking.id)"
                                    class="flex-1 bg-green-600 text-white text-center py-3 rounded-xl font-semibold hover:bg-green-700 transition-all text-sm"
                                >
                                    Download Ticket
                                </Link>
                                
                                <button
                                    v-if="booking.can_cancel"
                                    @click="openCancelModal(booking)"
                                    class="flex-1 bg-red-600 text-white py-3 rounded-xl font-semibold hover:bg-red-700 transition-all text-sm"
                                >
                                    Cancel Booking
                                </button>
                            </div>

                            <!-- Refund Info (if cancelled) -->
                            <div v-if="booking.status === 'cancelled' && booking.refund_amount" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-blue-700 font-medium">Refund Amount:</span>
                                    <span class="font-bold text-blue-900">{{ formatCurrency(booking.refund_amount) }}</span>
                                </div>
                                <div v-if="booking.cancellation_reason" class="text-xs text-blue-600 mt-1">
                                    Reason: {{ booking.cancellation_reason }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <MagnifyingGlassIcon class="h-10 w-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Bookings Found</h3>
                    <p class="text-gray-600 mb-6">
                        {{ filter === 'upcoming' ? "You don't have any upcoming flights." :
                           filter === 'past' ? "You don't have any past flights." :
                           filter === 'cancelled' ? "You don't have any cancelled bookings." :
                           "You haven't booked any flights yet." }}
                    </p>
                    <Link
                        :href="route('flight-booking.index')"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-600 text-white rounded-xl font-semibold hover:from-sky-700 hover:to-blue-700 transition-all shadow-md"
                    >
                        Search Flights
                        <ArrowRightIcon class="h-5 w-5 ml-2" />
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.data.length > 0 && (bookings.prev_page_url || bookings.next_page_url)" class="flex justify-center space-x-3">
                    <Link
                        v-if="bookings.prev_page_url"
                        :href="bookings.prev_page_url"
                        class="px-5 py-3 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 shadow-md transition-all"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="bookings.next_page_url"
                        :href="bookings.next_page_url"
                        class="px-5 py-3 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 shadow-md transition-all"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div 
            v-if="showCancelModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeCancelModal"
        >
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Cancel Booking</h3>
                        <button 
                            @click="closeCancelModal"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <div v-if="cancellingBooking" class="mb-4">
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl mb-4">
                            <div class="flex items-start space-x-2">
                                <ExclamationCircleIcon class="h-5 w-5 text-yellow-600 mt-0.5 flex-shrink-0" />
                                <div class="text-sm">
                                    <p class="text-yellow-900 font-medium mb-1">Cancellation Fee Applicable</p>
                                    <p class="text-yellow-700">
                                        A {{ cancellingBooking.flight_route.cancellation_fee_percentage }}% cancellation fee will be deducted. 
                                        You'll receive a refund of approximately {{ formatCurrency(cancellingBooking.total_amount * (1 - cancellingBooking.flight_route.cancellation_fee_percentage / 100)) }}.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Cancellation *
                            </label>
                            <textarea
                                v-model="cancelForm.cancellation_reason"
                                rows="4"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-0"
                                placeholder="Please tell us why you're cancelling this booking..."
                                required
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button
                            @click="closeCancelModal"
                            class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all"
                        >
                            Keep Booking
                        </button>
                        <button
                            @click="submitCancellation"
                            :disabled="cancelForm.processing"
                            class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-all disabled:opacity-50"
                        >
                            {{ cancelForm.processing ? 'Cancelling...' : 'Cancel Booking' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
