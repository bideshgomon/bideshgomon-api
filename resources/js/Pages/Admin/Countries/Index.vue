<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
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
    countries: Object, // Paginated data
    filters: Object,   // Search filters
    countryToEdit: Object, // Full country object for editing
    validation_errors: Array, // <-- Add prop for bulk upload errors
});

// --- State Management ---
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showBulkUploadModal = ref(false); // <-- Add state for upload modal
const countryToDelete = ref(null);

// Form state for Create/Edit
const form = useForm({
    id: null,
    name: '',
    iso_code: '',
    iso_code_3: '',
    country_code: '',
    capital: '',
    currency: '',
    continent: '',
    subregion: '',
    nationality: '',
    is_active: true,
});

// Form state for Bulk Upload
const uploadForm = useForm({
    csv_file: null,
});

// Computed property to determine if we are in "edit" mode
const isEditMode = computed(() => form.id !== null);

// --- Search Logic ---
const search = ref(props.filters.search || ''); // <-- Ensure default is empty string
watch(search, debounce((value) => {
    router.get(route('admin.countries.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// --- Modal Functions ---
const openCreateModal = () => {
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (countryId) => {
    router.get(route('admin.countries.index'), { edit_id: countryId }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const openDeleteModal = (country) => {
    countryToDelete.value = country;
    showDeleteModal.value = true;
};

const openBulkUploadModal = () => { // <-- Function to open upload modal
    uploadForm.reset();
    showBulkUploadModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    showBulkUploadModal.value = false; // <-- Close upload modal
    countryToDelete.value = null;

    // Clear props that trigger modals
    const queryParams = { search: search.value };
    if (props.countryToEdit || props.validation_errors) {
         router.get(route('admin.countries.index'), queryParams, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.countryToEdit, (newCountry) => {
    if (newCountry) {
        form.id = newCountry.id;
        form.name = newCountry.name;
        form.iso_code = newCountry.iso_code;
        form.iso_code_3 = newCountry.iso_code_3;
        form.country_code = newCountry.country_code || '';
        form.capital = newCountry.capital || '';
        form.currency = newCountry.currency || '';
        form.continent = newCountry.continent || '';
        form.subregion = newCountry.subregion || '';
        form.nationality = newCountry.nationality || '';
        form.is_active = newCountry.is_active;

        showEditModal.value = true;
    }
});

// --- Watcher for Bulk Upload Errors ---
watch(() => props.validation_errors, (errors) => {
    if (errors && errors.length > 0) {
        showBulkUploadModal.value = true;
    }
});


// --- Form Submission ---
const submitForm = () => {
    if (isEditMode.value) {
        // UPDATE
        form.put(route('admin.countries.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                form.reset();
            },
        });
    } else {
        // CREATE
        form.post(route('admin.countries.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                form.reset();
            },
        });
    }
};

// --- Bulk Upload Submission ---
const submitBulkUpload = () => {
    uploadForm.post(route('admin.countries.bulk-upload'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            uploadForm.reset();
        },
    });
};

// --- Delete Logic ---
const deleteCountry = () => {
    if (countryToDelete.value) {
        router.delete(route('admin.countries.destroy', countryToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

</script>

<template>
    <Head title="Manage Countries" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Countries
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage countries, states, and cities.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                         <TextInput
                            id="search"
                            type="text"
                            class="block w-full sm:w-56"
                            v-model="search"
                            placeholder="Search by name or code..."
                         />
                         <div class="flex items-center gap-3 w-full sm:w-auto">
                            <SecondaryButton @click="openBulkUploadModal" class="w-full sm:w-auto justify-center">
                                Bulk Upload
                            </SecondaryButton>
                            <PrimaryButton @click="openCreateModal" class="w-full sm:w-auto justify-center">
                                Add Country
                            </PrimaryButton>
                         </div>
                    </div>
                </header>

                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">ISO2</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">ISO3</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Continent</th>
                                         <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="countries.data.length === 0">
                                        <td colspan="6" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No countries found.</td>
                                    </tr>
                                    <tr v-for="country in countries.data" :key="country.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ country.name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ country.iso_code }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ country.iso_code_3 }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ country.continent || '—' }}</td>
                                        <td class="px-3 py-4 text-sm">
                                            <span :class="country.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20">
                                                {{ country.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-3 whitespace-nowrap">
                                            <button @click="openEditModal(country.id)" class="text-brand-primary hover:text-opacity-80">Edit</button>
                                            <button @click="openDeleteModal(country)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="countries.links" />
            </section>
        </div>

        <Modal :show="showCreateModal || showEditModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Country' : 'Add New Country' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                     {{ isEditMode ? 'Update the details for ' + form.name : 'Enter the details for the new country.' }}
                </p>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Country Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="iso_code" value="ISO Code (2 Letters)" />
                            <TextInput id="iso_code" type="text" class="mt-1 block w-full" v-model="form.iso_code" required maxlength="2" />
                            <InputError class="mt-2" :message="form.errors.iso_code" />
                        </div>
                        <div>
                            <InputLabel for="iso_code_3" value="ISO Code (3 Letters)" />
                            <TextInput id="iso_code_3" type="text" class="mt-1 block w-full" v-model="form.iso_code_3" required maxlength="3" />
                            <InputError class="mt-2" :message="form.errors.iso_code_3" />
                        </div>
                    </div>
                     <div>
                        <InputLabel for="country_code" value="Country/Phone Code (Optional)" />
                        <TextInput id="country_code" type="text" class="mt-1 block w-full" v-model="form.country_code" placeholder="+1"/>
                        <InputError class="mt-2" :message="form.errors.country_code" />
                    </div>
                    <div>
                        <InputLabel for="nationality" value="Nationality (Optional)" />
                        <TextInput id="nationality" type="text" class="mt-1 block w-full" v-model="form.nationality" placeholder="e.g., American"/>
                        <InputError class="mt-2" :message="form.errors.nationality" />
                    </div>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="continent" value="Continent (Optional)" />
                            <TextInput id="continent" type="text" class="mt-1 block w-full" v-model="form.continent" placeholder="e.g., North America" />
                            <InputError class="mt-2" :message="form.errors.continent" />
                        </div>
                         <div>
                            <InputLabel for="subregion" value="Subregion (Optional)" />
                            <TextInput id="subregion" type="text" class="mt-1 block w-full" v-model="form.subregion" placeholder="e.g., Northern America"/>
                            <InputError class="mt-2" :message="form.errors.subregion" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="capital" value="Capital City (Optional)" />
                            <TextInput id="capital" type="text" class="mt-1 block w-full" v-model="form.capital" />
                            <InputError class="mt-2" :message="form.errors.capital" />
                        </div>
                        <div>
                            <InputLabel for="currency" value="Currency (Optional)" />
                            <TextInput id="currency" type="text" class="mt-1 block w-full" v-model="form.currency" placeholder="e.g., USD"/>
                            <InputError class="mt-2" :message="form.errors.currency" />
                        </div>
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
                    Delete Country
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <strong>{{ countryToDelete?.name }}</strong>? This action cannot be undone and may affect related states and cities.
                </p>

                <div class="mt-6 flex justify-end space-x-4">
                     <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                     <DangerButton @click="deleteCountry" :disabled="form.processing">Delete</DangerButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showBulkUploadModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Bulk Upload Countries
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Upload a CSV file with the following headers:
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">name</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">iso_code</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">iso_code_3</code>.
                    <br>
                    Optional headers:
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">country_code</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">capital</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">currency</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">continent</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">subregion</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">nationality</code>,
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 p-1 rounded">is_active</code> (true/false).
                </p>

                <div v-if="validation_errors && validation_errors.length > 0" class="mt-4 max-h-40 overflow-y-auto p-3 bg-red-50 dark:bg-red-900 rounded-md">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please correct the following errors:</h3>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                        <li v-for="error in validation_errors" :key="error">{{ error }}</li>
                    </ul>
                </div>


                <form @submit.prevent="submitBulkUpload" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="csv_file" value="CSV File" />
                        <input
                            id="csv_file"
                            type="file"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            @input="uploadForm.csv_file = $event.target.files[0]"
                            accept=".csv, text/csv"
                        />
                         <InputError class="mt-2" :message="uploadForm.errors.csv_file" />

                        <div v-if="uploadForm.progress" class="mt-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: uploadForm.progress.percentage + '%' }"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="uploadForm.processing">{{ uploadForm.processing ? 'Uploading...' : 'Upload' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AdminLayout>
</template>