<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    // Initial filter options passed from controller
    users: Array,
    countries: Array,
    agencies: Array,
    statuses: Array,
});

const applications = ref({ data: [], links: [], total: 0, current_page: 1 });
const loading = ref(false);

// Filters ref
const filters = ref({
    user_id: '',
    destination_country_id: '',
    agency_id: '',
    status: '',
    search: '', // General search term
});

const fetchApplications = async (page = 1) => {
    loading.value = true;
    try {
        // Include filter values in the request params
        let params = { page: page, ...filters.value };
        // Remove empty filters
        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key];
            }
        });

        // ** IMPORTANT: Need an Admin API endpoint for fetching applications **
        // Assuming '/api/admin/work-visa-applications' exists
        const response = await axios.get(route('api.admin.work-visa-applications.index'), { params });
        applications.value = response.data;
    } catch (error) {
        console.error("Error fetching applications:", error);
        // Add user notification
    } finally {
        loading.value = false;
    }
};

// Watch filters and refetch data on change (debounce recommended for search)
watch(filters, () => {
    fetchApplications(1); // Reset to page 1 when filters change
}, { deep: true });

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(() => {
    fetchApplications();
});

const applicationList = computed(() => applications.value.data);

</script>

<template>
    <Head title="Admin - Work Visa Applications" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Work Visa Applications</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border rounded">
                             <input type="text" v-model="filters.search" placeholder="Search ref/name/email..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <select v-model="filters.user_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Users</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <select v-model="filters.destination_country_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Countries</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                            </select>
                             <select v-model="filters.agency_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Any Agency</option>
                                <option value="null">Unassigned</option> <option v-for="agency in agencies" :key="agency.id" :value="agency.id">{{ agency.name }}</option>
                            </select>
                            <select v-model="filters.status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Statuses</option>
                                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="loading">
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Loading...</td>
                                    </tr>
                                     <tr v-else-if="applicationList.length === 0">
                                         <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No applications found.</td>
                                    </tr>
                                    <tr v-else v-for="app in applicationList" :key="app.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(app.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ app.user?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.destination_country?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.agency?.name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ app.status }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.work-visa-applications.show', app.id)" class="text-indigo-600 hover:text-indigo-900">Manage</Link>
                                            </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                         <Pagination class="mt-6" :links="applications.links" @page-click="fetchApplications"/>

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>