<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'; // Import logo

const showingNavigationDropdown = ref(false);
</script>

<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('welcome')">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </Link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <NavLink :href="route('welcome')" :active="route().current('welcome')">
                            Home
                        </NavLink>
                        <NavLink :href="route('public.universities')" :active="route().current('public.universities') || route().current('public.universities.show')">
                            Universities
                        </NavLink>
                        <NavLink :href="route('public.courses')" :active="route().current('public.courses') || route().current('public.courses.show')">
                            Courses
                        </NavLink>
                         <NavLink :href="route('public.jobs')" :active="route().current('public.jobs') || route().current('public.job.detail')">
                            Jobs
                        </NavLink>
                        </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                     <div v-if="$page.props.auth.user" class="ms-3 relative">
                         <Link :href="route('dashboard')" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</Link>
                     </div>
                     <template v-else>
                        <Link :href="route('login')" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</Link>
                        <Link v-if="$page.props.canRegister" :href="route('register')" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</Link>
                     </template>
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
                <ResponsiveNavLink :href="route('welcome')" :active="route().current('welcome')">
                    Home
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('public.universities')" :active="route().current('public.universities') || route().current('public.universities.show')">
                    Universities
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('public.courses')" :active="route().current('public.courses') || route().current('public.courses.show')">
                    Courses
                </ResponsiveNavLink>
                 <ResponsiveNavLink :href="route('public.jobs')" :active="route().current('public.jobs') || route().current('public.job.detail')">
                    Jobs
                </ResponsiveNavLink>
            </div>

            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                 <div v-if="$page.props.auth.user" class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ $page.props.auth.user.name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                 </div>
                 <div v-if="$page.props.auth.user" class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('dashboard')"> Dashboard </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </ResponsiveNavLink>
                 </div>
                 <template v-else>
                     <div class="space-y-1">
                        <ResponsiveNavLink :href="route('login')">
                            Log in
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.canRegister" :href="route('register')">
                            Register
                        </ResponsiveNavLink>
                     </div>
                 </template>
            </div>
        </div>
    </nav>
</template>