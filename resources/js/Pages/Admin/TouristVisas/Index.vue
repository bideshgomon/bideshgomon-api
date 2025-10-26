<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue'; // Use standard component
import { Head, Link } from '@inertiajs/vue3'; // Removed router as axios handles navigation/data loading
import { ref, watch, onMounted } from 'vue';
import axios from 'axios'; // Import axios
import debounce from 'lodash/debounce';

const props = defineProps({
    filters: Object, // To receive initial filter values if passed
});

// Reactive state
const visas = ref({ data: [], links: [] });
const isLoading = ref(true); // Start loading initially
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

// Status options for the filter dropdown
const statusOptions = ref([
    { value: '', label: 'All Statuses' },
    { value: 'pending', label: 'Pending' },
    { value: 'submitted', label: 'Submitted' },
    { value: 'processing', label: 'Processing' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'cancelled', label: 'Cancelled' },
]);

// Fetch data function (Handles pagination via URL)
const fetchVisas = async (url = route('admin.api.tourist-visas.index')) => {
    isLoading.value = true;
    try {
        const response = await axios.get(url, {
            params: { // Axios automatically handles query parameters including page
                search: search.value || undefined, // Send undefined if empty
                status: statusFilter.value || undefined, // Send undefined if empty
            },
            headers: { 'Accept': 'application/json' }
        });
        visas.value = response.data;
    } catch (error) {
        console.error("Error fetching tourist visas:", error);
        // Handle error display if needed
    } finally {
        isLoading.value = false;
    }
};

// Watchers for filters (debounced)
// Fetch page 1 when filters change
watch([search, statusFilter], debounce(() => fetchVisas(), 300));

// Fetch initial data on mount
onMounted(() => fetchVisas());

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    try {
        return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) {
        return dateString; // Fallback
    }
};

// Helper for status badge class
const statusClass = (status) => {
    switch (status) {
        case 'pending':
        case 'submitted':
        case 'processing':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'approved':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'rejected':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'cancelled':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

</script>

<template>
    <Head title="Tourist Visa Applications" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Tourist Visa Applications
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Manage and review submitted tourist visa applications.
                    </p>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <TextInput
                        type="text"
                        v-model="search"
                        placeholder="Search by Applicant Name or Country..."
                        class="w-full"
                    />
                    <select
                        v-model="statusFilter"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    >
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div v-if="isLoading" class="text-center py-10">
                     <svg class="animate-spin h-8 w-8 text-brand-primary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Loading applications...</p>
                </div>

                <div v-else class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Applicant</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Destination</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Travel Date</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Applied On</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="visas.data.length === 0">
                                        <td colspan="6" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No applications found matching your criteria.
                                        </td>
                                    </tr>
                                    <tr v-for="visa in visas.data" :key="visa.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ visa.user?.name || 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ visa.destination_country?.name || 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(visa.intended_travel_date) }}</td>
                                        <td class="px-3 py-4 text-sm whitespace-nowrap">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize" :class="statusClass(visa.status)">
                                                {{ visa.status?.replace('_', ' ') || 'Unknown' }}
                                             </span>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(visa.created_at) }}</td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 whitespace-nowrap">
                                             <Link :href="route('admin.tourist-visas.show', visa.id)" class="text-brand-primary hover:text-opacity-80">
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                 <Pagination class="mt-6" :links="visas.links" @page-click="url => fetchVisas(url)" />
            </section>
        </div>
    </AdminLayout>
</template>