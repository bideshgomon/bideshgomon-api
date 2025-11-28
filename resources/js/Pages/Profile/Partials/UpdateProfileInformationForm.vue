<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    userProfile: {
        type: Object,
        default: () => ({}),
    },
});

const user = usePage().props.auth.user;
const saveError = ref('');

// Debug: Check what we're receiving
console.log('UpdateProfileInformationForm - userProfile prop:', props.userProfile);
console.log('UpdateProfileInformationForm - first_name:', props.userProfile?.first_name);
console.log('UpdateProfileInformationForm - last_name:', props.userProfile?.last_name);

// Passport-standard name fields
const form = useForm({
    first_name: props.userProfile?.first_name || '',
    middle_name: props.userProfile?.middle_name || '',
    last_name: props.userProfile?.last_name || '',
    name_as_per_passport: props.userProfile?.name_as_per_passport || '',
    email: user.email,
});

console.log('UpdateProfileInformationForm - form initialized:', {
    first_name: form.first_name,
    middle_name: form.middle_name,
    last_name: form.last_name,
    name_as_per_passport: form.name_as_per_passport
});

// Auto-generate passport name format when typing
const autoGeneratePassportName = () => {
    const parts = [form.first_name, form.middle_name, form.last_name].filter(p => p && p.trim());
    form.name_as_per_passport = parts.join(' ').toUpperCase();
};

// Hydrate form fields on mount if they are still empty but props have data (guards against race or cache issues)
onMounted(() => {
    if (!form.first_name && props.userProfile?.first_name) {
        form.first_name = props.userProfile.first_name || '';
        form.middle_name = props.userProfile.middle_name || '';
        form.last_name = props.userProfile.last_name || '';
        form.name_as_per_passport = props.userProfile.name_as_per_passport || '';
        console.log('Form hydrated from userProfile onMounted:', {
            first_name: form.first_name,
            middle_name: form.middle_name,
            last_name: form.last_name,
            name_as_per_passport: form.name_as_per_passport
        });
    }
});

const submit = () => {
    console.log('=== SUBMIT FUNCTION CALLED ===');
    console.log('Form data:', {
        first_name: form.first_name,
        middle_name: form.middle_name,
        last_name: form.last_name,
        name_as_per_passport: form.name_as_per_passport,
        email: form.email
    });
    console.log('Route URL:', route('profile.update'));
    console.log('Form processing:', form.processing);
    
    saveError.value = '';
    
    try {
        console.log('Calling form.patch...');
        const result = form.patch(route('profile.update'), {
            preserveScroll: true,
            onBefore: () => {
                console.log('onBefore callback triggered');
            },
            onStart: () => {
                console.log('onStart callback triggered');
            },
            onProgress: (progress) => {
                console.log('onProgress:', progress);
            },
            onSuccess: (page) => {
                console.log('onSuccess callback triggered', page);
                saveError.value = '';
            },
            onError: (errors) => {
                console.error('onError callback triggered', errors);
                saveError.value = 'Failed to update profile information. Please check the form and try again.';
            },
            onCancel: () => {
                console.log('onCancel callback triggered');
            },
            onFinish: () => {
                console.log('onFinish callback triggered');
            }
        });
        console.log('form.patch() returned:', result);
    } catch (error) {
        console.error('Exception in form.patch():', error);
    }
};
</script>

<template>
    <section>
        <header class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-ocean-500 to-sky-500 flex items-center justify-center shadow-rhythmic-md">
                    <UserCircleIcon class="h-6 w-6 text-white" />
                </div>
                <div>
                    <h2 class="font-display font-bold text-xl text-gray-800">
                        Profile Information
                    </h2>
                    <p class="text-xs text-gray-500">Update your account details</p>
                </div>
            </div>
        </header>

        <form @submit.prevent="submit" class="mt-4 md:mt-6">
            <RhythmicCard variant="light" class="space-y-rhythm-md">
                <!-- Passport Standard Name Fields -->
                <div class="space-y-4 p-3 md:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center text-sm text-blue-800">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Enter your name exactly as it appears on your passport</span>
                </div>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <InputLabel for="first_name" value="First Name *" />
                        <TextInput
                            id="first_name"
                            type="text"
                            class="mt-1 block w-full uppercase py-3 px-4 text-base rounded-lg touch-manipulation"
                            v-model="form.first_name"
                            @input="autoGeneratePassportName"
                            required
                            placeholder="FIRST"
                            autocomplete="given-name"
                        />
                        <InputError class="mt-2" :message="form.errors.first_name" />
                    </div>

                    <div>
                        <InputLabel for="middle_name" value="Middle Name (Optional)" />
                        <TextInput
                            id="middle_name"
                            type="text"
                            class="mt-1 block w-full uppercase py-3 px-4 text-base rounded-lg touch-manipulation"
                            v-model="form.middle_name"
                            @input="autoGeneratePassportName"
                            placeholder="MIDDLE"
                            autocomplete="additional-name"
                        />
                        <InputError class="mt-2" :message="form.errors.middle_name" />
                    </div>

                    <div>
                        <InputLabel for="last_name" value="Last Name *" />
                        <TextInput
                            id="last_name"
                            type="text"
                            class="mt-1 block w-full uppercase py-3 px-4 text-base rounded-lg touch-manipulation"
                            v-model="form.last_name"
                            @input="autoGeneratePassportName"
                            required
                            placeholder="LAST"
                            autocomplete="family-name"
                        />
                        <InputError class="mt-2" :message="form.errors.last_name" />
                    </div>
                </div>

                <div>
                    <InputLabel for="name_as_per_passport" value="Full Name (As Per Passport)" />
                    <TextInput
                        id="name_as_per_passport"
                        type="text"
                        class="mt-1 block w-full uppercase font-medium bg-gray-100 py-3 px-4 text-base rounded-lg"
                        v-model="form.name_as_per_passport"
                        readonly
                        placeholder="AUTO-GENERATED FROM ABOVE"
                    />
                    <p class="mt-1 text-xs text-gray-500">This will be auto-generated based on the fields above</p>
                    <InputError class="mt-2" :message="form.errors.name_as_per_passport" />
                </div>
            </div>

                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="mt-2 text-sm md:text-base text-gray-800">
                        Your email address is unverified.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="rounded-md text-sm md:text-base text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 text-sm md:text-base font-medium text-green-600"
                    >
                        A new verification link has been sent to your email address.
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
            </RhythmicCard>

            <!-- Mobile Sticky Save Button -->
            <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-2xl z-30 safe-area-bottom">
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-200 active:scale-98 touch-manipulation flex items-center justify-center gap-2"
                    type="button"
                >
                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ form.processing ? 'Saving...' : (form.recentlySuccessful ? '✓ Saved!' : 'Save Changes') }}</span>
                </button>
            </div>

            <!-- Mobile Bottom Spacer (prevents content from being hidden behind sticky button) -->
            <div class="md:hidden h-24"></div>
        </form>
    </section>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: max(1rem, env(safe-area-inset-bottom));
}

.active\:scale-98:active {
    transform: scale(0.98);
}
</style>
