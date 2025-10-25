<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash';

const props = defineProps({
    airlines: Object,
    filters: Object,
});

const search = ref(props.filters.search);

const fetchAirlines = (url) => {
    router.get(url, { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, debounce((value) => {
    router.get(route('admin.airlines.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteAirline = (id) => {
    if (confirm('Are you sure you want to delete this airline?')) {
        router.delete(route('admin.api.airlines.destroy', id), {
            onSuccess: () => fetchAirlines(props.airlines.path),
        });
    }
};
</script>

<template>
    <Head title="Manage Airlines" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Manage Airlines</h1>
                            <Link :href="route('admin.airlines.create')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Add Airline
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
                                        <th class="py-2 px-4 border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="airline in airlines.data" :key="airline.id">
                                        <td class="py-2 px-4 border-b">{{ airline.name }}</td>
                                        <td class="py-2 px-4 border-b">{{ airline.code }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <Link :href="route('admin.airlines.edit', airline.id)" class="text-blue-500 hover:text-blue-700 mr-2">
                                                Edit
                                            </Link>
                                            <button @click="deleteAirline(airline.id)" class="text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="airlines.data.length === 0">
                                        <td colspan="3" class="py-4 px-4 border-b text-center">No airlines found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="airlines.links" @page-click="fetchAirlines" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>