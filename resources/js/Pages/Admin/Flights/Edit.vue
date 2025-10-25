<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed } from 'vue';

const props = defineProps({
    flight: Object,
    airlines: Array,
    airports: Array,
});

// Helper function to format datetime-local input
const formatToDateTimeLocal = (isoDate) => {
    if (!isoDate) return '';
    const date = new Date(isoDate);
    // Adjust for timezone offset
    const timezoneOffset = date.getTimezoneOffset() * 60000;
    const localDate = new Date(date.getTime() - timezoneOffset);
    return localDate.toISOString().slice(0, 16);
};

const form = useForm({
    airline_id: props.flight.airline_id,
    origin_airport_id: props.flight.origin_airport_id,
    destination_airport_id: props.flight.destination_airport_id,
    flight_number: props.flight.flight_number,
    departure_at: formatToDateTimeLocal(props.flight.departure_at),
    arrival_at: formatToDateTimeLocal(props.flight.arrival_at),
    price: props.flight.price,
    available_seats: props.flight.available_seats,
});

const submit = () => {
    form.put(route('admin.api.flights.update', props.flight.id), {
        onSuccess: () => {
            router.visit(route('admin.flights.index'));
        },
    });
};
</script>

<template>
    <Head title="Edit Flight" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Edit Flight</h1>
                            <Link :href="route('admin.flights.index')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                Back to List
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="max-w-lg mx-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="flight_number" value="Flight Number" />
                                    <TextInput id="flight_number" type="text" class="mt-1 block w-full" v-model="form.flight_number" required />
                                    <InputError class="mt-2" :message="form.errors.flight_number" />
                                </div>
                                <div>
                                    <InputLabel for="airline_id" value="Airline" />
                                    <select id="airline_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.airline_id" required>
                                        <option value="" disabled>Select Airline</option>
                                        <option v-for="airline in airlines" :key="airline.id" :value="airline.id">{{ airline.name }}</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.airline_id" />
                                </div>
                                <div>
                                    <InputLabel for="origin_airport_id" value="Origin Airport" />
                                    <select id="origin_airport_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.origin_airport_id" required>
                                        <option value="" disabled>Select Origin</option>
                                        <option v-for="airport in airports" :key="airport.id" :value="airport.id">{{ airport.name }} ({{ airport.code }})</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.origin_airport_id" />
                                </div>
                                <div>
                                    <InputLabel for="destination_airport_id" value="Destination Airport" />
                                    <select id="destination_airport_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.destination_airport_id" required>
                                        <option value="" disabled>Select Destination</option>
                                        <option v-for="airport in airports" :key="airport.id" :value="airport.id">{{ airport.name }} ({{ airport.code }})</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.destination_airport_id" />
                                </div>
                                <div>
                                    <InputLabel for="departure_at" value="Departure Time" />
                                    <TextInput id="departure_at" type="datetime-local" class="mt-1 block w-full" v-model="form.departure_at" required />
                                    <InputError class="mt-2" :message="form.errors.departure_at" />
                                </div>
                                <div>
                                    <InputLabel for="arrival_at" value="Arrival Time" />
                                    <TextInput id="arrival_at" type="datetime-local" class="mt-1 block w-full" v-model="form.arrival_at" required />
                                    <InputError class="mt-2" :message="form.errors.arrival_at" />
                                </div>
                                <div>
                                    <InputLabel for="price" value="Price ($)" />
                                    <TextInput id="price" type="number" step="0.01" class="mt-1 block w-full" v-model="form.price" required />
                                    <InputError class="mt-2" :message="form.errors.price" />
                                </div>
                                <div>
                                    <InputLabel for="available_seats" value="Available Seats" />
                                    <TextInput id="available_seats" type="number" class="mt-1 block w-full" v-model="form.available_seats" required />
                                    <InputError class="mt-2" :message="form.errors.available_seats" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Flight
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>