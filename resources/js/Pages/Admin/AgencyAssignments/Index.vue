<template>
    <AdminLayout>
        <Head title="Agency Country Assignments" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Agency Country Assignments</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Manage which agencies handle which countries and set platform commission rates
                            </p>
                        </div>
                        <Link
                            :href="route('admin.agency-assignments.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Assign Agency to Country
                        </Link>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Assignments</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_assignments }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Assignments</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.active_assignments }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Agencies</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_agencies }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Platform Earnings</p>
                                <p class="text-2xl font-semibold text-gray-900">৳{{ formatNumber(stats.platform_earnings) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Agency</label>
                            <select
                                v-model="filters.agency_id"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Agencies</option>
                                <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                    {{ agency.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <input
                                v-model="filters.country"
                                @input="applyFilters"
                                type="text"
                                placeholder="Search by country..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select
                                v-model="filters.is_active"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Assignments Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Agency
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Country & Visa Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Commission
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Performance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="assignment in assignments.data" :key="assignment.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">
                                                {{ assignment.agency.name.substring(0, 2).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ assignment.agency.name }}</div>
                                            <div class="text-sm text-gray-500">{{ assignment.agency.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ assignment.country }}</div>
                                    <div class="text-sm text-gray-500">{{ assignment.visa_type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span v-if="assignment.commission_type === 'percentage'">
                                            {{ assignment.platform_commission_rate }}%
                                        </span>
                                        <span v-else>
                                            ৳{{ formatNumber(assignment.fixed_commission_amount) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ assignment.commission_type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ assignment.total_applications }} applications</div>
                                    <div class="text-xs text-gray-500">
                                        {{ assignment.approval_rate ? assignment.approval_rate.toFixed(1) : '0' }}% approval rate
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800': assignment.is_active,
                                            'bg-gray-100 text-gray-800': !assignment.is_active
                                        }"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ assignment.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.agency-assignments.show', assignment.id)"
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                    >
                                        View
                                    </Link>
                                    <button
                                        @click="toggleActive(assignment)"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3"
                                    >
                                        {{ assignment.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button
                                        @click="confirmDelete(assignment)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="assignments.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ assignments.from }}</span>
                                to
                                <span class="font-medium">{{ assignments.to }}</span>
                                of
                                <span class="font-medium">{{ assignments.total }}</span>
                                results
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in assignments.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="{
                                        'bg-blue-600 text-white': link.active,
                                        'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                    class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No assignments found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by assigning an agency to a country.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.agency-assignments.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Assign Agency
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
    </div>
  </AdminLayout>
</template><script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    assignments: Object,
    stats: Object,
    agencies: Array,
    filters: Object,
});

const filters = ref({
    agency_id: props.filters?.agency_id || '',
    country: props.filters?.country || '',
    is_active: props.filters?.is_active || '',
});

const applyFilters = () => {
    router.get(route('admin.agency-assignments.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const toggleActive = (assignment) => {
    if (confirm(`Are you sure you want to ${assignment.is_active ? 'deactivate' : 'activate'} this assignment?`)) {
        router.post(route('admin.agency-assignments.toggle-active', assignment.id), {}, {
            preserveScroll: true,
        });
    }
};

const confirmDelete = (assignment) => {
    if (confirm(`Are you sure you want to delete the assignment for ${assignment.agency.name} - ${assignment.country}? This will unassign all related visa requirements.`)) {
        router.delete(route('admin.agency-assignments.destroy', assignment.id));
    }
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-BD').format(num || 0);
};
</script>
