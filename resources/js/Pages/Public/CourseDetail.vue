<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Course data passed from the controller
const props = defineProps({
    course: Object,
});

// Helper function to format currency (optional)
const formatCurrency = (value) => {
    if (value === null || value === undefined) return 'N/A';
    // Basic formatting
    return `$${Number(value).toLocaleString()}`;
};

</script>

<template>
    <Head :title="course.name" />

    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                        <div class="mb-6 pb-6 border-b dark:border-gray-700">
                             <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                <Link :href="route('public.universities.show', course.university.id)" class="hover:underline text-brand-primary">
                                    {{ course.university.name }}
                                </Link>
                                - {{ course.university.city }}, {{ course.university.country.name }}
                            </p>
                            <h1 class="text-3xl font-bold mb-1">{{ course.name }}</h1>
                             <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ course.degree_level }} - {{ course.field_of_study }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">University</h3>
                                <Link :href="route('public.universities.show', course.university.id)" class="mt-1 text-lg text-brand-primary hover:underline">
                                    {{ course.university.name }}
                                </Link>
                            </div>
                             <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</h3>
                                <p class="mt-1 text-lg">{{ course.duration_years ? `${course.duration_years} years` : 'Not specified' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tuition Fee (Approx/Year)</h3>
                                <p class="mt-1 text-lg">{{ formatCurrency(course.tuition_fee) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Degree Level</h3>
                                <p class="mt-1 text-lg">{{ course.degree_level }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Field of Study</h3>
                                <p class="mt-1 text-lg">{{ course.field_of_study }}</p>
                            </div>
                             <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Application Deadline</h3>
                                <p class="mt-1 text-lg">{{ course.application_deadline || 'Not specified' }}</p>
                            </div>
                        </div>

                        <div v-if="course.description" class="mb-8">
                            <h3 class="text-xl font-semibold mb-2">Course Description</h3>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ course.description }}</p>
                        </div>


                        <div class="mt-8 pt-6 border-t dark:border-gray-700">
                            <Link :href="route('public.courses')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                                &larr; Back to Course Search
                            </Link>
                             <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                             <Link :href="route('public.universities.show', course.university.id)" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                                View University Details &rarr;
                            </Link>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>