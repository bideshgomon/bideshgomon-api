<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    UserCircleIcon,
    DocumentTextIcon,
    CogIcon,
    BriefcaseIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Define the layout for this page
defineOptions({
    layout: AuthenticatedLayout
});

// Navigation for the profile sidebar
const profileNavigation = computed(() => [
    { name: 'Profile Dashboard', href: route('profile.edit'), icon: UserCircleIcon, current: route().current('profile.edit') },
    
    // --- THIS IS THE FIX ---
    // The 'profile.cv.show' route does not exist yet (Task CV-003).
    // We must comment this out or disable it to prevent a crash.
    // { name: 'View My CV', href: route('profile.cv.show', { user: page.props.auth.user.id }), icon: DocumentTextIcon, current: route().current('profile.cv.show') },
    // --- END FIX ---

    { name: 'Account Settings', href: '#', icon: CogIcon, current: false, disabled: true },
    { name: 'My Applications', href: '#', icon: BriefcaseIcon, current: false, disabled: true },
]);

</script>

<template>
    <Head title="My Profile" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <div class="md:col-span-1">
                    <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="flex items-center mb-4">
                             <div class="flex-shrink-0 h-12 w-12 rounded-full bg-brand-primary text-white flex items-center justify-center text-xl font-bold">
                                {{ user.name.charAt(0) }}
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                            </div>
                        </div>
                        
                        <nav class="space-y-1">
                            <Link
                                v-for="item in profileNavigation"
                                :key="item.name"
                                :href="item.disabled ? '#' : item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-900 text-brand-primary'
                                        : 'text-gray-700 dark:text-gray-300 hover:text-brand-primary hover:bg-gray-50 dark:hover:bg-gray-700',
                                    'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                                    item.disabled ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-50' : ''
                                ]"
                                :aria-disabled="item.disabled"
                                :tabindex="item.disabled ? -1 : undefined"
                            >
                                <component :is="item.icon" class="mr-3 h-6 w-6 shrink-0" aria-hidden="true" />
                                {{ item.name }}
                            </Link>

                             <Link
                                :href="route('dashboard')"
                                class="text-gray-700 dark:text-gray-300 hover:text-brand-primary hover:bg-gray-50 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-4 border-t border-gray-200 dark:border-gray-700 pt-4"
                            >
                                <ArrowLeftIcon class="mr-3 h-6 w-6 shrink-0" aria-hidden="true" />
                                Back to Dashboard
                            </Link>
                        </nav>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>