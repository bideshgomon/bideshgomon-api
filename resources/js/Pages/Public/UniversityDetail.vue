<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'; // PATCH: Changed layout
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { MapPinIcon, GlobeAltIcon, CalendarDaysIcon, StarIcon } from '@heroicons/vue/24/outline'; // Icons for details

// University data passed from the controller
const props = defineProps({
    university: Object,
});

// Computed property for displaying intake months nicely
const intakeString = computed(() => {
    if (!props.university.intake_months || props.university.intake_months.length === 0) {
        return 'Not specified';
    }
    return props.university.intake_months.join(', '); //
});

// Computed property for logo URL
const logoUrl = computed(() => {
    return props.university.logo_path ? `/storage/${props.university.logo_path}` : null; //
});

</script>

<template>
    <Head :title="university.name" />

    <PublicLayout>
        <div class="container py-8 md:py-12">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-6 pb-6 border-b dark:border-gray-700">
                        <div class="flex-shrink-0">
                            <img
                                v-if="logoUrl"
                                :src="logoUrl"
                                :alt="`${university.name} Logo`"
                                class="h-24 w-24 rounded-lg object-contain border dark:border-gray-700 p-1"
                            />
                            <div
                                v-else
                                class="h-24 w-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 text-4xl font-bold"
                            >
                                {{ university.name.charAt(0) }}
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                                {{ university.name }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400">
                                <MapPinIcon class="inline-block h-5 w-5 mr-1 align-text-bottom"/>
                                {{ university.city }}, {{ university.country?.name }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-8">
                        <div v-if="university.website_url">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <GlobeAltIcon class="h-4 w-4 mr-1.5"/> Website
                            </h3>
                            <a :href="university.website_url" target="_blank" rel="noopener noreferrer" class="mt-1 text-brand-primary hover:underline break-words">
                                {{ university.website_url }}
                            </a>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <CalendarDaysIcon class="h-4 w-4 mr-1.5"/> Intake Months
                            </h3>
                            <p class="mt-1">{{ intakeString }}</p>
                        </div>
                        <div v-if="university.ranking">
                             <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <StarIcon class="h-4 w-4 mr-1.5"/> Ranking
                            </h3>
                            <p class="mt-1">{{ university.ranking }}</p>
                        </div>
                        </div>


                    <div class="mt-8 pt-6 border-t dark:border-gray-700">
                        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Courses Offered</h2>
                        <div v-if="university.courses && university.courses.length > 0" class="space-y-3">
                            <div
                                v-for="course in university.courses"
                                :key="course.id"
                                class="p-3 border dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150"
                            >
                                <Link :href="route('public.courses.show', course.id)" class="block">
                                    <h4 class="font-medium text-brand-primary dark:text-blue-400">{{ course.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ course.degree_level?.name || 'N/A' }} - {{ course.field_of_study?.name || 'N/A' }}
                                        <span v-if="course.duration_years"> ({{ course.duration_years }} years)</span>
                                    </p>
                                </Link>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 dark:text-gray-400 italic">
                            No specific courses listed for this university yet.
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t dark:border-gray-700">
                        <Link :href="route('public.universities')" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                             <span aria-hidden="true">&larr;</span> <span class="ml-1.5">Back to University Search</span>
                        </Link>
                    </div>

                </section>
            </div>
        </div>
    </PublicLayout>
</template>