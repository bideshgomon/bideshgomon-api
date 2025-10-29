<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue'; // Import computed
import {
    HomeIcon, GlobeAltIcon, MapIcon, MapPinIcon, BuildingOffice2Icon, AcademicCapIcon, BriefcaseIcon, TagIcon, UsersIcon, BeakerIcon, LanguageIcon, WrenchScrewdriverIcon
} from '@heroicons/vue/24/outline';

const page = usePage(); // Use page consistently
const currentUrl = computed(() => page.url); // Use computed for reactivity

// Sidebar links for admin - Use computed for reactivity
const adminLinks = computed(() => [
    { name: 'Dashboard', href: '#', icon: HomeIcon, current: currentUrl.value.startsWith('/admin/dashboard') || currentUrl.value === '/admin' }, // Handle base '/admin' if it's the dashboard
    { name: 'Countries', href: route('admin.countries.index'), icon: GlobeAltIcon, current: currentUrl.value.startsWith('/admin/countries') },
    { name: 'States', href: route('admin.states.index'), icon: MapIcon, current: currentUrl.value.startsWith('/admin/states') },
    { name: 'Cities', href: route('admin.cities.index'), icon: MapPinIcon, current: currentUrl.value.startsWith('/admin/cities') },
    { name: 'Universities', href: route('admin.universities.index'), icon: BuildingOffice2Icon, current: currentUrl.value.startsWith('/admin/universities') },
    { name: 'Courses', href: route('admin.courses.index'), icon: AcademicCapIcon, current: currentUrl.value.startsWith('/admin/courses') },
    { name: 'Degree Levels', href: '#', icon: AcademicCapIcon, current: currentUrl.value.startsWith('/admin/degree-levels'), disabled: true }, // Placeholder/disabled
    { name: 'Fields of Study', href: '#', icon: BeakerIcon, current: currentUrl.value.startsWith('/admin/fields-of-study'), disabled: true }, // Placeholder/disabled
    { name: 'Languages', href: '#', icon: LanguageIcon, current: currentUrl.value.startsWith('/admin/languages'), disabled: true }, // Placeholder/disabled
    { name: 'Skills', href: '#', icon: WrenchScrewdriverIcon, current: currentUrl.value.startsWith('/admin/skills'), disabled: true }, // Placeholder/disabled
    { name: 'Job Categories', href: '#', icon: TagIcon, current: currentUrl.value.startsWith('/admin/job-categories'), disabled: true }, // Placeholder/disabled
    { name: 'Job Postings', href: '#', icon: BriefcaseIcon, current: currentUrl.value.startsWith('/admin/jobs'), disabled: true }, // Placeholder/disabled
    { name: 'Manage Users', href: '#', icon: UsersIcon, current: currentUrl.value.startsWith('/admin/users'), disabled: true }, // Placeholder/disabled
]);

</script>

<template>
    <AuthenticatedLayout>
         <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Admin Panel
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8"> {/* Increased gap */}

                    {/* Sidebar */}
                    <aside class="lg:col-span-3 xl:col-span-2 mb-6 lg:mb-0"> {/* Adjusted col span */}
                        <nav class="space-y-1 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm"> {/* Added bg, padding, rounded, shadow */}
                            <Link
                                v-for="item in adminLinks"
                                :key="item.name"
                                :href="item.disabled ? '#' : item.href"
                                :class="[
                                    'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition duration-150 ease-in-out',
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
                                    item.disabled ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-60' : ''
                                ]"
                                :aria-current="item.current ? 'page' : undefined"
                                :aria-disabled="item.disabled"
                                :tabindex="item.disabled ? -1 : undefined"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[
                                        'mr-3 flex-shrink-0 h-5 w-5', // Slightly smaller icon
                                        item.current ? 'text-brand-primary dark:text-white' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300'
                                    ]"
                                    aria-hidden="true"
                                />
                                <span class="truncate">{{ item.name }}</span>
                            </Link>
                        </nav>
                    </aside>

                    {/* Main content slot */}
                    <div class="lg:col-span-9 xl:col-span-10"> {/* Adjusted col span */}
                        {/* The content (e.g., table) will often have its own bg/padding */}
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>