<script setup>
import { ref, computed, watch } from 'vue'; // <-- 'watch' ADDED HERE
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { XCircleIcon } from '@heroicons/vue/24/solid';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash);
const showFlash = ref(!!(flash.value?.success || flash.value?.error));

// Auto-hide flash message after a delay
if (showFlash.value) {
    setTimeout(() => {
        showFlash.value = false;
    }, 5000); // Hide after 5 seconds
}

// Watch for new flash messages coming from Inertia
watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
        showFlash.value = true;
        setTimeout(() => {
            showFlash.value = false;
        }, 5000); // Hide after 5 seconds
    }
}, { deep: true });
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto"
                                    />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('profile.edit')" :active="route().current('profile.edit') || route().current('profile.cv.show')">
                                    My Profile / CV
                                </NavLink>
                                <NavLink v-if="user?.role?.name === 'admin' && route().has('admin.universities.index')" :href="route('admin.universities.index')" :active="page.url.startsWith('/admin')">
                                    Admin Panel
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ user?.name || 'User' }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile Settings </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                     <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                     <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit') || route().current('profile.cv.show')">
                             My Profile / CV
                        </ResponsiveNavLink>
                         <ResponsiveNavLink v-if="user?.role?.name === 'admin' && route().has('admin.universities.index')" :href="route('admin.universities.index')" :active="page.url.startsWith('/admin')">
                            Admin Panel
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ user?.name || 'User' }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ user?.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile Settings </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white dark:bg-gray-800 shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <div v-if="showFlash && (flash?.success || flash?.error)" class="fixed top-20 right-5 z-50 max-w-sm w-full">
                 <transition
                    enter-active-class="transform ease-out duration-300 transition"
                    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                 >
                    <div v-if="flash.success" class="p-4 rounded-lg shadow-lg bg-green-50 dark:bg-green-800 border border-green-200 dark:border-green-700 flex items-start">
                        <span class="text-sm font-medium text-green-800 dark:text-green-200 flex-grow">{{ flash.success }}</span>
                         <button @click="showFlash = false" class="ml-3 flex-shrink-0 text-green-500 dark:text-green-300 hover:text-green-700 dark:hover:text-green-100">
                            <XCircleIcon class="h-5 w-5" />
                        </button>
                    </div>
                 </transition>
                 <transition
                    enter-active-class="transform ease-out duration-300 transition"
                    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                 >
                    <div v-if="flash.error" class="p-4 rounded-lg shadow-lg bg-red-50 dark:bg-red-800 border border-red-200 dark:border-red-700 flex items-start mt-2">
                        <span class="text-sm font-medium text-red-800 dark:text-red-200 flex-grow">{{ flash.error }}</span>
                        <button @click="showFlash = false" class="ml-3 flex-shrink-0 text-red-500 dark:text-red-300 hover:text-red-700 dark:hover:text-red-100">
                             <XCircleIcon class="h-5 w-5" />
                        </button>
                    </div>
                 </transition>
            </div>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>