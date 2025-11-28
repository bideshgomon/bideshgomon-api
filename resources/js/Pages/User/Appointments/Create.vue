<template>
    <AuthenticatedLayout>
        <Head title="Book Appointment" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-sky-100 rounded-lg">
                                    <CalendarDaysIcon class="w-8 h-8 text-sky-600" />
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900">Book Appointment</h2>
                            </div>
                            <Link :href="route('appointments.index')" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                                <ArrowLeftIcon class="w-4 h-4" />
                                Back
                            </Link>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Appointment Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Appointment Type <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex cursor-pointer rounded-xl border-2 p-4 focus:outline-none" :class="form.appointment_type === 'office_visit' ? 'border-sky-600 bg-sky-50' : 'border-gray-300 hover:border-sky-300'">
                                        <input type="radio" v-model="form.appointment_type" value="office_visit" class="sr-only" />
                                        <div class="flex flex-1">
                                            <div class="flex flex-col">
                                                <span class="block text-sm font-semibold text-gray-900">Office Visit</span>
                                                <span class="mt-1 flex items-center text-sm text-gray-500">In-person meeting</span>
                                            </div>
                                        </div>
                                        <CheckCircleIcon v-if="form.appointment_type === 'office_visit'" class="h-6 w-6 text-sky-600" />
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-xl border-2 p-4 focus:outline-none" :class="form.appointment_type === 'online_meeting' ? 'border-sky-600 bg-sky-50' : 'border-gray-300 hover:border-sky-300'">
                                        <input type="radio" v-model="form.appointment_type" value="online_meeting" class="sr-only" />
                                        <div class="flex flex-1">
                                            <div class="flex flex-col">
                                                <span class="block text-sm font-semibold text-gray-900">Online Meeting</span>
                                                <span class="mt-1 flex items-center text-sm text-gray-500">Video call</span>
                                            </div>
                                        </div>
                                        <CheckCircleIcon v-if="form.appointment_type === 'online_meeting'" class="h-6 w-6 text-sky-600" />
                                    </label>
                                </div>
                                <p v-if="form.errors.appointment_type" class="mt-1 text-sm text-red-600">{{ form.errors.appointment_type }}</p>
                            </div>

                            <!-- Date Selection -->
                            <div class="mb-4">
                                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    <CalendarDaysIcon class="w-4 h-4 inline mr-1" />
                                    Select Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="appointment_date"
                                    v-model="form.appointment_date"
                                    type="date"
                                    :min="minDate"
                                    :max="maxDate"
                                    @change="loadTimeSlotsForDate"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    :class="{ 'border-red-500': form.errors.appointment_date }"
                                    required
                                />
                                <p v-if="form.errors.appointment_date" class="mt-1 text-sm text-red-600">{{ form.errors.appointment_date }}</p>
                                <p class="mt-1 text-xs text-gray-500">Available: Monday - Friday, next 30 days</p>
                            </div>

                            <!-- Time Selection -->
                            <div v-if="form.appointment_date" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <ClockIcon class="w-4 h-4 inline mr-1" />
                                    Select Time <span class="text-red-500">*</span>
                                </label>
                                <div v-if="availableTimesForDate.length > 0" class="grid grid-cols-4 gap-2">
                                    <label v-for="time in availableTimesForDate" :key="time" class="relative flex cursor-pointer rounded-lg border-2 p-2 text-center focus:outline-none" :class="form.appointment_time === time ? 'border-sky-600 bg-sky-50' : 'border-gray-300 hover:border-sky-300'">
                                        <input type="radio" v-model="form.appointment_time" :value="time" class="sr-only" />
                                        <span class="flex-1 text-sm font-medium" :class="form.appointment_time === time ? 'text-sky-900' : 'text-gray-900'">
                                            {{ formatTime(time) }}
                                        </span>
                                    </label>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500">
                                    <ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
                                    <p class="mt-2 font-medium">No available time slots for this date</p>
                                    <p v-if="isWeekend" class="mt-1 text-sm text-amber-600">Office is closed on weekends. Please select a weekday.</p>
                                    <p v-else class="mt-1 text-sm">All slots are booked. Please try another date.</p>
                                </div>
                                <p v-if="form.errors.appointment_time" class="mt-1 text-sm text-red-600">{{ form.errors.appointment_time }}</p>
                            </div>

                            <!-- Purpose -->
                            <div class="mb-4">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                    Purpose <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="purpose"
                                    v-model="form.purpose"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    :class="{ 'border-red-500': form.errors.purpose }"
                                    required
                                >
                                    <option value="">Select Purpose</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="document_submission">Document Submission</option>
                                    <option value="general_inquiry">General Inquiry</option>
                                    <option value="visa_interview_prep">Visa Interview Preparation</option>
                                    <option value="application_review">Application Review</option>
                                </select>
                                <p v-if="form.errors.purpose" class="mt-1 text-sm text-red-600">{{ form.errors.purpose }}</p>
                            </div>

                            <!-- Notes -->
                            <div class="mb-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Additional Notes (Optional)
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    :class="{ 'border-red-500': form.errors.notes }"
                                    placeholder="Any specific requirements or topics you'd like to discuss..."
                                ></textarea>
                                <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3">
                                <Link :href="route('appointments.index')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !canSubmit"
                                    class="inline-flex items-center gap-2 px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-medium transition-colors"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing || !canSubmit }"
                                >
                                    <CalendarDaysIcon v-if="!form.processing" class="w-5 h-5" />
                                    <span v-if="form.processing">Booking...</span>
                                    <span v-else>Book Appointment</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarDaysIcon, ArrowLeftIcon, ClockIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    availableSlots: Object,
});

const form = useForm({
    appointment_type: 'office_visit',
    appointment_date: '',
    appointment_time: '',
    purpose: '',
    notes: '',
});

const today = new Date();
const minDate = today.toISOString().split('T')[0];
const maxDate = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

const availableTimesForDate = ref([]);

const canSubmit = computed(() => {
    return form.appointment_type && form.appointment_date && form.appointment_time && form.purpose;
});

const isWeekend = computed(() => {
    if (!form.appointment_date) return false;
    const date = new Date(form.appointment_date + 'T00:00:00');
    const day = date.getDay();
    return day === 0 || day === 6; // 0 = Sunday, 6 = Saturday
});

const loadTimeSlotsForDate = () => {
    const dateStr = form.appointment_date;
    availableTimesForDate.value = props.availableSlots[dateStr] || [];
    form.appointment_time = ''; // Reset time when date changes
};

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    return `${displayHour}:${minutes} ${ampm}`;
};

const submit = () => {
    form.post(route('appointments.store'), {
        preserveScroll: true,
    });
};
</script>
