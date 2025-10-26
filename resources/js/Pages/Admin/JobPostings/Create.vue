<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    jobCategories: Array,
    countries: Array
});

const form = useForm({
    title: '',
    job_category_id: null,
    country_id: null,
    state_id: null,
    city_id: null,
    company_name: '',
    description: '',
    responsibilities: '',
    requirements: '',
    salary_range: '',
    employment_type: 'full-time',
    experience_level: 'entry',
    education_level: '',
    application_deadline: '',
    status: 'draft',
});

const states = ref([]);
const cities = ref([]);

const loadStates = async () => {
    if (form.country_id) {
        try {
            // This axios call to an API route is fine
            const response = await axios.get(route('api.admin.countries.states.index', { country: form.country_id }));
            states.value = response.data;
            form.state_id = null;
            form.city_id = null;
            cities.value = [];
        } catch (error) {
            console.error('Error loading states:', error);
        }
    } else {
        states.value = [];
        cities.value = [];
        form.state_id = null;
        form.city_id = null;
    }
};

const loadCities = async () => {
    if (form.state_id) {
        try {
            // This axios call to an API route is fine
            const response = await axios.get(route('api.admin.states.cities.index', { state: form.state_id }));
            cities.value = response.data;
            form.city_id = null;
        } catch (error) {
            console.error('Error loading cities:', error);
        }
    } else {
        cities.value = [];
        form.city_id = null;
    }
};

const submit = () => {
    //
    // --- THE FIX ---
    // Point to the web route, not the api route.
    //
    form.post(route('admin.job-postings.store'), {
        // We don't need onSuccess because the backend
        // will redirect, which Inertia handles automatically.
        // The 'form.reset()' and 'recentlySuccessful'
        // will work correctly now.
        onError: (errors) => {
            console.error('Error creating job posting:', errors);
        }
    });
};
</script>

<template>
    <Head title="Create Job Posting" />
    <AdminLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Create New Job Posting</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Job Posting Information</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Fill in the details for the new job opening.
                </p>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    
                    <div>
                        <InputLabel for="title" value="Job Title" />
                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div>
                        <InputLabel for="company_name" value="Company Name" />
                        <TextInput
                            id="company_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.company_name"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.company_name" />
                    </div>

                    <div>
                        <InputLabel for="job_category_id" value="Job Category" />
                        <select
                            id="job_category_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="form.job_category_id"
                            required
                        >
                            <option :value="null" disabled>Select a category</option>
                            <option v-for="category in jobCategories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.job_category_id" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <InputLabel for="country_id" value="Country" />
                            <select
                                id="country_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.country_id"
                                @change="loadStates"
                                required
                            >
                                <option :value="null" disabled>Select a country</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.country_id" />
                        </div>
                        <div>
                            <InputLabel for="state_id" value="State" />
                            <select
                                id="state_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.state_id"
                                @change="loadCities"
                                :disabled="!states.length"
                            >
                                <option :value="null">Select a state (optional)</option>
                                <option v-for="state in states" :key="state.id" :value="state.id">
                                    {{ state.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.state_id" />
                        </div>
                        <div>
                            <InputLabel for="city_id" value="City" />
                            <select
                                id="city_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.city_id"
                                :disabled="!cities.length"
                            >
                                <option :value="null">Select a city (optional)</option>
                                <option v-for="city in cities" :key="city.id" :value="city.id">
                                    {{ city.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.city_id" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="description" value="Job Description" />
                        <TextareaInput
                            id="description"
                            class="mt-1 block w-full"
                            v-model="form.description"
                            rows="5"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div>
                        <InputLabel for="responsibilities" value="Key Responsibilities" />
                        <TextareaInput
                            id="responsibilities"
                            class="mt-1 block w-full"
                            v-model="form.responsibilities"
                            rows="3"
                        />
                        <InputError class="mt-2" :message="form.errors.responsibilities" />
                    </div>

                    <div>
                        <InputLabel for="requirements" value="Requirements" />
                        <TextareaInput
                            id="requirements"
                            class="mt-1 block w-full"
                            v-model="form.requirements"
                            rows="3"
                        />
                        <InputError class="mt-2" :message="form.errors.requirements" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="employment_type" value="Employment Type" />
                            <select
                                id="employment_type"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.employment_type"
                                required
                            >
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.employment_type" />
                        </div>

                        <div>
                            <InputLabel for="experience_level" value="Experience Level" />
                            <select
                                id="experience_level"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.experience_level"
                                required
                            >
                                <option value="entry">Entry</option>
                                <option value="mid">Mid</option>
                                <option value="senior">Senior</option>
                                <option value="manager">Manager</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.experience_level" />
                        </div>

                        <div>
                            <InputLabel for="salary_range" value="Salary Range (e.g., $50k - $70k)" />
                            <TextInput
                                id="salary_range"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.salary_range"
                            />
                            <InputError class="mt-2" :message="form.errors.salary_range" />
                        </div>

                        <div>
                            <InputLabel for="education_level" value="Education Level" />
                            <TextInput
                                id="education_level"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.education_level"
                                placeholder="e.g., Bachelor's Degree"
                            />
                            <InputError class="mt-2" :message="form.errors.education_level" />
                        </div>

                        <div>
                            <InputLabel for="application_deadline" value="Application Deadline" />
                            <TextInput
                                id="application_deadline"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.application_deadline"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.application_deadline" />
                        </div>

                        <div>
                            <InputLabel for="status" value="Status" />
                            <select
                                id="status"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.status"
                                required
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="expired">Expired</option>
                                <option value="filled">Filled</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>


                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Create</PrimaryButton>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Created.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>