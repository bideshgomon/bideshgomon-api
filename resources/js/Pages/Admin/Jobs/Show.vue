<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ArrowLeftIcon,
    PencilIcon,
    TrashIcon,
    SparklesIcon,
    MapPinIcon,
    BriefcaseIcon,
    CurrencyDollarIcon,
    CalendarIcon,
    UserGroupIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    EyeIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    job: Object,
    applications: Object,
});

const showApprovalModal = ref(false);

const approvalForm = useForm({
    admin_approved_fee: props.job.agency_posted_fee || props.job.application_fee || 0,
    notes: '',
});

const rejectionForm = useForm({
    rejection_reason: '',
});

const openApprovalModal = () => {
    approvalForm.admin_approved_fee = props.job.agency_posted_fee || props.job.application_fee || 0;
    showApprovalModal.value = true;
};

const approveJob = () => {
    approvalForm.post(route('admin.jobs.approve', props.job.id), {
        onSuccess: () => {
            showApprovalModal.value = false;
            approvalForm.reset();
        },
    });
};

const rejectJob = () => {
    if (confirm('Are you sure you want to reject this job posting?')) {
        rejectionForm.post(route('admin.jobs.reject', props.job.id), {
            onSuccess: () => {
                rejectionForm.reset();
            },
        });
    }
};

const categoryColors = {
    hospitality: 'bg-blue-100 text-blue-800',
    construction: 'bg-orange-100 text-orange-800',
    healthcare: 'bg-green-100 text-green-800',
    it: 'bg-purple-100 text-purple-800',
    manufacturing: 'bg-gray-100 text-gray-800',
    education: 'bg-yellow-100 text-yellow-800',
    retail: 'bg-pink-100 text-pink-800',
    transportation: 'bg-indigo-100 text-indigo-800',
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    reviewed: 'bg-blue-100 text-blue-800',
    shortlisted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    hired: 'bg-purple-100 text-purple-800',
};

const getCategoryColor = (category) => {
    return categoryColors[category?.toLowerCase()] || 'bg-gray-100 text-gray-800';
};

const getStatusColor = (status) => {
    return statusColors[status?.toLowerCase()] || 'bg-gray-100 text-gray-800';
};

const getJobTypeLabel = (type) => {
    const labels = {
        'full-time': 'Full Time',
        'full_time': 'Full Time',
        'part-time': 'Part Time',
        'part_time': 'Part Time',
        'contract': 'Contract',
        'temporary': 'Temporary',
        'seasonal': 'Seasonal',
        'internship': 'Internship',
    };
    return labels[type] || type.replace(/[_-]/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatCurrency = (amount, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'BDT',
        minimumFractionDigits: 0,
    }).format(amount);
};

const toggleFeatured = () => {
    if (confirm(`${props.job.is_featured ? 'Remove from' : 'Add to'} featured jobs?`)) {
        router.post(route('admin.jobs.toggle-featured', props.job.id));
    }
};

const toggleActive = () => {
    if (confirm(`${props.job.is_active ? 'Deactivate' : 'Activate'} this job posting?`)) {
        router.post(route('admin.jobs.toggle-active', props.job.id));
    }
};

const deleteJob = () => {
    if (confirm('Are you sure you want to delete this job posting? This action cannot be undone.')) {
        router.delete(route('admin.jobs.destroy', props.job.id));
    }
};

const isExpired = () => {
    return new Date(props.job.deadline) < new Date();
};
</script>

