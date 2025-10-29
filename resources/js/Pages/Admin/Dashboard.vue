<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { UsersIcon, BriefcaseIcon, BuildingLibraryIcon, AcademicCapIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    recentUsers: Array,
});

const statItems = [
    { name: 'Total Users', value: props.stats.total_users, icon: UsersIcon, color: 'text-blue-500' },
    { name: 'Job Postings', value: props.stats.total_jobs, icon: BriefcaseIcon, color: 'text-green-500' },
    { name: 'Universities', value: props.stats.total_universities, icon: BuildingLibraryIcon, color: 'text-indigo-500' },
    { name: 'Courses', value: props.stats.total_courses, icon: AcademicCapIcon, color: 'text-purple-500' },
];

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Dashboard
            </h2>
        </template>
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="item in statItems" :key="item.name" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex items-center space-x-4">
                            <div class="flex-shrink-0 p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                                <component :is="item.icon" class="w-8 h-8" :class="item.color" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ item.name }}
                                </p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ item.value }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Recent User Signups
                        </h3>
                        
                        <div class="mt-4 flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Name</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Email</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="user in recentUsers" :key="user.id">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ user.name }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(user.created_at) }}</td>
                                            </tr>
                                            <tr v-if="recentUsers.length === 0">
                                                <td colspan="3" class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    No recent users found.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>