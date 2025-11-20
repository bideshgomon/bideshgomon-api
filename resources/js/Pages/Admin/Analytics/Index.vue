<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    ChartBarIcon,
    UsersIcon,
    CurrencyDollarIcon,
    BriefcaseIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ArrowDownTrayIcon,
    CalendarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    period: String,
    userStats: Object,
    revenueStats: Object,
    jobStats: Object,
    serviceStats: Object,
    userGrowthChart: Array,
    revenueChart: Array,
    topCountries: Array,
    topJobCategories: Array,
    applicationStatusDistribution: Object,
    revenueGrowth: Number,
    userGrowthRate: Number,
    currentMonthRevenue: Number,
    previousMonthRevenue: Number,
});

const selectedPeriod = ref(props.period);

const changePeriod = (period) => {
    selectedPeriod.value = period;
    router.get(route('admin.analytics.index'), { period }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const exportReport = (type) => {
    window.location.href = route('admin.analytics.export', {
        type,
        period: selectedPeriod.value,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', {
        style: 'currency',
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(amount || 0);
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num || 0);
};

const maxRevenueValue = computed(() => {
    return Math.max(...props.revenueChart.map(d => d.amount));
});

const maxUserValue = computed(() => {
    return Math.max(...props.userGrowthChart.map(d => d.count));
});

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    reviewed: 'bg-blue-100 text-blue-800',
    shortlisted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    hired: 'bg-purple-100 text-purple-800',
};
</script>

<template>
    <Head title="Analytics & Reports" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl shadow-lg p-8 mb-8 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Analytics & Reports</h1>
                            <p class="text-blue-100">Comprehensive platform insights and metrics</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <CalendarIcon class="h-6 w-6" />
                            <select
                                v-model="selectedPeriod"
                                @change="changePeriod(selectedPeriod)"
                                class="px-4 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:ring-2 focus:ring-white/50 font-semibold"
                            >
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Growth Indicators -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Revenue Growth</h3>
                            <component
                                :is="revenueGrowth >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon"
                                :class="['h-6 w-6', revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600']"
                            />
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span
                                :class="[
                                    'text-3xl font-bold',
                                    revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600',
                                ]"
                            >
                                {{ revenueGrowth > 0 ? '+' : '' }}{{ revenueGrowth }}%
                            </span>
                            <span class="text-gray-600">vs last month</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Current Month</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(currentMonthRevenue) }}</span>
                            </div>
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-gray-600">Previous Month</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(previousMonthRevenue) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">User Growth</h3>
                            <component
                                :is="userGrowthRate >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon"
                                :class="['h-6 w-6', userGrowthRate >= 0 ? 'text-green-600' : 'text-red-600']"
                            />
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span
                                :class="[
                                    'text-3xl font-bold',
                                    userGrowthRate >= 0 ? 'text-green-600' : 'text-red-600',
                                ]"
                            >
                                {{ userGrowthRate > 0 ? '+' : '' }}{{ userGrowthRate }}%
                            </span>
                            <span class="text-gray-600">vs last month</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">New Users</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatNumber(userStats.new_users) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Active Users</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatNumber(userStats.active_users) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-2">
                            <UsersIcon class="h-8 w-8 text-blue-600" />
                        </div>
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(userStats.total_users) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ formatNumber(userStats.verified_users) }} verified</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-2">
                            <CurrencyDollarIcon class="h-8 w-8 text-green-600" />
                        </div>
                        <p class="text-sm text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(revenueStats.total_revenue) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ formatNumber(revenueStats.total_transactions) }} transactions</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-2">
                            <BriefcaseIcon class="h-8 w-8 text-purple-600" />
                        </div>
                        <p class="text-sm text-gray-600">Job Postings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(jobStats.total_postings) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ formatNumber(jobStats.active_postings) }} active</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-2">
                            <ChartBarIcon class="h-8 w-8 text-indigo-600" />
                        </div>
                        <p class="text-sm text-gray-600">Applications</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(jobStats.total_applications) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ jobStats.application_rate }} per job</p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Revenue Chart -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Revenue Trend (Last 30 Days)</h3>
                            <button
                                @click="exportReport('revenue')"
                                class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center gap-1"
                            >
                                <ArrowDownTrayIcon class="h-4 w-4" />
                                Export
                            </button>
                        </div>
                        <div class="h-64 flex items-end justify-between gap-1">
                            <div
                                v-for="(data, index) in revenueChart"
                                :key="index"
                                class="flex-1 flex flex-col items-center group"
                            >
                                <div
                                    :style="{
                                        height: maxRevenueValue > 0 ? `${(data.amount / maxRevenueValue) * 100}%` : '2%',
                                    }"
                                    class="w-full bg-gradient-to-t from-green-500 to-green-400 rounded-t hover:from-green-600 hover:to-green-500 transition-all relative"
                                >
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                        {{ formatCurrency(data.amount) }}
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 mt-2">{{ data.date }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Growth Chart -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">User Registrations (Last 30 Days)</h3>
                            <button
                                @click="exportReport('users')"
                                class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center gap-1"
                            >
                                <ArrowDownTrayIcon class="h-4 w-4" />
                                Export
                            </button>
                        </div>
                        <div class="h-64 flex items-end justify-between gap-1">
                            <div
                                v-for="(data, index) in userGrowthChart"
                                :key="index"
                                class="flex-1 flex flex-col items-center group"
                            >
                                <div
                                    :style="{
                                        height: maxUserValue > 0 ? `${(data.count / maxUserValue) * 100}%` : '2%',
                                    }"
                                    class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t hover:from-blue-600 hover:to-blue-500 transition-all relative"
                                >
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                        {{ data.count }} users
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 mt-2">{{ data.date }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Insights -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Top Countries -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Countries</h3>
                        <div class="space-y-3">
                            <div
                                v-for="(country, index) in topCountries"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-600">{{ index + 1 }}.</span>
                                    <span class="text-sm text-gray-900">{{ country.country }}</span>
                                </div>
                                <span class="text-sm font-bold text-blue-600">{{ formatNumber(country.count) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Job Categories -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Top Job Categories</h3>
                            <button
                                @click="exportReport('jobs')"
                                class="text-blue-600 hover:text-blue-800 font-semibold text-sm"
                            >
                                <ArrowDownTrayIcon class="h-4 w-4" />
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="(category, index) in topJobCategories"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <span class="text-sm text-gray-900 capitalize">{{ category.category }}</span>
                                <span class="text-sm font-bold text-purple-600">{{ formatNumber(category.job_count) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Application Status Distribution -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Status</h3>
                        <div class="space-y-3">
                            <div
                                v-for="(count, status) in applicationStatusDistribution"
                                :key="status"
                                class="flex items-center justify-between"
                            >
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-semibold capitalize',
                                        statusColors[status] || 'bg-gray-100 text-gray-800',
                                    ]"
                                >
                                    {{ status }}
                                </span>
                                <span class="text-sm font-bold text-gray-900">{{ formatNumber(count) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
