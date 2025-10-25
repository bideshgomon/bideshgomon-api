<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue'; // Needed for search
import DangerButton from '@/Components/DangerButton.vue'; // For delete button
import PrimaryButton from '@/Components/PrimaryButton.vue'; // For Add button
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    airports: Object, // Paginated airport data
    filters: Object, // Search filters applied
});

const search = ref(props.filters.search || '');

// Debounced search watcher
watch(search, debounce((value) => {
    router.get(route('admin.airports.index'), { search: value || undefined }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Delete function
const deleteAirport = (id) => {
    if (confirm('Are you sure you want to delete this airport?')) {
        router.delete(route('api.admin.airports.destroy', id), { // Use API route for delete action
            preserveScroll: true,
            // onSuccess: handled by AdminLayout flash message
        });
    }
};
</script>

<template>
    <Head title="Manage Airports" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Airports
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage all airports in the system.
                        </p>
                    </div>
                    <Link :href="route('admin.airports.create')">
                        <PrimaryButton>Add New Airport</PrimaryButton>
                    </Link>
                </header>

                <div class="mb-4">
                     <TextInput
                        type="text"
                        v-model="search"
                        placeholder="Search by name, code, city, or country..."
                        class="w-full sm:w-1/2 lg:w-1/3"
                    />
                </div>

                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">IATA</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">City / State / Country</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="airports.data.length === 0">
                                        <td colspan="4" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No airports found matching your search.
                                        </td>
                                    </tr>
                                    <tr v-for="airport in airports.data" :key="airport.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ airport.name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ airport.iata_code }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ airport.city?.name }}, {{ airport.city?.state?.name }}, {{ airport.city?.state?.country?.name }}
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-2 whitespace-nowrap">
                                            <Link :href="route('admin.airports.edit', airport.id)" class="text-brand-primary hover:text-opacity-80">Edit</Link>
                                            <DangerButton @click="deleteAirport(airport.id)" class="text-xs px-2 py-1">Delete</DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="airports.links" />
            </section>
        </div>
    </AdminLayout>
</template>