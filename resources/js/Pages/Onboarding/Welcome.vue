<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
import {
    CheckCircleIcon,
    RocketLaunchIcon,
    UserCircleIcon,
    BriefcaseIcon,
    AcademicCapIcon,
    DocumentTextIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    profile: Object,
    profileCompletion: Number,
});

const completionPercentage = computed(() => props.profileCompletion || 0);

const steps = [
    {
        icon: UserCircleIcon,
        title: 'Personal Information',
        description: 'Basic details, contact info, and identity documents',
        fields: ['Name', 'Email', 'Phone', 'Date of Birth', 'NID/Passport'],
        route: 'profile.edit',
        section: 'personal',
    },
    {
        icon: AcademicCapIcon,
        title: 'Education & Skills',
        description: 'Academic background and professional qualifications',
        fields: ['Degree', 'Institution', 'Skills', 'Languages'],
        route: 'profile.edit',
        section: 'education',
    },
    {
        icon: BriefcaseIcon,
        title: 'Work Experience',
        description: 'Employment history and professional achievements',
        fields: ['Company', 'Position', 'Duration', 'Responsibilities'],
        route: 'profile.edit',
        section: 'experience',
    },
    {
        icon: DocumentTextIcon,
        title: 'Documents & Verification',
        description: 'Upload necessary documents for applications',
        fields: ['Passport', 'Bank Statements', 'Certificates', 'Photos'],
        route: 'profile.edit',
        section: 'documents',
    },
];

const benefits = [
    'Apply for visas with one click - all fields pre-filled',
    'Job applications in seconds - no repetitive forms',
    'Smart suggestions based on your qualifications',
    'Track all applications in one dashboard',
    'Higher approval rates with complete profiles',
];

const startOnboarding = () => {
    router.visit(route('profile.edit', { section: 'personal' }));
};

const skipForNow = () => {
    router.visit(route('dashboard'));
};
</script>

<template>
    <Head title="Welcome to Bidesh Gomon" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Hero Section -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full mb-6 shadow-lg">
                        <RocketLaunchIcon class="h-10 w-10 text-white" />
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                        Welcome to Bidesh Gomon! 🎉
                    </h1>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-6">
                        Your account is ready! Complete your profile in just 5 minutes and unlock the full power 
                        of automated visa & job applications.
                    </p>
                    
                    <!-- Progress Bar -->
                    <div class="max-w-md mx-auto">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Profile Completion</span>
                            <span class="text-sm font-bold text-indigo-600">{{ completionPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div 
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 h-3 rounded-full transition-all duration-500"
                                :style="{ width: `${completionPercentage}%` }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" v-if="completionPercentage < 80">
                            {{ 80 - completionPercentage }}% more to unlock all features
                        </p>
                        <p class="text-xs text-green-600 font-semibold mt-2" v-else>
                            ✓ Great! Your profile is ready for applications
                        </p>
                    </div>
                </div>

                <!-- Why Complete Your Profile -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                        Why Complete Your Profile?
                    </h2>
                    <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                        <div 
                            v-for="(benefit, index) in benefits" 
                            :key="index"
                            class="flex items-start gap-3"
                        >
                            <CheckCircleIcon class="h-6 w-6 text-green-600 flex-shrink-0 mt-0.5" />
                            <span class="text-gray-700">{{ benefit }}</span>
                        </div>
                    </div>
                </div>

                <!-- Steps -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                        Complete Your Profile in 4 Easy Steps
                    </h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div
                            v-for="(step, index) in steps"
                            :key="index"
                            class="bg-white rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-100"
                        >
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <component :is="step.icon" class="h-6 w-6 text-indigo-600" />
                                </div>
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    {{ index + 1 }}
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ step.title }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ step.description }}</p>
                            <div class="space-y-1">
                                <p class="text-xs font-semibold text-gray-700 mb-2">What we'll ask:</p>
                                <div 
                                    v-for="(field, fieldIndex) in step.fields" 
                                    :key="fieldIndex"
                                    class="text-xs text-gray-600 flex items-center gap-1"
                                >
                                    <span class="w-1 h-1 bg-indigo-600 rounded-full"></span>
                                    {{ field }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-center shadow-2xl">
                    <h2 class="text-3xl font-bold text-white mb-4">
                        Ready to Get Started?
                    </h2>
                    <p class="text-indigo-100 mb-8 max-w-2xl mx-auto">
                        It takes only 5 minutes. Complete your profile now and start applying for opportunities today!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <button
                            @click="startOnboarding"
                            class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold text-lg hover:bg-gray-50 transform hover:scale-105 transition shadow-lg"
                        >
                            Complete My Profile Now
                            <ArrowRightIcon class="ml-2 h-5 w-5" />
                        </button>
                        <button
                            @click="skipForNow"
                            class="inline-flex items-center px-8 py-4 bg-indigo-500 text-white rounded-xl font-semibold text-lg hover:bg-indigo-400 transition"
                        >
                            I'll Do This Later
                        </button>
                    </div>
                    <p class="text-indigo-200 text-sm mt-4">
                        ⏱️ Average completion time: 5 minutes
                    </p>
                </div>

                <!-- Stats Footer -->
                <div class="mt-12 grid grid-cols-3 gap-6 max-w-3xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-indigo-600 mb-1">95%</div>
                        <div class="text-sm text-gray-600">Complete profiles get approved</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600 mb-1">70%</div>
                        <div class="text-sm text-gray-600">Faster application process</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-pink-600 mb-1">3x</div>
                        <div class="text-sm text-gray-600">More opportunities matched</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