<template>
    <Head :title="job.title" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link
                        :href="route('admin.jobs.index')"
                        class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4"
                    >
                        <ArrowLeftIcon class="h-5 w-5 mr-2" />
                        Back to Jobs
                    </Link>

                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ job.title }}</h1>
                                    <span
                                        v-if="job.is_featured"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"
                                    >
                                        <SparklesIcon class="h-4 w-4 mr-1" />
                                        Featured
                                    </span>
                                    <span
                                        :class="[
                                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                            job.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                                        ]"
                                    >
                                        {{ job.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span
                                        v-if="isExpired()"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800"
                                    >
                                        Expired
                                    </span>
                                </div>
                                <p class="text-gray-600">{{ job.company_name }}</p>
                                <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-600">
                                    <div class="flex items-center gap-1">
                                        <MapPinIcon class="h-4 w-4" />
                                        {{ job.city }}, {{ job.country?.name }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <BriefcaseIcon class="h-4 w-4" />
                                        {{ getJobTypeLabel(job.job_type) }}
                                    </div>
                                    <span
                                        :class="['px-2 py-1 rounded-full text-xs font-semibold', getCategoryColor(job.category)]"
                                    >
                                        {{ job.category }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button
                                    @click="toggleFeatured"
                                    :class="[
                                        'px-4 py-2 rounded-lg font-semibold text-sm transition-colors',
                                        job.is_featured
                                            ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                    ]"
                                >
                                    <SparklesIcon class="h-4 w-4 inline mr-1" />
                                    {{ job.is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                                <button
                                    @click="toggleActive"
                                    :class="[
                                        'px-4 py-2 rounded-lg font-semibold text-sm transition-colors',
                                        job.is_active
                                            ? 'bg-red-100 text-red-800 hover:bg-red-200'
                                            : 'bg-green-100 text-green-800 hover:bg-green-200',
                                    ]"
                                >
                                    {{ job.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <Link
                                    :href="route('admin.jobs.edit', job.id)"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-sm"
                                >
                                    <PencilIcon class="h-4 w-4 inline mr-1" />
                                    Edit
                                </Link>
                                <button
                                    @click="deleteJob"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors text-sm"
                                >
                                    <TrashIcon class="h-4 w-4 inline mr-1" />
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Job Details -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Description</h2>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ job.description }}</p>

                            <div v-if="job.responsibilities" class="mt-6">
                                <h3 class="text-md font-semibold text-gray-900 mb-3">Responsibilities</h3>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ job.responsibilities }}</p>
                            </div>

                            <div v-if="job.requirements" class="mt-6">
                                <h3 class="text-md font-semibold text-gray-900 mb-3">Requirements</h3>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ job.requirements }}</p>
                            </div>
                        </div>

                        <!-- Skills & Benefits -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <div v-if="job.skills_required?.length" class="mb-6">
                                <h3 class="text-md font-semibold text-gray-900 mb-3">Required Skills</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="(skill, index) in job.skills_required"
                                        :key="index"
                                        class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium"
                                    >
                                        {{ skill }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="job.benefits?.length">
                                <h3 class="text-md font-semibold text-gray-900 mb-3">Benefits</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="(benefit, index) in job.benefits"
                                        :key="index"
                                        class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium"
                                    >
                                        {{ benefit }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Applications -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Applications ({{ applications.total }})
                                </h2>
                                <Link
                                    :href="route('admin.applications.index', { job_id: job.id })"
                                    class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm"
                                >
                                    View All →
                                </Link>
                            </div>

                            <div v-if="applications.data.length" class="space-y-4">
                                <div
                                    v-for="application in applications.data"
                                    :key="application.id"
                                    class="border border-gray-200 rounded-xl p-4 hover:border-indigo-300 transition-colors"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">
                                                {{ application.user?.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ application.user?.email }}</p>
                                            <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <CalendarIcon class="h-4 w-4" />
                                                    Applied {{ formatDate(application.created_at) }}
                                                </div>
                                                <span
                                                    :class="[
                                                        'px-2 py-1 rounded-full text-xs font-semibold',
                                                        getStatusColor(application.status),
                                                    ]"
                                                >
                                                    {{ application.status }}
                                                </span>
                                                <span
                                                    v-if="application.payment_status === 'paid'"
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"
                                                >
                                                    <CheckCircleIcon class="h-3 w-3 mr-1" />
                                                    Paid
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            :href="route('admin.applications.show', application.id)"
                                            class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors font-semibold text-sm"
                                        >
                                            <EyeIcon class="h-4 w-4 inline mr-1" />
                                            View
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-12 text-gray-500">
                                <UserGroupIcon class="h-12 w-12 mx-auto mb-3 text-gray-400" />
                                <p>No applications yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Approval Status & Processing Fee -->
                        <div class="bg-white rounded-2xl shadow-sm p-6 border-2" :class="[
                            job.approval_status === 'approved' ? 'border-green-200' :
                            job.approval_status === 'rejected' ? 'border-red-200' :
                            'border-yellow-200'
                        ]">
                            <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <ExclamationTriangleIcon v-if="job.approval_status === 'pending'" class="h-5 w-5 text-yellow-600" />
                                <CheckCircleIcon v-else-if="job.approval_status === 'approved'" class="h-5 w-5 text-green-600" />
                                <XCircleIcon v-else class="h-5 w-5 text-red-600" />
                                Approval Status
                            </h3>
                            <div class="space-y-4 text-sm">
                                <div>
                                    <span :class="[
                                        'inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold',
                                        job.approval_status === 'approved' ? 'bg-green-100 text-green-800' :
                                        job.approval_status === 'rejected' ? 'bg-red-100 text-red-800' :
                                        'bg-yellow-100 text-yellow-800'
                                    ]">
                                        {{ job.approval_status === 'approved' ? '✓ Approved' : job.approval_status === 'rejected' ? '✗ Rejected' : '⏳ Pending Review' }}
                                    </span>
                                </div>

                                <!-- Fee Breakdown -->
                                <div v-if="job.agency_posted_fee || job.admin_approved_fee" class="pt-4 border-t border-gray-200">
                                    <p class="text-gray-600 font-medium mb-3">Fee Breakdown</p>
                                    
                                    <div v-if="job.agency_posted_fee" class="mb-2">
                                        <p class="text-gray-500 text-xs">Agency Posted Fee</p>
                                        <p class="font-semibold text-gray-900">৳{{ Number(job.agency_posted_fee).toLocaleString() }}</p>
                                    </div>

                                    <div v-if="job.admin_approved_fee" class="mb-2">
                                        <p class="text-gray-500 text-xs">Admin Approved Fee</p>
                                        <p class="font-bold text-indigo-600 text-lg">৳{{ Number(job.admin_approved_fee).toLocaleString() }}</p>
                                    </div>

                                    <div v-if="job.processing_fee && job.processing_fee > 0" class="mt-3 pt-3 border-t border-gray-200 bg-indigo-50 -mx-4 px-4 py-2 rounded">
                                        <p class="text-indigo-700 text-xs font-medium">Processing Fee (Admin Markup)</p>
                                        <p class="font-bold text-indigo-900 text-xl">+৳{{ Number(job.processing_fee).toLocaleString() }}</p>
                                        <p class="text-indigo-600 text-xs mt-1">{{ ((job.processing_fee / job.agency_posted_fee) * 100).toFixed(1) }}% markup</p>
                                    </div>
                                </div>

                                <!-- Approval Actions for Pending Jobs -->
                                <div v-if="job.approval_status === 'pending'" class="pt-4 border-t border-gray-200 space-y-2">
                                    <button
                                        @click="openApprovalModal"
                                        class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors text-sm flex items-center justify-center gap-2"
                                    >
                                        <CheckCircleIcon class="h-5 w-5" />
                                        Approve with Fee
                                    </button>
                                    <button
                                        @click="rejectJob"
                                        class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors text-sm flex items-center justify-center gap-2"
                                    >
                                        <XCircleIcon class="h-5 w-5" />
                                        Reject Posting
                                    </button>
                                </div>

                                <!-- Approval Info for Approved Jobs -->
                                <div v-if="job.approval_status === 'approved' && job.approved_at" class="pt-4 border-t border-gray-200">
                                    <p class="text-gray-500 text-xs">Approved On</p>
                                    <p class="font-semibold text-gray-900">{{ formatDate(job.approved_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Salary & Compensation -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <CurrencyDollarIcon class="h-5 w-5 text-indigo-600" />
                                Salary & Compensation
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Salary Range</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ formatCurrency(job.salary_min, job.salary_currency) }} -
                                        {{ formatCurrency(job.salary_max, job.salary_currency) }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-1">{{ job.salary_period }}</p>
                                </div>
                                <div v-if="job.application_fee">
                                    <p class="text-gray-600">Application Fee</p>
                                    <p class="font-semibold text-gray-900">৳{{ job.application_fee }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Job Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Job Information</h3>
                            <div class="space-y-3 text-sm">
                                <div v-if="job.experience_required">
                                    <p class="text-gray-600">Experience Required</p>
                                    <p class="font-semibold text-gray-900">{{ job.experience_required }}</p>
                                </div>
                                <div v-if="job.education_required">
                                    <p class="text-gray-600">Education Required</p>
                                    <p class="font-semibold text-gray-900">{{ job.education_required }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Gender Preference</p>
                                    <p class="font-semibold text-gray-900 capitalize">{{ job.gender_preference }}</p>
                                </div>
                                <div v-if="job.age_min || job.age_max">
                                    <p class="text-gray-600">Age Range</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ job.age_min || 'Any' }} - {{ job.age_max || 'Any' }} years
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Positions Available</p>
                                    <p class="font-semibold text-gray-900">{{ job.positions_available }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Important Dates -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <CalendarIcon class="h-5 w-5 text-indigo-600" />
                                Important Dates
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Posted On</p>
                                    <p class="font-semibold text-gray-900">{{ formatDate(job.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Application Deadline</p>
                                    <p
                                        :class="[
                                            'font-semibold',
                                            isExpired() ? 'text-red-600' : 'text-gray-900',
                                        ]"
                                    >
                                        {{ formatDate(job.deadline) }}
                                        <span v-if="isExpired()" class="text-xs">(Expired)</span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Last Updated</p>
                                    <p class="font-semibold text-gray-900">{{ formatDate(job.updated_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div v-if="job.contact_email || job.contact_phone" class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Contact Information</h3>
                            <div class="space-y-3 text-sm">
                                <div v-if="job.contact_email">
                                    <p class="text-gray-600">Email</p>
                                    <a
                                        :href="`mailto:${job.contact_email}`"
                                        class="font-semibold text-indigo-600 hover:text-indigo-800"
                                    >
                                        {{ job.contact_email }}
                                    </a>
                                </div>
                                <div v-if="job.contact_phone">
                                    <p class="text-gray-600">Phone</p>
                                    <a
                                        :href="`tel:${job.contact_phone}`"
                                        class="font-semibold text-indigo-600 hover:text-indigo-800"
                                    >
                                        {{ job.contact_phone }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-indigo-600 rounded-lg shadow-sm p-6 text-white">
                            <h3 class="text-md font-semibold mb-4">Statistics</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-indigo-100">Total Applications</span>
                                    <span class="font-bold text-2xl">{{ applications.total }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-indigo-100">Views</span>
                                    <span class="font-bold text-2xl">{{ job.views_count || 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Modal -->
        <div v-if="showApprovalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-green-600 text-white p-6 rounded-t-2xl border-b-2 border-green-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold">Approve Job Posting</h3>
                        <button @click="showApprovalModal = false" class="text-white hover:text-gray-200">
                            <XCircleIcon class="h-6 w-6" />
                        </button>
                    </div>
                    <p class="mt-2 text-green-100">Set the final application fee and approve this job posting</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Fee Information -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Agency Posted Fee:</span>
                            <span class="font-bold text-gray-900">৳{{ Number(job.agency_posted_fee || job.application_fee).toLocaleString() }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Approved Fee (৳)</label>
                            <input
                                v-model="approvalForm.admin_approved_fee"
                                type="number"
                                min="0"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-lg font-semibold"
                                placeholder="Enter final fee amount"
                            />
                            <p v-if="approvalForm.errors.admin_approved_fee" class="mt-1 text-sm text-red-600">{{ approvalForm.errors.admin_approved_fee }}</p>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-indigo-700">Processing Fee (Markup):</span>
                                <span class="font-bold text-indigo-900 text-lg">
                                    +৳{{ Number(Math.max(0, approvalForm.admin_approved_fee - (job.agency_posted_fee || job.application_fee))).toLocaleString() }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ ((Math.max(0, approvalForm.admin_approved_fee - (job.agency_posted_fee || job.application_fee)) / (job.agency_posted_fee || job.application_fee)) * 100).toFixed(1) }}% markup
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea
                            v-model="approvalForm.notes"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Add any notes about this approval..."
                        ></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button
                            @click="showApprovalModal = false"
                            type="button"
                            class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="approveJob"
                            :disabled="approvalForm.processing"
                            class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <CheckCircleIcon v-if="!approvalForm.processing" class="h-5 w-5" />
                            <span v-if="approvalForm.processing">Approving...</span>
                            <span v-else>Approve & Publish</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

