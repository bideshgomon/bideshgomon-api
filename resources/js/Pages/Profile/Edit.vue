<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import DocumentUploadSection from './Partials/DocumentUploadSection.vue';
import PersonalInformationSection from './Partials/PersonalInformationSection.vue';
import AccountSettingsSection from './Partials/AccountSettingsSection.vue';
import EducationSection from './Partials/EducationSection.vue';
import ExperienceSection from './Partials/ExperienceSection.vue';
import SkillsSection from './Partials/SkillsSection.vue';
import PortfolioSection from './Partials/PortfolioSection.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="My Profile" />

    <ProfileLayout>
        <div class="space-y-6">
            <PersonalInformationSection
                :must-verify-email="mustVerifyEmail"
                :status="status"
            />
            <DocumentUploadSection />
            <EducationSection />
            <ExperienceSection />
            <SkillsSection />
            <PortfolioSection /> 
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