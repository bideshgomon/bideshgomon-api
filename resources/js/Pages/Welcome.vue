<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import {
    ArrowRightIcon,
    LightBulbIcon,
    MagnifyingGlassIcon,
    LockClosedIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const features = [
    {
        name: 'AI-Powered Guidance',
        description: 'Our advanced AI analyzes your documents and profile to provide personalized advice.',
        icon: LightBulbIcon,
    },
    {
        name: 'Comprehensive Search',
        description: 'Search thousands of universities and courses from around the world to find your perfect match.',
        icon: MagnifyingGlassIcon,
    },
    {
        name: 'Data Privacy Control',
        description: 'You control your data. Grant access to consultants only when you are ready.',
        icon: LockClosedIcon,
    },
    {
        name: 'Streamlined Application',
        description: 'Manage all your applications, documents, and profile details in one secure dashboard.',
        icon: DocumentTextIcon,
    },
];
</script>

<template>
    <Head title="Welcome to BideshGomon" />
    <PublicLayout>
        <div class="relative bg-white dark:bg-gray-800 overflow-hidden">
             <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white dark:bg-gray-800 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <svg
                        class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white dark:text-gray-800 transform translate-x-1/2"
                        fill="currentColor"
                        viewBox="0 0 100 100"
                        preserveAspectRatio="none"
                        aria-hidden="true"
                    >
                        <polygon points="50,0 100,0 50,100 0,100" />
                    </svg>

                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">Your Journey</span>
                                <span class="block text-brand-primary xl:inline"> Abroad Starts Here</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-600 dark:text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Bidesh Gomon is your all-in-one platform for studying abroad. Find top universities, get AI-powered document analysis, and manage your applications with confidence.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start space-y-3 sm:space-y-0 sm:space-x-3">
                                <Link
                                    :href="route('register')"
                                    class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-brand-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition duration-150 ease-in-out shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                >
                                    Get started
                                    <ArrowRightIcon class="ml-2 -mr-1 h-5 w-5" aria-hidden="true" />
                                </Link>
                                <Link
                                    :href="route('universities.index')"
                                    class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-brand-primary bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition duration-150 ease-in-out"
                                >
                                    Browse Universities
                                </Link>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img
                    class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                    src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                    alt="International students studying"
                />
            </div>
        </div>

        <div class="py-16 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-base font-semibold text-brand-primary tracking-wide uppercase">Why Choose Us?</h2>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                        A Smarter Way to Study Abroad
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 lg:mx-auto">
                        Leverage cutting-edge technology and unparalleled support for your international education goals.
                    </p>
                </div>

                <div class="mt-12">
                    <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                        <div v-for="feature in features" :key="feature.name" class="relative group p-6 bg-white dark:bg-gray-900 rounded-lg shadow-md transition duration-300 ease-in-out hover:shadow-lg">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white shadow-lg">
                                    <component :is="feature.icon" class="h-6 w-6" aria-hidden="true" />
                                 </div>
                                <p class="ms-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    {{ feature.name }}
                                </p>
                            </dt>
                            <dd class="mt-2 ms-16 text-base text-gray-600 dark:text-gray-400">
                                {{ feature.description }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-brand-primary dark:bg-brand-primary">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    <span class="block">Ready to take the next step?</span>
                    <span classs="block text-brand-100">Create your free profile today.</span>
                </h2>
                <div class="mt-8 flex lg:mt-0 lg:shrink-0">
                    <div class="inline-flex rounded-md shadow">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-brand-primary bg-white hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-primary focus:ring-white transition duration-150 ease-in-out hover:shadow-md transform hover:-translate-y-0.5"
                        >
                            Register Now
                            <ArrowRightIcon class="ml-2 -mr-1 h-5 w-5" aria-hidden="true" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>