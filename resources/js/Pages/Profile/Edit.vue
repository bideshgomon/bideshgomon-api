<script setup>
// Import Link if not already imported
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or ProfileLayout if you use that here
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
// Import other partials if they exist
import PersonalInformationSection from './Partials/PersonalInformationSection.vue';
import EducationSection from './Partials/EducationSection.vue';
import ExperienceSection from './Partials/ExperienceSection.vue';
import SkillsSection from './Partials/SkillsSection.vue';
import PortfolioSection from './Partials/PortfolioSection.vue';
import DocumentUploadSection from './Partials/DocumentUploadSection.vue';
// Import AccountSettingsSection if used
// import AccountSettingsSection from './Partials/AccountSettingsSection.vue';


defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    // Add props for data needed by partials if not loaded via API within them
    // Example: skills: Array, documentTypes: Array, etc.
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout> {/* Or ProfileLayout */}
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Profile & CV Builder</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                {/* --- [PATCH START] Add Download CV Button --- */}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                         <header class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">CV / Résumé</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Download your generated CV based on the information provided below.
                                </p>
                            </div>
                             <Link
                                :href="route('profile.cv.download')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                                target="_blank" {{-- Optional: Open in new tab --}}
                             >
                                Download CV (PDF)
                            </Link>
                        </header>
                    </section>
                </div>
                {/* --- [PATCH END] --- */}


                {/* Existing Profile Sections */}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                 {/* Personal Info Section */}
                 <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <PersonalInformationSection class="max-w-xl" />
                </div>

                 {/* Education Section */}
                 <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <EducationSection />
                </div>

                {/* Experience Section */}
                 <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <ExperienceSection />
                </div>

                {/* Skills Section */}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <SkillsSection />
                </div>

                {/* Portfolio Section */}
                 <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <PortfolioSection />
                </div>

                {/* Document Upload Section */}
                 <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <DocumentUploadSection />
                 </div>

                {/* Add other sections if they exist: Languages, Licenses, Memberships etc. */}

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>