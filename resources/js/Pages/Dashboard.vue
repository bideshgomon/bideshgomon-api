<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircleIcon, ArrowRightIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    completeness: Number,
    recommendations: Array,
});

const completenessColorClass = (percentage) => {
    if (percentage < 40) return 'bg-red-500';
    if (percentage < 80) return 'bg-yellow-500';
    return 'bg-green-500';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                User Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Welcome back, <span class="font-semibold">{{ $page.props.auth.user.name }}</span>!
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Your Profile Completeness
                        </h3>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-2">
                            <div 
                                class="h-4 rounded-full transition-all duration-500 ease-out"
                                :class="completenessColorClass(completeness)"
                                :style="{ width: completeness + '%' }"
                            ></div>
                        </div>
                        <p class="text-right text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ completeness }}%
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Recommendations
                        </h3>
                        
                        <div v-if="recommendations.length > 0" class="space-y-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Complete these steps to improve your profile visibility and chances of success.
                            </p>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li v-for="(rec, index) in recommendations" :key="index" class="py-3">
                                    <Link :href="route(rec.route)" class="flex items-center justify-between group">
                                        <span class="text-md text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            {{ rec.text }}
                                        </span>
                                        <ArrowRightIcon class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:translate-x-1 transition-transform" />
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <div v-else class="flex flex-col items-center text-center p-6">
                            <CheckCircleIcon class="w-16 h-16 text-green-500" />
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-4">
                                Profile Complete!
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                Your profile is fully complete. Well done!
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>