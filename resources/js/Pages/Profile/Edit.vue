<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Import custom profile sections
import PersonalInformationSection from './Partials/PersonalInformationSection.vue';
import EducationSection from './Partials/EducationSection.vue';
import ExperienceSection from './Partials/ExperienceSection.vue';
import DocumentUploadSection from './Partials/DocumentUploadSection.vue';
import SkillsSection from './Partials/SkillsSection.vue';
import PortfolioSection from './Partials/PortfolioSection.vue';
// Import the Data Access Request Section
import DataAccessRequestSection from './Partials/DataAccessRequestSection.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const isUserRole = computed(() => usePage().props.auth.user?.role?.slug === 'user');

</script>

<template>
    <Head title="Profile Settings" />

    <ProfileLayout>
        <div class="space-y-6">

            <div v-if="isUserRole">
                <DataAccessRequestSection />
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <PersonalInformationSection v-if="isUserRole" />
            <EducationSection v-if="isUserRole" />
            <ExperienceSection v-if="isUserRole" />
            <DocumentUploadSection v-if="isUserRole" />
            <SkillsSection v-if="isUserRole" />
            <PortfolioSection v-if="isUserRole" />

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </ProfileLayout>
</template>