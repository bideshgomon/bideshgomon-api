<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    UserCircleIcon,
    RectangleStackIcon, // For CV Builder
    // Add other icons if needed later
} from '@heroicons/vue/24/outline';

const page = usePage();
const currentUrl = computed(() => page.url);

const navigation = computed(() => [
    {
        name: 'Profile Settings',
        href: route('profile.edit'),
        icon: UserCircleIcon,
        // Check if the current URL is exactly '/profile'
        current: currentUrl.value === '/profile'
    },
    {
        name: 'CV Builder',
        href: route('profile.cv.show'), // Ensure this route exists
        icon: RectangleStackIcon,
        // Check if the current URL starts with '/profile/cv' (adjust if route is different)
        current: currentUrl.value.startsWith('/profile/cv') // Example check
    },
    // Add more profile sections here as needed
]);

</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Profile & CV
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                 <div class="lg:grid lg:grid-cols-12 lg:gap-8"> {/* Increased gap */}

                    {/* Sidebar Navigation */}
                    <aside class="lg:col-span-3 xl:col-span-2 mb-6 lg:mb-0"> {/* Adjusted cols */}
                        <nav class="space-y-1 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm"> {/* Added styles */}
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                     'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition duration-150 ease-in-out',
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
                                ]"
                                :aria-current="item.current ? 'page' : undefined"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[
                                         'mr-3 flex-shrink-0 h-5 w-5', // Smaller icon
                                        item.current ? 'text-brand-primary dark:text-white' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300'
                                    ]"
                                    aria-hidden="true"
                                />
                                <span class="truncate">{{ item.name }}</span>
                            </Link>
                        </nav>
                    </aside>

                    {/* Main Content Slot */}
                    <div class="lg:col-span-9 xl:col-span-10"> {/* Adjusted cols */}
                         {/* Content sections usually have their own bg/padding */}
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>