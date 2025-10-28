<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    UsersIcon, BuildingOffice2Icon, AcademicCapIcon, BriefcaseIcon, GlobeAltIcon, MapIcon, MapPinIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Array, // Array of stat objects { name: String, count: Number, href: String }
});

// Map stat names to Heroicons components
const iconMap = {
    'Total Users': UsersIcon,
    'Total Universities': BuildingOffice2Icon,
    'Total Courses': AcademicCapIcon,
    'Total Job Postings': BriefcaseIcon,
    'Total Countries': GlobeAltIcon,
    'Total States': MapIcon,
    'Total Cities': MapPinIcon,
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div>
            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-6">Overview</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <Link
                    v-for="item in stats"
                    :key="item.name"
                    :href="item.href"
                    class="group relative flex items-start space-x-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-150"
                >
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center rounded-lg bg-brand-primary p-3">
                            <component
                                :is="iconMap[item.name]"
                                class="h-6 w-6 text-white"
                                aria-hidden="true" />
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ item.name }}</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ item.count }}</p>
                    </div>
                </Link>
            </div>
            </div>
    </AdminLayout>
</template>