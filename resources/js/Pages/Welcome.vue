<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
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
        variant: 'ocean',
        badge: 'Most Popular',
    },
    {
        icon: BriefcaseIcon,
        title: 'Job Opportunities',
        description: 'Find and apply for jobs abroad with one-click applications and profile matching.',
        variant: 'sky',
        badge: 'High Demand',
    },
    {
        icon: PaperAirplaneIcon,
        title: 'Travel Services',
        description: 'Book flights, hotels, and travel insurance seamlessly in one platform.',
        variant: 'growth',
        badge: 'Best Rates',
    },
    {
        icon: DocumentTextIcon,
        title: 'Document Services',
        description: 'Generate CVs, translate documents, and manage all your applications efficiently.',
        variant: 'sunrise',
        badge: 'Professional',
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

        <!-- Hero Section with Rhythmic Flow -->
        <AnimatedSection
            variant="ocean"
            :blobs="true"
            :animated="true"
            badge="Your Gateway to Global Opportunities"
            container="default"
            class="pt-20 pb-12 sm:pt-24 md:pt-32 sm:pb-16 md:pb-24"
        >
            <div class="text-center animate-fade-in-up">
                <!-- Heading with rhythmic typography -->
                <h1 class="font-display font-bold text-rhythm mb-rhythm-lg leading-tight text-4xl sm:text-5xl md:text-6xl lg:text-display-xl">
                    Go Abroad with
                    <span class="bg-gradient-to-r from-ocean-600 to-growth-600 bg-clip-text text-transparent"> Confidence</span>
                </h1>

                <p class="text-xl sm:text-2xl text-gray-700 mb-rhythm-2xl max-w-3xl mx-auto text-rhythm">
                    All-in-one platform for visa applications, job search, travel booking, and document services. 
                    Simplify your journey abroad with intelligent automation and expert guidance.
                </p>

                <!-- CTA Buttons with FlowButton -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-rhythm-md mb-rhythm-2xl">
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                    >
                        <FlowButton variant="sunrise" size="lg" class="group">
                            <template #iconBefore>
                                <RocketLaunchIcon class="h-5 w-5 group-hover:rotate-12 transition-transform" />
                            </template>
                            Get Started Free
                            <template #iconAfter>
                                <ArrowRightIcon class="h-5 w-5" />
                            </template>
                        </FlowButton>
                    </Link>
                    <a href="#features">
                        <FlowButton variant="outline" size="lg">
                            <template #iconBefore>
                                <LightBulbIcon class="h-5 w-5" />
                            </template>
                            Learn More
                        </FlowButton>
                    </a>
                </div>

                <!-- Stats with rhythmic spacing -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-rhythm-lg max-w-4xl mx-auto">
                    <div v-for="stat in stats" :key="stat.label" class="text-center group animate-scale-in">
                        <div class="mb-rhythm-md inline-flex items-center justify-center w-16 h-16 rounded-rhythm-xl bg-white shadow-rhythm group-hover:shadow-rhythm-xl transition-all duration-400 group-hover:scale-110">
                            <component :is="stat.icon" :class="`h-8 w-8 ${stat.color}`" />
                        </div>
                        <div class="text-display-sm font-bold text-gray-900 mb-rhythm-xs">{{ stat.value }}</div>
                        <div class="text-sm text-gray-600">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </AnimatedSection>

        <!-- Features Section with RhythmicCard -->
        <AnimatedSection
            id="features"
            title="Everything You Need in One Place"
            subtitle="From visa applications to job searches, we've got all your international needs covered."
            badge="Core Services"
            :badgeIcon="SparklesIcon"
            variant="light"
            :animated="true"
        >
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-rhythm-lg">
                <RhythmicCard
                    v-for="feature in features"
                    :key="feature.title"
                    :title="feature.title"
                    :description="feature.description"
                    :badge="feature.badge"
                    :variant="feature.variant"
                    size="md"
                    class="animate-fade-in-up"
                >
                    <template #icon>
                        <component :is="feature.icon" class="h-6 w-6" />
                    </template>
                    <template #footer>
                        <div class="flex items-center text-sm font-semibold transition-colors duration-300"
                             :class="{
                                 'text-ocean-600': feature.variant === 'ocean',
                                 'text-sky-600': feature.variant === 'sky',
                                 'text-growth-600': feature.variant === 'growth',
                                 'text-sunrise-600': feature.variant === 'sunrise',
                             }"
                        >
                            Learn more
                            <ArrowRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </template>
                </RhythmicCard>
            </div>
        </AnimatedSection>

        <!-- How It Works Section -->
        <AnimatedSection
            title="How It Works"
            subtitle="Get started in 3 simple steps and take control of your international journey"
            badge="Simple Process"
            :badgeIcon="RocketLaunchIcon"
            variant="light"
            :pattern="true"
            :animated="true"
        >
            <div class="grid md:grid-cols-3 gap-rhythm-2xl relative">
                <!-- Connection Lines (Desktop) -->
                <div class="hidden md:block absolute top-24 left-1/4 right-1/4 h-1 bg-gradient-to-r from-ocean-200 via-sky-300 to-growth-200 rounded-full"></div>
                
                <!-- Step 1 -->
                <RhythmicCard variant="growth" size="lg" class="relative">
                    <!-- Step Number Badge -->
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                        <div class="w-14 h-14 gradient-growth text-white rounded-full flex items-center justify-center text-xl font-bold shadow-rhythm-lg animate-scale-in">
                            1
                        </div>
                    </div>
                    
                    <template #icon>
                        <UserGroupIcon class="h-10 w-10 text-growth-600 mt-rhythm-md" />
                    </template>
                    
                    <h3 class="font-display font-bold text-2xl text-gray-900 mb-rhythm-sm text-center">
                        Create Your Profile
                    </h3>
                    <p class="text-gray-600 text-center text-rhythm">
                        Fill out your profile once with your personal details, education, work experience, and travel documents. Takes only 5 minutes!
                    </p>
                </RhythmicCard>

                <!-- Step 2 -->
                <RhythmicCard variant="ocean" size="lg" class="relative">
                    <!-- Step Number Badge -->
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                        <div class="w-14 h-14 gradient-ocean text-white rounded-full flex items-center justify-center text-xl font-bold shadow-rhythm-lg animate-scale-in" style="animation-delay: 0.2s;">
                            2
                        </div>
                    </div>
                    
                    <template #icon>
                        <BuildingOffice2Icon class="h-10 w-10 text-ocean-600 mt-rhythm-md" />
                    </template>
                    
                    <h3 class="font-display font-bold text-2xl text-gray-900 mb-rhythm-sm text-center">
                        Choose Your Service
                    </h3>
                    <p class="text-gray-600 text-center text-rhythm">
                        Browse visas, jobs, or travel services. Your profile data automatically fills all forms - no repetitive data entry!
                    </p>
                </RhythmicCard>

                <!-- Step 3 -->
                <RhythmicCard variant="sunrise" size="lg" class="relative">
                    <!-- Step Number Badge -->
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                        <div class="w-14 h-14 gradient-sunrise text-white rounded-full flex items-center justify-center text-xl font-bold shadow-rhythm-lg animate-scale-in" style="animation-delay: 0.4s;">
                            3
                        </div>
                    </div>
                    
                    <template #icon>
                        <TrophyIcon class="h-10 w-10 text-sunrise-600 mt-rhythm-md" />
                    </template>
                    
                    <h3 class="font-display font-bold text-2xl text-gray-900 mb-rhythm-sm text-center">
                        Track & Success
                    </h3>
                    <p class="text-gray-600 text-center text-rhythm">
                        Track all your applications in one dashboard. Get real-time updates and expert support throughout your journey.
                    </p>
                </RhythmicCard>
            </div>
        </AnimatedSection>

        <!-- Benefits Section -->
        <AnimatedSection
            id="about"
            title="Why Choose Bidesh Gomon?"
            subtitle="We combine cutting-edge technology with personalized service to make your international journey seamless and stress-free."
            badge="Why Choose Us"
            :badgeIcon="LightBulbIcon"
            variant="sunrise"
            :animated="true"
        >
            <div class="grid lg:grid-cols-2 gap-rhythm-2xl items-center">
                <!-- Benefits List -->
                <div class="space-y-rhythm-md animate-fade-in-up">
                    <div v-for="benefit in benefits" :key="benefit" class="flex items-start group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full gradient-growth flex items-center justify-center mt-0.5 shadow-rhythm group-hover:shadow-rhythm-lg group-hover:scale-110 transition-all duration-400">
                            <CheckCircleIcon class="h-5 w-5 text-white" />
                        </div>
                        <span class="ml-rhythm-md text-lg text-gray-700 text-rhythm group-hover:text-gray-900 transition-colors">{{ benefit }}</span>
                    </div>
                </div>
                
                <!-- Feature Cards Grid -->
                <div class="grid grid-cols-2 gap-rhythm-md animate-fade-in-up" style="animation-delay: 0.2s;">
                    <RhythmicCard variant="growth" size="md" class="text-center">
                        <template #icon>
                            <ClockIcon class="h-8 w-8 text-growth-600" />
                        </template>
                        <h4 class="text-2xl font-bold text-gray-900 mb-rhythm-xs">Fast</h4>
                        <p class="text-sm text-gray-600">Quick processing & approvals</p>
                    </RhythmicCard>
                    
                    <RhythmicCard variant="ocean" size="md" class="text-center">
                        <template #icon>
                            <ShieldCheckIcon class="h-8 w-8 text-ocean-600" />
                        </template>
                        <h4 class="text-2xl font-bold text-gray-900 mb-rhythm-xs">Secure</h4>
                        <p class="text-sm text-gray-600">Bank-level encryption</p>
                    </RhythmicCard>
                    
                    <RhythmicCard variant="sky" size="md" class="text-center">
                        <template #icon>
                            <UserGroupIcon class="h-8 w-8 text-sky-600" />
                        </template>
                        <h4 class="text-2xl font-bold text-gray-900 mb-rhythm-xs">Expert</h4>
                        <p class="text-sm text-gray-600">Professional guidance</p>
                    </RhythmicCard>
                    
                    <RhythmicCard variant="sunrise" size="md" class="text-center">
                        <template #icon>
                            <CurrencyDollarIcon class="h-8 w-8 text-sunrise-600" />
                        </template>
                        <h4 class="text-2xl font-bold text-gray-900 mb-rhythm-xs">Affordable</h4>
                        <p class="text-sm text-gray-600">Competitive pricing</p>
                    </RhythmicCard>
                </div>
            </div>
        </AnimatedSection>

        <!-- Testimonials Section -->
        <AnimatedSection
            title="Loved by Thousands"
            subtitle="See what our users have to say about their experience with Bidesh Gomon."
            badge="Customer Reviews"
            :badgeIcon="HeartIcon"
            variant="light"
            :animated="true"
        >
            <div class="grid md:grid-cols-3 gap-rhythm-lg">
                <RhythmicCard
                    v-for="testimonial in testimonials"
                    :key="testimonial.name"
                    variant="default"
                    size="md"
                    class="animate-fade-in-up"
                >
                    <!-- Quote Icon -->
                    <div class="mb-rhythm-md">
                        <svg class="h-8 w-8 text-growth-200" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>
                    
                    <div class="flex items-center mb-rhythm-md">
                        <img :src="testimonial.image" :alt="testimonial.name" class="w-14 h-14 rounded-full mr-rhythm-md ring-4 ring-growth-100 transition-all" />
                        <div>
                            <div class="font-bold text-gray-900">{{ testimonial.name }}</div>
                            <div class="text-sm text-gray-600">{{ testimonial.role }}</div>
                        </div>
                    </div>
                    
                    <div class="flex mb-rhythm-md">
                        <StarIcon v-for="i in testimonial.rating" :key="i" class="h-5 w-5 text-gold-400 fill-current" />
                    </div>
                    
                    <p class="text-gray-600 text-rhythm italic mb-rhythm-md">"{{ testimonial.content }}"</p>
                    
                    <template #footer>
                        <StatusBadge status="active" size="sm">
                            <CheckBadgeIcon class="h-4 w-4 mr-1 inline-block" />
                            Verified User
                        </StatusBadge>
                    </template>
                </RhythmicCard>
            </div>

            <!-- Trust Indicators -->
            <template #footer>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-rhythm-lg pt-rhythm-2xl animate-fade-in-up">
                    <div class="text-center group">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-rhythm-xl shadow-rhythm mb-rhythm-md group-hover:shadow-rhythm-lg group-hover:scale-110 transition-all duration-400">
                            <FireIcon class="h-8 w-8 text-sunrise-500" />
                        </div>
                        <div class="text-display-sm font-bold text-gray-900">4.9/5</div>
                        <div class="text-sm text-gray-600">Average Rating</div>
                    </div>
                    <div class="text-center group">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-rhythm-xl shadow-rhythm mb-rhythm-md group-hover:shadow-rhythm-lg group-hover:scale-110 transition-all duration-400">
                            <UserGroupIcon class="h-8 w-8 text-ocean-500" />
                        </div>
                        <div class="text-display-sm font-bold text-gray-900">50K+</div>
                        <div class="text-sm text-gray-600">Active Users</div>
                    </div>
                    <div class="text-center group">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-rhythm-xl shadow-rhythm mb-rhythm-md group-hover:shadow-rhythm-lg group-hover:scale-110 transition-all duration-400">
                            <TrophyIcon class="h-8 w-8 text-gold-500" />
                        </div>
                        <div class="text-display-sm font-bold text-gray-900">10K+</div>
                        <div class="text-sm text-gray-600">Success Stories</div>
                    </div>
                    <div class="text-center group">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-rhythm-xl shadow-rhythm mb-rhythm-md group-hover:shadow-rhythm-lg group-hover:scale-110 transition-all duration-400">
                            <ShieldCheckIcon class="h-8 w-8 text-growth-500" />
                        </div>
                        <div class="text-display-sm font-bold text-gray-900">100%</div>
                        <div class="text-sm text-gray-600">Secure Platform</div>
                    </div>
                </div>
            </template>
        </AnimatedSection>

        <!-- CTA Section -->
        <AnimatedSection
            id="contact"
            title="Ready to Start Your Journey?"
            subtitle="Join thousands of successful applicants who trusted Bidesh Gomon with their international dreams."
            variant="gradient"
            :animated="true"
            container="narrow"
        >
            <div class="text-center">
                <Link v-if="canRegister" :href="route('register')">
                    <FlowButton variant="outline" size="xl" class="bg-white border-2 border-white hover:bg-white/90 text-ocean-700">
                        <template #iconBefore>
                            <RocketLaunchIcon class="h-6 w-6" />
                        </template>
                        Create Free Account
                        <template #iconAfter>
                            <ArrowRightIcon class="h-6 w-6" />
                        </template>
                    </FlowButton>
                </Link>
            </div>
        </AnimatedSection>

        <!-- Footer -->
        <Footer />
    </div>
</template>
