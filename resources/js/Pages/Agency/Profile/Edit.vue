<template>
    <Head title="Edit Agency Profile" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Agency Profile</h1>
                    <p class="mt-2 text-sm text-gray-600">Update your agency information to attract more clients</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Business Information -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <BuildingOfficeIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Business Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                                <input 
                                    v-model="form.company_name" 
                                    type="text" 
                                    required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                                <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-600">{{ form.errors.company_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                                <input 
                                    v-model="form.registration_number" 
                                    type="text" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Established Year</label>
                                <input 
                                    v-model.number="form.established_year" 
                                    type="number" 
                                    :max="currentYear"
                                    min="1900"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">License Number</label>
                                <input 
                                    v-model="form.license_number" 
                                    type="text" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">License Expiry Date</label>
                                <input 
                                    v-model="form.license_expiry" 
                                    type="date" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <textarea 
                                    v-model="form.description" 
                                    rows="4" 
                                    required
                                    placeholder="Describe your agency's services and expertise..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Agency Type Selection -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <TagIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Agency Type *
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Select the category that best describes your agency's primary services</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            <div 
                                v-for="type in agencyTypes" 
                                :key="type.id"
                                @click="selectAgencyType(type.id)"
                                class="relative cursor-pointer rounded-lg border-2 p-4 hover:shadow-md transition-all"
                                :class="form.agency_type_id === type.id 
                                    ? `border-${type.color}-500 bg-${type.color}-50 ring-2 ring-${type.color}-500` 
                                    : 'border-gray-200 hover:border-gray-300'"
                            >
                                <div class="flex items-start space-x-3">
                                    <div 
                                        class="flex-shrink-0 mt-0.5 w-10 h-10 rounded-lg flex items-center justify-center"
                                        :class="`bg-${type.color}-100`"
                                    >
                                        <component 
                                            :is="getHeroIcon(type.icon)" 
                                            :class="`h-6 w-6 text-${type.color}-600`"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ type.name }}</h4>
                                        <p class="text-xs text-gray-600 line-clamp-2">{{ type.description }}</p>
                                    </div>
                                    <CheckCircleIcon 
                                        v-if="form.agency_type_id === type.id"
                                        :class="`h-5 w-5 text-${type.color}-600 flex-shrink-0`"
                                    />
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.agency_type_id" class="mt-2 text-sm text-red-600">{{ form.errors.agency_type_id }}</p>
                    </div>

                    <!-- Services Offered -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <ClipboardDocumentListIcon class="h-5 w-5 mr-2 text-indigo-600" />
                                Services Offered
                            </h3>
                            <span class="text-sm text-gray-600">
                                {{ selectedServicesCount }} selected
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Select all services your agency provides to clients</p>

                        <div class="space-y-6">
                            <div v-for="(services, category) in serviceModules" :key="category" class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-900">{{ category }}</h4>
                                    <button 
                                        type="button"
                                        @click="toggleCategoryServices(category)"
                                        class="text-xs text-indigo-600 hover:text-indigo-700 font-medium"
                                    >
                                        {{ isCategorySelected(category) ? 'Deselect All' : 'Select All' }}
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <label 
                                        v-for="service in services" 
                                        :key="service.id"
                                        class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer"
                                    >
                                        <input 
                                            type="checkbox" 
                                            :value="service.id" 
                                            v-model="form.services_offered"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                        />
                                        <span class="text-sm text-gray-700">{{ service.name }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <PhotoIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Agency Logo
                        </h3>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                            <div v-if="agency.logo_path" class="flex-shrink-0">
                                <img :src="`/storage/${agency.logo_path}`" alt="Agency Logo" class="h-20 w-20 object-cover rounded-lg border-2 border-gray-200" />
                            </div>
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    @change="handleLogoUpload" 
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" 
                                />
                                <p class="mt-1 text-xs text-gray-500">JPG or PNG, max 2MB</p>
                                <p v-if="logoUploading" class="mt-1 text-sm text-indigo-600">Uploading...</p>
                                <p v-if="logoError" class="mt-1 text-sm text-red-600">{{ logoError }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <PhoneIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                <input 
                                    v-model="form.phone" 
                                    type="tel" 
                                    required
                                    placeholder="01712-345678"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                                <input 
                                    v-model="form.whatsapp" 
                                    type="tel" 
                                    placeholder="01712-345678"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input 
                                    v-model="form.email" 
                                    type="email" 
                                    required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                                <input 
                                    v-model="form.website" 
                                    type="url" 
                                    placeholder="https://example.com"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <textarea 
                                    v-model="form.address" 
                                    rows="2" 
                                    required
                                    placeholder="Street address, district, division"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input 
                                    v-model="form.city" 
                                    type="text" 
                                    required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input 
                                    v-model="form.postal_code" 
                                    type="text" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <GlobeAltIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Social Media Links
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                                <input 
                                    v-model="form.facebook_url" 
                                    type="url" 
                                    placeholder="https://facebook.com/yourpage"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                                <input 
                                    v-model="form.linkedin_url" 
                                    type="url" 
                                    placeholder="https://linkedin.com/company/yourcompany"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                                <input 
                                    v-model="form.twitter_url" 
                                    type="url" 
                                    placeholder="https://twitter.com/yourhandle"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                                <input 
                                    v-model="form.instagram_url" 
                                    type="url" 
                                    placeholder="https://instagram.com/yourhandle"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Countries Expertise -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <MapIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Countries Expertise
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            <label 
                                v-for="country in countries" 
                                :key="country"
                                class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer"
                            >
                                <input 
                                    type="checkbox" 
                                    :value="country" 
                                    v-model="form.countries_expertise"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                />
                                <span class="text-sm text-gray-700">{{ country }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Languages Spoken -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <LanguageIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Languages Spoken
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            <label 
                                v-for="language in languages" 
                                :key="language"
                                class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer"
                            >
                                <input 
                                    type="checkbox" 
                                    :value="language" 
                                    v-model="form.languages_spoken"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                />
                                <span class="text-sm text-gray-700">{{ language }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Team Information -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <UserGroupIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Team Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Team Size</label>
                                <input 
                                    v-model.number="form.team_size" 
                                    type="number" 
                                    min="1"
                                    placeholder="Number of team members"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Office Hours</label>
                                <input 
                                    v-model="form.office_hours" 
                                    type="text" 
                                    placeholder="e.g., Mon-Fri 9AM-6PM"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Office Images -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <CameraIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Office Images
                        </h3>
                        <div v-if="agency.office_images && agency.office_images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 mb-4">
                            <div v-for="(image, index) in agency.office_images" :key="index" class="relative group">
                                <img :src="`/storage/${image}`" alt="Office image" class="h-24 w-full object-cover rounded-lg border-2 border-gray-200" />
                                <button 
                                    type="button" 
                                    @click="deleteOfficeImage(image)" 
                                    class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                                >
                                    <XMarkIcon class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                        <div>
                            <input 
                                type="file" 
                                @change="handleOfficeImagesUpload" 
                                accept="image/jpeg,image/png,image/jpg" 
                                multiple
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" 
                            />
                            <p class="mt-1 text-xs text-gray-500">JPG or PNG, max 10 images, 5MB each</p>
                            <p v-if="imagesUploading" class="mt-1 text-sm text-indigo-600">Uploading...</p>
                            <p v-if="imagesError" class="mt-1 text-sm text-red-600">{{ imagesError }}</p>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <ChartBarIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            Statistics
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Clients</label>
                                <input 
                                    v-model.number="form.total_clients" 
                                    type="number" 
                                    min="0"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Successful Applications</label>
                                <input 
                                    v-model.number="form.successful_applications" 
                                    type="number" 
                                    min="0"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Success Rate (%)</label>
                                <input 
                                    :value="successRate" 
                                    readonly
                                    class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" 
                                />
                                <p class="mt-1 text-xs text-gray-500">Calculated automatically</p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <MagnifyingGlassIcon class="h-5 w-5 mr-2 text-indigo-600" />
                            SEO & Marketing
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea 
                                v-model="form.meta_description" 
                                rows="2" 
                                maxlength="160"
                                placeholder="Brief description for search engines (max 160 characters)"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160 characters</p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                        <Link 
                            :href="route('agency.profile.show')" 
                            class="px-6 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 text-center"
                        >
                            Cancel
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BuildingOfficeIcon,
    TagIcon,
    ClipboardDocumentListIcon,
    PhotoIcon,
    PhoneIcon,
    GlobeAltIcon,
    MapIcon,
    LanguageIcon,
    UserGroupIcon,
    CameraIcon,
    ChartBarIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import {
    UsersIcon,
    AcademicCapIcon,
    GlobeAltIcon as GlobeAltSolidIcon,
    FolderOpenIcon,
    WrenchScrewdriverIcon,
    DocumentCheckIcon,
    HeartIcon,
    DocumentTextIcon,
    ShieldCheckIcon,
    TruckIcon,
    HomeIcon,
    ChartBarIcon as ChartBarSolidIcon,
    DocumentDuplicateIcon,
    BriefcaseIcon,
    FingerPrintIcon,
    UserGroupIcon as UserGroupSolidIcon,
} from '@heroicons/vue/24/solid';

const props = defineProps({
    agency: Object,
    countries: Array,
    languages: Array,
    agencyTypes: Array,
    serviceModules: Object,
});

const currentYear = new Date().getFullYear();

const form = useForm({
    company_name: props.agency.company_name || '',
    registration_number: props.agency.registration_number || '',
    agency_type_id: props.agency.agency_type_id || '',
    established_year: props.agency.established_year || null,
    license_number: props.agency.license_number || '',
    license_expiry: props.agency.license_expiry || '',
    description: props.agency.description || '',
    phone: props.agency.phone || '',
    whatsapp: props.agency.whatsapp || '',
    email: props.agency.email || '',
    website: props.agency.website || '',
    address: props.agency.address || '',
    city: props.agency.city || '',
    postal_code: props.agency.postal_code || '',
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

const selectedServicesCount = computed(() => {
    return form.services_offered.length;
});

const selectAgencyType = (typeId) => {
    form.agency_type_id = typeId;
    
    // Auto-suggest services based on agency type
    const selectedType = props.agencyTypes.find(t => t.id === typeId);
    if (selectedType && selectedType.allowed_service_modules) {
        const allowedServices = JSON.parse(selectedType.allowed_service_modules);
        // Suggest but don't force - user can still customize
        form.services_offered = allowedServices;
    }
};

const getHeroIcon = (iconName) => {
    const icons = {
        'users': UsersIcon,
        'academic-cap': AcademicCapIcon,
        'language': LanguageIcon,
        'folder-open': FolderOpenIcon,
        'wrench-screwdriver': WrenchScrewdriverIcon,
        'document-check': DocumentCheckIcon,
        'heart': HeartIcon,
        'globe-alt': GlobeAltSolidIcon,
        'document-text': DocumentTextIcon,
        'stamp': FolderOpenIcon, // Using folder as fallback for stamp
        'airplane': GlobeAltSolidIcon,
        'shield-check': ShieldCheckIcon,
        'truck': TruckIcon,
        'home': HomeIcon,
        'chart-bar': ChartBarSolidIcon,
        'document-duplicate': DocumentDuplicateIcon,
        'briefcase': BriefcaseIcon,
        'finger-print': FingerPrintIcon,
        'user-group': UserGroupSolidIcon,
    };
    return icons[iconName] || UsersIcon;
};

const isCategorySelected = (category) => {
    const categoryServices = props.serviceModules[category] || [];
    const categoryServiceIds = categoryServices.map(s => s.id);
    return categoryServiceIds.every(id => form.services_offered.includes(id));
};

const toggleCategoryServices = (category) => {
    const categoryServices = props.serviceModules[category] || [];
    const categoryServiceIds = categoryServices.map(s => s.id);
    
    if (isCategorySelected(category)) {
        // Deselect all
        form.services_offered = form.services_offered.filter(id => !categoryServiceIds.includes(id));
    } else {
        // Select all
        const newServices = [...new Set([...form.services_offered, ...categoryServiceIds])];
        form.services_offered = newServices;
    }
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

const deleteOfficeImage = async (imagePath) => {
    if (!confirm('Are you sure you want to delete this image?')) return;

    try {
        await router.delete(route('agency.profile.deleteOfficeImage'), {
            data: { image_path: imagePath },
            preserveScroll: true,
        });
    } catch (error) {
        alert('Failed to delete image');
    }
};

const submit = () => {
    form.put(route('agency.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
        },
    });
};
</script>
