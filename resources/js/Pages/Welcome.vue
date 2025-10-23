<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to BideshGomon" />

    <div class="bg-brand-light dark:bg-gray-900 text-brand-dark dark:text-gray-300 min-h-screen font-primary">
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm border-b border-brand-border dark:border-gray-700">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('welcome')">
                            <ApplicationLogo class="block h-10 w-auto" />
                        </Link>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="#" class="nav-link">Services</a>
                        <a href="#" class="nav-link">Consultants</a>
                        <a href="#" class="nav-link">Jobs</a>
                        <a href="#" class="nav-link">Blogs</a>
                        <a href="#" class="nav-link">About Us</a>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="route('dashboard')"
                                class="btn btn-secondary"
                            >
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-sm font-semibold text-gray-600 hover:text-brand-primary dark:text-gray-400 dark:hover:text-white mr-4"
                            >
                                Log in
                            </Link>
                            <Link
                                :href="route('register')"
                                class="btn btn-primary"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <section class="hero text-center">
                <h1 class="text-4xl md:text-5xl font-bold font-bengali">
                    আপনার বিদেশে যাত্রার বিশ্বস্ত সঙ্গী
                </h1>
                <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300">
                    ভিসা, ইউনিভার্সিটি, এবং চাকরির আবেদনে ওয়ান-স্টপ সল্যুশন।
                </p>
                <div class="flex justify-center gap-4 mt-8">
                    <Link :href="route('register')" class="btn btn-primary btn-lg">
                        Get Started
                    </Link>
                    <a href="#" class="btn btn-secondary btn-lg">Explore Services</a>
                </div>
            </section>
        </main>
    </div>
</template>

