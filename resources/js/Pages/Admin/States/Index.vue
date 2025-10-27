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
    states: Object,     // Paginated data from StatePageController
    countries: Array,   // List of countries for dropdowns
    filters: Object,    // Current filter values (search, country_id)
});

// --- STATE & FORMS ---
const search = ref(props.filters.search || '');
const countryFilter = ref(props.filters.country_id || ''); // Filter by country ID
const page = usePage();

// Modal visibility
const showCreateEditModal = ref(false);
const showUploadModal = ref(false);
const editingState = ref(null); // Holds the state being edited

// Form for Create/Edit
const form = useForm({
    name: '',
    country_id: '', // Add country_id field
});

// Form for CSV Upload
const csvForm = useForm({
    file: null,
    country_id_context: '', // Optional: Upload states for a specific country
});

// --- WATCHERS ---
// Watch filters and reload page
watch([search, countryFilter], debounce((newValues, oldValues) => {
    // Check if filters actually changed to avoid unnecessary reloads
    if (newValues[0] !== oldValues[0] || newValues[1] !== oldValues[1]) {
        router.get(route('admin.states.index'), {
            search: search.value,
            country_id: countryFilter.value,
        }, {
            preserveState: true,
            replace: true,
        });
    }
}, 300)); // 300ms debounce delay

// --- METHODS ---

// Open "Add New" modal
const openAddModal = () => {
    editingState.value = null;
    form.reset();
    // Pre-select country if filtered
    form.country_id = countryFilter.value || '';
    showCreateEditModal.value = true;
};

// Open "Edit" modal
const openEditModal = (state) => {
    editingState.value = state;
    form.name = state.name;
    form.country_id = state.country_id;
    form.errors = {};
    showCreateEditModal.value = true;
};

// Close Create/Edit modal
const closeModal = () => {
    showCreateEditModal.value = false;
    form.reset();
};

// Handle Create/Edit form submission
const submitForm = () => {
    if (editingState.value) {
        // --- UPDATE ---
        form.put(route('api.admin.states.update', editingState.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // --- CREATE ---
        form.post(route('api.admin.states.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Handle Delete
const deleteState = (state) => {
    if (confirm(`Are you sure you want to delete "${state.name}"?`)) {
        router.delete(route('api.admin.states.destroy', state.id), {
            preserveScroll: true,
        });
    }
};

// Open "Upload CSV" modal
const openUploadModal = () => {
    csvForm.reset();
    // Pre-select country context if filtered
    csvForm.country_id_context = countryFilter.value || '';
    showUploadModal.value = true;
};

// Handle CSV file submission
const submitUpload = () => {
    csvForm.post(route('api.admin.states.bulk'), {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            csvForm.reset();
        },
    });
};

</script>

<template>
    <Head title="Manage States" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage States / Provinces
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div class="md:col-span-1">
                                <InputLabel for="search" value="Search State Name" />
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="search"
                                    placeholder="Search state name..."
                                />
                            </div>
                            <div class="md:col-span-1">
                                <InputLabel for="country_filter" value="Filter by Country" />
                                <select
                                    id="country_filter"
                                    v-model="countryFilter"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="">All Countries</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="md:col-span-1 flex justify-end gap-2">
                                <SecondaryButton @click="openUploadModal" class="flex-1 md:flex-none">Upload CSV</SecondaryButton>
                                <PrimaryButton @click="openAddModal" class="flex-1 md:flex-none">Add New State</PrimaryButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Country</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="states.data.length === 0">
                                         <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No states found.</td>
                                    </tr>
                                    <tr v-else v-for="state in states.data" :key="state.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ state.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ state.country?.name || 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal(state)" class="text-brand-primary hover:text-opacity-80 dark:text-blue-400 dark:hover:text-blue-300">
                                                Edit
                                            </button>
                                            <DangerButton @click="deleteState(state)" class="ml-3 text-xs px-2 py-1">
                                                Delete
                                            </DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination class="mt-6" :links="states.links" />

                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
            <form @submit.prevent="submitForm" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingState ? 'Edit State/Province' : 'Add New State/Province' }}
                </h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="country_id" value="Country *" />
                         <select
                            id="country_id"
                            v-model="form.country_id"
                            required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="" disabled>Select a country</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.country_id" />
                    </div>
                    <div>
                        <InputLabel for="name" value="State/Province Name *" />
                        <TextInput id="name" type="text" v-model="form.name" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save State' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <Modal :show="showUploadModal" @close="showUploadModal = false">
             <form @submit.prevent="submitUpload" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Upload States CSV
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    CSV must have columns: `name`, `country_iso2` (Country's 2-letter code).
                </p>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="country_id_context" value="Upload for Specific Country (Optional)" />
                         <select
                            id="country_id_context"
                            v-model="csvForm.country_id_context"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="">Upload for All Countries (Match by ISO2 in CSV)</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                         <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                             If selected, the CSV only needs the `name` column. Otherwise, it needs `name` and `country_iso2`.
                         </p>
                         <InputError class="mt-2" :message="csvForm.errors.country_id_context" />
                    </div>

                    <div>
                        <InputLabel for="csv_file_state" value="CSV File *" />
                        <input
                            id="csv_file_state"
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