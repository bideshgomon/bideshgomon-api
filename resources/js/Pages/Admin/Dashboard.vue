<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    UsersIcon, CurrencyDollarIcon, BriefcaseIcon, 
    WalletIcon, ChartBarIcon, ArrowTrendingUpIcon,
    DocumentTextIcon, ShieldCheckIcon, ClipboardDocumentListIcon,
    ChartPieIcon, CogIcon, BuildingOffice2Icon, PaperAirplaneIcon,
    GlobeAltIcon, UserGroupIcon, RectangleStackIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    recentTransactions: Array,
    recentBookings: Array,
    recentHotelBookings: Array,
    recentVisaApplications: Array,
    userChartData: Array,
    revenueChartData: Array,
    recentImpersonations: Array,
});

const formatCurrency = (amount) => {
    return `৳${parseFloat(amount).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatDateTime = (date) => {
    return new Date(date).toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <!-- Header -->
        <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                        <p class="text-sm text-purple-100 mt-1">Monitor and manage your platform</p>
                    </div>
                    <ChartBarIcon class="h-12 w-12 text-purple-200 opacity-50" />
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Users -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <UsersIcon class="h-6 w-6 text-blue-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            +{{ stats.users.today }} today
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.users.total.toLocaleString() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ stats.users.active }} active this month</p>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            {{ formatCurrency(stats.revenue.today) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.revenue.total) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ formatCurrency(stats.revenue.this_month) }} this month</p>
                    </div>
                </div>

                <!-- Insurance Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 rounded-lg">
                            <ShieldCheckIcon class="h-6 w-6 text-emerald-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            +{{ stats.services.insurance_today }} today
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Insurance Bookings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.services.insurance_bookings }}</p>
                        <p class="text-xs text-gray-500 mt-1">Travel insurance policies</p>
                    </div>
                </div>

                <!-- CVs Created -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <DocumentTextIcon class="h-6 w-6 text-blue-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            +{{ stats.services.cvs_today }} today
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">CVs Created</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.services.cvs_created }}</p>
                        <p class="text-xs text-gray-500 mt-1">Professional CVs built</p>
                    </div>
                </div>
            </div>

            <!-- Hotel Bookings & Flight Requests Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Hotel Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <BuildingOffice2Icon class="h-6 w-6 text-orange-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            +{{ stats.services.hotel_bookings_today }} today
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Hotel Bookings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.services.hotel_bookings }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ stats.services.confirmed_hotel_bookings }} confirmed</p>
                    </div>
                </div>

                <!-- Hotel Revenue -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 rounded-lg">
                            <CurrencyDollarIcon class="h-6 w-6 text-amber-600" />
                        </div>
                        <span class="text-xs font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded">
                            This month
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Hotel Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.services.hotel_revenue_month) }}</p>
                        <p class="text-xs text-gray-500 mt-1">From confirmed bookings</p>
                    </div>
                </div>

                <!-- Flight Requests -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-sky-100 rounded-lg">
                            <PaperAirplaneIcon class="h-6 w-6 text-sky-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            +{{ stats.services.flight_requests_today }} today
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Flight Requests</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.services.flight_requests }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ stats.services.pending_flight_requests }} pending</p>
                    </div>
                </div>

                <!-- Visa Applications -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <DocumentTextIcon class="h-6 w-6 text-purple-600" />
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            +{{ stats.services.visa_applications_today }} today
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Visa Applications</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.services.visa_applications }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ stats.services.approved_visa_applications }} approved</p>
                    </div>
                </div>
            </div>

            <!-- Visa Revenue Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <CurrencyDollarIcon class="h-6 w-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Visa Service Revenue (This Month)</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.services.visa_revenue_month) }}</p>
                        </div>
                    </div>
                    <Link
                        :href="route('admin.visa-applications.index')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700"
                    >
                        Manage Applications
                    </Link>
                </div>
            </div>

            <!-- Quick Access Management -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <RectangleStackIcon class="h-5 w-5 mr-2 text-indigo-600" />
                    Quick Access Management
                </h2>
                
                <!-- Core Services -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Core Services</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            :href="route('admin.service-modules.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <RectangleStackIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Service Modules</p>
                                <p class="text-sm text-gray-600">39 services</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.visa-requirements.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <GlobeAltIcon class="h-6 w-6 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Visa Requirements</p>
                                <p class="text-sm text-gray-600">Country rules</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.agency-assignments.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors">
                                <UserGroupIcon class="h-6 w-6 text-indigo-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Agency Assignments</p>
                                <p class="text-sm text-gray-600">Country allocation</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.users.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <UsersIcon class="h-6 w-6 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">User Management</p>
                                <p class="text-sm text-gray-600">All users</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Travel Services -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Travel Services</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            :href="route('admin.hotels.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-orange-100 rounded-lg group-hover:bg-orange-200 transition-colors">
                                <BuildingOffice2Icon class="h-6 w-6 text-orange-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Hotels</p>
                                <p class="text-sm text-gray-600">Manage hotels</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.hotel-bookings.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-amber-100 rounded-lg group-hover:bg-amber-200 transition-colors">
                                <ClipboardDocumentListIcon class="h-6 w-6 text-amber-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Hotel Bookings</p>
                                <p class="text-sm text-gray-600">Reservations</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.flight-requests.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-sky-50 to-blue-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-sky-100 rounded-lg group-hover:bg-sky-200 transition-colors">
                                <PaperAirplaneIcon class="h-6 w-6 text-sky-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Flight Requests</p>
                                <p class="text-sm text-gray-600">Bookings</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.visa-applications.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <DocumentTextIcon class="h-6 w-6 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Visa Applications</p>
                                <p class="text-sm text-gray-600">Process visas</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Employment Services -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Employment Services</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            :href="route('admin.jobs.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors">
                                <BriefcaseIcon class="h-6 w-6 text-indigo-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Job Postings</p>
                                <p class="text-sm text-gray-600">Manage jobs</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.applications.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <ClipboardDocumentListIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Job Applications</p>
                                <p class="text-sm text-gray-600">Review applicants</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- System & Analytics -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">System & Analytics</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            :href="route('admin.analytics.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-green-50 to-teal-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                                <ChartPieIcon class="h-6 w-6 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Analytics</p>
                                <p class="text-sm text-gray-600">Insights & reports</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.settings.index')"
                            class="flex items-center p-4 bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-gray-100 rounded-lg group-hover:bg-gray-200 transition-colors">
                                <CogIcon class="h-6 w-6 text-gray-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Settings</p>
                                <p class="text-sm text-gray-600">Platform config</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Wallet Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <WalletIcon class="h-6 w-6 text-purple-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Wallet Balance</p>
                            <p class="text-xl font-bold text-gray-900">{{ formatCurrency(stats.wallet.total_balance) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <ArrowTrendingUpIcon class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Transactions</p>
                            <p class="text-xl font-bold text-gray-900">{{ stats.wallet.total_transactions.toLocaleString() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <BriefcaseIcon class="h-6 w-6 text-orange-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pending Withdrawals</p>
                            <p class="text-xl font-bold text-gray-900">{{ stats.wallet.pending_withdrawals }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Registrations Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">User Registrations (Last 7 Days)</h3>
                    <div v-if="userChartData && userChartData.length > 0" class="space-y-3">
                        <div 
                            v-for="data in userChartData" 
                            :key="data.date"
                            class="flex items-center space-x-3"
                        >
                            <div class="w-20 text-sm text-gray-600">{{ data.date }}</div>
                            <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                                <div 
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-full flex items-center justify-end pr-3 text-white text-sm font-medium transition-all duration-500"
                                    :style="{ width: `${Math.max((data.count / Math.max(...userChartData.map(d => d.count), 1)) * 100, 10)}%` }"
                                >
                                    {{ data.count }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No registration data available
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue (Last 7 Days)</h3>
                    <div v-if="revenueChartData && revenueChartData.length > 0" class="space-y-3">
                        <div 
                            v-for="data in revenueChartData" 
                            :key="data.date"
                            class="flex items-center space-x-3"
                        >
                            <div class="w-20 text-sm text-gray-600">{{ data.date }}</div>
                            <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                                <div 
                                    class="bg-gradient-to-r from-green-500 to-emerald-600 h-full flex items-center justify-end pr-3 text-white text-sm font-medium transition-all duration-500"
                                    :style="{ width: `${Math.max((data.amount / Math.max(...revenueChartData.map(d => d.amount), 1)) * 100, 10)}%` }"
                                >
                                    {{ formatCurrency(data.amount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No revenue data available
                    </div>
                </div>
            </div>

            <!-- Security & Audit: Recent Impersonations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Recent Admin Impersonations</h3>
                    <span class="text-xs text-gray-500">Last 10 sessions</span>
                </div>
                <div v-if="recentImpersonations && recentImpersonations.length" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Admin</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Target User</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Purpose</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Started</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Ended</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Duration</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="log in recentImpersonations" :key="log.id" class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <span class="font-medium text-gray-900" v-if="log.impersonator">{{ log.impersonator.name }}</span>
                                    <span class="text-gray-400" v-else>Unknown</span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="font-medium text-gray-900" v-if="log.target">{{ log.target.name }}</span>
                                    <span class="text-gray-400" v-else>Unknown</span>
                                </td>
                                <td class="px-3 py-2 text-gray-700">
                                    <span class="line-clamp-2 max-w-xs block">{{ log.purpose || '—' }}</span>
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ formatDateTime(log.started_at) }}</td>
                                <td class="px-3 py-2 text-gray-600">
                                    <span v-if="log.ended_at">{{ formatDateTime(log.ended_at) }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-3 py-2 text-gray-600">
                                    <span v-if="log.duration_minutes !== null">{{ log.duration_minutes }} min</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold"
                                        :class="{
                                            'bg-green-100 text-green-700': log.status === 'ended',
                                            'bg-yellow-100 text-yellow-700': log.status === 'active'
                                        }"
                                    >
                                        {{ log.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-8 text-sm text-gray-500">
                    No impersonation sessions recorded yet.
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Recent Users -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Recent Users</h3>
                    </div>
                    <div v-if="recentUsers && recentUsers.length > 0" class="divide-y divide-gray-100">
                        <div 
                            v-for="user in recentUsers" 
                            :key="user.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <p class="font-medium text-gray-900 text-sm">{{ user.name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ user.email }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ formatDate(user.created_at) }}</p>
                        </div>
                    </div>
                    <div v-else class="p-4 text-center text-gray-500 text-sm">
                        No recent users
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Recent Transactions</h3>
                    </div>
                    <div v-if="recentTransactions && recentTransactions.length > 0" class="divide-y divide-gray-100">
                        <div 
                            v-for="transaction in recentTransactions" 
                            :key="transaction.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900 text-sm">{{ transaction.wallet?.user?.name || 'Unknown' }}</p>
                                <span 
                                    class="text-sm font-bold"
                                    :class="transaction.transaction_type === 'credit' ? 'text-green-600' : 'text-red-600'"
                                >
                                    {{ transaction.transaction_type === 'credit' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ transaction.description }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ formatDateTime(transaction.created_at) }}</p>
                        </div>
                    </div>
                    <div v-else class="p-4 text-center text-gray-500 text-sm">
                        No recent transactions
                    </div>
                </div>

                <!-- Recent Insurance Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Insurance Bookings</h3>
                    </div>
                    <div v-if="recentBookings && recentBookings.length > 0" class="divide-y divide-gray-100">
                        <div 
                            v-for="booking in recentBookings" 
                            :key="booking.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <p class="font-medium text-gray-900 text-sm">{{ booking.user?.name || 'Unknown' }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ booking.package?.name || 'Insurance Package' }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500">{{ formatDate(booking.created_at) }}</span>
                                <span class="text-sm font-bold text-emerald-600">{{ formatCurrency(booking.total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-4 text-center text-gray-500 text-sm">
                        No recent insurance bookings
                    </div>
                </div>

                <!-- Recent Hotel Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Hotel Bookings</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div 
                            v-if="recentHotelBookings && recentHotelBookings.length > 0"
                            v-for="booking in recentHotelBookings" 
                            :key="booking.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <p class="font-medium text-gray-900 text-sm">{{ booking.user?.name || 'Unknown' }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ booking.hotel?.name || 'Hotel' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ booking.room?.type || 'Room' }} • {{ booking.booking_reference }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span 
                                    class="text-xs px-2 py-1 rounded-full"
                                    :class="{
                                        'bg-green-50 text-green-700': booking.status === 'confirmed',
                                        'bg-yellow-50 text-yellow-700': booking.status === 'pending',
                                        'bg-blue-50 text-blue-700': booking.status === 'checked_in',
                                        'bg-gray-50 text-gray-700': booking.status === 'checked_out',
                                        'bg-red-50 text-red-700': booking.status === 'cancelled'
                                    }"
                                >
                                    {{ booking.status }}
                                </span>
                                <span class="text-sm font-bold text-orange-600">{{ formatCurrency(booking.total_amount) }}</span>
                            </div>
                        </div>
                        <div v-else class="p-4 text-center text-gray-500 text-sm">
                            No hotel bookings yet
                        </div>
                    </div>
                </div>

                <!-- Recent Visa Applications -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Visa Applications</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div 
                            v-if="recentVisaApplications && recentVisaApplications.length > 0"
                            v-for="application in recentVisaApplications" 
                            :key="application.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <p class="font-medium text-gray-900 text-sm">{{ application.user?.name || 'Unknown' }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ application.destination_country }} • {{ application.visa_type }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ application.application_reference }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span 
                                    class="text-xs px-2 py-1 rounded-full"
                                    :class="{
                                        'bg-green-50 text-green-700': application.status === 'approved',
                                        'bg-blue-50 text-blue-700': application.status === 'submitted',
                                        'bg-yellow-50 text-yellow-700': application.status === 'under_review',
                                        'bg-orange-50 text-orange-700': application.status === 'documents_requested',
                                        'bg-purple-50 text-purple-700': application.status === 'interview_scheduled',
                                        'bg-red-50 text-red-700': application.status === 'rejected',
                                        'bg-gray-50 text-gray-700': application.status === 'cancelled'
                                    }"
                                >
                                    {{ application.status.replace(/_/g, ' ') }}
                                </span>
                                <span class="text-sm font-bold text-purple-600">{{ formatCurrency(application.total_amount) }}</span>
                            </div>
                        </div>
                        <div v-else class="p-4 text-center text-gray-500 text-sm">
                            No visa applications yet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
