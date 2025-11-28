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
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';

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
        <!-- Header with AnimatedSection -->
        <AnimatedSection variant="ocean" :blobs="true" :animated="true">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-rhythm-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="font-display font-bold text-rhythm text-4xl sm:text-5xl">
                            <span class="text-ocean-600">Admin</span> Dashboard
                        </h1>
                        <p class="text-ocean-200 mt-rhythm-sm text-lg">Monitor and manage your platform</p>
                    </div>
                    <ChartBarIcon class="h-16 w-16 text-ocean-300 opacity-50" />
                </div>
            </div>
        </AnimatedSection>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-rhythm-xl space-y-rhythm-xl">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-rhythm-lg">
                <!-- Total Users -->
                <RhythmicCard
                    variant="ocean"
                    size="md"
                    title="Total Users"
                    :description="`${stats.users.active} active this month`"
                    :badge="`+${stats.users.today} today`"
                >
                    <template #icon>
                        <UsersIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.users.total.toLocaleString() }}
                    </div>
                </RhythmicCard>

                <!-- Total Revenue -->
                <RhythmicCard
                    variant="growth"
                    size="md"
                    title="Total Revenue"
                    :description="`${formatCurrency(stats.revenue.this_month)} this month`"
                    :badge="formatCurrency(stats.revenue.today)"
                >
                    <template #icon>
                        <CurrencyDollarIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ formatCurrency(stats.revenue.total) }}
                    </div>
                </RhythmicCard>

                <!-- Insurance Bookings -->
                <RhythmicCard
                    variant="sunrise"
                    size="md"
                    title="Insurance Bookings"
                    description="Travel insurance policies"
                    :badge="`+${stats.services.insurance_today} today`"
                >
                    <template #icon>
                        <ShieldCheckIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.services.insurance_bookings }}
                    </div>
                </RhythmicCard>

                <!-- CVs Created -->
                <RhythmicCard
                    variant="sky"
                    size="md"
                    title="CVs Created"
                    description="Professional CVs built"
                    :badge="`+${stats.services.cvs_today} today`"
                >
                    <template #icon>
                        <DocumentTextIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.services.cvs_created }}
                    </div>
                </RhythmicCard>
            </div>

            <!-- Hotel Bookings & Flight Requests Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-rhythm-lg">
                <!-- Hotel Bookings -->
                <RhythmicCard
                    variant="heritage"
                    size="md"
                    title="Hotel Bookings"
                    :description="`${stats.services.confirmed_hotel_bookings} confirmed`"
                    :badge="`+${stats.services.hotel_bookings_today} today`"
                >
                    <template #icon>
                        <BuildingOffice2Icon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.services.hotel_bookings }}
                    </div>
                </RhythmicCard>

                <!-- Hotel Revenue -->
                <RhythmicCard
                    variant="gold"
                    size="md"
                    title="Hotel Revenue"
                    description="From confirmed bookings"
                    badge="This month"
                >
                    <template #icon>
                        <CurrencyDollarIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ formatCurrency(stats.services.hotel_revenue_month) }}
                    </div>
                </RhythmicCard>

                <!-- Flight Requests -->
                <RhythmicCard
                    variant="sky"
                    size="md"
                    title="Flight Requests"
                    :description="`${stats.services.pending_flight_requests} pending`"
                    :badge="`+${stats.services.flight_requests_today} today`"
                >
                    <template #icon>
                        <PaperAirplaneIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.services.flight_requests }}
                    </div>
                </RhythmicCard>

                <!-- Visa Applications -->
                <RhythmicCard
                    variant="ocean"
                    size="md"
                    title="Visa Applications"
                    :description="`${stats.services.approved_visa_applications} approved`"
                    :badge="`+${stats.services.visa_applications_today} today`"
                >
                    <template #icon>
                        <DocumentTextIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.services.visa_applications }}
                    </div>
                </RhythmicCard>

                <!-- Support Tickets -->
                <RhythmicCard
                    variant="sunrise"
                    size="md"
                    title="Support Tickets"
                    :description="`${stats.support.open_tickets} open · ${stats.support.urgent_tickets} urgent`"
                    :badge="`+${stats.support.tickets_today} today`"
                >
                    <template #icon>
                        <ClipboardDocumentListIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.support.total_tickets }}
                    </div>
                </RhythmicCard>

                <!-- Appointments -->
                <RhythmicCard
                    variant="growth"
                    size="md"
                    title="Appointments"
                    :description="`${stats.appointments.pending_appointments} pending · ${stats.appointments.confirmed_appointments} confirmed`"
                    :badge="`+${stats.appointments.appointments_today} today`"
                >
                    <template #icon>
                        <CogIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.appointments.total_appointments }}
                    </div>
                </RhythmicCard>

                <!-- Marketing Campaigns -->
                <RhythmicCard
                    variant="heritage"
                    size="md"
                    title="Marketing Campaigns"
                    :description="`${formatCurrency(stats.campaigns.campaign_reach_month)} reach this month`"
                    :badge="`${stats.campaigns.active_campaigns} active`"
                >
                    <template #icon>
                        <ChartPieIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">
                        {{ stats.campaigns.total_campaigns }}
                    </div>
                </RhythmicCard>
            </div>

            <!-- Visa Revenue Card -->
            <RhythmicCard variant="ocean" size="lg">
                <template #icon>
                    <CurrencyDollarIcon class="h-8 w-8" />
                </template>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium mb-2">Visa Service Revenue (This Month)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.services.visa_revenue_month) }}</p>
                    </div>
                    <Link
                        :href="route('admin.visa-applications.index')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        Manage Applications
                    </Link>
                </div>
            </RhythmicCard>

            <!-- Quick Access Management -->
            <RhythmicCard variant="light" size="lg">
                <div class="space-y-6">
                    <h2 class="font-bold text-2xl flex items-center text-gray-900">
                        <RectangleStackIcon class="h-6 w-6 mr-2 text-gray-600" />
                        Quick Access Management
                    </h2>
                
                <!-- Document Hub System (Priority) -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4 flex items-center">
                        <span class="inline-block w-2 h-2 bg-gray-600 rounded-full mr-2"></span>
                        Document Hub System (International Standards)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            :href="route('admin.master-documents.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group border border-gray-200"
                        >
                            <div class="p-3 bg-indigo-600 rounded-lg group-hover:bg-indigo-700 transition-colors">
                                <DocumentTextIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-3">
                                <p class="font-bold text-gray-900">Master Documents</p>
                                <p class="text-sm text-gray-600">36 documents library</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.document-categories.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group border border-gray-200"
                        >
                            <div class="p-3 bg-indigo-600 rounded-lg group-hover:bg-indigo-700 transition-colors">
                                <RectangleStackIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-3">
                                <p class="font-bold text-gray-900">Document Categories</p>
                                <p class="text-sm text-gray-600">8 categories</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.document-assignments.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group border border-gray-200"
                        >
                            <div class="p-3 bg-indigo-600 rounded-lg group-hover:bg-indigo-700 transition-colors">
                                <GlobeAltIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-3">
                                <p class="font-bold text-gray-900">Country Assignments</p>
                                <p class="text-sm text-gray-600">Document requirements</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Visa Management -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">Visa Management</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            :href="route('admin.visa-requirements.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group border border-gray-200"
                        >
                            <div class="p-3 bg-gray-100 rounded-lg group-hover:bg-gray-200 transition-colors">
                                <DocumentTextIcon class="h-6 w-6 text-gray-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Visa Requirements (Legacy)</p>
                                <p class="text-sm text-gray-600">Old system</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.visa-applications.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group border border-gray-200"
                        >
                            <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors">
                                <ClipboardDocumentListIcon class="h-6 w-6 text-indigo-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Visa Applications</p>
                                <p class="text-sm text-gray-600">Process visas</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('admin.agency-assignments.index')"
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
                        >
                            <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <UserGroupIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Agency Assignments</p>
                                <p class="text-sm text-gray-600">Country allocation</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Core Services -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Core Services</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                        <Link
                            :href="route('admin.service-modules.index')"
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            :href="route('admin.users.index')"
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
                            class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-all group"
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
            </RhythmicCard>

            <!-- Wallet Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-rhythm-lg">
                <RhythmicCard variant="heritage" size="sm">
                    <template #icon>
                        <WalletIcon class="h-8 w-8" />
                    </template>
                    <div>
                        <p class="text-sm text-gray-600 mb-rhythm-xs">Total Wallet Balance</p>
                        <p class="text-display-sm font-bold text-gray-900">{{ formatCurrency(stats.wallet.total_balance) }}</p>
                    </div>
                </RhythmicCard>

                <RhythmicCard variant="ocean" size="sm">
                    <template #icon>
                        <ArrowTrendingUpIcon class="h-8 w-8" />
                    </template>
                    <div>
                        <p class="text-sm text-gray-600 mb-rhythm-xs">Total Transactions</p>
                        <p class="text-display-sm font-bold text-gray-900">{{ stats.wallet.total_transactions.toLocaleString() }}</p>
                    </div>
                </RhythmicCard>

                <RhythmicCard variant="sunrise" size="sm">
                    <template #icon>
                        <BriefcaseIcon class="h-8 w-8" />
                    </template>
                    <div>
                        <p class="text-sm text-gray-600 mb-rhythm-xs">Pending Withdrawals</p>
                        <p class="text-display-sm font-bold text-gray-900">{{ stats.wallet.pending_withdrawals }}</p>
                    </div>
                </RhythmicCard>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-rhythm-lg">
                <!-- User Registrations Chart -->
                <RhythmicCard variant="light" size="lg">
                    <h3 class="font-display font-bold text-xl text-gray-900 mb-rhythm-md">User Registrations (Last 7 Days)</h3>
                    <div v-if="userChartData && userChartData.length > 0" class="space-y-rhythm-sm">
                        <div 
                            v-for="data in userChartData" 
                            :key="data.date"
                            class="flex items-center space-x-rhythm-sm"
                        >
                            <div class="w-20 text-sm text-gray-600">{{ data.date }}</div>
                            <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                                <div 
                                    class="bg-blue-600 h-full flex items-center justify-end pr-3 text-white text-sm font-medium transition-all duration-500"
                                    :style="{ width: `${Math.max((data.count / Math.max(...userChartData.map(d => d.count), 1)) * 100, 10)}%` }"
                                >
                                    {{ data.count }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-rhythm-lg text-gray-500">
                        No registration data available
                    </div>
                </RhythmicCard>

                <!-- Revenue Chart -->
                <RhythmicCard variant="light" size="lg">
                    <h3 class="font-display font-bold text-xl text-gray-900 mb-rhythm-md">Revenue (Last 7 Days)</h3>
                    <div v-if="revenueChartData && revenueChartData.length > 0" class="space-y-rhythm-sm">
                        <div 
                            v-for="data in revenueChartData" 
                            :key="data.date"
                            class="flex items-center space-x-rhythm-sm"
                        >
                            <div class="w-20 text-sm text-gray-600">{{ data.date }}</div>
                            <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden">
                                <div 
                                    class="bg-green-600 h-full flex items-center justify-end pr-3 text-white text-sm font-medium transition-all duration-500"
                                    :style="{ width: `${Math.max((data.amount / Math.max(...revenueChartData.map(d => d.amount), 1)) * 100, 10)}%` }"
                                >
                                    {{ formatCurrency(data.amount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-rhythm-lg text-gray-500">
                        No revenue data available
                    </div>
                </RhythmicCard>
            </div>

            <!-- Security & Audit: Recent Impersonations -->
            <RhythmicCard variant="light" size="lg">
                <div class="flex items-center justify-between mb-rhythm-md">
                    <h3 class="font-display font-bold text-xl text-gray-900">Recent Admin Impersonations</h3>
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
                <div v-else class="text-center py-rhythm-lg text-sm text-gray-500">
                    No impersonation sessions recorded yet.
                </div>
            </RhythmicCard>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-rhythm-lg">
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
