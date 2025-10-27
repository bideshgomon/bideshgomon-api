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
import { ref, watch, computed } from 'vue'; // Added computed
import debounce from 'lodash/debounce';

// --- PROPS ---
const props = defineProps({
    cities: Object,     // Paginated data from CityPageController
    countries: Array,   // List of countries for dropdowns
    states: Array,      // List of states (potentially pre-filtered by country)
    filters: Object,    // Current filter values (search, country_id, state_id)
});

// --- STATE & FORMS ---
const search = ref(props.filters.search || '');
const countryFilter = ref(props.filters.country_id || '');
const stateFilter = ref(props.filters.state_id || '');
const page = usePage();

// Modal visibility
const showCreateEditModal = ref(false);
const showUploadModal = ref(false);
const editingCity = ref(null); // Holds the city being edited

// Form for Create/Edit
const form = useForm({
    name: '',
    state_id: '',
});

// Form for CSV Upload
const csvForm = useForm({
    file: null,
    state_id_context: '', // Optional: Upload cities for a specific state
    country_id_context: '', // Optional: Upload for a country (if state not selected)
});

// --- COMPUTED ---
// Filter states based on the selected country in the *filters*
const filteredStatesForFilter = computed(() => {
    if (!countryFilter.value) {
        return props.states; // Show all initially passed states if no country filter
    }
    // Filter the initially passed states list
    return props.states.filter(state => state.country_id == countryFilter.value);
});

// Filter states based on the selected country in the *modal form*
const filteredStatesForForm = computed(() => {
    // We need all countries for the modal's country select
    // Fetch states dynamically or have them all available
    // For simplicity, let's assume we need to filter `props.countries` first to find the country,
    // then filter `props.states` based on that country_id FOR THE MODAL.
    // This requires having ALL states available, not just pre-filtered ones.
    // --> We need to adjust the PageController later if this is desired.
    // --> For now, let's just use all available states in the modal dropdwon
    // return props.states;

    // --- TEMPORARY SIMPLIFICATION: We need *all* states passed to the component ---
    // The current PageController only passes states filtered by the *request's* country_id.
    // We'll proceed assuming props.states contains *all* states needed for the modal.
    // A better approach involves fetching states dynamically based on modal country selection.
    if (!form.country_id_intermediate) { // Intermediate ref needed if we add country dropdown to modal
        return props.states;
    }
     return props.states.filter(state => state.country_id == form.country_id_intermediate);
});

// --- WATCHERS ---
// Watch filters and reload page
watch([search, countryFilter, stateFilter], debounce((newValues, oldValues) => {
    // Only reload if a filter value actually changed
    if (newValues.some((val, i) => val !== oldValues[i])) {
        router.get(route('admin.cities.index'), {
            search: search.value,
            country_id: countryFilter.value,
            state_id: stateFilter.value,
        }, {
            preserveState: true,
            replace: true,
            // Reset state filter if country filter changes
            onSuccess: (page) => {
                 if (newValues[1] !== oldValues[1]) { // If country_id changed
                    stateFilter.value = ''; // Reset state filter visually
                 }
            }
        });
    }
}, 300));

// --- METHODS ---

// Open "Add New" modal
const openAddModal = () => {
    editingCity.value = null;
    form.reset();
    // Pre-select state/country if filtered
    form.state_id = stateFilter.value || '';
    // If only country is filtered, maybe find the first state of that country?
    showCreateEditModal.value = true;
};

