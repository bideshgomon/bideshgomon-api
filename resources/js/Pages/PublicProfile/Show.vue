<script setup>
import { Head } from '@inertiajs/vue3';
import {
    UserIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    GlobeAltIcon,
    ChatBubbleLeftRightIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    profile: Object,
    totalViews: Number,
});

const hasBasicInfo = !!props.profile.basic_info;
const hasEducation = props.profile.education && props.profile.education.length > 0;
const hasWorkExperience = props.profile.work_experience && props.profile.work_experience.length > 0;
const hasLanguages = props.profile.languages && props.profile.languages.length > 0;
const hasTravelHistory = props.profile.travel_history && props.profile.travel_history.length > 0;
const hasSkills = props.profile.skills && props.profile.skills.length > 0;
</script>

<template>
    <Head :title="`${profile.name} - Public Profile`" />

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-8 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-4xl font-bold mb-2">{{ profile.name }}</h1>
                            <p v-if="profile.headline" class="text-xl text-indigo-100 mb-4">
                                {{ profile.headline }}
                            </p>
                            <div class="flex items-center text-indigo-100">
                                <EyeIcon class="h-5 w-5 mr-2" />
                                <span>{{ totalViews }} profile views</span>
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <UserIcon class="h-16 w-16 text-white" />
                        </div>
                    </div>
                    
                    <p v-if="profile.bio" class="mt-6 text-indigo-50 leading-relaxed">
                        {{ profile.bio }}
                    </p>
                </div>
            </div>

            <!-- Basic Info -->
            <div v-if="hasBasicInfo" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <UserIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Basic Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-if="profile.basic_info.full_name">
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.full_name }}</dd>
                        </div>
                        <div v-if="profile.basic_info.nationality">
                            <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.nationality }}</dd>
                        </div>
                        <div v-if="profile.basic_info.city">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.city }}, {{ profile.basic_info.country }}</dd>
                        </div>
                        <div v-if="profile.basic_info.email">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.email }}</dd>
                        </div>
                        <div v-if="profile.basic_info.phone">
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.phone }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Education -->
            <div v-if="hasEducation" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <AcademicCapIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Education</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div v-for="(edu, index) in profile.education" :key="index" class="border-l-4 border-indigo-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-900 capitalize">{{ edu.degree_level }}</h3>
                            <p class="text-indigo-600 font-medium">{{ edu.institution_name }}</p>
                            <p class="text-gray-600">{{ edu.field_of_study }}</p>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <span>{{ edu.start_date }} - {{ edu.end_date }}</span>
                                <span v-if="edu.gpa" class="ml-4">GPA: {{ edu.gpa }}</span>
                            </div>
                            <p v-if="edu.country" class="text-sm text-gray-500">{{ edu.country }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Experience -->
            <div v-if="hasWorkExperience" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <BriefcaseIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Work Experience</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div v-for="(work, index) in profile.work_experience" :key="index" class="border-l-4 border-purple-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ work.job_title }}</h3>
                            <p class="text-purple-600 font-medium">{{ work.company_name }}</p>
                            <p class="text-sm text-gray-500">{{ work.start_date }} - {{ work.end_date }}</p>
                            <p v-if="work.city" class="text-sm text-gray-500">{{ work.city }}, {{ work.country }}</p>
                            <p v-if="work.job_description" class="mt-2 text-gray-700 leading-relaxed">{{ work.job_description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Languages -->
            <div v-if="hasLanguages" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <ChatBubbleLeftRightIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Languages</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="(lang, index) in profile.languages" :key="index" class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900">{{ lang.language_name }}</h3>
                            <p class="text-sm text-gray-600 capitalize">{{ lang.proficiency_level }}</p>
                            <div v-if="lang.ielts_score || lang.toefl_score" class="mt-2 text-sm">
                                <span v-if="lang.ielts_score" class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">
                                    IELTS: {{ lang.ielts_score }}
                                </span>
                                <span v-if="lang.toefl_score" class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">
                                    TOEFL: {{ lang.toefl_score }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div v-if="hasSkills" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Skills</h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="(skill, index) in profile.skills"
                            :key="index"
                            class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium"
                        >
                            {{ skill }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Travel History -->
            <div v-if="hasTravelHistory" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <GlobeAltIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Travel History</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div v-for="(travel, index) in profile.travel_history" :key="index" class="text-center">
                            <div class="text-2xl mb-1">🌍</div>
                            <p class="font-semibold text-gray-900">{{ travel.country }}</p>
                            <p class="text-sm text-gray-500">{{ travel.entry_date }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ travel.purpose }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center py-8 text-gray-500">
                <p>Powered by <span class="font-semibold text-indigo-600">BideshGomon</span></p>
                <p class="text-sm mt-2">Professional Profile Platform for Bangladesh</p>
            </div>
        </div>
    </div>
</template>
