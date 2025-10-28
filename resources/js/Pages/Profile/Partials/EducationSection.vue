<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios'; // For making API requests

// Import necessary components
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

// --- State Management ---
const educationList = ref([]); // Holds the user's education records
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentEducationId = ref(null);

// --- Form Definition ---
const form = useForm({
    institute: '',
    degree: '',
    field_of_study: '',
    start_date: '',
    end_date: '',
    currently_studying: false,
    country: '',
    city: '',
});

// --- API Interaction ---
const fetchEducation = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('profile.education.index')); // API route
        educationList.value = response.data.data; // Assuming API returns data wrapped in 'data'
    } catch (error) {
        console.error("Error fetching education:", error);
        // Handle error display if needed
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentEducationId.value = null;
    showModal.value = true;
};

const openEditModal = (education) => {
    form.institute = education.institute;
    form.degree = education.degree;
    form.field_of_study = education.field_of_study;
    form.start_date = education.start_date; // Assuming YYYY-MM-DD format from API
    form.end_date = education.end_date;
    form.currently_studying = education.currently_studying;
    form.country = education.country;
    form.city = education.city;

    isEditMode.value = true;
    currentEducationId.value = education.id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

// --- Form Submission ---
const submit = () => {
    // Clear end_date if currently studying
    if (form.currently_studying) {
        form.end_date = null;
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            fetchEducation(); // Re-fetch list after successful save/update
        },
        onError: () => {
            // Handle specific errors if needed, e.g., scroll to error
        }
    };

    if (isEditMode.value) {
        form.put(route('profile.education.update', currentEducationId.value), options);
    } else {
        form.post(route('profile.education.store'), options);
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (education) => {
    if (window.confirm(`Are you sure you want to delete your education at "${education.institute}"?`)) {
        deleteForm.delete(route('profile.education.destroy', education.id), {
            preserveScroll: true,
            onSuccess: () => fetchEducation(), // Re-fetch list after delete
            onError: () => {
                // Handle delete error if needed
            },
        });
    }
};

// --- Lifecycle Hook ---
onMounted(() => {
    fetchEducation();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Education History</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add or update your educational qualifications.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading education history...</div>

        <div v-else-if="educationList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No education history added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="edu in educationList" :key="edu.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ edu.degree }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ edu.institute }} - {{ edu.field_of_study }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ edu.start_date }} - {{ edu.currently_studying ? 'Present' : edu.end_date }}
                        <span v-if="edu.city || edu.country"> | {{ [edu.city, edu.country].filter(Boolean).join(', ') }}</span>
                    </p>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(edu)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(edu)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal">Add Education</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Education Record' : 'Add New Education Record' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="institute" value="Institute / University" />
                        <TextInput id="institute" type="text" class="mt-1 block w-full" v-model="form.institute" required />
                        <InputError class="mt-2" :message="form.errors.institute" />
                    </div>

                    <div>
                        <InputLabel for="degree" value="Degree / Qualification" />
                        <TextInput id="degree" type="text" class="mt-1 block w-full" v-model="form.degree" required />
                        <InputError class="mt-2" :message="form.errors.degree" />
                    </div>

                    <div>
                        <InputLabel for="field_of_study" value="Field of Study" />
                        <TextInput id="field_of_study" type="text" class="mt-1 block w-full" v-model="form.field_of_study" />
                        <InputError class="mt-2" :message="form.errors.field_of_study" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>
                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" :disabled="form.currently_studying" />
                            <InputError class="mt-2" :message="form.errors.end_date" />
                        </div>
                    </div>

                    <div class="flex items-center">
                         <Checkbox id="currently_studying" v-model="form.currently_studying" :checked="form.currently_studying" />
                         <InputLabel for="currently_studying" value="I am currently studying here" class="ml-2" />
                    </div>

                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="city" value="City" />
                            <TextInput id="city" type="text" class="mt-1 block w-full" v-model="form.city" />
                            <InputError class="mt-2" :message="form.errors.city" />
                        </div>
                        <div>
                            <InputLabel for="country" value="Country" />
                            <TextInput id="country" type="text" class="mt-1 block w-full" v-model="form.country" />
                            <InputError class="mt-2" :message="form.errors.country" />
                        </div>
                    </div>


                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ isEditMode ? 'Update Record' : 'Save Record' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>