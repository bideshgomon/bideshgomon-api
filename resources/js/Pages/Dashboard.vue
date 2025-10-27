<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // <-- Use AuthenticatedLayout
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Define props passed from the controller (if any)
const props = defineProps({
    completeness: {
        type: Number,
        default: 0,
    },
    recommendations: {
        type: Array,
        default: () => [],
    },
});

const userName = computed(() => usePage().props.auth.user.name);

</script>

<template>
    <Head title="User Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Welcome back, {{ userName }}!
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Your profile is currently <span class="font-bold text-brand-primary dark:text-blue-400">{{ completeness }}%</span> complete.
                    </p>
                    <div class="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div
                            class="bg-brand-primary h-2.5 rounded-full transition-all duration-500"
                            :style="{ width: completeness + '%' }"
                        ></div>
                    </div>
                </div>

                <div v-if="recommendations.length > 0" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                     <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Next Steps to Complete Your Profile
                    </h3>
                    <div class="space-y-3">
                        <div v-for="(rec, index) in recommendations" :key="index" class="flex items-center">
                            <svg class="h-5 w-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099a.75.75 0 00-1.07-.01l-4.25 4.5a.75.75 0 001.08 1.04l3.71-3.91v11.53a.75.75 0 001.5 0V4.719l3.71 3.91a.75.75 0 101.08-1.04l-4.25-4.5a.75.75 0 00-.99-.01z" clip-rule="evenodd" /></svg>
                            <Link :href="route(rec.route)" class="text-sm text-brand-primary dark:text-blue-400 hover:underline">
                                {{ rec.text }}
                            </Link>
                        </div>
                    </div>
                     <div class="mt-6">
                         <Link :href="route('profile.edit')" class="btn btn-secondary text-sm">
                            Go to My Profile
                        </Link>
                    </div>
                </div>

                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                     <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Quick Actions
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link :href="route('profile.edit')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Manage Profile</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Update personal info, skills, documents.</p>
                        </Link>
                        <Link :href="route('public.universities.search')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Search Universities</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Explore study options abroad.</p>
                        </Link>
                         <Link :href="route('public.courses.search')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Search Courses</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Find specific academic programs.</p>
                        </Link>
                        <Link :href="route('public.jobs.search')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Search Jobs</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Find work opportunities overseas.</p>
                        </Link>
                        <Link :href="route('guidance.dashboard')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                             <h4 class="font-semibold text-gray-900 dark:text-gray-100">AI Guidance</h4>
                             <p class="text-sm text-gray-600 dark:text-gray-400">View profile analysis & recommendations.</p>
                         </Link>
                    </div>
                 </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>