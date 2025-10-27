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
    countries: Object, // Paginated data from CountryPageController
    filters: Object,   // Current filter values
});

// --- STATE & FORMS ---
const search = ref(props.filters.search || '');
const page = usePage();

// Modal visibility
const showCreateEditModal = ref(false);
const showUploadModal = ref(false);
const editingCountry = ref(null); // Holds the country being edited

// Form for Create/Edit
const form = useForm({
    name: '',
    iso2: '',
    iso3: '',
    phone_code: '',
    capital: '',
    currency: '',
    region: '',
});

// Form for CSV Upload
const csvForm = useForm({
    file: null, // File input
});

// --- WATCHERS ---
// Watch search bar and reload page
watch(search, debounce((value) => {
    router.get(route('admin.countries.index'), {
        search: value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// --- METHODS ---

// Open "Add New" modal
const openAddModal = () => {
    editingCountry.value = null;
    form.reset(); // Clear form
    showCreateEditModal.value = true;
};

// Open "Edit" modal
const openEditModal = (country) => {
    editingCountry.value = country;
    // Set form data from the selected country
    form.name = country.name;
    form.iso2 = country.iso2;
    form.iso3 = country.iso3;
    form.phone_code = country.phone_code;
    form.capital = country.capital;
    form.currency = country.currency;
    form.region = country.region;
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
    if (editingCountry.value) {
        // --- UPDATE ---
        form.put(route('api.admin.countries.update', editingCountry.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: (errors) => {
                // Errors are automatically handled by form.errors
            },
        });
    } else {
        // --- CREATE ---
        form.post(route('api.admin.countries.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: (errors) => {
                // Errors are automatically handled by form.errors
            },
        });
    }
};

// Handle Delete
const deleteCountry = (country) => {
    if (confirm(`Are you sure you want to delete "${country.name}"?`)) {
        router.delete(route('api.admin.countries.destroy', country.id), {
            preserveScroll: true,
            // onSuccess: () => { // Page will reload with updated list }
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
    csvForm.post(route('api.admin.countries.bulk'), {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            csvForm.reset();
        },
        onError: (errors) => {
            // Errors will be shown in the modal
        },
    });
};

</script>

<template>
    <Head title="Manage Countries" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Countries
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
                                placeholder="Search by name, ISO code, phone code..."
                            />
                            <div class="flex-shrink-0 flex gap-2 w-full md:w-auto">
                                <SecondaryButton @click="openUploadModal" class="w-1/2 md:w-auto">Upload CSV</SecondaryButton>
                                <PrimaryButton @click="openAddModal" class="w-1/2 md:w-auto">Add New Country</PrimaryButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ISO2</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ISO3</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Phone Code</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="countries.data.length === 0">
                                         <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No countries found.</td>
                                    </tr>
                                    <tr v-else v-for="country in countries.data" :key="country.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ country.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ country.iso2 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ country.iso3 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ country.phone_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal(country)" class="text-brand-primary hover:text-opacity-80 dark:text-blue-400 dark:hover:text-blue-300">
                                                Edit
                                            </button>
                                            <DangerButton @click="deleteCountry(country)" class="ml-3 text-xs px-2 py-1">
                                                Delete
                                            </DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination class="mt-6" :links="countries.links" />

                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
            <form @submit.prevent="submitForm" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingCountry ? 'Edit Country' : 'Add New Country' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in the details for the country.</p>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="name" value="Country Name *" />
                        <TextInput id="name" type="text" v-model="form.name" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="iso2" value="ISO2 Code (e.g., US)" />
                        <TextInput id="iso2" type="text" v-model="form.iso2" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.iso2" />
                    </div>
                    <div>
                        <InputLabel for="iso3" value="ISO3 Code (e.g., USA)" />
                        <TextInput id="iso3" type="text" v-model="form.iso3" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.iso3" />
                    </div>
                    <div>
                        <InputLabel for="phone_code" value="Phone Code (e.g., +1)" />
                        <TextInput id="phone_code" type="text" v-model="form.phone_code" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.phone_code" />
                    </div>
                    </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Country' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <Modal :show="showUploadModal" @close="showUploadModal = false">
             <form @submit.prevent="submitUpload" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Upload Countries CSV
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Upload a CSV file with columns: `name`, `iso2`, `iso3`, `phone_code`, `capital`, `currency`, `region`.
                </p>

                <div class="mt-6">
                    <InputLabel for="csv_file" value="CSV File *" />
                    <input
                        id="csv_file"
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