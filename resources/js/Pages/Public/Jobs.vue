<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'; // PATCH: Changed layout
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';
import { CalendarDaysIcon, MapPinIcon, BanknotesIcon, BuildingOffice2Icon } from '@heroicons/vue/24/outline'; // Icons

// Props for filters
const props = defineProps({
    categories: Array, // Assuming Job Categories are passed
    countries: Array,
});

// Filters
const searchTerm = ref('');
const selectedCategory = ref('');
const selectedCountry = ref('');

// Results & Loading
const jobs = ref({ data: [], links: [] });
const isLoading = ref(true); // PATCH: Set true for initial load
const firstLoadComplete = ref(false);

// Fetch Function
const fetchJobs = async () => {
    // Only set isLoading true if it's not the very first load triggered by onMounted
    if (firstLoadComplete.value) {
        isLoading.value = true;
    }
    try {
        const response = await axios.get(route('api.public.search.jobs'), { // Assuming API route exists
            params: {
                search: searchTerm.value || undefined,
                job_category_id: selectedCategory.value || undefined, // Adjust param name if needed
                country_id: selectedCountry.value || undefined,
            }
        });
        jobs.value = response.data;
    } catch (error) {
        console.error("Error fetching jobs:", error);
    } finally {
        isLoading.value = false;
        firstLoadComplete.value = true; // Mark first load complete *after* fetch
    }
};

// Debounce watcher
watch([searchTerm, selectedCategory, selectedCountry], debounce(fetchJobs, 300));

// Initial fetch
onMounted(fetchJobs);

// Helper function to format currency (assuming salary is numeric)
const formatCurrency = (value, currency = 'USD') => {
    if (value === null || value === undefined) return 'Not specified';
    // Basic formatting
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency, minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};

// Helper function to format date
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        return new Date(dateString).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    } catch (e) {
        return dateString; // Return original if parsing fails
    }
};
</script>

<template>
    <Head title="Find a Job" />

    <PublicLayout>

        <div class="hero" style="padding-top: 4rem; padding-bottom: 4rem; background-color: #fff; border-bottom: 1px solid var(--brand-border);">
            <div class="container">
                <h1 style="font-size: 2.5rem;">Find Your Next Job</h1>
                <p class="mt-2" style="font-size: 1.1rem; color: #555;">Browse international job opportunities matching your skills.</p>
            </div>
        </div>

        <div class="container py-8 md:py-12">

             <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="search" class="font-semibold text-sm">Search Title/Company</label>
                        <TextInput
                            id="search"
                            type="text"
                            class="mt-1 block w-full text-sm"
                            v-model="searchTerm"
                            placeholder="e.g., 'Software Engineer' or 'Google'"
                        />
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="category" class="font-semibold text-sm">Job Category</label>
                        <select id="category" class="mt-1 block w-full text-sm" v-model="selectedCategory">
                            <option value="">All Categories</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                     <div class="form-group" style="margin-bottom: 0;">
                        <label for="country" class="font-semibold text-sm">Country</label>
                        <select id="country" class="mt-1 block w-full text-sm" v-model="selectedCountry">
                            <option value="">All Countries</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div v-if="isLoading && !firstLoadComplete" class="space-y-4">
                 <div v-for="n in 5" :key="n" class="card animate-pulse flex flex-col sm:flex-row gap-4">
                    <div class="w-16 h-16 bg-gray-200 rounded flex-shrink-0"></div>
                    <div class="flex-grow space-y-2">
                        <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    </div>
                    <div class="w-full sm:w-auto h-8 bg-gray-200 rounded mt-2 sm:mt-0"></div>
                </div>
            </div>

            <div v-else>
                 <div v-if="jobs.data.length > 0" class="space-y-4">
                    <div
                        v-for="job in jobs.data"
                        :key="job.id"
                        class="card flex flex-col sm:flex-row gap-4 transition duration-150 ease-in-out"
                    >
                        <div class="flex-shrink-0 w-16 h-16">
                             <img
                                v-if="job.agency?.logo_path"
                                :src="`/storage/${job.agency.logo_path}`"
                                :alt="`${job.agency.name} Logo`"
                                class="w-full h-full object-contain rounded border p-1"
                            />
                            <div v-else class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-gray-400 font-bold text-xl">
                                {{ job.agency?.name?.charAt(0) || '?' }}
                            </div>
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-semibold text-lg" style="color: var(--brand-primary);">{{ job.title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                <BuildingOffice2Icon class="h-4 w-4 inline"/> {{ job.agency?.name || 'Company Not Listed' }}
                            </p>
                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300 flex flex-wrap gap-x-4 gap-y-1">
                                <span class="flex items-center gap-1"><MapPinIcon class="h-4 w-4 inline"/> {{ job.location_city }}, {{ job.location_country?.name || 'N/A' }}</span>
                                <span v-if="job.salary" class="flex items-center gap-1"><BanknotesIcon class="h-4 w-4 inline"/> {{ formatCurrency(job.salary, job.salary_currency) }} {{ job.salary_period || '' }}</span>
                                <span class="flex items-center gap-1"><CalendarDaysIcon class="h-4 w-4 inline"/> Posted: {{ formatDate(job.posting_date || job.created_at) }}</span>
                                <span v-if="job.deadline" class="flex items-center gap-1 text-red-600"><CalendarDaysIcon class="h-4 w-4 inline"/> Apply by: {{ formatDate(job.deadline) }}</span>
                            </div>
                            <p v-if="job.description" class="mt-2 text-xs text-gray-500 line-clamp-2">
                                {{ job.description }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 flex sm:flex-col items-center justify-end mt-2 sm:mt-0 sm:ml-4">
                            <a v-if="job.apply_url" :href="job.apply_url" target="_blank" rel="noopener noreferrer" class="btn btn-primary text-xs !py-1.5 !px-3 whitespace-nowrap">
                                Apply Now
                            </a>
                             <span v-else class="text-xs text-gray-400 italic mt-1">No link</span>
                             </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <h3 class="text-xl font-semibold text-gray-700">No Jobs Found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your search filters or check back later.</p>
                </div>

                <Pagination class="mt-8" :links="jobs.links" />
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
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