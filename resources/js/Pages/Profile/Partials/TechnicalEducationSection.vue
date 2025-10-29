<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { ChevronUpIcon, ChevronDownIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';

// Collapsible state
const isOpen = ref(true);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// State
const educationList = ref([]);
const editingEducationId = ref(null);

const form = useForm({
    course_name: '',
    institution: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

// Fetch data
const getEducation = () => {
    axios.get(route('api.profile.technical-education.index'))
        .then(response => {
            educationList.value = response.data;
        })
        .catch(error => console.error("Error fetching technical education:", error));
};

// Submit form
const submitEducation = () => {
    if (form.is_current) {
        form.end_date = ''; // Clear end date if currently studying
    }

    const url = editingEducationId.value
        ? route('api.profile.technical-education.update', editingEducationId.value)
        : route('api.profile.technical-education.store');
    
    const method = editingEducationId.value ? 'put' : 'post';

    axios[method](url, form.data())
        .then(() => {
            form.reset();
            form.clearErrors();
            editingEducationId.value = null;
            getEducation(); // Refresh list
            form.recentlySuccessful = true;
            setTimeout(() => form.recentlySuccessful = false, 2000);
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                form.setError(error.response.data.errors);
            } else {
                console.error("Error submitting form:", error);
            }
        });
};

// Start editing
const editEducation = (edu) => {
    editingEducationId.value = edu.id;
    form.course_name = edu.course_name;
    form.institution = edu.institution;
    form.start_date = edu.start_date ? edu.start_date.substring(0, 10) : '';
    form.end_date = edu.end_date ? edu.end_date.substring(0, 10) : '';
    form.is_current = edu.is_current;
    form.clearErrors();
};

// Cancel editing
const cancelEdit = () => {
    editingEducationId.value = null;
    form.reset();
    form.clearErrors();
};

// Delete item
const deleteEducation = (educationId) => {
    if (!confirm("Are you sure you want to delete this education record?")) return;

    axios.delete(route('api.profile.technical-education.destroy', educationId))
        .then(() => {
            getEducation(); // Refresh list
            if (editingEducationId.value === educationId) {
                cancelEdit();
            }
        })
        .catch(error => {
            console.error("Error deleting technical education:", error);
            alert("Failed to delete record.");
        });
};

// Load data on mount
onMounted(() => {
    getEducation();
});

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return 'Present';
    try {
        const date = new Date(dateString + 'T00:00:00Z'); // Assume UTC
        return date.toLocaleDateString(undefined, { timeZone: 'UTC', year: 'numeric', month: 'short' });
    } catch (e) {
        return 'Invalid Date';
    }
};

</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Technical Education
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add vocational training, bootcamps, or other technical courses.
                    </p>
                </div>
                <div>
                    <ChevronUpIcon v-if="isOpen" class="w-6 h-6 text-gray-500" />
                    <ChevronDownIcon v-else class="w-6 h-6 text-gray-500" />
                </div>
            </header>

            <div v-show="isOpen" class="mt-6 space-y-6">
                
                <form @submit.prevent="submitEducation" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        {{ editingEducationId ? 'Update Education' : 'Add New Education' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="te_course_name" value="Course/Program Name" />
                            <TextInput
                                id="te_course_name"
                                v-model="form.course_name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Full Stack Web Development"
                                required
                            />
                            <InputError :message="form.errors.course_name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="te_institution" value="Institution" />
                            <TextInput
                                id="te_institution"
                                v-model="form.institution"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Programming Hero, BITM"
                                required
                            />
                            <InputError :message="form.errors.institution" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <InputLabel for="te_start_date" value="Start Date" />
                            <TextInput
                                id="te_start_date"
                                v-model="form.start_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.start_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="te_end_date" value="End Date" />
                            <TextInput
                                id="te_end_date"
                                v-model="form.end_date"
                                type="date"
                                class="mt-1 block w-full"
                                :disabled="form.is_current"
                                :class="{ 'bg-gray-100 dark:bg-gray-800': form.is_current }"
                            />
                            <InputError :message="form.errors.end_date" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.is_current" name="te_is_current" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">I am currently attending this program</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            <PlusIcon v-if="!editingEducationId" class="w-4 h-4 mr-2" />
                            {{ editingEducationId ? 'Update Education' : 'Save Education' }}
                        </PrimaryButton>
                        <SecondaryButton v-if="editingEducationId" type="button" @click="cancelEdit">
                            Cancel
                        </SecondaryButton>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                        </Transition>
                    </div>
                </form>

                <div class="mt-6 space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        Your Technical Education
                    </h3>
                    <div v-if="educationList.length === 0" class="text-center text-gray-500 dark:text-gray-400 p-4 border-dashed border-2 border-gray-300 dark:border-gray-700 rounded-lg">
                        No technical education added yet.
                    </div>
                    
                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="edu in educationList" :key="edu.id" class="py-4 flex justify-between items-center">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ edu.course_name }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ edu.institution }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    {{ formatDate(edu.start_date) }} - {{ formatDate(edu.end_date) }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <SecondaryButton @click="editEducation(edu)" class="!px-3 !py-2">
                                    <PencilIcon class="w-4 h-4" />
                                </SecondaryButton>
                                <DangerButton @click="deleteEducation(edu.id)" class="!px-3 !py-2">
                                    <TrashIcon class="w-4 h-4" />
                                </DangerButton>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</template>