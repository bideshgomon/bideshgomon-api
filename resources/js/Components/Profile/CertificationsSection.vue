<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { PlusIcon, TrashIcon, CheckBadgeIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    certifications: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    certifications: props.certifications?.certifications || []
});

const certificationTypes = [
    { value: 'professional', label: 'Professional Certification', icon: '🎓', description: 'Industry certifications (PMP, CPA, etc.)' },
    { value: 'trade', label: 'Trade License', icon: '📋', description: 'Business or trade licenses' },
    { value: 'driving', label: 'Driving License', icon: '🚗', description: 'Vehicle operation permits' },
    { value: 'technical', label: 'Technical Certification', icon: '💻', description: 'IT, software, technical skills' },
    { value: 'language', label: 'Language Proficiency', icon: '🗣️', description: 'IELTS, TOEFL, etc.' },
    { value: 'other', label: 'Other License/Permit', icon: '📜', description: 'Any other certification' },
];

const addCertification = () => {
    form.certifications.push({
        id: Date.now(),
        type: 'professional',
        name: '',
        issuing_organization: '',
        issue_date: '',
        expiry_date: '',
        credential_id: '',
        credential_url: '',
        never_expires: false
    });
};

const removeCertification = (index) => {
    form.certifications.splice(index, 1);
};

const submit = () => {
    form.post(route('profile.certifications.update'), {
        preserveScroll: true,
    });
};

const getTypeInfo = (type) => {
    return certificationTypes.find(t => t.value === type) || certificationTypes[0];
};

const isExpired = (cert) => {
    if (cert.never_expires || !cert.expiry_date) return false;
    return new Date(cert.expiry_date) < new Date();
};

const isExpiringSoon = (cert) => {
    if (cert.never_expires || !cert.expiry_date) return false;
    const thirtyDaysFromNow = new Date();
    thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
    const expiryDate = new Date(cert.expiry_date);
    return expiryDate > new Date() && expiryDate <= thirtyDaysFromNow;
};

const certificationsByType = computed(() => {
    return certificationTypes.map(type => ({
        ...type,
        count: form.certifications.filter(c => c.type === type.value).length
    }));
});

const expiredCount = computed(() => 
    form.certifications.filter(c => isExpired(c)).length
);

