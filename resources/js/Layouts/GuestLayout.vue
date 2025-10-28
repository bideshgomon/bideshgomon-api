<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const user = computed(() => usePage().props.auth.user);
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('welcome')">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </Link>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <Link
                                :href="route('welcome')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                :class="route().current('welcome') ? 'border-brand-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700'"
                            >
                                Home
                            </Link>
                            <Link
                                :href="route('public.universities.search')"  
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                :class="route().current('public.universities.search') ? 'border-brand-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700'"
                            >
                                Universities
                            </Link>
                            <Link
                                :href="route('public.courses.search')"      
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                :class="route().current('public.courses.search') ? 'border-brand-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700'"
                            >
                                Courses
                            </Link>
                            </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <template v-if="user">
                            <Link
                                :href="route('dashboard')"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-brand-500"
                            >
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-brand-500"
                            >
                                Log in
                            </Link>
                            <Link
                                :href="route('register')"
                                class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-brand-500"
                            >
                                Register
                            </Link>
                        </template>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
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

            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <Link
                        :href="route('welcome')"
                        class="block w-full ps-3 pe-4 py-2 border-l-4 font-medium"
                        :class="route().current('welcome') ? 'border-brand-500 text-brand-600 bg-brand-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'"
                    >
                        Home
                    </Link>
                    <Link
                        :href="route('public.universities.search')"  
                        class="block w-full ps-3 pe-4 py-2 border-l-4 font-medium"
                        :class="route().current('public.universities.search') ? 'border-brand-500 text-brand-600 bg-brand-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'"
                    >
                        Universities
                    </Link>
                    <Link
                        :href="route('public.courses.search')"      
                        class="block w-full ps-3 pe-4 py-2 border-l-4 font-medium"
                        :class="route().current('public.courses.search') ? 'border-brand-500 text-brand-600 bg-brand-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'"
                    >
                        Courses
                    </Link>
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                        <template v-if="user">
                             <Link
                                :href="route('dashboard')"
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                Log in
                            </Link>
                            <Link
                                :href="route('register')"
                                class="mt-2 block font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            <slot />
        </main>

        <footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <ApplicationLogo class="block h-10 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            Your complete solution for studying abroad. Find universities, manage applications, and get AI-powered guidance.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase">Quick Links</h3>
                        <ul class="mt-4 space-y-2">
                            <li><Link :href="route('public.universities.search')" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Find Universities</Link></li> 
                            <li><Link :href="route('public.courses.search')" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Find Courses</Link></li>       
                            <li><Link href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">About Us</Link></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase">Legal</h3>
                        <ul class="mt-4 space-y-2">
                            <li><Link href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Privacy Policy</Link></li>
                            <li><Link href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Terms of Service</Link></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase">Contact</h3>
                        <ul class="mt-4 space-y-2">
                            <li class="text-sm text-gray-500 dark:text-gray-400">Dhaka, Bangladesh</li>
                            <li class="text-sm text-gray-500 dark:text-gray-400">info@bideshgomon.com</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ new Date().getFullYear() }} Bidesh Gomon Project. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>