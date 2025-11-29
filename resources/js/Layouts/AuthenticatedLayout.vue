<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import MobileBottomNav from '@/Components/MobileBottomNav.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import PWAInstallPrompt from '@/Components/PWAInstallPrompt.vue';
import NetworkStatus from '@/Components/NetworkStatus.vue';
import SlowConnectionWarning from '@/Components/SlowConnectionWarning.vue';
import { Link } from '@inertiajs/vue3';
import { 
    SparklesIcon,
    UserIcon,
    GlobeAltIcon,
    BellIcon,
    LightBulbIcon,
    DocumentMagnifyingGlassIcon,
    WalletIcon,
    UserGroupIcon,
    CalendarIcon,
    ChatBubbleLeftRightIcon,
    TicketIcon,
    QuestionMarkCircleIcon,
    ArrowRightOnRectangleIcon,
    ChartBarIcon,
    Cog6ToothIcon,
    BriefcaseIcon,
    DocumentTextIcon,
    HomeIcon,
    BuildingOfficeIcon,
    MapIcon,
    CurrencyDollarIcon,
    UserCircleIcon,
    ShieldCheckIcon,
    UsersIcon,
    DocumentCheckIcon,
    PaperAirplaneIcon
} from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const page = usePage();

const isAdmin = computed(() => page.props.auth?.user?.role?.slug === 'admin');
const isAgency = computed(() => page.props.auth?.user?.role?.slug === 'agency');
const isConsultant = computed(() => page.props.auth?.user?.role?.slug === 'consultant');

