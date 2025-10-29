<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ChevronUpIcon, ChevronDownIcon, PlusIcon, PencilIcon, TrashIcon, CalendarIcon } from '@heroicons/vue/24/solid';

// Collapsible state
const isOpen = ref(true);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// State
const licenseList = ref([]);
const editingLicenseId = ref(null);

const form = useForm({
    name: '',
    issuing_organization: '',
    issue_date: '',
    expiry_date: '',
    credential_id: '',
});

// Fetch data
const getLicenses = () => {
    axios.get(route('api.profile.licenses.index'))
        .then(response => {
            licenseList.value = response.data;
        })
        .catch(error => console.error("Error fetching licenses:", error));
};

// Submit form (Create or Update)
const submitLicense = () => {
    const url = editingLicenseId.value
        ? route('api.profile.licenses.update', editingLicenseId.value)
        : route('api.profile.licenses.store');
    
    const method = editingLicenseId.value ? 'put' : 'post';

    axios[method](url, form.data())
        .then(() => {
            form.reset();
            form.clearErrors();
            editingLicenseId.value = null;
            getLicenses(); // Refresh list
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
const editLicense = (license) => {
    editingLicenseId.value = license.id;
    form.name = license.name;
    form.issuing_organization = license.issuing_organization;
    form.issue_date = license.issue_date ? license.issue_date.substring(0, 10) : '';
    form.expiry_date = license.expiry_date ? license.expiry_date.substring(0, 10) : '';
    form.credential_id = license.credential_id || '';
    form.clearErrors();
};

// Cancel editing
const cancelEdit = () => {
    editingLicenseId.value = null;
    form.reset();
    form.clearErrors();
};

// Delete item
const deleteLicense = (licenseId) => {
    if (!confirm("Are you sure you want to delete this license?")) return;

    axios.delete(route('api.profile.licenses.destroy', licenseId))
        .then(() => {
            getLicenses(); // Refresh list
            if (editingLicenseId.value === licenseId) {
                cancelEdit();
            }
        })
        .catch(error => {
            console.error("Error deleting license:", error);
            alert("Failed to delete record.");
        });
};

// Load data on mount
onMounted(() => {
    getLicenses();
});

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return 'No Expiry';
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
                        Licenses & Certifications
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add your professional licenses, certifications, or credentials.
                    </p>
                </div>
                <div>
                    <ChevronUpIcon v-if="isOpen" class="w-6 h-6 text-gray-500" />
                    <ChevronDownIcon v-else class="w-6 h-6 text-gray-500" />
                </div>
            </header>

            <div v-show="isOpen" class="mt-6 space-y-6">
                
                <form @submit.prevent="submitLicense" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        {{ editingLicenseId ? 'Update License' : 'Add New License' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="lc_name" value="License/Certification Name" />
                            <TextInput
                                id="lc_name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., PMP, IELTS"
                                required
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="lc_org" value="Issuing Organization" />
                            <TextInput
                                id="lc_org"
                                v-model="form.issuing_organization"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., PMI, British Council"
                                required
                            />
                            <InputError :message="form.errors.issuing_organization" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <InputLabel for="lc_issue_date" value="Issue Date" />
                            <TextInput
                                id="lc_issue_date"
                                v-model="form.issue_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.issue_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="lc_expiry_date" value="Expiry Date (Optional)" />
                            <TextInput
                                id="lc_expiry_date"
                                v-model="form.expiry_date"
                                type="date"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.expiry_date" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="lc_credential_id" value="Credential ID (Optional)" />
                        <TextInput
                            id="lc_credential_id"
                            v-model="form.credential_id"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g., 1234567"
                        />
                        <InputError :message="form.errors.credential_id" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            <PlusIcon v-if="!editingLicenseId" class="w-4 h-4 mr-2" />
                            {{ editingLicenseId ? 'Update License' : 'Save License' }}
                        </PrimaryButton>
                        <SecondaryButton v-if="editingLicenseId" type="button" @click="cancelEdit">
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
                        Your Licenses
                    </h3>
                    <div v-if="licenseList.length === 0" class="text-center text-gray-500 dark:text-gray-400 p-4 border-dashed border-2 border-gray-300 dark:border-gray-700 rounded-lg">
                        No licenses added yet.
                    </div>
                    
                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="license in licenseList" :key="license.id" class="py-4 flex flex-col md:flex-row justify-between md:items-center">
                            <div class="flex-1 mb-2 md:mb-0">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ license.name }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Issued by {{ license.issuing_organization }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    <CalendarIcon class="w-4 h-4 inline-block -mt-1 mr-1" />
                                    Issued {{ formatDate(license.issue_date) }} | 
                                    Expires {{ formatDate(license.expiry_date) }}
                                </p>
                                <p v-if="license.credential_id" class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                    ID: {{ license.credential_id }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <SecondaryButton @click="editLicense(license)" class="!px-3 !py-2">
                                    <PencilIcon class="w-4 h-4" />
                                </SecondaryButton>
                                <DangerButton @click="deleteLicense(license.id)" class="!px-3 !py-2">
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