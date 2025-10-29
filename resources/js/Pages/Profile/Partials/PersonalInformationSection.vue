<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios'; // Import axios

// Import UI Components
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

// Get props passed from Profile/Edit.vue
const props = defineProps({
    user: Object,       // Core user data (name, email) - For display only
    userProfile: Object // UserProfile data (phone, address, etc.) - For editing
});

// Use useForm for reactive state and error handling
const form = useForm({
    // Only UserProfile fields are needed for saving via API
    first_name: props.userProfile?.first_name || '',
    last_name: props.userProfile?.last_name || '',
    phone: props.userProfile?.phone || '',
    dob: props.userProfile?.dob || '',
    nationality: props.userProfile?.nationality || '',
    passport_number: props.userProfile?.passport_number || '',
    gender: props.userProfile?.gender || '',
    marital_status: props.userProfile?.marital_status || '',
    current_occupation: props.userProfile?.current_occupation || '',
    bio: props.userProfile?.bio || '',
    present_address_line: props.userProfile?.present_address_line || '',
    present_city: props.userProfile?.present_city || '',
    present_country: props.userProfile?.present_country || '',
    present_postal_code: props.userProfile?.present_postal_code || '',
    is_permanent_same_as_present: props.userProfile?.is_permanent_same_as_present || false,
    permanent_address_line: props.userProfile?.permanent_address_line || '',
    permanent_city: props.userProfile?.permanent_city || '',
    permanent_country: props.userProfile?.permanent_country || '',
    permanent_postal_code: props.userProfile?.permanent_postal_code || '',
    social_linkedin: props.userProfile?.social_linkedin || '',
    social_github: props.userProfile?.social_github || '',
    social_website: props.userProfile?.social_website || '',
    portfolio_link: props.userProfile?.portfolio_link || '',
    travel_purpose: props.userProfile?.travel_purpose || '',
    // Add other UserProfile fields here (funding, dependents, etc.)
});

// Processing and success state
const isProcessing = ref(false);
const recentlySuccessful = ref(false);
const successMessage = ref('');

