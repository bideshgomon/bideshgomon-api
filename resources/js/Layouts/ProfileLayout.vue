<script setup>
import { Link, usePage } from '@inertiajs/vue3'; // <-- Add usePage
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const currentRouteName = usePage().props.ziggy.location; // <-- Get current route

const profileLinks = [
    { name: 'Personal Information', href: route('profile.edit'), current: currentRouteName.includes('/profile') && !currentRouteName.includes('/profile/cv-builder') }, // Adjust condition
    { name: 'CV Builder', href: route('profile.cv.show'), current: currentRouteName.includes('/profile/cv-builder') }, // <-- ADD THIS
    { name: 'Education', href: route('profile.edit') + '#education', current: false }, // Use anchors later if needed
    { name: 'Experience', href: route('profile.edit') + '#experience', current: false },
    { name: 'Skills & Portfolio', href: route('profile.edit') + '#skills', current: false },
    { name: 'Documents', href: route('profile.edit') + '#documents', current: false },
    { name: 'Account Settings', href: route('profile.edit') + '#account', current: false },
];
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
                    <aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
                        <nav class="space-y-1">
                            <Link
                                v-for="item in profileLinks"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white font-semibold' // Added font-semibold
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                                    'group rounded-md px-3 py-2 flex items-center text-sm font-medium transition-colors duration-150', // Added transition
                                ]"
                                :aria-current="item.current ? 'page' : undefined"
                            >
                                <span class="truncate">{{ item.name }}</span>
                            </Link>
                        </nav>
                    </aside>

                    <div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>