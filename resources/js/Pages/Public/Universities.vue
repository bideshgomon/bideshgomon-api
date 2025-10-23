<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Using GuestLayout for public access
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce'; // For smoother searching

// Props from the controller (for filter options)
const props = defineProps({
    countries: Array,
});

// Reactive state for search filters
const searchTerm = ref('');
const selectedCountry = ref('');

// Reactive state for results and loading
const universities = ref({ data: [], links: [] }); // Match pagination structure
const isLoading = ref(false);
const firstLoadComplete = ref(false); // To avoid showing "no results" initially

// Function to fetch universities from the API
const fetchUniversities = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.public.search.universities'), {
            params: {
                search: searchTerm.value,
                country_id: selectedCountry.value,
                // Add page parameter if needed for manual pagination calls,
                // but usually Inertia handles this if using Inertia Links for pagination
            }
        });
        universities.value = response.data;
    } catch (error) {
        console.error("Error fetching universities:", error);
        // Handle error display if needed
    } finally {
        isLoading.value = false;
        firstLoadComplete.value = true;
    }
};

// Watch for changes in filters and fetch data (debounced)
// This waits 300ms after the user stops typing before searching
watch([searchTerm, selectedCountry], debounce(fetchUniversities, 300));

// Fetch initial data when the component mounts
onMounted(fetchUniversities);

</script>

<template>
    <Head title="Search Universities" />

    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-semibold mb-6">Find Your University</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search by Name or City</label>
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="searchTerm"
                                    placeholder="e.g., Oxford or London"
                                />
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                                <select
                                    id="country"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="selectedCountry"
                                >
                                    <option value="">All Countries</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                            </div>
                             </div>

                        <div v-if="isLoading" class="text-center py-10">
                            <p>Loading universities...</p>
                            {/* You could add a spinner icon here */}
                        </div>

                        <div v-else>
                             <div v-if="universities.data.length === 0 && firstLoadComplete" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                No universities found matching your criteria.
                            </div>
                            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="uni in universities.data" :key="uni.id" class="border dark:border-gray-700 rounded-lg p-4 shadow hover:shadow-md transition-shadow duration-200">
                                    <img v-if="uni.logo_path" :src="`/storage/${uni.logo_path}`" alt="Logo" class="h-16 w-auto mb-3 mx-auto">
                                     <div v-else class="h-16 w-16 bg-gray-200 dark:bg-gray-700 rounded mb-3 mx-auto flex items-center justify-center text-gray-500">?</div>

                                    <h3 class="font-semibold text-lg text-center">{{ uni.name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">{{ uni.city }}, {{ uni.country.name }}</p>
                                    <div class="mt-3 text-center">
                                        <a v-if="uni.website_url" :href="uni.website_url" target="_blank" rel="noopener noreferrer" class="text-brand-primary hover:underline text-sm">
                                            Visit Website
                                        </a>
                                         <span v-else class="text-sm text-gray-400 dark:text-gray-500">Website not available</span>
                                    </div>
                                    </div>
                            </div>
                        </div>

                        <Pagination class="mt-6" :links="universities.links" />

                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>