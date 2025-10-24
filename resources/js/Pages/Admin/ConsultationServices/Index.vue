<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link }from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/solid';

defineProps({
    services: Object,
});
</script>

<template>
    <Head title="Consultation Services" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manage Consultation Services
                </h2>
                <Link :href="route('admin.consultation-services.create')">
                    <PrimaryButton>Create Service</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Duration
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Active
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="service in services.data" :key="service.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ service.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">${{ service.price }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ service.duration_minutes }} mins</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="service.is_active" class="p-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <CheckIcon class="h-4 w-4" />
                                            </span>
                                            <span v-else class="p-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <XMarkIcon class="h-4 w-4" />
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.consultation-services.edit', service.id)" class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="services.data.length === 0">
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500" colspan="5">
                                            No services found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>