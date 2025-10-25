<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'; // PATCH: Changed layout
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { AcademicCapIcon, BookOpenIcon, ClockIcon, CurrencyDollarIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline'; // Icons

// Course data passed from the controller
const props = defineProps({
    course: Object,
});

// Helper function to format currency
const formatCurrency = (value) => {
    if (value === null || value === undefined) return 'N/A';
    // Basic formatting, improve locale/currency symbol as needed
    return `$${Number(value).toLocaleString()}`;
};

// Computed property for University logo URL (handle potential null)
const universityLogoUrl = computed(() => {
    return props.course.university?.logo_path ? `/storage/${props.course.university.logo_path}` : null;
});

</script>

<template>
    <Head :title="course.name" />

    <PublicLayout>
        <div class="container py-8 md:py-12">
             <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <div class="mb-6 pb-6 border-b dark:border-gray-700">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ course.name }}
                        </h1>
                        <Link
                            :href="route('public.universities.show', course.university.id)"
                            class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-brand-primary transition duration-150 group"
                        >
                            <img
                                v-if="universityLogoUrl"
                                :src="universityLogoUrl"
                                :alt="`${course.university.name} Logo`"
                                class="h-6 w-6 rounded-sm object-contain border dark:border-gray-600"
                            />
                            <div
                                v-else
                                class="h-6 w-6 bg-gray-200 dark:bg-gray-700 rounded-sm flex items-center justify-center text-gray-500 text-xs font-bold"
                            >
                                {{ course.university.name.charAt(0) }}
                            </div>
                            <span class="group-hover:underline">{{ course.university.name }}</span>
                            <span class="text-sm">({{ course.university.city }}, {{ course.university.country.name }})</span>
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6 mb-8">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <AcademicCapIcon class="h-4 w-4 mr-1.5"/> Degree Level
                            </h3>
                            <p class="mt-1 text-lg font-medium">{{ course.degree_level?.name || 'N/A' }}</p>
                        </div>
                         <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <BookOpenIcon class="h-4 w-4 mr-1.5"/> Field of Study
                            </h3>
                            <p class="mt-1 text-lg font-medium">{{ course.field_of_study?.name || 'N/A' }}</p>
                        </div>
                        <div v-if="course.duration_years">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <ClockIcon class="h-4 w-4 mr-1.5"/> Duration
                            </h3>
                            <p class="mt-1 text-lg font-medium">{{ course.duration_years }} {{ course.duration_years > 1 ? 'years' : 'year' }}</p>
                        </div>
                        <div v-if="course.tuition_fee">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center">
                                <CurrencyDollarIcon class="h-4 w-4 mr-1.5"/> Tuition Fee
                            </h3>
                            <p class="mt-1 text-lg font-medium">{{ formatCurrency(course.tuition_fee) }} <span class="text-sm text-gray-500">/ year (approx)</span></p>
                        </div>
                         <div v-if="course.application_deadline">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center text-red-600 dark:text-red-500">
                                <CalendarDaysIcon class="h-4 w-4 mr-1.5"/> Application Deadline
                            </h3>
                            <p class="mt-1 text-lg font-medium text-red-600 dark:text-red-500">{{ course.application_deadline }}</p>
                        </div>
                        </div>

                    <div v-if="course.description" class="mt-8 pt-6 border-t dark:border-gray-700">
                        <h2 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Course Description</h2>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ course.description }}</p>
                    </div>

                    <div class="mt-8 pt-6 border-t dark:border-gray-700 flex flex-wrap items-center gap-x-4 gap-y-2">
                        <Link :href="route('public.courses')" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                             <span aria-hidden="true">&larr;</span> <span class="ml-1.5">Back to Course Search</span>
                        </Link>
                         <span class="text-gray-300 dark:text-gray-600 hidden sm:inline">|</span>
                         <Link :href="route('public.universities.show', course.university.id)" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-primary">
                            View University Details <span aria-hidden="true" class="ml-1.5">&rarr;</span>
                        </Link>
                    </div>

                </section>
            </div>
        </div>
    </PublicLayout>
</template>