<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import PersonalInformationSection from './Partials/PersonalInformationSection.vue';
import EducationSection from './Partials/EducationSection.vue';
import ExperienceSection from './Partials/ExperienceSection.vue';
import SkillsSection from './Partials/SkillsSection.vue';
import LanguagesSection from './Partials/LanguagesSection.vue';
import DocumentUploadSection from './Partials/DocumentUploadSection.vue';
import LicensesSection from './Partials/LicensesSection.vue';
import MembershipsSection from './Partials/MembershipsSection.vue';
import TechnicalEducationSection from './Partials/TechnicalEducationSection.vue';
import TravelHistorySection from './Partials/TravelHistorySection.vue'; // <-- 1. IMPORT
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    userProfile: Object,
});

const user = usePage().props.auth.user;

</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Profile & CV</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                 {/* ... Sections 1-9 ... */}
                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <PersonalInformationSection :user="user" :userProfile="userProfile" class="max-w-xl"/>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <EducationSection />
                </div>
                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <ExperienceSection />
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <SkillsSection />
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <LanguagesSection />
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <DocumentUploadSection />
                </div>
                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <LicensesSection />
                </div>
                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <MembershipsSection />
                </div>
                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <TechnicalEducationSection />
                </div>

                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <TravelHistorySection /> {/* <-- 2. INCLUDE */}
                </div>

                {/* ... Password, Delete Account ... */}
                 <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>