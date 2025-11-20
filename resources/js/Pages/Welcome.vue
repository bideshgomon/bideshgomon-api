<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    SparklesIcon,
    CheckCircleIcon,
    ArrowRightIcon,
    GlobeAltIcon,
    BriefcaseIcon,
    AcademicCapIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const features = [
    {
        icon: GlobeAltIcon,
        title: 'Visa Applications',
        description: 'Apply for visas to 50+ countries with smart form auto-fill',
        color: 'from-blue-500 to-indigo-600',
    },
    {
        icon: BriefcaseIcon,
        title: 'Job Opportunities',
        description: 'Find and apply for jobs abroad with one-click applications',
        color: 'from-purple-500 to-pink-600',
    },
    {
        icon: AcademicCapIcon,
        title: 'Travel Services',
        description: 'Book flights, hotels, and travel insurance seamlessly',
        color: 'from-green-500 to-teal-600',
    },
    {
        icon: DocumentTextIcon,
        title: 'Professional Documents',
        description: 'Generate CVs, translate documents, and manage applications',
        color: 'from-orange-500 to-red-600',
    },
];

const benefits = [
    'Fill your profile once, use everywhere',
    'Smart suggestions based on your goals',
    'Track all applications in one place',
    'Get alerts for new opportunities',
    'Professional document generation',
    'Priority support for complete profiles',
];
</script>

