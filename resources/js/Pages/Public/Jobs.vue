<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';
import { CalendarDaysIcon, MapPinIcon, BanknotesIcon } from '@heroicons/vue/24/outline'; // Icons

// Props for filters
const props = defineProps({
    categories: Array,
    countries: Array,
});

// Filters
const searchTerm = ref('');
const selectedCategory = ref('');
const selectedCountry = ref('');

// Results & Loading
const jobs = ref({ data: [], links: [] });
const isLoading = ref(false);
const firstLoadComplete = ref(false);

// Fetch Function
const fetchJobs = async () => {
    isLoading.value = true;
    firstLoadComplete.value = false; // Reset on new fetch
    try {
        const response = await axios.get(route('api.public.search.jobs'), {
            params: {
                search: searchTerm.value,
                job_category_id: selectedCategory.value,
                country_id: selectedCountry.value,
            }
        });
        jobs.value = response.data;
    } catch (error) {
        console.error("Error fetching jobs:", error);
    } finally {
        isLoading.value = false;
        firstLoadComplete.value = true; // Mark load complete
    }
};

// Watchers
watch([searchTerm, selectedCategory, selectedCountry], debounce(fetchJobs, 300));
onMounted(fetchJobs);

// Helpers
const formatSalary = (min, max, currency, period) => {
    if (!min && !max) return 'Not specified';
    let salary = '';
    const formatter = new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'USD', minimumFractionDigits: 0, maximumFractionDigits: 0 });

    if (min) salary += formatter.format(min);
    if (max) salary += ` - ${formatter.format(max)}`;
    else if (min) salary += '+'; // Indicate min only
    return `${salary} ${period || ''}`.trim();
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
        // Attempt to parse date regardless of format, default to UTC if timezone missing
         const date = new Date(dateString.includes('T') ? dateString : dateString + 'T00:00:00Z');
         if (isNaN(date)) return 'Invalid Date'; // Handle invalid date strings
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString(undefined, options);
    } catch (e) {
         console.error("Error formatting date:", dateString, e);
         return 'Invalid Date';
    }
};

</script>

<template>
    <Head title="Search Jobs" />

    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-semibold mb-6 text-center md:text-left">Find Your Next Opportunity</h2>

                        <!-- Filters Section - Use consistent styling -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8"> <!-- Increased gap -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keywords (Title, Company, Skill)</label>
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="searchTerm"
                                    placeholder="e.g., Engineer, Google, PHP"
                                />
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Job Category</label>
                                <!-- Standard Select Styling -->
                                <select
                                    id="category"
                                    v-model="selectedCategory"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                                <!-- Standard Select Styling -->
                                <select
                                    id="country"
                                    v-model="selectedCountry"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="">All Countries</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div v-if="isLoading" class="text-center py-10">
                            <p class="text-gray-500 dark:text-gray-400">Loading jobs...</p>
                            <svg class="animate-spin h-8 w-8 text-brand-primary mx-auto mt-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <!-- Results or No Results Message -->
                        <div v-else>
                            <div v-if="jobs.data.length === 0 && firstLoadComplete" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                No job postings found matching your criteria.
                            </div>
                            <div v-else class="space-y-5">
                                <div v-for="job in jobs.data" :key="job.id" class="border dark:border-gray-700 rounded-lg p-5 shadow hover:shadow-md transition-shadow duration-200 relative bg-white dark:bg-gray-800">
                                    <span v-if="job.is_featured" class="absolute top-0 right-0 -mt-2 -mr-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full shadow-md">Featured</span>
                                    <h3 class="font-semibold text-lg text-brand-primary dark:text-blue-400">{{ job.title }}</h3>
                                    <p class="text-md text-gray-700 dark:text-gray-300">{{ job.company_name }}</p>

                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 flex flex-wrap items-center gap-x-4 gap-y-1">
                                        <span class="flex items-center whitespace-nowrap"><MapPinIcon class="h-4 w-4 mr-1 text-gray-400"/> {{ job.location_city }}{{ job.country ? ', ' + job.country.name : '' }}</span>
                                        <span v-if="job.employment_type" class="flex items-center whitespace-nowrap"><CalendarDaysIcon class="h-4 w-4 mr-1 text-gray-400"/>{{ job.employment_type }}</span>
                                         <span v-if="job.job_category" class="flex items-center whitespace-nowrap">{{ job.job_category.name }}</span>
                                    </div>
                                     <div v-if="job.salary_min || job.salary_max" class="mt-2 text-sm text-green-700 dark:text-green-400 flex items-center">
                                         <BanknotesIcon class="h-4 w-4 mr-1"/> {{ formatSalary(job.salary_min, job.salary_max, job.salary_currency, job.salary_period) }}
                                     </div>

                                    <p class="mt-3 text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                        {{ job.description }}
                                    </p>
                                     <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center text-xs text-gray-500 dark:text-gray-500">
                                         <span class="mb-2 sm:mb-0">Posted: {{ formatDate(job.posting_date || job.created_at) }}</span>
                                         <a v-if="job.apply_url" :href="job.apply_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-3 py-1 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-opacity-90 transition ease-in-out duration-150">
                                             Apply Now
                                         </a>
                                         <span v-else class="italic">Apply link unavailable</span>
                                     </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <Pagination class="mt-6" :links="jobs.links" />
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  /* Fallback for non-webkit */
  max-height: 3.6em; /* Adjust based on line-height */
  line-height: 1.8em; /* Adjust based on font size */
}
</style>