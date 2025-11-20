<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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

// Passport-standard name fields
const form = useForm({
    first_name: props.userProfile?.first_name || '',
    middle_name: props.userProfile?.middle_name || '',
    last_name: props.userProfile?.last_name || '',
    name_as_per_passport: props.userProfile?.name_as_per_passport || '',
    email: user.email,
});

// Auto-generate passport name format when typing
const autoGeneratePassportName = () => {
    const parts = [form.first_name, form.middle_name, form.last_name].filter(p => p && p.trim());
    form.name_as_per_passport = parts.join(' ').toUpperCase();
};

const submit = () => {
    saveError.value = '';
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            saveError.value = '';
        },
        onError: (errors) => {
            saveError.value = 'Failed to update profile information. Please check the form and try again.';
            console.error('Save error:', errors);
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address. Enter your name exactly as it appears on your passport.
            </p>
        </header>

        <form
            @submit.prevent="submit"
            class="mt-6 space-y-6"
        >
            <!-- Passport Standard Name Fields -->
            <div class="space-y-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
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
                            class="mt-1 block w-full uppercase"
                            style="font-size: 16px"
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
                            class="mt-1 block w-full uppercase"
                            style="font-size: 16px"
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
                            class="mt-1 block w-full uppercase"
                            style="font-size: 16px"
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
                        class="mt-1 block w-full uppercase font-medium bg-gray-100"
                        style="font-size: 16px"
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
                    class="mt-1 block w-full"
                    style="font-size: 16px"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" style="min-height: 44px">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
