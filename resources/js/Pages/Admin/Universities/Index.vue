<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import debounce from 'lodash/debounce';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    universities: Object,
    countries: Array,
    filters: Object,
    universityToEdit: Object,
});

// --- State ---
const showCreateEditModal = ref(false);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const statesForModal = ref([]);
const citiesForModal = ref([]);
const isLoadingStates = ref(false);
const isLoadingCities = ref(false);
const selectedCountryHasStates = ref(true); // Assume yes until checked

// --- Form ---
const form = useForm({
    id: null,
    name: '',
    description: '',
    website_url: '',
    selected_country_id: '', // For modal country dropdown
    selected_state_id: '',   // For modal state dropdown
    city_id: '',           // Final selected city
    is_active: true,
});

const isEditMode = computed(() => form.id !== null);

// --- Search ---
const search = ref(props.filters.search || '');
watch(search, debounce((value) => {
    router.get(route('admin.universities.index'), { search: value }, {
        preserveState: true, replace: true,
    });
}, 300));

// --- Dynamic Dropdown Logic ---

// Helper to clear dropdowns and reset flags
const resetLocationDownstream = (level = 'country') => {
    if (level === 'country') {
        form.selected_state_id = '';
        statesForModal.value = [];
        selectedCountryHasStates.value = true; // Reset assumption
    }
    if (level === 'country' || level === 'state') {
        form.city_id = '';
        citiesForModal.value = [];
    }
    // Always reset loading flags when clearing
    isLoadingStates.value = false;
    isLoadingCities.value = false;
};

const fetchStates = async (countryId) => {
    resetLocationDownstream('country');
    if (!countryId) return;

    isLoadingStates.value = true;
    let hasStates = false;

    try {
        const response = await axios.get(route('admin.geography.getStates', { country_id: countryId }));
        statesForModal.value = response.data;
        hasStates = statesForModal.value.length > 0;
        selectedCountryHasStates.value = hasStates;

        if (!hasStates) {
            // Country has no states, fetch cities directly
            await fetchCities(countryId, null);
        }
    } catch (error) {
        console.error("Error fetching states:", error);
        resetLocationDownstream('country'); // Clear all on error
    } finally {
        isLoadingStates.value = false;
        // If country *has* states, city loading isn't active yet
        if (hasStates) {
            isLoadingCities.value = false;
        }
    }
};

const fetchCities = async (countryId, stateId) => {
    resetLocationDownstream('state'); // Clear city
    const requiresState = selectedCountryHasStates.value;

    // Conditions to fetch:
    const canFetchViaState = requiresState && stateId;
    const canFetchViaCountry = !requiresState && countryId;

    if (!canFetchViaState && !canFetchViaCountry) {
         isLoadingCities.value = false; // Ensure reset if no condition met
         return;
    }

    isLoadingCities.value = true;
    let url = '';
    let params = {};

    if (canFetchViaState) {
        url = route('admin.geography.getCitiesForState');
        params = { state_id: stateId };
    } else if (canFetchViaCountry) {
        url = route('admin.geography.getCitiesForCountry');
        params = { country_id: countryId };
    }

    try {
        const response = await axios.get(url, { params });
        citiesForModal.value = response.data;
    } catch (error) {
        console.error("Error fetching cities:", error);
        resetLocationDownstream('state'); // Clear city on error
    } finally {
        isLoadingCities.value = false; // *** This is the critical fix ***
    }
};

// Watchers for dropdowns
watch(() => form.selected_country_id, (newCountryId) => {
    fetchStates(newCountryId);
});

watch(() => form.selected_state_id, (newStateId) => {
    // Only fetch cities based on state if the country actually has states
    if (selectedCountryHasStates.value) {
         fetchCities(null, newStateId); // Pass null for countryId, it will fetch by state
    }
    // The "no states" case is handled by the fetchStates function
});


// --- Modals ---
const openModal = (itemToEdit = null) => {
    form.reset();
    resetLocationDownstream('country');

    if (itemToEdit) {
        // Fetch full data via Inertia partial reload
        // This will trigger the 'universityToEdit' watcher
        router.get(route('admin.universities.index'), { edit_id: itemToEdit.id }, {
            preserveState: true, preserveScroll: true, replace: true,
        });
    } else {
        // Open modal for creating new item
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
    resetLocationDownstream('country'); // Clear dynamic lists and flags on close

    if (props.universityToEdit) {
         router.get(route('admin.universities.index'), { search: search.value }, {
            preserveState: true, preserveScroll: true, replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.universityToEdit, async (newItem) => {
    if (newItem && newItem.city) { // Check if city relationship is loaded
        form.id = newItem.id;
        form.name = newItem.name;
        form.description = newItem.description || '';
        form.website_url = newItem.website_url || '';
        form.is_active = newItem.is_active;

        let initialCountryId = null;
        let initialStateId = null;

        if (newItem.city.state) { // City -> State -> Country
             initialStateId = newItem.city.state_id;
             initialCountryId = newItem.city.state.country_id;
        } else if (newItem.city.country_id) { // City -> Country
             initialStateId = ''; // No state
             initialCountryId = newItem.city.country_id;
        }

        if(initialCountryId){
            form.selected_country_id = initialCountryId; // Triggers state fetch
            await fetchStates(initialCountryId); // Wait for states to load

            if(initialStateId) {
                form.selected_state_id = initialStateId; // Triggers city fetch
                await fetchCities(null, initialStateId); // Wait for cities to load
            }
            
            form.city_id = newItem.city_id; // Set final city
        } else {
             console.error("Incomplete location data for university:", newItem.id);
             resetLocationDownstream('country');
        }

        showCreateEditModal.value = true;
    } else if (newItem) {
        // Handle missing city data
        console.error("University data loaded without city information:", newItem.id);
        form.id = newItem.id;
        form.name = newItem.name;
        form.is_active = newItem.is_active;
        resetLocationDownstream('country');
        showCreateEditModal.value = true;
    }
});

// --- Actions ---
const submitForm = () => {
     const dataToSend = {
        name: form.name,
        description: form.description,
        website_url: form.website_url,
        city_id: form.city_id,
        is_active: form.is_active,
    };

    if (isEditMode.value) {
        router.put(route('admin.universities.update', form.id), dataToSend, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: (errors) => { form.errors = errors; }
        });
    } else {
        router.post(route('admin.universities.store'), dataToSend, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: (errors) => { form.errors = errors; }
        });
    }
};

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('admin.universities.destroy', itemToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Helper to get location string
const getLocation = (uni) => {
    if (!uni.city) return 'N/A';
    if (uni.city.state) {
        return `${uni.city.name}, ${uni.city.state.name}, ${uni.city.state.country.name}`;
    }
    if (uni.city.country) {
        return `${uni.city.name}, ${uni.city.country.name}`;
    }
    return uni.city.name;
};
</script>

<template>
    </template>