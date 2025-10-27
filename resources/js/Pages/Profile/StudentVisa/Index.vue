<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';

const applications = ref({ data: [], links: [], total: 0, current_page: 1 });
const loading = ref(false);

const fetchApplications = async (page = 1) => {
    loading.value = true;
    try {
        // Use the correct API route name for user's applications
        const response = await axios.get(route('api.student-visa-applications.index'), {
            params: { page: page }
        });
        applications.value = response.data;
    } catch (error) {
        console.error("Error fetching student visa applications:", error);
        // Add user-friendly error notification
    } finally {
        loading.value = false;
    }
};

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
    <Head title="My Student Visa Applications" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Student Visa Applications</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-end">
                            <Link :href="route('profile.student-visa.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Apply for Student Visa
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                           <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">University</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="loading"><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Loading...</td></tr>
                                    <tr v-else-if="applicationList.length === 0"><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No applications found. Start by applying for a student visa.</td></tr>
                                    <tr v-else v-for="app in applicationList" :key="app.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(app.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ app.destination_country?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.university?.name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.course?.name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ app.status.replace('_', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('profile.student-visa.show', app.id)" class="text-indigo-600 hover:text-indigo-900">View Details</Link>
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
    </AuthenticatedLayout>
</template>