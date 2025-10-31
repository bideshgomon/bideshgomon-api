<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BuildingLibraryIcon, UserCircleIcon, DocumentTextIcon, ArrowRightIcon } from '@heroicons/vue/24/outline';

// Use the correct layout definition
defineOptions({
    layout: AuthenticatedLayout
});

const props = defineProps({
    auth: Object
});

const quickLinks = [
    {
        name: 'Edit Your Profile',
        href: route('profile.edit'),
        description: 'Update your personal info, skills, and experience.',
        icon: UserCircleIcon
    },
    {
        name: 'Search Universities',
        href: route('public.universities.search'),
        description: 'Browse our database of international universities.',
        icon: BuildingLibraryIcon
    },
    {
        name: 'Manage Documents',
        href: route('profile.edit'), // You can change this to a specific tab later
        description: 'Upload and manage your passport, transcripts, etc.',
        icon: DocumentTextIcon
    }
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-semibold mb-4">
                        Welcome back, {{ auth.user.name }}!
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        This is your personal dashboard. From here, you can manage your profile, search for universities, and prepare your applications.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Link
                            v-for="link in quickLinks"
                            :key="link.name"
                            :href="link.href"
                            class="block p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out group"
                        >
                            <div class="flex items-center mb-3">
                                <component :is="link.icon" class="h-8 w-8 text-brand-primary" />
                                <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ link.name }}
                                </h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ link.description }}
                            </p>
                            <span class="text-sm font-medium text-brand-primary group-hover:underline">
                                Go now <ArrowRightIcon class="inline h-4 w-4" />
                            </span>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>