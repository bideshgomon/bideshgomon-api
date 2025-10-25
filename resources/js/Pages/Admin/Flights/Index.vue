<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash';

const props = defineProps({
    flights: Object,
    filters: Object,
});

const search = ref(props.filters.search);

const fetchFlights = (url) => {
    router.get(url, { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, debounce((value) => {
    router.get(route('admin.flights.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteFlight = (id) => {
    if (confirm('Are you sure you want to delete this flight?')) {
        router.delete(route('admin.api.flights.destroy', id), {
            onSuccess: () => fetchFlights(props.flights.path),
        });
    }
};

const formatDateTime = (dateTimeString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateTimeString).toLocaleString(undefined, options);
};
</script>

<template>
    <Head title="Manage Flights" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Manage Flights</h1>
                            <Link :href="route('admin.flights.create')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Add Flight
                            </Link>
                        </div>

                        <div class="mb-4">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Search by flight number or airline..."
                                class="w-full px-3 py-2 border border-gray-300 rounded"
                            />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b">Flight No.</th>
                                        <th class="py-2 px-4 border-b">Airline</th>
                                        <th class="py-2 px-4 border-b">From</th>
                                        <th class="py-2 px-4 border-b">To</th>
                                        <th class="py-2 px-4 border-b">Departure</th>
                                        <th class="py-2 px-4 border-b">Arrival</th>
                                        <th class="py-2 px-4 border-b">Price</th>
                                        <th class="py-2 px-4 border-b">Seats</th>
                                        <th class="py-2 px-4 border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="flight in flights.data" :key="flight.id">
                                        <td class="py-2 px-4 border-b">{{ flight.flight_number }}</td>
                                        <td class="py-2 px-4 border-b">{{ flight.airline.name }}</td>
                                        <td class="py-2 px-4 border-b">{{ flight.origin_airport.code }}</td>
                                        <td class="py-2 px-4 border-b">{{ flight.destination_airport.code }}</td>
                                        <td class="py-2 px-4 border-b">{{ formatDateTime(flight.departure_at) }}</td>
                                        <td class="py-2 px-4 border-b">{{ formatDateTime(flight.arrival_at) }}</td>
                                        <td class="py-2 px-4 border-b">${{ flight.price }}</td>
                                        <td class="py-2 px-4 border-b">{{ flight.available_seats }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <Link :href="route('admin.flights.edit', flight.id)" class="text-blue-500 hover:text-blue-700 mr-2">
                                                Edit
                                            </Link>
                                            <button @click="deleteFlight(flight.id)" class="text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="flights.data.length === 0">
                                        <td colspan="9" class="py-4 px-4 border-b text-center">No flights found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="flights.links" @page-click="fetchFlights" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>