// Submit function using Axios
const submit = async () => {
    isProcessing.value = true;
    form.clearErrors(); // Clear previous errors
    recentlySuccessful.value = false;
    successMessage.value = '';

    try {
        // Use axios.put to the new API endpoint
        const response = await axios.put(route('profile.details.update'), form.data());

        // Handle success
        successMessage.value = response.data.message || 'Profile details saved.';
        recentlySuccessful.value = true;
        setTimeout(() => recentlySuccessful.value = false, 2000); // Show success message briefly

    } catch (error) {
        // Handle validation errors (422)
        if (error.response && error.response.status === 422) {
            form.setError(error.response.data.errors);
        } else {
            // Handle other errors
            console.error("Error updating profile details:", error);
            form.setError('general', 'An unexpected error occurred. Please try again.'); // Generic error
        }
    } finally {
        isProcessing.value = false;
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Personal Information</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your contact details, address, and other personal information.
                </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">

            <div v-if="props.user" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                 <div>
                    <InputLabel value="Full Name (Account)" />
                    <p class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm p-2.5 bg-gray-100 cursor-not-allowed">
                        {{ props.user.name }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Account name/email can be changed in Account Settings.</p>
                 </div>
                 <div>
                    <InputLabel value="Email (Account)" />
                     <p class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm p-2.5 bg-gray-100 cursor-not-allowed">
                        {{ props.user.email }}
                    </p>
                 </div>
            </div>

            <hr class="my-4 border-gray-200 dark:border-gray-700">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Contact & Bio</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="first_name" value="First Name" />
                    <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" />
                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>
                <div>
                    <InputLabel for="last_name" value="Last Name" />
                    <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" />
                    <InputError class="mt-2" :message="form.errors.last_name" />
                </div>
                 <div>
                    <InputLabel for="phone" value="Phone Number" />
                    <TextInput id="phone" type="tel" class="mt-1 block w-full" v-model="form.phone" />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
                <div>
                    <InputLabel for="dob" value="Date of Birth" />
                    <TextInput id="dob" type="date" class="mt-1 block w-full" v-model="form.dob" />
                    <InputError class="mt-2" :message="form.errors.dob" />
                </div>
                <div>
                    <InputLabel for="gender" value="Gender" />
                    <TextInput id="gender" type="text" class="mt-1 block w-full" v-model="form.gender" />
                    <InputError class="mt-2" :message="form.errors.gender" />
                </div>
                 <div>
                    <InputLabel for="marital_status" value="Marital Status" />
                    <TextInput id="marital_status" type="text" class="mt-1 block w-full" v-model="form.marital_status" />
                    <InputError class="mt-2" :message="form.errors.marital_status" />
                </div>
                 <div>
                    <InputLabel for="nationality" value="Nationality" />
                    <TextInput id="nationality" type="text" class="mt-1 block w-full" v-model="form.nationality" />
                    <InputError class="mt-2" :message="form.errors.nationality" />
                </div>
                 <div>
                    <InputLabel for="passport_number" value="Passport Number" />
                    <TextInput id="passport_number" type="text" class="mt-1 block w-full" v-model="form.passport_number" />
                    <InputError class="mt-2" :message="form.errors.passport_number" />
                </div>
                 <div class="sm:col-span-2">
                    <InputLabel for="current_occupation" value="Current Occupation" />
                    <TextInput id="current_occupation" type="text" class="mt-1 block w-full" v-model="form.current_occupation" />
                    <InputError class="mt-2" :message="form.errors.current_occupation" />
                </div>
            </div>
             <div>
                <InputLabel for="bio" value="Professional Bio" />
                <TextareaInput id="bio" class="mt-1 block w-full" v-model="form.bio" rows="4" />
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <hr class="my-4 border-gray-200 dark:border-gray-700">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Address</h3>
             <fieldset class="space-y-4">
                <legend class="text-sm font-semibold">Present Address</legend>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <InputLabel for="present_address_line" value="Address Line" />
                        <TextInput id="present_address_line" type="text" class="mt-1 block w-full" v-model="form.present_address_line" />
                        <InputError class="mt-2" :message="form.errors.present_address_line" />
                    </div>
                    <div>
                        <InputLabel for="present_city" value="City" />
                        <TextInput id="present_city" type="text" class="mt-1 block w-full" v-model="form.present_city" />
                         <InputError class="mt-2" :message="form.errors.present_city" />
                    </div>
                     <div>
                        <InputLabel for="present_country" value="Country" />
                        <TextInput id="present_country" type="text" class="mt-1 block w-full" v-model="form.present_country" />
                         <InputError class="mt-2" :message="form.errors.present_country" />
                    </div>
                      <div>
                        <InputLabel for="present_postal_code" value="Postal Code" />
                        <TextInput id="present_postal_code" type="text" class="mt-1 block w-full" v-model="form.present_postal_code" />
                         <InputError class="mt-2" :message="form.errors.present_postal_code" />
                    </div>
                </div>
            </fieldset>

             <fieldset class="space-y-4">
                <legend class="text-sm font-semibold">Permanent Address</legend>
                 <div class="flex items-center">
                    <Checkbox id="is_permanent_same_as_present" v-model="form.is_permanent_same_as_present" :checked="form.is_permanent_same_as_present" />
                    <InputLabel for="is_permanent_same_as_present" value="Same as present address" class="ml-2" />
                </div>

                <div v-if="!form.is_permanent_same_as_present" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                     <div class="sm:col-span-2">
                        <InputLabel for="permanent_address_line" value="Address Line" />
                        <TextInput id="permanent_address_line" type="text" class="mt-1 block w-full" v-model="form.permanent_address_line" />
                         <InputError class="mt-2" :message="form.errors.permanent_address_line" />
                    </div>
                    <div>
                        <InputLabel for="permanent_city" value="City" />
                        <TextInput id="permanent_city" type="text" class="mt-1 block w-full" v-model="form.permanent_city" />
                         <InputError class="mt-2" :message="form.errors.permanent_city" />
                    </div>
                     <div>
                        <InputLabel for="permanent_country" value="Country" />
                        <TextInput id="permanent_country" type="text" class="mt-1 block w-full" v-model="form.permanent_country" />
                         <InputError class="mt-2" :message="form.errors.permanent_country" />
                    </div>
                     <div>
                        <InputLabel for="permanent_postal_code" value="Postal Code" />
                        <TextInput id="permanent_postal_code" type="text" class="mt-1 block w-full" v-model="form.permanent_postal_code" />
                         <InputError class="mt-2" :message="form.errors.permanent_postal_code" />
                    </div>
                </div>
            </fieldset>

            <hr class="my-4 border-gray-200 dark:border-gray-700">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Links</h3>
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                 <div>
                    <InputLabel for="social_linkedin" value="LinkedIn URL" />
                    <TextInput id="social_linkedin" type="url" class="mt-1 block w-full" v-model="form.social_linkedin" placeholder="https://linkedin.com/in/..." />
                     <InputError class="mt-2" :message="form.errors.social_linkedin" />
                </div>
                <div>
                    <InputLabel for="social_github" value="GitHub URL" />
                    <TextInput id="social_github" type="url" class="mt-1 block w-full" v-model="form.social_github" placeholder="https://github.com/..." />
                     <InputError class="mt-2" :message="form.errors.social_github" />
                </div>
                 <div>
                    <InputLabel for="social_website" value="Website URL" />
                    <TextInput id="social_website" type="url" class="mt-1 block w-full" v-model="form.social_website" placeholder="https://..." />
                     <InputError class="mt-2" :message="form.errors.social_website" />
                </div>
                <div>
                    <InputLabel for="portfolio_link" value="Portfolio URL" />
                    <TextInput id="portfolio_link" type="url" class="mt-1 block w-full" v-model="form.portfolio_link" placeholder="https://..." />
                     <InputError class="mt-2" :message="form.errors.portfolio_link" />
                </div>
            </div>

            <hr class="my-4 border-gray-200 dark:border-gray-700">
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Travel & Financial Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="travel_purpose" value="Primary Travel Purpose" />
                    <TextInput id="travel_purpose" type="text" class="mt-1 block w-full" v-model="form.travel_purpose" />
                    <InputError class="mt-2" :message="form.errors.travel_purpose" />
                </div>
                </div>

             <InputError class="mt-2" :message="form.errors.general" />

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="isProcessing">Save Personal Info</PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="recentlySuccessful" class="text-sm text-green-600 dark:text-green-400">
                        {{ successMessage }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>