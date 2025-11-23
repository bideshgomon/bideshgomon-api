<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BriefcaseIcon, SparklesIcon, GlobeAltIcon, 
    ShieldCheckIcon, DocumentTextIcon, BuildingOffice2Icon,
    PaperAirplaneIcon, ClipboardDocumentListIcon,
    ChartBarIcon, UserGroupIcon, EyeIcon, CurrencyDollarIcon,
    CheckCircleIcon, XCircleIcon, ClockIcon, StarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jobApplicationStats: Object,
    profileAssessmentStats: Object,
    publicProfileStats: Object,
    insuranceStats: Object,
    cvStats: Object,
    hotelStats: Object,
    flightStats: Object,
    visaStats: Object,
    recentJobApplications: Array,
    recentAssessments: Array,
    recentPublicProfiles: Array,
    serviceChartData: Object,
});

const formatCurrency = (amount) => {
    return `৳${parseFloat(amount).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        shortlisted: 'bg-blue-100 text-blue-800',
        approved: 'bg-green-100 text-green-800',
        confirmed: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getRiskColor = (risk) => {
    const colors = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-red-100 text-red-800',
    };
    return colors[risk] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Service Management Dashboard" />

    <AdminLayout>
        <!-- Header -->
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Service Management Dashboard</h1>
                        <p class="text-sm text-purple-100 mt-2">Comprehensive overview of all platform services</p>
                    </div>
                    <ChartBarIcon class="h-16 w-16 text-purple-200 opacity-50" />
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
            
            <!-- New Services Grid -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <StarIcon class="h-6 w-6 text-indigo-600 mr-2" />
                    New Platform Services
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Job Applications Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <BriefcaseIcon class="h-8 w-8 text-blue-600" />
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                +{{ jobApplicationStats.today }} today
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Job Applications</h3>
                        <p class="text-3xl font-extrabold text-blue-600 mb-4">{{ jobApplicationStats.total }}</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pending</span>
                                <span class="font-semibold text-yellow-600">{{ jobApplicationStats.pending }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shortlisted</span>
                                <span class="font-semibold text-blue-600">{{ jobApplicationStats.shortlisted }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Approved</span>
                                <span class="font-semibold text-green-600">{{ jobApplicationStats.approved }}</span>
                            </div>
                        </div>
                        
                        <Link
                            :href="route('admin.job-applications.index')"
                            class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            Manage Applications
                        </Link>
                    </div>

                    <!-- Profile Assessments Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <SparklesIcon class="h-8 w-8 text-purple-600" />
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                +{{ profileAssessmentStats.today }} today
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">AI Assessments</h3>
                        <p class="text-3xl font-extrabold text-purple-600 mb-1">{{ profileAssessmentStats.total }}</p>
                        <p class="text-sm text-gray-500 mb-4">Avg Score: {{ profileAssessmentStats.average_score }}/100</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">High Risk</span>
                                <span class="font-semibold text-red-600">{{ profileAssessmentStats.high_risk }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Medium Risk</span>
                                <span class="font-semibold text-yellow-600">{{ profileAssessmentStats.medium_risk }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Low Risk</span>
                                <span class="font-semibold text-green-600">{{ profileAssessmentStats.low_risk }}</span>
                            </div>
                        </div>
                        
                        <Link
                            :href="route('admin.users.index')"
                            class="block w-full text-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors font-medium"
                        >
                            View Assessments
                        </Link>
                    </div>

                    <!-- Public Profiles Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-emerald-100 rounded-lg">
                                <GlobeAltIcon class="h-8 w-8 text-emerald-600" />
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                +{{ publicProfileStats.views_today }} views today
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Public Profiles</h3>
                        <p class="text-3xl font-extrabold text-emerald-600 mb-1">{{ publicProfileStats.total_public }}</p>
                        <p class="text-sm text-gray-500 mb-4">{{ publicProfileStats.total_views }} total views</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Public Profiles</span>
                                <span class="font-semibold text-emerald-600">{{ publicProfileStats.total_public }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Private Profiles</span>
                                <span class="font-semibold text-gray-600">{{ publicProfileStats.total_private }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Avg Views/Profile</span>
                                <span class="font-semibold text-blue-600">{{ publicProfileStats.average_views_per_profile }}</span>
                            </div>
                        </div>
                        
                        <Link
                            :href="route('admin.users.index')"
                            class="block w-full text-center bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium"
                        >
                            Manage Profiles
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Existing Services Grid -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <ChartBarIcon class="h-6 w-6 text-gray-600 mr-2" />
                    Core Services Overview
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Travel Insurance -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="p-2 bg-emerald-100 rounded-lg">
                                <ShieldCheckIcon class="h-6 w-6 text-emerald-600" />
                            </div>
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                                +{{ insuranceStats.today }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Insurance</p>
                        <p class="text-2xl font-bold text-gray-900">{{ insuranceStats.total }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ formatCurrency(insuranceStats.revenue_month) }} this month</p>
                    </div>

                    <!-- CV Builder -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <DocumentTextIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                                +{{ cvStats.today }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">CVs Created</p>
                        <p class="text-2xl font-bold text-gray-900">{{ cvStats.total }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ cvStats.this_week }} this week</p>
                    </div>

                    <!-- Hotel Bookings -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="p-2 bg-orange-100 rounded-lg">
                                <BuildingOffice2Icon class="h-6 w-6 text-orange-600" />
                            </div>
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                                +{{ hotelStats.today }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Hotel Bookings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ hotelStats.total }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ formatCurrency(hotelStats.revenue_month) }} revenue</p>
                    </div>

                    <!-- Flight Requests -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="p-2 bg-sky-100 rounded-lg">
                                <PaperAirplaneIcon class="h-6 w-6 text-sky-600" />
                            </div>
                            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                                +{{ flightStats.today }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Flight Requests</p>
                        <p class="text-2xl font-bold text-gray-900">{{ flightStats.total }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ flightStats.pending }} pending</p>
                    </div>

                    <!-- Visa Applications -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 md:col-span-2 lg:col-span-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-lg">
                                    <ClipboardDocumentListIcon class="h-8 w-8 text-purple-600" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Visa Applications</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ visaStats.total }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Pending</p>
                                    <p class="text-2xl font-bold text-yellow-600">{{ visaStats.pending }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Approved</p>
                                    <p class="text-2xl font-bold text-green-600">{{ visaStats.approved }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Revenue (Month)</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ formatCurrency(visaStats.revenue_month) }}</p>
                                </div>
                                <Link
                                    :href="route('admin.visa-applications.index')"
                                    class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors"
                                >
                                    Manage Visas
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Recent Job Applications -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <BriefcaseIcon class="h-5 w-5 text-blue-600 mr-2" />
                        Recent Job Applications
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="application in recentJobApplications"
                            :key="application.id"
                            class="border-l-4 border-blue-500 pl-3 py-2"
                        >
                            <p class="font-semibold text-gray-900 text-sm">{{ application.user.name }}</p>
                            <p class="text-xs text-gray-600">{{ application.job.title }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span :class="getStatusColor(application.status)" class="text-xs font-medium px-2 py-0.5 rounded">
                                    {{ application.status }}
                                </span>
                                <span class="text-xs text-gray-500">{{ formatDate(application.created_at) }}</span>
                            </div>
                        </div>
                        <div v-if="recentJobApplications.length === 0" class="text-center py-4 text-gray-500 text-sm">
                            No recent applications
                        </div>
                    </div>
                </div>

                <!-- Recent Assessments -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <SparklesIcon class="h-5 w-5 text-purple-600 mr-2" />
                        Recent AI Assessments
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="assessment in recentAssessments"
                            :key="assessment.id"
                            class="border-l-4 border-purple-500 pl-3 py-2"
                        >
                            <p class="font-semibold text-gray-900 text-sm">{{ assessment.user.name }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-purple-600">{{ assessment.overall_score }}</span>
                                    <span :class="getRiskColor(assessment.risk_level)" class="text-xs font-medium px-2 py-0.5 rounded">
                                        {{ assessment.risk_level }} risk
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">{{ formatDate(assessment.assessed_at) }}</span>
                            </div>
                        </div>
                        <div v-if="recentAssessments.length === 0" class="text-center py-4 text-gray-500 text-sm">
                            No recent assessments
                        </div>
                    </div>
                </div>

                <!-- Top Public Profiles -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <GlobeAltIcon class="h-5 w-5 text-emerald-600 mr-2" />
                        Top Viewed Profiles
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="(profile, index) in publicProfileStats.top_viewed_profiles"
                            :key="index"
                            class="border-l-4 border-emerald-500 pl-3 py-2"
                        >
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-gray-900 text-sm">{{ profile.name }}</p>
                                <div class="flex items-center text-emerald-600">
                                    <EyeIcon class="h-4 w-4 mr-1" />
                                    <span class="text-sm font-bold">{{ profile.views }}</span>
                                </div>
                            </div>
                            <a
                                :href="`/profile/${profile.slug}`"
                                target="_blank"
                                class="text-xs text-blue-600 hover:underline"
                            >
                                View Profile →
                            </a>
                        </div>
                        <div v-if="publicProfileStats.top_viewed_profiles.length === 0" class="text-center py-4 text-gray-500 text-sm">
                            No public profiles yet
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <Link
                        :href="route('admin.job-applications.index', { filter: 'pending' })"
                        class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group"
                    >
                        <ClockIcon class="h-8 w-8 text-yellow-600 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="text-sm font-medium text-gray-900">Pending Jobs</span>
                        <span class="text-xs text-gray-600">{{ jobApplicationStats.pending }} waiting</span>
                    </Link>

                    <Link
                        :href="route('admin.users.index', { filter: 'high_risk' })"
                        class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group"
                    >
                        <XCircleIcon class="h-8 w-8 text-red-600 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="text-sm font-medium text-gray-900">High Risk</span>
                        <span class="text-xs text-gray-600">{{ profileAssessmentStats.high_risk }} profiles</span>
                    </Link>

                    <Link
                        :href="route('admin.users.index', { filter: 'public_profiles' })"
                        class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group"
                    >
                        <GlobeAltIcon class="h-8 w-8 text-emerald-600 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="text-sm font-medium text-gray-900">Public Profiles</span>
                        <span class="text-xs text-gray-600">{{ publicProfileStats.total_public }} active</span>
                    </Link>

                    <Link
                        :href="route('admin.visa-applications.index', { filter: 'pending' })"
                        class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition-all group"
                    >
                        <ClipboardDocumentListIcon class="h-8 w-8 text-purple-600 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="text-sm font-medium text-gray-900">Pending Visas</span>
                        <span class="text-xs text-gray-600">{{ visaStats.pending }} waiting</span>
                    </Link>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
