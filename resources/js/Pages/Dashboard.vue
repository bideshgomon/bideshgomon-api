<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

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

const completenessColor = () => {
    if (props.completeness < 50) return 'text-red-600';
    if (props.completeness < 100) return 'text-yellow-600';
    return 'text-green-600';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">AI Guidance Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 bg-white border-b border-gray-200">
                        
                        <div class="mb-8">
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                                Welcome, {{ $page.props.auth.user.name }}!
                            </h3>
                            <p class="text-gray-600">
                                This is your AI-powered guidance dashboard. Your first step is to complete your profile to 100%. A complete profile is essential for all visa applications, job placements, and university admissions.
                            </p>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-lg font-medium text-gray-800 mb-3">Your Profile Completeness</h4>
                            <div class="w-full bg-gray-200 rounded-full h-8">
                                <div
                                    class="bg-blue-600 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                    :style="{ width: completeness + '%' }"
                                >
                                    {{ Math.round(completeness) }}%
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-medium text-gray-800 mb-3">
                                Next Steps & Recommendations
                            </h4>
                            <div class="border border-gray-200 rounded-lg">
                                <ul class="divide-y divide-gray-200">
                                    <li v-if="recommendations.length === 0" class="p-4 flex items-center">
                                        <CheckCircleIcon class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" />
                                        <span class="text-gray-700 font-medium">
                                            Congratulations! Your profile is 100% complete.
                                        </span>
                                    </li>
                                    
                                    <li v-for="(item, index) in recommendations" :key="index" class="p-4 flex items-center justify-between hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <XCircleIcon class="h-6 w-6 text-red-500 mr-3 flex-shrink-0" />
                                            <span class="text-gray-700">{{ item.text }}</span>
                                        </div>
                                        <Link
                                            :href="route(item.route)"
                                            class="ml-4 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium hover:bg-blue-200"
                                        >
                                            Go to Profile
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>