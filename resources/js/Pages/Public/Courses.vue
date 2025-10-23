<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';

// Props from the controller (for filter options)
const props = defineProps({
    universities: Array,
    degreeLevels: Array,
    fieldsOfStudy: Array,
});

// Reactive state for search filters
const searchTerm = ref('');
const selectedUniversity = ref('');
const selectedLevel = ref('');
const selectedField = ref('');

// Reactive state for results and loading
const courses = ref({ data: [], links: [] }); // Match pagination structure
const isLoading = ref(false);
const firstLoadComplete = ref(false);

// Function to fetch courses from the API
const fetchCourses = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.public.search.courses'), {
            params: {
                search: searchTerm.value,
                university_id: selectedUniversity.value,
                degree_level: selectedLevel.value,
                field_of_study: selectedField.value,
                // Add page param if needed
            }
        });
        courses.value = response.data;
    } catch (error) {
        console.error("Error fetching courses:", error);
    } finally {
        isLoading.value = false;
        firstLoadComplete.value = true;
    }
};

// Watch for changes in filters and fetch data (debounced)
watch([searchTerm, selectedUniversity, selectedLevel, selectedField], debounce(fetchCourses, 300));

// Fetch initial data when the component mounts
onMounted(fetchCourses);

// Helper function to format currency (optional)
const formatCurrency = (value) => {
    if (value === null || value === undefined) return 'N/A';
    // Basic formatting, consider using a library for more complex needs
    return `$${Number(value).toLocaleString()}`;
};

</script>

<template>
    <Head title="Search Courses" />

    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-semibold mb-6">Find Your Course</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course Name</label>
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="searchTerm"
                                    placeholder="e.g., Computer Science"
                                />
                            </div>
                            <div>
                                <label for="university" class="block text-sm font-medium text-gray-700 dark:text-gray-300">University</label>
                                <select
                                    id="university"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="selectedUniversity"
                                >
                                    <option value="">All Universities</option>
                                    <option v-for="uni in universities" :key="uni.id" :value="uni.id">
                                        {{ uni.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Degree Level</label>
                                <select
                                    id="level"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="selectedLevel"
                                >
                                    <option value="">All Levels</option>
                                    <option v-for="level in degreeLevels" :key="level" :value="level">
                                        {{ level }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="field" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Field of Study</label>
                                <select
                                    id="field"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="selectedField"
                                >
                                    <option value="">All Fields</option>
                                    <option v-for="field in fieldsOfStudy" :key="field" :value="field">
                                        {{ field }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="isLoading" class="text-center py-10">
                            <p>Loading courses...</p>
                        </div>

                        <div v-else>
                            <div v-if="courses.data.length === 0 && firstLoadComplete" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                No courses found matching your criteria.
                            </div>
                            
                            <div v-else class="space-y-4">
                                <Link
                                    v-for="course in courses.data"
                                    :key="course.id"
                                    :href="route('public.courses.show', course.id)"
                                    class="block border dark:border-gray-700 rounded-lg p-4 shadow hover:shadow-md hover:border-brand-primary dark:hover:border-blue-500 transition-all duration-200"
                                >
                                    <h3 class="font-semibold text-lg text-brand-primary dark:text-blue-400">{{ course.name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span @click.stop> 
                                            <Link :href="route('public.universities.show', course.university.id)" class="hover:underline">
                                                {{ course.university.name }}
                                            </Link>
                                        </span>
                                        - {{ course.university.city }}, {{ course.university.country.name }}
                                    </p>
                                    <div class="mt-2 text-sm text-gray-800 dark:text-gray-300 flex flex-wrap gap-x-4 gap-y-1">
                                        <span><strong>Level:</strong> {{ course.degree_level }}</span>
                                        <span><strong>Field:</strong> {{ course.field_of_study }}</span>
                                        <span v-if="course.duration_years"><strong>Duration:</strong> {{ course.duration_years }} years</span>
                                        <span v-if="course.tuition_fee"><strong>Tuition:</strong> {{ formatCurrency(course.tuition_fee) }}/year</span>
                                        <span v-if="course.application_deadline"><strong>Apply by:</strong> {{ course.application_deadline }}</span>
                                    </div>
                                    <p v-if="course.description" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ course.description.substring(0, 150) }}{{ course.description.length > 150 ? '...' : '' }}
                                    </p>
                                </Link> 
                            </div>
                        </div>

                        <Pagination class="mt-6" :links="courses.links" />

                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>