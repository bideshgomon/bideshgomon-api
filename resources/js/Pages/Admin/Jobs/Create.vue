<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    countries: Array,
});

const form = useForm({
    title: '',
    company_name: '',
    company_logo: '',
    country_id: '',
    city: '',
    job_type: 'full-time',
    category: '',
    description: '',
    responsibilities: '',
    requirements: '',
    salary_min: '',
    salary_max: '',
    salary_currency: 'BDT',
    salary_period: 'monthly',
    experience_required: '',
    education_required: '',
    skills_required: [],
    benefits: [],
    gender_preference: 'any',
    age_min: '',
    age_max: '',
    positions_available: 1,
    application_fee: 0,
    agency_posted_fee: 0,
    admin_approved_fee: 0,
    approval_status: 'approved',
    is_featured: false,
    is_active: true,
    deadline: '',
    contact_email: '',
    contact_phone: '',
});

const skillInput = ref('');
const benefitInput = ref('');

const addSkill = () => {
    if (skillInput.value.trim()) {
        form.skills_required.push(skillInput.value.trim());
        skillInput.value = '';
    }
};

const removeSkill = (index) => {
    form.skills_required.splice(index, 1);
};

const addBenefit = () => {
    if (benefitInput.value.trim()) {
        form.benefits.push(benefitInput.value.trim());
        benefitInput.value = '';
    }
};

const removeBenefit = (index) => {
    form.benefits.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.jobs.store'));
};
</script>

