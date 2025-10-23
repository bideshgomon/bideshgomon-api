<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
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
const isLoading = ref(false);
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
    <Head title="Search Universities" />

    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-semibold mb-6">Find Your University</h2>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Search by Name or City
                                </label>
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="searchTerm"
                                    placeholder="e.g., Oxford or London"
                                />
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Country
                                </label>
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

                        <!-- Loading -->
                        <div v-if="isLoading" class="text-center py-10">
                            <p>Loading universities...</p>
                        </div>

                        <!-- Results -->
                        <div v-else>
                            <!-- No results -->
                            <div v-if="universities.data.length === 0 && firstLoadComplete" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                No universities found matching your criteria.
                            </div>

                            <!-- Cards -->
                            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <Link
                                    v-for="uni in universities.data"
                                    :key="uni.id"
                                    :href="route('public.universities.show', uni.id)"
                                    class="block border dark:border-gray-700 rounded-lg p-4 shadow hover:shadow-lg hover:border-brand-primary dark:hover:border-blue-500 transition-all duration-200"
                                >
                                    <img
                                        v-if="uni.logo_path"
                                        :src="`/storage/${uni.logo_path}`"
                                        alt="Logo"
                                        class="h-16 w-auto mb-3 mx-auto"
                                    />
                                    <div
                                        v-else
                                        class="h-16 w-16 bg-gray-200 dark:bg-gray-700 rounded mb-3 mx-auto flex items-center justify-center text-gray-500"
                                    >
                                        ?
                                    </div>

                                    <h3 class="font-semibold text-lg text-center">{{ uni.name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                                        {{ uni.city }}, {{ uni.country?.name }}
                                    </p>
                                    <div class="mt-3 text-center">
                                        <span class="text-brand-primary hover:underline text-sm">
                                            View Details <span aria-hidden="true">&rarr;</span>
                                        </span>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <Pagination class="mt-6" :links="universities.links" />
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
