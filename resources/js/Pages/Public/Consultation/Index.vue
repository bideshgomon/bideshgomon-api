<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or your main public layout
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    services: Array, // Passed from ConsultationBookingController@index
});
</script>

<template>
    <Head title="Book a Consultation" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Consultation Services
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 bg-white border-b border-gray-200">
                        <h3 class="text-2xl font-semibold mb-6 text-gray-900">Choose a Service</h3>

                        <div v-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="service in services" :key="service.id" class="border border-gray-200 rounded-lg p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                <div>
                                    <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ service.name }}</h4>
                                    <p class="text-gray-600 mb-4">{{ service.description }}</p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <div>
                                        <p class="text-lg font-bold text-brand-primary">${{ service.price }}</p>
                                        <p class="text-sm text-gray-500">{{ service.duration_minutes }} minutes</p>
                                    </div>
                                    <Link :href="route('consultations.book.form', service.id)">
                                        <PrimaryButton>Book Now</PrimaryButton>
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 py-10">
                            No consultation services are currently available. Please check back later.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>