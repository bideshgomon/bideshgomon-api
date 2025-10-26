<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue'; // Assuming you have this
import PrimaryButton from '@/Components/PrimaryButton.vue';

// --- THE FIX ---
// Define the paginated 'jobPostings' prop passed from the controller
defineProps({
    jobPostings: Object
});
</script>

<template>
    <Head title="Job Postings" />
    <AdminLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Manage Job Postings</h1>
                <Link :href="route('admin.job-postings.create')">
                    <PrimaryButton>Create Job</PrimaryButton>
                </Link>
            </div>

            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ $page.props.flash.error }}
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <table class="w-full text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Company</th>
                            <th scope="col" class="px-6 py-3">Category</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="jobPostings.data.length" v-for="job in jobPostings.data" :key="job.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ job.title }}</td>
                            <td class="px-6 py-4">{{ job.company_name }}</td>
                            <td class="px-6 py-4">{{ job.job_category ? job.job_category.name : 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-green-100 text-green-800': job.status === 'published',
                                    'bg-yellow-100 text-yellow-800': job.status === 'draft',
                                    'bg-red-100 text-red-800': job.status === 'expired',
                                }" class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                                    {{ job.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <Link :href="route('admin.job-postings.edit', job.id)" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Edit</Link>
                                </td>
                        </tr>
                        <tr v-else>
                            <td colspan="5" class="px-6 py-4 text-center">No job postings found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="jobPostings.links.length > 3" :links="jobPostings.links" class="mt-6" />
        </div>
    </AdminLayout>
</template>