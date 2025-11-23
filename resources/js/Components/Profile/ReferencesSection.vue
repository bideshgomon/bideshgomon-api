<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    references: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    references: props.references?.references || []
});

const referenceTypes = [
    { value: 'professional', label: 'Professional Reference', description: 'Supervisor, manager, or colleague' },
    { value: 'academic', label: 'Academic Reference', description: 'Professor, teacher, or advisor' },
    { value: 'personal', label: 'Personal Reference', description: 'Character reference from non-relative' },
];

const relationships = [
    'Current Supervisor',
    'Former Supervisor',
    'Manager',
    'Colleague',
    'Professor',
    'Teacher',
    'Academic Advisor',
    'Mentor',
    'Business Partner',
    'Client',
    'Friend',
    'Community Leader',
    'Other'
];

const addReference = () => {
    form.references.push({
        id: Date.now(),
        type: 'professional',
        name: '',
        relationship: '',
        organization: '',
        position: '',
        email: '',
        phone: '',
        address: '',
        years_known: '',
        can_contact: true
    });
};

const removeReference = (index) => {
    form.references.splice(index, 1);
};

const submit = () => {
    form.post(route('profile.references.update'), {
        preserveScroll: true,
    });
};

const getReferenceTypeInfo = (type) => {
    return referenceTypes.find(t => t.value === type) || referenceTypes[0];
};

const professionalReferences = computed(() => 
    form.references.filter(r => r.type === 'professional')
);

const academicReferences = computed(() => 
    form.references.filter(r => r.type === 'academic')
);

const personalReferences = computed(() => 
    form.references.filter(r => r.type === 'personal')
);
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">References</h3>
            <p class="mt-1 text-sm text-gray-600">
                Add references who can vouch for your character, skills, and work ethic. Typically required for visa applications and job opportunities.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Information Notice -->
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Reference Guidelines</h4>
                        <ul class="mt-2 text-sm text-blue-800 space-y-1">
                            <li>• Always ask permission before listing someone as a reference</li>
                            <li>• Professional references should be supervisors or colleagues</li>
                            <li>• Academic references should be professors or advisors</li>
                            <li>• Avoid family members - use professional contacts instead</li>
                            <li>• Provide accurate contact information</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Reference Statistics -->
            <div v-if="form.references.length > 0" class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="text-sm text-gray-600">Professional</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-600">
                        {{ professionalReferences.length }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="text-sm text-gray-600">Academic</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-600">
                        {{ academicReferences.length }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="text-sm text-gray-600">Personal</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-600">
                        {{ personalReferences.length }}
                    </div>
                </div>
            </div>

            <!-- References List -->
            <div class="space-y-4">
                <div
                    v-for="(reference, index) in form.references"
                    :key="reference.id"
                    class="rounded-lg border border-gray-300 bg-white p-6 shadow-sm"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600">
                                {{ index + 1 }}
                            </span>
                            <h4 class="text-base font-medium text-gray-900">
                                Reference {{ index + 1 }}
                            </h4>
                        </div>
                        <button
                            type="button"
                            @click="removeReference(index)"
                            class="rounded-md p-2 text-red-600 hover:bg-red-50 transition-colors"
                        >
                            <TrashIcon class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Reference Type -->
                        <div>
                            <InputLabel :for="`type-${index}`" value="Reference Type *" />
                            <select
                                :id="`type-${index}`"
                                v-model="reference.type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option v-for="type in referenceTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ getReferenceTypeInfo(reference.type).description }}
                            </p>
                        </div>

                        <!-- Name and Relationship -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <InputLabel :for="`name-${index}`" value="Full Name *" />
                                <TextInput
                                    :id="`name-${index}`"
                                    v-model="reference.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., Dr. John Smith"
                                />
                            </div>

                            <div>
                                <InputLabel :for="`relationship-${index}`" value="Relationship *" />
                                <select
                                    :id="`relationship-${index}`"
                                    v-model="reference.relationship"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select relationship</option>
                                    <option v-for="rel in relationships" :key="rel" :value="rel">
                                        {{ rel }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Organization and Position -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <InputLabel :for="`organization-${index}`" value="Organization/Company *" />
                                <TextInput
                                    :id="`organization-${index}`"
                                    v-model="reference.organization"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., ABC Corporation"
                                />
                            </div>

                            <div>
                                <InputLabel :for="`position-${index}`" value="Position/Title *" />
                                <TextInput
                                    :id="`position-${index}`"
                                    v-model="reference.position"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., Senior Manager"
                                />
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <InputLabel :for="`email-${index}`" value="Email Address *" />
                                <TextInput
                                    :id="`email-${index}`"
                                    v-model="reference.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="reference@example.com"
                                />
                            </div>

                            <div>
                                <InputLabel :for="`phone-${index}`" value="Phone Number *" />
                                <TextInput
                                    :id="`phone-${index}`"
                                    v-model="reference.phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="+880 1XXX-XXXXXX"
                                />
                            </div>
                        </div>

                        <!-- Address and Years Known -->
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <InputLabel :for="`address-${index}`" value="Address (Optional)" />
                                <TextInput
                                    :id="`address-${index}`"
                                    v-model="reference.address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="City, Country"
                                />
                            </div>

                            <div>
                                <InputLabel :for="`years-${index}`" value="Years Known" />
                                <TextInput
                                    :id="`years-${index}`"
                                    v-model="reference.years_known"
                                    type="number"
                                    min="0"
                                    max="50"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 5"
                                />
                            </div>
                        </div>

                        <!-- Can Contact Checkbox -->
                        <div class="flex items-center">
                            <input
                                :id="`contact-${index}`"
                                v-model="reference.can_contact"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label :for="`contact-${index}`" class="ml-2 text-sm text-gray-700">
                                Permission granted to contact this reference
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Reference Button -->
            <div class="flex justify-center">
                <SecondaryButton type="button" @click="addReference" class="flex items-center gap-2">
                    <PlusIcon class="h-4 w-4" />
                    Add Another Reference
                </SecondaryButton>
            </div>

            <!-- Empty State -->
            <div v-if="form.references.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900">No references added</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Start by adding your first reference. Most applications require 2-3 references.
                </p>
                <div class="mt-6">
                    <PrimaryButton type="button" @click="addReference" class="flex items-center gap-2">
                        <PlusIcon class="h-4 w-4" />
                        Add Your First Reference
                    </PrimaryButton>
                </div>
            </div>

            <!-- Best Practices -->
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-amber-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    Reference Best Practices
                </h4>
                <ul class="space-y-1 text-sm text-amber-800">
                    <li>• Choose people who know your work well (at least 1-2 years)</li>
                    <li>• Mix professional and academic references for visa applications</li>
                    <li>• Inform references before submitting applications</li>
                    <li>• Provide them with context about the opportunity you're applying for</li>
                    <li>• Keep reference contact information up to date</li>
                    <li>• Thank your references after they've been contacted</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div v-if="form.references.length > 0" class="flex items-center justify-between border-t border-gray-200 pt-6">
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    ✓ References saved successfully!
                </p>
                <div class="ml-auto">
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save References' }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
