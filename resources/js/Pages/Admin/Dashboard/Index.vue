<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // <-- Use the NEW layout
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    UsersIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    GlobeAltIcon,
    Cog6ToothIcon,
    UserGroupIcon,
    DocumentMagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';
import { ChartBarIcon, ClockIcon } from '@heroicons/vue/24/solid';

// Define the 'stats' prop
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ // Example stats - adjust keys as needed from your controller
            totalUsers: 0,
            totalAgencies: 0,
            totalConsultants: 0,
            pendingApplications: 0,
            totalUniversities: 0,
            totalCourses: 0,
            totalJobs: 0,
            totalCountries: 0,
        }),
    },
});

// Helper to format numbers
const formatNumber = (value) => {
    return value != null ? value.toLocaleString() : '0';
};

// Computed property for the stats grid (Top KPIs)
const statCards = computed(() => [
    { title: 'Total Users', value: formatNumber(props.stats?.totalUsers), icon: UsersIcon, color: 'bg-gradient-to-br from-blue-400 to-blue-600', href: route('admin.users.index') },
    { title: 'Countries', value: formatNumber(props.stats?.totalCountries), icon: GlobeAltIcon, color: 'bg-gradient-to-br from-orange-400 to-orange-600', href: route('admin.countries.index') },
    { title: 'Universities', value: formatNumber(props.stats?.totalUniversities), icon: AcademicCapIcon, color: 'bg-gradient-to-br from-purple-400 to-purple-600', href: route('admin.universities.index') },
    { title: 'Job Postings', value: formatNumber(props.stats?.totalJobs), icon: BriefcaseIcon, color: 'bg-gradient-to-br from-pink-400 to-pink-600', href: route('admin.job-postings.index') },
]);

// Example Recent Activity Data (replace with real data later)
const recentActivity = ref([
    { id: 1, user: 'Admin User', action: 'added 15 countries via CSV', time: '2h ago', icon: GlobeAltIcon },
    { id: 2, user: 'System', action: 'updated site settings', time: 'Yesterday', icon: Cog6ToothIcon },
    { id: 3, user: 'Admin User', action: 'created new user: Test Agency', time: '3 days ago', icon: UsersIcon },
]);

</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Admin Dashboard
            </h2>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                    v-for="(stat, index) in statCards"
                    :key="`stat-${index}`"
                    :href="stat.href"
                    class="block p-6 rounded-xl shadow-lg text-white transition-all transform hover:-translate-y-1 hover:shadow-xl"
                    :class="stat.color"
                >
                    <div class="flex items-center justify-between">
                         <div class="text-sm font-medium uppercase tracking-wider">{{ stat.title }}</div>
                         <component :is="stat.icon" class="h-7 w-7 opacity-80" aria-hidden="true" />
                    </div>
                    <div class="mt-2 text-4xl font-semibold">{{ stat.value }}</div>
                    <div class="mt-1 text-xs opacity-80 font-medium">Click to manage &rarr;</div>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                         <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <ClockIcon class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500" />
                            Recent Activity
                         </h3>
                    </div>
                    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                        <li v-for="activity in recentActivity" :key="activity.id" class="px-4 py-4 sm:px-6 flex items-center space-x-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center ring-1 ring-gray-200 dark:ring-gray-600">
                                <component :is="activity.icon" class="h-5 w-5 text-gray-500 dark:text-gray-400" aria-hidden="true" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-800 dark:text-gray-200 truncate">
                                    <span class="font-medium">{{ activity.user }}</span> {{ activity.action }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 self-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity.time }}</p>
                            </div>
                        </li>
                         <li v-if="!recentActivity.length" class="px-4 py-10 sm:px-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            No recent activity recorded.
                         </li>
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                         <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                           Quick Links
                         </h3>
                    </div>
                    <nav class="px-4 py-4 space-y-1">
                        <Link :href="route('admin.users.index')" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                            <UsersIcon class="text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" aria-hidden="true" />
                            Manage Users
                        </Link>
                         <Link :href="route('admin.countries.index')" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                            <GlobeAltIcon class="text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" aria-hidden="true" />
                            Manage Countries
                        </Link>
                         <Link :href="route('admin.job-postings.index')" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                            <BriefcaseIcon class="text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" aria-hidden="true" />
                            Manage Job Postings
                        </Link>
                         <Link :href="route('admin.settings.index')" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                            <Cog6ToothIcon class="text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" aria-hidden="true" />
                            Site Settings
                        </Link>
                    </nav>
                 </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>