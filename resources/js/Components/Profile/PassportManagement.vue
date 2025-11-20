<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    passports: {
        type: Array,
        default: () => []
    }
});

const { formatDate } = useBangladeshFormat();
const showModal = ref(false);
const editingPassport = ref(null);
const showDeleteModal = ref(false);
const deletingPassport = ref(null);

const form = useForm({
    id: null,
    passport_number: '',
    passport_type: 'regular',
    issuing_country: 'BD',
    issuing_authority: '',
    issue_date: '',
    expiry_date: '',
    place_of_issue: '',
    is_current_passport: false,
    is_lost_or_stolen: false,
    surname: '',
    given_names: '',
    nationality: 'BD',
    sex: 'M',
    date_of_birth: '',
    place_of_birth: '',
    notes: ''
});

const passportTypes = [
    { value: 'regular', label: 'Regular' },
    { value: 'diplomatic', label: 'Diplomatic' },
    { value: 'official', label: 'Official' },
    { value: 'service', label: 'Service' },
    { value: 'emergency', label: 'Emergency' }
];

const countries = [
    { code: 'BD', name: 'Bangladesh' },
    { code: 'IN', name: 'India' },
    { code: 'PK', name: 'Pakistan' },
    { code: 'US', name: 'United States' },
    { code: 'GB', name: 'United Kingdom' },
    { code: 'CA', name: 'Canada' },
    { code: 'AU', name: 'Australia' }
];

const openAddModal = () => {
    form.reset();
    editingPassport.value = null;
    showModal.value = true;
};

const openEditModal = (passport) => {
    editingPassport.value = passport;
    form.id = passport.id;
    form.passport_number = passport.passport_number;
    form.passport_type = passport.passport_type;
    form.issuing_country = passport.issuing_country;
    form.issuing_authority = passport.issuing_authority || '';
    form.issue_date = passport.issue_date;
    form.expiry_date = passport.expiry_date;
    form.place_of_issue = passport.place_of_issue || '';
    form.is_current_passport = passport.is_current_passport;
    form.is_lost_or_stolen = passport.is_lost_or_stolen;
    form.surname = passport.surname || '';
    form.given_names = passport.given_names || '';
    form.nationality = passport.nationality || 'BD';
    form.sex = passport.sex || 'M';
    form.date_of_birth = passport.date_of_birth || '';
    form.place_of_birth = passport.place_of_birth || '';
    form.notes = passport.notes || '';
    showModal.value = true;
};

