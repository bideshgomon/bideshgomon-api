<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { DocumentTextIcon } from '@heroicons/vue/24/solid';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import {
    GENDER_OPTIONS,
    BANGLADESH_DIVISIONS,
    BANGLADESH_DISTRICTS
} from '@/Constants/profileData';

const props = defineProps({
    userProfile: Object,
    divisions: Array,
    countries: Array,
});

const { formatDateFromISO, parseDateToISO } = useBangladeshFormat();

// Display format for date (DD/MM/YYYY)
const displayDob = ref(props.userProfile?.dob ? formatDateFromISO(props.userProfile.dob) : '');

// Format dates to YYYY-MM-DD for date inputs
const formatDateForInput = (dateStr) => {
    if (!dateStr) return '';
    // If it's already in YYYY-MM-DD format, return as is
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
    // If it's a full datetime, extract the date part
    return dateStr.split(' ')[0];
};

const form = useForm({
    bio: props.userProfile?.bio || '',
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
        <header class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-growth-500 flex items-center justify-center shadow-rhythmic-md">
                    <DocumentTextIcon class="h-6 w-6 text-white" />
                </div>
                <div>
                    <h2 class="font-display font-bold text-xl text-gray-800">Profile Details</h2>
                    <p class="text-xs text-gray-500">Bangladesh-specific information</p>
                </div>
            </div>
        </header>

        <form @submit.prevent="submit" class="space-y-rhythm-md">
            <RhythmicCard variant="light" class="space-y-rhythm-md">
            <!-- Bio -->
            <div class="space-y-rhythm-sm">
                <InputLabel for="bio" value="Bio" class="text-sm md:text-base" />
                <textarea
                    id="bio"
                    v-model="form.bio"
                    class="block w-full border-gray-300 focus:border-ocean-500 focus:ring-ocean-500 rounded-lg shadow-sm text-base py-3 px-4 touch-manipulation"
                    rows="3"
                    placeholder="Tell us about yourself..."
                />
                <InputError class="mt-1" :message="form.errors.bio" />
            </div>

            <!-- Date of Birth, NID & Gender -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="space-y-rhythm-xs">
                    <InputLabel for="dob" value="Date of Birth" />
                    <input
                        id="dob"
                        type="text"
                        v-model="displayDob"
                        @blur="updateDob(displayDob)"
                        class="block w-full border-gray-300 focus:border-ocean-500 focus:ring-ocean-500 rounded-lg shadow-sm py-2 px-3"
                        placeholder="DD/MM/YYYY"
                    />
                    <p class="text-xs text-gray-500">Format: DD/MM/YYYY (e.g., 15/08/1990)</p>
                    <InputError :message="form.errors.dob" />
                </div>

                <div class="space-y-rhythm-xs">
                    <InputLabel for="nid" value="National ID (NID)" />
                    <TextInput
                        id="nid"
                        type="text"
                        class="block w-full"
                        v-model="form.nid"
                        placeholder="10 or 17 digits"
                    />
                    <InputError :message="form.errors.nid" />
                </div>

                <div class="space-y-rhythm-xs">
                    <InputLabel for="gender" value="Gender" />
                    <select
                        id="gender"
                        v-model="form.gender"
                        class="block w-full border-gray-300 focus:border-ocean-500 focus:ring-ocean-500 rounded-lg shadow-sm py-2 px-3"
                    >
                        <option value="">Select Gender</option>
                        <option v-for="gender in GENDER_OPTIONS" :key="gender" :value="gender.toLowerCase()">
                            {{ gender }}
                        </option>
                    </select>
                    <InputError :message="form.errors.gender" />
                </div>
            </div>

            <!-- Nationality -->
            <div class="space-y-rhythm-xs">
                <InputLabel for="nationality" value="Nationality" />
                <input
                    id="nationality"
                    v-model="form.nationality"
                    list="nationalities-list"
                    type="text"
                    placeholder="Type to search nationality..."
                    class="block w-full border-gray-300 focus:border-ocean-500 focus:ring-ocean-500 rounded-lg shadow-sm py-2 px-3"
                />
                <datalist id="nationalities-list">
                    <option v-for="country in countries" :key="country.id" :value="country.nationality">
                        {{ country.nationality }}
                    </option>
                </datalist>
                <InputError :message="form.errors.nationality" />
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

            <div class="space-y-4">
                <!-- Error Message -->
                <div v-if="saveError" class="rounded-md bg-red-50 p-3 md:p-4 text-sm md:text-base">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 md:h-5 md:w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-xs md:text-sm font-medium text-red-800">{{ saveError }}</h3>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="saveError = ''" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 touch-manipulation">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Save Button -->
                <div class="hidden md:flex items-center gap-4 mt-rhythm-lg">
                    <FlowButton 
                        type="submit"
                        :disabled="form.processing"
                        :loading="form.processing"
                    >
                        Save Changes
                    </FlowButton>
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">
                            ✓ Saved successfully!
                        </p>
                    </Transition>
                </div>
                </div>
            </RhythmicCard>
        </form>
        
        <!-- Mobile Sticky Save Button -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-2xl z-30 safe-area-bottom">
            <button
                @click="submit"
                :disabled="form.processing"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg active:scale-98 transition-all touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-base"
            >
                <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ form.processing ? 'Saving...' : (form.recentlySuccessful ? '✓ Saved!' : 'Save Changes') }}
            </button>
        </div>
        <!-- Mobile Spacer -->
        <div class="md:hidden h-24"></div>
    </section>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: max(1rem, env(safe-area-inset-bottom));
}
</style>
