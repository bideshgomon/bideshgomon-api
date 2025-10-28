<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// --->>> THIS IMPORT MUST BE EXACTLY LIKE THIS <<<---
import { Head, Link, usePage } from '@inertiajs/vue3';
// --->>> END IMPORT CHECK <<<---
import { computed } from 'vue';

// Define props passed from the controller
const props = defineProps({
    userProfile: Object,
    completenessChecks: Object, // Use the correct prop name for the checks object
    profileScore: Number,       // Use the correct prop name for the score number
    recommendations: Array,
});

// Accessing user name (requires usePage import)
const user = computed(() => usePage().props.auth.user); // This line needs usePage

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Welcome back, {{ user?.name || 'User' }}!
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-2">Profile Completeness</h3>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-brand-primary h-2.5 rounded-full" :style="{ width: profileScore + '%' }"></div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ profileScore }}% Complete</p>
                        </div>

                        <div v-if="recommendations && recommendations.length > 0">
                            <h3 class="text-lg font-medium mb-2">Next Steps to Improve Your Profile</h3>
                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li v-for="(rec, index) in recommendations" :key="index">
                                    <Link v-if="rec.route" :href="route(rec.route)" class="text-brand-primary hover:underline font-medium">
                                        {{ rec.text }}
                                    </Link>
                                    <span v-else>{{ rec.text }}</span>
                                </li>
                            </ul>
                        </div>
                         <div v-else class="text-sm text-green-600 dark:text-green-400">
                            Your profile looks great! No immediate recommendations.
                        </div>
                    </div>
                </div>

                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                         <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                         <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <Link :href="route('profile.edit')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Edit Profile</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Update your personal details.</p>
                            </Link>
                            <Link :href="route('public.universities.search')" class="group block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Search Universities</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Explore institutions worldwide.</p>
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
                                 <p class="text-sm text-gray-600 dark:text-gray-400">Get insights from our AI assistant.</p>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>