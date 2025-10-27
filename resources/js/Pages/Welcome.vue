<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'; // <-- Use GuestLayout
import { Head, Link } from '@inertiajs/vue3';
import { AcademicCapIcon, BriefcaseIcon, CpuChipIcon } from '@heroicons/vue/24/outline';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const features = [
    {
        name: 'Student Visa Guidance',
        description: 'Navigate the complexities of studying abroad. Find universities, prepare documents, and apply confidently with AI assistance.',
        icon: AcademicCapIcon,
        href: route('public.courses.search'), // Link to course search
    },
    {
        name: 'Work Visa Opportunities',
        description: 'Explore international job markets. Build your CV, find relevant job postings, and connect with potential employers.',
        icon: BriefcaseIcon,
        href: route('public.jobs.search'), // Link to job search
    },
    {
        name: 'AI-Powered Profile Analysis',
        description: 'Get personalized insights. Our AI analyzes your profile to suggest suitable paths and improve your application success rate.',
        icon: CpuChipIcon,
        href: route('register'), // Encourage registration
    },
];

</script>

<template>
    <Head title="Welcome" />

    <GuestLayout>
        <section class="relative bg-gradient-to-br from-white via-brand-light to-red-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 overflow-hidden pt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-24 lg:py-32 text-center">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-gray-100 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Your Trusted Partner</span>
                        <span class="block text-brand-primary xl:inline"> for Going Abroad</span>
                    </h1>
                    <p class="mt-3 max-w-md mx-auto text-base text-gray-600 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                        A one-stop solution for visas, universities, and job applications. Simplify your international journey with AI-driven guidance and expert advice.
                    </p>
                    <div class="mt-8 max-w-md mx-auto sm:flex sm:justify-center md:mt-10 gap-4">
                        <Link :href="route('register')" class="btn btn-primary text-base md:text-lg w-full sm:w-auto">
                            Get Started
                        </Link>
                        <Link :href="route('public.courses.search')" class="btn btn-secondary text-base md:text-lg w-full sm:w-auto mt-3 sm:mt-0">
                            Explore Services
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-base font-semibold tracking-wide uppercase text-brand-primary">Our Core Features</h2>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-gray-100 sm:text-4xl">
                        Everything You Need in One Platform
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="feature in features" :key="feature.name" class="card text-center p-6 hover:shadow-xl transition-shadow">
                         <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary/10 text-brand-primary mx-auto mb-4">
                            <component :is="feature.icon" class="h-6 w-6" aria-hidden="true" />
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ feature.name }}</h3>
                        <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                            {{ feature.description }}
                        </p>
                        <div class="mt-4">
                            <Link :href="feature.href" class="text-sm font-medium text-brand-primary hover:text-brand-primary/80">
                                Learn More <span aria-hidden="true">&rarr;</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        </GuestLayout>
</template>

<style scoped>
/* Scoped styles specific to Welcome page if needed */
.btn-lg { /* Example size adjustment */
    padding: 0.8rem 2rem;
}
</style>