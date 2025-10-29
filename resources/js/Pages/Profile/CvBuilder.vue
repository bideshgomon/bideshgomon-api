<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    user: Object,
});
</script>

<template>
    <Head title="CV Builder" />

    <ProfileLayout>
        <div class="space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            CV Builder
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            This is the CV Builder page. (Work in Progress)
                        </p>
                    </header>

                    <div class="mt-6">
                        <p class="text-gray-700 dark:text-gray-300">
                            Welcome, {{ user.name }}. Your CV-building tools and preview will appear here.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </ProfileLayout>
</template>