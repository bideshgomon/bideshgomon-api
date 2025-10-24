<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or your main public layout
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { computed } from 'vue';

const props = defineProps({
    service: Object, // Passed from ConsultationBookingController@showBookingForm
    consultants: Array, // Passed from ConsultationBookingController@showBookingForm
});

const form = useForm({
    consultation_service_id: props.service.id,
    consultant_id: '',
    preferred_date: '',
    preferred_time_slot: '',
    notes: '',
});

const submit = () => {
    form.post(route('consultations.book.store'), {
        onSuccess: () => {
            // Optionally reset form or handle success state
            // Inertia will redirect on success based on controller response
        },
    });
};

const timeSlots = [
    { value: 'morning', label: 'Morning (9am - 12pm)' },
    { value: 'afternoon', label: 'Afternoon (1pm - 5pm)' },
    { value: 'evening', label: 'Evening (6pm - 9pm)' },
];

// Get today's date in YYYY-MM-DD format for the min attribute
const today = computed(() => {
    const d = new Date();
    const month = ('0' + (d.getMonth() + 1)).slice(-2);
    const day = ('0' + d.getDate()).slice(-2);
    return `${d.getFullYear()}-${month}-${day}`;
});

</script>

<template>
    <Head :title="`Book: ${service.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Book Consultation: {{ service.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="mb-6 p-4 border border-gray-200 rounded-md bg-gray-50">
                                <h4 class="text-lg font-semibold text-gray-800">{{ service.name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ service.description }}</p>
                                <div class="flex justify-between items-baseline mt-2 text-sm">
                                    <span>Duration: {{ service.duration_minutes }} minutes</span>
                                    <span class="font-bold text-brand-primary">Price: ${{ service.price }}</span>
                                </div>
                                <input type="hidden" v-model="form.consultation_service_id">
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <InputLabel for="consultant_id" value="Preferred Consultant" />
                                    <SelectInput
                                        id="consultant_id"
                                        class="mt-1 block w-full"
                                        v-model="form.consultant_id"
                                        required
                                    >
                                        <option value="" disabled>Select a consultant</option>
                                        <option v-for="consultant in consultants" :key="consultant.id" :value="consultant.id">
                                            {{ consultant.name }}
                                            <span v-if="consultant.consultant_profile?.title"> ({{ consultant.consultant_profile.title }})</span>
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.consultant_id" />
                                    <p v-if="consultants.length === 0" class="mt-1 text-sm text-red-600">
                                        No consultants are currently available.
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="preferred_date" value="Preferred Date" />
                                        <TextInput
                                            id="preferred_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.preferred_date"
                                            :min="today"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.preferred_date" />
                                    </div>
                                    <div>
                                        <InputLabel for="preferred_time_slot" value="Preferred Time Slot" />
                                        <SelectInput
                                            id="preferred_time_slot"
                                            class="mt-1 block w-full"
                                            v-model="form.preferred_time_slot"
                                            required
                                        >
                                            <option value="" disabled>Select a time slot</option>
                                            <option v-for="slot in timeSlots" :key="slot.value" :value="slot.value">
                                                {{ slot.label }}
                                            </option>
                                        </SelectInput>
                                        <InputError class="mt-2" :message="form.errors.preferred_time_slot" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="notes" value="Notes / Questions (Optional)" />
                                    <Textarea
                                        id="notes"
                                        class="mt-1 block w-full"
                                        v-model="form.notes"
                                        rows="4"
                                        placeholder="Add any specific questions or details for the consultant."
                                    />
                                    <InputError class="mt-2" :message="form.errors.notes" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('consultations.index')" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing || consultants.length === 0">
                                    Submit Request
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>