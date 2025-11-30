<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    MapPinIcon, 
    BriefcaseIcon,
    CurrencyDollarIcon,
    CalendarIcon,
    ClockIcon,
    UserGroupIcon,
    AcademicCapIcon,
    CheckCircleIcon,
    XMarkIcon,
    ArrowLeftIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    job: Object,
    hasApplied: Boolean,
    application: Object,
    relatedJobs: Array,
});

const showApplicationModal = ref(false);

const form = useForm({
    cover_letter: '',
    user_cv_id: null,
});

const applyForJob = () => {
    form.post(route('jobs.apply', props.job.id), {
        onSuccess: () => {
            showApplicationModal.value = false;
            form.reset();
        },
    });
};

const formatSalary = (job) => {
    const symbol = job.salary_currency === 'BDT' ? '৳' : job.salary_currency;
    if (job.salary_max && job.salary_max > job.salary_min) {
        return `${symbol}${Number(job.salary_min).toLocaleString()} - ${symbol}${Number(job.salary_max).toLocaleString()}`;
    }
    return `${symbol}${Number(job.salary_min).toLocaleString()}`;
};

const getCategoryColor = (category) => {
    // Default color - can be extended with database color field later
    return 'bg-indigo-100 text-indigo-800';
};

const getJobTypeLabel = (type) => {
    const labels = {
        'full_time': 'Full Time',
        'part_time': 'Part Time',
        'contract': 'Contract',
        'temporary': 'Temporary',
        'seasonal': 'Seasonal',
        'internship': 'Internship',
    };
    return labels[type] || type.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
};
</script>

