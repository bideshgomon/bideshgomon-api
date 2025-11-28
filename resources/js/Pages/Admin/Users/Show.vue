<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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
    CreditCardIcon,
    NoSymbolIcon,
    CheckCircleIcon,
    TrashIcon,
    PencilIcon,
    ClockIcon,
    GlobeAltIcon,
    CheckBadgeIcon,
    XCircleIcon,
    CurrencyDollarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
});

const activeTab = ref('overview');

const suspendForm = useForm({
    reason: '',
});

const roleForm = useForm({
    role_id: props.user.role_id,
});

const suspendUser = () => {
    if (!suspendForm.reason) {
        alert('Please enter a suspension reason');
        return;
    }

    if (confirm(`Suspend ${props.user.name}?`)) {
        suspendForm.post(route('admin.users.suspend', props.user.id), {
            preserveScroll: true,
            onSuccess: () => {
                suspendForm.reset();
            }
        });
    }
};

const unsuspendUser = () => {
    if (confirm(`Unsuspend ${props.user.name}?`)) {
        router.post(route('admin.users.unsuspend', props.user.id), {}, {
            preserveScroll: true,
        });
    }
};

const updateRole = () => {
    if (confirm(`Change ${props.user.name}'s role?`)) {
        roleForm.post(route('admin.users.update-role', props.user.id), {
            preserveScroll: true,
        });
    }
};

