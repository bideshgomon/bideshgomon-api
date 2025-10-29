<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import DocumentUploadSection from './Partials/DocumentUploadSection.vue';
import PersonalInformationSection from './Partials/PersonalInformationSection.vue';
import AccountSettingsSection from './Partials/AccountSettingsSection.vue';
import EducationSection from './Partials/EducationSection.vue';
import ExperienceSection from './Partials/ExperienceSection.vue';
import SkillsSection from './Partials/SkillsSection.vue';
import PortfolioSection from './Partials/PortfolioSection.vue';
import TravelHistorySection from './Partials/TravelHistorySection.vue';
import LanguagesSection from './Partials/LanguagesSection.vue';
import LicensesSection from './Partials/LicensesSection.vue';
import TechnicalEducationSection from './Partials/TechnicalEducationSection.vue';
import MembershipSection from './Partials/MembershipSection.vue';
import { Head, usePage } from '@inertiajs/vue3'; // 1. IMPORT usePage

// 2. GET the page props, which contain shared data from the backend
const page = usePage();

// 3. EXTRACT the specific data objects you need
const user = page.props.auth.user;
const userProfile = page.props.userProfile; // This must be passed from your Controller

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    prebuiltData: Object,
});
</script>

<template>
    <Head title="My Profile" />

    <ProfileLayout>
                <div class="space-y-6">
            <PersonalInformationSection
                :user="user"
                :user-profile="userProfile"
                :must-verify-email="mustVerifyEmail"
                :status="status"
                :countries="prebuiltData.countries"
            />

            <DocumentUploadSection
                :document-types="prebuiltData.documentTypes"
            />
            <EducationSection />
            <ExperienceSection />
            <TechnicalEducationSection />
            <LicensesSection />
            <SkillsSection />
            <LanguagesSection
                 :user-languages="prebuiltData.userLanguages"
                 :languages="prebuiltData.languages"
            />

            <MembershipSection />

            <PortfolioSection />
            <TravelHistorySection
                :countries="prebuiltData.countries"
            />
            <AccountSettingsSection />
        </div>
    </ProfileLayout>
</template>

<style>
/* Adds space between our collapsible sections */
.space-y-6 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(1.5rem * var(--tw-space-y-reverse));
}
</style>