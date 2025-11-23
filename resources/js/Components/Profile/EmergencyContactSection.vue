<script setup>
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    emergencyContact: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    emergency_contact_name: props.emergencyContact?.emergency_contact_name || '',
    emergency_contact_relationship: props.emergencyContact?.emergency_contact_relationship || '',
    emergency_contact_phone: props.emergencyContact?.emergency_contact_phone || '',
    emergency_contact_email: props.emergencyContact?.emergency_contact_email || '',
    emergency_contact_address: props.emergencyContact?.emergency_contact_address || '',
});

const relationships = [
    'Father',
    'Mother',
    'Spouse',
    'Brother',
    'Sister',
    'Son',
    'Daughter',
    'Guardian',
    'Friend',
    'Colleague',
    'Other'
];

const submit = () => {
    form.post(route('profile.emergency-contact.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Emergency Contact Information</h3>
            <p class="mt-1 text-sm text-gray-600">
                Provide details of someone we can contact in case of emergency. This information may be shared with authorities or service providers when necessary.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Alert Notice -->
            <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-red-900">Important Information</h4>
                        <p class="mt-1 text-sm text-red-800">
                            This contact will be reached only in emergencies such as accidents, medical situations, or when you cannot be contacted directly.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Full Name -->
                <div>
                    <InputLabel for="emergency_contact_name" value="Full Name *" />
                    <TextInput
                        id="emergency_contact_name"
                        v-model="form.emergency_contact_name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Enter full name"
                        required
                    />
                    <p v-if="form.errors.emergency_contact_name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.emergency_contact_name }}
                    </p>
                </div>

                <!-- Relationship -->
                <div>
                    <InputLabel for="emergency_contact_relationship" value="Relationship *" />
                    <select
                        id="emergency_contact_relationship"
                        v-model="form.emergency_contact_relationship"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Select relationship</option>
                        <option v-for="rel in relationships" :key="rel" :value="rel.toLowerCase()">
                            {{ rel }}
                        </option>
                    </select>
                    <p v-if="form.errors.emergency_contact_relationship" class="mt-1 text-sm text-red-600">
                        {{ form.errors.emergency_contact_relationship }}
                    </p>
                </div>

                <!-- Phone Number -->
                <div>
                    <InputLabel for="emergency_contact_phone" value="Phone Number *" />
                    <TextInput
                        id="emergency_contact_phone"
                        v-model="form.emergency_contact_phone"
                        type="tel"
                        class="mt-1 block w-full"
                        placeholder="+880 1XXX-XXXXXX"
                        required
                    />
                    <p v-if="form.errors.emergency_contact_phone" class="mt-1 text-sm text-red-600">
                        {{ form.errors.emergency_contact_phone }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Include country code (e.g., +880 for Bangladesh)
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="emergency_contact_email" value="Email Address" />
                    <TextInput
                        id="emergency_contact_email"
                        v-model="form.emergency_contact_email"
                        type="email"
                        class="mt-1 block w-full"
                        placeholder="email@example.com"
                    />
                    <p v-if="form.errors.emergency_contact_email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.emergency_contact_email }}
                    </p>
                </div>
            </div>

            <!-- Address -->
            <div>
                <InputLabel for="emergency_contact_address" value="Address" />
                <textarea
                    id="emergency_contact_address"
                    v-model="form.emergency_contact_address"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Complete address including city and postal code"
                ></textarea>
                <p v-if="form.errors.emergency_contact_address" class="mt-1 text-sm text-red-600">
                    {{ form.errors.emergency_contact_address }}
                </p>
            </div>

            <!-- Tips Section -->
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-blue-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Best Practices
                </h4>
                <ul class="space-y-1 text-sm text-blue-800">
                    <li>• Choose someone who is usually available and can respond quickly</li>
                    <li>• Inform your emergency contact that you've listed them</li>
                    <li>• Ensure the phone number is always active and reachable</li>
                    <li>• Update this information if your emergency contact changes</li>
                    <li>• Consider choosing someone in your destination country if traveling</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    ✓ Emergency contact saved successfully!
                </p>
                <div class="ml-auto">
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Emergency Contact' }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
