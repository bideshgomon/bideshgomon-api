<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    HomeIcon,
    BuildingOfficeIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    ChevronDownIcon,
    Bars3Icon,
    XMarkIcon,
    UserGroupIcon,
    CogIcon,
    ClipboardDocumentCheckIcon,
    IdentificationIcon,
} from '@heroicons/vue/24/outline';
import AdminNavLink from "@/Components/AdminNavLink.vue"; 
import FlashMessage from "@/Components/FlashMessage.vue";

const showingNavigationDropdown = ref(false);
const page = usePage();

</script>

<template>
    <div>
        <FlashMessage />

        <div class="min-h-screen bg-gray-100 flex">
            <aside 
                class="w-64 bg-gray-800 text-gray-200 flex-shrink-0 sm:block" 
                :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
            >
                <div class="flex items-center justify-center h-16 bg-gray-900 shadow-md">
                    <Link :href="route('dashboard')">
                        <ApplicationLogo class="block h-9 w-auto" />
                    </Link>
                </div>
                
                <nav class="mt-4">
                    <AdminNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                        <HomeIcon class="h-5 w-5 mr-3" />
                        Dashboard
                    </AdminNavLink>
                    
                    <div class="px-4 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Management
                    </div>
                    
                    <AdminNavLink :href="route('admin.universities.index')" :active="route().current('admin.universities.*')">
                        <BuildingOfficeIcon class="h-5 w-5 mr-3" />
                        Universities
                    </AdminNavLink>
                    <AdminNavLink :href="route('admin.courses.index')" :active="route().current('admin.courses.*')">
                        <AcademicCapIcon class="h-5 w-5 mr-3" />
                        Courses
                    </AdminNavLink>
                    <AdminNavLink :href="route('admin.job-categories.index')" :active="route().current('admin.job-categories.*')">
                        <BriefcaseIcon class="h-5 w-5 mr-3" />
                        Job Categories
                    </AdminNavLink>
                    <AdminNavLink :href="route('admin.jobs.index')" :active="route().current('admin.jobs.*')">
                        <BriefcaseIcon class="h-5 w-5 mr-3" />
                        Job Postings
                    </AdminNavLink>
                    
                    <AdminNavLink :href="route('admin.consultation-services.index')" :active="route().current('admin.consultation-services.*')">
                        <ClipboardDocumentCheckIcon class="h-5 w-5 mr-3" />
                        Consultation Services
                    </AdminNavLink>
                    
                    <AdminNavLink :href="route('admin.consultants.index')" :active="route().current('admin.consultants.*')">
                        <IdentificationIcon class="h-5 w-5 mr-3" />
                        Consultants
                    </AdminNavLink>

                    <div class="px-4 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        System
                    </div>
                    <AdminNavLink href="#"> <UserGroupIcon class="h-5 w-5 mr-3" />
                        Users
                    </AdminNavLink>
                    <AdminNavLink href="#"> <CogIcon class="h-5 w-5 mr-3" />
                        Settings
                    </AdminNavLink>

                </nav>
            </aside>

            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <div class="flex items-center sm:hidden">
                                    <button
                                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                    >
                                        <Bars3Icon class="h-6 w-6" :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" />
                                        <XMarkIcon class="h-6 w-6" :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" />
                                    </button>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <div class="ms-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                                >
                                                    {{ $page.props.auth.user.name }}

                                                    <ChevronDownIcon class="ms-2 -me-0.5 h-4 w-4" />
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('dashboard')"> User Dashboard </DropdownLink>
                                            <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button">
                                                Log Out
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <header class="bg-white shadow" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <main class="flex-1 p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>