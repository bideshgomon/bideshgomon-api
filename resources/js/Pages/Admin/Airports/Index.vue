<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash';

const props = defineProps({
    airports: Object,
    filters: Object,
});

const search = ref(props.filters.search);

const fetchAirports = (url) => {
    router.get(url, { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, debounce((value) => {
    router.get(route('admin.airports.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteAirport = (id) => {
    if (confirm('Are you sure you want to delete this airport?')) {
        router.delete(route('admin.api.airports.destroy', id), {
            onSuccess: () => fetchAirports(props.airports.path),
        });
    }
};
</script>

<template>
    <Head title="Manage Airports" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Manage Airports</h1>
                            <Link :href="route('admin.airports.create')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Add Airport
                            </Link>
                        </div>

                        <div class="mb-4">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Search by name or code..."
                                class="w-full px-3 py-2 border border-gray-300 rounded"
                            />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b">Name</th>
                                        <th class="py-2 px-4 border-b">Code</th>
                                        <th class="py-2 px-4 border-b">Country</th>
                                        <th class="py-2 px-4 border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="airport in airports.data" :key="airport.id">
                                        <td class="py-2 px-4 border-b">{{ airport.name }}</td>
                                        <td class="py-2 px-4 border-b">{{ airport.code }}</td>
                                        <td class="py-2 px-4 border-b">{{ airport.country.name }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <Link :href="route('admin.airports.edit', airport.id)" class="text-blue-500 hover:text-blue-700 mr-2">
                                                Edit
                                            </Link>
                                            <button @click="deleteAirport(airport.id)" class="text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="airports.data.length === 0">
                                        <td colspan="4" class="py-4 px-4 border-b text-center">No airports found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="airports.links" @page-click="fetchAirports" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>