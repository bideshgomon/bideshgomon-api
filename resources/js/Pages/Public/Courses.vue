<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'; // PATCH: Changed layout
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';

// Props from the controller (for filter options)
const props = defineProps({
    universities: Array,
    degreeLevels: Array, // Assuming these are passed, e.g., ['Bachelor', 'Master', 'PhD']
    fieldsOfStudy: Array, // Assuming these are passed, e.g., ['Engineering', 'Arts', 'Science']
});

// Reactive state for search filters
const searchTerm = ref('');
const selectedUniversity = ref('');
const selectedLevel = ref('');
const selectedField = ref('');

// Reactive state for results and loading
const courses = ref({ data: [], links: [] }); // Match pagination structure
const isLoading = ref(true); // PATCH: Set true for initial load
const firstLoadComplete = ref(false);

// Function to fetch courses from the API
const fetchCourses = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.public.search.courses'), {
            params: {
                search: searchTerm.value || undefined, // Send undefined if empty
                university_id: selectedUniversity.value || undefined,
                degree_level: selectedLevel.value || undefined, // Pass names directly if API expects strings
                field_of_study: selectedField.value || undefined, // Pass names directly if API expects strings
                // Add page param if needed for pagination via API
            }
        });
        courses.value = response.data;
    } catch (error) {
        console.error("Error fetching courses:", error);
        // Maybe set an error state here to show a message
    } finally {
        isLoading.value = false;
        firstLoadComplete.value = true;
    }
};

// Debounce watcher for filters
watch([searchTerm, selectedUniversity, selectedLevel, selectedField], debounce(fetchCourses, 300));

// Fetch initial data on mount
onMounted(fetchCourses);

// Helper function to format currency (optional)
const formatCurrency = (value) => {
    if (value === null || value === undefined) return 'N/A';
    // Basic formatting, improve as needed
    return `$${Number(value).toLocaleString()}`;
};

</script>

<template>
    <Head title="Find a Course" />

    <PublicLayout>

        <div class="hero" style="padding-top: 4rem; padding-bottom: 4rem; background-color: #fff; border-bottom: 1px solid var(--brand-border);">
            <div class="container">
                <h1 style="font-size: 2.5rem;">Find Your Course</h1>
                <p class="mt-2" style="font-size: 1.1rem; color: #555;">Explore academic programs from universities around the globe.</p>
            </div>
        </div>

        <div class="container py-8 md:py-12">

            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="search" class="font-semibold text-sm">Search Course Name</label>
                        <TextInput
                            id="search"
                            type="text"
                            class="mt-1 block w-full text-sm"
                            v-model="searchTerm"
                            placeholder="e.g., 'Computer Science'"
                        />
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="university" class="font-semibold text-sm">University</label>
                        <select id="university" class="mt-1 block w-full text-sm" v-model="selectedUniversity">
                            <option value="">All Universities</option>
                            <option v-for="uni in universities" :key="uni.id" :value="uni.id">{{ uni.name }}</option>
                        </select>
                    </div>
                     <div class="form-group" style="margin-bottom: 0;">
                        <label for="level" class="font-semibold text-sm">Degree Level</label>
                        <select id="level" class="mt-1 block w-full text-sm" v-model="selectedLevel">
                            <option value="">All Levels</option>
                            <option v-for="level in degreeLevels" :key="level" :value="level">{{ level }}</option>
                        </select>
                    </div>
                     <div class="form-group" style="margin-bottom: 0;">
                        <label for="field" class="font-semibold text-sm">Field of Study</label>
                        <select id="field" class="mt-1 block w-full text-sm" v-model="selectedField">
                            <option value="">All Fields</option>
                             <option v-for="field in fieldsOfStudy" :key="field" :value="field">{{ field }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div v-if="isLoading && !firstLoadComplete" class="space-y-4">
                 <div v-for="n in 5" :key="n" class="card animate-pulse flex gap-4 items-center">
                    <div class="w-16 h-16 bg-gray-200 rounded flex-shrink-0"></div>
                    <div class="flex-grow space-y-2">
                        <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    </div>
                </div>
            </div>

             <div v-else>
                <div v-if="courses.data.length > 0" class="space-y-4">
                    <Link
                        v-for="course in courses.data"
                        :key="course.id"
                        :href="route('public.courses.show', course.id)"
                        class="card block hover:shadow-md transition duration-150 ease-in-out no-underline"
                    >
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-shrink-0 w-16 h-16">
                                <img
                                    v-if="course.university.logo_path"
                                    :src="`/storage/${course.university.logo_path}`"
                                    :alt="`${course.university.name} Logo`"
                                    class="w-full h-full object-contain rounded border p-1"
                                />
                                <div v-else class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-gray-400 font-bold text-xl">
                                    {{ course.university.name.charAt(0) }}
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h3 class="font-semibold text-lg" style="color: var(--brand-primary);">{{ course.name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ course.university.name }} - {{ course.university.city }}, {{ course.university.country.name }}
                                </p>
                                <div class="mt-2 text-sm text-gray-800 dark:text-gray-300 flex flex-wrap gap-x-4 gap-y-1">
                                    <span><strong>Level:</strong> {{ course.degree_level || 'N/A' }}</span>
                                    <span><strong>Field:</strong> {{ course.field_of_study || 'N/A' }}</span>
                                    <span v-if="course.duration_years"><strong>Duration:</strong> {{ course.duration_years }} years</span>
                                    <span v-if="course.tuition_fee"><strong>Tuition:</strong> {{ formatCurrency(course.tuition_fee) }}/year</span>
                                    <span v-if="course.application_deadline" class="text-red-600"><strong>Apply by:</strong> {{ course.application_deadline }}</span>
                                </div>
                                </div>
                            <div class="flex-shrink-0 flex items-center justify-end mt-2 sm:mt-0">
                                <span class="text-brand-primary font-semibold text-sm">
                                    Details <span aria-hidden="true">&rarr;</span>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                 <div v-else class="text-center py-12">
                    <h3 class="text-xl font-semibold text-gray-700">No Courses Found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your search filters.</p>
                </div>

                <Pagination class="mt-8" :links="courses.links" />
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
/* Scoped styles can be added if needed */
.no-underline {
    text-decoration: none !important;
}
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  /* Fallback for non-webkit */
  max-height: 3.2em; /* Adjust based on line-height */
  line-height: 1.6em; /* Adjust based on line-height */
}
</style>