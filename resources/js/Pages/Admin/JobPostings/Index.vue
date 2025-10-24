<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    postings: Object, // Paginated data
});
</script>

<template>
    <Head title="Manage Job Postings" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Job Postings</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage job opportunities for users.</p>
                    </div>
                    <Link :href="route('admin.job-postings.create')" class="inline-flex items-center justify-center px-4 py-2 bg-brand-primary border border-transparent rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-opacity-90 transition ease-in-out duration-150">
                        Add New Posting
                    </Link>
                </header>

                <div class="mt-6 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Title</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Company</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Category</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Location</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Edit</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="postings.data.length === 0">
                                        <td colspan="6" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No job postings found.</td>
                                    </tr>
                                    <tr v-for="post in postings.data" :key="post.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ post.title }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ post.company_name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ post.job_category?.name || 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ post.location_city }}{{ post.country ? ', ' + post.country.name : '' }}</td>
                                         <td class="px-3 py-4 text-sm">
                                            <span :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': post.status === 'active',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': post.status === 'expired',
                                                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300': post.status === 'filled',
                                                }" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20 capitalize">
                                                {{ post.status }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                            <Link :href="route('admin.job-postings.edit', post.id)" class="text-brand-primary hover:text-opacity-80">Edit</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <Pagination class="mt-6" :links="postings.links" />
            </section>
        </div>
    </AdminLayout>
</template>