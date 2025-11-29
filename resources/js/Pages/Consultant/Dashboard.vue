<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DocumentTextIcon, ClockIcon, CheckCircleIcon, UserGroupIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    stats: Object,
    recentApplications: Array,
    permissions: Object,
    agency: Object,
    teamMember: Object,
})

const getRoleBadgeColor = (role) => {
    const colors = {
        manager: 'bg-purple-100 text-purple-800',
        consultant: 'bg-blue-100 text-blue-800',
        support: 'bg-gray-100 text-gray-800',
    }
    return colors[role] || colors.consultant
}

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <Head title="Consultant Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Consultant Portal</h1>
                            <p class="mt-1 text-sm text-gray-600">{{ agency.name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="getRoleBadgeColor(stats.my_role)" class="px-3 py-1 text-xs font-semibold rounded-full capitalize">
                                {{ stats.my_role }}
                            </span>
                            <span v-if="teamMember?.status === 'active'" class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <!-- Total Applications -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <DocumentTextIcon class="h-5 w-5 text-indigo-600" />
                            </div>
                            <div class="ml-3 min-w-0">
                                <p class="text-xs text-gray-600 truncate">Total Applications</p>
                                <p class="text-xl font-semibold text-gray-900">{{ stats.total_applications }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <ClockIcon class="h-5 w-5 text-yellow-600" />
                            </div>
                            <div class="ml-3 min-w-0">
                                <p class="text-xs text-gray-600 truncate">Pending</p>
                                <p class="text-xl font-semibold text-gray-900">{{ stats.pending_applications }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <CheckCircleIcon class="h-5 w-5 text-green-600" />
                            </div>
                            <div class="ml-3 min-w-0">
                                <p class="text-xs text-gray-600 truncate">Completed</p>
                                <p class="text-xl font-semibold text-gray-900">{{ stats.completed_applications }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Info -->
                <div v-if="permissions" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <ShieldCheckIcon class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-blue-900 mb-2">Your Permissions</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-blue-800">
                                <div class="flex items-center gap-2">
                                    <span v-if="permissions.can_view_applications" class="text-green-600">✓</span>
                                    <span v-else class="text-red-600">✗</span>
                                    View Applications
                                </div>
                                <div class="flex items-center gap-2">
                                    <span v-if="permissions.can_submit_quotes" class="text-green-600">✓</span>
                                    <span v-else class="text-red-600">✗</span>
                                    Submit Quotes
                                </div>
                                <div class="flex items-center gap-2">
                                    <span v-if="permissions.can_view_financials" class="text-green-600">✓</span>
                                    <span v-else class="text-red-600">✗</span>
                                    View Financials
                                </div>
                                <div class="flex items-center gap-2">
                                    <span v-if="permissions.can_manage_team" class="text-green-600">✓</span>
                                    <span v-else class="text-red-600">✗</span>
                                    Manage Team
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 sm:p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
                    </div>
                    
                    <div v-if="recentApplications.length === 0" class="p-8 text-center">
                        <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No applications yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Applications will appear here once clients submit them.</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Service</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="application in recentApplications" :key="application.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ application.id }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ application.user?.name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 hidden sm:table-cell">
                                        {{ application.service_module?.name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span :class="getStatusColor(application.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ application.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 hidden sm:table-cell">
                                        {{ new Date(application.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                        <Link 
                                            :href="route('agency.applications.show', application.id)"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
