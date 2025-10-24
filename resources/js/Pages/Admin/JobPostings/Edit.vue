<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    posting: Object,
    categories: Array,
    countries: Array,
});

// Initialize form with existing data, converting numbers/dates as needed
const form = useForm({
    _method: 'PUT',
    job_category_id: props.posting.job_category_id || null,
    country_id: props.posting.country_id || null,
    title: props.posting.title,
    company_name: props.posting.company_name,
    location_city: props.posting.location_city || '',
    employment_type: props.posting.employment_type || 'Full-time',
    description: props.posting.description,
    responsibilities: props.posting.responsibilities || '',
    qualifications: props.posting.qualifications || '',
    skills_required: props.posting.skills_required || '',
    salary_min: props.posting.salary_min ? String(props.posting.salary_min) : '',
    salary_max: props.posting.salary_max ? String(props.posting.salary_max) : '',
    salary_currency: props.posting.salary_currency || 'USD',
    salary_period: props.posting.salary_period || 'Yearly',
    apply_url: props.posting.apply_url || '',
    posting_date: props.posting.posting_date || '', // Assumes YYYY-MM-DD format
    closing_date: props.posting.closing_date || '', // Assumes YYYY-MM-DD format
    status: props.posting.status || 'active',
    is_featured: props.posting.is_featured || false,
});

const submit = () => {
    form.put(route('api.admin.job-postings.update', props.posting.id));
};

const deletePosting = () => {
    if (confirm('Are you sure you want to delete this job posting?')) {
        router.delete(route('api.admin.job-postings.destroy', props.posting.id), {
            onSuccess: () => router.visit(route('admin.job-postings.index')),
        });
    }
};

const employmentTypes = ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship'];
const salaryPeriods = ['Hourly', 'Weekly', 'Monthly', 'Yearly'];
const statuses = ['active', 'expired', 'filled'];

</script>

<template>
    <Head :title="'Edit Job: ' + posting.title" />

    <AdminLayout>
         <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Job Posting: {{ posting.title }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update the job opportunity details.</p>
                </header>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="title" value="Job Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="company_name" value="Company Name" />
                            <TextInput id="company_name" type="text" class="mt-1 block w-full" v-model="form.company_name" required />
                            <InputError class="mt-2" :message="form.errors.company_name" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                         <div>
                            <InputLabel for="job_category_id" value="Job Category" />
                            <select id="job_category_id" v-model="form.job_category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option :value="null">Select Category (Optional)</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.job_category_id" />
                        </div>
                        <div>
                            <InputLabel for="country_id" value="Country" />
                            <select id="country_id" v-model="form.country_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                 <option :value="null">Select Country (Optional)</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.country_id" />
                        </div>
                         <div>
                            <InputLabel for="location_city" value="City (Optional)" />
                            <TextInput id="location_city" type="text" class="mt-1 block w-full" v-model="form.location_city" />
                            <InputError class="mt-2" :message="form.errors.location_city" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="employment_type" value="Employment Type" />
                        <select id="employment_type" v-model="form.employment_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option v-for="type in employmentTypes" :key="type" :value="type">{{ type }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.employment_type" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Job Description" />
                        <TextareaInput id="description" class="mt-1 block w-full" v-model="form.description" rows="5" required />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                     <div>
                        <InputLabel for="responsibilities" value="Responsibilities (Optional)" />
                        <TextareaInput id="responsibilities" class="mt-1 block w-full" v-model="form.responsibilities" rows="3" />
                        <InputError class="mt-2" :message="form.errors.responsibilities" />
                    </div>
                     <div>
                        <InputLabel for="qualifications" value="Qualifications (Optional)" />
                        <TextareaInput id="qualifications" class="mt-1 block w-full" v-model="form.qualifications" rows="3" />
                        <InputError class="mt-2" :message="form.errors.qualifications" />
                    </div>
                     <div>
                        <InputLabel for="skills_required" value="Skills Required (Optional, comma-separated)" />
                        <TextInput id="skills_required" type="text" class="mt-1 block w-full" v-model="form.skills_required" />
                        <InputError class="mt-2" :message="form.errors.skills_required" />
                    </div>

                    <div class="border-t dark:border-gray-700 pt-6">
                        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Salary Information (Optional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <InputLabel for="salary_min" value="Min Salary" />
                                <TextInput id="salary_min" type="number" step="0.01" class="mt-1 block w-full" v-model="form.salary_min" />
                                <InputError class="mt-2" :message="form.errors.salary_min" />
                            </div>
                             <div>
                                <InputLabel for="salary_max" value="Max Salary" />
                                <TextInput id="salary_max" type="number" step="0.01" class="mt-1 block w-full" v-model="form.salary_max" />
                                <InputError class="mt-2" :message="form.errors.salary_max" />
                            </div>
                             <div>
                                <InputLabel for="salary_currency" value="Currency" />
                                <TextInput id="salary_currency" type="text" class="mt-1 block w-full" v-model="form.salary_currency" placeholder="e.g., USD" />
                                <InputError class="mt-2" :message="form.errors.salary_currency" />
                            </div>
                            <div>
                                <InputLabel for="salary_period" value="Period" />
                                <select id="salary_period" v-model="form.salary_period" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                    <option v-for="period in salaryPeriods" :key="period" :value="period">{{ period }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.salary_period" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t dark:border-gray-700 pt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                         <div>
                            <InputLabel for="apply_url" value="Apply URL (Optional)" />
                            <TextInput id="apply_url" type="url" class="mt-1 block w-full" v-model="form.apply_url" placeholder="https://..." />
                            <InputError class="mt-2" :message="form.errors.apply_url" />
                        </div>
                        <div>
                            <InputLabel for="posting_date" value="Posting Date (Optional)" />
                            <TextInput id="posting_date" type="date" class="mt-1 block w-full" v-model="form.posting_date" />
                            <InputError class="mt-2" :message="form.errors.posting_date" />
                        </div>
                         <div>
                            <InputLabel for="closing_date" value="Closing Date (Optional)" />
                            <TextInput id="closing_date" type="date" class="mt-1 block w-full" v-model="form.closing_date" />
                            <InputError class="mt-2" :message="form.errors.closing_date" />
                        </div>
                    </div>

                     <div class="border-t dark:border-gray-700 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div>
                            <InputLabel for="status" value="Status" />
                            <select id="status" v-model="form.status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize">{{ s }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                         <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <Checkbox name="is_featured" v-model:checked="form.is_featured" />
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Featured Job</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.is_featured" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-6 border-t dark:border-gray-700">
                         <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">{{ form.processing ? 'Saving...' : 'Update Job Posting' }}</PrimaryButton>
                            <Link :href="route('admin.job-postings.index')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-25 transition ease-in-out duration-150">Cancel</Link>
                        </div>
                         <DangerButton @click="deletePosting" type="button" :disabled="form.processing">Delete Posting</DangerButton>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>