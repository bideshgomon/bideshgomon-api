<template>
    <AuthenticatedLayout>
        <Head title="My Visa Applications" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Visa Applications</h1>
                        <p class="mt-2 text-gray-600">Track and manage your visa applications</p>
                    </div>
                    <Link
                        :href="route('visa.index')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"
                    >
                        New Application
                    </Link>
                </div>

                <!-- Applications List -->
                <div v-if="applications.data.length > 0" class="space-y-4">
                    <div
                        v-for="application in applications.data"
                        :key="application.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="text-2xl mr-3">🇺🇸</span>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ application.destination_country }} - {{ application.visa_type }}
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ application.application_reference }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div>
                                            <div class="text-xs text-gray-600 mb-1">Status</div>
                                            <span :class="getStatusClass(application.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ formatStatus(application.status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600 mb-1">Payment</div>
                                            <span :class="application.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'" class="text-sm font-semibold">
                                                {{ application.payment_status === 'paid' ? 'Paid' : 'Pending' }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600 mb-1">Total Amount</div>
                                            <div class="text-sm font-semibold text-gray-900">৳{{ application.total_amount.toLocaleString() }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600 mb-1">Submitted</div>
                                            <div class="text-sm text-gray-900">{{ formatDate(application.submitted_at) }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center space-x-3">
                                        <Link
                                            :href="route('visa.show', application.id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200"
                                        >
                                            View Details
                                        </Link>
                                        <Link
                                            v-if="application.payment_status === 'pending'"
                                            :href="route('visa.payment', application.id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"
                                        >
                                            Make Payment
                                        </Link>
                                    </div>
                                </div>

                                <div class="ml-4">
                                    <div v-if="application.documents_count" class="text-center">
                                        <div class="text-2xl font-bold text-indigo-600">{{ application.documents_count }}</div>
                                        <div class="text-xs text-gray-600">Documents</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No applications yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first visa application.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('visa.index')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"
                        >
                            Browse Visa Services
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="applications.data.length > 0" class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ applications.from }} to {{ applications.to }} of {{ applications.total }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            <Link
                                v-for="link in applications.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 text-sm font-medium rounded-lg',
                                    link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    applications: Object,
});

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        submitted: 'bg-blue-100 text-blue-800',
        under_review: 'bg-yellow-100 text-yellow-800',
        documents_requested: 'bg-orange-100 text-orange-800',
        documents_received: 'bg-purple-100 text-purple-800',
        interview_scheduled: 'bg-indigo-100 text-indigo-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatStatus = (status) => {
    return (status || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>
