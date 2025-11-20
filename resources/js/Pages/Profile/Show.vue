<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';

const props = defineProps({
    user: Object,
    userProfile: Object,
    familyMembers: Array,
    languages: Array,
    securityInformation: Object,
    educations: Array,
    workExperiences: Array,
    skills: Array,
    travelHistory: Array,
    phoneNumbers: Array,
    profileCompletion: Number,
});

const { formatCurrency, formatPhone, formatDate, divisions } = useBangladeshFormat();

const completionColor = () => {
    if (props.profileCompletion >= 80) return 'text-green-600';
    if (props.profileCompletion >= 50) return 'text-yellow-600';
    return 'text-red-600';
};

const getRiskLevel = () => {
    if (!props.securityInformation) return null;
    const score = props.securityInformation.risk_score || 0;
    if (score >= 75) return { text: 'High Risk', color: 'text-red-600 bg-red-50' };
    if (score >= 50) return { text: 'Medium Risk', color: 'text-yellow-600 bg-yellow-50' };
    if (score >= 25) return { text: 'Low Risk', color: 'text-blue-600 bg-blue-50' };
    return { text: 'Minimal Risk', color: 'text-green-600 bg-green-50' };
};

const getProficiencyLabel = (level) => {
    const levels = {
        'native': 'Native',
        'fluent': 'Fluent',
        'advanced': 'Advanced',
        'intermediate': 'Intermediate',
        'beginner': 'Beginner',
    };
    return levels[level] || level;
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile Overview</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ user.name }}</p>
                </div>
                <Link 
                    :href="route('profile.edit')" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    style="min-height: 44px"
                >
                    Edit Profile
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
                <!-- Mobile Sticky Profile Card -->
                <div class="sticky top-0 z-10 -mx-4 sm:mx-0 sm:relative">
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 sm:rounded-xl shadow-lg">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-2xl sm:text-3xl">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg sm:text-xl font-bold text-white truncate">{{ user.name }}</h3>
                                    <p class="text-sm text-indigo-100">{{ user.email }}</p>
                                    <p class="text-xs text-indigo-200 mt-1 capitalize">{{ user.role?.name || 'User' }}</p>
                                </div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-white">Profile Completion</span>
                                    <span class="text-lg font-bold text-white">{{ profileCompletion }}%</span>
                                </div>
                                <div class="w-full bg-white/20 rounded-full h-2.5">
                                    <div 
                                        class="h-2.5 rounded-full transition-all duration-500"
                                        :class="{
                                            'bg-green-400': profileCompletion >= 80,
                                            'bg-yellow-400': profileCompletion >= 50 && profileCompletion < 80,
                                            'bg-red-400': profileCompletion < 50
                                        }"
                                        :style="{ width: profileCompletion + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-200">
                    <div class="h-1 bg-gradient-to-r from-gray-500 to-slate-600"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">👤</span>
                            <span>Basic Information</span>
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-medium">{{ user.name }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 break-all">{{ user.email }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Role</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">{{ user.role?.name || 'N/A' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userProfile?.phone ? formatPhone(userProfile.phone) : 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userProfile?.dob ? formatDate(userProfile.dob) : 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Gender</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">
                                    {{ userProfile?.gender || 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nationality</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ userProfile?.nationality || 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bio</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userProfile?.bio || 'No bio provided' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Phone Numbers -->
                <div v-if="phoneNumbers && phoneNumbers.length > 0" class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-200">
                    <div class="h-1 bg-gradient-to-r from-sky-500 to-blue-600"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">📱</span>
                            <span>Phone Numbers</span>
                        </h3>
                        <div class="space-y-3">
                            <div v-for="phone in phoneNumbers" :key="phone.id" class="bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-200 rounded-lg p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xl">
                                                {{ phone.phone_type === 'mobile' ? '📱' : phone.phone_type === 'work' ? '💼' : phone.phone_type === 'whatsapp' ? '💬' : '🏠' }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a :href="`tel:${phone.country_code}${phone.phone_number}`" class="text-base font-semibold text-gray-900 hover:text-sky-600">
                                                {{ phone.country_code }} {{ phone.phone_number }}
                                            </a>
                                            <div class="text-xs text-gray-600 capitalize mt-0.5">{{ phone.phone_type }}</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span v-if="phone.is_primary" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-600 text-white">
                                            Primary
                                        </span>
                                        <span v-if="phone.is_verified" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white">
                                            ✓ Verified
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education & Qualifications -->
                <div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-200">
                    <div class="h-1 bg-gradient-to-r from-purple-500 to-indigo-600"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🎓</span>
                            <span>Education & Qualifications</span>
                        </h3>
                        <div v-if="educations && educations.length > 0" class="space-y-3">
                            <div v-for="education in educations" :key="education.id" class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-4">
                                <div class="flex justify-between items-start gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ education.institution_name }}</h4>
                                        <p class="text-sm text-gray-700 font-medium mt-1">{{ education.degree }}</p>
                                        <p v-if="education.field_of_study" class="text-xs text-gray-600 mt-0.5">{{ education.field_of_study }}</p>
                                    </div>
                                    <span v-if="education.is_completed" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-600 text-white flex-shrink-0">
                                        Completed
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-600 text-white flex-shrink-0">
                                        In Progress
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Start Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(education.start_date) }}</dd>
                                    </div>
                                    <div v-if="education.end_date" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">End Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(education.end_date) }}</dd>
                                    </div>
                                    <div v-if="education.gpa_or_grade" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">GPA/Grade</dt>
                                        <dd class="text-gray-900 font-bold mt-0.5">{{ education.gpa_or_grade }}</dd>
                                    </div>
                                    <div v-if="education.country" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Location</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ education.city }}, {{ education.country }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            <span class="text-4xl block mb-2">📚</span>
                            No education records added yet
                        </div>
                    </div>
                </div>

                <!-- Work Experience -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-orange-50 to-amber-50 shadow sm:rounded-lg border border-orange-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">💼</span> Work Experience
                    </h3>
                    <div v-if="workExperiences && workExperiences.length > 0" class="space-y-4">
                        <div v-for="work in workExperiences" :key="work.id" class="bg-white rounded-lg p-4 shadow-sm border border-orange-100">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ work.position }}</h4>
                                    <p class="text-sm text-gray-600">{{ work.company_name }}</p>
                                </div>
                                <span v-if="work.is_current" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Current
                                </span>
                            </div>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-3">
                                <div>
                                    <dt class="text-gray-500">Start Date</dt>
                                    <dd class="text-gray-900">{{ formatDate(work.start_date) }}</dd>
                                </div>
                                <div v-if="work.end_date">
                                    <dt class="text-gray-500">End Date</dt>
                                    <dd class="text-gray-900">{{ formatDate(work.end_date) }}</dd>
                                </div>
                                <div v-if="work.country" class="col-span-2">
                                    <dt class="text-gray-500">Location</dt>
                                    <dd class="text-gray-900">{{ work.city }}, {{ work.country }}</dd>
                                </div>
                            </dl>
                            <div v-if="work.responsibilities" class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                {{ work.responsibilities }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No work experience added yet
                    </div>
                </div>

                <!-- Skills & Expertise -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-teal-50 to-cyan-50 shadow sm:rounded-lg border border-teal-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">⚡</span> Skills & Expertise
                    </h3>
                    <div v-if="skills && skills.length > 0" class="space-y-4">
                        <div v-for="userSkill in skills" :key="userSkill.id" class="bg-white rounded-lg p-4 shadow-sm border border-teal-100">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ userSkill.skill?.name || 'N/A' }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 capitalize">
                                    {{ userSkill.proficiency_level }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Experience:</span>
                                    <span class="text-gray-900 font-medium ml-1">{{ userSkill.years_of_experience }} years</span>
                                </div>
                                <div v-if="userSkill.skill?.category" class="text-gray-500">
                                    • {{ userSkill.skill.category }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No skills added yet
                    </div>
                </div>

                <!-- Travel History -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-sky-50 to-blue-50 shadow sm:rounded-lg border border-sky-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">✈️</span> Travel History
                    </h3>
                    <div v-if="travelHistory && travelHistory.length > 0" class="space-y-4">
                        <div v-for="travel in travelHistory" :key="travel.id" class="bg-white rounded-lg p-4 shadow-sm border border-sky-100">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ travel.country_visited }}</h4>
                                    <p class="text-sm text-gray-600">{{ travel.city_visited }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 capitalize">
                                    {{ travel.purpose }}
                                </span>
                            </div>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <div>
                                    <dt class="text-gray-500">Entry Date</dt>
                                    <dd class="text-gray-900">{{ formatDate(travel.entry_date) }}</dd>
                                </div>
                                <div v-if="travel.exit_date">
                                    <dt class="text-gray-500">Exit Date</dt>
                                    <dd class="text-gray-900">{{ formatDate(travel.exit_date) }}</dd>
                                </div>
                                <div v-if="travel.duration_days">
                                    <dt class="text-gray-500">Duration</dt>
                                    <dd class="text-gray-900 font-medium">{{ travel.duration_days }} days</dd>
                                </div>
                                <div v-if="travel.accommodation_type">
                                    <dt class="text-gray-500">Accommodation</dt>
                                    <dd class="text-gray-900 capitalize">{{ travel.accommodation_type }}</dd>
                                </div>
                                <div v-if="travel.transportation_mode">
                                    <dt class="text-gray-500">Transportation</dt>
                                    <dd class="text-gray-900 capitalize">{{ travel.transportation_mode }}</dd>
                                </div>
                                <div v-if="travel.visa_type_used">
                                    <dt class="text-gray-500">Visa Type</dt>
                                    <dd class="text-gray-900">{{ travel.visa_type_used }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No travel history recorded yet
                    </div>
                </div>

                <!-- Address Information -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">📍</span> Address Information
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Present Address</h4>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Division</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ userProfile?.present_division || 'Not provided' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">District</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ userProfile?.present_district || 'Not provided' }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-3">
                                    <dt class="text-sm font-medium text-gray-500">Full Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ userProfile?.present_address_line || 'Not provided' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Permanent Address</h4>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Division</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ userProfile?.permanent_division || 'Not provided' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">District</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ userProfile?.permanent_district || 'Not provided' }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-3">
                                    <dt class="text-sm font-medium text-gray-500">Full Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ userProfile?.permanent_address_line || 'Not provided' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">📄</span> Documents
                    </h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">National ID (NID)</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ userProfile?.nid || 'Not provided' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Passport Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ userProfile?.passport_number || 'Not provided' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Passport Issue Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ userProfile?.passport_issue_date ? formatDate(userProfile.passport_issue_date) : 'Not provided' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Passport Expiry Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ userProfile?.passport_expiry_date ? formatDate(userProfile.passport_expiry_date) : 'Not provided' }}        
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Family Members -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-purple-50 to-pink-50 shadow sm:rounded-lg border border-purple-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">👨‍👩‍👧‍👦</span> Family Members
                    </h3>
                    <div v-if="familyMembers && familyMembers.length > 0" class="space-y-4">
                        <div v-for="member in familyMembers" :key="member.id" class="bg-white rounded-lg p-4 shadow-sm border border-purple-100">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ member.full_name }}</h4>
                                    <p class="text-sm text-gray-600 capitalize">{{ member.relationship }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ member.relationship }}
                                </span>
                            </div>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-500">Date of Birth</dt>
                                    <dd class="text-gray-900">{{ member.date_of_birth ? formatDate(member.date_of_birth) : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Gender</dt>
                                    <dd class="text-gray-900 capitalize">{{ member.gender || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Nationality</dt>
                                    <dd class="text-gray-900">{{ member.nationality || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Phone</dt>
                                    <dd class="text-gray-900">{{ member.contact_phone ? formatPhone(member.contact_phone) : 'N/A' }}</dd>
                                </div>
                                <div v-if="member.occupation" class="col-span-2">
                                    <dt class="text-gray-500">Occupation</dt>
                                    <dd class="text-gray-900">{{ member.occupation }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No family members added yet
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-green-50 to-emerald-50 shadow sm:rounded-lg border border-green-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">💰</span> Financial Information
                    </h3>
                    
                    <!-- Financial Summary -->
                    <div v-if="userProfile?.monthly_income_bdt || userProfile?.bank_balance_bdt || userProfile?.net_worth_bdt" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-green-100">
                            <dt class="text-sm text-gray-600">Monthly Income</dt>
                            <dd class="text-2xl font-semibold text-green-600 mt-1">
                                {{ userProfile?.monthly_income_bdt ? formatCurrency(userProfile.monthly_income_bdt) : '৳0' }}
                            </dd>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-green-100">
                            <dt class="text-sm text-gray-600">Bank Balance</dt>
                            <dd class="text-2xl font-semibold text-blue-600 mt-1">
                                {{ userProfile?.bank_balance_bdt ? formatCurrency(userProfile.bank_balance_bdt) : '৳0' }}
                            </dd>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-green-100">
                            <dt class="text-sm text-gray-600">Net Worth</dt>
                            <dd class="text-2xl font-semibold text-purple-600 mt-1">
                                {{ userProfile?.net_worth_bdt ? formatCurrency(userProfile.net_worth_bdt) : '৳0' }}
                            </dd>
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div v-if="userProfile?.employer_name" class="bg-white rounded-lg p-4 shadow-sm border border-green-100 mb-4">
                        <h4 class="font-medium text-gray-900 mb-3">Employment</h4>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">Employer</dt>
                                <dd class="text-gray-900">{{ userProfile.employer_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Annual Income</dt>
                                <dd class="text-gray-900">{{ userProfile.annual_income_bdt ? formatCurrency(userProfile.annual_income_bdt) : 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Assets -->
                    <div v-if="userProfile?.owns_property || userProfile?.owns_vehicle" class="bg-white rounded-lg p-4 shadow-sm border border-green-100 mb-4">
                        <h4 class="font-medium text-gray-900 mb-3">Assets</h4>
                        <div class="space-y-3 text-sm">
                            <div v-if="userProfile?.owns_property">
                                <dt class="text-gray-500">Property</dt>
                                <dd class="text-gray-900">{{ userProfile.property_type }} - {{ formatCurrency(userProfile.property_value_bdt || 0) }}</dd>
                            </div>
                            <div v-if="userProfile?.owns_vehicle">
                                <dt class="text-gray-500">Vehicle</dt>
                                <dd class="text-gray-900">{{ userProfile.vehicle_make_model }} ({{ userProfile.vehicle_year }}) - {{ formatCurrency(userProfile.vehicle_value_bdt || 0) }}</dd>
                            </div>
                        </div>
                    </div>

                    <div v-if="!userProfile?.monthly_income_bdt && !userProfile?.bank_balance_bdt" class="text-center py-8 text-gray-500">
                        No financial information provided yet
                    </div>
                </div>

                <!-- Language Proficiency -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-blue-50 to-indigo-50 shadow sm:rounded-lg border border-blue-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">🗣️</span> Language Proficiency
                    </h3>
                    <div v-if="languages && languages.length > 0" class="space-y-4">
                        <div v-for="language in languages" :key="language.id" class="bg-white rounded-lg p-4 shadow-sm border border-blue-100">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ language.language_name }}</h4>
                                    <p class="text-sm text-gray-600">{{ getProficiencyLabel(language.proficiency) }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                    {{ language.proficiency }}
                                </span>
                            </div>
                            <div v-if="language.test_taken && language.test_taken !== 'none'" class="mt-3 pt-3 border-t border-blue-100">
                                <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                    <div>
                                        <dt class="text-gray-500">Test</dt>
                                        <dd class="text-gray-900 uppercase">{{ language.test_taken }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Overall Score</dt>
                                        <dd class="text-gray-900 font-medium">{{ language.overall_score || 'N/A' }}</dd>
                                    </div>
                                    <div v-if="language.reading_score">
                                        <dt class="text-gray-500">Reading</dt>
                                        <dd class="text-gray-900">{{ language.reading_score }}</dd>
                                    </div>
                                    <div v-if="language.writing_score">
                                        <dt class="text-gray-500">Writing</dt>
                                        <dd class="text-gray-900">{{ language.writing_score }}</dd>
                                    </div>
                                    <div v-if="language.listening_score">
                                        <dt class="text-gray-500">Listening</dt>
                                        <dd class="text-gray-900">{{ language.listening_score }}</dd>
                                    </div>
                                    <div v-if="language.speaking_score">
                                        <dt class="text-gray-500">Speaking</dt>
                                        <dd class="text-gray-900">{{ language.speaking_score }}</dd>
                                    </div>
                                    <div v-if="language.test_date" class="col-span-2">
                                        <dt class="text-gray-500">Test Date</dt>
                                        <dd class="text-gray-900">{{ formatDate(language.test_date) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No languages added yet
                    </div>
                </div>

                <!-- Security & Background -->
                <div class="p-4 sm:p-8 bg-gradient-to-br from-red-50 to-orange-50 shadow sm:rounded-lg border border-red-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="text-2xl mr-2">🛡️</span> Security & Background
                        </h3>
                        <span v-if="getRiskLevel()" :class="[getRiskLevel().color, 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium']">
                            {{ getRiskLevel().text }}
                        </span>
                    </div>
                    
                    <div v-if="securityInformation" class="space-y-4">
                        <!-- Criminal Records -->
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-red-100">
                            <h4 class="font-medium text-gray-900 mb-3">Criminal Records</h4>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-500">Has Criminal Record</dt>
                                    <dd class="text-gray-900">
                                        <span :class="securityInformation.has_criminal_record ? 'text-red-600 font-medium' : 'text-green-600'">
                                            {{ securityInformation.has_criminal_record ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                <div v-if="securityInformation.criminal_record_details">
                                    <dt class="text-gray-500">Details</dt>
                                    <dd class="text-gray-900">{{ securityInformation.criminal_record_details }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Police Clearance -->
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-red-100">
                            <h4 class="font-medium text-gray-900 mb-3">Police Clearance</h4>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-500">Has Clearance</dt>
                                    <dd class="text-gray-900">
                                        <span :class="securityInformation.has_police_clearance ? 'text-green-600 font-medium' : 'text-gray-600'">
                                            {{ securityInformation.has_police_clearance ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                <div v-if="securityInformation.police_clearance_date">
                                    <dt class="text-gray-500">Clearance Date</dt>
                                    <dd class="text-gray-900">{{ formatDate(securityInformation.police_clearance_date) }}</dd>
                                </div>
                                <div v-if="securityInformation.police_clearance_country">
                                    <dt class="text-gray-500">Country</dt>
                                    <dd class="text-gray-900">{{ securityInformation.police_clearance_country }}</dd>
                                </div>
                                <div v-if="securityInformation.police_clearance_expiry_date">
                                    <dt class="text-gray-500">Expiry Date</dt>
                                    <dd class="text-gray-900">{{ formatDate(securityInformation.police_clearance_expiry_date) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Risk Assessment -->
                        <div v-if="securityInformation.risk_score" class="bg-white rounded-lg p-4 shadow-sm border border-red-100">
                            <h4 class="font-medium text-gray-900 mb-3">Risk Assessment</h4>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-500">Risk Score</span>
                                        <span class="font-medium">{{ securityInformation.risk_score }}/100</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="h-2 rounded-full transition-all duration-500"
                                            :class="{
                                                'bg-green-500': securityInformation.risk_score < 25,
                                                'bg-blue-500': securityInformation.risk_score >= 25 && securityInformation.risk_score < 50,
                                                'bg-yellow-500': securityInformation.risk_score >= 50 && securityInformation.risk_score < 75,
                                                'bg-red-500': securityInformation.risk_score >= 75
                                            }"
                                            :style="{ width: securityInformation.risk_score + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No security information provided yet
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
