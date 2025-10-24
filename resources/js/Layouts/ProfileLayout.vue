<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    UserCircleIcon, 
    DocumentTextIcon, 
    BriefcaseIcon, 
    AcademicCapIcon, 
    SparklesIcon, 
    CogIcon, 
    RectangleStackIcon 
} from '@heroicons/vue/24/outline';

// Get the page object from Inertia
const page = usePage();

// The navigation array
// We make it a computed property so it updates when the page changes
const navigation = computed(() => [
    { 
        name: 'Profile Settings', 
        href: route('profile.edit'), 
        icon: UserCircleIcon, 
        // This is the correct way to check the current route
        // page.url contains the path, e.g., "/profile"
        current: page.url === '/profile' 
    },
    { 
        name: 'CV Builder', 
        href: route('profile.cv.show'), 
        icon: RectangleStackIcon, 
        // Check for the CV builder path
        current: page.url === '/profile/cv-builder' 
    },
    // You can add more links here later, e.g.,
    // { name: 'My Appointments', href: '#', icon: CogIcon, current: page.url === '/profile/appointments' },
]);

</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Your Profile</h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
                <aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
                    <nav class="space-y-1">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            :class="[
                                item.current
                                    ? 'bg-gray-100 text-blue-600 border-blue-600'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent',
                                'group border-l-4 py-2 px-3 flex items-center text-sm font-medium',
                            ]"
                            :aria-current="item.current ? 'page' : undefined"
                        >
                            <component
                                :is="item.icon"
                                :class="[
                                    item.current ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500',
                                    'flex-shrink-0 -ml-1 mr-3 h-6 w-6',
                                ]"
                                aria-hidden="true"
                            />
                            <span class="truncate">{{ item.name }}</span>
                        </Link>
                    </nav>
                </aside>

                <div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
                    <slot />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>