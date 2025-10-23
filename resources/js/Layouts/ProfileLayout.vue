<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const profileLinks = [
    { name: 'Personal Information', href: '#', current: false },
    { name: 'Education', href: '#', current: true },
    { name: 'Experience', href: '#', current: false },
    { name: 'Skills & Portfolio', href: '#', current: false },
    { name: 'Documents', href: '#', current: false },
    { name: 'Account Settings', href: '#', current: false },
];
</script>

<template>
    <AuthenticatedLayout>
        <!-- ✅ Header slot preserved -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Profile
            </h2>
        </template>

        <!-- ✅ Main layout structure -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
                    <!-- Sidebar Navigation -->
                    <aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
                        <nav class="space-y-1">
                            <Link
                                v-for="item in profileLinks"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800',
                                    'group rounded-md px-3 py-2 flex items-center text-sm font-medium',
                                ]"
                                :aria-current="item.current ? 'page' : undefined"
                            >
                                <span class="truncate">{{ item.name }}</span>
                            </Link>
                        </nav>
                    </aside>

                    <!-- Main Content Slot -->
                    <div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
