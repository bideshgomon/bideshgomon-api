<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BriefcaseIcon, 
    AcademicCapIcon, 
    SparklesIcon, 
    UserCircleIcon, 
    DocumentTextIcon, 
    GlobeAltIcon, 
    StarIcon,
    ComputerDesktopIcon,
    UserGroupIcon // <-- NEWLY ADDED
} from '@heroicons/vue/24/outline';
import SectionBorder from '@/Components/SectionBorder.vue';

defineProps({
    user: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return 'Present';
    const options = { year: 'numeric', month: 'short' };
    const date = new Date(dateString + 'T00:00:00Z');
    return date.toLocaleDateString(undefined, { timeZone: 'UTC', ...options });
};

const getInitials = (name) => {
    if (!name) return '..';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};
</script>

<template>
    <Head title="CV Builder" />

    <ProfileLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                CV / Résumé Builder
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Preview your CV based on your profile information. Download it as a PDF.
            </p>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex justify-end gap-4 mb-6">
                    <Link
                        :href="route('profile.edit')"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-200 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-800 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Edit Profile
                    </Link>
                    
                    <a
                        :href="route('profile.cv.download')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-blue-500 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Download PDF
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-8 md:p-12 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center mb-8">
                        <div class="flex-shrink-0 h-24 w-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-4xl font-bold">{{ getInitials(user.name) }}</span>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-300 mt-1">
                                {{ user.email }}
                                <span v-if="user.profile?.phone" class="mx-2">|</span>
                                {{ user.profile?.phone }}
                            </p>
                            <p v-if="user.profile?.address" class="text-md text-gray-500 dark:text-gray-400 mt-1">
                                {{ user.profile.address }}
                            </p>
                        </div>
                    </div>
                    
                    <div v-if="user.profile?.bio" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <UserCircleIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Professional Summary
                        </h2>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ user.profile.bio }}</p>
                    </div>

                    <div v-if="user.experiences?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <BriefcaseIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Work Experience
                        </h2>
                        <div v-for="exp in user.experiences" :key="exp.id" class="mb-6">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ exp.title }}</h3>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ formatDate(exp.start_date) }} - 
                                    {{ exp.is_current ? 'Present' : formatDate(exp.end_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ exp.company }} <span v-if="exp.location" class="text-gray-500 dark:text-gray-400 font-normal">| {{ exp.location }}</span></h4>
                            <p class="text-gray-700 dark:text-gray-300 mt-2">{{ exp.description }}</p>
                        </div>
                    </div>

                    <div v-if="user.travel_histories?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <GlobeAltIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Travel History
                        </h2>
                        <div v-for="th in user.travel_histories" :key="th.id" class="mb-6">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ th.country.name }}</h3>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ formatDate(th.entry_date) }} - 
                                    {{ formatDate(th.exit_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ th.purpose }}</h4>
                        </div>
                    </div>

                    <div v-if="user.educations?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <AcademicCapIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Formal Education
                        </h2>
                        <div v-for="edu in user.educations" :key="edu.id" class="mb-6">
                             <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ edu.degree }}</h3>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ formatDate(edu.start_date) }} - 
                                    {{ edu.is_current ? 'Present' : formatDate(edu.end_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ edu.school }} <span v-if="edu.location" class="text-gray-500 dark:text-gray-400 font-normal">| {{ edu.location }}</span></h4>
                        </div>
                    </div>
                    
                    <div v-if="user.technical_educations?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <ComputerDesktopIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Technical & Vocational Education
                        </h2>
                        <div v-for="tech in user.technical_educations" :key="tech.id" class="mb-6">
                             <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ tech.course_name }}</h3>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ formatDate(tech.start_date) }} - 
                                    {{ tech.is_current ? 'Present' : formatDate(tech.end_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ tech.institution }}</h4>
                        </div>
                    </div>
                    
                    <div v-if="user.memberships?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <UserGroupIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Professional Memberships
                        </h2>
                        <div v-for="mem in user.memberships" :key="mem.id" class="mb-6">
                             <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ mem.organization_name }}</h3>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ formatDate(mem.start_date) }} - 
                                    {{ mem.is_current ? 'Present' : formatDate(mem.end_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ mem.role }}</h4>
                        </div>
                    </div>
                    <div v-if="user.licenses?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <StarIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Licenses & Certifications
                        </h2>
                        <div v-for="license in user.licenses" :key="license.id" class="mb-6">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ license.name }}</h3>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Issued {{ formatDate(license.issue_date) }}
                                </span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                {{ license.issuing_organization }}
                            </h4>
                            <p v-if="license.credential_id" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                ID: {{ license.credential_id }}
                            </p>
                        </div>
                    </div>

                    <div v-if="user.skills?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <SparklesIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Skills
                        </h2>
                        <div class="flex flex-wrap gap-3">
                            <span v-for="skill in user.skills" :key="skill.id" class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-sm font-medium px-4 py-2 rounded-full">
                                {{ skill.name }}
                            </span>
                        </div>
                    </div>
                    
                    <div v-if="user.portfolios?.length > 0" class="mb-8">
                        <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                            <DocumentTextIcon class="h-6 w-6 inline-block -mt-1 mr-2" />
                            Portfolio
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="item in user.portfolios" :key="item.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ item.title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ item.description }}</a_model>
                                <a :href="item.url" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline">
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