const savePassport = () => {
    if (editingPassport.value) {
        form.put(route('profile.passports.update', editingPassport.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('profile.passports.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const confirmDelete = (passport) => {
    deletingPassport.value = passport;
    showDeleteModal.value = true;
};

const deletePassport = () => {
    form.delete(route('profile.passports.destroy', deletingPassport.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingPassport.value = null;
        }
    });
};

const getPassportStatus = (passport) => {
    const expiryDate = new Date(passport.expiry_date);
    const now = new Date();
    const sixMonthsFromNow = new Date();
    sixMonthsFromNow.setMonth(sixMonthsFromNow.getMonth() + 6);

    if (expiryDate < now) {
        return { text: 'Expired', class: 'bg-red-100 text-red-800' };
    } else if (expiryDate < sixMonthsFromNow) {
        return { text: 'Expiring Soon', class: 'bg-yellow-100 text-yellow-800' };
    } else {
        return { text: 'Valid', class: 'bg-green-100 text-green-800' };
    }
};

const getDaysUntilExpiry = (expiryDate) => {
    const expiry = new Date(expiryDate);
    const now = new Date();
    const diffTime = expiry - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Passport Information</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Manage your passports for visa applications. All countries require valid passport.
                </p>
            </div>
            <PrimaryButton @click="openAddModal">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Passport
            </PrimaryButton>
        </div>

        <div class="p-6">
            <!-- Passports List -->
            <div v-if="passports.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No passports</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your passport information.</p>
                <div class="mt-6">
                    <PrimaryButton @click="openAddModal">
                        Add Your First Passport
                    </PrimaryButton>
                </div>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="passport in passports"
                    :key="passport.id"
                    class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
                    :class="{ 'ring-2 ring-indigo-500': passport.is_current_passport }"
                >
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ passport.passport_number }}
                                </h4>
                                <span
                                    v-if="passport.is_current_passport"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                >
                                    Current
                                </span>
                                <span
                                    :class="getPassportStatus(passport).class"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ getPassportStatus(passport).text }}
                                </span>
                                <span
                                    v-if="passport.is_lost_or_stolen"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                >
                                    Lost/Stolen
                                </span>
                            </div>

                            <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Type:</span>
                                    <span class="ml-2 text-gray-900 capitalize">{{ passport.passport_type }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Country:</span>
                                    <span class="ml-2 text-gray-900">{{ passport.issuing_country }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Issue Date:</span>
                                    <span class="ml-2 text-gray-900">{{ formatDate(passport.issue_date) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Expiry Date:</span>
                                    <span class="ml-2 text-gray-900">{{ formatDate(passport.expiry_date) }}</span>
                                    <span
                                        v-if="getDaysUntilExpiry(passport.expiry_date) > 0"
                                        class="ml-2 text-xs text-gray-500"
                                    >
                                        ({{ getDaysUntilExpiry(passport.expiry_date) }} days remaining)
                                    </span>
                                </div>
                                <div v-if="passport.place_of_issue" class="col-span-2">
                                    <span class="text-gray-500">Place of Issue:</span>
                                    <span class="ml-2 text-gray-900">{{ passport.place_of_issue }}</span>
                                </div>
                                <div v-if="passport.surname || passport.given_names" class="col-span-2">
                                    <span class="text-gray-500">Name:</span>
                                    <span class="ml-2 text-gray-900">
                                        {{ passport.surname }}, {{ passport.given_names }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="passport.notes" class="mt-2 text-sm text-gray-600 italic">
                                {{ passport.notes }}
                            </div>
                        </div>

                        <div class="flex gap-2 ml-4">
                            <SecondaryButton @click="openEditModal(passport)" class="text-sm">
                                Edit
                            </SecondaryButton>
                            <DangerButton @click="confirmDelete(passport)" class="text-sm">
                                Delete
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="showModal" @close="showModal = false" max-width="3xl">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ editingPassport ? 'Edit Passport' : 'Add New Passport' }}
                </h2>

                <form @submit.prevent="savePassport" class="mt-6 space-y-6">
                    <!-- Passport Number -->
                    <div>
                        <InputLabel for="passport_number" value="Passport Number *" />
                        <TextInput
                            id="passport_number"
                            v-model="form.passport_number"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            placeholder="e.g., BN1234567"
                        />
                        <InputError :message="form.errors.passport_number" class="mt-2" />
                    </div>

                    <!-- Type and Country -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="passport_type" value="Passport Type *" />
                            <select
                                id="passport_type"
                                v-model="form.passport_type"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option v-for="type in passportTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors.passport_type" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="issuing_country" value="Issuing Country *" />
                            <select
                                id="issuing_country"
                                v-model="form.issuing_country"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option v-for="country in countries" :key="country.code" :value="country.code">
                                    {{ country.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.issuing_country" class="mt-2" />
                        </div>
                    </div>

                    <!-- Issue and Expiry Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="issue_date" value="Issue Date *" />
                            <TextInput
                                id="issue_date"
                                v-model="form.issue_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.issue_date" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="expiry_date" value="Expiry Date *" />
                            <TextInput
                                id="expiry_date"
                                v-model="form.expiry_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.expiry_date" class="mt-2" />
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="surname" value="Surname (as on passport)" />
                            <TextInput
                                id="surname"
                                v-model="form.surname"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.surname" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="given_names" value="Given Names (as on passport)" />
                            <TextInput
                                id="given_names"
                                v-model="form.given_names"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.given_names" class="mt-2" />
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="form.is_current_passport"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <span class="ml-2 text-sm text-gray-600">This is my current passport</span>
                        </label>

                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="form.is_lost_or_stolen"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <span class="ml-2 text-sm text-gray-600">This passport is lost or stolen</span>
                        </label>
                    </div>

                    <!-- Notes -->
                    <div>
                        <InputLabel for="notes" value="Notes" />
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Any additional notes about this passport..."
                        ></textarea>
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <SecondaryButton @click="showModal = false" type="button">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ editingPassport ? 'Update' : 'Save' }} Passport
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Delete Passport
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Are you sure you want to delete this passport? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deletePassport" :disabled="form.processing">
                        Delete Passport
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
