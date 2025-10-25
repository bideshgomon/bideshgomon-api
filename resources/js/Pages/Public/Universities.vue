<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'; // PATCH: Changed layout
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';

// Props from Laravel controller
const props = defineProps({
    countries: Array,
});

// Reactive states
const searchTerm = ref('');
const selectedCountry = ref('');
const universities = ref({ data: [], links: [] });
const isLoading = ref(true); // PATCH: Set to true on initial load
const firstLoadComplete = ref(false);

// Fetch universities via API
const fetchUniversities = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.public.search.universities'), {
            params: {
                search: searchTerm.value,
                country_id: selectedCountry.value,
            },
        });
        universities.value = response.data;
    } catch (error) {
        console.error('Error fetching universities:', error);
    } finally {
        isLoading.value = false;
        firstLoadComplete.value = true;
    }
};

// Debounce watcher for filters
watch([searchTerm, selectedCountry], debounce(fetchUniversities, 300));

// Fetch initial data on mount
onMounted(fetchUniversities);
</script>

<template>
    <Head title="Find a University" />

    <PublicLayout>
        
        <div class="hero" style="padding-top: 4rem; padding-bottom: 4rem; background-color: #fff; border-bottom: 1px solid var(--brand-border);">
            <div class="container">
                <h1 style="font-size: 2.5rem;">Find Your University</h1>
                <p class="mt-2" style="font-size: 1.1rem; color: #555;">Search thousands of institutions worldwide to find your perfect match.</p>
            </div>
        </div>

        <div class="container py-8 md:py-12">
            
            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="search" class="font-semibold">Search by Name or City</label>
                        <TextInput
                            id="search"
                            type="text"
                            class="mt-1 block w-full"
                            vLocation-model="searchTerm"
                            placeholder="e.g., 'University of Toronto' or 'Sydney'"
                        />
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="country" class="font-semibold">Filter by Country</label>
                        <select
                            id="country"
                            class="mt-1 block w-full"
                            v-model="selectedCountry"
                        >
                            <option value="">All Countries</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div v-if="isLoading && !firstLoadComplete" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="n in 6" :key="n" class="card animate-pulse">
                    <div class="h-16 w-16 bg-gray-200 rounded mb-3 mx-auto"></div>
                    <div class="h-6 bg-gray-200 rounded w-3/4 mx-auto mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mx-auto"></div>
                    <div class="mt-3 text-center">
                        <div class="h-4 bg-gray-200 rounded w-1/4 mx-auto"></div>
                    </div>
                </div>
            </div>

            <div v-else>
                <div v-if="universities.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="uni in universities.data"
                        :key="uni.id"
                        :href="route('public.universities.show', uni.id)"
                        class="card text-decoration-none"
                    >
                        <img
                            v-if="uni.logo_path"
                            :src="`/storage/${uni.logo_path}`"
                            :alt="`${uni.name} Logo`"
                            class="h-16 w-auto mb-3 mx-auto"
                            style="max-width: 150px; object-fit: contain;"
                        />
                        <div
                            v-else
                            class="h-16 w-16 bg-gray-200 dark:bg-gray-700 rounded-full mb-3 mx-auto flex items-center justify-center text-gray-500 text-2xl font-bold"
                        >
                            {{ uni.name.charAt(0) }}
                        </div>

                        <h3 class="font-semibold text-lg text-center" style="color: var(--brand-dark);">{{ uni.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                            {{ uni.city }}, {{ uni.country?.name }}
                        </p>
                        <div class="mt-3 text-center">
                            <span class="font-semibold" style="color: var(--brand-primary); text-decoration: none;">
                                View Details <span aria-hidden="true">&rarr;</span>
                            </span>
                        </div>
                    </Link>
                </div>
                
                <div v-else class="text-center py-12">
                    <h3 class="text-xl font-semibold text-gray-700">No Universities Found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your search filters.</p>
                </div>

                <Pagination class="mt-8" :links="universities.links" />
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
/* Ensure cards are not underlined when wrapped in <Link> */
.card {
    text-decoration: none;
}
</style>