<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import {
    SparklesIcon,
    CheckCircleIcon,
    ArrowRightIcon,
    GlobeAltIcon,
    BriefcaseIcon,
    AcademicCapIcon,
    DocumentTextIcon,
    UserGroupIcon,
    ShieldCheckIcon,
    ClockIcon,
    CurrencyDollarIcon,
    StarIcon,
    BuildingOffice2Icon,
    MapPinIcon,
    PaperAirplaneIcon,
    LanguageIcon,
    HeartIcon,
    LightBulbIcon,
    RocketLaunchIcon,
    TrophyIcon,
} from '@heroicons/vue/24/outline';
import {
    CheckBadgeIcon,
    FireIcon,
} from '@heroicons/vue/24/solid';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const page = usePage();

// Redirect authenticated users to dashboard
onMounted(() => {
    if (page.props.auth?.user) {
        router.visit('/dashboard');
    }
});

const features = [
    {
        icon: GlobeAltIcon,
        title: 'Visa Applications',
        description: 'Apply for visas to 50+ countries with smart form auto-fill and document management.',
        color: 'from-blue-500 to-indigo-600',
        iconBg: 'bg-blue-50',
        badge: 'Most Popular',
        badgeColor: 'bg-blue-100 text-blue-700',
    },
    {
        icon: BriefcaseIcon,
        title: 'Job Opportunities',
        description: 'Find and apply for jobs abroad with one-click applications and profile matching.',
        color: 'from-purple-500 to-pink-600',
        iconBg: 'bg-purple-50',
        badge: 'High Demand',
        badgeColor: 'bg-purple-100 text-purple-700',
    },
    {
        icon: PaperAirplaneIcon,
        title: 'Travel Services',
        description: 'Book flights, hotels, and travel insurance seamlessly in one platform.',
        color: 'from-green-500 to-teal-600',
        iconBg: 'bg-green-50',
        badge: 'Best Rates',
        badgeColor: 'bg-green-100 text-green-700',
    },
    {
        icon: DocumentTextIcon,
        title: 'Document Services',
        description: 'Generate CVs, translate documents, and manage all your applications efficiently.',
        color: 'from-orange-500 to-red-600',
        iconBg: 'bg-orange-50',
        badge: 'Professional',
        badgeColor: 'bg-orange-100 text-orange-700',
    },
];

const benefits = [
    'Fill your profile once, use everywhere',
    'Smart AI-powered suggestions',
    'Track all applications in one place',
    'Get instant alerts for opportunities',
    'Secure document storage',
    '24/7 customer support',
];

const stats = [
    { value: '50K+', label: 'Happy Users', icon: UserGroupIcon, color: 'text-blue-600' },
    { value: '195', label: 'Countries', icon: MapPinIcon, color: 'text-purple-600' },
    { value: '10K+', label: 'Successful Visas', icon: CheckBadgeIcon, color: 'text-green-600' },
    { value: '98%', label: 'Success Rate', icon: TrophyIcon, color: 'text-orange-600' },
];

const testimonials = [
    {
        name: 'Ahmed Rahman',
        role: 'Software Engineer',
        image: 'https://ui-avatars.com/api/?name=Ahmed+Rahman&background=10b981&color=fff',
        content: 'Bidesh Gomon made my visa application process so smooth! Got my work visa in just 2 weeks.',
        rating: 5,
    },
    {
        name: 'Fatima Khan',
        role: 'Student',
        image: 'https://ui-avatars.com/api/?name=Fatima+Khan&background=6366f1&color=fff',
        content: 'The platform helped me find the perfect university and handled all the paperwork. Highly recommended!',
        rating: 5,
    },
    {
        name: 'Kamal Hossain',
        role: 'Business Owner',
        image: 'https://ui-avatars.com/api/?name=Kamal+Hossain&background=f59e0b&color=fff',
        content: 'Professional service! They helped me expand my business internationally with proper documentation.',
        rating: 5,
    },
];
</script>

