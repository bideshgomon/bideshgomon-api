<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    ArrowLeftIcon, ArrowRightIcon, CheckCircleIcon, 
    PlusIcon, TrashIcon, DocumentTextIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    template: Object,
    user: Object,
    countries: Array,
    profileEducation: Array,
    profileExperience: Array,
    profileLanguages: Array,
});

const currentStep = ref(1);
const totalSteps = 5;
const showPaymentConfirmation = ref(false);

const isPremiumTemplate = computed(() => {
    return props.template.is_premium && props.template.price > 0;
});

const userWalletBalance = computed(() => {
    return props.user.wallet?.balance || 0;
});

const hasEnoughBalance = computed(() => {
    return !isPremiumTemplate.value || userWalletBalance.value >= props.template.price;
});

const form = useForm({
    cv_template_id: props.template.id,
    title: `My ${props.template.name} CV`,
    full_name: props.user.name || '',
    email: props.user.email || '',
    phone: props.user.profile?.phone || '',
    city: props.user.profile?.city || '',
    country_id: props.user.profile?.country_id || null,
    address: props.user.profile?.address || '',
    linkedin_url: '',
    website_url: '',
    professional_summary: '',
    education: props.profileEducation || [],
    experience: props.profileExperience || [],
    skills: [],
    languages: props.profileLanguages || [],
    certifications: [],
    projects: [],
    references: [],
});

// Add new education entry
const addEducation = () => {
    form.education.push({
        degree: '',
        institution: '',
        field_of_study: '',
        start_date: '',
        end_date: '',
        grade: '',
        description: '',
    });
};

// Add new experience entry
const addExperience = () => {
    form.experience.push({
        job_title: '',
        company: '',
        location: '',
        start_date: '',
        end_date: '',
        is_current: false,
        description: '',
    });
};

// Add new skill
const addSkill = () => {
    form.skills.push({
        name: '',
        proficiency: 'intermediate',
    });
};

// Add new language
const addLanguage = () => {
    form.languages.push({
        language: '',
        proficiency: 'intermediate',
    });
};

// Add new certification
const addCertification = () => {
    form.certifications.push({
        name: '',
        issuing_organization: '',
        issue_date: '',
        expiry_date: '',
        credential_id: '',
    });
};

// Remove functions
const removeEducation = (index) => form.education.splice(index, 1);
const removeExperience = (index) => form.experience.splice(index, 1);
const removeSkill = (index) => form.skills.splice(index, 1);
const removeLanguage = (index) => form.languages.splice(index, 1);
const removeCertification = (index) => form.certifications.splice(index, 1);

