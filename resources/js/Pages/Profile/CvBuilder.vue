<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BriefcaseIcon, AcademicCapIcon, SparklesIcon, UserCircleIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';
// The unused 'SectionBorder' import has been removed.

defineProps({
    user: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'short' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getInitials = (name) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};
</script>

<template>
    <Head title="CV Builder" />

    <ProfileLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                CV / Résumé Builder
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Preview your CV based on your profile information. Download it as a PDF.
            </p>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex justify-end gap-4 mb-6">
                    <Link
                        :href="route('profile.edit')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Edit Profile
                    </Link>
                    
                    <a
                        :href="route('profile.cv.download')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Download PDF
                    </a>

                </div>

                <div class="bg-white shadow-xl sm:rounded-lg p-8 md:p-12">
                    <div class="flex items-center mb-8">
                        <div class="flex-shrink-0 h-24 w-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-4xl font-bold">{{ getInitials(user.name) }}</span>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-4xl font-bold text-gray-900">{{ user.name }}</h1>
                            <p class="text-lg text-gray-600 mt-1">
                                {{ user.email }}
                                <span v-if="user.profile?.phone" class="mx-2">|</span>
                                {{ user.profile?.phone }}
                            </p>
                            <p v-if="user.profile?.address" class="text-md text-gray-500 mt-1">
                                {{ user.profile.address }}
                            </p>
                        </div>
                    </div>
                    
                    <div v.if="user.profile?.bio" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                            <UserCircleIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Professional Summary
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ user.profile.bio }}</p>
                    </div>

                    <div v-if="user.experiences?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                            <BriefcaseIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Work Experience
                        </h2>
                        <div v-for="exp in user.experiences" :key="exp.id" class="mb-6">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800">{{ exp.title }}</h3>
                                <span class="text-sm font-medium text-gray-500">
                                    {{ formatDate(exp.start_date) }} - 
                                    {{ exp.is_current ? 'Present' : formatDate(exp.end_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600">{{ exp.company }} <span v-if="exp.location" class="text-gray-500 font-normal">| {{ exp.location }}</span></h4>
                            <p class="text-gray-700 mt-2">{{ exp.description }}</p>
                        </div>
                    </div>

                    <div v-if="user.educations?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                            <AcademicCapIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Education
                        </h2>
                        <div v-for="edu in user.educations" :key="edu.id" class="mb-6">
                             <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800">{{ edu.degree }}</h3>
                                <span class="text-sm font-medium text-gray-500">
                                    {{ formatDate(edu.start_date) }} - 
                                    {{ edu.is_current ? 'Present' : formatDate(edu.end_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600">{{ edu.school }} <span v-if="edu.location" class="text-gray-500 font-normal">| {{ edu.location }}</span></h4>
                        </div>
                    </div>

                    <div v-if="user.skills?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                            <SparklesIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Skills
                        </h2>
                        <div class="flex flex-wrap gap-3">
                            <span v-for="skill in user.skills" :key="skill.id" class="bg-blue-100 text-blue-800 text-sm font-medium px-4 py-2 rounded-full">
                                {{ skill.name }}
                            </span>
                        </div>
                    </div>
                    
                    <div v-if="user.portfolios?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                            <DocumentTextIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Portfolio
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="item in user.portfolios" :key="item.id" class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-bold text-gray-800">{{ item.title }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ item.description }}</p>
                                <a :href="item.url" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                    View Project
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </ProfileLayout>
</template>