<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

// We'll need a public layout later, for now use Authenticated or make a simple one
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Placeholder layout
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    universities: Object, // Paginated university data
    filters: Object,      // Applied filters (currently just search)
});

const search = ref(props.filters.search || '');

// Watch for search input changes
watch(search, debounce((value) => {
    router.get(route('universities.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300)); // Debounce search requests

// Helper to get location string
const getLocation = (uni) => {
    if (!uni.city) return 'Location Unknown';
    if (uni.city.state) { // City -> State -> Country
        return `${uni.city.name}, ${uni.city.state.name}, ${uni.city.state.country.name}`;
    }
    if (uni.city.country) { // City -> Country (direct)
        return `${uni.city.name}, ${uni.city.country.name}`;
    }
    return uni.city.name; // Just city? (Shouldn't happen with our structure)
};

</script>

<template>
    <Head title="Universities" />

    <AuthenticatedLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <header class="mb-8 px-4 sm:px-0">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                        Find Universities
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Browse universities offering courses abroad.
                    </p>
                    <div class="mt-6 max-w-md">
                        <TextInput
                             id="search"
                             type="text"
                             class="block w-full"
                             v-model="search"
                             placeholder="Search by name, location..."
                         />
                    </div>
                     </header>

                <div v-if="universities.data.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="uni in universities.data"
                        :key="uni.id"
                        :href="route('universities.show', uni.id)"
                        class="group block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden"
                    >
                        <div class="h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 dark:text-gray-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>
                         </div>
                        <div class="p-4 sm:p-5">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-brand-primary truncate">
                                {{ uni.name }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ getLocation(uni) }}
                            </p>
                            <p v-if="uni.description" class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                {{ uni.description }}
                            </p>
                        </div>
                    </Link>
                </div>

                 <div v-else class="text-center py-10">
                    <p class="text-gray-500 dark:text-gray-400">No universities found matching your criteria.</p>
                </div>

                <Pagination class="mt-8" :links="universities.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>