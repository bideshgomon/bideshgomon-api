<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Placeholder layout

const props = defineProps({
    university: Object, // The specific university data
});

// Helper to get location string (same as index page)
const getLocation = (uni) => {
    if (!uni.city) return 'Location Unknown';
    if (uni.city.state) {
        return `${uni.city.name}, ${uni.city.state.name}, ${uni.city.state.country.name}`;
    }
    if (uni.city.country) {
        return `${uni.city.name}, ${uni.city.country.name}`;
    }
    return uni.city.name;
};
</script>

<template>
    <Head :title="university.name" />

    <AuthenticatedLayout>
         <div class="py-12 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 md:p-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ university.name }}</h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ getLocation(university) }}</p>

                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Description</h2>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ university.description || 'No description available.' }}</p>
                    </div>

                    <div v-if="university.courses && university.courses.length > 0" class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                         <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Courses Offered</h2>
                         <ul>
                             <li v-for="course in university.courses" :key="course.id" class="text-gray-700 dark:text-gray-300 mb-1">
                                 {{ course.name }}
                             </li>
                         </ul>
                    </div>

                </div>
            </div>
         </div>
    </AuthenticatedLayout>
</template>