<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PublicFooter from '@/Components/PublicFooter.vue'; // Assuming this exists
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const showingMobileMenu = ref(false);

// For the search input
const searchTerm = ref('');

/**
 * Submits the search form, redirecting to the public university search page.
 */
const submitSearch = () => {
    // Navigate to the public university search page with the search term
    // THIS IS THE CORRECT WEB ROUTE NAME
    router.get(
        route('public.universities.search'), 
        { search: searchTerm.value }, 
        { preserveState: true } 
    );
};

// --- CORRECTED navLinks using web route names ---
const navLinks = [
    // Corrected the href to use the public search route name
    { name: 'Universities', href: route('public.universities.search'), current: route().current('public.universities.search') },
    // Corrected the href to use the public search route name
    { name: 'Courses', href: route('public.courses.search'), current: route().current('public.courses.search') },
    // Jobs route does not exist in web.php, disabling for now.
    { name: 'Jobs', href: '#', current: false, disabled: true },
];
// --- END CORRECTION ---
</script>

<template>
    <div class="flex flex-col min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('welcome')">
                                <ApplicationLogo class="block h-9 w-auto" />
                            </Link>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <Link
                                v-for="link in navLinks"
                                :key="link.name"
                                :href="link.disabled ? '#' : link.href"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    link.current
                                        ? 'border-brand-primary text-gray-900 dark:text-gray-100 focus:outline-none focus:border-brand-primary'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700',
                                    link.disabled ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed line-through' : ''
                                ]"
                                :aria-disabled="link.disabled"
                                :tabindex="link.disabled ? -1 : undefined"
                            >
                                {{ link.name }}
                            </Link>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <form @submit.prevent="submitSearch" class="flex items-center mr-6">
                            <TextInput
                                v-model="searchTerm"
                                type="search"
                                placeholder="Search universities..."
                                class="w-48 h-8 text-sm"
                            />
                            <PrimaryButton type="submit" class="ms-2 h-8 text-xs py-0">Search</PrimaryButton>
                        </form>
                        <template v-if="user">
                            <Link :href="route('dashboard')" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-primary dark:hover:text-brand-primary transition duration-150 ease-in-out">
                                Dashboard
                            </Link>
                         </template>
                         <template v-else>
                            <Link :href="route('login')" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-primary dark:hover:text-brand-primary transition duration-150 ease-in-out mr-4">
                                Log in
                            </Link>
                            <Link v-if="page.props.canRegister" :href="route('register')" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150">
                                Register
                            </Link>
                         </template>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingMobileMenu = !showingMobileMenu"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        >
                            <Bars3Icon v-if="!showingMobileMenu" class="h-6 w-6" />
                            <XMarkIcon v-else class="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ block: showingMobileMenu, hidden: !showingMobileMenu }" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name + '-mobile'"
                        :href="link.disabled ? '#' : link.href"
                        :class="[
                            'block w-full ps-3 pe-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out',
                            link.current
                                ? 'border-brand-primary text-brand-primary bg-brand-50 dark:bg-brand-900/50 focus:outline-none focus:text-brand-primary focus:bg-brand-100 focus:border-brand-primary'
                                : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600',
                            link.disabled ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed line-through' : ''
                        ]"
                        :aria-disabled="link.disabled"
                        :tabindex="link.disabled ? -1 : undefined"
                        @click="showingMobileMenu = false"
                    >
                        {{ link.name }}
                    </Link>
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <template v-if="user">
                         <div class="px-4 mb-3">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ user.name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ user.email }}</div>
                         </div>
                         <div class="space-y-1">
                              <Link :href="route('dashboard')" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600" @click="showingMobileMenu = false">Dashboard</Link>
                              <Link :href="route('logout')" method="post" as="button" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600" @click="showingMobileMenu = false">Log Out</Link>
                         </div>
                    </template>
                    <template v-else>
                         <div class="space-y-1">
                            <Link :href="route('login')" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600" @click="showingMobileMenu = false">Log in</Link>
                            <Link v-if="page.props.canRegister" :href="route('register')" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600" @click="showingMobileMenu = false">Register</Link>
                         </div>
                    </template>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            <slot /> 
        </main>

        <PublicFooter /> 
    </div>
</template>