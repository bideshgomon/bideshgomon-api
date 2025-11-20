<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    bookings: Object,
    stats: Object,
    hotels: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const hotelId = ref(props.filters.hotel_id || '');
const status = ref(props.filters.status || '');
const paymentStatus = ref(props.filters.payment_status || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const applyFilters = () => {
    router.get(route('admin.hotel-bookings.index'), {
        search: search.value,
        hotel_id: hotelId.value,
        status: status.value,
        payment_status: paymentStatus.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const updateStatus = (bookingId, newStatus) => {
    router.post(route('admin.hotel-bookings.update-status', bookingId), {
        status: newStatus,
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        checked_in: 'bg-green-100 text-green-800',
        checked_out: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        paid: 'bg-green-100 text-green-800',
        partially_paid: 'bg-blue-100 text-blue-800',
        refunded: 'bg-purple-100 text-purple-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Hotel Bookings - Admin" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800">Hotel Bookings Management</h2>
                        <p class="text-gray-600 mt-1">Monitor and manage all hotel reservations</p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 p-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-blue-600 text-xs font-medium">Total</div>
                            <div class="text-xl font-bold text-blue-900">{{ stats.total_bookings }}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-yellow-600 text-xs font-medium">Pending</div>
                            <div class="text-xl font-bold text-yellow-900">{{ stats.pending }}</div>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <div class="text-indigo-600 text-xs font-medium">Confirmed</div>
                            <div class="text-xl font-bold text-indigo-900">{{ stats.confirmed }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 text-xs font-medium">Active</div>
                            <div class="text-xl font-bold text-green-900">{{ stats.active }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-xs font-medium">Completed</div>
                            <div class="text-xl font-bold text-gray-900">{{ stats.completed }}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-red-600 text-xs font-medium">Cancelled</div>
                            <div class="text-xl font-bold text-red-900">{{ stats.cancelled }}</div>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-lg">
                            <div class="text-emerald-600 text-xs font-medium">Revenue</div>
                            <div class="text-xl font-bold text-emerald-900">৳{{ Number(stats.total_revenue).toLocaleString() }}</div>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="text-orange-600 text-xs font-medium">Pending Pay</div>
                            <div class="text-xl font-bold text-orange-900">৳{{ Number(stats.pending_payment).toLocaleString() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            <input
                                v-model="search"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="Search by booking ID or guest..."
                                class="rounded-lg border-gray-300"
                            />
                            <select
                                v-model="hotelId"
                                @change="applyFilters"
                                class="rounded-lg border-gray-300"
                            >
                                <option value="">All Hotels</option>
                                <option v-for="hotel in hotels" :key="hotel.id" :value="hotel.id">
                                    {{ hotel.name }}
                                </option>
                            </select>
                            <select
                                v-model="status"
                                @change="applyFilters"
                                class="rounded-lg border-gray-300"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <select
                                v-model="paymentStatus"
                                @change="applyFilters"
                                class="rounded-lg border-gray-300"
                            >
                                <option value="">All Payments</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="refunded">Refunded</option>
                            </select>
                            <input
                                v-model="dateFrom"
                                @change="applyFilters"
                                type="date"
                                placeholder="From Date"
                                class="rounded-lg border-gray-300"
                            />
                            <input
                                v-model="dateTo"
                                @change="applyFilters"
                                type="date"
                                placeholder="To Date"
                                class="rounded-lg border-gray-300"
                            />
                        </div>
                    </div>
                </div>

                <!-- Bookings List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="bookings.data.length === 0" class="text-center py-12 text-gray-500">
                            No bookings found
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hotel</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guests</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="booking in bookings.data" :key="booking.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ booking.booking_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ booking.guest_name }}</div>
                                            <div class="text-sm text-gray-500">{{ booking.guest_email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ booking.hotel?.name }}</div>
                                            <div class="text-sm text-gray-500">{{ booking.room?.room_type }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>In: {{ booking.check_in_date }}</div>
                                            <div>Out: {{ booking.check_out_date }}</div>
                                            <div class="text-xs text-gray-400">{{ booking.total_nights }} nights</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>Adults: {{ booking.adults }}</div>
                                            <div v-if="booking.children > 0">Children: {{ booking.children }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            ৳{{ Number(booking.total_amount).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusColor(booking.status)" class="px-2 py-1 rounded text-xs font-medium">
                                                {{ booking.status.replace('_', ' ').toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getPaymentStatusColor(booking.payment_status)" class="px-2 py-1 rounded text-xs font-medium">
                                                {{ booking.payment_status.replace('_', ' ').toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <Link
                                                    :href="route('admin.hotel-bookings.show', booking.id)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    View
                                                </Link>
                                                <button
                                                    v-if="booking.status === 'pending'"
                                                    @click="updateStatus(booking.id, 'confirmed')"
                                                    class="text-green-600 hover:text-green-900"
                                                >
                                                    Confirm
                                                </button>
                                                <button
                                                    v-if="booking.status === 'confirmed'"
                                                    @click="updateStatus(booking.id, 'checked_in')"
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
                                                    Check In
                                                </button>
                                                <button
                                                    v-if="booking.status === 'checked_in'"
                                                    @click="updateStatus(booking.id, 'checked_out')"
                                                    class="text-gray-600 hover:text-gray-900"
                                                >
                                                    Check Out
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="bookings.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link
                                v-for="(link, index) in bookings.links"
                                :key="index"
                                :href="link.url"
                                :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-2 rounded border"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
