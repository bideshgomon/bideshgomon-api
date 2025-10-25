<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// University data passed from the controller
const props = defineProps({
    university: Object,
});

// Computed property for displaying intake months nicely
const intakeString = computed(() => {
    if (!props.university.intake_months || props.university.intake_months.length === 0) {
        return 'Not specified';
    }
    return props.university.intake_months.join(', ');
});

// Computed property for logo URL
const logoUrl = computed(() => {
    return props.university.logo_path ? `/storage/${props.university.logo_path}` : null;
});

</script>

<template>
    <Head :title="university.name" />

    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                        <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-6 pb-6 border-b dark:border-gray-700">
                            <div class="flex-shrink-0">
                                <img v-if="logoUrl" :src="logoUrl" :alt="university.name + ' Logo'" class="h-24 w-24 object-contain rounded-lg border dark:border-gray-700 p-1 bg-white">
                                <div v-else class="h-24 w-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 text-4xl">?</div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold mb-1">{{ university.name }}</h1>
                                <p class="text-lg text-gray-600 dark:text-gray-400">
                                    {{ university.city }}, {{ university.country.name }}
                                </p>
                                <a v-if="university.website_url" :href="university.website_url" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-brand-primary hover:underline">
                                    Visit Website <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</h3>
                                <p class="mt-1 text-lg">{{ university.city }}, {{ university.country.name }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ranking</h3>
                                <p class="mt-1 text-lg">{{ university.ranking || 'Not specified' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Intake Months</h3>
                                <p class="mt-1 text-lg">{{ intakeString }}</p>
                            </div>
                        </div>

                        <div v-if="university.description" class="mb-8">
                            <h3 class="text-xl font-semibold mb-2">About {{ university.name }}</h3>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ university.description }}</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold mb-4">Courses Offered</h3>
                            <div v-if="university.courses && university.courses.length > 0" class="space-y-3">
                                <div v-for="course in university.courses" :key="course.id" class="p-4 border dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    <Link :href="route('public.courses.show', course.id)" class="block">
                                        <h4 class="font-medium text-brand-primary dark:text-blue-400">{{ course.name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ course.degree_level }} - {{ course.field_of_study }}
                                            <span v-if="course.duration_years"> ({{ course.duration_years }} years)</span>
                                        </p>
                                    </Link>
                                </div>
                            </div>
                            <div v-else class="text-gray-500 dark:text-gray-400">
                                No specific courses listed for this university yet.
                            </div>
                        </div>

                         <div class="mt-8 pt-6 border-t dark:border-gray-700">
                            <Link :href="route('public.universities')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                                &larr; Back to University Search
                            </Link>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>