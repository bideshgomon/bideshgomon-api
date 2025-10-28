<script setup>
// This component is nearly identical to DegreeLevels/Index.vue
// Just replace 'DegreeLevel'/'degreeLevel' with 'FieldOfStudy'/'fieldOfStudy'
import { ref, watch, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    fieldsOfStudy: Object, // <-- Changed prop name
    filters: Object,
    fieldOfStudyToEdit: Object, // <-- Changed prop name
});

// --- State ---
const showCreateEditModal = ref(false);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);

// Form
const form = useForm({
    id: null,
    name: '',
    is_active: true,
});

const isEditMode = computed(() => form.id !== null);

// --- Search ---
const search = ref(props.filters.search || '');
watch(search, debounce((value) => {
    router.get(route('admin.fields-of-study.index'), { search: value }, { // <-- Changed route name
        preserveState: true,
        replace: true,
    });
}, 300));

// --- Modals ---
const openModal = (itemToEdit = null) => {
    if (itemToEdit) {
        router.get(route('admin.fields-of-study.index'), { edit_id: itemToEdit.id }, { // <-- Changed route name
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    } else {
        form.reset();
        showCreateEditModal.value = true;
    }
};

const openDeleteModal = (item) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showCreateEditModal.value = false;
    showDeleteModal.value = false;
    itemToDelete.value = null;

    if (props.fieldOfStudyToEdit) { // <-- Changed prop name
         router.get(route('admin.fields-of-study.index'), { search: search.value }, { // <-- Changed route name
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.fieldOfStudyToEdit, (newItem) => { // <-- Changed prop name
    if (newItem) {
        form.id = newItem.id;
        form.name = newItem.name;
        form.is_active = newItem.is_active;
        showCreateEditModal.value = true;
    }
});

// --- Actions ---
const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('admin.fields-of-study.update', form.id), { // <-- Changed route name
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.fields-of-study.store'), { // <-- Changed route name
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('admin.fields-of-study.destroy', itemToDelete.value.id), { // <-- Changed route name
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

</script>

<template>
    <Head title="Manage Fields of Study" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Fields of Study
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage academic fields or disciplines (e.g., Engineering, Arts).
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                         <TextInput
                            id="search"
                            type="text"
                            class="block w-full sm:w-56"
                            v-model="search"
                            placeholder="Search by name..."
                         />
                         <PrimaryButton @click="openModal()" class="w-full sm:w-auto justify-center">
                            Add Field of Study
                         </PrimaryButton>
                    </div>
                </header>

                <div class="flow-root">
                     <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="fieldsOfStudy.data.length === 0"> <td colspan="3" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No fields of study found.</td>
                                    </tr>
                                    <tr v-for="field in fieldsOfStudy.data" :key="field.id"> <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ field.name }}</td>
                                        <td class="px-3 py-4 text-sm">
                                             <span :class="field.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20">
                                                {{ field.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-3 whitespace-nowrap">
                                            <button @click="openModal(field)" class="text-brand-primary hover:text-opacity-80">Edit</button>
                                            <button @click="openDeleteModal(field)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="fieldsOfStudy.links" /> </section>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Field of Study' : 'Add New Field of Study' }}
                </h2>
                 <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                     Enter the name for the field of study.
                </p>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Name *" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="block">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Active</span>
                        </label>
                         <InputError class="mt-2" :message="form.errors.is_active" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">{{ form.processing ? 'Saving...' : (isEditMode ? 'Update' : 'Save') }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete Field of Study
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <strong>{{ itemToDelete?.name }}</strong>? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end space-x-4">
                     <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                     <DangerButton @click="deleteItem" :disabled="form.processing">Delete</DangerButton>
                </div>
            </div>
        </Modal>

    </AdminLayout>
</template>