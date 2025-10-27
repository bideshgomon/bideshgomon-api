<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'; // Icons for mobile menu

const showingMobileMenu = ref(false);

const publicNavLinks = [
    { name: 'Home', href: route('welcome'), current: route().current('welcome') },
    { name: 'Services', href: '#', current: false }, // Placeholder link
    { name: 'Universities', href: route('public.universities.search'), current: route().current('public.universities.search') },
    { name: 'Courses', href: route('public.courses.search'), current: route().current('public.courses.search') },
    { name: 'Jobs', href: route('public.jobs.search'), current: route().current('public.jobs.search') },
    { name: 'About Us', href: '#', current: false }, // Placeholder link
    { name: 'Contact', href: '#', current: false }, // Placeholder link
];
</script>

<template>
    <div class="min-h-screen bg-brand-light dark:bg-gray-900 text-brand-dark dark:text-gray-300 font-primary flex flex-col">
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm border-b border-brand-border dark:border-gray-700 shadow-sm">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex-shrink-0">
                        <Link :href="route('welcome')">
                            <ApplicationLogo class="block h-10 w-auto" />
                        </Link>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <Link
                            v-for="item in publicNavLinks"
                            :key="item.name"
                            :href="item.href"
                            :class="[
                                item.current ? 'border-brand-primary text-brand-primary font-semibold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-700 hover:text-gray-700 dark:hover:text-gray-300',
                                'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out'
                            ]"
                        >
                            {{ item.name }}
                        </Link>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" class="btn btn-secondary text-sm">Dashboard</Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-medium text-gray-500 hover:text-brand-primary dark:text-gray-400 dark:hover:text-white mr-4">Log in</Link>
                            <Link :href="route('register')" class="btn btn-primary text-sm">Register</Link>
                        </template>
                    </div>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingMobileMenu = !showingMobileMenu" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-primary">
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!showingMobileMenu" class="block h-6 w-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </nav>

            <div :class="{'block': showingMobileMenu, 'hidden': !showingMobileMenu}" class="sm:hidden border-t border-brand-border dark:border-gray-700">
                <div class="pt-2 pb-3 space-y-1">
                    <Link
                        v-for="item in publicNavLinks"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            item.current ? 'bg-brand-primary/10 border-brand-primary text-brand-primary' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-700 hover:text-gray-800 dark:hover:text-white',
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition'
                        ]"
                         @click="showingMobileMenu = false"
                    >
                        {{ item.name }}
                    </Link>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700 px-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="btn btn-secondary w-full text-center">Dashboard</Link>
                    </template>
                    <template v-else>
                        <div class="flex items-center justify-between gap-4">
                            <Link :href="route('login')" class="flex-1 btn btn-outline text-center">Log in</Link>
                            <Link :href="route('register')" class="flex-1 btn btn-primary text-center">Register</Link>
                        </div>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <div v-if="$page.component.startsWith('Auth/')" class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                 <div class="mb-6">
                    <Link href="/">
                        <ApplicationLogo class="w-24 h-auto" />
                    </Link>
                </div>
                <div class="w-full sm:max-w-md px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                    <slot />
                </div>
            </div>
            <div v-else>
                <slot />
            </div>
        </main>

        <footer class="footer bg-white dark:bg-gray-800 border-t border-brand-border dark:border-gray-700 mt-auto">
            <div class="footer-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="footer-section">
                     <Link href="/">
                        <ApplicationLogo class="footer-logo-img mb-4" />
                    </Link>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Simplifying your journey for migration, overseas education, and travel with AI-driven guidance.
                    </p>
                </div>

                <div class="footer-section">
                    <h4>Core Services</h4>
                    <ul>
                        <li><Link href="#">Work Visa</Link></li>
                        <li><Link :href="route('public.courses.search')">Student Visa</Link></li>
                        <li><Link href="#">Tourist Visa</Link></li>
                        <li><Link href="#">AI Profile Analysis</Link></li>
                        <li><Link href="#">CV Builder</Link></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><Link href="#">About Us</Link></li>
                        <li><Link href="#">Contact Us</Link></li>
                        <li><Link href="#">Our Agencies</Link></li>
                        <li><Link href="#">Careers</Link></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><Link href="#">Privacy Policy</Link></li>
                        <li><Link href="#">Terms of Service</Link></li>
                    </ul>
                    <h4 class="mt-6">Follow Us</h4>
                    <div class="footer-socials flex space-x-4 mt-2">
                        <a href="#" class="text-gray-500 hover:text-brand-primary">FB</a>
                        <a href="#" class="text-gray-500 hover:text-brand-primary">IN</a>
                        <a href="#" class="text-gray-500 hover:text-brand-primary">TW</a>
                        <a href="#" class="text-gray-500 hover:text-brand-primary">YT</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                    &copy; {{ new Date().getFullYear() }} {{ $page.props.appName }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Scoped styles specifically for GuestLayout if needed */
.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Responsive columns */
  gap: 2rem;
}

.footer-section h4 {
  @apply text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-4;
}

.footer-section ul li a {
  @apply text-sm text-gray-600 dark:text-gray-400 hover:text-brand-primary dark:hover:text-blue-400 block pb-2;
}

.footer-socials a {
 @apply text-gray-500 hover:text-brand-primary dark:hover:text-blue-400;
 /* Add icons later */
}

.footer-logo-img {
  max-width: 150px;
  height: auto;
}
</style>