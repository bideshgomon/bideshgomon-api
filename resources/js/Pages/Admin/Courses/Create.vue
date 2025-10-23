<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue'; // Assuming you have or create this
import { Head, Link, useForm } from '@inertiajs/vue3';

// Define props passed from the controller
const props = defineProps({
    universities: Array,
});

// Initialize the form state
const form = useForm({
    university_id: '',
    name: '',
    degree_level: '',
    field_of_study: '',
    tuition_fee: '',
    duration_years: '',
    description: '',
    application_deadline: '',
});

// Submit the form
const submit = () => {
    form.post(route('api.admin.courses.store'), {
        onSuccess: () => form.reset(), // Clear form on success
    });
};

// Example options for dropdowns (you might fetch these from DB later)
const degreeLevels = ['Bachelors', 'Masters', 'PhD', 'Diploma', 'Certificate'];
const fieldsOfStudy = ['Computer Science', 'Engineering', 'Business', 'Arts', 'Medicine', 'Law', 'Other'];

</script>

<template>
    <Head title="Add New Course" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Add New Course
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Enter the details for the new course.
                    </p>
                </header>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="university_id" value="University" />
                        <select
                            id="university_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="form.university_id"
                            required
                        >
                            <option value="" disabled>Select a university</option>
                            <option v-for="uni in universities" :key="uni.id" :value="uni.id">
                                {{ uni.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.university_id" />
                    </div>

                    <div>
                        <InputLabel for="name" value="Course Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="degree_level" value="Degree Level" />
                            <select
                                id="degree_level"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.degree_level"
                                required
                            >
                                <option value="" disabled>Select level</option>
                                <option v-for="level in degreeLevels" :key="level" :value="level">
                                    {{ level }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.degree_level" />
                        </div>

                        <div>
                            <InputLabel for="field_of_study" value="Field of Study" />
                             <select
                                id="field_of_study"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.field_of_study"
                                required
                            >
                                <option value="" disabled>Select field</option>
                                <option v-for="field in fieldsOfStudy" :key="field" :value="field">
                                    {{ field }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.field_of_study" />
                        </div>
                    </div>

                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="tuition_fee" value="Tuition Fee (Approx, Per Year, Optional)" />
                            <TextInput
                                id="tuition_fee"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full"
                                v-model="form.tuition_fee"
                            />
                            <InputError class="mt-2" :message="form.errors.tuition_fee" />
                        </div>

                        <div>
                            <InputLabel for="duration_years" value="Duration (Years, Optional)" />
                            <TextInput
                                id="duration_years"
                                type="number"
                                step="0.5"
                                class="mt-1 block w-full"
                                v-model="form.duration_years"
                            />
                            <InputError class="mt-2" :message="form.errors.duration_years" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="description" value="Description (Optional)" />
                        <TextareaInput
                            id="description"
                            class="mt-1 block w-full"
                            v-model="form.description"
                            rows="4"
                        />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                     <div>
                        <InputLabel for="application_deadline" value="Application Deadline (Optional)" />
                        <TextInput
                            id="application_deadline"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.application_deadline"
                        />
                        <InputError class="mt-2" :message="form.errors.application_deadline" />
                    </div>


                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Course' }}
                        </PrimaryButton>

                        <Link
                            :href="route('admin.courses.index')"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>