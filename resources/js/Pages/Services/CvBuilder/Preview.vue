<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ArrowLeftIcon, PencilIcon, ArrowDownTrayIcon, 
    EnvelopeIcon, PhoneIcon, MapPinIcon, GlobeAltIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    cv: Object,
});

const primaryColor = props.cv.cv_template.color_scheme.primary;
const secondaryColor = props.cv.cv_template.color_scheme.secondary;
</script>

<template>
    <Head :title="`Preview: ${cv.title}`" />

    <AuthenticatedLayout>
        <!-- Header with Actions -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <Link :href="route('cv-builder.my-cvs')" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                            <ArrowLeftIcon class="h-5 w-5" />
                        </Link>
                        <div class="flex-1 min-w-0">
                            <h1 class="text-lg sm:text-xl font-bold">CV Preview</h1>
                            <p class="text-xs sm:text-sm text-gray-300 truncate">{{ cv.title }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto">
                        <Link 
                            :href="route('cv-builder.edit', cv.id)"
                            class="flex-1 sm:flex-none flex items-center justify-center space-x-2 bg-white/10 text-white px-4 py-2.5 rounded-lg hover:bg-white/20 transition-colors text-sm"
                        >
                            <PencilIcon class="h-5 w-5" />
                            <span>Edit</span>
                        </Link>
                        <a 
                            :href="route('cv-builder.download', cv.id)"
                            class="flex-1 sm:flex-none flex items-center justify-center space-x-2 bg-emerald-600 text-white px-4 py-2.5 rounded-lg hover:bg-emerald-700 transition-colors font-medium text-sm"
                        >
                            <ArrowDownTrayIcon class="h-5 w-5" />
                            <span>Download</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CV Preview (A4 Size Paper) -->
        <div class="bg-gray-100 min-h-screen py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-2 sm:px-4">
                <!-- A4 Paper Simulation with Print Quality -->
                <div class="bg-white shadow-2xl rounded-lg overflow-hidden" style="max-width: 210mm; margin: 0 auto;">
                    <div class="p-8 sm:p-12 lg:p-16 h-full" style="font-size: 10pt; line-height: 1.6;">
                        <!-- Header Section -->
                        <div class="mb-6 sm:mb-8 pb-4 sm:pb-6 border-b-3 sm:border-b-4" :style="{ borderColor: primaryColor }">
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-2 sm:mb-3 tracking-tight" :style="{ color: primaryColor }">
                                {{ cv.full_name }}
                            </h1>
                            
                            <!-- Contact Info -->
                            <div class="flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm text-gray-700 mt-3 sm:mt-4">
                                <div class="flex items-center space-x-2">
                                    <EnvelopeIcon class="h-5 w-5 flex-shrink-0" :style="{ color: secondaryColor }" />
                                    <span>{{ cv.email }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <PhoneIcon class="h-5 w-5 flex-shrink-0" :style="{ color: secondaryColor }" />
                                    <span>{{ cv.phone }}</span>
                                </div>
                                <div v-if="cv.city" class="flex items-center space-x-2">
                                    <MapPinIcon class="h-5 w-5 flex-shrink-0" :style="{ color: secondaryColor }" />
                                    <span>{{ cv.city }}<span v-if="cv.country">, {{ cv.country.name }}</span></span>
                                </div>
                            </div>
                            
                            <!-- Social Links -->
                            <div v-if="cv.linkedin_url || cv.website_url" class="flex flex-wrap gap-4 text-sm mt-3">
                                <a v-if="cv.linkedin_url" :href="cv.linkedin_url" target="_blank" class="flex items-center space-x-2 hover:underline" :style="{ color: primaryColor }">
                                    <GlobeAltIcon class="h-5 w-5" />
                                    <span>LinkedIn Profile</span>
                                </a>
                                <a v-if="cv.website_url" :href="cv.website_url" target="_blank" class="flex items-center space-x-2 hover:underline" :style="{ color: primaryColor }">
                                    <GlobeAltIcon class="h-5 w-5" />
                                    <span>Personal Website</span>
                                </a>
                            </div>
                        </div>

                        <!-- Professional Summary -->
                        <div v-if="cv.professional_summary" class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 tracking-tight" :style="{ color: primaryColor }">Professional Summary</h2>
                            <p class="text-gray-800 leading-relaxed whitespace-pre-line text-sm sm:text-base">{{ cv.professional_summary }}</p>
                        </div>

                        <!-- Work Experience -->
                        <div v-if="cv.experience && cv.experience.length > 0" class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 tracking-tight" :style="{ color: primaryColor }">Work Experience</h2>
                            <div class="space-y-4 sm:space-y-5">
                                <div v-for="(exp, index) in cv.experience" :key="index" class="relative pl-4 sm:pl-6 border-l-2 sm:border-l-3" :style="{ borderColor: secondaryColor, borderLeftWidth: '3px' }">
                                    <div class="font-bold text-base sm:text-lg text-gray-900">{{ exp.job_title }}</div>
                                    <div class="text-gray-700 font-medium mt-1 text-sm sm:text-base">{{ exp.company }}<span v-if="exp.location"> • {{ exp.location }}</span></div>
                                    <div class="text-xs sm:text-sm text-gray-600 mt-1 font-medium">
                                        {{ exp.start_date }} - {{ exp.is_current ? 'Present' : exp.end_date }}
                                    </div>
                                    <p v-if="exp.description" class="text-gray-800 mt-2 leading-relaxed whitespace-pre-line text-sm sm:text-base">{{ exp.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Education -->
                        <div v-if="cv.education && cv.education.length > 0" class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 tracking-tight" :style="{ color: primaryColor }">Education</h2>
                            <div class="space-y-4">
                                <div v-for="(edu, index) in cv.education" :key="index">
                                    <div class="font-bold text-lg text-gray-900">{{ edu.degree }}<span v-if="edu.field_of_study"> in {{ edu.field_of_study }}</span></div>
                                    <div class="text-gray-700 font-medium mt-1">{{ edu.institution }}</div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ edu.start_date }} - {{ edu.end_date }}
                                        <span v-if="edu.grade" class="font-semibold"> • Grade: {{ edu.grade }}</span>
                                    </div>
                                    <p v-if="edu.description" class="text-gray-700 mt-2 text-sm">{{ edu.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div v-if="cv.skills && cv.skills.length > 0" class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 tracking-tight" :style="{ color: primaryColor }">Skills</h2>
                            <div class="flex flex-wrap gap-2 sm:gap-3">
                                <div 
                                    v-for="(skill, index) in cv.skills" 
                                    :key="index"
                                    class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium text-white shadow-sm"
                                    :style="{ backgroundColor: secondaryColor }"
                                >
                                    {{ skill.name }}
                                    <span v-if="skill.level" class="text-xs opacity-90 ml-1.5">• {{ skill.level }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Languages -->
                        <div v-if="cv.languages && cv.languages.length > 0" class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 tracking-tight" :style="{ color: primaryColor }">Languages</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                                <div v-for="(lang, index) in cv.languages" :key="index" class="flex items-center">
                                    <span class="font-semibold text-gray-900 mr-2">{{ lang.language }}</span>
                                    <span class="text-gray-600 text-sm capitalize">{{ lang.proficiency }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Certifications -->
                        <div v-if="cv.certifications && cv.certifications.length > 0" class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 tracking-tight" :style="{ color: primaryColor }">Certifications</h2>
                            <div class="space-y-4">
                                <div v-for="(cert, index) in cv.certifications" :key="index">
                                    <div class="font-bold text-gray-900">{{ cert.name }}</div>
                                    <div class="text-gray-700 mt-1">{{ cert.issuing_organization }}</div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        Issued: {{ cert.issue_date }}
                                        <span v-if="cert.expiry_date"> • Expires: {{ cert.expiry_date }}</span>
                                    </div>
                                    <div v-if="cert.credential_id" class="text-xs text-gray-500 mt-1">
                                        Credential ID: {{ cert.credential_id }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Below CV -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 mt-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0 text-xs sm:text-sm text-gray-600">
                        <div>Template: <span class="font-medium text-gray-900">{{ cv.cv_template.name }}</span></div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                            <div>{{ cv.view_count }} views</div>
                            <div>{{ cv.download_count }} downloads</div>
                            <div class="w-full sm:w-auto">Last updated {{ new Date(cv.updated_at).toLocaleDateString() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* A4 Paper proportions */
@media print {
    .bg-gray-100 {
        background: white;
    }
}
</style>
