<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    resources: Object,
    filters: Object,
});

const statusFilter = ref(props.filters?.status || '');
const resourceTypeFilter = ref(props.filters?.resource_type || '');
const searchQuery = ref(props.filters?.search || '');

const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showDeleteModal = ref(false);
const selectedResource = ref(null);

const approveForm = useForm({
    admin_notes: '',
});

const rejectForm = useForm({
    admin_notes: '',
});

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
        search: searchQuery.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    statusFilter.value = '';
    resourceTypeFilter.value = '';
    searchQuery.value = '';
    router.get(route('admin.agency-resources.index'));
};

const openApproveModal = (resource) => {
    selectedResource.value = resource;
    approveForm.admin_notes = '';
    showApproveModal.value = true;
};

const openRejectModal = (resource) => {
    selectedResource.value = resource;
    rejectForm.admin_notes = '';
    showRejectModal.value = true;
};

const openDeleteModal = (resource) => {
    selectedResource.value = resource;
    showDeleteModal.value = true;
};

const closeModals = () => {
    showApproveModal.value = false;
    showRejectModal.value = false;
    showDeleteModal.value = false;
    selectedResource.value = null;
};

const submitApproval = () => {
    approveForm.post(route('admin.agency-resources.approve', selectedResource.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModals();
        },
    });
};

const submitRejection = () => {
    rejectForm.post(route('admin.agency-resources.reject', selectedResource.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModals();
        },
    });
};

const confirmDelete = () => {
    router.delete(route('admin.agency-resources.destroy', selectedResource.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModals();
        },
    });
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
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Resource
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input
                                v-model="searchQuery"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="Resource name, agency..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select
                                v-model="statusFilter"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending Approval</option>
                                <option value="approved">Approved</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resource Type</label>
                            <select
                                v-model="resourceTypeFilter"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Types</option>
                                <option v-for="(label, value) in resourceTypeLabels" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button
                                @click="applyFilters"
                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition"
                            >
                                Apply
                            </button>
                            <button
                                @click="clearFilters"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"
                            >
                                Clear
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            Primary Owner
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ resource.special_commission_rate || '—' }}%</div>
                                    <div v-if="!resource.special_commission_rate" class="text-xs text-gray-500">Uses default rate</div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <button
                                            v-if="!resource.is_approved"
                                            @click="openApproveModal(resource)"
                                            class="text-green-600 hover:text-green-900 font-medium"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            v-if="!resource.is_approved"
                                            @click="openRejectModal(resource)"
                                            class="text-red-600 hover:text-red-900 font-medium"
                                        >
                                            Reject
                                        </button>
                                        <Link
                                            :href="route('admin.agency-resources.edit', resource.id)"
                                            class="text-gray-600 hover:text-gray-900 font-medium transition"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="openDeleteModal(resource)"
                                            class="text-red-600 hover:text-red-900 font-medium transition"
                                        >
                                            Delete
                                        </button>
                                    </div>
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

                <!-- Approve Modal -->
                <Modal :show="showApproveModal" @close="closeModals">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Resource Assignment</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Approve <span class="font-medium">{{ selectedResource?.resource_name }}</span> for 
                            <span class="font-medium">{{ selectedResource?.agency?.name }}</span>?
                        </p>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                            <textarea
                                v-model="approveForm.admin_notes"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Add any notes about this approval..."
                            ></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <SecondaryButton @click="closeModals">Cancel</SecondaryButton>
                            <PrimaryButton @click="submitApproval" :disabled="approveForm.processing">Approve</PrimaryButton>
                        </div>
                    </div>
                </Modal>

                <!-- Reject Modal -->
                <Modal :show="showRejectModal" @close="closeModals">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Resource Assignment</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Reject <span class="font-medium">{{ selectedResource?.resource_name }}</span> for 
                            <span class="font-medium">{{ selectedResource?.agency?.name }}</span>?
                        </p>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                            <textarea
                                v-model="rejectForm.admin_notes"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Explain why this resource is being rejected..."
                                required
                            ></textarea>
                            <p v-if="rejectForm.errors.admin_notes" class="mt-1 text-sm text-red-600">{{ rejectForm.errors.admin_notes }}</p>
                        </div>
                        <div class="flex justify-end gap-3">
                            <SecondaryButton @click="closeModals">Cancel</SecondaryButton>
                            <DangerButton @click="submitRejection" :disabled="rejectForm.processing || !rejectForm.admin_notes">Reject</DangerButton>
                        </div>
                    </div>
                </Modal>

                <!-- Delete Confirmation Modal -->
                <Modal :show="showDeleteModal" @close="closeModals">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Resource Assignment</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to delete <span class="font-medium">{{ selectedResource?.resource_name }}</span>? 
                            This action cannot be undone.
                        </p>
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                            <p class="text-sm text-red-800">
                                ⚠️ Warning: This will permanently remove the resource assignment.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3">
                            <SecondaryButton @click="closeModals">Cancel</SecondaryButton>
                            <DangerButton @click="confirmDelete">Delete Permanently</DangerButton>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </AdminLayout>
</template>
