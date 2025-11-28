<template>
    <AuthenticatedLayout>
        <Head title="My Appointments" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-sky-100 rounded-lg">
                                    <CalendarDaysIcon class="w-8 h-8 text-sky-600" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">My Appointments</h2>
                                    <p class="text-sm text-gray-600">Manage your appointments</p>
                                </div>
                            </div>
                            <Link :href="route('appointments.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-medium transition-colors">
                                <PlusIcon class="w-5 h-5" />
                                Book Appointment
                            </Link>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 flex flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <FunnelIcon class="w-4 h-4 text-gray-500" />
                                <select v-model="filterStatus" @change="applyFilters" class="rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <select v-model="filterType" @change="applyFilters" class="rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="">All Types</option>
                                <option value="office_visit">Office Visit</option>
                                <option value="online_meeting">Online Meeting</option>
                            </select>
                        </div>

                        <!-- Appointments List -->
                        <div v-if="appointments.data.length > 0" class="space-y-3">
                            <Link v-for="appointment in appointments.data" :key="appointment.id" :href="route('appointments.show', appointment.id)" class="block border-2 border-gray-200 rounded-xl p-4 hover:border-sky-400 hover:bg-sky-50 transition-all group">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span :class="statusClass(appointment.status)" class="px-2.5 py-1 text-xs font-semibold rounded-lg">
                                                {{ statusLabel(appointment.status) }}
                                            </span>
                                            <span :class="typeClass(appointment.appointment_type)" class="px-2.5 py-1 text-xs font-semibold rounded-lg">
                                                {{ typeLabel(appointment.appointment_type) }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4 mb-2">
                                            <div class="flex items-center text-gray-700">
                                                <CalendarIcon class="w-5 h-5 mr-2 text-sky-600" />
                                                <span class="font-semibold">{{ formatDate(appointment.appointment_date) }}</span>
                                            </div>
                                            <div class="flex items-center text-gray-700">
                                                <ClockIcon class="w-5 h-5 mr-2 text-sky-600" />
                                                <span class="font-semibold">{{ appointment.appointment_time }}</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-1">
                                            <span class="font-medium">Purpose:</span> {{ purposeLabel(appointment.purpose) }}
                                        </p>
                                        <p v-if="appointment.notes" class="text-sm text-gray-600 line-clamp-2">{{ appointment.notes }}</p>
                                        <div v-if="appointment.assigned_to" class="mt-2 text-xs text-gray-500">
                                            Assigned to: {{ appointment.assigned_to.name }}
                                        </div>
                                    </div>
                                    <div>
                                        <ChevronRightIcon class="w-5 h-5 text-gray-400 group-hover:text-sky-600 transition-colors" />
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <div v-else class="text-center py-12">
                            <CalendarDaysIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by booking your first appointment.</p>
                            <div class="mt-6">
                                <Link :href="route('appointments.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-medium transition-colors">
                                    <PlusIcon class="w-5 h-5" />
                                    Book Appointment
                                </Link>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="appointments.data.length > 0" class="mt-6">
                            <Pagination :links="appointments.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { CalendarDaysIcon, PlusIcon, FunnelIcon, CalendarIcon, ClockIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    appointments: Object,
    filters: Object,
});

const filterStatus = ref(props.filters?.status || '');
const filterType = ref(props.filters?.type || '');

const applyFilters = () => {
    router.get(route('appointments.index'), {
        status: filterStatus.value,
        type: filterType.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800',
        rescheduled: 'bg-purple-100 text-purple-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const statusLabel = (status) => {
    const labels = {
        pending: 'Pending',
        confirmed: 'Confirmed',
        completed: 'Completed',
        cancelled: 'Cancelled',
        rescheduled: 'Rescheduled',
    };
    return labels[status] || status;
};

const typeClass = (type) => {
    const classes = {
        office_visit: 'bg-indigo-100 text-indigo-800',
        online_meeting: 'bg-cyan-100 text-cyan-800',
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const typeLabel = (type) => {
    const labels = {
        office_visit: 'Office Visit',
        online_meeting: 'Online Meeting',
    };
    return labels[type] || type;
};

const purposeLabel = (purpose) => {
    const labels = {
        consultation: 'Consultation',
        document_submission: 'Document Submission',
        general_inquiry: 'General Inquiry',
        visa_interview_prep: 'Visa Interview Preparation',
        application_review: 'Application Review',
    };
    return labels[purpose] || purpose;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>
