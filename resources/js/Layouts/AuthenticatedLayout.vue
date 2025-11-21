<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import MobileBottomNav from '@/Components/MobileBottomNav.vue';
import { Link } from '@inertiajs/vue3';
import { SparklesIcon } from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const page = usePage();

const isAdmin = computed(() => page.props.auth?.user?.role?.slug === 'admin');
const isAgency = computed(() => page.props.auth?.user?.role?.slug === 'agency');
const isConsultant = computed(() => page.props.auth?.user?.role?.slug === 'consultant');
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Impersonation Banner -->
            <div v-if="$page.props.auth.user.impersonating" class="bg-gradient-to-r from-amber-600 via-orange-600 to-red-600 text-white shadow-inner">
                <div class="max-w-7xl mx-auto px-4 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-[13px]">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold tracking-wide uppercase">Impersonation Mode</span>
                            <span class="px-2 py-0.5 rounded bg-black/20 text-xs font-medium">SECURITY NOTICE</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="opacity-90">Acting as:</span>
                            <span class="font-semibold">{{ $page.props.auth.user.name }}</span>
                        </div>
                        <div v-if="$page.props.auth.user.impersonator" class="flex items-center space-x-2">
                            <span class="opacity-90">Original Admin:</span>
                            <span class="font-medium">{{ $page.props.auth.user.impersonator.name }}</span>
                            <span class="px-2 py-0.5 rounded bg-black/20 text-xs">ID {{ $page.props.auth.user.impersonator.id }}</span>
                        </div>
                    </div>
                    <form :action="route('admin.impersonation.leave')" method="post" class="flex items-center">
                        <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                        <button type="submit" class="inline-flex items-center px-4 py-1.5 rounded-md bg-black/25 hover:bg-black/35 transition font-semibold text-xs tracking-wide">
                            Exit & Restore Admin
                        </button>
                    </form>
                </div>
            </div>
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="flex items-center hover:opacity-80 transition">
                                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 p-1.5 rounded-lg shadow-sm">
                                        <SparklesIcon class="h-5 w-5 text-white" />
                                    </div>
                                    <div class="ml-2 hidden sm:block">
                                        <div class="text-sm font-bold text-gray-900">Bidesh Gomon</div>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                    <NavLink :href="route('documents.index')" :active="route().current('documents.*')">
                                        Documents
                                    </NavLink>
                                    <NavLink :href="route('notifications.index')" :active="route().current('notifications.*')">
                                        <span class="inline-flex items-center gap-1">
                                            Notifications
                                            <span v-if="$page.props.auth?.user?.unread_notifications > 0" class="ml-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-600 text-white">
                                                {{ $page.props.auth.user.unread_notifications }}
                                            </span>
                                        </span>
                                    </NavLink>
                                
                                <!-- Services Dropdown -->
                                <div class="relative">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out h-16">
                                                Services
                                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <DropdownLink :href="route('flight-requests.create')">✈️ Flight Requests</DropdownLink>
                                            <DropdownLink :href="route('hotels.index')">🏨 Hotel Bookings</DropdownLink>
                                            <DropdownLink :href="route('visa.index')">🛂 Visa Services</DropdownLink>
                                            <DropdownLink :href="route('translation.index')">🌐 Translation</DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                                
                                <NavLink
                                    :href="route('jobs.index')"
                                    :active="route().current('jobs.*')"
                                >
                                    Jobs
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">👤 Profile</DropdownLink>
                                        <DropdownLink :href="route('profile.assessment.show')">✨ AI Assessment</DropdownLink>
                                        <DropdownLink :href="route('profile.public.settings')">🌐 Public Profile</DropdownLink>
                                        
                                        <!-- Admin Menu -->
                                        <template v-if="isAdmin">
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <div class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold tracking-wider">Admin Panel</div>
                                            <DropdownLink :href="route('admin.dashboard')">📊 Dashboard</DropdownLink>
                                            <DropdownLink :href="route('admin.services.index')">🎯 Service Management</DropdownLink>
                                            <DropdownLink :href="route('admin.hotels.index')">🏨 Hotels Management</DropdownLink>
                                            <DropdownLink :href="route('admin.visa-applications.index')">🛂 Visa Applications</DropdownLink>
                                            <DropdownLink :href="route('admin.jobs.index')">💼 Job Postings</DropdownLink>
                                            <DropdownLink :href="route('admin.applications.index')">📝 Job Applications</DropdownLink>
                                            <DropdownLink :href="route('admin.users.index')">👥 User Management</DropdownLink>
                                            <DropdownLink :href="route('admin.analytics.index')">📈 Analytics & Reports</DropdownLink>
                                            <DropdownLink :href="route('seo-settings.index')">🔍 SEO Settings</DropdownLink>
                                            <DropdownLink :href="route('admin.settings.index')">⚙️ Platform Settings</DropdownLink>
                                                <DropdownLink :href="route('admin.documents.verify.index')">📄 Verify Documents</DropdownLink>
                                                <DropdownLink :href="route('admin.notifications.index')">🔔 Admin Notifications</DropdownLink>
                                        </template>
                                        
                                        <!-- Regular User Menu -->
                                        <template v-else>
    <DropdownLink :href="route('suggestions.index')">✨ Smart Suggestions</DropdownLink>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <DropdownLink :href="route('wallet.index')">💰 My Wallet</DropdownLink>
                                            <DropdownLink :href="route('referral.index')">🔗 Referrals</DropdownLink>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">My Services</div>
                                            <DropdownLink :href="route('flight-requests.index')">✈️ My Flights</DropdownLink>
                                            <DropdownLink :href="route('hotels.my-bookings')">🏨 My Hotels</DropdownLink>
                                            <DropdownLink :href="route('visa.my-applications')">🛂 My Visas</DropdownLink>
                                            <DropdownLink :href="route('translation.my-requests')">🌐 My Translations</DropdownLink>
                                            <DropdownLink :href="route('jobs.my-applications')">📝 My Applications</DropdownLink>
                                        </template>
                                        
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            🚪 Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        
                        <div class="px-3 py-2">
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Services</div>
                        </div>
                        <ResponsiveNavLink :href="route('flight-requests.create')">✈️ Flight Requests</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('hotels.index')">🏨 Hotel Bookings</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('visa.index')">🛂 Visa Services</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('translation.index')">🌐 Translation</ResponsiveNavLink>
                        
                        <ResponsiveNavLink
                            :href="route('jobs.index')"
                            :active="route().current('jobs.*')"
                        >
                            Jobs
                        </ResponsiveNavLink>
                        
                        <!-- Admin Section -->
                        <template v-if="isAdmin">
                            <div class="border-t border-gray-200 my-2"></div>
                            <div class="px-3 py-2">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Admin Panel</div>
                            </div>
                            <ResponsiveNavLink :href="route('admin.dashboard')">📊 Dashboard</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.services.index')">🎯 Service Management</ResponsiveNavLink>
                            <div class="px-3 py-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Services</div>
                            </div>
                            <ResponsiveNavLink :href="route('admin.hotels.index')">🏨 Hotels</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.visa-applications.index')">🛂 Visa Applications</ResponsiveNavLink>
                            <div class="px-3 py-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Management</div>
                            </div>
                            <ResponsiveNavLink :href="route('admin.jobs.index')">💼 Job Postings</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.applications.index')">📝 Job Applications</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.users.index')">👥 Users</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.analytics.index')">📈 Analytics</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('seo-settings.index')">🔍 SEO</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.settings.index')">⚙️ Settings</ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                👤 Profile
                            </ResponsiveNavLink>
                            
                            <!-- Regular User Menu -->
                            <template v-if="!isAdmin">
                                <div class="border-t border-gray-200 my-2"></div>
                                <ResponsiveNavLink :href="route('wallet.index')">
                                    💰 My Wallet
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('referral.index')">
                                    🔗 Referrals
                                </ResponsiveNavLink>
                                
                                <div class="border-t border-gray-200 my-2"></div>
                                <div class="px-4 py-2 text-xs text-gray-400 uppercase">My Services</div>
                                <ResponsiveNavLink :href="route('flight-requests.index')">✈️ My Flights</ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('hotels.my-bookings')">🏨 My Hotels</ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('visa.my-applications')">🛂 My Visas</ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('translation.my-requests')">🌐 My Translations</ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('jobs.my-applications')">📝 My Applications</ResponsiveNavLink>
                            </template>
                            
                            <div class="border-t border-gray-200 my-2"></div>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                🚪 Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>

            <!-- Mobile Bottom Navigation -->
            <MobileBottomNav />
        </div>
    </div>
</template>
