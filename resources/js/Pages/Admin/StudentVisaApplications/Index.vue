<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce'; // Import debounce

const props = defineProps({
    users: Array, countries: Array, universities: Array, agencies: Array, statuses: Array,
});
const applications = ref({ data: [], links: [], meta: {} }); // Use meta for pagination details
const loading = ref(false);
const filters = ref({
    user_id: '',
    destination_country_id: '',
    university_id: '',
    agency_id: '',
    status: '',
    search: '',
});

const fetchApplications = async (page = 1) => {
    loading.value = true;
    try {
        let params = { page: page };
        // Add filters to params only if they have a value
        for (const key in filters.value) {
            if (filters.value[key]) {
                params[key] = filters.value[key];
            }
        }

        // Use correct Admin API route name
        const response = await axios.get(route('api.admin.student-visa-applications.index'), { params });
        applications.value = response.data;
    } catch (error) {
        console.error("Error fetching admin student visa applications:", error);
        // Add admin-specific error notification
    } finally {
        loading.value = false;
    }
};

// Debounce the fetch function for search input
const debouncedFetch = debounce(() => {
    fetchApplications(1); // Reset to page 1 on search/filter change
}, 500); // 500ms delay

// Watch filters (except search) and trigger immediate fetch
watch(() => [filters.value.user_id, filters.value.destination_country_id, filters.value.university_id, filters.value.agency_id, filters.value.status], () => {
    fetchApplications(1);
});
// Watch search separately with debounce
watch(() => filters.value.search, debouncedFetch);


const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(fetchApplications);
const applicationList = computed(() => applications.value.data);

</script>

<template>
    <Head title="Admin - Student Visa Applications" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Student Visa Applications</h2>
        </template>
        <div class="py-12">
            <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8"> {/* Wider container */}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6 p-4 border rounded-lg bg-gray-50 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-center">
                            <div class="lg:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input id="search" type="text" v-model.lazy="filters.search" placeholder="Ref, Name, Email..." class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="user" class="block text-sm font-medium text-gray-700 mb-1">Applicant</label>
                                <select id="user" v-model="filters.user_id" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Users</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                           <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <select id="country" v-model="filters.destination_country_id" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Countries</option>
                                    <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                           </div>
                           <div>
                                <label for="university" class="block text-sm font-medium text-gray-700 mb-1">University</label>
                                <select id="university" v-model="filters.university_id" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Universities</option>
                                    <option v-for="u in universities" :key="u.id" :value="u.id">{{ u.name }}</option>
                                </select>
                            </div>
                           <div>
                                <label for="agency" class="block text-sm font-medium text-gray-700 mb-1">Agency</label>
                                <select id="agency" v-model="filters.agency_id" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Any Agency</option>
                                    <option value="null">Unassigned</option>
                                    <option v-for="a in agencies" :key="a.id" :value="a.id">{{ a.name }}</option>
                                </select>
                           </div>
                           <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" v-model="filters.status" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm capitalize">
                                    <option value="">All Statuses</option>
                                    <option v-for="s in statuses" :key="s" :value="s">{{ s.replace('_', ' ') }}</option>
                                </select>
                           </div>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                           <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">University</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="loading"><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Loading applications...</td></tr>
                                    <tr v-else-if="applicationList.length === 0"><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No applications match the current filters.</td></tr>
                                    <tr v-else v-for="app in applicationList" :key="app.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(app.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ app.user?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.destination_country?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.university?.name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.agency?.name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ app.status.replace('_', ' ') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.student-visa-applications.show', app.id)" class="text-indigo-600 hover:text-indigo-900">Manage</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                         <Pagination class="mt-6" :links="applications.links" :meta="applications.meta" @page-click="fetchApplications"/>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>