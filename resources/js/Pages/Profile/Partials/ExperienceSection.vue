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
import TextareaInput from '@/Components/TextareaInput.vue'; // Make sure you have this component
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

// --- State Management ---
const experienceList = ref([]); // Holds the user's work experience records
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentExperienceId = ref(null);

// --- Form Definition ---
const form = useForm({
    company_name: '',
    position: '',
    start_date: '',
    end_date: '',
    currently_working: false,
    description: '',
    country: '',
    city: '',
});

// --- API Interaction ---
const fetchExperience = async () => {
    isLoading.value = true;
    try {
        // Use the correct API route name based on your api.php
        const response = await axios.get(route('profile.work-experience.index'));
        experienceList.value = response.data.data; // Assuming API returns data wrapped in 'data'
    } catch (error) {
        console.error("Error fetching work experience:", error);
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentExperienceId.value = null;
    showModal.value = true;
};

const openEditModal = (experience) => {
    form.company_name = experience.company_name;
    form.position = experience.position;
    form.start_date = experience.start_date; // Assuming YYYY-MM-DD
    form.end_date = experience.end_date;
    form.currently_working = experience.currently_working;
    form.description = experience.description;
    form.country = experience.country;
    form.city = experience.city;

    isEditMode.value = true;
    currentExperienceId.value = experience.id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

// --- Form Submission ---
const submit = () => {
    // Clear end_date if currently working
    if (form.currently_working) {
        form.end_date = null;
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            fetchExperience(); // Re-fetch list
        },
        onError: () => {}
    };

    if (isEditMode.value) {
        // Use the correct API route name
        form.put(route('profile.work-experience.update', currentExperienceId.value), options);
    } else {
        // Use the correct API route name
        form.post(route('profile.work-experience.store'), options);
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (experience) => {
    if (window.confirm(`Are you sure you want to delete your experience at "${experience.company_name}"?`)) {
        // Use the correct API route name
        deleteForm.delete(route('profile.work-experience.destroy', experience.id), {
            preserveScroll: true,
            onSuccess: () => fetchExperience(), // Re-fetch list
            onError: () => {},
        });
    }
};

// --- Lifecycle Hook ---
onMounted(() => {
    fetchExperience();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Work Experience</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add or update your professional work history.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading work experience...</div>

        <div v-else-if="experienceList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No work experience added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="exp in experienceList" :key="exp.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ exp.position }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ exp.company_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ exp.start_date }} - {{ exp.currently_working ? 'Present' : exp.end_date }}
                        <span v-if="exp.city || exp.country"> | {{ [exp.city, exp.country].filter(Boolean).join(', ') }}</span>
                    </p>
                    <p v-if="exp.description" class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ exp.description }}</p>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(exp)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(exp)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal">Add Experience</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Work Experience' : 'Add New Work Experience' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="company_name" value="Company Name" />
                        <TextInput id="company_name" type="text" class="mt-1 block w-full" v-model="form.company_name" required />
                        <InputError class="mt-2" :message="form.errors.company_name" />
                    </div>

                    <div>
                        <InputLabel for="position" value="Position / Job Title" />
                        <TextInput id="position" type="text" class="mt-1 block w-full" v-model="form.position" required />
                        <InputError class="mt-2" :message="form.errors.position" />
                    </div>

                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>
                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" :disabled="form.currently_working" />
                            <InputError class="mt-2" :message="form.errors.end_date" />
                        </div>
                    </div>

                    <div class="flex items-center">
                         <Checkbox id="currently_working" v-model="form.currently_working" :checked="form.currently_working" />
                         <InputLabel for="currently_working" value="I am currently working here" class="ml-2" />
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

                    <div>
                        <InputLabel for="description" value="Description (Optional)" />
                        <TextareaInput id="description" class="mt-1 block w-full" v-model="form.description" rows="4" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ isEditMode ? 'Update Experience' : 'Save Experience' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>