// Open "Edit" modal
const openEditModal = (city) => {
    editingCity.value = city;
    form.name = city.name;
    form.state_id = city.state_id;
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
    if (editingCity.value) {
        // --- UPDATE ---
        form.put(route('api.admin.cities.update', editingCity.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // --- CREATE ---
        form.post(route('api.admin.cities.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Handle Delete
const deleteCity = (city) => {
    if (confirm(`Are you sure you want to delete "${city.name}"?`)) {
        router.delete(route('api.admin.cities.destroy', city.id), {
            preserveScroll: true,
        });
    }
};

// Open "Upload CSV" modal
const openUploadModal = () => {
    csvForm.reset();
    // Pre-select context if filtered
    csvForm.state_id_context = stateFilter.value || '';
    csvForm.country_id_context = countryFilter.value || '';
    showUploadModal.value = true;
};

// Handle CSV file submission
const submitUpload = () => {
    csvForm.post(route('api.admin.cities.bulk'), {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            csvForm.reset();
        },
    });
};

</script>

<template>
    <Head title="Manage Cities" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Cities
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-1">
                                <InputLabel for="search" value="Search City Name" />
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="search"
                                    placeholder="Search city name..."
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
                            <div class="md:col-span-1">
                                <InputLabel for="state_filter" value="Filter by State" />
                                <select
                                    id="state_filter"
                                    v-model="stateFilter"
                                    :disabled="!countryFilter && props.states.length > 0 && !filteredStatesForFilter.length" {{-- Disable if no country selected unless states came prefiltered --}}
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm disabled:opacity-50"
                                >
                                    <option value="">All States {{ countryFilter ? 'in selected country' : '' }}</option>
                                    <option v-for="state in filteredStatesForFilter" :key="state.id" :value="state.id">
                                        {{ state.name }}
                                    </option>
                                </select>
                                <p v-if="countryFilter && filteredStatesForFilter.length === 0 && props.states.length > 0" class="mt-1 text-xs text-gray-500">No states found for selected country.</p>
                            </div>
                            <div class="md:col-span-1 flex justify-end gap-2">
                                <SecondaryButton @click="openUploadModal" class="flex-1 md:flex-none">Upload CSV</SecondaryButton>
                                <PrimaryButton @click="openAddModal" class="flex-1 md:flex-none">Add New City</PrimaryButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State / Province</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Country</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="cities.data.length === 0">
                                         <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No cities found.</td>
                                    </tr>
                                    <tr v-else v-for="city in cities.data" :key="city.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ city.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ city.state?.name || 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ city.state?.country?.name || 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal(city)" class="text-brand-primary hover:text-opacity-80 dark:text-blue-400 dark:hover:text-blue-300">
                                                Edit
                                            </button>
                                            <DangerButton @click="deleteCity(city)" class="ml-3 text-xs px-2 py-1">
                                                Delete
                                            </DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination class="mt-6" :links="cities.links" />

                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
            <form @submit.prevent="submitForm" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingCity ? 'Edit City' : 'Add New City' }}
                </h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="state_id" value="State / Province *" />
                         <select
                            id="state_id"
                            v-model="form.state_id"
                            required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="" disabled>Select a state/province</option>
                            <option v-for="state in props.states" :key="state.id" :value="state.id">
                                {{ state.name }} ({{ countries.find(c => c.id === state.country_id)?.name }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.state_id" />
                    </div>
                    <div>
                        <InputLabel for="name" value="City Name *" />
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
                        {{ form.processing ? 'Saving...' : 'Save City' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <Modal :show="showUploadModal" @close="showUploadModal = false">
             <form @submit.prevent="submitUpload" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Upload Cities CSV
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    CSV must have columns: `name`, `state_name`, `country_iso2`.
                </p>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="country_id_context_city" value="Upload for Specific Country (Optional)" />
                         <select
                            id="country_id_context_city"
                            v-model="csvForm.country_id_context"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="">Upload cities for multiple countries (Match by State Name + Country ISO2)</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="csvForm.errors.country_id_context" />
                    </div>
                    <div>
                        <InputLabel for="state_id_context_city" value="Upload for Specific State (Optional)" />
                         <select
                            id="state_id_context_city"
                            v-model="csvForm.state_id_context"
                            :disabled="!csvForm.country_id_context" {{-- Only enable if country is selected --}}
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm disabled:opacity-50"
                        >
                            <option value="">Upload cities for selected country (Match by State Name)</option>
                            <option v-for="state in states.filter(s => s.country_id == csvForm.country_id_context)" :key="state.id" :value="state.id">
                                {{ state.name }}
                            </option>
                        </select>
                         <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                             If State is selected, CSV only needs `name`. If only Country selected, needs `name` and `state_name`. Otherwise, needs `name`, `state_name`, `country_iso2`.
                         </p>
                        <InputError class="mt-2" :message="csvForm.errors.state_id_context" />
                    </div>

                    <div>
                        <InputLabel for="csv_file_city" value="CSV File *" />
                        <input
                            id="csv_file_city"
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