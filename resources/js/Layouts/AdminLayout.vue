<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {
    HomeIcon,
    Bars3Icon,
    XMarkIcon,
    BuildingOfficeIcon,
    AcademicCapIcon,
    GlobeAltIcon, // <-- IMPORT NEW ICON
    MapIcon,
    MapPinIcon
} from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);

// --- UPDATED NAVIGATION ---
const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: HomeIcon, current: route().current('admin.dashboard') },
    { 
        name: 'Universities', 
        href: route('admin.universities.index'), 
        icon: BuildingOfficeIcon, 
        current: route().current('admin.universities.index') 
    },
    { 
        name: 'Courses', 
        href: route('admin.courses.index'), 
        icon: AcademicCapIcon, 
        current: route().current('admin.courses.index') 
    },
    // --- NEW LINK ADDED ---
    { 
        name: 'Countries', 
        href: route('admin.countries.index'), 
        icon: GlobeAltIcon, 
        current: route().current('admin.countries.index') 
    },
    // --- END NEW LINK ---
    { 
        name: 'States', 
        href: '#', // route('admin.states.index')
        icon: MapIcon, 
        current: false, // route().current('admin.states.index') 
        disabled: true // Add this to disable link until ready
    },
    { 
        name: 'Cities', 
        href: '#', // route('admin.cities.index')
        icon: MapPinIcon, 
        current: false, // route().current('admin.cities.index') 
        disabled: true // Add this to disable link until ready
    },
];
// --- END UPDATED NAVIGATION ---
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hidden md:flex">
                <div class="flex flex-shrink-0 items-center px-4 h-16 border-b border-gray-200 dark:border-gray-700">
                    <Link :href="route('admin.dashboard')">
                        <ApplicationLogo class="block h-9 w-auto" />
                    </Link>
                </div>
                <div class="flex flex-1 flex-col overflow-y-auto">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1 mt-4">
                                <li v-for="item in navigation" :key="item.name">
                                    <Link
                                        :href="item.disabled ? '#' : item.href"
                                        :class="[
                                            item.current
                                                ? 'bg-gray-100 dark:bg-gray-900 text-brand-primary'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-brand-primary hover:bg-gray-50 dark:hover:bg-gray-700',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold mx-2',
                                            item.disabled ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed line-through' : ''
                                        ]"
                                        :aria-disabled="item.disabled"
                                        :tabindex="item.disabled ? -1 : undefined"
                                    >
                                        <component :is="item.icon" class="h-6 w-6 shrink-0" aria-hidden="true" />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="md:pl-64 flex flex-col flex-1">
                <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <button type="button" class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 md:hidden" @click="showingNavigationDropdown = true">
                        <span class="sr-only">Open sidebar</span>
                        <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                    </button>

                    <div class="flex-1 text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                         {{ $page.props.title || 'Admin Panel' }}
                    </div>

                    <div class="flex items-center gap-x-4">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="-m-1.5 flex items-center p-1.5 rounded-full text-left text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-brand-primary flex items-center justify-center text-white font-semibold">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <span class="hidden lg:flex lg:items-center ml-3">
                                            <span class="text-sm font-semibold leading-6">{{ user.name }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <header class="bg-white dark:bg-gray-800 shadow" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>
                
                <main class="flex-1">
                    <slot />
                </main>
            </div>

            <div v-if="showingNavigationDropdown" class="relative z-50 md:hidden" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-900/80 transition-opacity ease-linear duration-300" :class="showingNavigationDropdown ? 'opacity-100' : 'opacity-0'" @click="showingNavigationDropdown = false"></div>

                <div class="fixed inset-0 flex">
                    <div class="relative mr-16 flex w-full max-w-xs flex-1 transform transition ease-in-out duration-300" :class="showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full'" @click.stop>
                        <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                            <button type="button" class="-m-2.5 p-2.5" @click="showingNavigationDropdown = false">
                                <span class="sr-only">Close sidebar</span>
                                <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                            </button>
                        </div>
                        
                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-800 px-6 pb-4">
                            <div class="flex h-16 shrink-0 items-center">
                                <Link :href="route('admin.dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto" />
                                </Link>
                            </div>
                            <nav class="flex flex-1 flex-col">
                                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                    <li>
                                        <ul role="list" class="-mx-2 space-y-1">
                                            <li v-for="item in navigation" :key="item.name">
                                                <Link
                                                    :href="item.disabled ? '#' : item.href"
                                                    @click="showingNavigationDropdown = false"
                                                    :class="[
                                                        item.current
                                                            ? 'bg-gray-100 dark:bg-gray-900 text-brand-primary'
                                                            : 'text-gray-700 dark:text-gray-300 hover:text-brand-primary hover:bg-gray-50 dark:hover:bg-gray-700',
                                                        'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                        item.disabled ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed line-through' : ''
                                                    ]"
                                                    :aria-disabled="item.disabled"
                                                    :tabindex="item.disabled ? -1 : undefined"
                                                >
                                                    <component :is="item.icon" class="h-6 w-6 shrink-0" aria-hidden="true" />
                                                    {{ item.name }}
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>