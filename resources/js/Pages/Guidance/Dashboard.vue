<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    userProfile: Object,
    completeness: Object,
    profileScore: Number,
});

// Create the "To-Do" list based on completeness data
const recommendations = [
    { 
        id: 'personal_info',
        text: 'Confirm your Personal Information', 
        complete: props.completeness.personal_info,
        href: route('profile.edit') 
    },
    { 
        id: 'education',
        text: 'Add at least one education record', 
        complete: props.completeness.education,
        href: route('profile.edit') 
    },
    { 
        id: 'experience',
        text: 'Add at least one work experience record', 
        complete: props.completeness.experience,
        href: route('profile.edit') 
    },
    { 
        id: 'skills',
        text: 'Add your professional skills', 
        complete: props.completeness.skills,
        href: route('profile.edit') 
    },
    { 
        id: 'documents',
        text: 'Upload at least one document', 
        complete: props.completeness.documents,
        href: route('profile.edit') 
    },
    { 
        id: 'passport',
        text: 'Upload your Passport (Crucial!)', 
        complete: props.completeness.passport,
        href: route('profile.edit') 
    },
];

</script>

<template>
    <Head title="My Guidance" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Guidance Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Welcome, {{ userProfile.name }}!
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Your profile is <span class="font-bold text-brand-primary">{{ profileScore }}%</span> complete. Complete the steps below to unlock your full AI guidance.
                    </p>
                    
                    <div class="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div 
                            class="bg-brand-primary h-2.5 rounded-full transition-all duration-500" 
                            :style="{ width: profileScore + '%' }"
                        ></div>
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Your Next Steps
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Complete these tasks to improve your profile and get accurate recommendations.
                    </p>

                    <div class="mt-6 space-y-4">
                        <Link 
                            v-for="item in recommendations" 
                            :key="item.id"
                            :href="item.href"
                            class="flex items-center p-4 border dark:border-gray-700 rounded-lg"
                        >
                            <div v-if="item.complete">
                                <CheckCircleIcon class="h-6 w-6 text-brand-secondary" />
                            </div>
                            <div v-else>
                                <XCircleIcon class="h-6 w-6 text-brand-primary" />
                            </div>
                            
                            <span 
                                :class="[
                                    'ml-4 text-md font-medium',
                                    item.complete ? 'text-gray-500 dark:text-gray-400 line-through' : 'text-gray-900 dark:text-gray-100'
                                ]"
                            >
                                {{ item.text }}
                            </span>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>