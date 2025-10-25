<template>
    <PublicLayout title="Welcome">

        <div class="relative bg-white dark:bg-gray-800 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-24 lg:py-32">
                    <div class="max-w-xl mx-auto text-center">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-gray-100 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Your Journey Abroad</span>
                            <span class="block xl:inline" style="color: var(--brand-primary);">Starts Here.</span>
                        </h1>
                        <p class="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            A comprehensive, all-in-one digital platform that simplifies the entire journey of migration, overseas education, and travel.
                        </p>
                        <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                            <div class="rounded-md shadow">
                                <Link :href="route('register')" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white md:py-4 md:text-lg md:px-10" style="background-color: var(--brand-primary); opacity: 0.9;">
                                    Get Started
                                </Link>
                            </div>
                            <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                                <Link :href="route('public.universities')" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md md:py-4 md:text-lg md:px-10" style="background-color: var(--brand-secondary); color: white; opacity: 0.9;">
                                    Find Universities
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12" style="background-color: var(--brand-light);">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-base font-semibold tracking-wide uppercase" style="color: var(--brand-primary);">Our Services</h2>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-gray-100 sm:text-4xl">
                        Everything You Need in One Platform
                    </p>
                </div>

                <div class="mt-10">
                    <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10 lg:grid-cols-3">

                        <div class="relative p-6 bg-white dark:bg-gray-700 rounded-lg shadow-md">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md text-white" style="background-color: var(--brand-secondary);">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /> </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Student Visa Service</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                Get AI-driven guidance and connect with consultants to find the right university and course.
                            </dd>
                        </div>

                        <div class="relative p-6 bg-white dark:bg-gray-700 rounded-lg shadow-md">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gray-700 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" /> </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">AI Guidance</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                Our AI analyzes your profile, suggests financial plans, and helps build a perfect CV.
                            </dd>
                        </div>

                        <div class="relative p-6 bg-white dark:bg-gray-700 rounded-lg shadow-md">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md text-white" style="background-color: var(--brand-primary);">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.075c0 1.313-.964 2.411-2.278 2.599-1.314.188-2.47-.935-2.47-2.278v-4.075a3.375 3.375 0 01-6.75 0v4.075c0 1.313-.964 2.411-2.278 2.599-1.314.188-2.47-.935-2.47-2.278v-4.075a3.375 3.375 0 016.75 0v4.075c0 1.313.964 2.411 2.278 2.599 1.314.188 2.47-.935 2.47-2.278v-4.075zM12 18.75c-.621 0-1.125-.504-1.125-1.125v-1.5c0-.621.504-1.125 1.125-1.125s1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125zM12 3.75c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125s1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125zM3.75 14.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125s1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125zM20.25 14.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125s1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125zM12 9.75c-3.866 0-7.5 1.68-7.5 3.75v.75c0 1.933 1.343 3.5 3 3.5h9c1.657 0 3-1.567 3-3.5v-.75c0-2.07-3.634-3.75-7.5-3.75zM12 9.75c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125s1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125z" /> </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Work Visa Service</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                Connect with agencies and employers. Build your CV and find opportunities abroad.
                            </dd>
                        </div>
                        </dl>
                </div>
            </div>
        </div>

    </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'; // Head is imported here now
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<style>
/* You can add specific styles here or ensure custom-style.css is loaded globally */
</style>