<template>
    <Head title="Welcome to Bidesh Gomon - Your Gateway to Global Opportunities" />

    <div class="min-h-screen bg-white">
        <!-- Header -->
        <Header :can-login="canLogin" :can-register="canRegister" />

        <!-- Hero Section -->
        <section class="relative pt-20 pb-12 sm:pt-24 md:pt-32 sm:pb-16 md:pb-24 overflow-hidden">
            <!-- Background Effects -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-gradient-to-br from-emerald-50 via-blue-50 to-purple-50 opacity-70"></div>
                <div class="absolute top-10 sm:top-20 left-5 sm:left-10 w-48 h-48 sm:w-72 sm:h-72 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute top-20 sm:top-40 right-5 sm:right-10 w-48 h-48 sm:w-72 sm:h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-10 sm:bottom-20 left-1/2 w-48 h-48 sm:w-72 sm:h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 rounded-full bg-emerald-50 border border-emerald-200 mb-6 sm:mb-8 hover:bg-emerald-100 transition-colors">
                        <SparklesIcon class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-600 mr-1.5 sm:mr-2 animate-pulse" />
                        <span class="text-xs sm:text-sm font-medium text-emerald-900">Your Gateway to Global Opportunities</span>
                        <FireIcon class="h-3 w-3 sm:h-4 sm:w-4 text-orange-500 ml-1.5 sm:ml-2" />
                    </div>

                    <!-- Heading -->
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight px-2">
                        Go Abroad with
                        <span class="bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent"> Confidence</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
                        All-in-one platform for visa applications, job search, travel booking, and document services. 
                        Simplify your journey abroad with intelligent automation and expert guidance.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-4 mb-10 sm:mb-12 px-4 sm:px-0">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl font-semibold text-base sm:text-lg hover:from-emerald-700 hover:to-green-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 group"
                        >
                            <RocketLaunchIcon class="mr-2 h-4 w-4 sm:h-5 sm:w-5 group-hover:rotate-12 transition-transform" />
                            Get Started Free
                            <ArrowRightIcon class="ml-2 h-4 w-4 sm:h-5 sm:w-5 group-hover:translate-x-1 transition-transform" />
                        </Link>
                        <a
                            href="#features"
                            class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 bg-white text-gray-900 rounded-xl font-semibold text-base sm:text-lg border-2 border-gray-200 hover:border-emerald-600 transition-all shadow-sm hover:shadow-md group"
                        >
                            <LightBulbIcon class="mr-2 h-4 w-4 sm:h-5 sm:w-5 text-gray-600 group-hover:text-emerald-600 transition-colors" />
                            Learn More
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8 max-w-4xl mx-auto px-2">
                        <div v-for="stat in stats" :key="stat.label" class="text-center group">
                            <div class="mb-3 sm:mb-4 inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-xl sm:rounded-2xl bg-white shadow-lg group-hover:shadow-xl transition-all group-hover:scale-110">
                                <component :is="stat.icon" :class="`h-6 w-6 sm:h-7 sm:w-7 md:h-8 md:w-8 ${stat.color}`" />
                            </div>
                            <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-1 sm:mb-2">{{ stat.value }}</div>
                            <div class="text-xs sm:text-sm text-gray-600">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16 sm:py-24 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Everything You Need in One Place
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        From visa applications to job searches, we've got all your international needs covered.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 group relative overflow-hidden"
                    >
                        <!-- Badge -->
                        <div :class="`absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold ${feature.badgeColor}`">
                            {{ feature.badge }}
                        </div>
                        
                        <!-- Icon Container with Gradient and Glow -->
                        <div class="relative mb-6">
                            <div :class="`absolute inset-0 ${feature.iconBg} rounded-xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity`"></div>
                            <div :class="`relative w-14 h-14 rounded-xl bg-gradient-to-br ${feature.color} flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg`">
                                <component :is="feature.icon" class="h-7 w-7 text-white" />
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors">
                            {{ feature.title }}
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ feature.description }}</p>
                        
                        <!-- Learn More Link -->
                        <div class="mt-4 flex items-center text-emerald-600 font-semibold text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                            Learn more
                            <ArrowRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-16 sm:py-24 bg-white relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-emerald-100 to-blue-100 rounded-full blur-3xl opacity-20"></div>
            </div>
            
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-50 border border-blue-200 mb-6">
                        <RocketLaunchIcon class="h-5 w-5 text-blue-600 mr-2" />
                        <span class="text-sm font-medium text-blue-900">Simple Process</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        How It Works
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Get started in 3 simple steps and take control of your international journey
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 relative">
                    <!-- Connection Lines (Desktop) -->
                    <div class="hidden md:block absolute top-24 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-emerald-200 via-blue-200 to-purple-200"></div>
                    
                    <!-- Step 1 -->
                    <div class="relative group">
                        <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-gray-100 group-hover:border-emerald-300">
                            <!-- Step Number Badge -->
                            <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                                    1
                                </div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex justify-center mb-6 mt-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <UserGroupIcon class="h-10 w-10 text-emerald-600" />
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Create Your Profile</h3>
                            <p class="text-gray-600 text-center leading-relaxed">
                                Fill out your profile once with your personal details, education, work experience, and travel documents. Takes only 5 minutes!
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative group">
                        <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-gray-100 group-hover:border-blue-300">
                            <!-- Step Number Badge -->
                            <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                                    2
                                </div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex justify-center mb-6 mt-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <BuildingOffice2Icon class="h-10 w-10 text-blue-600" />
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Choose Your Service</h3>
                            <p class="text-gray-600 text-center leading-relaxed">
                                Browse visas, jobs, or travel services. Your profile data automatically fills all forms - no repetitive data entry!
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative group">
                        <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-gray-100 group-hover:border-purple-300">
                            <!-- Step Number Badge -->
                            <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                                    3
                                </div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex justify-center mb-6 mt-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <TrophyIcon class="h-10 w-10 text-purple-600" />
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Track & Success</h3>
                            <p class="text-gray-600 text-center leading-relaxed">
                                Track all your applications in one dashboard. Get real-time updates and expert support throughout your journey.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section id="about" class="py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-50 border border-emerald-200 mb-6">
                            <LightBulbIcon class="h-5 w-5 text-emerald-600 mr-2" />
                            <span class="text-sm font-medium text-emerald-900">Why Choose Us</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                            Why Choose Bidesh Gomon?
                        </h2>
                        <p class="text-lg text-gray-600 mb-8">
                            We combine cutting-edge technology with personalized service to make your international journey seamless and stress-free.
                        </p>
                        <ul class="space-y-4">
                            <li v-for="benefit in benefits" :key="benefit" class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center mt-0.5 group-hover:bg-emerald-600 transition-colors">
                                    <CheckCircleIcon class="h-4 w-4 text-emerald-600 group-hover:text-white transition-colors" />
                                </div>
                                <span class="ml-3 text-gray-700 group-hover:text-gray-900 transition-colors">{{ benefit }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-8 text-center border border-emerald-100 hover:border-emerald-300 transition-all group">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4 group-hover:shadow-xl group-hover:scale-110 transition-all">
                                <ClockIcon class="h-8 w-8 text-emerald-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">Fast</div>
                            <p class="text-sm text-gray-600">Quick processing & approvals</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 text-center border border-blue-100 hover:border-blue-300 transition-all group">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4 group-hover:shadow-xl group-hover:scale-110 transition-all">
                                <ShieldCheckIcon class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">Secure</div>
                            <p class="text-sm text-gray-600">Bank-level encryption</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 text-center border border-purple-100 hover:border-purple-300 transition-all group">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4 group-hover:shadow-xl group-hover:scale-110 transition-all">
                                <UserGroupIcon class="h-8 w-8 text-purple-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">Expert</div>
                            <p class="text-sm text-gray-600">Professional guidance</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-8 text-center border border-orange-100 hover:border-orange-300 transition-all group">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4 group-hover:shadow-xl group-hover:scale-110 transition-all">
                                <CurrencyDollarIcon class="h-8 w-8 text-orange-600" />
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">Affordable</div>
                            <p class="text-sm text-gray-600">Competitive pricing</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-16 sm:py-24 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-yellow-50 border border-yellow-200 mb-6">
                        <HeartIcon class="h-5 w-5 text-yellow-600 mr-2" />
                        <span class="text-sm font-medium text-yellow-900">Customer Reviews</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Loved by Thousands
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        See what our users have to say about their experience with Bidesh Gomon.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        class="bg-white rounded-3xl p-8 shadow-sm border-2 border-gray-100 hover:border-emerald-200 hover:shadow-xl transition-all group"
                    >
                        <!-- Quote Icon -->
                        <div class="mb-4">
                            <svg class="h-8 w-8 text-emerald-200" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                            </svg>
                        </div>
                        
                        <div class="flex items-center mb-6">
                            <img :src="testimonial.image" :alt="testimonial.name" class="w-14 h-14 rounded-full mr-4 ring-4 ring-gray-100 group-hover:ring-emerald-100 transition-all" />
                            <div>
                                <div class="font-bold text-gray-900">{{ testimonial.name }}</div>
                                <div class="text-sm text-gray-600">{{ testimonial.role }}</div>
                            </div>
                        </div>
                        
                        <div class="flex mb-4">
                            <StarIcon v-for="i in testimonial.rating" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
                        </div>
                        
                        <p class="text-gray-600 leading-relaxed italic">"{{ testimonial.content }}"</p>
                        
                        <!-- Verified Badge -->
                        <div class="mt-4 flex items-center text-sm text-emerald-600">
                            <CheckBadgeIcon class="h-5 w-5 mr-1" />
                            <span class="font-semibold">Verified User</span>
                        </div>
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                            <FireIcon class="h-8 w-8 text-orange-500" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900">4.9/5</div>
                        <div class="text-sm text-gray-600">Average Rating</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                            <UserGroupIcon class="h-8 w-8 text-blue-500" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900">50K+</div>
                        <div class="text-sm text-gray-600">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                            <TrophyIcon class="h-8 w-8 text-yellow-500" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900">10K+</div>
                        <div class="text-sm text-gray-600">Success Stories</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                            <ShieldCheckIcon class="h-8 w-8 text-green-500" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900">100%</div>
                        <div class="text-sm text-gray-600">Secure Platform</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section id="contact" class="py-16 sm:py-24 bg-gradient-to-br from-emerald-600 to-green-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                    Ready to Start Your Journey?
                </h2>
                <p class="text-xl text-emerald-50 mb-10 max-w-2xl mx-auto">
                    Join thousands of successful applicants who trusted Bidesh Gomon with their international dreams.
                </p>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="inline-flex items-center px-8 py-4 bg-white text-emerald-600 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                    Create Free Account
                    <ArrowRightIcon class="ml-2 h-5 w-5" />
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
