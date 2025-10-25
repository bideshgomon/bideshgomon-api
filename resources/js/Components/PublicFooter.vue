<template>
    <footer class="border-t" style="background-color: var(--brand-light); border-color: var(--brand-border);">
        <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">

                <div class="footer-section col-span-2 md:col-span-1">
                    <h4 class="font-bold text-gray-900 mb-4">BideshGomon</h4>
                    <p class="text-sm text-gray-600">Simplifying your journey to study, work, and live abroad. Your trusted partner in global migration.</p>
                    <div class="mt-4 footer-socials flex space-x-3">
                        <a href="#" class="text-gray-500 hover:text-gray-700">FB</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700">LI</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700">TW</a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4 class="font-bold text-gray-900 mb-4">Our Services</h4>
                    <ul class="space-y-2 text-sm">
                        <li><Link :href="route('public.universities')" class="text-gray-600 hover:text-gray-900">Student Visa</Link></li>
                        <li><Link :href="route('public.courses')" class="text-gray-600 hover:text-gray-900">Courses</Link></li>
                        <li><Link :href="route('public.jobs')" class="text-gray-600 hover:text-gray-900">Jobs</Link></li>
                        </ul>
                </div>

                <div class="footer-section">
                    <h4 class="font-bold text-gray-900 mb-4">Company</h4>
                     <ul class="space-y-2 text-sm">
                        <li><Link href="#" class="text-gray-600 hover:text-gray-900">About Us</Link></li>
                        <li><Link href="#" class="text-gray-600 hover:text-gray-900">Contact Us</Link></li>
                        <li><Link href="#" class="text-gray-600 hover:text-gray-900">Blog</Link></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4 class="font-bold text-gray-900 mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><Link href="#" class="text-gray-600 hover:text-gray-900">Privacy Policy</Link></li>
                        <li><Link href="#" class="text-gray-600 hover:text-gray-900">Terms & Conditions</Link></li>
                    </ul>
                </div>

            </div>
            <div class="footer-bottom mt-8 pt-8 text-center text-sm text-gray-600 border-t border-gray-200">
                <p>&copy; {{ new Date().getFullYear() }} BideshGomon. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
/* Using styles from public/css/custom-style.css potentially */
.footer-section ul li a {
    transition: color 0.2s ease-in-out;
}
</style>