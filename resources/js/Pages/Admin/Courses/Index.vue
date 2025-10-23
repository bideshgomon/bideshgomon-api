<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Object, // Paginated data from controller
});
</script>

<template>
    <Head title="Manage Courses" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <!-- Header -->
                <header class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Courses
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage all courses offered by universities.
                        </p>
                    </div>

                    <div>
                        <Link
                            :href="route('admin.courses.create')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-brand-primary border border-transparent rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-95 transition ease-in-out duration-150"
                        >
                            Add New Course
                        </Link>
                    </div>
                </header>

                <!-- Table -->
                <div class="mt-6 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">
                                            Course Name
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            University
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Degree Level
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Field
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="courses.data.length === 0">
                                        <td colspan="5" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No courses found. Add universities first, then add courses.
                                        </td>
                                    </tr>

                                    <tr v-for="course in courses.data" :key="course.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">
                                            {{ course.name }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ course.university?.name || '—' }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ course.degree_level || '—' }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ course.field_of_study || '—' }}
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                            <Link
                                                :href="route('admin.courses.edit', course.id)"
                                                class="text-brand-primary hover:text-opacity-80"
                                            >
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <Pagination class="mt-6" :links="courses.links" />
            </section>
        </div>
    </AdminLayout>
</template>