<template>
    <Head :title="job.title" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="bg-blue-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <Link
                    :href="route('jobs.index')"
                    class="inline-flex items-center text-gray-600 hover:text-blue-600 mb-6 font-medium transition-colors"
                >
                    <ArrowLeftIcon class="h-5 w-5 mr-2" />
                    Back to Jobs
                </Link>

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">{{ job.title }}</h1>
                        <p class="text-xl font-medium text-gray-700 mb-4">{{ job.company_name }}</p>
                        
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <div class="flex items-center font-medium">
                                <MapPinIcon class="h-5 w-5 mr-2 text-blue-600" />
                                {{ job.city }}, {{ job.country.name }}
                            </div>
                            <div class="flex items-center font-medium">
                                <BriefcaseIcon class="h-5 w-5 mr-2 text-blue-600" />
                                {{ getJobTypeLabel(job.job_type) }}
                            </div>
                            <div class="flex items-center font-medium">
                                <CalendarIcon class="h-5 w-5 mr-2 text-blue-600" />
                                Posted {{ new Date(job.published_at).toLocaleDateString() }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-0 sm:ml-6">
                        <button
                            v-if="!hasApplied"
                            @click="showApplicationModal = true"
                            class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center space-x-2"
                        >
                            <DocumentTextIcon class="h-5 w-5" />
                            <span>Apply Now</span>
                        </button>
                        <div v-else class="flex items-center space-x-2 px-8 py-3.5 bg-green-100 text-green-700 rounded-xl font-semibold border border-green-200">
                            <CheckCircleIcon class="h-5 w-5" />
                            <span>Already Applied</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Salary & Benefits -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-md">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <div class="text-sm font-medium text-gray-500 mb-2">Salary Range</div>
                                <div class="text-4xl font-bold text-blue-900">
                                    {{ formatSalary(job) }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">per {{ job.salary_period }}</div>
                            </div>
                            <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center border-2 border-blue-200">
                                <CurrencyDollarIcon class="h-10 w-10 text-blue-600" />
                            </div>
                        </div>
                        
                        <div v-if="job.benefits && job.benefits.length > 0" class="mt-4">
                            <div class="text-sm font-semibold text-gray-700 mb-2">Benefits:</div>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="benefit in job.benefits"
                                    :key="benefit"
                                    class="px-3 py-1 bg-gray-50 rounded-full text-sm text-gray-700 border border-gray-200"
                                >
                                    {{ benefit }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Job Description</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ job.description }}</p>
                    </div>

                    <!-- Responsibilities -->
                    <div v-if="job.responsibilities" class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Responsibilities</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ job.responsibilities }}</p>
                    </div>

                    <!-- Requirements -->
                    <div v-if="job.requirements" class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Requirements</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ job.requirements }}</p>
                    </div>

                    <!-- Skills -->
                    <div v-if="job.skills && job.skills.length > 0" class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Required Skills</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="skill in job.skills"
                                :key="skill"
                                class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-medium border border-indigo-100"
                            >
                                {{ skill }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Job Details Card -->
                    <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Job Details</h3>
                        
                        <div class="space-y-4">
                            <div v-if="job.job_category">
                                <div class="text-sm text-gray-500 mb-1">Category</div>
                                <span :class="['inline-block px-3 py-1 rounded-full text-sm font-medium', getCategoryColor(job.job_category.slug)]">
                                    {{ job.job_category.name }}
                                </span>
                            </div>

                            <div v-if="job.positions_available">
                                <div class="text-sm text-gray-500 mb-1">Positions Available</div>
                                <div class="flex items-center text-gray-900">
                                    <UserGroupIcon class="h-5 w-5 mr-2 text-gray-400" />
                                    {{ job.positions_available }} positions
                                </div>
                            </div>

                            <div v-if="job.experience_years">
                                <div class="text-sm text-gray-500 mb-1">Experience Required</div>
                                <div class="text-gray-900">{{ job.experience_years }} years</div>
                            </div>

                            <div v-if="job.education_level">
                                <div class="text-sm text-gray-500 mb-1">Education Required</div>
                                <div class="flex items-center text-gray-900">
                                    <AcademicCapIcon class="h-5 w-5 mr-2 text-gray-400" />
                                    {{ job.education_level }}
                                </div>
                            </div>

                            <div v-if="job.gender_preference">
                                <div class="text-sm text-gray-500 mb-1">Gender Preference</div>
                                <div class="text-gray-900 capitalize">{{ job.gender_preference }}</div>
                            </div>

                            <div v-if="job.age_min || job.age_max">
                                <div class="text-sm text-gray-500 mb-1">Age Range</div>
                                <div class="text-gray-900">
                                    {{ job.age_min }}{{ job.age_max ? ` - ${job.age_max}` : '+' }} years
                                </div>
                            </div>

                            <div v-if="job.application_deadline">
                                <div class="text-sm text-gray-500 mb-1">Application Deadline</div>
                                <div class="flex items-center text-orange-600">
                                    <ClockIcon class="h-5 w-5 mr-2" />
                                    {{ new Date(job.application_deadline).toLocaleDateString() }}
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <div class="text-sm text-gray-500 mb-1">Application Fee</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ job.application_fee > 0 ? `৳${Number(job.application_fee).toLocaleString()}` : 'Free' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div v-if="job.contact_email || job.contact_phone" class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Contact Information</h3>
                        <div class="space-y-2 text-sm">
                            <div v-if="job.contact_email">
                                <div class="text-gray-500">Email</div>
                                <a :href="`mailto:${job.contact_email}`" class="text-indigo-600 hover:text-indigo-700">
                                    {{ job.contact_email }}
                                </a>
                            </div>
                            <div v-if="job.contact_phone">
                                <div class="text-gray-500">Phone</div>
                                <a :href="`tel:${job.contact_phone}`" class="text-indigo-600 hover:text-indigo-700">
                                    {{ job.contact_phone }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Related Jobs -->
                    <div v-if="relatedJobs.length > 0" class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Related Jobs</h3>
                        <div class="space-y-3">
                            <Link
                                v-for="relatedJob in relatedJobs"
                                :key="relatedJob.id"
                                :href="route('jobs.show', relatedJob.id)"
                                class="block p-4 border border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/50 transition-all"
                            >
                                <div class="font-medium text-gray-900 mb-1">{{ relatedJob.title }}</div>
                                <div class="text-sm text-gray-600">{{ relatedJob.company_name }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ relatedJob.country.name }}</div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Modal -->
        <div v-if="showApplicationModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showApplicationModal = false"></div>

                <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl">
                    <!-- Modal Header -->
                    <div class="bg-blue-600 px-6 py-5 border-b-2 border-blue-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <DocumentTextIcon class="h-6 w-6 mr-2" />
                                Apply for this Job
                            </h3>
                            <button @click="showApplicationModal = false" class="text-white/80 hover:text-white transition-colors">
                                <XMarkIcon class="h-6 w-6" />
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="applyForJob" class="p-6">
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <DocumentTextIcon class="h-5 w-5 text-blue-600 mt-0.5 mr-2" />
                                <div class="text-sm text-blue-800">
                                    <div class="font-semibold mb-1">Application Fee: {{ job.application_fee > 0 ? `৳${Number(job.application_fee).toLocaleString()}` : 'Free' }}</div>
                                    <div v-if="job.application_fee > 0">The fee will be deducted from your wallet balance.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Cover Letter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cover Letter <span class="text-gray-400">(Optional)</span>
                            </label>
                            <textarea
                                v-model="form.cover_letter"
                                rows="6"
                                placeholder="Tell us why you're a great fit for this position..."
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                            <div v-if="form.errors.cover_letter" class="text-red-600 text-sm mt-1">
                                {{ form.errors.cover_letter }}
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button
                                type="button"
                                @click="showApplicationModal = false"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg disabled:opacity-50"
                            >
                                {{ form.processing ? 'Submitting...' : 'Submit Application' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
