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
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <Link :href="route('cv-builder.my-cvs')" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                            <ArrowLeftIcon class="h-5 w-5" />
                        </Link>
                        <div>
                            <h1 class="text-xl font-bold">CV Preview</h1>
                            <p class="text-sm text-gray-300">{{ cv.title }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <Link 
                            :href="route('cv-builder.edit', cv.id)"
                            class="flex items-center space-x-2 bg-white/10 text-white px-4 py-2 rounded-lg hover:bg-white/20 transition-colors"
                        >
                            <PencilIcon class="h-5 w-5" />
                            <span class="hidden sm:inline">Edit</span>
                        </Link>
                        <Link 
                            :href="route('cv-builder.download', cv.id)"
                            class="flex items-center space-x-2 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium"
                        >
                            <ArrowDownTrayIcon class="h-5 w-5" />
                            <span class="hidden sm:inline">Download PDF</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- CV Preview (A4 Size Paper) -->
        <div class="bg-gray-100 min-h-screen py-8">
            <div class="max-w-4xl mx-auto px-4">
                <!-- A4 Paper Simulation -->
                <div class="bg-white shadow-2xl" style="aspect-ratio: 210/297;">
                    <div class="p-12 h-full overflow-y-auto">
                        <!-- Header Section -->
                        <div class="mb-6 pb-6 border-b-4" :style="{ borderColor: primaryColor }">
                            <h1 class="text-4xl font-bold mb-2" :style="{ color: primaryColor }">
                                {{ cv.full_name }}
                            </h1>
                            
                            <!-- Contact Info -->
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mt-3">
                                <div class="flex items-center space-x-1">
                                    <EnvelopeIcon class="h-4 w-4" />
                                    <span>{{ cv.email }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <PhoneIcon class="h-4 w-4" />
                                    <span>{{ cv.phone }}</span>
                                </div>
                                <div v-if="cv.city" class="flex items-center space-x-1">
                                    <MapPinIcon class="h-4 w-4" />
                                    <span>{{ cv.city }}<span v-if="cv.country">, {{ cv.country.name }}</span></span>
                                </div>
                            </div>
                            
                            <!-- Social Links -->
                            <div v-if="cv.linkedin_url || cv.website_url" class="flex flex-wrap gap-4 text-sm mt-2">
                                <a v-if="cv.linkedin_url" :href="cv.linkedin_url" target="_blank" class="flex items-center space-x-1 text-blue-600 hover:underline">
                                    <GlobeAltIcon class="h-4 w-4" />
                                    <span>LinkedIn</span>
                                </a>
                                <a v-if="cv.website_url" :href="cv.website_url" target="_blank" class="flex items-center space-x-1 text-blue-600 hover:underline">
                                    <GlobeAltIcon class="h-4 w-4" />
                                    <span>Website</span>
                                </a>
                            </div>
                        </div>

                        <!-- Professional Summary -->
                        <div v-if="cv.professional_summary" class="mb-6">
                            <h2 class="text-xl font-bold mb-3" :style="{ color: primaryColor }">Professional Summary</h2>
                            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ cv.professional_summary }}</p>
                        </div>

                        <!-- Work Experience -->
                        <div v-if="cv.experience && cv.experience.length > 0" class="mb-6">
                            <h2 class="text-xl font-bold mb-3" :style="{ color: primaryColor }">Work Experience</h2>
                            <div class="space-y-4">
                                <div v-for="(exp, index) in cv.experience" :key="index" class="relative pl-4 border-l-2" :style="{ borderColor: secondaryColor }">
                                    <div class="font-semibold text-gray-900">{{ exp.job_title }}</div>
                                    <div class="text-sm text-gray-600">{{ exp.company }} • {{ exp.location }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ exp.start_date }} - {{ exp.is_current ? 'Present' : exp.end_date }}
                                    </div>
                                    <p v-if="exp.description" class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ exp.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Education -->
                        <div v-if="cv.education && cv.education.length > 0" class="mb-6">
                            <h2 class="text-xl font-bold mb-3" :style="{ color: primaryColor }">Education</h2>
                            <div class="space-y-3">
                                <div v-for="(edu, index) in cv.education" :key="index">
                                    <div class="font-semibold text-gray-900">{{ edu.degree }} in {{ edu.field_of_study }}</div>
                                    <div class="text-sm text-gray-600">{{ edu.institution }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ edu.start_date }} - {{ edu.end_date }}
                                        <span v-if="edu.grade"> • {{ edu.grade }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div v-if="cv.skills && cv.skills.length > 0" class="mb-6">
                            <h2 class="text-xl font-bold mb-3" :style="{ color: primaryColor }">Skills</h2>
                            <div class="flex flex-wrap gap-2">
                                <div 
                                    v-for="(skill, index) in cv.skills" 
                                    :key="index"
                                    class="px-3 py-1 rounded-full text-sm text-white"
                                    :style="{ backgroundColor: secondaryColor }"
                                >
                                    {{ skill.name }}
                                    <span class="text-xs opacity-75 ml-1">({{ skill.proficiency }})</span>
                                </div>
                            </div>
                        </div>

                        <!-- Languages -->
                        <div v-if="cv.languages && cv.languages.length > 0" class="mb-6">
                            <h2 class="text-xl font-bold mb-3" :style="{ color: primaryColor }">Languages</h2>
                            <div class="grid grid-cols-2 gap-2">
                                <div v-for="(lang, index) in cv.languages" :key="index" class="text-sm">
                                    <span class="font-medium text-gray-900">{{ lang.language }}</span>
                                    <span class="text-gray-600"> - {{ lang.proficiency }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Certifications -->
                        <div v-if="cv.certifications && cv.certifications.length > 0" class="mb-6">
                            <h2 class="text-xl font-bold mb-3" :style="{ color: primaryColor }">Certifications</h2>
                            <div class="space-y-3">
                                <div v-for="(cert, index) in cv.certifications" :key="index">
                                    <div class="font-semibold text-gray-900">{{ cert.name }}</div>
                                    <div class="text-sm text-gray-600">{{ cert.issuing_organization }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Issued: {{ cert.issue_date }}
                                        <span v-if="cert.expiry_date"> • Expires: {{ cert.expiry_date }}</span>
                                        <span v-if="cert.credential_id"> • ID: {{ cert.credential_id }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Below CV -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mt-4">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <div>Template: <span class="font-medium text-gray-900">{{ cv.cv_template.name }}</span></div>
                        <div class="flex items-center space-x-6">
                            <div>{{ cv.view_count }} views</div>
                            <div>{{ cv.download_count }} downloads</div>
                            <div>Last updated {{ new Date(cv.updated_at).toLocaleDateString() }}</div>
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