const leaveImpersonation = () => {
    router.post(route('admin.impersonation.leave'), {}, {
        preserveScroll: false,
    });
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Impersonation Banner -->
            <div v-if="$page.props.auth.user.impersonating" class="bg-red-600 text-white shadow-inner">
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
                    <form @submit.prevent="leaveImpersonation" class="flex items-center">
                        <button type="submit" class="inline-flex items-center px-4 py-1.5 rounded-md bg-black/25 hover:bg-black/35 transition font-semibold text-xs tracking-wide">
                            Exit & Restore Admin
                        </button>
                    </form>
                </div>
            </div>
            <nav
                class="border-b border-gray-100 bg-white sticky top-0 z-40"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-3 sm:px-4 md:px-6 lg:px-8">
                    <div class="flex h-14 sm:h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="h-10 w-auto" />
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
                                    
                                    <!-- Services Dropdown -->
                                    <Dropdown align="left" width="48" class="hidden sm:block">
                                        <template #trigger>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                                :class="{ 'border-b-2 border-indigo-400 text-gray-900': route().current('services.*') || route().current('user.applications.*') }"
                                            >
                                                <Cog6ToothIcon class="w-5 h-5 mr-1.5" />
                                                Services
                                                <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <DropdownLink :href="route('services.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <DocumentMagnifyingGlassIcon class="w-5 h-5" />
                                                </template>
                                                Browse
                                            </DropdownLink>
                                            <DropdownLink :href="route('user.applications.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <DocumentTextIcon class="w-5 h-5" />
                                                </template>
                                                My Applications
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                
                                <NavLink
                                    :href="route('jobs.index')"
                                    :active="route().current('jobs.*')"
                                >
                                    Jobs
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center sm:gap-2">
                            <!-- Language Switcher -->
                            <LanguageSwitcher />
                            
                            <!-- Notification Bell -->
                            <NotificationBell />
                            
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
                                        <DropdownLink :href="route('profile.edit')" icon-class="text-ocean-600">
                                            <template #icon>
                                                <UserIcon class="w-5 h-5" />
                                            </template>
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.assessment.show')" icon-class="text-heritage-600">
                                            <template #icon>
                                                <SparklesIcon class="w-5 h-5" />
                                            </template>
                                            AI
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.public.settings')" icon-class="text-sky-600">
                                            <template #icon>
                                                <GlobeAltIcon class="w-5 h-5" />
                                            </template>
                                            Public
                                        </DropdownLink>
                                        <DropdownLink :href="route('notification-preferences.index')" icon-class="text-gold-600">
                                            <template #icon>
                                                <BellIcon class="w-5 h-5" />
                                            </template>
                                            Notifications
                                        </DropdownLink>
                                        
                                        <!-- Admin Menu -->
                                        <template v-if="isAdmin">
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <div class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold tracking-wider">Admin</div>
                                            <DropdownLink :href="route('admin.dashboard')" icon-class="text-ocean-600">
                                                <template #icon>
                                                    <ChartBarIcon class="w-5 h-5" />
                                                </template>
                                                Dashboard
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.service-modules.index')" icon-class="text-heritage-600">
                                                <template #icon>
                                                    <Cog6ToothIcon class="w-5 h-5" />
                                                </template>
                                                Services
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.visa-requirements.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <DocumentTextIcon class="w-5 h-5" />
                                                </template>
                                                Requirements
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.services.index')" icon-class="text-growth-600">
                                                <template #icon>
                                                    <Cog6ToothIcon class="w-5 h-5" />
                                                </template>
                                                Management
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.hotels.index')" icon-class="text-sunrise-600">
                                                <template #icon>
                                                    <BuildingOfficeIcon class="w-5 h-5" />
                                                </template>
                                                Hotels
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.visa-applications.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <DocumentTextIcon class="w-5 h-5" />
                                                </template>
                                                Applications
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.jobs.index')" icon-class="text-heritage-600">
                                                <template #icon>
                                                    <BriefcaseIcon class="w-5 h-5" />
                                                </template>
                                                Jobs
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.applications.index')" icon-class="text-ocean-600">
                                                <template #icon>
                                                    <DocumentTextIcon class="w-5 h-5" />
                                                </template>
                                                Applications
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.users.index')" icon-class="text-growth-600">
                                                <template #icon>
                                                    <UserGroupIcon class="w-5 h-5" />
                                                </template>
                                                Users
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.analytics.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <ChartBarIcon class="w-5 h-5" />
                                                </template>
                                                Analytics
                                            </DropdownLink>
                                            <DropdownLink :href="route('seo-settings.index')" icon-class="text-heritage-600">
                                                <template #icon>
                                                    <DocumentMagnifyingGlassIcon class="w-5 h-5" />
                                                </template>
                                                SEO
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.settings.index')" icon-class="text-gold-600">
                                                <template #icon>
                                                    <Cog6ToothIcon class="w-5 h-5" />
                                                </template>
                                                Settings
                                            </DropdownLink>
                                                <DropdownLink :href="route('admin.documents.verify.index')" icon-class="text-sky-600">
                                                    <template #icon>
                                                        <DocumentTextIcon class="w-5 h-5" />
                                                    </template>
                                                    Documents
                                                </DropdownLink>
                                                <DropdownLink :href="route('admin.notifications.index')" icon-class="text-gold-600">
                                                    <template #icon>
                                                        <BellIcon class="w-5 h-5" />
                                                    </template>
                                                    Notifications
                                                </DropdownLink>
                                        </template>
                                        
                                        <!-- Agency Menu -->
                                        <template v-else-if="isAgency">
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <div class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold tracking-wider">Agency Panel</div>
                                            <DropdownLink :href="route('agency.dashboard')" icon-class="text-ocean-600">
                                                <template #icon>
                                                    <ChartBarIcon class="w-5 h-5" />
                                                </template>
                                                Dashboard
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.country-assignments.index')" icon-class="text-growth-600">
                                                <template #icon>
                                                    <MapIcon class="w-5 h-5" />
                                                </template>
                                                Countries
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.applications.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <DocumentTextIcon class="w-5 h-5" />
                                                </template>
                                                Applications
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.earnings.index')" icon-class="text-green-600">
                                                <template #icon>
                                                    <CurrencyDollarIcon class="w-5 h-5" />
                                                </template>
                                                Earnings
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.profile.show')" icon-class="text-indigo-600">
                                                <template #icon>
                                                    <UserCircleIcon class="w-5 h-5" />
                                                </template>
                                                Profile
                                            </DropdownLink>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <DropdownLink :href="route('agency.team.index')" icon-class="text-purple-600">
                                                <template #icon>
                                                    <UsersIcon class="w-5 h-5" />
                                                </template>
                                                Team
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.visa-management.index')" icon-class="text-blue-600">
                                                <template #icon>
                                                    <DocumentCheckIcon class="w-5 h-5" />
                                                </template>
                                                Visa Management
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.flight-requests.index')" icon-class="text-orange-600">
                                                <template #icon>
                                                    <PaperAirplaneIcon class="w-5 h-5" />
                                                </template>
                                                Flight Requests
                                            </DropdownLink>
                                            <DropdownLink :href="route('agency.verification.index')" icon-class="text-teal-600">
                                                <template #icon>
                                                    <ShieldCheckIcon class="w-5 h-5" />
                                                </template>
                                                Verification
                                            </DropdownLink>
                                        </template>
                                        
                                        <!-- Regular User Menu -->
                                        <template v-else>
                                            <DropdownLink :href="route('suggestions.index')" icon-class="text-heritage-600">
                                                <template #icon>
                                                    <LightBulbIcon class="w-5 h-5" />
                                                </template>
                                                Suggestions
                                            </DropdownLink>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <DropdownLink :href="route('document-scanner.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <DocumentMagnifyingGlassIcon class="w-5 h-5" />
                                                </template>
                                                Scanner
                                            </DropdownLink>
                                            <DropdownLink :href="route('wallet.index')" icon-class="text-growth-600">
                                                <template #icon>
                                                    <WalletIcon class="w-5 h-5" />
                                                </template>
                                                Wallet
                                            </DropdownLink>
                                            <DropdownLink :href="route('referral.index')" icon-class="text-ocean-600">
                                                <template #icon>
                                                    <UserGroupIcon class="w-5 h-5" />
                                                </template>
                                                Referrals
                                            </DropdownLink>
                                            <DropdownLink :href="route('appointments.index')" icon-class="text-sky-600">
                                                <template #icon>
                                                    <CalendarIcon class="w-5 h-5" />
                                                </template>
                                                Appointments
                                            </DropdownLink>
                                            <DropdownLink :href="route('support.index')" icon-class="text-sunrise-600">
                                                <template #icon>
                                                    <ChatBubbleLeftRightIcon class="w-5 h-5" />
                                                </template>
                                                Support
                                            </DropdownLink>
                                            <DropdownLink :href="route('events.index')" icon-class="text-heritage-600">
                                                <template #icon>
                                                    <TicketIcon class="w-5 h-5" />
                                                </template>
                                                Events
                                            </DropdownLink>
                                            <DropdownLink :href="route('faqs.index')" icon-class="text-ocean-600">
                                                <template #icon>
                                                    <QuestionMarkCircleIcon class="w-5 h-5" />
                                                </template>
                                                FAQs
                                            </DropdownLink>
                                        </template>
                                        
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="text-red-600 hover:text-red-800"
                                            icon-class="text-red-600"
                                        >
                                            <template #icon>
                                                <ArrowRightOnRectangleIcon class="w-5 h-5" />
                                            </template>
                                            Log Out
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
                        
                        <ResponsiveNavLink :href="route('services.index')" :active="route().current('services.*')" icon-class="text-sky-600">
                            <template #icon>
                                <Cog6ToothIcon class="w-5 h-5" />
                            </template>
                            Services
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink :href="route('user.applications.index')" :active="route().current('user.applications.*')" icon-class="text-sky-600">
                            <template #icon>
                                <DocumentTextIcon class="w-5 h-5" />
                            </template>
                            My Applications
                        </ResponsiveNavLink>
                        
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
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Admin</div>
                            </div>
                            <ResponsiveNavLink :href="route('admin.dashboard')" icon-class="text-ocean-600">
                                <template #icon>
                                    <ChartBarIcon class="w-5 h-5" />
                                </template>
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.service-modules.index')" icon-class="text-heritage-600">
                                <template #icon>
                                    <Cog6ToothIcon class="w-5 h-5" />
                                </template>
                                Services
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.visa-requirements.index')" icon-class="text-sky-600">
                                <template #icon>
                                    <DocumentTextIcon class="w-5 h-5" />
                                </template>
                                Requirements
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.services.index')" icon-class="text-growth-600">
                                <template #icon>
                                    <Cog6ToothIcon class="w-5 h-5" />
                                </template>
                                Management
                            </ResponsiveNavLink>
                            <div class="px-3 py-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Services</div>
                            </div>
                            <ResponsiveNavLink :href="route('admin.hotels.index')" icon-class="text-sunrise-600">
                                <template #icon>
                                    <BuildingOfficeIcon class="w-5 h-5" />
                                </template>
                                Hotels
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.visa-applications.index')" icon-class="text-sky-600">
                                <template #icon>
                                    <DocumentTextIcon class="w-5 h-5" />
                                </template>
                                Applications
                            </ResponsiveNavLink>
                            <div class="px-3 py-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Management</div>
                            </div>
                            <ResponsiveNavLink :href="route('admin.jobs.index')" icon-class="text-heritage-600">
                                <template #icon>
                                    <BriefcaseIcon class="w-5 h-5" />
                                </template>
                                Jobs
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.applications.index')" icon-class="text-ocean-600">
                                <template #icon>
                                    <DocumentTextIcon class="w-5 h-5" />
                                </template>
                                Applications
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.users.index')" icon-class="text-growth-600">
                                <template #icon>
                                    <UserGroupIcon class="w-5 h-5" />
                                </template>
                                Users
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.analytics.index')" icon-class="text-sky-600">
                                <template #icon>
                                    <ChartBarIcon class="w-5 h-5" />
                                </template>
                                Analytics
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('seo-settings.index')" icon-class="text-heritage-600">
                                <template #icon>
                                    <DocumentMagnifyingGlassIcon class="w-5 h-5" />
                                </template>
                                SEO
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.settings.index')" icon-class="text-gold-600">
                                <template #icon>
                                    <Cog6ToothIcon class="w-5 h-5" />
                                </template>
                                Settings
                            </ResponsiveNavLink>
                        </template>
                        
                        <!-- Agency Section -->
                        <template v-else-if="isAgency">
                            <div class="border-t border-gray-200 my-2"></div>
                            <div class="px-3 py-2">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Agency Panel</div>
                            </div>
                            <ResponsiveNavLink :href="route('agency.dashboard')" icon-class="text-ocean-600">
                                <template #icon>
                                    <ChartBarIcon class="w-5 h-5" />
                                </template>
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.country-assignments.index')" icon-class="text-growth-600">
                                <template #icon>
                                    <MapIcon class="w-5 h-5" />
                                </template>
                                Countries
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.applications.index')" icon-class="text-sky-600">
                                <template #icon>
                                    <DocumentTextIcon class="w-5 h-5" />
                                </template>
                                Applications
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.earnings.index')" icon-class="text-green-600">
                                <template #icon>
                                    <CurrencyDollarIcon class="w-5 h-5" />
                                </template>
                                Earnings
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.profile.show')" icon-class="text-indigo-600">
                                <template #icon>
                                    <UserCircleIcon class="w-5 h-5" />
                                </template>
                                Profile
                            </ResponsiveNavLink>
                            <div class="border-t border-gray-200 my-2"></div>
                            <ResponsiveNavLink :href="route('agency.team.index')" icon-class="text-purple-600">
                                <template #icon>
                                    <UsersIcon class="w-5 h-5" />
                                </template>
                                Team
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.visa-management.index')" icon-class="text-blue-600">
                                <template #icon>
                                    <DocumentCheckIcon class="w-5 h-5" />
                                </template>
                                Visa Management
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.flight-requests.index')" icon-class="text-orange-600">
                                <template #icon>
                                    <PaperAirplaneIcon class="w-5 h-5" />
                                </template>
                                Flight Requests
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('agency.verification.index')" icon-class="text-teal-600">
                                <template #icon>
                                    <ShieldCheckIcon class="w-5 h-5" />
                                </template>
                                Verification
                            </ResponsiveNavLink>
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
                            <ResponsiveNavLink :href="route('profile.edit')" icon-class="text-ocean-600">
                                <template #icon>
                                    <UserIcon class="w-5 h-5" />
                                </template>
                                Profile
                            </ResponsiveNavLink>
                            
                            <!-- Regular User Menu -->
                            <template v-if="!isAdmin">
                                <div class="border-t border-gray-200 my-2"></div>
                                <ResponsiveNavLink :href="route('wallet.index')" icon-class="text-growth-600">
                                    <template #icon>
                                        <WalletIcon class="w-5 h-5" />
                                    </template>
                                    Wallet
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('referral.index')" icon-class="text-ocean-600">
                                    <template #icon>
                                        <UserGroupIcon class="w-5 h-5" />
                                    </template>
                                    Referrals
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('appointments.index')" icon-class="text-sky-600">
                                    <template #icon>
                                        <CalendarIcon class="w-5 h-5" />
                                    </template>
                                    Appointments
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('support.index')" icon-class="text-sunrise-600">
                                    <template #icon>
                                        <ChatBubbleLeftRightIcon class="w-5 h-5" />
                                    </template>
                                    Support
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('events.index')" icon-class="text-heritage-600">
                                    <template #icon>
                                        <TicketIcon class="w-5 h-5" />
                                    </template>
                                    Events
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('faqs.index')" icon-class="text-ocean-600">
                                    <template #icon>
                                        <QuestionMarkCircleIcon class="w-5 h-5" />
                                    </template>
                                    FAQs
                                </ResponsiveNavLink>
                            </template>
                            
                            <div class="border-t border-gray-200 my-2"></div>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                icon-class="text-red-600"
                            >
                                <template #icon>
                                    <ArrowRightOnRectangleIcon class="w-5 h-5" />
                                </template>
                                Log Out
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

        <!-- PWA Components -->
        <NetworkStatus />
        <PWAInstallPrompt />
        <SlowConnectionWarning />
    </div>
</template>
