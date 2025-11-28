<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
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
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';

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
    if (!props.revenueChart || props.revenueChart.length === 0) return 0;
    return Math.max(...props.revenueChart.map(d => d.amount || 0), 1);
});

const maxUserValue = computed(() => {
    if (!props.userGrowthChart || props.userGrowthChart.length === 0) return 0;
    return Math.max(...props.userGrowthChart.map(d => d.count || 0), 1);
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

    <AdminLayout>
        <!-- Header with AnimatedSection -->
        <AnimatedSection variant="ocean" :blobs="true" :animated="true">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-rhythm-xl">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-rhythm-md">
                    <div>
                        <h1 class="font-display font-bold text-rhythm text-4xl sm:text-5xl">
                            <span class="text-ocean-600">Analytics</span> & Reports
                        </h1>
                        <p class="text-ocean-200 mt-rhythm-sm text-lg">Comprehensive platform insights and metrics</p>
                    </div>
                    <div class="flex items-center gap-rhythm-sm bg-white/10 backdrop-blur-sm rounded-xl px-rhythm-md py-rhythm-sm border border-white/20">
                        <CalendarIcon class="h-6 w-6 text-ocean-200" />
                        <select
                            v-model="selectedPeriod"
                            @change="changePeriod(selectedPeriod)"
                            class="px-rhythm-md py-rhythm-sm bg-white text-ocean-700 rounded-lg border-0 focus:ring-2 focus:ring-ocean-400 font-semibold"
                        >
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                            <option value="365">Last Year</option>
                        </select>
                    </div>
                </div>
            </div>
        </AnimatedSection>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-rhythm-xl space-y-rhythm-xl">

            <!-- Growth Indicators -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-rhythm-lg">
                <RhythmicCard variant="growth" size="lg">
                    <template #icon>
                        <component
                            :is="revenueGrowth >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon"
                            :class="['h-8 w-8', revenueGrowth >= 0 ? 'text-growth-600' : 'text-red-600']"
                        />
                    </template>
                    <div>
                        <h3 class="font-display font-bold text-xl text-gray-900 mb-rhythm-md">Revenue Growth</h3>
                        <div class="flex items-baseline gap-rhythm-xs">
                            <span
                                :class="[
                                    'text-display-lg font-bold',
                                    revenueGrowth >= 0 ? 'text-growth-600' : 'text-red-600',
                                ]"
                            >
                                {{ revenueGrowth > 0 ? '+' : '' }}{{ revenueGrowth }}%
                            </span>
                            <span class="text-gray-600">vs last month</span>
                        </div>
                        <div class="mt-rhythm-md pt-rhythm-md border-t border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Current Month</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(currentMonthRevenue) }}</span>
                            </div>
                            <div class="flex justify-between text-sm mt-rhythm-xs">
                                <span class="text-gray-600">Previous Month</span>
                                <span class="font-semibold text-gray-900">{{ formatCurrency(previousMonthRevenue) }}</span>
                            </div>
                        </div>
                    </div>
                </RhythmicCard>

                <RhythmicCard variant="ocean" size="lg">
                    <template #icon>
                        <component
                            :is="userGrowthRate >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon"
                            :class="['h-8 w-8', userGrowthRate >= 0 ? 'text-growth-600' : 'text-red-600']"
                        />
                    </template>
                    <div>
                        <h3 class="font-display font-bold text-xl text-gray-900 mb-rhythm-md">User Growth</h3>
                        <div class="flex items-baseline gap-rhythm-xs">
                            <span
                                :class="[
                                    'text-display-lg font-bold',
                                    userGrowthRate >= 0 ? 'text-growth-600' : 'text-red-600',
                                ]"
                            >
                                {{ userGrowthRate > 0 ? '+' : '' }}{{ userGrowthRate }}%
                            </span>
                            <span class="text-gray-600">vs last month</span>
                        </div>
                        <div class="mt-rhythm-md pt-rhythm-md border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-rhythm-md">
                                <div>
                                    <p class="text-sm text-gray-600">New Users</p>
                                    <p class="text-display-md font-bold text-gray-900">{{ formatNumber(userStats.new_users) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Active Users</p>
                                    <p class="text-display-md font-bold text-gray-900">{{ formatNumber(userStats.active_users) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </RhythmicCard>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-rhythm-lg">
                <RhythmicCard variant="ocean" size="md" title="Total Users" :description="`${formatNumber(userStats.verified_users)} verified`">
                    <template #icon>
                        <UsersIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">{{ formatNumber(userStats.total_users) }}</div>
                </RhythmicCard>

                <RhythmicCard variant="growth" size="md" title="Total Revenue" :description="`${formatNumber(revenueStats.total_transactions)} transactions`">
                    <template #icon>
                        <CurrencyDollarIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">{{ formatCurrency(revenueStats.total_revenue) }}</div>
                </RhythmicCard>

                <RhythmicCard variant="heritage" size="md" title="Job Postings" :description="`${formatNumber(jobStats.active_postings)} active`">
                    <template #icon>
                        <BriefcaseIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">{{ formatNumber(jobStats.total_postings) }}</div>
                </RhythmicCard>

                <RhythmicCard variant="sky" size="md" title="Applications" :description="`${jobStats.application_rate} per job`">
                    <template #icon>
                        <ChartBarIcon class="h-8 w-8" />
                    </template>
                    <div class="text-display-md font-bold text-gray-900">{{ formatNumber(jobStats.total_applications) }}</div>
                </RhythmicCard>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-rhythm-lg">
                <!-- Revenue Chart -->
                <RhythmicCard variant="light" size="lg">
                    <div class="flex items-center justify-between mb-rhythm-md">
                        <h3 class="font-display font-bold text-xl text-gray-900">Revenue Trend (Last 30 Days)</h3>
                        <button
                            @click="exportReport('revenue')"
                            class="text-growth-600 hover:text-growth-800 font-semibold text-sm flex items-center gap-rhythm-xs"
                        >
                            <ArrowDownTrayIcon class="h-4 w-4" />
                            Export
                        </button>
                    </div>
                    <div v-if="revenueChart && revenueChart.length > 0" class="relative">
                        <div class="overflow-x-auto">
                            <div class="h-64 flex items-end gap-1 border-l border-b border-gray-200 pl-2 pb-12" :style="{ minWidth: revenueChart.length * 32 + 'px' }">
                                <div
                                    v-for="(data, index) in revenueChart"
                                    :key="index"
                                    class="flex flex-col items-center group relative"
                                    style="min-width: 28px; flex: 0 0 auto;"
                                >
                                    <div
                                        :style="{
                                            height: maxRevenueValue > 0 ? `${Math.max((data.amount / maxRevenueValue) * 100, 8)}%` : '8%',
                                        }"
                                        class="w-full bg-green-600 hover:bg-green-700 rounded-t transition-all relative shadow-sm cursor-pointer"
                                    >
                                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 shadow-lg">
                                            {{ formatCurrency(data.amount) }}
                                        </div>
                                    </div>
                                    <span class="absolute top-full mt-1 text-[10px] text-gray-600 whitespace-nowrap origin-top-left transform rotate-45" style="left: 50%; transform: rotate(45deg) translateX(-30%);" :title="data.date">{{ data.date }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div v-else class="h-64 flex items-center justify-center border border-dashed border-gray-300 rounded-lg bg-gray-50">
                            <div class="text-center">
                                <ChartBarIcon class="h-12 w-12 text-gray-400 mx-auto mb-2" />
                                <p class="text-gray-500 text-sm">No revenue data available for this period</p>
                            </div>
                        </div>
                </RhythmicCard>

                <!-- User Growth Chart -->
                <RhythmicCard variant="light" size="lg">
                    <div class="flex items-center justify-between mb-rhythm-md">
                        <h3 class="font-display font-bold text-xl text-gray-900">User Registrations (Last 30 Days)</h3>
                        <button
                            @click="exportReport('users')"
                            class="text-ocean-600 hover:text-ocean-800 font-semibold text-sm flex items-center gap-rhythm-xs"
                        >
                            <ArrowDownTrayIcon class="h-4 w-4" />
                            Export
                        </button>
                    </div>
                    <div v-if="userGrowthChart && userGrowthChart.length > 0" class="relative">
                        <div class="overflow-x-auto">
                            <div class="h-64 flex items-end gap-1 border-l border-b border-gray-200 pl-2 pb-12" :style="{ minWidth: userGrowthChart.length * 32 + 'px' }">
                                <div
                                    v-for="(data, index) in userGrowthChart"
                                    :key="index"
                                    class="flex flex-col items-center group relative"
                                    style="min-width: 28px; flex: 0 0 auto;"
                                >
                                    <div
                                        :style="{
                                            height: maxUserValue > 0 ? `${Math.max((data.count / maxUserValue) * 100, 8)}%` : '8%',
                                        }"
                                        class="w-full bg-blue-600 hover:bg-blue-700 rounded-t transition-all relative shadow-sm cursor-pointer"
                                    >
                                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 shadow-lg">
                                            {{ data.count }} users
                                        </div>
                                    </div>
                                    <span class="absolute top-full mt-1 text-[10px] text-gray-600 whitespace-nowrap origin-top-left transform rotate-45" style="left: 50%; transform: rotate(45deg) translateX(-30%);" :title="data.date">{{ data.date }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="h-64 flex items-center justify-center border border-dashed border-gray-300 rounded-lg bg-gray-50">
                        <div class="text-center">
                            <UsersIcon class="h-12 w-12 text-gray-400 mx-auto mb-2" />
                            <p class="text-gray-500 text-sm">No user registration data available for this period</p>
                        </div>
                    </div>
                </RhythmicCard>
            </div>

            <!-- Additional Insights -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-rhythm-lg">
                <!-- Top Countries -->
                <RhythmicCard variant="light" size="md">
                    <h3 class="font-display font-bold text-lg text-gray-900 mb-rhythm-md">Top Countries</h3>
                    <div class="space-y-rhythm-sm">
                            <div
                                v-for="(country, index) in topCountries"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-rhythm-xs">
                                    <span class="text-sm font-semibold text-gray-600">{{ index + 1 }}.</span>
                                    <span class="text-sm text-gray-900">{{ country.country }}</span>
                                </div>
                                <span class="text-sm font-bold text-ocean-600">{{ formatNumber(country.count) }}</span>
                            </div>
                        </div>
                </RhythmicCard>

                <!-- Top Job Categories -->
                <RhythmicCard variant="light" size="md">
                    <div class="flex items-center justify-between mb-rhythm-md">
                        <h3 class="font-display font-bold text-lg text-gray-900">Top Job Categories</h3>
                        <button
                            @click="exportReport('jobs')"
                            class="text-heritage-600 hover:text-heritage-800 font-semibold text-sm"
                        >
                            <ArrowDownTrayIcon class="h-4 w-4" />
                        </button>
                    </div>
                    <div class="space-y-rhythm-sm">
                            <div
                                v-for="(category, index) in topJobCategories"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <span class="text-sm text-gray-900 capitalize">{{ category.category }}</span>
                                <span class="text-sm font-bold text-heritage-600">{{ formatNumber(category.job_count) }}</span>
                            </div>
                        </div>
                </RhythmicCard>

                <!-- Application Status Distribution -->
                <RhythmicCard variant="light" size="md">
                    <h3 class="font-display font-bold text-lg text-gray-900 mb-rhythm-md">Application Status</h3>
                    <div class="space-y-rhythm-sm">
                            <div
                                v-for="(count, status) in applicationStatusDistribution"
                                :key="status"
                                class="flex items-center justify-between"
                            >
                                <span
                                    :class="[
                                        'px-rhythm-xs py-1 rounded-full text-xs font-semibold capitalize',
                                        statusColors[status] || 'bg-gray-100 text-gray-800',
                                    ]"
                                >
                                    {{ status }}
                                </span>
                                <span class="text-sm font-bold text-gray-900">{{ formatNumber(count) }}</span>
                            </div>
                        </div>
                </RhythmicCard>
            </div>
        </div>
    </AdminLayout>
</template>
