<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ClockIcon, 
    DocumentTextIcon, 
    CheckCircleIcon, 
    BriefcaseIcon,
    CurrencyDollarIcon,
    ArrowRightIcon,
    GlobeAltIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    availableApplications: Array,
    myApplications: Array,
    assignedCountries: Array,
});

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'assigned': 'bg-blue-100 text-blue-800',
        'in_progress': 'bg-indigo-100 text-indigo-800',
        'accepted': 'bg-green-100 text-green-800',
        'completed': 'bg-emerald-100 text-emerald-800',
        'rejected': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Agency Dashboard" />

    <AuthenticatedLayout>
        <div class="py-4 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Agency Dashboard</h1>
                            <p class="mt-1 text-xs sm:text-sm text-gray-600">Manage service applications, submit quotes, and track your earnings</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <Link
                                :href="route('agency.country-assignments.index')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <GlobeAltIcon class="w-5 h-5 mr-2" />
                                My Countries
                            </Link>
                            <Link
                                :href="route('agency.applications.index')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                <DocumentTextIcon class="w-5 h-5 mr-2" />
                                All Applications
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <!-- My Pending Applications -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="bg-yellow-100 p-2 sm:p-3 rounded-lg">
                                <ClockIcon class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" />
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">My Pending</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ stats.my_pending }}</p>
                        <p class="text-xs text-gray-500 mt-1 sm:mt-2">Awaiting action</p>
                    </div>

                    <!-- My Active Applications -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="bg-blue-100 p-2 sm:p-3 rounded-lg">
                                <DocumentTextIcon class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" />
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Active</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ stats.my_active }}</p>
                        <p class="text-xs text-gray-500 mt-1 sm:mt-2">In progress</p>
                    </div>

                    <!-- Completed -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="bg-green-100 p-2 sm:p-3 rounded-lg">
                                <CheckCircleIcon class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" />
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Completed</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ stats.my_completed }}</p>
                        <p class="text-xs text-gray-500 mt-1 sm:mt-2">Successfully finished</p>
                    </div>

                    <!-- Total Earnings -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="bg-emerald-100 p-2 sm:p-3 rounded-lg">
                                <CurrencyDollarIcon class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" />
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Earnings</p>
                        <p class="text-xl sm:text-3xl font-bold text-gray-900">৳{{ Number(stats.total_earnings || 0).toLocaleString() }}</p>
                        <p class="text-xs text-gray-500 mt-1 sm:mt-2">All time revenue</p>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Available Applications</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.available_applications }}</p>
                                <p class="text-xs text-gray-500 mt-1">New applications you can quote for</p>
                            </div>
                            <div class="bg-purple-100 p-2 sm:p-3 rounded-lg">
                                <BriefcaseIcon class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" />
                            </div>
                        </div>
                        <Link
                            :href="route('agency.applications.index', { filter: 'available' })"
                            class="inline-flex items-center text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-700 mt-3 sm:mt-4"
                        >
                            Browse available applications
                            <ArrowRightIcon class="w-3 h-3 sm:w-4 sm:h-4 ml-1" />
                        </Link>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Pending Quotes</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_quotes }}</p>
                                <p class="text-xs text-gray-500 mt-1">Quotes awaiting customer response</p>
                            </div>
                            <div class="bg-orange-100 p-2 sm:p-3 rounded-lg">
                                <ChatBubbleLeftRightIcon class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" />
                            </div>
                        </div>
                        <Link
                            :href="route('agency.applications.index', { filter: 'quoted' })"
                            class="inline-flex items-center text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-700 mt-3 sm:mt-4"
                        >
                            View pending quotes
                            <ArrowRightIcon class="w-3 h-3 sm:w-4 sm:h-4 ml-1" />
                        </Link>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Available Applications -->
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Available Applications</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">New applications you can submit quotes for</p>
                                </div>
                                <span class="px-2 sm:px-3 py-1 bg-purple-100 text-purple-700 text-xs sm:text-sm font-medium rounded-full">
                                    {{ availableApplications.length }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div v-if="availableApplications.length > 0" class="space-y-3">
                                <div v-for="app in availableApplications" :key="app.id" class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors">
                                    <div class="flex justify-between items-start mb-2 sm:mb-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ app.application_number }}</p>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ app.service_module?.name }}</p>
                                        </div>
                                        <span :class="getStatusColor(app.status)" class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap ml-2">
                                            {{ app.status }}
                                        </span>
                                    </div>
                                    <p v-if="app.tourist_visa?.destination_country" class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3 flex items-center">
                                        <GlobeAltIcon class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" />
                                        <span class="truncate">{{ app.tourist_visa.destination_country.name }}</span>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">
                                            {{ new Date(app.created_at).toLocaleDateString('en-GB') }}
                                        </span>
                                        <Link
                                            :href="route('agency.applications.show', app.id)"
                                            class="inline-flex items-center text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-700"
                                        >
                                            <span class="hidden sm:inline">View & Quote</span>
                                            <span class="sm:hidden">Quote</span>
                                            <ArrowRightIcon class="w-3 h-3 sm:w-4 sm:h-4 ml-1" />
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 sm:py-12">
                                <BriefcaseIcon class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" />
                                <h3 class="mt-2 text-xs sm:text-sm font-medium text-gray-900">No available applications</h3>
                                <p class="mt-1 text-xs sm:text-sm text-gray-500">Check back later for new opportunities</p>
                            </div>
                        </div>
                    </div>

                    <!-- My Applications -->
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">My Applications</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Applications assigned to you</p>
                                </div>
                                <span class="px-2 sm:px-3 py-1 bg-blue-100 text-blue-700 text-xs sm:text-sm font-medium rounded-full">
                                    {{ myApplications.length }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div v-if="myApplications.length > 0" class="space-y-3">
                                <div v-for="app in myApplications" :key="app.id" class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-blue-300 hover:bg-blue-50/50 transition-colors">
                                    <div class="flex justify-between items-start mb-2 sm:mb-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ app.application_number }}</p>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ app.service_module?.name }}</p>
                                        </div>
                                        <span :class="getStatusColor(app.status)" class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap ml-2">
                                            {{ app.status }}
                                        </span>
                                    </div>
                                    <p v-if="app.tourist_visa?.destination_country" class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3 flex items-center">
                                        <GlobeAltIcon class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" />
                                        <span class="truncate">{{ app.tourist_visa.destination_country.name }}</span>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">
                                            {{ new Date(app.created_at).toLocaleDateString('en-GB') }}
                                        </span>
                                        <Link
                                            :href="route('agency.applications.show', app.id)"
                                            class="inline-flex items-center text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-700"
                                        >
                                            <span class="hidden sm:inline">View Details</span>
                                            <span class="sm:hidden">View</span>
                                            <ArrowRightIcon class="w-3 h-3 sm:w-4 sm:h-4 ml-1" />
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 sm:py-12">
                                <DocumentTextIcon class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" />
                                <h3 class="mt-2 text-xs sm:text-sm font-medium text-gray-900">No applications assigned yet</h3>
                                <p class="mt-1 text-xs sm:text-sm text-gray-500">Submit quotes to available applications to get started</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