const deleteUser = () => {
    if (confirm(`Are you sure you want to delete ${props.user.name}? This action cannot be undone.`)) {
        router.delete(route('admin.users.destroy', props.user.id));
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatDateTime = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (amount) => {
    return '৳' + new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(amount || 0);
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    reviewed: 'bg-blue-100 text-blue-800 border-blue-200',
    shortlisted: 'bg-green-100 text-green-800 border-green-200',
    rejected: 'bg-red-100 text-red-800 border-red-200',
    hired: 'bg-purple-100 text-purple-800 border-purple-200',
};

const getStatusColor = (status) => {
    return statusColors[status?.toLowerCase()] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const tabs = [
    { id: 'overview', label: 'Overview', icon: UserCircleIcon },
    { id: 'applications', label: 'Job Applications', icon: BriefcaseIcon },
    { id: 'cvs', label: 'CVs & Documents', icon: DocumentTextIcon },
    { id: 'activity', label: 'Activity Log', icon: ClockIcon },
];

</script>

<template>
    <Head :title="`${user.name} - User Details`" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header with Back Button -->
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.users.index')"
                        class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                        Back to Users
                    </Link>
                    <div class="flex gap-2">
                        <Link
                            :href="route('admin.users.edit', user.id)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
                        >
                            <PencilIcon class="h-4 w-4" />
                            Edit User
                        </Link>
                        <button
                            @click="deleteUser"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors"
                        >
                            <TrashIcon class="h-4 w-4" />
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Profile Header Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <!-- Header Section -->
                    <div class="bg-indigo-600 px-6 py-8">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <!-- Avatar and Basic Info -->
                            <div class="flex items-center gap-4">
                                <div class="h-24 w-24 rounded-lg bg-white flex items-center justify-center text-indigo-600 text-4xl font-bold shadow-md">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-white">{{ user.name }}</h1>
                                    <p class="text-indigo-100 flex items-center gap-2 mt-1">
                                        <EnvelopeIcon class="h-4 w-4" />
                                        {{ user.email }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        <span
                                            :class="[
                                                'px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 bg-white',
                                                user.role?.slug === 'admin' ? 'text-indigo-700' : 'text-indigo-600',
                                            ]"
                                        >
                                            <ShieldCheckIcon class="h-4 w-4" />
                                            {{ user.role?.name || 'User' }}
                                        </span>
                                        <span
                                            :class="[
                                                'px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5',
                                                user.suspended_at ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700',
                                            ]"
                                        >
                                            <component :is="user.suspended_at ? NoSymbolIcon : CheckCircleIcon" class="h-4 w-4" />
                                            {{ user.suspended_at ? 'Suspended' : 'Active' }}
                                        </span>
                                        <span
                                            :class="[
                                                'px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5',
                                                user.email_verified_at ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700',
                                            ]"
                                        >
                                            <component :is="user.email_verified_at ? CheckBadgeIcon : XCircleIcon" class="h-4 w-4" />
                                            {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-if="!user.suspended_at"
                                    @click="suspendUser"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors text-sm flex items-center gap-2"
                                >
                                    <NoSymbolIcon class="h-4 w-4" />
                                    Suspend
                                </button>
                                <button
                                    v-else
                                    @click="unsuspendUser"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors text-sm flex items-center gap-2"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                    Unsuspend
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats Row -->
                    <div class="px-6 pb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ user.job_applications?.length || 0 }}</div>
                                <div class="text-sm text-gray-600">Applications</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ user.cvs?.length || 0 }}</div>
                                <div class="text-sm text-gray-600">CVs</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ formatCurrency(user.wallet?.balance || 0) }}</div>
                                <div class="text-sm text-gray-600">Wallet Balance</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ Math.floor((new Date() - new Date(user.created_at)) / (1000 * 60 * 60 * 24)) }}</div>
                                <div class="text-sm text-gray-600">Days Active</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 overflow-hidden">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="activeTab = tab.id"
                                :class="[
                                    'flex items-center gap-2 px-6 py-4 font-medium text-sm border-b-2 transition-colors',
                                    activeTab === tab.id
                                        ? 'border-indigo-600 text-indigo-600'
                                        : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300',
                                ]"
                            >
                                <component :is="tab.icon" class="h-5 w-5" />
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'">
                            <!-- Contact Information -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <EnvelopeIcon class="h-6 w-6 text-indigo-600" />
                                    Contact Information
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <EnvelopeIcon class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-600 uppercase font-semibold">Email</p>
                                            <p class="font-medium text-gray-900 mt-1">{{ user.email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <PhoneIcon class="h-5 w-5 text-green-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-600 uppercase font-semibold">Phone</p>
                                            <p class="font-medium text-gray-900 mt-1">{{ user.phone || 'Not provided' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                        <div class="p-2 bg-purple-100 rounded-lg">
                                            <MapPinIcon class="h-5 w-5 text-purple-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-600 uppercase font-semibold">Country</p>
                                            <p class="font-medium text-gray-900 mt-1">{{ user.country?.name || 'Not specified' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                        <div class="p-2 bg-orange-100 rounded-lg">
                                            <CalendarIcon class="h-5 w-5 text-orange-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-600 uppercase font-semibold">Member Since</p>
                                            <p class="font-medium text-gray-900 mt-1">{{ formatDate(user.created_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Details -->
                            <div v-if="user.profile" class="bg-white rounded-xl shadow-sm p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <UserCircleIcon class="h-6 w-6 text-blue-600" />
                                    Profile Details
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Date of Birth</p>
                                        <p class="font-medium text-gray-900">{{ user.profile.date_of_birth ? formatDate(user.profile.date_of_birth) : 'Not provided' }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Gender</p>
                                        <p class="font-medium text-gray-900 capitalize">{{ user.profile.gender || 'Not specified' }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Nationality</p>
                                        <p class="font-medium text-gray-900">{{ user.profile.nationality || 'Not specified' }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Marital Status</p>
                                        <p class="font-medium text-gray-900 capitalize">{{ user.profile.marital_status || 'Not specified' }}</p>
                                    </div>
                                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg">
                                        <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Address</p>
                                        <p class="font-medium text-gray-900">{{ user.profile.address || 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Applications Tab -->
                        <div v-if="activeTab === 'applications'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <BriefcaseIcon class="h-6 w-6 text-indigo-600" />
                                    Job Applications
                                </span>
                                <span class="text-sm font-medium px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg">
                                    {{ user.job_applications?.length || 0 }}
                                </span>
                            </h2>
                            <div v-if="user.job_applications && user.job_applications.length > 0" class="space-y-4">
                                <div
                                    v-for="application in user.job_applications"
                                    :key="application.id"
                                    class="border border-gray-200 rounded-lg p-5 hover:border-indigo-300 hover:shadow-sm transition-all"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-lg text-gray-900">{{ application.job_posting?.title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                                                <GlobeAltIcon class="h-4 w-4" />
                                                {{ application.job_posting?.company_name }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-3 mt-4">
                                                <span
                                                    :class="['px-3 py-1.5 rounded-lg text-xs font-medium', getStatusColor(application.status)]"
                                                >
                                                    {{ application.status }}
                                                </span>
                                                <span class="text-xs text-gray-600 flex items-center gap-1">
                                                    <ClockIcon class="h-4 w-4" />
                                                    {{ formatDate(application.created_at) }}
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            :href="route('admin.applications.show', application.id)"
                                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm"
                                        >
                                            View Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-16">
                                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                                    <BriefcaseIcon class="h-10 w-10 text-gray-400" />
                                </div>
                                <p class="text-gray-600 font-medium">No job applications yet</p>
                                <p class="text-sm text-gray-500 mt-1">User hasn't applied to any jobs</p>
                            </div>
                        </div>

                        <!-- CVs Tab -->
                        <div v-if="activeTab === 'cvs'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <DocumentTextIcon class="h-6 w-6 text-indigo-600" />
                                    CVs & Documents
                                </span>
                                <span class="text-sm font-medium px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg">
                                    {{ user.cvs?.length || 0 }}
                                </span>
                            </h2>
                            <div v-if="user.cvs && user.cvs.length > 0" class="space-y-3">
                                <div
                                    v-for="cv in user.cvs"
                                    :key="cv.id"
                                    class="flex items-center justify-between p-5 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-sm transition-all"
                                >
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-indigo-100 rounded-lg">
                                            <DocumentTextIcon class="h-6 w-6 text-indigo-600" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ cv.title || 'Untitled CV' }}</p>
                                            <p class="text-sm text-gray-600 mt-1">Created {{ formatDate(cv.created_at) }}</p>
                                        </div>
                                    </div>
                                    <span
                                        :class="[
                                            'px-4 py-2 rounded-lg text-xs font-medium',
                                            cv.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700',
                                        ]"
                                    >
                                        {{ cv.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-center py-16">
                                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                                    <DocumentTextIcon class="h-10 w-10 text-gray-400" />
                                </div>
                                <p class="text-gray-600 font-medium">No CVs created yet</p>
                                <p class="text-sm text-gray-500 mt-1">User hasn't uploaded any CVs</p>
                            </div>
                        </div>

                        <!-- Activity Tab -->
                        <div v-if="activeTab === 'activity'" class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <ClockIcon class="h-6 w-6 text-blue-600" />
                                Activity Log
                            </h2>
                            <div class="space-y-4">
                                <div class="flex gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <CheckCircleIcon class="h-5 w-5 text-green-600" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Account Created</p>
                                        <p class="text-sm text-gray-600">{{ formatDateTime(user.created_at) }}</p>
                                    </div>
                                </div>
                                <div v-if="user.email_verified_at" class="flex gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <CheckBadgeIcon class="h-5 w-5 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Email Verified</p>
                                        <p class="text-sm text-gray-600">{{ formatDateTime(user.email_verified_at) }}</p>
                                    </div>
                                </div>
                                <div v-if="user.last_login_at" class="flex gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <ClockIcon class="h-5 w-5 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Last Login</p>
                                        <p class="text-sm text-gray-600">{{ formatDateTime(user.last_login_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Wallet Information -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <CreditCardIcon class="h-6 w-6 text-indigo-600" />
                                Wallet Balance
                            </h3>
                            <div class="space-y-4">
                                <div class="p-4 bg-indigo-50 rounded-lg">
                                    <p class="text-sm font-medium text-indigo-700 mb-1">Main Balance</p>
                                    <p class="text-3xl font-bold text-indigo-900">{{ formatCurrency(user.wallet?.balance || 0) }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Cashback</span>
                                        <span class="text-xl font-bold text-gray-900">{{ formatCurrency(user.wallet?.cashback_balance || 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Management -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-md font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <ShieldCheckIcon class="h-6 w-6 text-indigo-600" />
                                Role Management
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Role</label>
                                    <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                        <p class="font-bold text-indigo-900">{{ user.role?.name || 'User' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Change To</label>
                                    <select
                                        v-model="roleForm.role_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="1">Admin</option>
                                        <option value="2">User</option>
                                        <option value="3">Agency</option>
                                        <option value="4">Consultant</option>
                                    </select>
                                </div>
                                <button
                                    @click="updateRole"
                                    :disabled="roleForm.processing || roleForm.role_id === user.role_id"
                                    class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="roleForm.processing">Updating...</span>
                                    <span v-else>Update Role</span>
                                </button>
                            </div>
                        </div>

                        <!-- Suspension Management -->
                        <div v-if="!user.suspended_at" class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
                            <h3 class="text-md font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <NoSymbolIcon class="h-6 w-6 text-red-600" />
                                Suspend User
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Suspension Reason</label>
                                    <textarea
                                        v-model="suspendForm.reason"
                                        rows="4"
                                        placeholder="Enter detailed reason for suspension..."
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    ></textarea>
                                </div>
                                <button
                                    @click="suspendUser"
                                    :disabled="suspendForm.processing || !suspendForm.reason"
                                    class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="suspendForm.processing">Suspending...</span>
                                    <span v-else>Suspend User</span>
                                </button>
                            </div>
                        </div>

                        <!-- Suspension Info -->
                        <div v-else class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <h3 class="text-md font-bold text-red-900 mb-3 flex items-center gap-2">
                                <NoSymbolIcon class="h-6 w-6" />
                                Account Suspended
                            </h3>
                            <div class="space-y-3">
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <p class="text-xs text-red-700 font-bold uppercase mb-1">Reason</p>
                                    <p class="text-sm text-red-900 font-medium">{{ user.suspension_reason }}</p>
                                </div>
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <p class="text-xs text-red-700 font-bold uppercase mb-1">Suspended On</p>
                                    <p class="text-sm text-red-900 font-medium">{{ formatDateTime(user.suspended_at) }}</p>
                                </div>
                                <button
                                    @click="unsuspendUser"
                                    class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                                >
                                    Unsuspend User
                                </button>
                            </div>
                        </div>

                        <!-- Account Statistics -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-md font-bold text-gray-900 mb-4">Quick Stats</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium">Applications</span>
                                    <span class="text-lg font-bold text-indigo-600">{{ user.job_applications?.length || 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium">CVs Created</span>
                                    <span class="text-lg font-bold text-indigo-600">{{ user.cvs?.length || 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium">Email Status</span>
                                    <span
                                        :class="[
                                            'text-sm font-bold',
                                            user.email_verified_at ? 'text-green-600' : 'text-amber-600',
                                        ]"
                                    >
                                        {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-semibold">Account Age</span>
                                    <span class="text-sm font-bold text-orange-600">
                                        {{ Math.floor((new Date() - new Date(user.created_at)) / (1000 * 60 * 60 * 24)) }} days
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Account Details -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-md font-bold text-gray-900 mb-4">Account Details</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">User ID</span>
                                    <span class="font-bold text-gray-900">#{{ user.id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Created</span>
                                    <span class="font-bold text-gray-900">{{ formatDate(user.created_at) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Last Updated</span>
                                    <span class="font-bold text-gray-900">{{ formatDate(user.updated_at) }}</span>
                                </div>
                                <div v-if="user.last_login_at" class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Last Login</span>
                                    <span class="font-bold text-gray-900">{{ formatDate(user.last_login_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
