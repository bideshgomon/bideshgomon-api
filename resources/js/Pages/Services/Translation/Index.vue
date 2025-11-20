<template>
    <AuthenticatedLayout>
        <Head title="Translation Services" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Professional Translation Services</h1>
                    <p class="mt-2 text-gray-600">Fast, accurate, and certified translations</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="pair in languagePairs" :key="`${pair.from}-${pair.to}`"
                         class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ pair.from }} → {{ pair.to }}
                        </h3>
                        <p class="text-2xl font-bold text-indigo-600 mb-4">৳{{ pair.price }}/page</p>
                        <Link :href="route('translation.create')" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Request Translation
                        </Link>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <Link :href="route('translation.my-requests')" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        My Requests
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ languagePairs: Array });
</script>
