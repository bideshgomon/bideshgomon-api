<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import {
    NATIONALITIES,
    GENDER_OPTIONS,
    BANGLADESH_DIVISIONS,
    BANGLADESH_DISTRICTS
} from '@/Constants/profileData';

const props = defineProps({
    userProfile: Object,
    divisions: Array,
});

const { formatDateFromISO, parseDateToISO } = useBangladeshFormat();

// Display format for date (DD/MM/YYYY)
const displayDob = ref(props.userProfile?.dob ? formatDateFromISO(props.userProfile.dob) : '');

const form = useForm({
    bio: props.userProfile?.bio || '',
    phone: props.userProfile?.phone || '',
    dob: props.userProfile?.dob || '',
    gender: props.userProfile?.gender || '',
    nationality: props.userProfile?.nationality || 'Bangladeshi',
    present_address_line: props.userProfile?.present_address_line || '',
    present_division: props.userProfile?.present_division || '',
    present_district: props.userProfile?.present_district || '',
    permanent_address_line: props.userProfile?.permanent_address_line || '',
    permanent_division: props.userProfile?.permanent_division || '',
    permanent_district: props.userProfile?.permanent_district || '',
    nid: props.userProfile?.nid || '',
    passport_number: props.userProfile?.passport_number || '',
    passport_issue_date: props.userProfile?.passport_issue_date || '',
    passport_expiry_date: props.userProfile?.passport_expiry_date || '',
});

// Get districts based on selected division
const presentDistricts = computed(() => {
    return form.present_division ? BANGLADESH_DISTRICTS[form.present_division] || [] : [];
});

const permanentDistricts = computed(() => {
    return form.permanent_division ? BANGLADESH_DISTRICTS[form.permanent_division] || [] : [];
});

// Update form.dob when display format changes
const updateDob = (value) => {
    displayDob.value = value;
    form.dob = parseDateToISO(value);
};

const saveError = ref('');

const submit = () => {
    saveError.value = '';
    form.post(route('profile.update.details'), {
        preserveScroll: true,
        onSuccess: () => {
            if (form.dob) {
                displayDob.value = formatDateFromISO(form.dob);
            }
        },
        onError: (errors) => {
            saveError.value = 'Failed to save profile details. Please check the form and try again.';
            console.error('Save error:', errors);
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Details</h2>
            <p class="mt-1 text-sm text-gray-600">
                Complete your profile information with Bangladesh-specific details.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <!-- Bio -->
            <div>
                <InputLabel for="bio" value="Bio" />
                <textarea
                    id="bio"
                    v-model="form.bio"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    rows="3"
                    placeholder="Tell us about yourself..."
                />
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <!-- Phone -->
            <div>
                <InputLabel for="phone" value="Phone Number" />
                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    placeholder="+880 1XXX-XXXXXX"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <!-- Date of Birth & Gender -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel for="dob" value="Date of Birth" />
                    <input
                        id="dob"
                        type="text"
                        v-model="displayDob"
                        @blur="updateDob(displayDob)"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="DD/MM/YYYY"
                    />
                    <p class="mt-1 text-xs text-gray-500">Format: DD/MM/YYYY (e.g., 15/08/1990)</p>
                    <InputError class="mt-2" :message="form.errors.dob" />
                </div>

                <div>
                    <InputLabel for="gender" value="Gender" />
                    <select
                        id="gender"
                        v-model="form.gender"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Select Gender</option>
                        <option v-for="gender in GENDER_OPTIONS" :key="gender" :value="gender.toLowerCase()">
                            {{ gender }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.gender" />
                </div>
            </div>

            <!-- Nationality -->
            <div>
                <InputLabel for="nationality" value="Nationality" />
                <input
                    id="nationality"
                    v-model="form.nationality"
                    list="nationalities-list"
                    type="text"
                    placeholder="Type to search nationality..."
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                />
                <datalist id="nationalities-list">
                    <option v-for="nationality in NATIONALITIES" :key="nationality" :value="nationality">
                        {{ nationality }}
                    </option>
                </datalist>
                <InputError class="mt-2" :message="form.errors.nationality" />
            </div>

            <!-- Present Address -->
            <div class="border-t pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Present Address</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="present_division" value="Division" />
                            <select
                                id="present_division"
                                v-model="form.present_division"
                                @change="form.present_district = ''"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Select Division</option>
                                <option v-for="division in BANGLADESH_DIVISIONS" :key="division" :value="division">
                                    {{ division }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.present_division" />
                        </div>

                        <div>
                            <InputLabel for="present_district" value="District" />
                            <select
                                id="present_district"
                                v-model="form.present_district"
                                :disabled="!form.present_division"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                                <option value="">{{ form.present_division ? 'Select District' : 'Select division first' }}</option>
                                <option v-for="district in presentDistricts" :key="district" :value="district">
                                    {{ district }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.present_district" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="present_address_line" value="Full Address" />
                        <textarea
                            id="present_address_line"
                            v-model="form.present_address_line"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="2"
                            placeholder="House/Flat, Road, Area"
                        />
                        <InputError class="mt-2" :message="form.errors.present_address_line" />
                    </div>
                </div>
            </div>

            <!-- Permanent Address -->
            <div class="border-t pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Permanent Address</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="permanent_division" value="Division" />
                            <select
                                id="permanent_division"
                                v-model="form.permanent_division"
                                @change="form.permanent_district = ''"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Select Division</option>
                                <option v-for="division in BANGLADESH_DIVISIONS" :key="division" :value="division">
                                    {{ division }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.permanent_division" />
                        </div>

                        <div>
                            <InputLabel for="permanent_district" value="District" />
                            <select
                                id="permanent_district"
                                v-model="form.permanent_district"
                                :disabled="!form.permanent_division"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                                <option value="">{{ form.permanent_division ? 'Select District' : 'Select division first' }}</option>
                                <option v-for="district in permanentDistricts" :key="district" :value="district">
                                    {{ district }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.permanent_district" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="permanent_address_line" value="Full Address" />
                        <textarea
                            id="permanent_address_line"
                            v-model="form.permanent_address_line"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="2"
                            placeholder="House/Flat, Road, Area"
                        />
                        <InputError class="mt-2" :message="form.errors.permanent_address_line" />
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="border-t pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Identity Documents</h3>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="nid" value="National ID (NID)" />
                        <TextInput
                            id="nid"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.nid"
                            placeholder="10 or 17 digits"
                        />
                        <InputError class="mt-2" :message="form.errors.nid" />
                    </div>

                    <div>
                        <InputLabel for="passport_number" value="Passport Number" />
                        <TextInput
                            id="passport_number"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.passport_number"
                            placeholder="e.g., BN1234567"
                        />
                        <InputError class="mt-2" :message="form.errors.passport_number" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="passport_issue_date" value="Passport Issue Date" />
                            <TextInput
                                id="passport_issue_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.passport_issue_date"
                            />
                            <InputError class="mt-2" :message="form.errors.passport_issue_date" />
                        </div>

                        <div>
                            <InputLabel for="passport_expiry_date" value="Passport Expiry Date" />
                            <TextInput
                                id="passport_expiry_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.passport_expiry_date"
                            />
                            <InputError class="mt-2" :message="form.errors.passport_expiry_date" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Error Message -->
                <div v-if="saveError" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ saveError }}</h3>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="saveError = ''" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                            ✓ Saved successfully.
                        </p>
                    </Transition>
                </div>
            </div>
        </form>
    </section>
</template>
