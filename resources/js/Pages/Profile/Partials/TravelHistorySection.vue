<script setup>
import { ref, onMounted }from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';
import { ChevronUpIcon, ChevronDownIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';

// Props
const props = defineProps({
    countries: Array, // Passed down from Edit.vue
});

// Collapsible state
const isOpen = ref(true); // Default to open
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// State
const travelHistoryList = ref([]);
const editingHistoryId = ref(null); // Holds the ID of the item being edited

const form = useForm({
    country_id: '',
    entry_date: '',
    exit_date: '',
    purpose: '',
    notes: '',
});

// Fetch data
const getTravelHistory = () => {
    axios.get(route('api.profile.travel-histories.index'))
        .then(response => {
            travelHistoryList.value = response.data;
        })
        .catch(error => console.error("Error fetching travel history:", error));
};

// Submit form (Create or Update)
const submitTravelHistory = () => {
    const url = editingHistoryId.value
        ? route('api.profile.travel-histories.update', editingHistoryId.value)
        : route('api.profile.travel-histories.store');
    
    const method = editingHistoryId.value ? 'put' : 'post';

    // We use axios for API routes, not form.submit
    axios[method](url, form.data())
        .then(response => {
            form.reset();
            form.clearErrors();
            editingHistoryId.value = null;
            getTravelHistory(); // Refresh the list
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

// Start editing an item
const editHistory = (history) => {
    editingHistoryId.value = history.id;
    form.country_id = history.country_id;
    form.entry_date = history.entry_date ? history.entry_date.substring(0, 10) : '';
    form.exit_date = history.exit_date ? history.exit_date.substring(0, 10) : '';
    form.purpose = history.purpose;
    form.notes = history.notes || '';
    form.clearErrors();
};

// Cancel editing
const cancelEdit = () => {
    editingHistoryId.value = null;
    form.reset();
    form.clearErrors();
};

// Delete item
const deleteHistory = (historyId) => {
    if (!confirm("Are you sure you want to delete this travel record?")) return;

    axios.delete(route('api.profile.travel-histories.destroy', historyId))
        .then(() => {
            getTravelHistory(); // Refresh the list
            if (editingHistoryId.value === historyId) {
                cancelEdit();
            }
        })
        .catch(error => {
            console.error("Error deleting travel history:", error);
            alert("Failed to delete record.");
        });
};

// Load data on mount
onMounted(() => {
    getTravelHistory();
});

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        // Use UTC date constructor to avoid timezone issues
        const date = new Date(dateString + 'T00:00:00Z');
        return date.toLocaleDateString(undefined, { timeZone: 'UTC', year: 'numeric', month: 'short', day: 'numeric' });
    } catch (e) {
        console.error("Error formatting date:", dateString, e);
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
                        Travel History
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add countries you have visited for work, study, or tourism.
                    </p>
                </div>
                <div>
                    <ChevronUpIcon v-if="isOpen" class="w-6 h-6 text-gray-500" />
                    <ChevronDownIcon v-else class="w-6 h-6 text-gray-500" />
                </div>
            </header>

            <div v-show="isOpen" class="mt-6 space-y-6">
                
                <form @submit.prevent="submitTravelHistory" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        {{ editingHistoryId ? 'Update Travel Record' : 'Add New Travel Record' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="th_country" value="Country" />
                            <select
                                id="th_country"
                                v-model="form.country_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            >
                                <option value="" disabled>Select a country</option>
                                <option v-for="country in props.countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.country_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="th_purpose" value="Purpose of Visit" />
                            <TextInput
                                id="th_purpose"
                                v-model="form.purpose"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Tourism, Work, Study"
                                required
                            />
                            <InputError :message="form.errors.purpose" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <InputLabel for="th_entry_date" value="Entry Date" />
                            <TextInput
                                id="th_entry_date"
                                v-model="form.entry_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.entry_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="th_exit_date" value="Exit Date (Optional)" />
                            <TextInput
                                id="th_exit_date"
                                v-model="form.exit_date"
                                type="date"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.exit_date" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="th_notes" value="Notes (Optional)" />
                        <TextareaInput
                            id="th_notes"
                            v-model="form.notes"
                            class="mt-1 block w-full"
                            rows="3"
                            placeholder="Add any relevant notes about this trip..."
                        />
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            <PlusIcon v-if="!editingHistoryId" class="w-4 h-4 mr-2" />
                            {{ editingHistoryId ? 'Update Record' : 'Save Record' }}
                        </PrimaryButton>
                        <SecondaryButton v-if="editingHistoryId" type="button" @click="cancelEdit">
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
                        Saved Travel History
                    </h3>
                    <div v-if="travelHistoryList.length === 0" class="text-center text-gray-500 dark:text-gray-400 p-4 border-dashed border-2 border-gray-300 dark:border-gray-700 rounded-lg">
                        No travel history added yet.
                    </div>
                    
                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="history in travelHistoryList" :key="history.id" class="py-4 flex flex-col md:flex-row justify-between md:items-center">
                            <div class="flex-1 mb-2 md:mb-0">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ history.country ? history.country.name : 'Unknown Country' }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ history.purpose }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    {{ formatDate(history.entry_date) }} - {{ formatDate(history.exit_date) }}
                                </p>
                                <p v-if="history.notes" class="text-sm text-gray-700 dark:text-gray-300 mt-2 italic">
                                    "{{ history.notes }}"
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <SecondaryButton @click="editHistory(history)" class="!px-3 !py-2">
                                    <PencilIcon class="w-4 h-4" />
                                </SecondaryButton>
                                <DangerButton @click="deleteHistory(history.id)" class="!px-3 !py-2">
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