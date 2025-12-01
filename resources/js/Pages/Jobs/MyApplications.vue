<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BriefcaseIcon,
    MapPinIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    ClockIcon,
    EyeIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    applications: Object,
    stats: Object,
});

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'under_review': 'bg-blue-100 text-blue-800',
        'shortlisted': 'bg-indigo-100 text-indigo-800',
        'interviewed': 'bg-purple-100 text-purple-800',
        'offered': 'bg-cyan-100 text-cyan-800',
        'accepted': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'withdrawn': 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pending',
        'under_review': 'Under Review',
        'shortlisted': 'Shortlisted',
        'interviewed': 'Interviewed',
        'offered': 'Offered',
        'accepted': 'Accepted',
        'rejected': 'Rejected',
        'withdrawn': 'Withdrawn',
    };
    return labels[status] || ((status || '').charAt(0).toUpperCase() || '') + (status || '').slice(1);
};
</script>

<template>
    <Head title="My Applications" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="bg-blue-50 border-b border-gray-200 px-4 py-8 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-blue-900">My Applications</h1>
                        <p class="text-gray-600 text-sm mt-2">Track your job applications and status</p>
                    </div>
                    <Link
                        :href="route('jobs.index')"
                        class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg font-medium"
                    >
                        <BriefcaseIcon class="h-5 w-5" />
                        <span class="hidden sm:inline">Browse Jobs</span>
                    </Link>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="text-gray-500 text-xs font-medium mb-1">Total</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="text-gray-500 text-xs font-medium mb-1">Pending</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="text-gray-500 text-xs font-medium mb-1">Under Review</div>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.under_review }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="text-gray-500 text-xs font-medium mb-1">Shortlisted</div>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.shortlisted }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="text-gray-500 text-xs font-medium mb-1">Rejected</div>
                        <div class="text-2xl font-bold text-red-600">{{ stats.rejected }}</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="text-gray-500 text-xs font-medium mb-1">Accepted</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.accepted }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- No Applications -->
            <div v-if="applications.data.length === 0" class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-blue-100 flex items-center justify-center border-2 border-blue-200">
                    <BriefcaseIcon class="h-14 w-14 text-blue-600" />
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No applications yet</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Start applying for jobs to track them here and manage your career opportunities</p>
                <Link
                    :href="route('jobs.index')"
                    class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl"
                >
                    <BriefcaseIcon class="h-5 w-5 mr-2" />
                    Browse Jobs
                </Link>
            </div>

            <!-- Applications List -->
            <div v-else class="space-y-6">
                <div
                    v-for="application in applications.data"
                    :key="application.id"
                    class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-200"
                >
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6">
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                                            {{ application.job_posting.title }}
                                        </h3>
                                        <p class="text-sm font-medium text-gray-600 mb-3">
                                            {{ application.job_posting.company_name }}
                                        </p>
                                    </div>
                                    <span :class="['ml-4 px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap', getStatusColor(application.status)]">
                                        {{ getStatusLabel(application.status) }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-4 text-sm text-gray-500 mt-3">
                                    <div class="flex items-center">
                                        <MapPinIcon class="h-4 w-4 mr-1" />
                                        {{ application.job_posting.city }}, {{ application.job_posting.country.name }}
                                    </div>
                                    <div class="flex items-center">
                                        <CalendarIcon class="h-4 w-4 mr-1" />
                                        Applied {{ new Date(application.submitted_at).toLocaleDateString() }}
                                    </div>
                                    <div v-if="application.application_fee_paid > 0" class="flex items-center">
                                        <CurrencyDollarIcon class="h-4 w-4 mr-1" />
                                        Fee: ৳{{ Number(application.application_fee_paid).toLocaleString() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cover Letter Preview -->
                        <div v-if="application.cover_letter" class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-start">
                                <DocumentTextIcon class="h-5 w-5 text-gray-400 mt-0.5 mr-2 flex-shrink-0" />
                                <div class="flex-1">
                                    <div class="text-xs font-medium text-gray-500 mb-1">Cover Letter</div>
                                    <p class="text-sm text-gray-700 line-clamp-2">
                                        {{ application.cover_letter }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="flex items-center space-x-2 text-xs text-gray-500 mb-4">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-1"></div>
                                <span>Applied: {{ new Date(application.submitted_at).toLocaleDateString() }}</span>
                            </div>
                            <div v-if="application.reviewed_at" class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-1"></div>
                                <span>Reviewed: {{ new Date(application.reviewed_at).toLocaleDateString() }}</span>
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div v-if="application.admin_notes" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="text-xs font-medium text-blue-700 mb-1">Note from Employer</div>
                            <p class="text-sm text-blue-900">{{ application.admin_notes }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                            <Link
                                :href="route('jobs.show', application.job_posting.id)"
                                class="flex-1 flex items-center justify-center space-x-2 px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-md hover:shadow-lg"
                            >
                                <EyeIcon class="h-5 w-5" />
                                <span>View Job Details</span>
                            </Link>
                            
                            <div v-if="application.status === 'accepted'" class="flex-1">
                                <div class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg font-medium">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Congratulations! You're Hired</span>
                                </div>
                            </div>
                            
                            <div v-else-if="application.status === 'pending'" class="flex-1">
                                <div class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg font-medium">
                                    <ClockIcon class="h-5 w-5" />
                                    <span>Awaiting Review</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div v-if="application.payment_status === 'paid'" class="mt-3 flex items-center text-xs text-green-600">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Payment Confirmed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="applications.data.length > 0" class="mt-8 flex items-center justify-between border-t border-gray-200 pt-4">
                <div class="text-sm text-gray-700">
                    Showing {{ applications.from }} to {{ applications.to }} of {{ applications.total }} applications
                </div>
                <div class="flex space-x-2">
                    <component
                        :is="link.url ? Link : 'span'"
                        v-for="link in applications.links"
                        :key="link.label"
                        :href="link.url || undefined"
                        :class="[
                            'px-3 py-2 text-sm rounded-lg',
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : link.url
                                ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
