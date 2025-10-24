<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link }from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

defineProps({
    consultants: Object,
});
</script>

<template>
    <Head title="Consultants" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manage Consultants
                </h2>
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
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Profile Status
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="consultant in consultants.data" :key="consultant.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ consultant.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ consultant.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="consultant.consultant_profile" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <CheckCircleIcon class="h-4 w-4 mr-1" />
                                                Profile Complete
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <XCircleIcon class="h-4 w-4 mr-1" />
                                                Profile Missing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.consultants.edit', consultant.id)" class="text-indigo-600 hover:text-indigo-900">
                                                Manage Profile
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="consultants.data.length === 0">
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500" colspan="4">
                                            No users with the 'consultant' role found.
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