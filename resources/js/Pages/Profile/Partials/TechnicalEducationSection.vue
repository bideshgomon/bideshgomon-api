<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

// Import components
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

// --- State Management ---
const techEduList = ref([]);
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentTechEduId = ref(null);

// --- Form Definition ---
const form = useForm({
    institution_name: '',
    course_name: '',
    field_of_study: '', // Optional, but good to have
    start_date: '',
    completion_date: '', // Nullable
    is_ongoing: false,
    description: '', // Optional
});

// --- API Interaction ---
const fetchTechEdu = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('profile.technical-education.index')); // API route
        techEduList.value = response.data.data || [];
    } catch (error) {
        console.error("Error fetching technical education:", error);
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentTechEduId.value = null;
    showModal.value = true;
};

const openEditModal = (techEdu) => {
    form.institution_name = techEdu.institution_name;
    form.course_name = techEdu.course_name;
    form.field_of_study = techEdu.field_of_study;
    form.start_date = techEdu.start_date; // Assuming YYYY-MM-DD
    form.completion_date = techEdu.completion_date;
    form.is_ongoing = techEdu.is_ongoing;
    form.description = techEdu.description;

    isEditMode.value = true;
    currentTechEduId.value = techEdu.id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

// --- Form Submission ---
const submit = () => {
    // Clear completion_date if ongoing
    if (form.is_ongoing) {
        form.completion_date = null;
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            fetchTechEdu(); // Re-fetch list
        },
        onError: () => {}
    };

    if (isEditMode.value) {
        form.put(route('profile.technical-education.update', currentTechEduId.value), options);
    } else {
        form.post(route('profile.technical-education.store'), options);
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (techEdu) => {
    if (window.confirm(`Are you sure you want to delete the record for "${techEdu.course_name}" at "${techEdu.institution_name}"?`)) {
        deleteForm.delete(route('profile.technical-education.destroy', techEdu.id), {
            preserveScroll: true,
            onSuccess: () => fetchTechEdu(), // Re-fetch list
            onError: () => {},
        });
    }
};

// --- Lifecycle Hook ---
onMounted(() => {
    fetchTechEdu();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Technical Education & Training</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                List any vocational training, certifications, bootcamps, or other technical courses.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading technical education...</div>

        <div v-else-if="techEduList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No technical education or training added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="te in techEduList" :key="te.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ te.course_name }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ te.institution_name }} <span v-if="te.field_of_study"> - {{ te.field_of_study }}</span></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ te.start_date }} - {{ te.is_ongoing ? 'Ongoing' : (te.completion_date || 'N/A') }}
                    </p>
                     <p v-if="te.description" class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ te.description }}</p>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(te)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(te)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal" :disabled="isLoading">Add Technical Education</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Technical Education Record' : 'Add New Technical Education Record' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="institution_name" value="Institution / Provider Name" />
                        <TextInput id="institution_name" type="text" class="mt-1 block w-full" v-model="form.institution_name" required />
                        <InputError class="mt-2" :message="form.errors.institution_name" />
                    </div>

                    <div>
                        <InputLabel for="course_name" value="Course / Program Name" />
                        <TextInput id="course_name" type="text" class="mt-1 block w-full" v-model="form.course_name" required />
                        <InputError class="mt-2" :message="form.errors.course_name" />
                    </div>

                     <div>
                        <InputLabel for="tech_field_of_study" value="Field of Study (Optional)" />
                        <TextInput id="tech_field_of_study" type="text" class="mt-1 block w-full" v-model="form.field_of_study" />
                        <InputError class="mt-2" :message="form.errors.field_of_study" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="tech_start_date" value="Start Date" />
                            <TextInput id="tech_start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>
                        <div>
                            <InputLabel for="completion_date" value="Completion Date" />
                            <TextInput id="completion_date" type="date" class="mt-1 block w-full" v-model="form.completion_date" :disabled="form.is_ongoing"/>
                            <InputError class="mt-2" :message="form.errors.completion_date" />
                        </div>
                    </div>

                     <div class="flex items-center">
                         <Checkbox id="is_ongoing" v-model="form.is_ongoing" :checked="form.is_ongoing" />
                         <InputLabel for="is_ongoing" value="This program is currently ongoing" class="ml-2" />
                    </div>

                    <div>
                        <InputLabel for="tech_description" value="Description (Optional)" />
                        <TextareaInput id="tech_description" class="mt-1 block w-full" v-model="form.description" rows="3" />
                        <InputError class="mt-2" :message="form.errors.description" />
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