<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// --- PROPS ---
const props = defineProps({
    skills: Object, // Paginated data from SkillPageController
    filters: Object,   // Current filter values (search)
});

// --- STATE & FORMS ---
const search = ref(props.filters.search || '');
const page = usePage();

// Modal visibility
const showCreateEditModal = ref(false);
const showUploadModal = ref(false);
const editingSkill = ref(null); // Holds the skill being edited

// Form for Create/Edit
const form = useForm({
    name: '',
    category: '', // Add category field
});

// Form for CSV Upload
const csvForm = useForm({
    file: null, // File input
});

// --- WATCHERS ---
// Watch search bar and reload page
watch(search, debounce((value) => {
    router.get(route('admin.skills.index'), {
        search: value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// --- METHODS ---

// Open "Add New" modal
const openAddModal = () => {
    editingSkill.value = null;
    form.reset(); // Clear form
    showCreateEditModal.value = true;
};

// Open "Edit" modal
const openEditModal = (skill) => {
    editingSkill.value = skill;
    // Set form data from the selected skill
    form.name = skill.name;
    form.category = skill.category;
    form.errors = {}; // Clear previous errors
    showCreateEditModal.value = true;
};

// Close Create/Edit modal
const closeModal = () => {
    showCreateEditModal.value = false;
    form.reset();
};

// Handle Create/Edit form submission
const submitForm = () => {
    if (editingSkill.value) {
        // --- UPDATE ---
        form.put(route('api.admin.skills.update', editingSkill.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // --- CREATE ---
        form.post(route('api.admin.skills.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Handle Delete
const deleteSkill = (skill) => {
    if (confirm(`Are you sure you want to delete "${skill.name}"?`)) {
        router.delete(route('api.admin.skills.destroy', skill.id), {
            preserveScroll: true,
        });
    }
};

// Open "Upload CSV" modal
const openUploadModal = () => {
    csvForm.reset();
    showUploadModal.value = true;
};

// Handle CSV file submission
const submitUpload = () => {
    csvForm.post(route('api.admin.skills.bulk'), { // Assumes 'api.admin.skills.bulk' route
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            csvForm.reset();
        },
    });
};

</script>

<template>
    <Head title="Manage Skills" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Skills
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                            <TextInput
                                type="text"
                                class="block w-full md:w-96"
                                v-model="search"
                                placeholder="Search by name or category..."
                            />
                            <div class="flex-shrink-0 flex gap-2 w-full md:w-auto">
                                <SecondaryButton @click="openUploadModal" class="w-1/2 md:w-auto">Upload CSV</SecondaryButton>
                                <PrimaryButton @click="openAddModal" class="w-1/2 md:w-auto">Add New Skill</PrimaryButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Skill Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="skills.data.length === 0">
                                         <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No skills found.</td>
                                    </tr>
                                    <tr v-else v-for="skill in skills.data" :key="skill.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ skill.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ skill.category || 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal(skill)" class="text-brand-primary hover:text-opacity-80 dark:text-blue-400 dark:hover:text-blue-300">
                                                Edit
                                            </button>
                                            <DangerButton @click="deleteSkill(skill)" class="ml-3 text-xs px-2 py-1">
                                                Delete
                                            </DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination class="mt-6" :links="skills.links" />

                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
            <form @submit.prevent="submitForm" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingSkill ? 'Edit Skill' : 'Add New Skill' }}
                </h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="name" value="Skill Name *" />
                        <TextInput id="name" type="text" v-model="form.name" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                     <div>
                        <InputLabel for="category" value="Category (Optional)" />
                        <TextInput id="category" type="text" v-model="form.category" class="mt-1 block w-full" placeholder="e.g., Technical, Soft Skill, Language"/>
                        <InputError class="mt-2" :message="form.errors.category" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Skill' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <Modal :show="showUploadModal" @close="showUploadModal = false">
             <form @submit.prevent="submitUpload" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Upload Skills CSV
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    CSV must have columns: `name`, `category` (optional).
                </p>

                <div class="mt-6">
                    <InputLabel for="csv_file_skill" value="CSV File *" />
                    <input
                        id="csv_file_skill"
                        type="file"
                        @input="csvForm.file = $event.target.files[0]"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-primary/10 file:text-brand-primary hover:file:bg-brand-primary/20"
                        accept=".csv"
                        required
                    />
                    <progress v-if="csvForm.progress" :value="csvForm.progress.percentage" max="100" class="w-full mt-2">
                        {{ csvForm.progress.percentage }}%
                    </progress>
                    <InputError class="mt-2" :message="csvForm.errors.file" />
                </div>

                 <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showUploadModal = false"> Cancel </SecondaryButton>
                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': csvForm.processing }"
                        :disabled="csvForm.processing"
                    >
                        {{ csvForm.processing ? 'Uploading...' : 'Upload File' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

    </AuthenticatedLayout>
</template>