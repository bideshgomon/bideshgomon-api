<template>
    <AdminLayout>
        <Head title="Agency Verification Requests" />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Agency Verification Requests</h1>
                <p class="mt-2 text-gray-600">Review and manage agency verification submissions.</p>
            </div>

            <!-- Filters -->
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select
                            id="status"
                            v-model="filterForm.status"
                            @change="applyFilters"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="requires_changes">Requires Changes</option>
                        </select>
                    </div>

                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Agency</label>
                        <input
                            type="text"
                            id="search"
                            v-model="filterForm.search"
                            @input="applyFilters"
                            placeholder="Agency name..."
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed By</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="request in requests.data" :key="request.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ request.agency.name }}</div>
                                    <div class="text-sm text-gray-500">{{ request.agency.company_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        request.status === 'approved' ? 'bg-green-100 text-green-800' :
                                        request.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                        request.status === 'under_review' ? 'bg-blue-100 text-blue-800' :
                                        request.status === 'requires_changes' ? 'bg-orange-100 text-orange-800' :
                                        'bg-yellow-100 text-yellow-800'
                                    ]"
                                >
                                    {{ request.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(request.submitted_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ request.reviewer ? request.reviewer.name : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="route('admin.agency-verification.show', request.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Review
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div v-if="requests.data.length === 0" class="text-center py-12">
                    <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No verification requests</h3>
                    <p class="mt-1 text-sm text-gray-500">No requests match your current filters.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="requests.data.length > 0" class="mt-6">
                <Pagination :links="requests.links" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { DocumentIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    requests: Object,
    filters: Object,
});

const filterForm = ref({
    status: props.filters.status || '',
    search: props.filters.search || '',
});

const applyFilters = () => {
    router.get(route('admin.agency-verification.index'), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.value = { status: '', search: '' };
    applyFilters();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>
