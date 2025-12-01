<template>
    <AuthenticatedLayout>
        <Head title="Visa Application Details" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Application Details</h1>
                        <p class="mt-2 text-gray-600">{{ application.application_reference }}</p>
                    </div>
                    <Link
                        :href="route('visa.my-applications')"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300"
                    >
                        Back to Applications
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Status Card -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Application Status</h2>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span :class="getStatusClass(application.status)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                        {{ formatStatus(application.status) }}
                                    </span>
                                    <p class="mt-2 text-sm text-gray-600">Submitted on {{ formatDate(application.submitted_at) }}</p>
                                </div>
                                <div v-if="application.payment_status === 'pending'" class="text-right">
                                    <Link
                                        :href="route('visa.payment', application.id)"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700"
                                    >
                                        Make Payment
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Visa Details -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Visa Details</h2>
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Visa Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatStatus(application.visa_type) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Destination</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.destination_country }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Category</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatStatus(application.visa_category) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Processing Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatStatus(application.processing_type) }} ({{ application.processing_days }} days)</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Duration</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.duration_days }} days</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Travel Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(application.intended_travel_date) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Applicant Information -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Applicant Information</h2>
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.applicant_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.applicant_email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.applicant_phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(application.applicant_dob) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Nationality</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.nationality }}</dd>
                                </div>
                                <div v-if="application.occupation">
                                    <dt class="text-sm font-medium text-gray-600">Occupation</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.occupation }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Passport Information -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Passport Information</h2>
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Passport Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ application.passport_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Issuing Country</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ application.passport_issuing_country }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Issue Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(application.passport_issue_date) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(application.passport_expiry_date) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Documents -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Required Documents</h2>
                            <div v-if="application.documents && application.documents.length > 0" class="space-y-3">
                                <div
                                    v-for="doc in application.documents"
                                    :key="doc.id"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                >
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ formatStatus(doc.document_type) }}</div>
                                            <div class="text-xs text-gray-600">{{ doc.file_name }}</div>
                                        </div>
                                    </div>
                                    <span :class="getDocumentStatusClass(doc.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ formatStatus(doc.status) }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <p class="text-sm text-gray-600">No documents uploaded yet</p>
                                <button
                                    class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700"
                                >
                                    Upload Documents
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Payment Summary -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h2>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Service Fee</dt>
                                    <dd class="text-sm font-medium text-gray-900">৳{{ application.service_fee.toLocaleString() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Government Fee</dt>
                                    <dd class="text-sm font-medium text-gray-900">৳{{ application.government_fee.toLocaleString() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Processing Fee</dt>
                                    <dd class="text-sm font-medium text-gray-900">৳{{ application.processing_fee.toLocaleString() }}</dd>
                                </div>
                                <div class="pt-3 border-t border-gray-200 flex justify-between">
                                    <dt class="text-base font-semibold text-gray-900">Total</dt>
                                    <dd class="text-base font-bold text-green-600">৳{{ application.total_amount.toLocaleString() }}</dd>
                                </div>
                                <div class="pt-2">
                                    <span :class="application.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
                                        {{ application.payment_status === 'paid' ? 'Paid' : 'Payment Pending' }}
                                    </span>
                                </div>
                            </dl>
                        </div>

                        <!-- Appointments -->
                        <div v-if="application.appointments && application.appointments.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointments</h2>
                            <div class="space-y-3">
                                <div
                                    v-for="apt in application.appointments"
                                    :key="apt.id"
                                    class="p-3 bg-blue-50 rounded-lg"
                                >
                                    <div class="text-sm font-medium text-gray-900">{{ formatStatus(apt.appointment_type) }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ formatDate(apt.appointment_date) }}</div>
                                    <div class="text-xs text-gray-600">{{ apt.location }}</div>
                                    <span :class="getAppointmentStatusClass(apt.status)" class="mt-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                        {{ formatStatus(apt.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                            <div class="space-y-2">
                                <button
                                    v-if="application.payment_status === 'paid' && !['approved', 'rejected', 'cancelled'].includes(application.status)"
                                    class="w-full px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300"
                                >
                                    Upload Documents
                                </button>
                                <button
                                    v-if="!['approved', 'rejected', 'cancelled'].includes(application.status)"
                                    @click="cancelApplication"
                                    class="w-full px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100"
                                >
                                    Cancel Application
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    application: Object,
});

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        submitted: 'bg-blue-100 text-blue-800',
        under_review: 'bg-yellow-100 text-yellow-800',
        documents_requested: 'bg-orange-100 text-orange-800',
        documents_received: 'bg-blue-100 text-blue-800',
        interview_scheduled: 'bg-blue-100 text-blue-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getDocumentStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        verified: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        reupload_required: 'bg-orange-100 text-orange-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getAppointmentStatusClass = (status) => {
    const classes = {
        scheduled: 'bg-blue-100 text-blue-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-gray-100 text-gray-800',
        missed: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
        rescheduled: 'bg-yellow-100 text-yellow-800',
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

const cancelApplication = () => {
    if (confirm('Are you sure you want to cancel this application?')) {
        router.post(route('visa.cancel', application.id));
    }
};
</script>
