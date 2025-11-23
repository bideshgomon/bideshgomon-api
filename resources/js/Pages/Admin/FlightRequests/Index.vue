<template>
    <AdminLayout>
        <Head title="Flight Requests Management" />

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Flight Requests Management</h1>
                    <p class="mt-2 text-gray-600">Manage and assign flight requests to agencies</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="text-sm text-gray-500 mb-1">Total Requests</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-6">
                        <div class="text-sm text-yellow-700 mb-1">Pending</div>
                        <div class="text-2xl font-bold text-yellow-900">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-6">
                        <div class="text-sm text-blue-700 mb-1">Assigned</div>
                        <div class="text-2xl font-bold text-blue-900">{{ stats.assigned }}</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg shadow-sm border border-purple-200 p-6">
                        <div class="text-sm text-purple-700 mb-1">Quoted</div>
                        <div class="text-2xl font-bold text-purple-900">{{ stats.quoted }}</div>
                    </div>
                    <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-6">
                        <div class="text-sm text-green-700 mb-1">Completed</div>
                        <div class="text-2xl font-bold text-green-900">{{ stats.completed }}</div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <!-- Filter Tabs -->
                        <div class="flex space-x-2">
                            <Link
                                :href="route('admin.flight-requests.index')"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                                    filter === 'all'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                All
                            </Link>
                            <Link
                                :href="route('admin.flight-requests.index', { filter: 'pending' })"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                                    filter === 'pending'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Pending
                            </Link>
                            <Link
                                :href="route('admin.flight-requests.index', { filter: 'assigned' })"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                                    filter === 'assigned'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Assigned
                            </Link>
                            <Link
                                :href="route('admin.flight-requests.index', { filter: 'quoted' })"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition',
                                    filter === 'quoted'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Quoted
                            </Link>
                        </div>

                        <!-- Search -->
                        <div class="flex space-x-2">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by reference, route, user..."
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <button
                                @click="search"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div v-if="selectedRequests.length > 0" class="mt-4 flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ selectedRequests.length }} selected</span>
                        <select
                            v-model="bulkAssignAgency"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select agency...</option>
                            <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                {{ agency.name }}
                            </option>
                        </select>
                        <button
                            @click="bulkAssign"
                            :disabled="!bulkAssignAgency"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50"
                        >
                            Bulk Assign
                        </button>
                        <button
                            @click="clearSelection"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Requests Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        @change="toggleAll"
                                        :checked="allSelected"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reference
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Route
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Budget
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigned To
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quotes
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="request in flightRequests.data" :key="request.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        v-model="selectedRequests"
                                        :value="request.id"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ request.request_reference }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ request.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ request.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ request.route_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatDate(request.travel_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ request.formatted_budget }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="request.status_color"
                                        class="px-2 py-1 rounded-full text-xs font-medium border"
                                    >
                                        {{ request.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="request.assigned_to" class="text-sm text-gray-900">
                                        {{ request.assigned_to.name }}
                                    </div>
                                    <div v-else class="text-sm text-gray-400">Not assigned</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="request.quotes_count > 0" class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                        {{ request.quotes_count }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400">0</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <Link
                                        :href="route('admin.flight-requests.show', request.id)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        View
                                    </Link>
                                    <button
                                        v-if="request.status === 'pending'"
                                        @click="showAssignModal(request)"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        Assign
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="flightRequests.data.length > 0" class="mt-6">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Showing {{ flightRequests.from }} to {{ flightRequests.to }} of {{ flightRequests.total }} requests
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-for="link in flightRequests.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-4 py-2 border rounded-lg text-sm',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>

                <!-- Assign Modal -->
                <div v-if="assignModal.show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Request</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Assign request {{ assignModal.request?.request_reference }} to an agency
                        </p>
                        
                        <select
                            v-model="assignModal.agencyId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4"
                        >
                            <option value="">Select agency...</option>
                            <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                {{ agency.name }} ({{ agency.email }})
                            </option>
                        </select>

                        <div class="flex justify-end space-x-3">
                            <button
                                @click="closeAssignModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>
                            <button
                                @click="assignRequest"
                                :disabled="!assignModal.agencyId"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                Assign
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    flightRequests: {
        type: Object,
        required: true
    },
    agencies: {
        type: Array,
        required: true
    },
    filter: {
        type: String,
        default: 'all'
    },
    search: {
        type: String,
        default: ''
    },
    stats: {
        type: Object,
        required: true
    }
});

const searchQuery = ref(props.search);
const selectedRequests = ref([]);
const bulkAssignAgency = ref('');

const assignModal = ref({
    show: false,
    request: null,
    agencyId: ''
});

const allSelected = computed(() => {
    return props.flightRequests.data.length > 0 && 
           selectedRequests.value.length === props.flightRequests.data.length;
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const search = () => {
    router.get(route('admin.flight-requests.index'), {
        filter: props.filter,
        search: searchQuery.value
    });
};

const toggleAll = () => {
    if (allSelected.value) {
        selectedRequests.value = [];
    } else {
        selectedRequests.value = props.flightRequests.data.map(r => r.id);
    }
};

const clearSelection = () => {
    selectedRequests.value = [];
    bulkAssignAgency.value = '';
};

const bulkAssign = () => {
    if (!bulkAssignAgency.value || selectedRequests.value.length === 0) return;

    router.post(route('admin.flight-requests.bulk-assign'), {
        request_ids: selectedRequests.value,
        agency_id: bulkAssignAgency.value
    }, {
        onSuccess: () => {
            clearSelection();
        }
    });
};

const showAssignModal = (request) => {
    assignModal.value = {
        show: true,
        request: request,
        agencyId: ''
    };
};

const closeAssignModal = () => {
    assignModal.value = {
        show: false,
        request: null,
        agencyId: ''
    };
};

const assignRequest = () => {
    if (!assignModal.value.agencyId) return;

    router.post(route('admin.flight-requests.assign', assignModal.value.request.id), {
        agency_id: assignModal.value.agencyId
    }, {
        onSuccess: () => {
            closeAssignModal();
        }
    });
};
</script>
