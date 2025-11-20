<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ShieldCheckIcon, 
    ClockIcon,
    MapPinIcon,
    DocumentTextIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    bookings: Object,
});

const formatCurrency = (amount) => {
    return '৳ ' + Number(amount).toLocaleString('en-BD', { minimumFractionDigits: 2 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-BD', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        active: 'bg-green-100 text-green-800',
        expired: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        paid: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        refunded: 'bg-purple-100 text-purple-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="My Travel Insurance Bookings" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 pb-20">
            <!-- Header -->
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white px-4 pt-8 pb-32">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center space-x-3 mb-4">
                        <ShieldCheckIcon class="h-10 w-10" />
                        <h1 class="text-3xl font-bold">My Travel Insurance</h1>
                    </div>
                    <p class="text-emerald-100 text-lg">
                        View and manage your travel insurance policies
                    </p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto px-4 -mt-24">
                <!-- Bookings List -->
                <div v-if="bookings.data.length > 0" class="space-y-4">
                    <Link
                        v-for="booking in bookings.data"
                        :key="booking.id"
                        :href="route('travel-insurance.booking-details', booking.id)"
                        class="block bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all"
                    >
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">{{ booking.package.name }}</h3>
                                    <ChevronRightIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ booking.booking_reference }}
                                    </span>
                                    <span v-if="booking.policy_number" class="font-mono text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded">
                                        {{ booking.policy_number }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-emerald-600">{{ formatCurrency(booking.total_amount) }}</div>
                                <span
                                    :class="getPaymentStatusColor(booking.payment_status)"
                                    class="inline-block text-xs font-bold px-2 py-1 rounded-full mt-1"
                                >
                                    {{ booking.payment_status }}
                                </span>
                            </div>
                        </div>

                        <!-- Trip Details -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div class="flex items-center space-x-2 text-sm">
                                <MapPinIcon class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <span class="text-gray-700">
                                    {{ booking.destination_country.flag_emoji }} {{ booking.destination_country.name }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm">
                                <ClockIcon class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                <span class="text-gray-700">{{ booking.duration_days }} days</span>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="bg-gray-50 rounded-xl p-3 mb-3">
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <div class="text-gray-500 text-xs mb-1">Start Date</div>
                                    <div class="font-semibold text-gray-900">{{ formatDate(booking.trip_start_date) }}</div>
                                </div>
                                <div class="text-gray-300">→</div>
                                <div class="text-right">
                                    <div class="text-gray-500 text-xs mb-1">End Date</div>
                                    <div class="font-semibold text-gray-900">{{ formatDate(booking.trip_end_date) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <DocumentTextIcon class="h-4 w-4" />
                                <span>{{ booking.travelers_count }} traveler(s)</span>
                            </div>
                            <span
                                :class="getStatusColor(booking.status)"
                                class="text-xs font-bold px-3 py-1 rounded-full"
                            >
                                {{ booking.status }}
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl p-12 text-center shadow-sm">
                    <ShieldCheckIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Travel Insurance Yet</h3>
                    <p class="text-gray-500 mb-6">Protect your journey with comprehensive travel insurance</p>
                    <Link
                        :href="route('travel-insurance.index')"
                        class="inline-block bg-emerald-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all"
                    >
                        Browse Packages
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.data.length > 0 && (bookings.prev_page_url || bookings.next_page_url)" class="mt-6 flex justify-center space-x-2">
                    <Link
                        v-if="bookings.prev_page_url"
                        :href="bookings.prev_page_url"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="bookings.next_page_url"
                        :href="bookings.next_page_url"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