<template>
    <Head title="Create Job Posting" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <Link
                    :href="route('admin.jobs.index')"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6"
                >
                    <ArrowLeftIcon class="h-5 w-5 mr-2" />
                    Back to Jobs
                </Link>

                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Job Posting</h1>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="e.g., Senior Software Engineer"
                                    />
                                    <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                                    <input
                                        v-model="form.company_name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.company_name" class="text-red-600 text-sm mt-1">{{ form.errors.company_name }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo URL</label>
                                    <input
                                        v-model="form.company_logo"
                                        type="url"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="https://example.com/logo.png"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                    <select
                                        v-model="form.country_id"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="">Select Country</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">
                                            {{ country.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.country_id" class="text-red-600 text-sm mt-1">{{ form.errors.country_id }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.city" class="text-red-600 text-sm mt-1">{{ form.errors.city }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Type *</label>
                                    <select
                                        v-model="form.job_type"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="full-time">Full-time</option>
                                        <option value="part-time">Part-time</option>
                                        <option value="contract">Contract</option>
                                        <option value="temporary">Temporary</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <input
                                        v-model="form.category"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="e.g., IT, Healthcare, Construction"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Details</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                                    <textarea
                                        v-model="form.description"
                                        required
                                        rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="Describe the job position..."
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
                                    <textarea
                                        v-model="form.responsibilities"
                                        rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="List job responsibilities..."
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                                    <textarea
                                        v-model="form.requirements"
                                        rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="List job requirements..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Salary & Compensation -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Salary & Compensation</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary *</label>
                                    <input
                                        v-model.number="form.salary_min"
                                        type="number"
                                        required
                                        min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary *</label>
                                    <input
                                        v-model.number="form.salary_max"
                                        type="number"
                                        required
                                        min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency *</label>
                                    <select
                                        v-model="form.salary_currency"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="BDT">BDT</option>
                                        <option value="USD">USD</option>
                                        <option value="AED">AED</option>
                                        <option value="SAR">SAR</option>
                                        <option value="QAR">QAR</option>
                                        <option value="KWD">KWD</option>
                                        <option value="MYR">MYR</option>
                                        <option value="SGD">SGD</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Salary Period *</label>
                                    <select
                                        v-model="form.salary_period"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                        <option value="hourly">Hourly</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Requirements</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience Required</label>
                                    <input
                                        v-model="form.experience_required"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="e.g., 3-5 years"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Education Required</label>
                                    <input
                                        v-model="form.education_required"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="e.g., Bachelor's Degree"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender Preference</label>
                                    <select
                                        v-model="form.gender_preference"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="any">Any</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Age</label>
                                        <input
                                            v-model.number="form.age_min"
                                            type="number"
                                            min="18"
                                            max="100"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Age</label>
                                        <input
                                            v-model.number="form.age_max"
                                            type="number"
                                            min="18"
                                            max="100"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        />
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Skills Required</label>
                                    <div class="flex gap-2 mb-2">
                                        <input
                                            v-model="skillInput"
                                            type="text"
                                            @keyup.enter="addSkill"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                            placeholder="Add a skill and press Enter"
                                        />
                                        <button
                                            type="button"
                                            @click="addSkill"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                        >
                                            Add
                                        </button>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(skill, index) in form.skills_required"
                                            :key="index"
                                            class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm flex items-center gap-2"
                                        >
                                            {{ skill }}
                                            <button type="button" @click="removeSkill(index)" class="text-indigo-900 hover:text-red-600">
                                                ×
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Benefits</label>
                                    <div class="flex gap-2 mb-2">
                                        <input
                                            v-model="benefitInput"
                                            type="text"
                                            @keyup.enter="addBenefit"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                            placeholder="Add a benefit and press Enter"
                                        />
                                        <button
                                            type="button"
                                            @click="addBenefit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                        >
                                            Add
                                        </button>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(benefit, index) in form.benefits"
                                            :key="index"
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm flex items-center gap-2"
                                        >
                                            {{ benefit }}
                                            <button type="button" @click="removeBenefit(index)" class="text-green-900 hover:text-red-600">
                                                ×
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Settings</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Positions Available *</label>
                                    <input
                                        v-model.number="form.positions_available"
                                        type="number"
                                        required
                                        min="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Deadline *</label>
                                    <input
                                        v-model="form.deadline"
                                        type="date"
                                        required
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                    <input
                                        v-model="form.contact_email"
                                        type="email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                    <input
                                        v-model="form.contact_phone"
                                        type="tel"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Application Fee & Processing Fee -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Application Fees & Processing</h2>
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-indigo-800">
                                    <strong>Note:</strong> Application fee is a small processing charge. Agency Posted Fee is the main fee amount. 
                                    Admin sets the final approved fee (includes markup). <strong>Public users will only see the final approved fee.</strong>
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Application Fee (৳)
                                        <span class="text-xs text-gray-500 block mt-1">Small processing charge</span>
                                    </label>
                                    <input
                                        v-model.number="form.application_fee"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="e.g. 500"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Agency Posted Fee (৳) *
                                        <span class="text-xs text-indigo-600 block mt-1 font-medium">Main fee amount</span>
                                    </label>
                                    <input
                                        v-model.number="form.agency_posted_fee"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="e.g. 300000"
                                        class="w-full px-4 py-2 border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-semibold"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Approved Fee (৳) *
                                        <span class="text-xs text-gray-500 block mt-1">Final amount (shown to public)</span>
                                    </label>
                                    <input
                                        v-model.number="form.admin_approved_fee"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="e.g. 350000"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Processing Fee (৳)
                                        <span class="text-xs text-gray-500 block mt-1">Auto-calculated markup</span>
                                    </label>
                                    <div class="text-2xl font-bold text-indigo-600">
                                        +৳{{ Number(Math.max(0, (form.admin_approved_fee || 0) - (form.agency_posted_fee || 0))).toLocaleString() }}
                                    </div>
                                    <div v-if="form.agency_posted_fee > 0 && form.admin_approved_fee > form.agency_posted_fee" class="text-xs text-gray-600 mt-1">
                                        {{ (((form.admin_approved_fee - form.agency_posted_fee) / form.agency_posted_fee) * 100).toFixed(1) }}% markup
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_featured"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Featured Job (appears at top of listings)</span>
                                </label>

                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active (visible to job seekers)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('admin.jobs.index')"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors disabled:opacity-50"
                            >
                                {{ form.processing ? 'Creating...' : 'Create Job Posting' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