<template>
    <Head title="Welcome to Bidesh Gomon" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 fixed w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link :href="route('welcome')" class="flex items-center hover:opacity-80 transition">
                        <div class="flex items-center">
                            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 p-2 rounded-lg shadow-md">
                                <SparklesIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-3">
                                <div class="text-xl font-bold text-gray-900">Bidesh Gomon</div>
                                <div class="text-xs text-gray-500 -mt-1">Your Journey Abroad</div>
                            </div>
                        </div>
                    </Link>
                    <div class="flex items-center gap-4" v-if="canLogin">
                        <Link
                            :href="route('login')"
                            class="text-gray-700 hover:text-indigo-600 font-medium transition"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center">
                    <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-6">
                        Your Journey Abroad
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                            Starts Here
                        </span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        Complete your profile once, and apply for visas, jobs, and travel services with a single click. 
                        We handle the paperwork, you focus on your dreams.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 transform hover:scale-105 transition shadow-lg"
                        >
                            Start Your Journey
                            <ArrowRightIcon class="ml-2 h-5 w-5" />
                        </Link>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold text-lg hover:bg-gray-50 transform hover:scale-105 transition shadow-lg border-2 border-indigo-600"
                        >
                            Already a Member? Sign In
                        </Link>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div class="bg-white/60 backdrop-blur rounded-xl p-6 shadow-sm">
                            <div class="text-3xl font-bold text-indigo-600">50+</div>
                            <div class="text-sm text-gray-600 mt-1">Countries</div>
                        </div>
                        <div class="bg-white/60 backdrop-blur rounded-xl p-6 shadow-sm">
                            <div class="text-3xl font-bold text-purple-600">1000+</div>
                            <div class="text-sm text-gray-600 mt-1">Jobs</div>
                        </div>
                        <div class="bg-white/60 backdrop-blur rounded-xl p-6 shadow-sm">
                            <div class="text-3xl font-bold text-green-600">95%</div>
                            <div class="text-sm text-gray-600 mt-1">Success Rate</div>
                        </div>
                        <div class="bg-white/60 backdrop-blur rounded-xl p-6 shadow-sm">
                            <div class="text-3xl font-bold text-orange-600">24/7</div>
                            <div class="text-sm text-gray-600 mt-1">Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Everything You Need</h2>
                    <p class="text-xl text-gray-600">One platform for all your international travel and work needs</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="(feature, index) in features"
                        :key="index"
                        class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100"
                    >
                        <div :class="['w-14 h-14 rounded-xl bg-gradient-to-br flex items-center justify-center mb-4', feature.color]">
                            <component :is="feature.icon" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="py-20 bg-gradient-to-br from-indigo-50 to-purple-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                    <p class="text-xl text-gray-600">Get started in 3 simple steps</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="bg-white rounded-2xl p-8 shadow-lg">
                            <div class="w-12 h-12 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                                1
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Create Your Profile</h3>
                            <p class="text-gray-600">
                                Fill out your profile once with your personal details, education, work experience, and travel documents. 
                                Takes only 5 minutes!
                            </p>
                        </div>
                        <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-indigo-300"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="bg-white rounded-2xl p-8 shadow-lg">
                            <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                                2
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Choose Your Service</h3>
                            <p class="text-gray-600">
                                Browse visas, jobs, or travel services. Your profile data automatically fills all forms - 
                                no repetitive data entry!
                            </p>
                        </div>
                        <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-purple-300"></div>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg">
                        <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                            3
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Track & Success</h3>
                        <p class="text-gray-600">
                            Track all your applications in one dashboard. Get real-time updates and expert support 
                            throughout your journey.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6">Why Choose Bidesh Gomon?</h2>
                        <p class="text-xl text-gray-600 mb-8">
                            We've helped thousands of Bangladeshis achieve their international dreams. 
                            Here's what makes us different:
                        </p>
                        <div class="space-y-4">
                            <div
                                v-for="(benefit, index) in benefits"
                                :key="index"
                                class="flex items-start gap-3"
                            >
                                <CheckCircleIcon class="h-6 w-6 text-green-600 flex-shrink-0 mt-0.5" />
                                <span class="text-gray-700 text-lg">{{ benefit }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl p-8 text-white shadow-2xl">
                        <h3 class="text-3xl font-bold mb-6">Start Your Free Trial</h3>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon class="h-6 w-6 flex-shrink-0" />
                                <span>No credit card required</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon class="h-6 w-6 flex-shrink-0" />
                                <span>Access to all features</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon class="h-6 w-6 flex-shrink-0" />
                                <span>Expert consultation included</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon class="h-6 w-6 flex-shrink-0" />
                                <span>Cancel anytime</span>
                            </li>
                        </ul>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="block w-full text-center bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition shadow-lg"
                        >
                            Create Free Account
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Ready to Start Your Journey?
                </h2>
                <p class="text-xl text-indigo-100 mb-8">
                    Join thousands of successful applicants who trusted Bidesh Gomon with their international dreams.
                </p>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="inline-flex items-center px-10 py-5 bg-white text-indigo-600 rounded-xl font-bold text-xl hover:bg-gray-50 transform hover:scale-105 transition shadow-2xl"
                >
                    Get Started Free
                    <ArrowRightIcon class="ml-3 h-6 w-6" />
                </Link>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8 mb-12">
                    <!-- Brand -->
                    <div class="md:col-span-1">
                        <div class="flex items-center mb-4">
                            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 p-2 rounded-lg">
                                <SparklesIcon class="h-6 w-6 text-white" />
                            </div>
                            <span class="ml-2 text-xl font-bold text-white">Bidesh Gomon</span>
                        </div>
                        <p class="text-gray-400 text-sm mb-4">Your trusted partner for international travel and work opportunities since 2020.</p>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-indigo-600 rounded-full flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-indigo-600 rounded-full flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-indigo-600 rounded-full flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Services -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Our Services</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-indigo-400 transition">Visa Applications</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Job Opportunities</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Flight Bookings</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Hotel Reservations</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Travel Insurance</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Document Translation</a></li>
                        </ul>
                    </div>

                    <!-- Company -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Company</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-indigo-400 transition">About Us</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">How It Works</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Success Stories</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Careers</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Blog</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Support</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-indigo-400 transition">Help Center</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">FAQs</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Terms of Service</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition">Refund Policy</a></li>
                        </ul>
                        <div class="mt-6">
                            <p class="text-white font-semibold text-sm mb-2">Contact Us</p>
                            <p class="text-gray-400 text-sm">📧 info@bideshgomon.com</p>
                            <p class="text-gray-400 text-sm">📞 +880 1234-567890</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-gray-500 text-sm">© 2025 Bidesh Gomon. All rights reserved.</p>
                        <div class="flex gap-6 text-sm">
                            <a href="#" class="text-gray-400 hover:text-indigo-400 transition">Privacy</a>
                            <a href="#" class="text-gray-400 hover:text-indigo-400 transition">Terms</a>
                            <a href="#" class="text-gray-400 hover:text-indigo-400 transition">Cookies</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
