<template>
    <Head :title="`${assignment.agency.name} - ${assignment.country}`" />

    <AdminLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ assignment.agency.name }} - {{ assignment.country }}
                                </h1>
                                <span 
                                    :class="[
                                        'px-3 py-1 text-sm font-medium rounded-full',
                                        assignment.is_active 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ assignment.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="mt-2 text-gray-600">{{ assignment.visa_type }} visa management</p>
                        </div>
                        <div class="flex space-x-3">
                            <button
                                @click="toggleActive"
                                :class="[
                                    'px-4 py-2 rounded-md text-white transition',
                                    assignment.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'
                                ]"
                            >
                                {{ assignment.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <Link 
                                :href="route('admin.agency-assignments.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to List
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Assignment Details -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Assignment Details</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Agency</p>
                                    <p class="font-medium text-gray-900">{{ assignment.agency.name }}</p>
                                    <p class="text-sm text-gray-500">{{ assignment.agency.email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Assigned By</p>
                                    <p class="font-medium text-gray-900">{{ assignment.assigned_by_user?.name || 'System' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Country</p>
                                    <p class="font-medium text-gray-900">{{ assignment.country }} ({{ assignment.country_code }})</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Visa Type</p>
                                    <p class="font-medium text-gray-900">{{ assignment.visa_type }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Assigned Date</p>
                                    <p class="font-medium text-gray-900">{{ formatDate(assignment.assigned_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span 
                                        :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            assignment.is_active 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ assignment.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="assignment.assignment_notes" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Notes:</p>
                                <p class="text-sm text-gray-900">{{ assignment.assignment_notes }}</p>
                            </div>
                        </div>

                        <!-- Commission Settings -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Commission Settings</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Commission Type</p>
                                    <p class="font-medium text-gray-900 capitalize">{{ assignment.commission_type }}</p>
                                </div>
                                <div v-if="assignment.commission_type === 'percentage'">
                                    <p class="text-sm text-gray-600">Commission Rate</p>
                                    <p class="font-medium text-gray-900">{{ assignment.platform_commission_rate }}%</p>
                                </div>
                                <div v-if="assignment.commission_type === 'fixed'">
                                    <p class="text-sm text-gray-600">Fixed Commission</p>
                                    <p class="font-medium text-gray-900">৳{{ formatNumber(assignment.fixed_commission_amount) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Agency Permissions</h2>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg 
                                        :class="[
                                            'w-5 h-5 mr-2',
                                            assignment.can_edit_requirements ? 'text-green-500' : 'text-gray-400'
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Can Edit Requirements</span>
                                </div>
                                <div class="flex items-center">
                                    <svg 
                                        :class="[
                                            'w-5 h-5 mr-2',
                                            assignment.can_set_fees ? 'text-green-500' : 'text-gray-400'
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Can Set Fees</span>
                                </div>
                                <div class="flex items-center">
                                    <svg 
                                        :class="[
                                            'w-5 h-5 mr-2',
                                            assignment.can_process_applications ? 'text-green-500' : 'text-gray-400'
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Can Process Applications</span>
                                </div>
                            </div>
                        </div>

                        <!-- Visa Requirements -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">Assigned Visa Requirements</h2>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    {{ requirements.length }} requirements
                                </span>
                            </div>
                            <div v-if="requirements.length > 0" class="space-y-4">
                                <div 
                                    v-for="requirement in requirements" 
                                    :key="requirement.id"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition"
                                >
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">
                                                {{ requirement.country }} - {{ requirement.visa_type }}
                                            </h3>
                                            <p v-if="requirement.visa_category" class="text-sm text-gray-500 mt-1">
                                                {{ requirement.visa_category }}
                                            </p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm">
                                                <span class="text-gray-600">
                                                    <span class="font-medium">Documents:</span> {{ requirement.documents?.length || 0 }}
                                                </span>
                                                <span class="text-gray-600">
                                                    <span class="font-medium">Professions:</span> {{ requirement.profession_requirements?.length || 0 }}
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            :href="route('admin.visa-requirements.show', requirement.id)"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                        >
                                            View Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                No visa requirements assigned yet
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Performance Stats -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance</h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Applications</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ assignment.total_applications || 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Approved</p>
                                    <p class="text-xl font-bold text-green-600">{{ assignment.approved_applications || 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Rejected</p>
                                    <p class="text-xl font-bold text-red-600">{{ assignment.rejected_applications || 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Pending</p>
                                    <p class="text-xl font-bold text-yellow-600">{{ assignment.pending_applications || 0 }}</p>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-600">Approval Rate</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ assignment.approval_rate ? assignment.approval_rate.toFixed(1) : '0.0' }}%
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Stats -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Revenue</h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Revenue</p>
                                    <p class="text-2xl font-bold text-gray-900">৳{{ formatNumber(assignment.total_revenue || 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Platform Earnings</p>
                                    <p class="text-xl font-bold text-indigo-600">৳{{ formatNumber(assignment.platform_earnings || 0) }}</p>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-600">Agency Earnings</p>
                                    <p class="text-xl font-bold text-green-600">
                                        ৳{{ formatNumber((assignment.total_revenue || 0) - (assignment.platform_earnings || 0)) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                            <div class="space-y-2">
                                <button 
                                    @click="toggleActive"
                                    :class="[
                                        'w-full px-4 py-2 rounded-md text-white transition',
                                        assignment.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'
                                    ]"
                                >
                                    {{ assignment.is_active ? 'Deactivate Assignment' : 'Activate Assignment' }}
                                </button>
                                <button
                                    @click="confirmDelete"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                                >
                                    Delete Assignment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
  </AdminLayout>
</template><script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    assignment: Object,
    requirements: Array,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-BD').format(num || 0);
};

const toggleActive = () => {
    if (confirm(`Are you sure you want to ${props.assignment.is_active ? 'deactivate' : 'activate'} this assignment?`)) {
        router.post(route('admin.agency-assignments.toggle-active', props.assignment.id), {}, {
            preserveScroll: true,
        });
    }
};

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete this assignment? This will unassign all related visa requirements from ${props.assignment.agency.name}.`)) {
        router.delete(route('admin.agency-assignments.destroy', props.assignment.id));
    }
};
</script>