const expiringSoonCount = computed(() => 
    form.certifications.filter(c => isExpiringSoon(c)).length
);
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Certifications & Licenses</h3>
            <p class="mt-1 text-sm text-gray-600">
                Manage your professional certifications, trade licenses, driving permits, and other credentials. Keep track of expiry dates to stay compliant.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Alert Notifications -->
            <div v-if="expiredCount > 0 || expiringSoonCount > 0" class="space-y-3">
                <div v-if="expiredCount > 0" class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-900">{{ expiredCount }} Expired Certification{{ expiredCount > 1 ? 's' : '' }}</h4>
                            <p class="mt-1 text-sm text-red-800">Please renew your expired certifications to maintain compliance.</p>
                        </div>
                    </div>
                </div>

                <div v-if="expiringSoonCount > 0" class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-amber-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-amber-900">{{ expiringSoonCount }} Expiring Soon</h4>
                            <p class="mt-1 text-sm text-amber-800">{{ expiringSoonCount }} certification{{ expiringSoonCount > 1 ? 's' : '' }} will expire within 30 days.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certification Statistics -->
            <div v-if="form.certifications.length > 0" class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
                <div
                    v-for="type in certificationsByType"
                    :key="type.value"
                    class="rounded-lg border border-gray-200 bg-white p-4"
                >
                    <div class="text-2xl mb-1">{{ type.icon }}</div>
                    <div class="text-xs text-gray-600">{{ type.label }}</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-600">
                        {{ type.count }}
                    </div>
                </div>
            </div>

            <!-- Certifications List -->
            <div class="space-y-4">
                <div
                    v-for="(cert, index) in form.certifications"
                    :key="cert.id"
                    class="rounded-lg border bg-white p-6 shadow-sm"
                    :class="{
                        'border-red-300 bg-red-50': isExpired(cert),
                        'border-amber-300 bg-amber-50': isExpiringSoon(cert),
                        'border-gray-300': !isExpired(cert) && !isExpiringSoon(cert)
                    }"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-xl">
                                {{ getTypeInfo(cert.type).icon }}
                            </span>
                            <div>
                                <h4 class="text-base font-medium text-gray-900">
                                    Certification {{ index + 1 }}
                                </h4>
                                <p v-if="isExpired(cert)" class="text-xs font-semibold text-red-600">
                                    ⚠️ EXPIRED
                                </p>
                                <p v-else-if="isExpiringSoon(cert)" class="text-xs font-semibold text-amber-600">
                                    ⏰ EXPIRING SOON
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="removeCertification(index)"
                            class="rounded-md p-2 text-red-600 hover:bg-red-50 transition-colors"
                        >
                            <TrashIcon class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Certification Type -->
                        <div>
                            <InputLabel :for="`type-${index}`" value="Certification Type *" />
                            <select
                                :id="`type-${index}`"
                                v-model="cert.type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option v-for="type in certificationTypes" :key="type.value" :value="type.value">
                                    {{ type.icon }} {{ type.label }}
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ getTypeInfo(cert.type).description }}
                            </p>
                        </div>

                        <!-- Name and Organization -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <InputLabel :for="`name-${index}`" value="Certification Name *" />
                                <TextInput
                                    :id="`name-${index}`"
                                    v-model="cert.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., PMP, AWS Certified, IELTS"
                                />
                            </div>

                            <div>
                                <InputLabel :for="`org-${index}`" value="Issuing Organization *" />
                                <TextInput
                                    :id="`org-${index}`"
                                    v-model="cert.issuing_organization"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., PMI, AWS, British Council"
                                />
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <InputLabel :for="`issue-${index}`" value="Issue Date *" />
                                <TextInput
                                    :id="`issue-${index}`"
                                    v-model="cert.issue_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel :for="`expiry-${index}`" value="Expiry Date" />
                                <TextInput
                                    :id="`expiry-${index}`"
                                    v-model="cert.expiry_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :disabled="cert.never_expires"
                                />
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        :id="`never-${index}`"
                                        v-model="cert.never_expires"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        @change="cert.never_expires ? cert.expiry_date = '' : null"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Never expires</span>
                                </label>
                            </div>
                        </div>

                        <!-- Credential Details -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <InputLabel :for="`credential-${index}`" value="Credential ID/Number" />
                                <TextInput
                                    :id="`credential-${index}`"
                                    v-model="cert.credential_id"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Certificate or license number"
                                />
                            </div>

                            <div>
                                <InputLabel :for="`url-${index}`" value="Credential URL" />
                                <TextInput
                                    :id="`url-${index}`"
                                    v-model="cert.credential_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    placeholder="https://verify.example.com/..."
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Link to verify or view the credential online
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Certification Button -->
            <div class="flex justify-center">
                <SecondaryButton type="button" @click="addCertification" class="flex items-center gap-2">
                    <PlusIcon class="h-4 w-4" />
                    Add Another Certification
                </SecondaryButton>
            </div>

            <!-- Empty State -->
            <div v-if="form.certifications.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <CheckBadgeIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-4 text-sm font-medium text-gray-900">No certifications added</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Add your professional certifications, licenses, and credentials to strengthen your profile.
                </p>
                <div class="mt-6">
                    <PrimaryButton type="button" @click="addCertification" class="flex items-center gap-2">
                        <PlusIcon class="h-4 w-4" />
                        Add Your First Certification
                    </PrimaryButton>
                </div>
            </div>

            <!-- Information Notice -->
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Certification Guidelines</h4>
                        <ul class="mt-2 text-sm text-blue-800 space-y-1">
                            <li>• Include all relevant professional and technical certifications</li>
                            <li>• Keep expiry dates updated for time-sensitive credentials</li>
                            <li>• Trade licenses may be required for visa applications</li>
                            <li>• Language proficiency certificates are valuable for study/work abroad</li>
                            <li>• Add verification URLs when available for authenticity</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Common Certifications Examples -->
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <h4 class="mb-3 text-sm font-medium text-gray-900">Popular Certifications by Category</h4>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">🎓 Professional</h5>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• PMP (Project Management)</li>
                            <li>• CPA (Accounting)</li>
                            <li>• CFA (Financial Analyst)</li>
                            <li>• Six Sigma Certifications</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">💻 Technical</h5>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• AWS/Azure/GCP Certified</li>
                            <li>• CCNA/CCNP (Networking)</li>
                            <li>• CompTIA A+/Network+</li>
                            <li>• CISSP (Cybersecurity)</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">🗣️ Language</h5>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• IELTS (English)</li>
                            <li>• TOEFL (English)</li>
                            <li>• HSK (Chinese)</li>
                            <li>• DELE (Spanish)</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">📋 Trade</h5>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Business Trade License</li>
                            <li>• Import/Export License</li>
                            <li>• Professional Practice License</li>
                            <li>• Industry-specific Permits</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">🚗 Driving</h5>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Driving License (Car)</li>
                            <li>• Motorcycle License</li>
                            <li>• Commercial Vehicle License</li>
                            <li>• International Driving Permit</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div v-if="form.certifications.length > 0" class="flex items-center justify-between border-t border-gray-200 pt-6">
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    ✓ Certifications saved successfully!
                </p>
                <div class="ml-auto">
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Certifications' }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
