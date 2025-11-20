<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    UserCircleIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    CalendarIcon,
    ShieldCheckIcon,
    BriefcaseIcon,
    DocumentTextIcon,
    WalletIcon,
    NoSymbolIcon,
    CheckCircleIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
});

const suspendForm = useForm({
    reason: '',
});

const roleForm = useForm({
    role: props.user.role,
});

const suspendUser = () => {
    if (!suspendForm.reason) {
        alert('Please enter a suspension reason');
        return;
    }

    if (confirm(`Suspend ${props.user.name}?`)) {
        suspendForm.post(route('admin.users.suspend', props.user.id));
    }
};

const unsuspendUser = () => {
    if (confirm(`Unsuspend ${props.user.name}?`)) {
        router.post(route('admin.users.unsuspend', props.user.id));
    }
};

const updateRole = () => {
    if (confirm(`Change ${props.user.name}'s role to ${roleForm.role}?`)) {
        roleForm.post(route('admin.users.update-role', props.user.id));
    }
};

const deleteUser = () => {
    if (confirm(`Are you sure you want to delete ${props.user.name}? This action cannot be undone.`)) {
        router.delete(route('admin.users.destroy', props.user.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(amount);
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    reviewed: 'bg-blue-100 text-blue-800',
    shortlisted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    hired: 'bg-purple-100 text-purple-800',
};

const getStatusColor = (status) => {
    return statusColors[status?.toLowerCase()] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="user.name" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Button -->
                <Link
                    :href="route('admin.users.index')"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6"
                >
                    <ArrowLeftIcon class="h-5 w-5 mr-2" />
                    Back to Users
                </Link>

                <!-- Header Card -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="h-20 w-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                                <p class="text-gray-600">{{ user.email }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-xs font-semibold',
                                            user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
                                        ]"
                                    >
                                        {{ user.role }}
                                    </span>
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-xs font-semibold',
                                            user.suspended_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800',
                                        ]"
                                    >
                                        {{ user.suspended_at ? 'Suspended' : 'Active' }}
                                    </span>
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-xs font-semibold',
                                            user.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                                        ]"
                                    >
                                        {{ user.email_verified_at ? 'Email Verified' : 'Email Unverified' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button
                                v-if="!user.suspended_at"
                                @click="suspendUser"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors text-sm"
                            >
                                <NoSymbolIcon class="h-4 w-4 inline mr-1" />
                                Suspend
                            </button>
                            <button
                                v-else
                                @click="unsuspendUser"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors text-sm"
                            >
                                <CheckCircleIcon class="h-4 w-4 inline mr-1" />
                                Unsuspend
                            </button>
                            <button
                                @click="deleteUser"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors text-sm"
                            >
                                <TrashIcon class="h-4 w-4 inline mr-1" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Contact Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <UserCircleIcon class="h-6 w-6 text-blue-600" />
                                Contact Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-start gap-3">
                                    <EnvelopeIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium text-gray-900">{{ user.email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <PhoneIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Phone</p>
                                        <p class="font-medium text-gray-900">{{ user.phone || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <MapPinIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Country</p>
                                        <p class="font-medium text-gray-900">{{ user.country?.name || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <CalendarIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Member Since</p>
                                        <p class="font-medium text-gray-900">{{ formatDate(user.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Details -->
                        <div v-if="user.profile" class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Details</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Date of Birth</p>
                                    <p class="font-medium text-gray-900">{{ user.profile.date_of_birth || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Gender</p>
                                    <p class="font-medium text-gray-900 capitalize">{{ user.profile.gender || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Nationality</p>
                                    <p class="font-medium text-gray-900">{{ user.profile.nationality || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Marital Status</p>
                                    <p class="font-medium text-gray-900 capitalize">{{ user.profile.marital_status || 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-gray-600">Address</p>
                                    <p class="font-medium text-gray-900">{{ user.profile.address || 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Job Applications -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <BriefcaseIcon class="h-6 w-6 text-blue-600" />
                                Job Applications ({{ user.job_applications?.length || 0 }})
                            </h2>
                            <div v-if="user.job_applications && user.job_applications.length > 0" class="space-y-4">
                                <div
                                    v-for="application in user.job_applications"
                                    :key="application.id"
                                    class="border border-gray-200 rounded-xl p-4"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ application.job_posting?.title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ application.job_posting?.company_name }}</p>
                                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                                <span
                                                    :class="['px-2 py-1 rounded-full text-xs font-semibold', getStatusColor(application.status)]"
                                                >
                                                    {{ application.status }}
                                                </span>
                                                <span class="text-xs text-gray-600">
                                                    Applied {{ formatDate(application.created_at) }}
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            :href="route('admin.applications.show', application.id)"
                                            class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors font-semibold text-sm"
                                        >
                                            View
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                <BriefcaseIcon class="h-12 w-12 mx-auto mb-3 text-gray-400" />
                                <p>No job applications yet</p>
                            </div>
                        </div>

                        <!-- CVs -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <DocumentTextIcon class="h-6 w-6 text-blue-600" />
                                CVs ({{ user.cvs?.length || 0 }})
                            </h2>
                            <div v-if="user.cvs && user.cvs.length > 0" class="space-y-3">
                                <div
                                    v-for="cv in user.cvs"
                                    :key="cv.id"
                                    class="flex items-center justify-between p-4 border border-gray-200 rounded-xl"
                                >
                                    <div>
                                        <p class="font-medium text-gray-900">{{ cv.title || 'Untitled CV' }}</p>
                                        <p class="text-sm text-gray-600">Created {{ formatDate(cv.created_at) }}</p>
                                    </div>
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-xs font-semibold',
                                            cv.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800',
                                        ]"
                                    >
                                        {{ cv.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                <DocumentTextIcon class="h-12 w-12 mx-auto mb-3 text-gray-400" />
                                <p>No CVs created yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Role Management -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <ShieldCheckIcon class="h-5 w-5 text-blue-600" />
                                Role Management
                            </h3>
                            <div class="space-y-3">
                                <select
                                    v-model="roleForm.role"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <button
                                    @click="updateRole"
                                    :disabled="roleForm.processing || roleForm.role === user.role"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50"
                                >
                                    Update Role
                                </button>
                            </div>
                        </div>

                        <!-- Suspension Form -->
                        <div v-if="!user.suspended_at" class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Suspend User</h3>
                            <div class="space-y-3">
                                <textarea
                                    v-model="suspendForm.reason"
                                    rows="4"
                                    placeholder="Enter suspension reason..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                ></textarea>
                                <button
                                    @click="suspendUser"
                                    :disabled="suspendForm.processing || !suspendForm.reason"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors disabled:opacity-50"
                                >
                                    Suspend User
                                </button>
                            </div>
                        </div>

                        <!-- Suspension Info -->
                        <div v-else class="bg-red-50 border border-red-200 rounded-2xl p-6">
                            <h3 class="text-md font-semibold text-red-900 mb-2 flex items-center gap-2">
                                <NoSymbolIcon class="h-5 w-5" />
                                Suspended
                            </h3>
                            <p class="text-sm text-red-800 mb-3">
                                <span class="font-medium">Reason:</span> {{ user.suspension_reason }}
                            </p>
                            <p class="text-xs text-red-600">
                                Suspended on {{ formatDate(user.suspended_at) }}
                            </p>
                        </div>

                        <!-- Wallet Information -->
                        <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl shadow-sm p-6 text-white">
                            <h3 class="text-md font-semibold mb-4 flex items-center gap-2">
                                <WalletIcon class="h-5 w-5" />
                                Wallet
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-green-100 text-sm">Balance</p>
                                    <p class="text-2xl font-bold">{{ formatCurrency(user.wallet?.balance || 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-green-100 text-sm">Cashback Balance</p>
                                    <p class="text-xl font-bold">{{ formatCurrency(user.wallet?.cashback_balance || 0) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Stats -->
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Account Statistics</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Applications</span>
                                    <span class="font-semibold text-gray-900">{{ user.job_applications?.length || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">CVs Created</span>
                                    <span class="font-semibold text-gray-900">{{ user.cvs?.length || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email Verified</span>
                                    <span
                                        :class="[
                                            'font-semibold',
                                            user.email_verified_at ? 'text-green-600' : 'text-yellow-600',
                                        ]"
                                    >
                                        {{ user.email_verified_at ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Last Login</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ user.last_login_at ? formatDate(user.last_login_at) : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
