<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    resources: Object,
    filters: Object,
});

const statusFilter = ref(props.filters?.status || '');
const resourceTypeFilter = ref(props.filters?.resource_type || '');

const resourceTypeLabels = {
    university: 'University',
    school: 'School',
    language_center: 'Language Center',
    training_institute: 'Training Institute',
    job_portal: 'Job Portal',
    other: 'Other',
};

const applyFilters = () => {
    router.get(route('admin.agency-resources.index'), {
        status: statusFilter.value,
        resource_type: resourceTypeFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    statusFilter.value = '';
    resourceTypeFilter.value = '';
    router.get(route('admin.agency-resources.index'));
};

const approveResource = (resourceId) => {
    if (confirm('Approve this resource assignment?')) {
        router.post(route('admin.agency-resources.approve', resourceId), {
            admin_notes: 'Approved by admin',
        });
    }
};

const rejectResource = (resourceId) => {
    const notes = prompt('Reason for rejection:');
    if (notes) {
        router.post(route('admin.agency-resources.reject', resourceId), {
            admin_notes: notes,
        });
    }
};

const deleteResource = (resourceId) => {
    if (confirm('Delete this resource? This cannot be undone.')) {
        router.delete(route('admin.agency-resources.destroy', resourceId));
    }
};

const getStatusBadgeClass = (resource) => {
    if (!resource.is_active) return 'bg-gray-100 text-gray-800';
    if (resource.is_approved) return 'bg-green-100 text-green-800';
    return 'bg-yellow-100 text-yellow-800';
};

const getStatusText = (resource) => {
    if (!resource.is_active) return 'Inactive';
    if (resource.is_approved) return 'Approved';
    return 'Pending';
};
</script>

<template>
    <Head title="Agency Resources" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Agency Resources</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage exclusive resource assignments (universities, schools, etc.)</p>
                    </div>
                    <Link
                        :href="route('admin.agency-resources.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Resource
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="statusFilter"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending Approval</option>
                                <option value="approved">Approved</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resource Type</label>
                            <select
                                v-model="resourceTypeFilter"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Types</option>
                                <option v-for="(label, value) in resourceTypeLabels" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="clearFilters"
                                class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Resources Table -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="resource in resources.data" :key="resource.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ resource.resource_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ resourceTypeLabels[resource.resource_type] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ resource.agency?.name }}</div>
                                    <div class="text-sm text-gray-500">{{ resource.agency?.email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ resource.service_module?.name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ resource.country?.name }}</div>
                                    <div v-if="resource.city" class="text-sm text-gray-500">{{ resource.city }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="getStatusBadgeClass(resource)"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ getStatusText(resource) }}
                                    </span>
                                    <div v-if="resource.is_primary_owner" class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            Primary Owner
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ resource.special_commission_rate || resource.service_module?.platform_commission_rate }}%
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    <button
                                        v-if="!resource.is_approved"
                                        @click="approveResource(resource.id)"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        v-if="!resource.is_approved"
                                        @click="rejectResource(resource.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Reject
                                    </button>
                                    <Link
                                        :href="route('admin.agency-resources.edit', resource.id)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteResource(resource.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="resources.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No resources found. Start by adding an exclusive resource assignment.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="resources.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ resources.from }} to {{ resources.to }} of {{ resources.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-if="resources.prev_page_url"
                                    :href="resources.prev_page_url"
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="resources.next_page_url"
                                    :href="resources.next_page_url"
                                    class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
