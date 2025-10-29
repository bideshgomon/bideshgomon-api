<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

// Define footer links structured for easier management
const footerLinks = {
    company: [
        { name: 'About Us', href: '#' },
        { name: 'Careers', href: '#' },
        { name: 'Press', href: '#' },
    ],
    services: [
        // --- CORRECTED Route Names ---
        { name: 'Universities', href: route('universities.index') },
        { name: 'Courses', href: route('courses.index') },
        { name: 'Jobs', href: route('jobs.index'), disabled: false }, // Use web route
        // --- END CORRECTION ---
        { name: 'Consultancy', href: '#' }, // Assuming this stays '#' for now
    ],
    legal: [
        { name: 'Privacy Policy', href: '#' },
        { name: 'Terms of Service', href: '#' },
    ],
};
</script>

<template>
    <footer class="bg-gray-800 dark:bg-black text-gray-300 dark:text-gray-400 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
                <div class="col-span-2 md:col-span-4 lg:col-span-1">
                     <Link href="/" class="mb-4 inline-block">
                        <ApplicationLogo class="block h-10 w-auto" :use-white-logo="true" />
                    </Link>
                    <p class="text-sm">
                        Connecting you to global opportunities in education, career, and beyond.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-100 dark:text-white tracking-wider uppercase mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li v-for="link in footerLinks.company" :key="link.name">
                            <Link :href="link.href" class="text-sm hover:text-white transition duration-150">{{ link.name }}</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-100 dark:text-white tracking-wider uppercase mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li v-for="link in footerLinks.services" :key="link.name">
                            <Link
                                :href="link.disabled ? '#' : link.href"
                                :class="['text-sm hover:text-white transition duration-150', link.disabled ? 'text-gray-500 cursor-not-allowed line-through' : '']"
                                :aria-disabled="link.disabled"
                                :tabindex="link.disabled ? -1 : undefined"
                            >
                                {{ link.name }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-100 dark:text-white tracking-wider uppercase mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li v-for="link in footerLinks.legal" :key="link.name">
                            <Link :href="link.href" class="text-sm hover:text-white transition duration-150">{{ link.name }}</Link>
                        </li>
                    </ul>
                </div>

                 <div>
                    <h3 class="text-sm font-semibold text-gray-100 dark:text-white tracking-wider uppercase mb-4">Contact</h3>
                     <ul class="space-y-2">
                         <li class="text-sm">Dhaka, Bangladesh</li>
                         <li class="text-sm">info@bideshgomon.com</li>
                     </ul>
                </div>
            </div>

            <div class="mt-12 border-t border-gray-700 dark:border-gray-800 pt-8 text-center">
                <p class="text-sm">
                    &copy; {{ new Date().getFullYear() }} Bidesh Gomon Project. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* Add specific styles if needed */
</style>