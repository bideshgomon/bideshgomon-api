<template>
    <AuthenticatedLayout>
        <Head :title="`Appointment - ${formatDate(appointment.appointment_date)}`" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Appointment Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <Link :href="route('appointments.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                ← {{ __('Back to Appointments') }}
                            </Link>
                            <div class="flex items-center gap-2">
                                <button
                                    v-if="appointment.status === 'pending' || appointment.status === 'confirmed'"
                                    @click="showRescheduleModal = true"
                                    class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200"
                                >
                                    {{ __('Reschedule') }}
                                </button>
                                <button
                                    v-if="canCancel"
                                    @click="showCancelModal = true"
                                    class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-md hover:bg-red-200"
                                >
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>

                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span :class="statusClass(appointment.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                                        {{ statusLabel(appointment.status) }}
                                    </span>
                                    <span :class="typeClass(appointment.appointment_type)" class="px-3 py-1 text-sm font-semibold rounded-full">
                                        {{ typeLabel(appointment.appointment_type) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="flex items-center text-gray-900 mb-1">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="font-semibold text-lg">{{ formatDate(appointment.appointment_date) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex items-center text-gray-900 mb-1">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-semibold text-lg">{{ formatTime(appointment.appointment_time) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="text-sm font-medium text-gray-600 w-32">{{ __('Purpose') }}:</span>
                                        <span class="text-sm text-gray-900">{{ purposeLabel(appointment.purpose) }}</span>
                                    </div>
                                    <div v-if="appointment.notes" class="flex items-start">
                                        <span class="text-sm font-medium text-gray-600 w-32">{{ __('Notes') }}:</span>
                                        <span class="text-sm text-gray-900">{{ appointment.notes }}</span>
                                    </div>
                                    <div v-if="appointment.assigned_to" class="flex items-start">
                                        <span class="text-sm font-medium text-gray-600 w-32">{{ __('Assigned to') }}:</span>
                                        <span class="text-sm text-gray-900">{{ appointment.assigned_to.name }}</span>
                                    </div>
                                    <div v-if="appointment.meeting_link && appointment.status === 'confirmed'" class="flex items-start">
                                        <span class="text-sm font-medium text-gray-600 w-32">{{ __('Meeting Link') }}:</span>
                                        <a :href="appointment.meeting_link" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline">
                                            {{ __('Join Meeting') }}
                                        </a>
                                    </div>
                                </div>

                                <div v-if="appointment.admin_notes" class="mt-4 p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm font-medium text-blue-900 mb-1">{{ __('Admin Notes') }}:</p>
                                    <p class="text-sm text-blue-800">{{ appointment.admin_notes }}</p>
                                </div>

                                <div v-if="appointment.cancellation_reason" class="mt-4 p-4 bg-red-50 rounded-lg">
                                    <p class="text-sm font-medium text-red-900 mb-1">{{ __('Cancellation Reason') }}:</p>
                                    <p class="text-sm text-red-800">{{ appointment.cancellation_reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Timeline') }}</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Appointment Requested') }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(appointment.created_at) }}</p>
                                </div>
                            </div>

                            <div v-if="appointment.confirmed_at" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Appointment Confirmed') }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(appointment.confirmed_at) }}</p>
                                </div>
                            </div>

                            <div v-if="appointment.completed_at" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Appointment Completed') }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(appointment.completed_at) }}</p>
                                </div>
                            </div>

                            <div v-if="appointment.cancelled_at" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Appointment Cancelled') }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(appointment.cancelled_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <Modal :show="showCancelModal" @close="showCancelModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Cancel Appointment') }}</h3>
                <form @submit.prevent="cancelAppointment">
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Reason for Cancellation') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="cancellation_reason"
                            v-model="cancelForm.reason"
                            rows="4"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            :class="{ 'border-red-500': cancelForm.errors.reason }"
                            required
                            placeholder="Please let us know why you're cancelling..."
                        ></textarea>
                        <p v-if="cancelForm.errors.reason" class="mt-1 text-sm text-red-600">{{ cancelForm.errors.reason }}</p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            @click="showCancelModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            {{ __('Keep Appointment') }}
                        </button>
                        <button
                            type="submit"
                            :disabled="cancelForm.processing"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 transition"
                            :class="{ 'opacity-50 cursor-not-allowed': cancelForm.processing }"
                        >
                            {{ cancelForm.processing ? __('Cancelling...') : __('Cancel Appointment') }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    appointment: Object,
});

const showCancelModal = ref(false);
const showRescheduleModal = ref(false);

const cancelForm = useForm({
    reason: '',
});

const canCancel = computed(() => {
    return ['pending', 'confirmed'].includes(props.appointment.status) && 
           new Date(props.appointment.appointment_date) >= new Date();
});

const cancelAppointment = () => {
    cancelForm.post(route('appointments.cancel', props.appointment.id), {
        preserveScroll: true,
        onSuccess: () => {
            showCancelModal.value = false;
            cancelForm.reset();
        },
    });
};

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const statusLabel = (status) => {
    const labels = {
        pending: 'Pending Confirmation',
        confirmed: 'Confirmed',
        completed: 'Completed',
        cancelled: 'Cancelled',
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
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    return `${displayHour}:${minutes} ${ampm}`;
};

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
