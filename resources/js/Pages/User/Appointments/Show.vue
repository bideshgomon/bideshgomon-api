<template>
    <AuthenticatedLayout>
        <Head :title="`Appointment - ${formatDate(appointment.appointment_date)}`" />

        <div class="py-rhythm-2xl">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Appointment Header -->
                <RhythmicCard variant="sky" size="lg" class="mb-rhythm-lg">
                    <template #default>
                        <div class="flex items-center justify-between mb-rhythm-md">
                            <Link :href="route('appointments.index')" class="inline-flex items-center gap-rhythm-xs text-sky-700 hover:text-sky-900 font-medium">
                                <ArrowLeftIcon class="w-4 h-4" />
                                {{ __('Back to Appointments') }}
                            </Link>
                            <div class="flex items-center gap-rhythm-xs">
                                <FlowButton
                                    v-if="appointment.status === 'pending' || appointment.status === 'confirmed'"
                                    variant="sky"
                                    size="sm"
                                    @click="showRescheduleModal = true"
                                >
                                    <template #icon-left>
                                        <ArrowPathIcon class="w-4 h-4" />
                                    </template>
                                    {{ __('Reschedule') }}
                                </FlowButton>
                                <FlowButton
                                    v-if="canCancel"
                                    variant="error"
                                    size="sm"
                                    @click="showCancelModal = true"
                                >
                                    <template #icon-left>
                                        <XMarkIcon class="w-4 h-4" />
                                    </template>
                                    {{ __('Cancel') }}
                                </FlowButton>
                            </div>
                        </div>

                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-rhythm-sm mb-rhythm-sm">
                                    <StatusBadge :status="getStatusType(appointment.status)" :label="statusLabel(appointment.status)" size="md" />
                                    <StatusBadge :status="'info'" :label="typeLabel(appointment.appointment_type)" size="md" />
                                </div>

                                <div class="grid grid-cols-2 gap-rhythm-md mb-rhythm-md">
                                    <div>
                                        <div class="flex items-center text-gray-900 mb-rhythm-xs">
                                            <div class="p-rhythm-xs rounded-xl bg-sky-100 mr-rhythm-sm">
                                                <CalendarDaysIcon class="w-5 h-5 text-sky-600" />
                                            </div>
                                            <span class="font-semibold text-lg">{{ formatDate(appointment.appointment_date) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex items-center text-gray-900 mb-rhythm-xs">
                                            <div class="p-rhythm-xs rounded-xl bg-sky-100 mr-rhythm-sm">
                                                <ClockIcon class="w-5 h-5 text-sky-600" />
                                            </div>
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

                                <div v-if="appointment.admin_notes" class="mt-rhythm-md p-rhythm-md bg-sky-50 border-2 border-sky-200 rounded-xl">
                                    <p class="text-sm font-medium text-sky-900 mb-rhythm-xs">{{ __('Admin Notes') }}:</p>
                                    <p class="text-sm text-sky-800">{{ appointment.admin_notes }}</p>
                                </div>

                                <div v-if="appointment.cancellation_reason" class="mt-rhythm-md p-rhythm-md bg-red-50 border-2 border-red-200 rounded-xl">
                                    <p class="text-sm font-medium text-red-900 mb-rhythm-xs">{{ __('Cancellation Reason') }}:</p>
                                    <p class="text-sm text-red-800">{{ appointment.cancellation_reason }}</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </RhythmicCard>

                <!-- Appointment Timeline -->
                <RhythmicCard variant="white" size="lg">
                    <template #default>
                        <h3 class="text-lg font-semibold text-gray-900 mb-rhythm-md">{{ __('Timeline') }}</h3>
                        <div class="space-y-rhythm-md">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                                        <ClockIconSolid class="w-4 h-4 text-sky-600" />
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
                                        <CheckCircleIcon class="w-4 h-4 text-green-600" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Appointment Confirmed') }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(appointment.confirmed_at) }}</p>
                                </div>
                            </div>

                            <div v-if="appointment.completed_at" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                                        <CheckCircleIcon class="w-4 h-4 text-sky-600" />
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
                                        <XCircleIcon class="w-4 h-4 text-red-600" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Appointment Cancelled') }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(appointment.cancelled_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </RhythmicCard>
            </div>
        </div>

        <!-- Cancel Modal -->
        <Modal :show="showCancelModal" @close="showCancelModal = false">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <XCircleIcon class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Cancel Appointment') }}</h3>
                </div>
                <form @submit.prevent="cancelAppointment">
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Reason for Cancellation') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="cancellation_reason"
                            v-model="cancelForm.reason"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            :class="{ 'border-red-500': cancelForm.errors.reason }"
                            required
                            placeholder="Please let us know why you're cancelling..."
                        ></textarea>
                        <p v-if="cancelForm.errors.reason" class="mt-1 text-sm text-red-600">{{ cancelForm.errors.reason }}</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showCancelModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            {{ __('Keep Appointment') }}
                        </button>
                        <button
                            type="submit"
                            :disabled="cancelForm.processing"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border-2 border-red-300 text-red-700 rounded-lg hover:bg-red-100 font-medium transition-colors"
                            :class="{ 'opacity-50 cursor-not-allowed': cancelForm.processing }"
                        >
                            <XMarkIcon v-if="!cancelForm.processing" class="w-5 h-5" />
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
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import { ArrowLeftIcon, CalendarDaysIcon, ClockIcon, XMarkIcon, ArrowPathIcon, ClockIcon as ClockIconSolid, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

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

const getStatusType = (status) => {
    const types = {
        pending: 'warning',
        confirmed: 'success',
        completed: 'info',
        cancelled: 'error',
    };
    return types[status] || 'pending';
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
