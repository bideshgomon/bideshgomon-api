<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Textarea from '@/Components/Textarea.vue'; // We created this earlier
import SelectInput from '@/Components/SelectInput.vue'; // Need to create this

defineProps({
    categories: Array, // Passed from JobController@create
});

const form = useForm({
    job_category_id: '',
    title: '',
    description: '',
    company_name: '',
    location: '',
    salary_range: '',
    employment_type: 'Full-time', // Default value
    experience_level: '',
    requirements: '', // Will be handled as JSON/Array later if needed
    responsibilities: '', // Will be handled as JSON/Array later if needed
    apply_url: '',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.jobs.store'), {
        onSuccess: () => form.reset(),
        // Consider handling requirements/responsibilities array conversion here if needed
    });
};

const employmentTypes = ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship'];
const experienceLevels = ['Entry-level', 'Mid-level', 'Senior-level', 'Manager', 'Executive'];

</script>

<template>
    <Head title="Create Job Posting" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Job Posting
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                    <InputLabel for="job_category_id" value="Job Category" />
                                    <SelectInput
                                        id="job_category_id"
                                        class="mt-1 block w-full"
                                        v-model="form.job_category_id"
                                        required
                                    >
                                        <option value="" disabled>Select a category</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.job_category_id" />
                                </div>

                                <div>
                                    <InputLabel for="company_name" value="Company Name" />
                                    <TextInput
                                        id="company_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.company_name"
                                    />
                                    <InputError class="mt-2" :message="form.errors.company_name" />
                                </div>

                                <div>
                                    <InputLabel for="location" value="Location" />
                                    <TextInput
                                        id="location"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.location"
                                        placeholder="e.g., Dhaka, Bangladesh or Remote"
                                    />
                                    <InputError class="mt-2" :message="form.errors.location" />
                                </div>

                                <div>
                                    <InputLabel for="salary_range" value="Salary Range (Optional)" />
                                    <TextInput
                                        id="salary_range"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.salary_range"
                                        placeholder="e.g., $50k - $70k per year"
                                    />
                                    <InputError class="mt-2" :message="form.errors.salary_range" />
                                </div>
                                
                                <div>
                                    <InputLabel for="apply_url" value="Apply URL / Email (Optional)" />
                                    <TextInput
                                        id="apply_url"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.apply_url"
                                        placeholder="Link or email address for applications"
                                    />
                                    <InputError class="mt-2" :message="form.errors.apply_url" />
                                </div>


                                <div>
                                    <InputLabel for="employment_type" value="Employment Type" />
                                    <SelectInput
                                        id="employment_type"
                                        class="mt-1 block w-full"
                                        v-model="form.employment_type"
                                        required
                                    >
                                        <option v-for="type in employmentTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.employment_type" />
                                </div>

                                <div>
                                    <InputLabel for="experience_level" value="Experience Level (Optional)" />
                                     <SelectInput
                                        id="experience_level"
                                        class="mt-1 block w-full"
                                        v-model="form.experience_level"
                                    >
                                        <option value="" >Select level (optional)</option>
                                        <option v-for="level in experienceLevels" :key="level" :value="level">
                                            {{ level }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.experience_level" />
                                </div>

                                <div class="md:col-span-2">
                                    <InputLabel for="description" value="Job Description" />
                                    <Textarea
                                        id="description"
                                        class="mt-1 block w-full"
                                        v-model="form.description"
                                        rows="6"
                                    />
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <div class="md:col-span-2">
                                    <InputLabel for="requirements" value="Requirements (Optional)" />
                                    <Textarea
                                        id="requirements"
                                        class="mt-1 block w-full"
                                        v-model="form.requirements"
                                        rows="4"
                                        placeholder="List key requirements, one per line (optional)"
                                    />
                                    <InputError class="mt-2" :message="form.errors.requirements" />
                                </div>

                                <div class="md:col-span-2">
                                    <InputLabel for="responsibilities" value="Responsibilities (Optional)" />
                                    <Textarea
                                        id="responsibilities"
                                        class="mt-1 block w-full"
                                        v-model="form.responsibilities"
                                        rows="4"
                                        placeholder="List key responsibilities, one per line (optional)"
                                    />
                                    <InputError class="mt-2" :message="form.errors.responsibilities" />
                                </div>

                                <div class="md:col-span-2 block">
                                    <label class="flex items-center">
                                        <Checkbox name="is_active" v-model:checked="form.is_active" />
                                        <span class="ms-2 text-sm text-gray-600">Active (Visible on public site)</span>
                                    </label>
                                    <InputError class="mt-2" :message="form.errors.is_active" />
                                </div>

                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('admin.jobs.index')" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Create Job
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>