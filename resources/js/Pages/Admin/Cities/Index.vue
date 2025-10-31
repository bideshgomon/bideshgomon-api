<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue'; // Assuming you have this

// Set the layout for this page
defineOptions({
    layout: AdminLayout
});

// Get props from the controller
defineProps({
    cities: Object, // This will be a paginated object
});
</script>

<template>
    <Head title="Manage Cities" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-semibold mb-4">Manage Cities</h2>
                    <p class="mb-6">
                        This is the placeholder page for managing cities.
                    </p>

                    <div class="mt-4" v-if="cities && cities.data.length > 0">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="city in cities.data" :key="city.id" class="py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ city.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    State: {{ city.state ? city.state.name : 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Country: {{ city.state && city.state.country ? city.state.country.name : 'N/A' }}
                                </p>
                            </li>
                        </ul>

                        <Pagination class="mt-6" :links="cities.links" />
                    </div>
                    <div v-else>
                        <p>No cities found.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>