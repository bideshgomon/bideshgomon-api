<template>
    <AuthenticatedLayout>
        <Head title="Visa Services" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Visa Application Services</h1>
                    <p class="mt-2 text-gray-600">Professional visa assistance for multiple countries worldwide</p>
                </div>

                <!-- Visa Types -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Visa Types</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="(visa, type) in visaTypes"
                            :key="type"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer"
                        >
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ visa.name }}</h3>
                                    <p class="mt-2 text-sm text-gray-600">{{ visa.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular Countries -->
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Popular Destinations</h2>
                        <Link
                            :href="route('visa.my-applications')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"
                        >
                            My Applications
                        </Link>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="country in popularCountries"
                            :key="country.code"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                        >
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <span class="text-4xl mr-3">{{ country.flag }}</span>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ country.name }}</h3>
                                            <p class="text-sm text-gray-600">{{ country.code }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="text-sm text-gray-600 mb-1">Service Fee (from)</div>
                                    <div class="text-2xl font-bold text-indigo-600">৳{{ country.service_fee.toLocaleString() }}</div>
                                </div>

                                <Link
                                    :href="route('visa.create', { country: country.name })"
                                    class="block w-full text-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                                >
                                    Apply Now
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="mt-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Why Choose Our Visa Services?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <div class="flex items-center mb-3">
                                <svg class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Expert Guidance</h3>
                            </div>
                            <p class="text-gray-600">Professional consultation from experienced visa consultants</p>
                        </div>
                        <div>
                            <div class="flex items-center mb-3">
                                <svg class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Fast Processing</h3>
                            </div>
                            <p class="text-gray-600">Standard, express, and urgent processing options available</p>
                        </div>
                        <div>
                            <div class="flex items-center mb-3">
                                <svg class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Secure Payment</h3>
                            </div>
                            <p class="text-gray-600">Multiple payment methods with secure transaction processing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    visaTypes: Object,
    popularCountries: Array,
});
</script>