// Navigation
const nextStep = () => {
    if (currentStep.value < totalSteps) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const canProceed = computed(() => {
    switch (currentStep.value) {
        case 1:
            return form.title && form.full_name && form.email && form.phone;
        case 2:
            return form.professional_summary && form.professional_summary.length >= 50;
        case 3:
            return form.education.length > 0;
        case 4:
            return form.experience.length > 0;
        case 5:
            return form.skills.length > 0;
        default:
            return true;
    }
});

const submit = () => {
    form.post(route('cv-builder.store'));
};

const stepTitles = [
    'Personal Information',
    'Professional Summary',
    'Education',
    'Work Experience',
    'Skills & Languages',
];
</script>

<template>
    <Head title="Create CV" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <Link :href="route('cv-builder.index')" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                            <ArrowLeftIcon class="h-5 w-5" />
                        </Link>
                        <div>
                            <h1 class="text-xl font-bold">Create Your CV</h1>
                            <p class="text-sm text-blue-100">{{ template.name }}</p>
                        </div>
                    </div>
                    <DocumentTextIcon class="h-10 w-10 text-blue-200 opacity-50" />
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium">Step {{ currentStep }} of {{ totalSteps }}</div>
                        <div class="text-sm text-blue-100">{{ Math.round((currentStep / totalSteps) * 100) }}% Complete</div>
                    </div>
                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-white transition-all duration-300"
                            :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Step Indicator -->
            <div class="mb-6 flex items-center justify-between">
                <div 
                    v-for="(step, index) in stepTitles" 
                    :key="index"
                    class="flex-1 relative"
                >
                    <div class="flex flex-col items-center">
                        <div 
                            class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-all"
                            :class="currentStep > index + 1 ? 'bg-green-500 text-white' : currentStep === index + 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'"
                        >
                            <CheckCircleIcon v-if="currentStep > index + 1" class="h-6 w-6" />
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <div class="text-xs mt-2 text-center hidden sm:block" :class="currentStep === index + 1 ? 'text-blue-600 font-semibold' : 'text-gray-500'">
                            {{ step }}
                        </div>
                    </div>
                    <div 
                        v-if="index < stepTitles.length - 1"
                        class="absolute top-5 left-1/2 w-full h-0.5 -z-10"
                        :class="currentStep > index + 1 ? 'bg-green-500' : 'bg-gray-200'"
                    ></div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Step 1: Personal Information -->
                <div v-show="currentStep === 1" class="space-y-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Personal Information</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CV Title *</label>
                        <input 
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Software Developer CV"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input 
                                v-model="form.full_name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input 
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                            <input 
                                v-model="form.phone"
                                type="tel"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input 
                                v-model="form.city"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <select 
                            v-model="form.country_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option :value="null">Select Country</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input 
                            v-model="form.address"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                            <input 
                                v-model="form.linkedin_url"
                                type="url"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://linkedin.com/in/username"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website/Portfolio</label>
                            <input 
                                v-model="form.website_url"
                                type="url"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://yourwebsite.com"
                            />
                        </div>
                    </div>
                </div>

                <!-- Step 2: Professional Summary -->
                <div v-show="currentStep === 2" class="space-y-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Professional Summary</h2>
                    <p class="text-sm text-gray-600 mb-4">Write a compelling 2-3 paragraph summary highlighting your experience, skills, and career goals. (Minimum 50 characters)</p>
                    
                    <div>
                        <textarea 
                            v-model="form.professional_summary"
                            rows="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Experienced software developer with 5+ years in full-stack development..."
                        ></textarea>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ form.professional_summary.length }} characters (minimum 50)
                        </div>
                    </div>
                </div>

                <!-- Step 3: Education -->
                <div v-show="currentStep === 3" class="space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Education</h2>
                        <button 
                            @click="addEducation"
                            type="button"
                            class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                        >
                            <PlusIcon class="h-4 w-4" />
                            <span>Add Education</span>
                        </button>
                    </div>

                    <div v-if="form.education.length === 0" class="text-center py-8 text-gray-500">
                        <p>No education entries yet. Click "Add Education" to start.</p>
                    </div>

                    <div 
                        v-for="(edu, index) in form.education" 
                        :key="index"
                        class="border border-gray-200 rounded-lg p-4 space-y-3 relative"
                    >
                        <button 
                            @click="removeEducation(index)"
                            type="button"
                            class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="h-5 w-5" />
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Degree</label>
                                <input 
                                    v-model="edu.degree"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Bachelor of Science"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                <input 
                                    v-model="edu.institution"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="University Name"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                            <input 
                                v-model="edu.field_of_study"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="Computer Science"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input 
                                    v-model="edu.start_date"
                                    type="month"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input 
                                    v-model="edu.end_date"
                                    type="month"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade/GPA</label>
                                <input 
                                    v-model="edu.grade"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="3.8/4.0"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Work Experience -->
                <div v-show="currentStep === 4" class="space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Work Experience</h2>
                        <button 
                            @click="addExperience"
                            type="button"
                            class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                        >
                            <PlusIcon class="h-4 w-4" />
                            <span>Add Experience</span>
                        </button>
                    </div>

                    <div v-if="form.experience.length === 0" class="text-center py-8 text-gray-500">
                        <p>No work experience yet. Click "Add Experience" to start.</p>
                    </div>

                    <div 
                        v-for="(exp, index) in form.experience" 
                        :key="index"
                        class="border border-gray-200 rounded-lg p-4 space-y-3 relative"
                    >
                        <button 
                            @click="removeExperience(index)"
                            type="button"
                            class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="h-5 w-5" />
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                                <input 
                                    v-model="exp.job_title"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Senior Developer"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                <input 
                                    v-model="exp.company"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Company Name"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input 
                                v-model="exp.location"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="Dhaka, Bangladesh"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input 
                                    v-model="exp.start_date"
                                    type="month"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input 
                                    v-model="exp.end_date"
                                    type="month"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    :disabled="exp.is_current"
                                />
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input 
                                v-model="exp.is_current"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 rounded"
                            />
                            <label class="ml-2 text-sm text-gray-700">I currently work here</label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea 
                                v-model="exp.description"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="Describe your responsibilities and achievements..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Skills & Languages -->
                <div v-show="currentStep === 5" class="space-y-6">
                    <!-- Skills -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Skills</h2>
                            <button 
                                @click="addSkill"
                                type="button"
                                class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                            >
                                <PlusIcon class="h-4 w-4" />
                                <span>Add Skill</span>
                            </button>
                        </div>

                        <div v-if="form.skills.length === 0" class="text-center py-8 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                            <p>No skills added yet. Click "Add Skill" to start.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div 
                                v-for="(skill, index) in form.skills" 
                                :key="index"
                                class="flex items-center space-x-2 border border-gray-200 rounded-lg p-3"
                            >
                                <input 
                                    v-model="skill.name"
                                    type="text"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Skill name"
                                />
                                <select 
                                    v-model="skill.proficiency"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                >
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                                <button 
                                    @click="removeSkill(index)"
                                    type="button"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Languages -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Languages</h2>
                            <button 
                                @click="addLanguage"
                                type="button"
                                class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                            >
                                <PlusIcon class="h-4 w-4" />
                                <span>Add Language</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div 
                                v-for="(lang, index) in form.languages" 
                                :key="index"
                                class="flex items-center space-x-2 border border-gray-200 rounded-lg p-3"
                            >
                                <input 
                                    v-model="lang.language"
                                    type="text"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Language name"
                                />
                                <select 
                                    v-model="lang.proficiency"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                >
                                    <option value="basic">Basic</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="fluent">Fluent</option>
                                    <option value="native">Native</option>
                                </select>
                                <button 
                                    @click="removeLanguage(index)"
                                    type="button"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Certifications (Optional) -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Certifications <span class="text-sm font-normal text-gray-500">(Optional)</span></h2>
                            <button 
                                @click="addCertification"
                                type="button"
                                class="flex items-center space-x-2 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm"
                            >
                                <PlusIcon class="h-4 w-4" />
                                <span>Add Certification</span>
                            </button>
                        </div>

                        <div 
                            v-for="(cert, index) in form.certifications" 
                            :key="index"
                            class="border border-gray-200 rounded-lg p-4 mb-3 space-y-3 relative"
                        >
                            <button 
                                @click="removeCertification(index)"
                                type="button"
                                class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                            >
                                <TrashIcon class="h-5 w-5" />
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Certification Name</label>
                                    <input 
                                        v-model="cert.name"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                        placeholder="AWS Certified Developer"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Issuing Organization</label>
                                    <input 
                                        v-model="cert.issuing_organization"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                        placeholder="Amazon Web Services"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                                    <input 
                                        v-model="cert.issue_date"
                                        type="month"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input 
                                        v-model="cert.expiry_date"
                                        type="month"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Credential ID</label>
                                    <input 
                                        v-model="cert.credential_id"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                        placeholder="Optional"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-6 flex items-center justify-between">
                <button 
                    v-if="currentStep > 1"
                    @click="prevStep"
                    type="button"
                    class="flex items-center space-x-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                    <span>Previous</span>
                </button>
                <div v-else></div>

                <button 
                    v-if="currentStep < totalSteps"
                    @click="nextStep"
                    :disabled="!canProceed"
                    type="button"
                    class="flex items-center space-x-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span>Next Step</span>
                    <ArrowRightIcon class="h-5 w-5" />
                </button>
                <button 
                    v-else
                    @click="isPremiumTemplate && !hasEnoughBalance ? null : (isPremiumTemplate ? (showPaymentConfirmation = true) : submit())"
                    :disabled="form.processing || !canProceed"
                    type="button"
                    class="flex items-center space-x-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <CheckCircleIcon class="h-5 w-5" />
                    <span>{{ form.processing ? 'Creating...' : 'Create CV' }}</span>
                </button>
            </div>

            <!-- Payment Insufficient Balance Warning -->
            <div v-if="isPremiumTemplate && !hasEnoughBalance" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">
                    ⚠️ Insufficient wallet balance. You need <span class="font-bold">৳{{ template.price }}</span> but have <span class="font-bold">৳{{ userWalletBalance }}</span>.
                    <Link :href="route('wallet.index')" class="underline font-medium">Add funds to your wallet</Link>
                </p>
            </div>
        </div>

        <!-- Payment Confirmation Modal -->
        <div 
            v-if="showPaymentConfirmation" 
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="showPaymentConfirmation = false"
        >
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Confirm Payment</h3>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">Template:</span>
                        <span class="font-semibold text-gray-900">{{ template.name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">Price:</span>
                        <span class="font-bold text-lg text-blue-600">৳{{ template.price }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">Current Balance:</span>
                        <span class="font-semibold" :class="hasEnoughBalance ? 'text-green-600' : 'text-red-600'">
                            ৳{{ userWalletBalance }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Balance After:</span>
                        <span class="font-semibold text-gray-900">৳{{ (userWalletBalance - template.price).toFixed(2) }}</span>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button
                        @click="showPaymentConfirmation = false"
                        type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        type="button"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50"
                    >
                        {{ form.processing ? 'Processing...' : 'Confirm & Pay' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
