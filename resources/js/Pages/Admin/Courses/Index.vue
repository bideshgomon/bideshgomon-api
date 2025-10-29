<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';
import Pagination from '@/Components/Pagination.vue';
import { PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';

// Set layout
defineOptions({
    layout: AdminLayout
});

// Props received from CoursePageController
const props = defineProps({
    courses: Object,
    universities: Array,
    degrees: Array,
    fieldsOfStudy: Array,
});

// Modal state
const showingModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'

// Form state
const form = useForm({
    id: null,
    name: '',
    description: '',
    university_id: null,
    degree_id: null,
    field_of_study_id: null,
    duration: '',
    tuition_fee: '',
    application_deadline: '',
    intake_semesters: '', // Using text input for JSON field for simplicity
    entry_requirements: '',
    course_website: '',
});

// Functions
const openCreateModal = () => {
    form.reset();
    modalMode.value = 'create';
    showingModal.value = true;
};

const openEditModal = (course) => {
    form.id = course.id;
    form.name = course.name;
    form.description = course.description;
    form.university_id = course.university_id;
    form.degree_id = course.degree_id;
    form.field_of_study_id = course.field_of_study_id;
    form.duration = course.duration;
    form.tuition_fee = course.tuition_fee;
    form.application_deadline = course.application_deadline;
    form.intake_semesters = Array.isArray(course.intake_semesters) ? course.intake_semesters.join(', ') : course.intake_semesters;
    form.entry_requirements = course.entry_requirements;
    form.course_website = course.course_website;
    
    modalMode.value = 'edit';
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        // Use API route to store
        form.post(route('api.admin.courses.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => console.log('Error creating course'),
        });
    } else {
        // Use API route to update
        form.put(route('api.admin.courses.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => console.log('Error updating course'),
        });
    }
};

const deleteCourse = (id) => {
    if (confirm('Are you sure you want to delete this course?')) {
        useForm({}).delete(route('api.admin.courses.destroy', id), {
            preserveScroll: true,
            onSuccess: () => console.log('Course deleted'),
        });
    }
};
</script>

<template>
    <Head title="Manage Courses" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Manage Courses</h1>
                <PrimaryButton @click="openCreateModal">
                    <PlusIcon class="h-5 w-5 mr-2" />
                    Create Course
                </PrimaryButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">University</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Degree</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="courses.data.length === 0">
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No courses found.
                                </td>
                            </tr>
                            <tr v-for="course in courses.data" :key="course.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ course.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ course.university?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ course.degree?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="openEditModal(course)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                        <PencilIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="deleteCourse(course.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :links="courses.links" class="mt-6" />
        </div>
    </div>

    <Modal :show="showingModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ modalMode === 'create' ? 'Create New Course' : 'Edit Course' }}
            </h2>

            <form @submit.prevent="submitForm" class="mt-6 space-y-4">
                <div>
                    <InputLabel for="name" value="Course Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     <div>
                        <InputLabel for="university_id" value="University" />
                        <select id="university_id" v-model="form.university_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option :value="null">Select a university</option>
                            <option v-for="uni in universities" :key="uni.id" :value="uni.id">{{ uni.name }}</option>
                        </select>
                        <InputError :message="form.errors.university_id" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="degree_id" value="Degree" />
                        <select id="degree_id" v-model="form.degree_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option :value="null">Select a degree</option>
                            <option v-for="degree in degrees" :key="degree.id" :value="degree.id">{{ degree.name }}</option>
                        </select>
                        <InputError :message="form.errors.degree_id" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="field_of_study_id" value="Field of Study" />
                        <select id="field_of_study_id" v-model="form.field_of_study_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option :value="null">Select a field</option>
                            <option v-for="field in fieldsOfStudy" :key="field.id" :value="field.id">{{ field.name }}</option>
                        </select>
                        <InputError :message="form.errors.field_of_study_id" class="mt-2" />
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <InputLabel for="duration" value="Duration (e.g., 2 Years)" />
                        <TextInput id="duration" v-model="form.duration" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.duration" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="tuition_fee" value="Tuition Fee (e.g., $10,000/year)" />
                        <TextInput id="tuition_fee" v-model="form.tuition_fee" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.tuition_fee" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="application_deadline" value="Application Deadline" />
                        <TextInput id="application_deadline" v-model="form.application_deadline" type="date" class="mt-1 block w-full" />
                        <InputError :message="form.errors.application_deadline" class="mt-2" />
                    </div>
                </div>
                
                <div>
                    <InputLabel for="intake_semesters" value="Intake Semesters (comma-separated)" />
                    <TextInput id="intake_semesters" v-model="form.intake_semesters" type="text" class="mt-1 block w-full" placeholder="e.g., Fall, Spring" />
                    <InputError :message="form.errors.intake_semesters" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="entry_requirements" value="Entry Requirements" />
                    <TextareaInput id="entry_requirements" v-model="form.entry_requirements" class="mt-1 block w-full" rows="3" />
                    <InputError :message="form.errors.entry_requirements" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <TextareaInput id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="course_website" value="Course Website URL" />
                    <TextInput id="course_website" v-model="form.course_website" type="url" class="mt-1 block w-full" placeholder="https://university.com/course" />
                    <InputError :message="form.errors.course_website" class="mt-2" />
                </div>


                <div class="flex justify-end pt-4 space-x-3">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : (modalMode === 'create' ? 'Create' : 'Save Changes') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>