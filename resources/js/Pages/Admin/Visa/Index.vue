<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    MagnifyingGlassIcon,
    DocumentTextIcon,
    CalendarIcon,
    UserIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    EyeIcon,
    CurrencyDollarIcon,
    FunnelIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    applications: Object,
    stats: Object,
    staffMembers: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const visaType = ref(props.filters.visa_type || '');
const destination = ref(props.filters.destination || '');
const assignedTo = ref(props.filters.assigned_to || '');
const paymentStatus = ref(props.filters.payment_status || '');
const showFilters = ref(false);

const applyFilters = () => {
    router.get(route('admin.visa-applications.index'), {
        search: search.value,
        status: status.value,
        visa_type: visaType.value,
        destination: destination.value,
        assigned_to: assignedTo.value,
        payment_status: paymentStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    visaType.value = '';
    destination.value = '';
    assignedTo.value = '';
    paymentStatus.value = '';
    applyFilters();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', {
        style: 'currency',
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(amount || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const statusColors = {
    submitted: 'bg-blue-100 text-blue-800',
    under_review: 'bg-yellow-100 text-yellow-800',
    documents_requested: 'bg-orange-100 text-orange-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    completed: 'bg-purple-100 text-purple-800',
};

const paymentColors = {
    paid: 'bg-green-100 text-green-800',
    unpaid: 'bg-red-100 text-red-800',
    partial: 'bg-yellow-100 text-yellow-800',
};
</script>

<template>
    <Head title="Visa Applications" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white border-b border-gray-200 rounded-lg shadow-sm p-8 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Visa Applications</h1>
                            <p class="text-gray-600">Manage and track all visa applications</p>
                        </div>
                        <button
                            @click="showFilters = !showFilters"
                            class="mt-4 md:mt-0 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold flex items-center gap-2 transition"
                        >
                            <FunnelIcon class="h-5 w-5" />
                            {{ showFilters ? 'Hide' : 'Show' }} Filters
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <DocumentTextIcon class="h-8 w-8 text-gray-600 mb-2" />
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <ClockIcon class="h-8 w-8 text-blue-600 mb-2" />
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-blue-900">{{ stats.pending }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <DocumentTextIcon class="h-8 w-8 text-yellow-600 mb-2" />
                        <p class="text-sm text-gray-600">Under Review</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ stats.under_review }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <CheckCircleIcon class="h-8 w-8 text-green-600 mb-2" />
                        <p class="text-sm text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-green-900">{{ stats.approved }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <XCircleIcon class="h-8 w-8 text-red-600 mb-2" />
                        <p class="text-sm text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-red-900">{{ stats.rejected }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <CurrencyDollarIcon class="h-8 w-8 text-purple-600 mb-2" />
                        <p class="text-sm text-gray-600">Revenue</p>
                        <p class="text-xl font-bold text-purple-900">{{ formatCurrency(stats.revenue) }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <div v-if="showFilters" class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Reference, name, email, passport..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select
                                v-model="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="submitted">Submitted</option>
                                <option value="under_review">Under Review</option>
                                <option value="documents_requested">Documents Requested</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type</label>
                            <select
                                v-model="visaType"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Types</option>
                                <option value="tourist">Tourist</option>
                                <option value="student">Student</option>
                                <option value="work">Work</option>
                                <option value="business">Business</option>
                                <option value="family">Family</option>
                                <option value="transit">Transit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                            <input
                                v-model="destination"
                                type="text"
                                placeholder="Enter country"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                            <select
                                v-model="assignedTo"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Staff</option>
                                <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">
                                    {{ staff.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <select
                                v-model="paymentStatus"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Payments</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="applyFilters"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition"
                        >
                            Apply Filters
                        </button>
                        <button
                            @click="clearFilters"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                        >
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Applications Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Applicant</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Visa Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Destination</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Travel Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="application in applications.data" :key="application.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ application.application_reference }}</div>
                                        <div class="text-sm text-gray-500">{{ formatDate(application.created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ application.applicant_name }}</div>
                                        <div class="text-sm text-gray-500">{{ application.applicant_email }}</div>
                                        <div class="text-xs text-gray-400">{{ application.passport_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900 capitalize">{{ application.visa_type }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ application.destination_country }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ formatDate(application.travel_date) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-3 py-1 text-xs font-semibold rounded-full capitalize',
                                                statusColors[application.status] || 'bg-gray-100 text-gray-800'
                                            ]"
                                        >
                                            {{ (application?.status || '').replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-3 py-1 text-xs font-semibold rounded-full capitalize',
                                                paymentColors[application.payment_status] || 'bg-gray-100 text-gray-800'
                                            ]"
                                        >
                                            {{ application.payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(application.total_amount) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a
                                            :href="route('admin.visa-applications.show', application.id)"
                                            class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold text-sm"
                                        >
                                            <EyeIcon class="h-4 w-4" />
                                            View
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="applications.data.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing {{ applications.from }} to {{ applications.to }} of {{ applications.total }} applications
                            </div>
                            <div class="flex gap-2">
                                <a
                                    v-for="link in applications.links"
                                    :key="link.label"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg border transition',
                                        link.active
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : link.url
                                            ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                            : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                    ]"
                                ></a>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="px-6 py-12 text-center">
                        <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No applications found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
