<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    HomeIcon, GlobeAltIcon, MapIcon, MapPinIcon, BuildingOffice2Icon, AcademicCapIcon, BriefcaseIcon, TagIcon, UsersIcon, BeakerIcon, LanguageIcon, WrenchScrewdriverIcon // <-- CORRECTED IMPORT
} from '@heroicons/vue/24/outline';

// --- Active link logic ---
const currentRoute = usePage().url;

// --- Sidebar links with icons ---
const adminLinks = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: HomeIcon, current: currentRoute === '/admin' || currentRoute.startsWith('/admin/dashboard') },

    // --- GEOGRAPHY ---
    { name: 'Countries', href: route('admin.countries.index'), icon: GlobeAltIcon, current: currentRoute.startsWith('/admin/countries') },
    { name: 'States', href: route('admin.states.index'), icon: MapIcon, current: currentRoute.startsWith('/admin/states') },
    { name: 'Cities', href: route('admin.cities.index'), icon: MapPinIcon, current: currentRoute.startsWith('/admin/cities') },

    // --- ACADEMICS & PRE-BUILT ---
    { name: 'Universities', href: route('admin.universities.index'), icon: BuildingOffice2Icon, current: currentRoute.startsWith('/admin/universities') },
    { name: 'Courses', href: route('admin.courses.index'), icon: AcademicCapIcon, current: currentRoute.startsWith('/admin/courses') },
    { name: 'Degree Levels', href: route('admin.degree-levels.index'), icon: AcademicCapIcon, current: currentRoute.startsWith('/admin/degree-levels') },
    { name: 'Fields of Study', href: route('admin.fields-of-study.index'), icon: BeakerIcon, current: currentRoute.startsWith('/admin/fields-of-study') },
    { name: 'Languages', href: route('admin.languages.index'), icon: LanguageIcon, current: currentRoute.startsWith('/admin/languages') },
    { name: 'Skills', href: route('admin.skills.index'), icon: WrenchScrewdriverIcon, current: currentRoute.startsWith('/admin/skills') }, // Uses WrenchScrewdriverIcon

    // --- JOBS ---
    { name: 'Job Categories', href: route('admin.job-categories.index'), icon: TagIcon, current: currentRoute.startsWith('/admin/job-categories') },
    { name: 'Job Postings', href: route('admin.jobs.index'), icon: BriefcaseIcon, current: currentRoute.startsWith('/admin/jobs') },

    // --- USER MANAGEMENT ---
    { name: 'Manage Users', href: '#', icon: UsersIcon, current: false }, // Placeholder
];

// --- Flash message logic ---
const flash = ref(usePage().props.flash);
const showFlash = ref(false);

watch(
    () => usePage().props.flash,
    (newFlash) => {
        flash.value = newFlash;
        if (newFlash && (newFlash.success || newFlash.error)) {
            showFlash.value = true;
            setTimeout(() => (showFlash.value = false), 3000);
        }
    },
    { deep: true }
);
</script>

<template>
    <AuthenticatedLayout>
        <div
            v-if="showFlash && flash.success"
            class="fixed top-20 right-5 z-50 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 shadow"
            role="alert"
        >
            <span class="font-medium">Success!</span> {{ flash.success }}
        </div>
        <div
            v-if="showFlash && flash.error"
            class="fixed top-20 right-5 z-50 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 shadow"
            role="alert"
        >
            <span class="font-medium">Error!</span> {{ flash.error }}
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">

                    <aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
                        <nav class="space-y-1">
                            <Link
                                v-for="item in adminLinks"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                                    'group rounded-md px-3 py-2 flex items-center text-sm font-medium transition-colors duration-150',
                                ]"
                                :aria-current="item.current ? 'page' : undefined"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[
                                        item.current ? 'text-brand-primary dark:text-white' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300',
                                        'mr-3 flex-shrink-0 h-6 w-6 transition-colors duration-150',
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
        </div>
    </AuthenticatedLayout>
</template>