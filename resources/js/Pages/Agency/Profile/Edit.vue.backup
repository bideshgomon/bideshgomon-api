<template>
    <Head title="Edit Agency Profile" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Edit Agency Profile</h2>
                            <Link :href="route('agency.profile.show')" class="text-indigo-600 hover:text-indigo-900">
                                Back to Profile
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Business Information -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Agency Type</label>
                                        <select v-model="form.agency_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Agency Type</option>
                                            <option v-for="type in agencyTypes" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.agency_type_id" class="mt-1 text-sm text-red-600">{{ form.errors.agency_type_id }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Established Year</label>
                                        <input v-model="form.established_year" type="number" min="1900" :max="new Date().getFullYear()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.established_year" class="mt-1 text-sm text-red-600">{{ form.errors.established_year }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">License Number</label>
                                        <input v-model="form.license_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.license_number" class="mt-1 text-sm text-red-600">{{ form.errors.license_number }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">License Expiry Date</label>
                                        <input v-model="form.license_expiry" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.license_expiry" class="mt-1 text-sm text-red-600">{{ form.errors.license_expiry }}</p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea v-model="form.description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Logo Upload -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agency Logo</h3>
                                <div class="flex items-center space-x-6">
                                    <div v-if="agency.logo" class="flex-shrink-0">
                                        <img :src="`/storage/${agency.logo}`" alt="Current logo" class="h-24 w-24 object-cover rounded-lg" />
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" @change="handleLogoUpload" accept="image/jpeg,image/png,image/jpg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                        <p class="mt-1 text-xs text-gray-500">JPG, PNG. Max 2MB.</p>
                                        <p v-if="logoUploading" class="mt-1 text-sm text-indigo-600">Uploading...</p>
                                        <p v-if="logoError" class="mt-1 text-sm text-red-600">{{ logoError }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                                        <input v-model="form.whatsapp" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.whatsapp" class="mt-1 text-sm text-red-600">{{ form.errors.whatsapp }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Website</label>
                                        <input v-model="form.website" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.website" class="mt-1 text-sm text-red-600">{{ form.errors.website }}</p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Address</label>
                                        <textarea v-model="form.address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                        <input v-model="form.facebook_url" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                                        <input v-model="form.linkedin_url" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Twitter URL</label>
                                        <input v-model="form.twitter_url" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                        <input v-model="form.instagram_url" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </div>
                                </div>
                            </div>

                            <!-- Services Offered -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Services Offered</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="service in availableServices" :key="service" class="flex items-center space-x-3">
                                        <input type="checkbox" :value="service" v-model="form.services_offered" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <span class="text-sm text-gray-700">{{ formatServiceName(service) }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.services_offered" class="mt-2 text-sm text-red-600">{{ form.errors.services_offered }}</p>
                            </div>

                            <!-- Countries Expertise -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Countries Expertise</h3>
                                <select v-model="form.countries_expertise" multiple size="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple countries</p>
                                <p v-if="form.errors.countries_expertise" class="mt-1 text-sm text-red-600">{{ form.errors.countries_expertise }}</p>
                            </div>

                            <!-- Languages Spoken -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Languages Spoken</h3>
                                <select v-model="form.languages_spoken" multiple size="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option v-for="language in languages" :key="language" :value="language">{{ language }}</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple languages</p>
                                <p v-if="form.errors.languages_spoken" class="mt-1 text-sm text-red-600">{{ form.errors.languages_spoken }}</p>
                            </div>

                            <!-- Team Information -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Team Size</label>
                                        <input v-model.number="form.team_size" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.team_size" class="mt-1 text-sm text-red-600">{{ form.errors.team_size }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Office Hours</label>
                                        <input v-model="form.office_hours" type="text" placeholder="e.g., Mon-Fri 9AM-6PM" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.office_hours" class="mt-1 text-sm text-red-600">{{ form.errors.office_hours }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Office Images -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Office Images</h3>
                                <div v-if="agency.office_images && agency.office_images.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <div v-for="(image, index) in agency.office_images" :key="index" class="relative group">
                                        <img :src="`/storage/${image}`" alt="Office image" class="h-24 w-full object-cover rounded-lg" />
                                        <button type="button" @click="deleteOfficeImage(index)" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <XMarkIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <input type="file" @change="handleOfficeImagesUpload" accept="image/jpeg,image/png,image/jpg" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    <p class="mt-1 text-xs text-gray-500">JPG, PNG. Max 10 images, 5MB each.</p>
                                    <p v-if="imagesUploading" class="mt-1 text-sm text-indigo-600">Uploading...</p>
                                    <p v-if="imagesError" class="mt-1 text-sm text-red-600">{{ imagesError }}</p>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Clients</label>
                                        <input v-model.number="form.total_clients" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.total_clients" class="mt-1 text-sm text-red-600">{{ form.errors.total_clients }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Successful Applications</label>
                                        <input v-model.number="form.successful_applications" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <p v-if="form.errors.successful_applications" class="mt-1 text-sm text-red-600">{{ form.errors.successful_applications }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Success Rate (%)</label>
                                        <input :value="successRate" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" />
                                        <p class="mt-1 text-xs text-gray-500">Calculated automatically</p>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO & Marketing</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea v-model="form.meta_description" rows="2" maxlength="160" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        <p class="mt-1 text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160 characters</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-4">
                                <Link :href="route('agency.profile.show')" class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Cancel
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    agency: Object,
    countries: Array,
    languages: Array,
    agencyTypes: Array,
});

const availableServices = [
    'visa_application',
    'work_permit',
    'student_visa',
    'permanent_residence',
    'citizenship',
    'family_sponsorship',
    'business_immigration',
    'refugee_claims',
    'document_translation',
    'job_placement',
    'accommodation',
    'travel_insurance'
];

const form = useForm({
    agency_type_id: props.agency.agency_type_id || '',
    business_type: props.agency.business_type || '', // Keep for backward compatibility
    established_year: props.agency.established_year || null,
    license_number: props.agency.license_number || '',
    license_expiry: props.agency.license_expiry || '',
    description: props.agency.description || '',
    whatsapp: props.agency.whatsapp || '',
    website: props.agency.website || '',
    address: props.agency.address || '',
    facebook_url: props.agency.facebook_url || '',
    linkedin_url: props.agency.linkedin_url || '',
    twitter_url: props.agency.twitter_url || '',
    instagram_url: props.agency.instagram_url || '',
    services_offered: props.agency.services_offered || [],
    countries_expertise: props.agency.countries_expertise || [],
    languages_spoken: props.agency.languages_spoken || [],
    team_size: props.agency.team_size || null,
    office_hours: props.agency.office_hours || '',
    total_clients: props.agency.total_clients || 0,
    successful_applications: props.agency.successful_applications || 0,
    meta_description: props.agency.meta_description || '',
});

const logoUploading = ref(false);
const logoError = ref('');
const imagesUploading = ref(false);
const imagesError = ref('');

const successRate = computed(() => {
    if (form.total_clients > 0 && form.successful_applications > 0) {
        return ((form.successful_applications / form.total_clients) * 100).toFixed(1);
    }
    return '0.0';
});

const formatServiceName = (service) => {
    return service.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const handleLogoUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        logoError.value = 'File size must be less than 2MB';
        return;
    }

    logoUploading.value = true;
    logoError.value = '';

    const formData = new FormData();
    formData.append('logo', file);

    try {
        await router.post(route('agency.profile.uploadLogo'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                logoUploading.value = false;
            },
            onError: (errors) => {
                logoUploading.value = false;
                logoError.value = errors.logo || 'Upload failed';
            }
        });
    } catch (error) {
        logoUploading.value = false;
        logoError.value = 'Upload failed';
    }
};

const handleOfficeImagesUpload = async (event) => {
    const files = Array.from(event.target.files);
    if (files.length === 0) return;

    if (files.length > 10) {
        imagesError.value = 'You can only upload up to 10 images';
        return;
    }

    const oversizedFiles = files.filter(file => file.size > 5 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        imagesError.value = 'Each file must be less than 5MB';
        return;
    }

    imagesUploading.value = true;
    imagesError.value = '';

    const formData = new FormData();
    files.forEach(file => {
        formData.append('images[]', file);
    });

    try {
        await router.post(route('agency.profile.uploadOfficeImages'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                imagesUploading.value = false;
                event.target.value = '';
            },
            onError: (errors) => {
                imagesUploading.value = false;
                imagesError.value = errors['images.0'] || 'Upload failed';
            }
        });
    } catch (error) {
        imagesUploading.value = false;
        imagesError.value = 'Upload failed';
    }
};

const deleteOfficeImage = async (index) => {
    if (!confirm('Are you sure you want to delete this image?')) return;

    try {
        await router.delete(route('agency.profile.deleteOfficeImage'), {
            data: { index },
            preserveScroll: true,
        });
    } catch (error) {
        alert('Failed to delete image');
    }
};

const submit = () => {
    form.put(route('agency.profile.update'), {
        preserveScroll: true,
    });
};
</script>
