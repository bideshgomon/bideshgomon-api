<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bars3Icon,
    XMarkIcon,
    ChevronDownIcon,
    HomeIcon,
    UserCircleIcon,
    UsersIcon,
    Cog6ToothIcon,
    GlobeAltIcon,
    MapIcon,
    MapPinIcon,
    WrenchScrewdriverIcon,
    TagIcon, // Using TagIcon for Job Categories
    AcademicCapIcon,
    BookOpenIcon,
    BriefcaseIcon,
    ShieldCheckIcon, // Using Shield for Guidance/Security related things
    ArrowRightOnRectangleIcon, // Icon for collapse button
} from '@heroicons/vue/24/outline';

const page = usePage();
const user = computed(() => page.props.auth.user);
const userRole = computed(() => user.value?.role?.slug);

// --- Sidebar State ---
const sidebarOpen = ref(false); // Mobile sidebar toggle
// Read initial state from localStorage, default to false (expanded)
const sidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true' || false);
// Read open menus state from localStorage, default to empty object
const openMenus = ref(JSON.parse(localStorage.getItem('openMenus')) || {});

// --- Navigation Structure ---
// Define a structured navigation with nesting
const navigationStructure = computed(() => {
    let structure = [];

    // --- ADMIN ---
    if (userRole.value === 'admin') {
        structure = [
            { name: 'Admin Dashboard', href: route('admin.dashboard'), icon: HomeIcon, current: route().current('admin.dashboard') },
            {
                name: 'User Management',
                icon: UsersIcon,
                key: 'userMgmt', // Unique key for toggling
                // Check if any child route is active
                current: route().current('admin.users.*'),
                children: [
                    { name: 'All Users', href: route('admin.users.index'), current: route().current('admin.users.*') },
                    // { name: 'Roles & Permissions', href: '#', current: false }, // Future feature
                ]
            },
            {
                name: 'Data Management',
                icon: GlobeAltIcon,
                key: 'dataMgmt',
                // Check if any child route is active
                current: route().current('admin.countries.*') || route().current('admin.states.*') || route().current('admin.cities.*') || route().current('admin.skills.*') || route().current('admin.job-categories.*') || route().current('admin.universities.*') || route().current('admin.courses.*'),
                children: [
                    { name: 'Countries', href: route('admin.countries.index'), current: route().current('admin.countries.*') },
                    { name: 'States', href: route('admin.states.index'), current: route().current('admin.states.*') },
                    { name: 'Cities', href: route('admin.cities.index'), current: route().current('admin.cities.*') },
                    { name: 'Skills', href: route('admin.skills.index'), current: route().current('admin.skills.*') },
                    { name: 'Job Categories', href: route('admin.job-categories.index'), current: route().current('admin.job-categories.*') },
                    { name: 'Universities', href: route('admin.universities.index'), current: route().current('admin.universities.*') },
                    { name: 'Courses', href: route('admin.courses.index'), current: route().current('admin.courses.*') },
                    // Add other data types here later (Degree Levels, Fields of Study)
                ]
            },
            {
                name: 'Job Postings',
                icon: BriefcaseIcon,
                href: route('admin.job-postings.index'),
                current: route().current('admin.job-postings.*')
            },
            // Add other top-level admin sections here (e.g., Application Management)
            { name: 'Site Settings', href: route('admin.settings.index'), icon: Cog6ToothIcon, current: route().current('admin.settings.*') },
        ];
    }
    // --- GENERAL USER ---
    else if (userRole.value === 'user') {
        structure = [
             { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
             { name: 'My Profile', href: route('profile.edit'), icon: UserCircleIcon, current: route().current('profile.edit') || route().current('profile.cv.show') },
             { name: 'AI Guidance', href: route('guidance.dashboard'), icon: ShieldCheckIcon, current: route().current('guidance.dashboard') },
            // { name: 'My Applications', href: '#', icon: BriefcaseIcon, current: false }, // Placeholder for Work/Student Visa etc.
        ];
    }
    // --- AGENCY (placeholder) ---
    else if (userRole.value === 'agency') {
         structure = [
             { name: 'Agency Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
             { name: 'My Agency Profile', href: '#', icon: BriefcaseIcon, current: false }, // Placeholder
             { name: 'Manage Consultants', href: '#', icon: UsersIcon, current: false }, // Placeholder
             { name: 'Manage Clients', href: '#', icon: UserGroupIcon, current: false }, // Placeholder
         ];
    }
    // --- CONSULTANT (placeholder) ---
     else if (userRole.value === 'consultant') {
         structure = [
             { name: 'Consultant Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
             { name: 'My Profile', href: route('profile.edit'), icon: UserCircleIcon, current: route().current('profile.edit') || route().current('profile.cv.show') },
             { name: 'Assigned Clients', href: '#', icon: UsersIcon, current: false }, // Placeholder
         ];
    }

    return structure;
});

// --- Methods ---
const toggleSubmenu = (key) => {
    openMenus.value[key] = !openMenus.value[key];
    localStorage.setItem('openMenus', JSON.stringify(openMenus.value));
};

const toggleSidebarCollapse = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value);
};

// Check if a submenu or its parent group should be marked as current/active
const isMenuActive = (item) => {
    if (item.current) return true;
    if (item.children) {
        return item.children.some(child => child.current);
    }
    return false;
};

// Auto-open active submenu on page load
navigationStructure.value.forEach(item => {
    if (item.children && isMenuActive(item)) {
        if (openMenus.value[item.key] !== false) { // Check if not explicitly set to false
             openMenus.value[item.key] = true;
        }
    }
});
</script>

<template>
    <div class="min-h-screen bg-brand-light dark:bg-gray-900 font-primary flex">
        <Transition name="slide-fade">
            <div v-if="sidebarOpen" class="fixed inset-0 flex z-40 lg:hidden" role="dialog" aria-modal="true">
                <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-30" aria-hidden="true"></div>

                <div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white dark:bg-gray-800 z-40">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button @click="sidebarOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Close sidebar</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>

                    <div class="flex-shrink-0 flex items-center px-4">
                        <Link :href="route('dashboard')">
                           <ApplicationLogo class="h-8 w-auto" />
                        </Link>
                    </div>
                    <div class="mt-5 flex-1 h-0 overflow-y-auto">
                        <nav class="px-2 space-y-1">
                            <template v-for="item in navigationStructure" :key="item.name">
                                <Link v-if="!item.children" :href="item.href" :class="[item.current ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700', 'group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors']">
                                    <component :is="item.icon" :class="[item.current ? 'text-brand-primary dark:text-gray-300' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300', 'mr-4 flex-shrink-0 h-6 w-6 transition-colors']" aria-hidden="true" />
                                    {{ item.name }}
                                </Link>
                                <div v-else>
                                    <button @click="toggleSubmenu(item.key)" :class="[isMenuActive(item) ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700', 'group w-full flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors text-left']">
                                         <component :is="item.icon" :class="[isMenuActive(item) ? 'text-brand-primary dark:text-gray-300' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300', 'mr-4 flex-shrink-0 h-6 w-6 transition-colors']" aria-hidden="true" />
                                         <span class="flex-1">{{ item.name }}</span>
                                         <ChevronDownIcon :class="[openMenus[item.key] ? 'rotate-180 text-gray-500 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500', 'ml-2 h-5 w-5 transform transition-transform duration-150']"/>
                                    </button>
                                    <div v-show="openMenus[item.key]" class="mt-1 ml-4 pl-4 border-l border-gray-200 dark:border-gray-600 space-y-1">
                                        <Link v-for="child in item.children" :key="child.name" :href="child.href" :class="[child.current ? 'text-brand-primary dark:text-white font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white', 'block px-2 py-1.5 text-sm rounded-md transition-colors']">
                                            {{ child.name }}
                                        </Link>
                                    </div>
                                </div>
                            </template>
                        </nav>
                    </div>
                </div>
                <div class="flex-shrink-0 w-14" aria-hidden="true"></div>
            </div>
        </Transition>

        <div :class="[sidebarCollapsed ? 'w-20' : 'w-64', 'hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 transition-width duration-200 ease-in-out z-20']">
            <div class="flex flex-col flex-grow bg-white dark:bg-gray-800 pt-5 pb-0 overflow-y-auto border-r border-gray-200 dark:border-gray-700">
                <div :class="['flex items-center flex-shrink-0 px-4 h-8 mb-5 transition-all', sidebarCollapsed ? 'justify-center' : '']">
                     <Link :href="route('dashboard')">
                        <ApplicationLogo :class="['h-8 w-auto transition-opacity duration-200', sidebarCollapsed ? 'hidden' : 'opacity-100']" />
                        <img src="/images/Fev Icon.png" alt="Icon" :class="['h-8 w-auto transition-opacity duration-200', sidebarCollapsed ? 'opacity-100' : 'hidden']">
                     </Link>
                </div>

                <div class="flex-grow flex flex-col overflow-y-auto">
                    <nav class="flex-1 px-2 space-y-1" aria-label="Sidebar">
                        <template v-for="item in navigationStructure" :key="item.name">
                            <Link v-if="!item.children" :href="item.href" :title="sidebarCollapsed ? item.name : null" :class="[item.current ? 'bg-gray-100 dark:bg-gray-900 text-brand-primary dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700', 'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors']">
                                <component :is="item.icon" :class="[item.current ? 'text-brand-primary dark:text-gray-300' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300', 'flex-shrink-0 h-6 w-6 transition-colors', sidebarCollapsed ? 'mx-auto' : 'mr-3']" aria-hidden="true" />
                                <span :class="['flex-1 transition-opacity duration-100 whitespace-nowrap', sidebarCollapsed ? 'opacity-0 sr-only' : 'opacity-100']">{{ item.name }}</span>
                            </Link>
                            <div v-else>
                                <button @click="toggleSubmenu(item.key)" :title="sidebarCollapsed ? item.name : null" :class="[isMenuActive(item) ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700', 'group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors text-left']">
                                     <component :is="item.icon" :class="[isMenuActive(item) ? 'text-brand-primary dark:text-gray-300' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300', 'flex-shrink-0 h-6 w-6 transition-colors', sidebarCollapsed ? 'mx-auto' : 'mr-3']" aria-hidden="true" />
                                     <span :class="['flex-1 transition-opacity duration-100 whitespace-nowrap', sidebarCollapsed ? 'opacity-0 sr-only' : 'opacity-100']">{{ item.name }}</span>
                                     <ChevronDownIcon :class="[openMenus[item.key] ? 'rotate-180 text-gray-500 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500', sidebarCollapsed ? 'opacity-0 sr-only' : 'opacity-100', 'ml-2 h-5 w-5 transform transition-transform duration-150 transition-opacity']"/>
                                </button>
                                <div v-show="openMenus[item.key] && !sidebarCollapsed" class="mt-1 ml-4 pl-4 border-l border-gray-200 dark:border-gray-600 space-y-1">
                                    <Link v-for="child in item.children" :key="child.name" :href="child.href" :class="[child.current ? 'text-brand-primary dark:text-white font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white', 'block px-2 py-1.5 text-sm rounded-md transition-colors']">
                                        {{ child.name }}
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </nav>
                </div>

                 <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-2">
                    <button @click="toggleSidebarCollapse" class="w-full p-2 rounded-md text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-primary" :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                        <ArrowRightOnRectangleIcon :class="['h-6 w-6 mx-auto transform transition-transform duration-200', sidebarCollapsed ? 'rotate-180' : '']" aria-hidden="true" />
                    </button>
                 </div>
            </div>
        </div>

        <div :class="['flex flex-col flex-1 transition-padding duration-200 ease-in-out', sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64']">
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
                 <button @click="sidebarOpen = true" type="button" class="px-4 border-r border-gray-200 dark:border-gray-700 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-primary lg:hidden">
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <div class="flex-1 px-4 flex justify-between sm:px-6">
                     <div class="flex-1 flex items-center">
                         <slot name="header">
                              <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
                         </slot>
                     </div>
                     <div class="ml-4 flex items-center md:ml-6">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="max-w-xs bg-white dark:bg-gray-800 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary lg:p-2 lg:rounded-md lg:hover:bg-gray-50 dark:lg:hover:bg-gray-700" aria-expanded="false" aria-haspopup="true">
                                    <span class="inline-block h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                        <img
                                            v-if="user?.avatar"
                                            :src="user.avatar"
                                            alt="User Avatar"
                                            class="h-full w-full object-cover"
                                        >
                                        <svg
                                            v-else
                                            class="h-full w-full text-gray-300 dark:text-gray-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                    <span class="hidden ml-3 text-gray-700 dark:text-gray-200 text-sm font-medium lg:block">{{ user?.name }}</span>
                                    <ChevronDownIcon class="hidden flex-shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" aria-hidden="true" />
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user?.email }}</p>
                                </div>
                                <DropdownLink :href="route('profile.edit')"> My Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <main class="flex-1 py-8">
                 <div v-if="$page.props.flash.success || $page.props.flash.error" class="px-4 sm:px-6 lg:px-8 mb-4">
                     <div v-if="$page.props.flash.success" class="p-4 bg-green-100 text-green-700 rounded-md shadow-sm dark:bg-green-900 dark:text-green-300">
                         {{ $page.props.flash.success }}
                     </div>
                     <div v-if="$page.props.flash.error" class="p-4 bg-red-100 text-red-700 rounded-md shadow-sm dark:bg-red-900 dark:text-red-300">
                         {{ $page.props.flash.error }}
                     </div>
                 </div>

                 <div class="px-4 sm:px-6 lg:px-8">
                     <slot />
                 </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Mobile Sidebar Transition */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

/* Width Transition for Desktop Sidebar */
.transition-width {
    transition-property: width;
}
.transition-padding {
    transition-property: padding-left;
}
/* Ensure icons are centered when collapsed */
.lg\:flex.lg\:flex-col.lg\:fixed.lg\:inset-y-0.w-20 nav a,
.lg\:flex.lg\:flex-col.lg\:fixed.lg\:inset-y-0.w-20 nav button {
    justify-content: center;
}
</style>